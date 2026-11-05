<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Coach;
use App\Models\ParticipantPackage;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Menampilkan riwayat kehadiran peserta
     * yang berada di bawah tanggung jawab Coach yang sedang login.
     */
    public function index(Request $request): View
    {
        $coach = $this->getAuthenticatedCoach($request);

        $baseQuery = Attendance::query()
            ->whereHas(
                'booking.classSchedule',
                function ($query) use ($coach) {
                    $query->where('coach_id', $coach->id);
                }
            );

        $totalAttendance = (clone $baseQuery)->count();

        $presentCount = (clone $baseQuery)
            ->where('status', 'present')
            ->count();

        $permissionCount = (clone $baseQuery)
            ->where('status', 'permission')
            ->count();

        $sickCount = (clone $baseQuery)
            ->where('status', 'sick')
            ->count();

        $absentCount = (clone $baseQuery)
            ->where('status', 'absent')
            ->count();

        $attendances = (clone $baseQuery)
            ->with([
                'booking.participant',
                'booking.classSchedule.program',
            ])
            ->latest('checked_at')
            ->paginate(15)
            ->withQueryString();

        return view('coach.attendance.index', [
            'attendances' => $attendances,
            'totalAttendance' => $totalAttendance,
            'presentCount' => $presentCount,
            'permissionCount' => $permissionCount,
            'sickCount' => $sickCount,
            'absentCount' => $absentCount,
        ]);
    }

    /**
     * Menyimpan kehadiran peserta.
     *
     * Aturan penggunaan sesi:
     *
     * present    = sesi terpotong
     * permission = sesi tidak terpotong
     * sick       = sesi tidak terpotong
     * absent     = sesi tidak terpotong
     */
    public function store(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'present',
                    'permission',
                    'sick',
                    'absent',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $coach = $this->getAuthenticatedCoach($request);

        DB::transaction(function () use (
            $booking,
            $coach,
            $validated
        ): void {
            /*
            |--------------------------------------------------------------------------
            | Lock Booking
            |--------------------------------------------------------------------------
            |
            | Mencegah dua request memproses booking yang sama
            | pada waktu bersamaan.
            |
            */

            /*
             * Kunci jadwal sebelum booking agar urutan lock
             * konsisten dengan pembatalan oleh admin.
             */
            $lockedSchedule = \App\Models\ClassSchedule::query()
                ->whereKey($booking->class_schedule_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking->load([
                'classSchedule',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validasi Jadwal
            |--------------------------------------------------------------------------
            */

            $schedule = $lockedSchedule;

            if (
                (int) $lockedBooking->class_schedule_id
                !== (int) $lockedSchedule->id
            ) {
                throw ValidationException::withMessages([
                    'attendance' =>
                        'Jadwal booking berubah. Silakan muat ulang halaman.',
                ]);
            }

            if ($schedule === null) {
                throw ValidationException::withMessages([
                    'attendance' => 'Jadwal kelas untuk booking ini tidak ditemukan.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Coach
            |--------------------------------------------------------------------------
            */

            abort_unless(
                (int) $schedule->coach_id === (int) $coach->id,
                403,
                'Anda tidak memiliki akses untuk mencatat kehadiran booking ini.'
            );

            /*
            |--------------------------------------------------------------------------
            | Validasi Status Jadwal
            |--------------------------------------------------------------------------
            |
            | Attendance hanya boleh dilakukan pada jadwal
            | yang masih berstatus scheduled.
            |
            */

            if ($schedule->status !== 'scheduled') {
                throw ValidationException::withMessages([
                    'attendance' =>
                        'Kehadiran tidak dapat dicatat karena jadwal kelas sudah tidak aktif.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Waktu Kelas
            |--------------------------------------------------------------------------
            |
            | Coach tidak boleh mencatat kehadiran sebelum
            | tanggal dan jam mulai kelas.
            |
            */

            $classStartsAt = Carbon::parse(
                $schedule->class_date->format('Y-m-d')
                . ' '
                . $schedule->start_time
            );

            if (now()->lt($classStartsAt)) {
                throw ValidationException::withMessages([
                    'attendance' =>
                        'Kehadiran belum dapat dicatat karena kelas belum dimulai.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Status Booking
            |--------------------------------------------------------------------------
            |
            | Booking harus sudah dikonfirmasi Admin.
            |
            | Pending tidak boleh langsung diabsen karena belum
            | melalui proses konfirmasi.
            |
            */

            if ($lockedBooking->status !== 'confirmed') {
                $message = match ($lockedBooking->status) {
                    'pending' =>
                        'Booking belum dikonfirmasi Admin dan belum dapat diabsen.',

                    'completed' =>
                        'Booking ini sudah selesai dan tidak dapat diabsen kembali.',

                    'cancelled' =>
                        'Booking ini sudah dibatalkan.',

                    'rescheduled' =>
                        'Booking ini sudah dipindahkan ke jadwal lain.',

                    default =>
                        'Booking ini tidak dapat diabsen.',
                };

                throw ValidationException::withMessages([
                    'attendance' => $message,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Proteksi Attendance Ganda
            |--------------------------------------------------------------------------
            |
            | attendance.booking_id juga memiliki UNIQUE constraint
            | pada database, tetapi pengecekan aplikasi tetap dilakukan
            | agar pesan error lebih jelas.
            |
            */

            $existingAttendance = Attendance::query()
                ->where(
                    'booking_id',
                    $lockedBooking->id
                )
                ->lockForUpdate()
                ->first();

            if ($existingAttendance !== null) {
                throw ValidationException::withMessages([
                    'attendance' =>
                        'Kehadiran peserta ini sudah pernah dicatat.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Tentukan Penggunaan Sesi
            |--------------------------------------------------------------------------
            |
            | Hanya peserta dengan status HADIR yang menggunakan
            | satu sesi paket.
            |
            | Izin, sakit, dan tidak hadir tidak mengurangi sesi.
            |
            */

            $shouldDeductSession =
                $validated['status'] === 'present';

            /*
            |--------------------------------------------------------------------------
            | Potong Sesi
            |--------------------------------------------------------------------------
            */

            if ($shouldDeductSession) {
                $participantPackage = ParticipantPackage::query()
                    ->whereKey(
                        $lockedBooking->participant_package_id
                    )
                    ->lockForUpdate()
                    ->first();

                if ($participantPackage === null) {
                    throw ValidationException::withMessages([
                        'attendance' =>
                            'Paket peserta tidak ditemukan.',
                    ]);
                }

                if (
                    (int) $participantPackage->participant_id
                    !==
                    (int) $lockedBooking->participant_id
                ) {
                    throw ValidationException::withMessages([
                        'attendance' =>
                            'Paket tidak sesuai dengan peserta pada booking.',
                    ]);
                }

                if ($participantPackage->status !== 'active') {
                    throw ValidationException::withMessages([
                        'attendance' =>
                            'Paket peserta sudah tidak aktif.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Proteksi Pemotongan Ganda
                |--------------------------------------------------------------------------
                */

                if ($lockedBooking->session_deducted) {
                    throw ValidationException::withMessages([
                        'attendance' =>
                            'Sesi untuk booking ini sudah pernah dipotong.',
                    ]);
                }

                if (
                    (int) $participantPackage->remaining_sessions
                    <= 0
                ) {
                    throw ValidationException::withMessages([
                        'attendance' =>
                            'Sisa sesi peserta sudah habis.',
                    ]);
                }

                $newUsedSessions =
                    (int) $participantPackage->used_sessions + 1;

                $newRemainingSessions =
                    (int) $participantPackage->remaining_sessions - 1;

                $participantPackage->used_sessions =
                    $newUsedSessions;

                $participantPackage->remaining_sessions =
                    $newRemainingSessions;

                /*
                |--------------------------------------------------------------------------
                | Paket Otomatis Selesai
                |--------------------------------------------------------------------------
                */

                if ($newRemainingSessions <= 0) {
                    $participantPackage->status = 'completed';
                }

                $participantPackage->save();

                $lockedBooking->session_deducted = true;
            }

            /*
            |--------------------------------------------------------------------------
            | Selesaikan Booking
            |--------------------------------------------------------------------------
            |
            | Semua status attendance berarti booking untuk sesi tersebut
            | sudah selesai diproses.
            |
            | Namun hanya status present yang mengurangi sesi paket.
            |
            */

            $lockedBooking->status = 'completed';
            $lockedBooking->save();

            /*
            |--------------------------------------------------------------------------
            | Simpan Attendance
            |--------------------------------------------------------------------------
            */

            Attendance::create([
                'booking_id' => $lockedBooking->id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'checked_at' => now(),
            ]);
        }, 3);

        return redirect()
            ->route(
                'coach.schedules.show',
                $booking->class_schedule_id
            )
            ->with(
                'success',
                'Kehadiran peserta berhasil dicatat.'
            );
    }

    /**
     * Mengambil profil Coach aktif berdasarkan
     * user yang sedang login.
     */
    private function getAuthenticatedCoach(
        Request $request
    ): Coach {
        $coach = Coach::query()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->where(
                'status',
                'active'
            )
            ->first();

        abort_if(
            $coach === null,
            403,
            'Profil coach aktif tidak ditemukan.'
        );

        return $coach;
    }
}
