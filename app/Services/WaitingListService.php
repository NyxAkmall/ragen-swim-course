<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\ParticipantPackage;
use App\Models\WaitingList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WaitingListService
{
    public const OFFER_DURATION_MINUTES = 120;

    /** Customer joins a full class. No automatic offers. */
    public function join(int $participantId, int $scheduleId): WaitingList
    {
        return DB::transaction(function () use ($participantId, $scheduleId) {
            $schedule = ClassSchedule::query()->lockForUpdate()->findOrFail($scheduleId);
            $this->ensureUpcoming($schedule);
            $this->expireOffersLocked($schedule);

            $alreadyBooked = Booking::query()
                ->where('participant_id', $participantId)
                ->where('class_schedule_id', $scheduleId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($alreadyBooked) {
                $this->fail('Peserta sudah memiliki booking aktif pada jadwal ini.');
            }

            $existing = WaitingList::query()
                ->where('participant_id', $participantId)
                ->where('class_schedule_id', $scheduleId)
                ->first();

            if ($existing !== null && in_array($existing->status, ['waiting', 'offered', 'accepted'], true)) {
                $this->fail('Peserta sudah terdaftar pada Waiting List jadwal ini.');
            }

            if ($this->availableSlotsLocked($schedule) > 0) {
                $this->fail('Kelas masih memiliki slot. Silakan melakukan booking langsung.');
            }

            $nextNumber = ((int) WaitingList::query()
                ->where('class_schedule_id', $scheduleId)
                ->max('queue_number')) + 1;

            if ($existing !== null) {
                $existing->update([
                    'status' => 'waiting',
                    'queue_number' => $nextNumber,
                    'offered_at' => null,
                    'expires_at' => null,
                ]);

                return $existing->refresh();
            }

            return WaitingList::create([
                'participant_id' => $participantId,
                'class_schedule_id' => $scheduleId,
                'status' => 'waiting',
                'queue_number' => $nextNumber,
                'offered_at' => null,
                'expires_at' => null,
            ]);
        }, 3);
    }

    /** Admin selects a specific waiting participant; no automatic or FIFO selection. */
    public function offerSelectedByAdmin(int $waitingListId): WaitingList
    {
        return DB::transaction(function () use ($waitingListId) {
            $initial = WaitingList::query()->findOrFail($waitingListId);
            $schedule = ClassSchedule::query()
                ->lockForUpdate()->findOrFail($initial->class_schedule_id);
            $this->ensureUpcoming($schedule);
            $this->expireOffersLocked($schedule);

            $entry = WaitingList::query()
                ->lockForUpdate()->findOrFail($waitingListId);
            if ((int) $entry->class_schedule_id !== (int) $schedule->id
                || $entry->status !== 'waiting') {
                $this->fail('Peserta ini tidak lagi berada dalam daftar tunggu aktif.');
            }
            if ($this->availableSlotsLocked($schedule) <= 0) {
                $this->fail('Tidak ada slot tersedia untuk ditawarkan.');
            }

            $entry->update([
                'status' => 'offered',
                'offered_at' => now(),
                'expires_at' => now()->addMinutes(self::OFFER_DURATION_MINUTES),
            ]);

            return $entry->refresh();
        }, 3);
    }

    /**
     * Accept a live offer and convert its reserved slot to a booking.
     * The controller must authenticate the customer and pass their user ID.
     */
    public function acceptOffer(
        int $waitingListId,
        int $userId,
        int $participantPackageId
    ): Booking {
        /*
         * Kedaluwarsakan penawaran dalam transaksi terpisah.
         * Dengan begitu, status expired tetap tersimpan
         * meskipun penerimaan ditolak dan transaksi booking
         * mengalami rollback.
         *
         * Tidak ada penawaran otomatis kepada antrean berikutnya.
         */
        $initialEntry = WaitingList::query()
            ->with('participant')
            ->findOrFail($waitingListId);

        /*
         * Pastikan customer memiliki antrean ini sebelum
         * menjalankan proses yang dapat mengubah database.
         */
        abort_unless(
            $initialEntry->participant !== null
                && (int) $initialEntry->participant->user_id === $userId,
            403,
            'Antrean bukan milik customer ini.'
        );

        /*
         * Perbarui penawaran kedaluwarsa dalam transaksi terpisah.
         * Status expired tetap tersimpan jika penerimaan ditolak.
         * Tidak ada penawaran otomatis kepada antrean berikutnya.
         */
        $this->refreshQueue((int) $initialEntry->class_schedule_id);

        return DB::transaction(function () use ($waitingListId, $userId, $participantPackageId) {
            $initialEntry = WaitingList::query()->findOrFail($waitingListId);
            $schedule = ClassSchedule::query()
                ->lockForUpdate()
                ->findOrFail($initialEntry->class_schedule_id);

            $entry = WaitingList::query()->lockForUpdate()->findOrFail($waitingListId);
            if ((int) $entry->class_schedule_id !== (int) $schedule->id) {
                $this->fail('Data antrean berubah. Silakan coba kembali.');
            }

            $entry->load('participant');
            abort_unless(
                $entry->participant !== null
                    && (int) $entry->participant->user_id === $userId,
                403,
                'Antrean bukan milik customer ini.'
            );

            $this->ensureUpcoming($schedule);
            $this->expireOffersLocked($schedule);
            $entry->refresh();

            if ($entry->status !== 'offered'
                || $entry->expires_at === null
                || !$entry->expires_at->isFuture()) {
                $this->fail('Penawaran sudah tidak aktif atau telah kedaluwarsa.');
            }

            \App\Models\Participant::query()
                ->lockForUpdate()
                ->findOrFail($entry->participant_id);

            $participantPackage = ParticipantPackage::query()
                ->lockForUpdate()
                ->findOrFail($participantPackageId);
            $participantPackage->load(['participant', 'package']);

            abort_unless(
                (int) $participantPackage->participant_id === (int) $entry->participant_id
                    && $participantPackage->participant !== null
                    && (int) $participantPackage->participant->user_id === $userId,
                403,
                'Paket bukan milik peserta dalam antrean.'
            );

            if ($participantPackage->participant->status !== 'active'
                || $participantPackage->status !== 'active'
                || (int) $participantPackage->remaining_sessions <= 0) {
                $this->fail('Peserta atau paket sudah tidak aktif, atau sesi paket telah habis.');
            }

            if ($participantPackage->start_date !== null
                && ($participantPackage->start_date->isAfter(today())
                    || $schedule->class_date->isBefore($participantPackage->start_date))) {
                $this->fail('Jadwal berada sebelum masa berlaku paket.');
            }

            if ($participantPackage->expiry_date !== null
                && ($participantPackage->expiry_date->isBefore(today())
                    || $schedule->class_date->isAfter($participantPackage->expiry_date))) {
                $this->fail('Masa berlaku paket tidak mencakup jadwal kelas.');
            }

            if ($participantPackage->package === null
                || (int) $participantPackage->package->program_id !== (int) $schedule->program_id) {
                $this->fail('Program paket tidak sesuai dengan jadwal kelas.');
            }

            $reservedSessions = Booking::query()
                ->where('participant_package_id', $participantPackage->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('session_deducted', false)
                ->count();

            if ((int) $participantPackage->remaining_sessions - $reservedSessions <= 0) {
                $this->fail('Seluruh sisa sesi paket sudah dialokasikan untuk booking aktif.');
            }

            $duplicate = Booking::query()
                ->where('participant_id', $entry->participant_id)
                ->where('class_schedule_id', $schedule->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($duplicate) {
                $this->fail('Peserta sudah memiliki booking aktif pada jadwal ini.');
            }

            $hasConflict = Booking::query()
                ->where('participant_id', $entry->participant_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereHas('classSchedule', function ($query) use ($schedule) {
                    $query->whereDate('class_date', $schedule->class_date)
                        ->where('start_time', '<', $schedule->end_time)
                        ->where('end_time', '>', $schedule->start_time);
                })
                ->exists();

            if ($hasConflict) {
                $this->fail('Peserta memiliki jadwal lain pada waktu yang sama.');
            }

            // The current offer reserves one slot. Verify all reservations fit.
            $bookedCount = Booking::query()
                ->where('class_schedule_id', $schedule->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();
            $reservedOffers = $this->getReservedOfferCount((int) $schedule->id);

            if ($reservedOffers < 1
                || $bookedCount + $reservedOffers > (int) $schedule->capacity) {
                $this->fail('Kapasitas kelas tidak tersedia untuk penawaran ini.');
            }

            do {
                $bookingCode = 'BKG-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Booking::query()->where('booking_code', $bookingCode)->exists());

            $booking = Booking::create([
                'participant_id' => $entry->participant_id,
                'participant_package_id' => $participantPackage->id,
                'class_schedule_id' => $schedule->id,
                'booking_code' => $bookingCode,
                'status' => 'pending',
                'session_deducted' => false,
                'booked_at' => now(),
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'previous_booking_id' => null,
                'notes' => 'Booking dari penawaran Waiting List #' . $entry->id,
            ]);

            $entry->update(['status' => 'accepted']);

            return $booking;
        }, 3);
    }

    /** Decline an active offer without triggering another offer. */
    public function declineOffer(int $waitingListId, int $userId): WaitingList
    {
        return DB::transaction(function () use ($waitingListId, $userId) {
            $initialEntry = WaitingList::query()->findOrFail($waitingListId);
            $schedule = ClassSchedule::query()
                ->lockForUpdate()->findOrFail($initialEntry->class_schedule_id);
            $entry = WaitingList::query()->lockForUpdate()->findOrFail($waitingListId);

            if ((int) $entry->class_schedule_id !== (int) $schedule->id) {
                $this->fail('Data antrean berubah. Silakan coba kembali.');
            }

            $entry->load('participant');
            abort_unless(
                $entry->participant !== null
                    && (int) $entry->participant->user_id === $userId,
                403,
                'Antrean bukan milik customer ini.'
            );

            $this->expireOffersLocked($schedule);
            $entry->refresh();

            if ($entry->status !== 'offered') {
                $this->fail('Penawaran sudah tidak dapat ditolak.');
            }

            $entry->update([
                'status' => 'cancelled',
                'offered_at' => null,
                'expires_at' => null,
            ]);

            return $entry->refresh();
        }, 3);
    }

    /** Customer cancels a waiting entry or active offer. */
    public function cancel(int $waitingListId, int $participantId): WaitingList
    {
        return DB::transaction(function () use ($waitingListId, $participantId) {
            $initialEntry = WaitingList::query()->findOrFail($waitingListId);
            $schedule = ClassSchedule::query()
                ->lockForUpdate()->findOrFail($initialEntry->class_schedule_id);
            $entry = WaitingList::query()->lockForUpdate()->findOrFail($waitingListId);

            if ((int) $entry->class_schedule_id !== (int) $schedule->id) {
                $this->fail('Data antrean berubah. Silakan coba kembali.');
            }
            if ((int) $entry->participant_id !== $participantId) {
                abort(403, 'Antrean bukan milik peserta.');
            }

            $this->expireOffersLocked($schedule);
            $entry->refresh();

            if (!in_array($entry->status, ['waiting', 'offered'], true)) {
                $this->fail('Antrean sudah tidak dapat dibatalkan.');
            }

            $entry->update([
                'status' => 'cancelled',
                'offered_at' => null,
                'expires_at' => null,
            ]);

            return $entry->refresh();
        }, 3);
    }

    /** Admin cancels an entry; authorization belongs to the controller. */
    public function cancelByAdmin(int $waitingListId): WaitingList
    {
        return DB::transaction(function () use ($waitingListId) {
            $initialEntry = WaitingList::query()->findOrFail($waitingListId);
            $schedule = ClassSchedule::query()
                ->lockForUpdate()->findOrFail($initialEntry->class_schedule_id);
            $entry = WaitingList::query()->lockForUpdate()->findOrFail($waitingListId);

            if ((int) $entry->class_schedule_id !== (int) $schedule->id) {
                $this->fail('Data antrean berubah. Silakan coba kembali.');
            }

            $this->expireOffersLocked($schedule);
            $entry->refresh();

            if (!in_array($entry->status, ['waiting', 'offered'], true)) {
                $this->fail('Antrean sudah tidak dapat dibatalkan.');
            }

            $entry->update([
                'status' => 'cancelled',
                'offered_at' => null,
                'expires_at' => null,
            ]);

            return $entry->refresh();
        }, 3);
    }

    /** Expire elapsed offers; never offer automatically. */
    public function expireOffersLocked(ClassSchedule $schedule): int
    {
        return WaitingList::query()
            ->where('class_schedule_id', $schedule->id)
            ->where('status', 'offered')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '<=', now());
            })
            ->update(['status' => 'expired']);
    }

    public function refreshQueue(int $scheduleId): int
    {
        return DB::transaction(function () use ($scheduleId) {
            $schedule = ClassSchedule::query()->lockForUpdate()->findOrFail($scheduleId);
            return $this->expireOffersLocked($schedule);
        }, 3);
    }

    public function getQueuePosition(WaitingList $entry): ?int
    {
        if ($entry->status !== 'waiting') {
            return null;
        }

        return WaitingList::query()
            ->where('class_schedule_id', $entry->class_schedule_id)
            ->where('status', 'waiting')
            ->where(function ($query) use ($entry) {
                $query->where('queue_number', '<', $entry->queue_number)
                    ->orWhere(function ($query) use ($entry) {
                        $query->where('queue_number', $entry->queue_number)
                            ->where('id', '<', $entry->id);
                    });
            })
            ->count() + 1;
    }

    public function getReservedOfferCount(int $scheduleId): int
    {
        return WaitingList::query()
            ->where('class_schedule_id', $scheduleId)
            ->where('status', 'offered')
            ->where('expires_at', '>', now())
            ->count();
    }

    private function availableSlotsLocked(ClassSchedule $schedule): int
    {
        $bookedCount = Booking::query()
            ->where('class_schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        $reservedOffers = $this->getReservedOfferCount((int) $schedule->id);

        return max(0, (int) $schedule->capacity - $bookedCount - $reservedOffers);
    }

    private function ensureUpcoming(ClassSchedule $schedule): void
    {
        if (!$schedule->isScheduled()
            || $schedule->class_date === null
            || $schedule->start_time === null
            || $schedule->hasStarted()) {
            $this->fail('Jadwal kelas sudah tidak tersedia.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['waiting_list' => $message]);
    }
}
