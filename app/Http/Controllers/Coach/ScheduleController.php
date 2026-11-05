<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Menampilkan seluruh jadwal milik coach yang sedang login.
     */
    public function index(Request $request): View
    {
        $coach = $this->getAuthenticatedCoach($request);

        $today = now()->toDateString();

        /**
         * Jadwal hari ini.
         */
        $todaySchedules = ClassSchedule::query()
            ->with([
                'program',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->whereIn('status', [
                        'pending',
                        'confirmed',
                    ]);
                },
            ])
            ->where('coach_id', $coach->id)
            ->whereDate('class_date', $today)
            ->orderBy('start_time')
            ->get();

        /**
         * Jadwal yang akan datang.
         */
        $upcomingSchedules = ClassSchedule::query()
            ->with([
                'program',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->whereIn('status', [
                        'pending',
                        'confirmed',
                    ]);
                },
            ])
            ->where('coach_id', $coach->id)
            ->whereDate('class_date', '>', $today)
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        /**
         * Riwayat jadwal.
         *
         * Jadwal dianggap masuk riwayat jika:
         * - tanggalnya sudah lewat, atau
         * - status kelas completed/cancelled.
         */
        $historySchedules = ClassSchedule::query()
            ->with([
                'program',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->whereIn('status', [
                        'pending',
                        'confirmed',
                        'completed',
                    ]);
                },
            ])
            ->where('coach_id', $coach->id)
            ->where(function ($query) use ($today) {
                $query
                    ->whereDate('class_date', '<', $today)
                    ->orWhereIn('status', [
                        'completed',
                        'cancelled',
                    ]);
            })
            ->orderByDesc('class_date')
            ->orderByDesc('start_time')
            ->limit(30)
            ->get();

        /**
         * Supaya jadwal completed/cancelled hari ini atau masa depan
         * tidak tampil dua kali pada bagian aktif.
         */
        $todaySchedules = $todaySchedules
            ->where('status', 'scheduled')
            ->values();

        $upcomingSchedules = $upcomingSchedules
            ->where('status', 'scheduled')
            ->values();

        return view('coach.schedules.index', [
            'coach' => $coach,
            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,
            'historySchedules' => $historySchedules,
        ]);
    }

    /**
     * Menampilkan detail sebuah kelas.
     *
     * Keamanan:
     * coach hanya boleh membuka jadwal miliknya sendiri.
     */
    public function show(
        Request $request,
        ClassSchedule $schedule
    ): View {
        $coach = $this->getAuthenticatedCoach($request);

        abort_unless(
            (int) $schedule->coach_id === (int) $coach->id,
            403,
            'Anda tidak memiliki akses ke jadwal ini.'
        );

        $schedule->load([
            'program',
            'location',
            'coach.user',

            'bookings' => function ($query) {
                $query
                    ->whereIn('status', [
                        'pending',
                        'confirmed',
                        'completed',
                    ])
                    ->orderBy('booked_at');
            },

            'bookings.participant',
            'bookings.participantPackage.package.program',
            'bookings.attendance',

            /**
             * Progress perkembangan peserta.
             *
             * Digunakan pada halaman detail kelas untuk menentukan:
             * - peserta belum dinilai
             * - peserta sudah dinilai
             * - tombol Isi Perkembangan
             * - tombol Lihat / Edit Perkembangan
             */
            'bookings.progressRecord',
        ]);

        return view('coach.schedules.show', [
            'coach' => $coach,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Mengambil profil coach berdasarkan user yang login.
     */
    private function getAuthenticatedCoach(Request $request): Coach
    {
        $coach = Coach::query()
            ->with('user')
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        abort_if(
            $coach === null,
            403,
            'Profil coach aktif tidak ditemukan.'
        );

        return $coach;
    }
}
