<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Participant;
use App\Models\WaitingList;
use App\Services\WaitingListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WaitingListController extends Controller
{
    public function __construct(
        private readonly WaitingListService $waitingListService
    ) {
    }

    /**
     * Customer mendaftarkan peserta ke Waiting List.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'participant_id' => [
                'required',
                'integer',
                'exists:participants,id',
            ],
            'class_schedule_id' => [
                'required',
                'integer',
                'exists:class_schedules,id',
            ],
        ]);

        $userId = (int) $request->user()->id;

        $participant = Participant::query()
            ->where('id', $validated['participant_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$participant->isActive()) {
            throw ValidationException::withMessages([
                'participant_id' =>
                    'Peserta harus berstatus aktif untuk masuk daftar tunggu.',
            ]);
        }

        $schedule = ClassSchedule::query()
            ->findOrFail($validated['class_schedule_id']);

        if (
            !$schedule->isScheduled()
            || $schedule->hasStarted()
        ) {
            throw ValidationException::withMessages([
                'class_schedule_id' =>
                    'Jadwal kelas sudah tidak tersedia.',
            ]);
        }

        $hasEligiblePackage = $participant
            ->participantPackages()
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->whereHas('package', function ($query) use ($schedule) {
                $query->where(
                    'program_id',
                    $schedule->program_id
                );
            })
            ->where(function ($query) {
                $query
                    ->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', today());
            })
            ->exists();

        if (!$hasEligiblePackage) {
            throw ValidationException::withMessages([
                'participant_id' =>
                    'Peserta belum memiliki paket aktif yang sesuai dengan program kelas ini.',
            ]);
        }

        $entry = $this->waitingListService->join(
            (int) $participant->id,
            (int) $schedule->id
        );

        $position = $this->waitingListService
            ->getQueuePosition($entry);

        $message = 'Berhasil masuk Waiting List.';

        if ($position !== null) {
            $message .= ' Posisi antrean saat ini: '
                . $position . '.';
        }

        $message .=
            ' Admin akan menawarkan slot apabila tersedia.';

        return redirect()
            ->route('customer.bookings.index')
            ->with('success', $message);
    }

    /**
     * Customer membatalkan antrean miliknya.
     */
    public function destroy(
        Request $request,
        WaitingList $waitingList
    ): RedirectResponse {
        $userId = (int) $request->user()->id;

        $this->ensureOwnership($waitingList, $userId);

        $this->waitingListService->cancel(
            (int) $waitingList->id,
            (int) $waitingList->participant_id
        );

        return redirect()
            ->route('customer.bookings.index')
            ->with(
                'success',
                'Pendaftaran Waiting List berhasil dibatalkan.'
            );
    }

    /**
     * Customer menerima penawaran slot dari admin.
     *
     * Paket dipilih oleh customer.
     * Validasi dan pembuatan booking dilakukan
     * secara atomik oleh WaitingListService.
     */
    public function accept(
        Request $request,
        WaitingList $waitingList
    ): RedirectResponse {
        $userId = (int) $request->user()->id;

        $this->ensureOwnership($waitingList, $userId);

        $validated = $request->validate([
            'participant_package_id' => [
                'required',
                'integer',
                'exists:participant_packages,id',
            ],
        ]);

        $booking = $this->waitingListService->acceptOffer(
            (int) $waitingList->id,
            $userId,
            (int) $validated['participant_package_id']
        );

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with(
                'success',
                'Penawaran berhasil diterima. '
                . 'Booking dibuat dengan kode '
                . $booking->booking_code . '.'
            );
    }

    /**
     * Customer menolak penawaran slot dari admin.
     *
     * Penolakan tidak menyebabkan penawaran
     * otomatis kepada peserta berikutnya.
     */
    public function decline(
        Request $request,
        WaitingList $waitingList
    ): RedirectResponse {
        $userId = (int) $request->user()->id;

        $this->ensureOwnership($waitingList, $userId);

        $this->waitingListService->declineOffer(
            (int) $waitingList->id,
            $userId
        );

        return redirect()
            ->route('customer.bookings.index')
            ->with(
                'success',
                'Penawaran Waiting List berhasil ditolak. '
                . 'Slot telah dilepaskan.'
            );
    }

    /**
     * Mengambil daftar antrean milik customer.
     */
    public function getCustomerWaitingLists(
        Request $request
    ) {
        $userId = (int) $request->user()->id;

        return WaitingList::query()
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
            ->get();
    }

    /**
     * Memastikan Waiting List milik customer
     * yang sedang login.
     */
    private function ensureOwnership(
        WaitingList $waitingList,
        int $userId
    ): void {
        $belongsToCustomer = Participant::query()
            ->where('id', $waitingList->participant_id)
            ->where('user_id', $userId)
            ->exists();

        abort_unless(
            $belongsToCustomer,
            403,
            'Anda tidak memiliki akses ke antrean ini.'
        );
    }
}
