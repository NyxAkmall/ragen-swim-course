<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\ParticipantPackage;
use App\Models\WaitingList;
use App\Services\WaitingListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Halaman booking customer.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $participantPackages = ParticipantPackage::query()
            ->with([
                'participant',
                'package.program',
            ])
            ->whereHas('participant', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'active');
            })
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', today());
            })
            ->orderBy('expiry_date')
            ->get()
            ->filter(function ($participantPackage) {
                return $this->hasAvailableSession(
                    $participantPackage
                );
            })
            ->values();

        $schedules = ClassSchedule::query()
            ->with([
                'program',
                'coach',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->whereIn(
                        'status',
                        ['pending', 'confirmed']
                    );
                },

                // Penawaran Waiting List yang belum kedaluwarsa
                // juga menggunakan kapasitas kelas.
                'waitingLists as active_offers_count' => function ($query) {
                    $query
                        ->where('status', 'offered')
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', now());
                },
            ])
            ->where('status', 'scheduled')
            ->whereDate('class_date', '>=', today())
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($schedule) {
                return $this->isScheduleUpcoming($schedule);
            })
            ->values();

        $activeBookings = Booking::query()
            ->with([
                'participant',
                'participantPackage.package.program',
                'classSchedule.program',
                'classSchedule.coach',
                'classSchedule.location',
            ])
            ->whereHas('participant', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('classSchedule', function ($query) {
                $query->whereDate('class_date', '>=', today());
            })
            ->latest('booked_at')
            ->get();

        /**
         * Seluruh Waiting List milik customer.
         *
         * Status historis tetap ditampilkan supaya customer
         * dapat melihat antrean yang sudah selesai,
         * dibatalkan, atau kedaluwarsa.
         */
        $waitingLists = WaitingList::query()
            ->with([
                'participant',
                'classSchedule.program',
                'classSchedule.coach',
                'classSchedule.location',
            ])
            ->whereHas('participant', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        /**
         * ID antrean yang masih aktif.
         *
         * Digunakan untuk menghindari customer
         * mendaftar ulang pada jadwal yang sama.
         */
        $activeWaitingListKeys = $waitingLists
            ->filter(function ($waitingList) {
                if ($waitingList->status === 'waiting') {
                    return true;
                }

                return $waitingList->status === 'offered'
                    && $waitingList->expires_at !== null
                    && $waitingList->expires_at->isFuture();
            })
            ->map(function ($waitingList) {
                return $waitingList->participant_id
                    . ':'
                    . $waitingList->class_schedule_id;
            })
            ->values()
            ->all();

        return view('customer.bookings.index', compact(
            'participantPackages',
            'schedules',
            'activeBookings',
            'waitingLists',
            'activeWaitingListKeys'
        ));
    }

    /**
     * Detail booking.
     */
    public function show(
        Request $request,
        Booking $booking
    ): View {
        $this->ensureBookingOwnership($request, $booking);

        $booking->load([
            'participant',
            'participantPackage.package.program',
            'classSchedule.program',
            'classSchedule.coach',
            'classSchedule.location',
            'previousBooking',
            'rescheduledBooking',
        ]);

        return view(
            'customer.bookings.show',
            compact('booking')
        );
    }

    /**
     * Membuat booking baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'participant_package_id' => [
                'required',
                'integer',
                'exists:participant_packages,id',
            ],
            'class_schedule_id' => [
                'required',
                'integer',
                'exists:class_schedules,id',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $userId = $request->user()->id;

        $booking = DB::transaction(function () use (
            $validated,
            $userId
        ) {
            $schedule = ClassSchedule::query()
                ->lockForUpdate()
                ->findOrFail($validated['class_schedule_id']);

            $packageParticipantId = ParticipantPackage::query()
                ->whereKey($validated['participant_package_id'])
                ->value('participant_id');

            abort_if($packageParticipantId === null, 404);

            \App\Models\Participant::query()
                ->lockForUpdate()
                ->findOrFail($packageParticipantId);

            $participantPackage = ParticipantPackage::query()
                ->lockForUpdate()
                ->findOrFail($validated['participant_package_id']);

            abort_if(
                (int) $participantPackage->participant_id
                    !== (int) $packageParticipantId,
                409,
                'Data peserta pada paket berubah.'
            );

            $participantPackage->load([
                'participant',
                'package.program',
            ]);

            $this->ensurePackageOwnership(
                $participantPackage,
                $userId
            );

            $schedule->load([
                'program',
                'coach',
                'location',
            ]);

            $this->validateParticipantPackage(
                $participantPackage,
                $schedule
            );

            $this->validateScheduleAvailable($schedule);

            $this->validateProgramMatch(
                $participantPackage,
                $schedule
            );

            $this->validateDuplicateBooking(
                $participantPackage->participant_id,
                $schedule->id
            );

            $this->validateScheduleConflict(
                $participantPackage->participant_id,
                $schedule
            );

            $this->validateScheduleCapacity($schedule);

            return Booking::create([
                'participant_id' =>
                    $participantPackage->participant_id,
                'participant_package_id' =>
                    $participantPackage->id,
                'class_schedule_id' =>
                    $schedule->id,
                'booking_code' =>
                    $this->generateBookingCode(),
                'status' => 'pending',
                'session_deducted' => false,
                'booked_at' => now(),
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'previous_booking_id' => null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $booking->load('classSchedule');

        $schedule = $booking->classSchedule;

        $scheduleInfo = '';

        if ($schedule && $schedule->class_date) {
            $date = $schedule->class_date
                ->locale('id')
                ->translatedFormat('l, d F Y');

            $start = substr((string) $schedule->start_time, 0, 5);
            $end = substr((string) $schedule->end_time, 0, 5);

            $scheduleInfo = ' Jadwal kelas yang dipesan: '
                . $date
                . ', pukul '
                . $start
                . '–'
                . $end
                . ' WIB.';
        }

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with(
                'success',
                'Booking berhasil dibuat dengan kode '
                . $booking->booking_code
                . '. Menunggu konfirmasi admin.'
                . $scheduleInfo
            );
    }

    /**
     * Membatalkan booking.
     */
    public function cancel(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        $validated = $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'min:3',
                'max:1000',
            ],
        ]);

        $userId = $request->user()->id;

        DB::transaction(function () use (
            $booking,
            $validated,
            $userId
        ) {
            $scheduleId = Booking::query()
                ->whereKey($booking->id)
                ->value('class_schedule_id');

            abort_if($scheduleId === null, 404);

            $lockedSchedule = ClassSchedule::query()
                ->whereKey($scheduleId)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            abort_if(
                (int) $lockedBooking->class_schedule_id
                    !== (int) $lockedSchedule->id,
                409,
                'Jadwal booking berubah. Silakan coba kembali.'
            );

            $lockedBooking->load([
                'participant',
                'participantPackage',
                'classSchedule',
            ]);

            abort_unless(
                $lockedBooking->participant !== null
                && (int) $lockedBooking->participant->user_id
                    === (int) $userId,
                403
            );

            abort_unless(
                in_array(
                    $lockedBooking->status,
                    ['pending', 'confirmed'],
                    true
                ),
                422,
                'Booking ini sudah tidak dapat dibatalkan.'
            );

            $this->validateBookingBeforeClass($lockedBooking);

            abort_if(
                $lockedBooking->session_deducted,
                422,
                'Sesi paket sudah digunakan. Silakan hubungi admin.'
            );

            $lockedBooking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' =>
                    $validated['cancellation_reason'],
            ]);
        });

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with(
                'success',
                'Booking berhasil dibatalkan.'
            );
    }

    /**
     * Halaman reschedule.
     */
    public function reschedule(
        Request $request,
        Booking $booking
    ): View {
        $this->ensureBookingOwnership($request, $booking);

        $booking->load([
            'participant',
            'participantPackage.package.program',
            'classSchedule.program',
            'classSchedule.coach',
            'classSchedule.location',
            'rescheduledBooking',
        ]);

        $this->validateRescheduleEligibility($booking);

        $participantPackage = $booking->participantPackage;

        $this->validateParticipantPackage(
            $participantPackage
        );

        $schedules = ClassSchedule::query()
            ->with([
                'program',
                'coach',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->whereIn(
                        'status',
                        ['pending', 'confirmed']
                    );
                },
                'waitingLists as active_offers_count' => function ($query) {
                    $query->where('status', 'offered')
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', now());
                },
            ])
            ->where(
                'program_id',
                $participantPackage->package->program_id
            )
            ->where('status', 'scheduled')
            ->where('id', '!=', $booking->class_schedule_id)
            ->whereDate('class_date', '>=', today())
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($schedule) use ($participantPackage) {
                if (!$this->isScheduleUpcoming($schedule)) {
                    return false;
                }

                if (
                    $participantPackage->start_date !== null
                    && $schedule->class_date->isBefore(
                        $participantPackage->start_date
                    )
                ) {
                    return false;
                }

                if (
                    $participantPackage->expiry_date !== null
                    && $schedule->class_date->isAfter(
                        $participantPackage->expiry_date
                    )
                ) {
                    return false;
                }

                return true;
            })
            ->values();

        return view(
            'customer.bookings.reschedule',
            compact('booking', 'schedules')
        );
    }

    /**
     * Memproses perpindahan jadwal.
     */
    public function processReschedule(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        $validated = $request->validate([
            'class_schedule_id' => [
                'required',
                'integer',
                'exists:class_schedules,id',
            ],
        ]);

        $userId = $request->user()->id;

        $newBooking = DB::transaction(function () use (
            $booking,
            $validated,
            $userId
        ) {
            $oldScheduleId = Booking::query()
                ->whereKey($booking->id)
                ->value('class_schedule_id');

            abort_if($oldScheduleId === null, 404);

            $newScheduleId = (int) $validated['class_schedule_id'];

            $scheduleIds = array_unique([
                (int) $oldScheduleId,
                $newScheduleId,
            ]);

            sort($scheduleIds, SORT_NUMERIC);

            foreach ($scheduleIds as $scheduleId) {
                ClassSchedule::query()
                    ->whereKey($scheduleId)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $bookingParticipantId = Booking::query()
                ->whereKey($booking->id)
                ->value('participant_id');

            abort_if($bookingParticipantId === null, 404);

            \App\Models\Participant::query()
                ->lockForUpdate()
                ->findOrFail($bookingParticipantId);

            $oldBooking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            abort_if(
                (int) $oldBooking->participant_id
                    !== (int) $bookingParticipantId,
                409,
                'Data peserta booking berubah.'
            );

            abort_if(
                (int) $oldBooking->class_schedule_id
                    !== (int) $oldScheduleId,
                409,
                'Jadwal booking berubah. Silakan coba kembali.'
            );

            $oldBooking->load([
                'participant',
                'participantPackage.package',
                'classSchedule',
            ]);

            abort_unless(
                $oldBooking->participant !== null
                && (int) $oldBooking->participant->user_id
                    === (int) $userId,
                403
            );

            $this->validateRescheduleEligibility($oldBooking);

            $participantPackage = ParticipantPackage::query()
                ->lockForUpdate()
                ->findOrFail(
                    $oldBooking->participant_package_id
                );

            $participantPackage->load([
                'participant',
                'package.program',
            ]);

            $this->ensurePackageOwnership(
                $participantPackage,
                $userId
            );

            $newSchedule = ClassSchedule::query()
                ->lockForUpdate()
                ->findOrFail($validated['class_schedule_id']);

            $newSchedule->load([
                'program',
                'coach',
                'location',
            ]);

            abort_if(
                (int) $newSchedule->id
                    === (int) $oldBooking->class_schedule_id,
                422,
                'Silakan pilih jadwal yang berbeda.'
            );

            $this->validateParticipantPackage(
                $participantPackage,
                $newSchedule,
                $oldBooking->id
            );

            $this->validateScheduleAvailable($newSchedule);

            $this->validateProgramMatch(
                $participantPackage,
                $newSchedule
            );

            $this->validateDuplicateBooking(
                $oldBooking->participant_id,
                $newSchedule->id,
                $oldBooking->id
            );

            $this->validateScheduleConflict(
                $oldBooking->participant_id,
                $newSchedule,
                $oldBooking->id
            );

            $this->validateScheduleCapacity($newSchedule);

            $newBooking = Booking::create([
                'participant_id' =>
                    $oldBooking->participant_id,
                'participant_package_id' =>
                    $oldBooking->participant_package_id,
                'class_schedule_id' =>
                    $newSchedule->id,
                'booking_code' =>
                    $this->generateBookingCode(),
                'status' => 'pending',
                'session_deducted' => false,
                'booked_at' => now(),
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'previous_booking_id' =>
                    $oldBooking->id,
                'notes' => $oldBooking->notes,
            ]);

            $oldBooking->update([
                'status' => 'rescheduled',
            ]);

            return $newBooking;
        });

        return redirect()
            ->route('customer.bookings.show', $newBooking)
            ->with(
                'success',
                'Jadwal berhasil diubah. Kode booking baru: '
                . $newBooking->booking_code . '.'
            );
    }

    /**
     * Memastikan booking milik customer.
     */
    private function ensureBookingOwnership(
        Request $request,
        Booking $booking
    ): void {
        $owned = Booking::query()
            ->whereKey($booking->id)
            ->whereHas('participant', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->exists();

        abort_unless($owned, 404, 'Booking tidak ditemukan.');
    }

    /**
     * Memastikan paket milik customer.
     */
    private function ensurePackageOwnership(
        ParticipantPackage $participantPackage,
        int $userId
    ): void {
        abort_unless(
            $participantPackage->participant !== null
            && (int) $participantPackage->participant->user_id
                === (int) $userId,
            403
        );
    }

    /**
     * Validasi paket dan peserta.
     */
    private function validateParticipantPackage(
        ParticipantPackage $participantPackage,
        ?ClassSchedule $schedule = null,
        ?int $exceptBookingId = null
    ): void {
        abort_unless(
            $participantPackage->participant !== null
            && $participantPackage->participant->status === 'active',
            422,
            'Peserta sedang nonaktif dan tidak dapat melakukan booking.'
        );

        abort_unless(
            $participantPackage->status === 'active',
            422,
            'Paket peserta sudah tidak aktif.'
        );

        abort_if(
            (int) $participantPackage->remaining_sessions <= 0,
            422,
            'Sisa sesi paket sudah habis.'
        );

        abort_if(
            $participantPackage->start_date !== null
            && $participantPackage->start_date->isAfter(today()),
            422,
            'Paket belum memasuki tanggal mulai.'
        );

        abort_if(
            $participantPackage->expiry_date !== null
            && $participantPackage->expiry_date->isBefore(today()),
            422,
            'Masa berlaku paket sudah berakhir.'
        );

        if ($schedule !== null) {
            abort_if(
                $participantPackage->start_date !== null
                && $schedule->class_date->isBefore(
                    $participantPackage->start_date
                ),
                422,
                'Jadwal berada sebelum tanggal mulai paket.'
            );

            abort_if(
                $participantPackage->expiry_date !== null
                && $schedule->class_date->isAfter(
                    $participantPackage->expiry_date
                ),
                422,
                'Jadwal berada setelah masa berlaku paket.'
            );
        }

        abort_unless(
            $this->hasAvailableSession(
                $participantPackage,
                $exceptBookingId
            ),
            422,
            'Seluruh sisa sesi paket sudah dialokasikan untuk booking aktif.'
        );
    }

    /**
     * Menghitung sesi yang masih dapat dipesan.
     *
     * Booking pending/confirmed yang belum dipotong
     * tetap dianggap menggunakan alokasi sesi.
     */
    private function hasAvailableSession(
        ParticipantPackage $participantPackage,
        ?int $exceptBookingId = null
    ): bool {
        $query = Booking::query()
            ->where(
                'participant_package_id',
                $participantPackage->id
            )
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('session_deducted', false);

        if ($exceptBookingId !== null) {
            $query->where('id', '!=', $exceptBookingId);
        }

        $reservedSessions = $query->count();

        return (
            (int) $participantPackage->remaining_sessions
            - $reservedSessions
        ) > 0;
    }

    /**
     * Validasi kesesuaian program.
     */
    private function validateProgramMatch(
        ParticipantPackage $participantPackage,
        ClassSchedule $schedule
    ): void {
        abort_unless(
            $participantPackage->package !== null
            && (int) $participantPackage->package->program_id
                === (int) $schedule->program_id,
            422,
            'Program paket tidak sesuai dengan jadwal.'
        );
    }

    /**
     * Validasi jadwal tersedia.
     */
    private function validateScheduleAvailable(
        ClassSchedule $schedule
    ): void {
        abort_unless(
            $schedule->status === 'scheduled',
            422,
            'Jadwal sudah tidak tersedia.'
        );

        abort_unless(
            $this->isScheduleUpcoming($schedule),
            422,
            'Kelas sudah dimulai atau jadwal sudah lewat.'
        );
    }

    /**
     * Memeriksa apakah kelas belum dimulai.
     */
    private function isScheduleUpcoming(
        ClassSchedule $schedule
    ): bool {
        if ($schedule->class_date->isBefore(today())) {
            return false;
        }

        if (
            $schedule->class_date->isToday()
            && now()->format('H:i:s')
                >= $schedule->start_time
        ) {
            return false;
        }

        return true;
    }

    /**
     * Booking hanya dapat diubah sebelum kelas.
     */
    private function validateBookingBeforeClass(
        Booking $booking
    ): void {
        abort_unless(
            $booking->classSchedule !== null,
            422,
            'Data jadwal booking tidak ditemukan.'
        );

        abort_unless(
            $this->isScheduleUpcoming($booking->classSchedule),
            422,
            'Kelas sudah dimulai atau jadwal sudah lewat.'
        );
    }

    /**
     * Validasi kelayakan reschedule.
     */
    private function validateRescheduleEligibility(
        Booking $booking
    ): void {
        abort_unless(
            in_array(
                $booking->status,
                ['pending', 'confirmed'],
                true
            ),
            422,
            'Booking ini sudah tidak dapat dijadwalkan ulang.'
        );

        $this->validateBookingBeforeClass($booking);

        abort_if(
            $booking->session_deducted,
            422,
            'Booking yang sudah menggunakan sesi tidak dapat dijadwalkan ulang.'
        );
    }

    /**
     * Mencegah booking ganda.
     */
    private function validateDuplicateBooking(
        int $participantId,
        int $scheduleId,
        ?int $exceptBookingId = null
    ): void {
        $query = Booking::query()
            ->where('participant_id', $participantId)
            ->where('class_schedule_id', $scheduleId)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($exceptBookingId !== null) {
            $query->where('id', '!=', $exceptBookingId);
        }

        abort_if(
            $query->exists(),
            422,
            'Peserta sudah memiliki booking aktif pada jadwal ini.'
        );
    }

    /**
     * Mencegah bentrok waktu latihan.
     */
    private function validateScheduleConflict(
        int $participantId,
        ClassSchedule $schedule,
        ?int $exceptBookingId = null
    ): void {
        $query = Booking::query()
            ->where('participant_id', $participantId)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($exceptBookingId !== null) {
            $query->where('id', '!=', $exceptBookingId);
        }

        $hasConflict = $query
            ->whereHas('classSchedule', function ($query) use ($schedule) {
                $query
                    ->whereDate(
                        'class_date',
                        $schedule->class_date
                    )
                    ->where(
                        'start_time',
                        '<',
                        $schedule->end_time
                    )
                    ->where(
                        'end_time',
                        '>',
                        $schedule->start_time
                    );
            })
            ->exists();

        abort_if(
            $hasConflict,
            422,
            'Peserta memiliki jadwal lain pada waktu yang sama.'
        );
    }

    /**
     * Memastikan kapasitas kelas tersedia.
     */
    private function validateScheduleCapacity(
        ClassSchedule $schedule
    ): void {
        $bookedCount = Booking::query()
            ->where('class_schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        // Penawaran Waiting List yang aktif juga menahan kapasitas.
        // Jadwal sudah dikunci oleh store() atau processReschedule().
        $reservedOffers = app(WaitingListService::class)
            ->getReservedOfferCount((int) $schedule->id);

        abort_if(
            $bookedCount + $reservedOffers >= (int) $schedule->capacity,
            422,
            'Kelas sudah penuh atau slot sedang ditawarkan kepada peserta Waiting List. Silakan pilih jadwal lain.'
        );
    }

    /**
     * Membuat kode booking unik.
     */
    private function generateBookingCode(): string
    {
        do {
            $bookingCode = 'BKG-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(Str::random(6));
        } while (
            Booking::query()
                ->where('booking_code', $bookingCode)
                ->exists()
        );

        return $bookingCode;
    }
}
