<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use App\Services\WaitingListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaitingListController extends Controller
{
    public function __construct(
        private readonly WaitingListService $waitingListService
    ) {
    }

    /**
     * Menampilkan seluruh Waiting List untuk admin.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');
        $date = (string) $request->input('date', '');

        $query = WaitingList::query()
            ->with([
                'participant.user',
                'classSchedule.program',
                'classSchedule.coach.user',
                'classSchedule.location',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Pencarian peserta, customer, dan program
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $query->where(function ($waitingQuery) use ($search) {
                $waitingQuery
                    ->whereHas(
                        'participant',
                        function ($participantQuery) use ($search) {
                            $participantQuery
                                ->where(
                                    'full_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'classSchedule.program',
                        function ($programQuery) use ($search) {
                            $programQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */
        $allowedStatuses = [
            'waiting',
            'offered',
            'accepted',
            'expired',
            'cancelled',
        ];

        if (in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal jadwal
        |--------------------------------------------------------------------------
        */
        if ($date !== '') {
            $request->validate([
                'date' => ['date_format:Y-m-d'],
            ]);

            $query->whereHas(
                'classSchedule',
                function ($scheduleQuery) use ($date) {
                    $scheduleQuery->whereDate(
                        'class_date',
                        $date
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar antrean
        |--------------------------------------------------------------------------
        */
        $waitingLists = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */
        $statistics = [
            'total' => WaitingList::query()->count(),

            'waiting' => WaitingList::query()
                ->where('status', 'waiting')
                ->count(),

            'offered' => WaitingList::query()
                ->where('status', 'offered')
                ->where('expires_at', '>', now())
                ->count(),

            'accepted' => WaitingList::query()
                ->where('status', 'accepted')
                ->count(),

            'expired' => WaitingList::query()
                ->where(function ($query) {
                    $query
                        ->where('status', 'expired')
                        ->orWhere(function ($offerQuery) {
                            $offerQuery
                                ->where('status', 'offered')
                                ->where(function ($expiryQuery) {
                                    $expiryQuery
                                        ->whereNull('expires_at')
                                        ->orWhere(
                                            'expires_at',
                                            '<=',
                                            now()
                                        );
                                });
                        });
                })
                ->count(),

            'cancelled' => WaitingList::query()
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view(
            'admin.waiting-lists.index',
            compact(
                'waitingLists',
                'statistics',
                'search',
                'status',
                'date'
            )
        );
    }

    /**
     * Menampilkan detail antrean.
     */
    public function show(WaitingList $waitingList): View
    {
        $waitingList->load([
            'participant.user',
            'participant.participantPackages.package.program',
            'classSchedule.program',
            'classSchedule.coach.user',
            'classSchedule.location',
        ]);

        $queuePosition = $this->waitingListService
            ->getQueuePosition($waitingList);

        $reservedOffers = $this->waitingListService
            ->getReservedOfferCount(
                (int) $waitingList->class_schedule_id
            );

        return view(
            'admin.waiting-lists.show',
            compact(
                'waitingList',
                'queuePosition',
                'reservedOffers'
            )
        );
    }

    /** Admin offers a slot to the participant explicitly selected on the page. */
    public function offer(WaitingList $waitingList): RedirectResponse
    {
        $entry = $this->waitingListService
            ->offerSelectedByAdmin((int) $waitingList->id);

        return redirect()
            ->route('admin.waiting-lists.show', $entry)
            ->with('success', 'Slot berhasil ditawarkan kepada peserta yang dipilih. Penawaran berlaku selama 2 jam.');
    }

    /**
     * Admin membatalkan Waiting List.
     */
    public function cancel(
        WaitingList $waitingList
    ): RedirectResponse {
        $entry = $this->waitingListService
            ->cancelByAdmin(
                (int) $waitingList->id
            );

        return redirect()
            ->route(
                'admin.waiting-lists.show',
                $entry
            )
            ->with(
                'success',
                'Waiting List peserta berhasil dibatalkan.'
            );
    }
}
