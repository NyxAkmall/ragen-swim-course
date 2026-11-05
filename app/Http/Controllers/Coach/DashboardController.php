<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard coach yang sedang login.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Tanggal Hari Ini
        |--------------------------------------------------------------------------
        */

        $today = today()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Profil Coach yang Sedang Login
        |--------------------------------------------------------------------------
        |
        | Hanya coach aktif yang dapat mengakses dashboard.
        | Data coach diambil berdasarkan akun yang sedang login.
        |
        */

        $coach = Coach::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('user')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Status Booking Aktif
        |--------------------------------------------------------------------------
        */

        $activeBookingStatuses = [
            'pending',
            'confirmed',
        ];

        /*
        |--------------------------------------------------------------------------
        | Jadwal Hari Ini
        |--------------------------------------------------------------------------
        |
        | Menampilkan jadwal mengajar coach pada hari ini
        | yang masih berstatus scheduled.
        |
        */

        $todaySchedules = ClassSchedule::query()
            ->where('coach_id', $coach->id)
            ->whereDate('class_date', $today)
            ->where('status', 'scheduled')
            ->with([
                'program',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) use ($activeBookingStatuses) {
                    $query->whereIn(
                        'status',
                        $activeBookingStatuses
                    );
                },
            ])
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Jadwal Mendatang
        |--------------------------------------------------------------------------
        |
        | Menampilkan maksimal lima jadwal terdekat
        | setelah hari ini.
        |
        */

        $upcomingSchedules = ClassSchedule::query()
            ->where('coach_id', $coach->id)
            ->whereDate('class_date', '>', $today)
            ->where('status', 'scheduled')
            ->with([
                'program',
                'location',
            ])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) use ($activeBookingStatuses) {
                    $query->whereIn(
                        'status',
                        $activeBookingStatuses
                    );
                },
            ])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik Jadwal
        |--------------------------------------------------------------------------
        */

        $todayScheduleCount = $todaySchedules->count();

        $upcomingScheduleCount = ClassSchedule::query()
            ->where('coach_id', $coach->id)
            ->whereDate('class_date', '>', $today)
            ->where('status', 'scheduled')
            ->count();

        $completedScheduleCount = ClassSchedule::query()
            ->where('coach_id', $coach->id)
            ->where('status', 'completed')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Jumlah Peserta Aktif
        |--------------------------------------------------------------------------
        |
        | Menghitung peserta unik yang memiliki booking
        | pending atau confirmed pada jadwal coach
        | mulai hari ini dan seterusnya.
        |
        | Perhitungan dilakukan langsung di database
        | agar lebih efisien dibandingkan mengambil
        | seluruh data booking ke memori aplikasi.
        |
        */

        $activeParticipantCount = Booking::query()
            ->whereIn('status', $activeBookingStatuses)
            ->whereNotNull('participant_id')
            ->whereHas('classSchedule', function ($query) use ($coach, $today) {
                $query
                    ->where('coach_id', $coach->id)
                    ->where('status', 'scheduled')
                    ->whereDate('class_date', '>=', $today);
            })
            ->distinct()
            ->count('participant_id');

        /*
        |--------------------------------------------------------------------------
        | Tampilkan Dashboard Coach
        |--------------------------------------------------------------------------
        */

        return view('coach.dashboard', [
            'coach' => $coach,

            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,

            'todayScheduleCount' => $todayScheduleCount,
            'upcomingScheduleCount' => $upcomingScheduleCount,
            'completedScheduleCount' => $completedScheduleCount,

            'activeParticipantCount' => $activeParticipantCount,
        ]);
    }
}
