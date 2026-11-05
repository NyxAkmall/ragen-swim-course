<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClassSchedule;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Menampilkan seluruh booking peserta.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $date = $request->input('date');

        $query = Booking::query()
            ->with([
                'participant.user',
                'participantPackage.package.program',
                'classSchedule.program',
                'classSchedule.coach.user',
                'classSchedule.location',
                'attendance',
                'progressRecord',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        |
        | Admin dapat mencari berdasarkan:
        | - kode booking
        | - nama peserta
        | - nama customer/orang tua
        | - nama program
        | - nama coach
        | - nama lokasi
        |
        */
        if ($search !== '') {
            $query->where(function ($bookingQuery) use ($search) {
                $bookingQuery
                    ->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('participant', function ($participantQuery) use ($search) {
                        $participantQuery
                            ->where('full_name', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            });
                    })
                    ->orWhereHas('classSchedule.program', function ($programQuery) use ($search) {
                        $programQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('classSchedule.coach.user', function ($userQuery) use ($search) {
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('classSchedule.location', function ($locationQuery) use ($search) {
                        $locationQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */
        if (
            $status &&
            in_array(
                $status,
                [
                    'pending',
                    'confirmed',
                    'completed',
                    'cancelled',
                    'rescheduled',
                ],
                true
            )
        ) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal Kelas
        |--------------------------------------------------------------------------
        */
        if ($date) {
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
        | Urutan Booking
        |--------------------------------------------------------------------------
        */
        $bookings = $query
            ->orderByDesc('booked_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */
        $statistics = [
            'total' => Booking::count(),

            'pending' => Booking::where(
                'status',
                'pending'
            )->count(),

            'confirmed' => Booking::where(
                'status',
                'confirmed'
            )->count(),

            'completed' => Booking::where(
                'status',
                'completed'
            )->count(),

            'cancelled' => Booking::where(
                'status',
                'cancelled'
            )->count(),

            'rescheduled' => Booking::where(
                'status',
                'rescheduled'
            )->count(),
        ];

        return view(
            'admin.bookings.index',
            compact(
                'bookings',
                'statistics',
                'search',
                'status',
                'date'
            )
        );
    }

    /**
     * Menampilkan detail satu booking.
     */
    public function show(Booking $booking): View
    {
        $booking->load([
            'participant.user',

            'participantPackage.package.program',

            'classSchedule.program',
            'classSchedule.coach.user',
            'classSchedule.location',

            'attendance',

            'progressRecord.coach.user',

            'previousBooking.classSchedule',

            'rescheduledBooking.classSchedule',
        ]);

        return view(
            'admin.bookings.show',
            compact('booking')
        );
    }

    /**
     * Mengonfirmasi booking yang masih pending.
     */
    public function confirm(
        Booking $booking
    ): RedirectResponse {
        DB::transaction(
            function () use ($booking) {

                $scheduleId = Booking::query()
                    ->whereKey($booking->id)
                    ->value('class_schedule_id');

                abort_if($scheduleId === null, 404);

                $lockedSchedule = ClassSchedule::query()
                    ->whereKey($scheduleId)
                    ->lockForUpdate()
                    ->firstOrFail();
                $lockedBooking = Booking::query()
                    ->with([
                        'classSchedule',
                    ])
                    ->lockForUpdate()
                    ->findOrFail(
                        $booking->id
                    );
                if ((int) $lockedBooking->class_schedule_id !== (int) $lockedSchedule->id) {
                    abort(409, 'Jadwal booking berubah. Silakan coba kembali.');
                }

                /*
                |--------------------------------------------------------------------------
                | Booking hanya dapat dikonfirmasi dari status pending
                |--------------------------------------------------------------------------
                */
                if (
                    $lockedBooking->status
                    !==
                    'pending'
                ) {
                    abort(
                        422,
                        'Hanya booking berstatus pending yang dapat dikonfirmasi.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Booking yang sudah menggunakan sesi tidak boleh dimodifikasi
                |--------------------------------------------------------------------------
                */
                if (
                    $lockedBooking->session_deducted
                ) {
                    abort(
                        422,
                        'Booking ini sudah menggunakan sesi paket dan tidak dapat dikonfirmasi ulang.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Pastikan jadwal masih tersedia
                |--------------------------------------------------------------------------
                */
                $schedule =
                    $lockedBooking->classSchedule;

                if ($schedule === null) {
                    abort(
                        422,
                        'Data jadwal booking tidak ditemukan.'
                    );
                }

                if (
                    $schedule->status
                    !==
                    'scheduled'
                ) {
                    abort(
                        422,
                        'Booking tidak dapat dikonfirmasi karena jadwal kelas sudah tidak aktif.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Pastikan kelas belum dimulai
                |--------------------------------------------------------------------------
                */
                $this->validateBookingBeforeClass(
                    $lockedBooking
                );

                /*
                |--------------------------------------------------------------------------
                | Konfirmasi Booking
                |--------------------------------------------------------------------------
                |
                | Tidak ada pengurangan sesi pada proses ini.
                | Sesi hanya dikurangi melalui proses kehadiran Coach.
                |
                */
                $lockedBooking->update([
                    'status' => 'confirmed',
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.bookings.show',
                $booking
            )
            ->with(
                'success',
                'Booking '
                . $booking->booking_code
                . ' berhasil dikonfirmasi.'
            );
    }

    /**
     * Membatalkan booking oleh Admin.
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
        ], [
            'cancellation_reason.required' =>
                'Alasan pembatalan wajib diisi.',

            'cancellation_reason.min' =>
                'Alasan pembatalan minimal 3 karakter.',

            'cancellation_reason.max' =>
                'Alasan pembatalan maksimal 1000 karakter.',
        ]);

        DB::transaction(
            function () use (
                $booking,
                $validated
            ) {
                $scheduleId = Booking::query()
                    ->whereKey($booking->id)
                    ->value('class_schedule_id');

                abort_if($scheduleId === null, 404);

                $lockedSchedule = ClassSchedule::query()
                    ->whereKey($scheduleId)
                    ->lockForUpdate()
                    ->firstOrFail();
                $lockedBooking =
                    Booking::query()
                        ->with([
                            'classSchedule',
                            'attendance',
                        ])
                        ->lockForUpdate()
                        ->findOrFail(
                            $booking->id
                        );
                if ((int) $lockedBooking->class_schedule_id !== (int) $lockedSchedule->id) {
                    abort(409, 'Jadwal booking berubah. Silakan coba kembali.');
                }

                /*
                |--------------------------------------------------------------------------
                | Hanya booking aktif yang dapat dibatalkan
                |--------------------------------------------------------------------------
                */
                if (
                    !in_array(
                        $lockedBooking->status,
                        [
                            'pending',
                            'confirmed',
                        ],
                        true
                    )
                ) {
                    abort(
                        422,
                        'Booking ini sudah tidak dapat dibatalkan.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Booking yang sudah menggunakan sesi tidak boleh dibatalkan
                |--------------------------------------------------------------------------
                */
                if (
                    $lockedBooking->session_deducted
                ) {
                    abort(
                        422,
                        'Booking ini sudah menggunakan sesi paket dan tidak dapat dibatalkan.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Booking yang sudah memiliki kehadiran tidak boleh dibatalkan
                |--------------------------------------------------------------------------
                |
                | Ini menjadi pengaman tambahan agar Admin tidak mengubah booking
                | yang sudah masuk ke proses kehadiran Coach.
                |
                */
                if (
                    $lockedBooking->attendance
                    !==
                    null
                ) {
                    abort(
                        422,
                        'Booking yang sudah memiliki data kehadiran tidak dapat dibatalkan.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Pastikan kelas belum dimulai
                |--------------------------------------------------------------------------
                */
                $this->validateBookingBeforeClass(
                    $lockedBooking
                );

                /*
                |--------------------------------------------------------------------------
                | Batalkan Booking
                |--------------------------------------------------------------------------
                |
                | Pembatalan tidak mengurangi sesi paket.
                |
                */
                $lockedBooking->update([
                    'status' => 'cancelled',

                    'cancelled_at' => now(),

                    'cancellation_reason' =>
                        $validated[
                            'cancellation_reason'
                        ],
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.bookings.show',
                $booking
            )
            ->with(
                'success',
                'Booking '
                . $booking->booking_code
                . ' berhasil dibatalkan.'
            );
    }

    /**
     * Memastikan booking masih berada sebelum waktu kelas dimulai.
     */
    private function validateBookingBeforeClass(
        Booking $booking
    ): void {
        $schedule =
            $booking->classSchedule;

        if ($schedule === null) {
            abort(
                422,
                'Data jadwal booking tidak ditemukan.'
            );
        }

        if (
            $schedule->class_date
                ->isBefore(today())
        ) {
            abort(
                422,
                'Kelas sudah lewat.'
            );
        }

        if (
            $schedule->class_date->isToday()
            &&
            now()->format('H:i:s')
                >=
            $schedule->start_time
        ) {
            abort(
                422,
                'Kelas sudah dimulai.'
            );
        }
    }
}
