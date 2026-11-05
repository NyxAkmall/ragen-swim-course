<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Coach;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\ParticipantPackage;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama admin.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'customers' => User::query()
                ->where('role', 'customer')
                ->count(),

            'participants' => Participant::query()
                ->count(),

            'coaches' => Coach::query()
                ->where('status', 'active')
                ->count(),

            'bookings' => Booking::query()
                ->count(),

            'paid_orders' => Order::query()
                ->where('status', 'paid')
                ->count(),

            'revenue' => Payment::query()
                ->where('status', 'paid')
                ->sum('amount'),
        ];


        /*
        |--------------------------------------------------------------------------
        | Booking Terbaru
        |--------------------------------------------------------------------------
        */

        $latestBookings = Booking::query()
            ->with([
                'participant.user',
                'classSchedule.program',
                'classSchedule.coach.user',
                'classSchedule.location',
            ])
            ->orderByDesc('booked_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Transaksi Terbaru
        |--------------------------------------------------------------------------
        */

        $latestOrders = Order::query()
            ->with([
                'user',
                'items.participant',
                'payments',
                'invoice',
            ])
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Customer Terbaru
        |--------------------------------------------------------------------------
        */

        $latestCustomers = User::query()
            ->where('role', 'customer')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();


        // Pusat tindakan admin: semua angka dibaca langsung dari database.
        // Waiting list tetap dikelola manual oleh admin, tanpa penawaran otomatis.
        $today = today()->toDateString();
        $actionCenter = [
            'pending_bookings' => Booking::query()->where('status', 'pending')->count(),
            'waiting_list' => WaitingList::query()->where('status', 'waiting')->count(),
            'pending_payments' => Payment::query()->where('status', 'pending')->count(),
            'expiring_packages' => ParticipantPackage::query()
                ->where('status', 'active')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate('expiry_date', '<=', today()->addDays(7)->toDateString())
                ->count(),
            'today_schedules' => ClassSchedule::query()
                ->whereDate('class_date', $today)
                ->where('status', 'scheduled')
                ->count(),
        ];

        // Agenda tujuh hari: query terbatas dan relasi eager-loaded untuk menghindari N+1.
        $agendaStart = today();
        $agendaEnd = today()->addDays(6);
        $upcomingSchedules = ClassSchedule::query()
            ->with(['program', 'coach.user', 'location'])
            ->whereBetween('class_date', [$agendaStart->toDateString(), $agendaEnd->toDateString()])
            ->where('status', 'scheduled')
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'actionCenter' => $actionCenter,
            'upcomingSchedules' => $upcomingSchedules,
            'latestBookings' => $latestBookings,
            'latestOrders' => $latestOrders,
            'latestCustomers' => $latestCustomers,
        ]);
    }
}
