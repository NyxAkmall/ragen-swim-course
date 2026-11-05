<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Participant;
use App\Models\ParticipantPackage;
use App\Models\ProgressRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard customer Ragen Swim Course.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Customer dan Tanggal Hari Ini
        |--------------------------------------------------------------------------
        */

        $user = $request->user();
        $today = today()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Daftar Peserta Aktif
        |--------------------------------------------------------------------------
        */

        $participants = Participant::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->orderBy('id')
            ->get();

        $participantIds = $participants->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Paket Aktif yang Masih Dapat Digunakan
        |--------------------------------------------------------------------------
        |
        | Paket harus memenuhi syarat:
        | - Peserta masih aktif.
        | - Status paket active.
        | - Memiliki sisa sesi.
        | - Tanggal mulai sudah tiba.
        | - Masa berlaku belum berakhir.
        |
        | Paket yang belum dimulai atau kedaluwarsa
        | tidak dihitung sebagai paket siap digunakan.
        |
        */

        $activePackages = ParticipantPackage::query()
            ->with([
                'participant',
                'package.program',
            ])
            ->whereIn('participant_id', $participantIds)
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function ($query) use ($today) {
                $query
                    ->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query
                    ->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', $today);
            })
            ->orderByDesc('id')
            ->get();

        // Pengingat paket yang akan berakhir dalam tujuh hari, berdasarkan
        // paket aktif milik peserta pada akun ini (bukan milik pengguna lain).
        $expiringPackages = $activePackages
            ->filter(function ($participantPackage) use ($today) {
                if (! $participantPackage->expiry_date) {
                    return false;
                }

                $expiryDate = \Illuminate\Support\Carbon::parse($participantPackage->expiry_date)->startOfDay();

                return $expiryDate->betweenIncluded(
                    \Illuminate\Support\Carbon::parse($today)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($today)->addDays(7)->endOfDay()
                );
            })
            ->sortBy('expiry_date')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Total Sisa Pertemuan
        |--------------------------------------------------------------------------
        |
        | Menghitung sisa sesi paket yang masih
        | dapat digunakan secara administratif.
        |
        | Angka ini belum dikurangi sesi yang telah
        | dialokasikan untuk booking pending/confirmed.
        |
        */

        $totalRemainingSessions = (int) $activePackages
            ->sum('remaining_sessions');

        /*
        |--------------------------------------------------------------------------
        | Booking Mendatang
        |--------------------------------------------------------------------------
        |
        | Menampilkan booking milik peserta aktif,
        | dengan status pending atau confirmed.
        |
        */

        $upcomingBookings = Booking::query()
            ->with([
                'participant',
                'classSchedule.program',
                'classSchedule.coach.user',
                'classSchedule.location',
            ])
            ->whereIn('participant_id', $participantIds)
            ->whereIn('status', [
                'pending',
                'confirmed',
            ])
            ->whereHas('classSchedule', function ($query) use ($today) {
                $query->whereDate(
                    'class_date',
                    '>=',
                    $today
                );
            })
            ->orderBy(
                ClassSchedule::query()
                    ->select('class_date')
                    ->whereColumn(
                        'class_schedules.id',
                        'bookings.class_schedule_id'
                    )
                    ->limit(1)
            )
            ->orderBy(
                ClassSchedule::query()
                    ->select('start_time')
                    ->whereColumn(
                        'class_schedules.id',
                        'bookings.class_schedule_id'
                    )
                    ->limit(1)
            )
            ->orderBy('bookings.id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Booking Terdekat
        |--------------------------------------------------------------------------
        */

        $nextBooking = $upcomingBookings->first();

        /*
        |--------------------------------------------------------------------------
        | Perkembangan Renang Terbaru
        |--------------------------------------------------------------------------
        */

        $latestProgress = ProgressRecord::query()
            ->with([
                'participant',
                'coach.user',
                'booking.classSchedule.program',
                'booking.classSchedule.location',
            ])
            ->whereIn('participant_id', $participantIds)
            ->orderByDesc('assessment_date')
            ->orderByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Transaksi Terakhir
        |--------------------------------------------------------------------------
        */

        $latestOrder = Order::query()
            ->with([
                'items.participant',
                'items.package',
                'payments',
                'invoice',
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Invoice Terbaru
        |--------------------------------------------------------------------------
        */

        $latestInvoice = Invoice::query()
            ->with('order')
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Statistik Dashboard
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'participants' => $participants->count(),

            'active_packages' => $activePackages->count(),

            'remaining_sessions' => $totalRemainingSessions,

            'upcoming_bookings' => $upcomingBookings->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Kirim Data ke Blade
        |--------------------------------------------------------------------------
        |
        | Seluruh nama variabel dipertahankan agar
        | kompatibel dengan customer.dashboard.
        |
        */

        return view('customer.dashboard', [
            'user' => $user,

            'participants' => $participants,

            'activePackages' => $activePackages,
            'expiringPackages' => $expiringPackages,

            'totalRemainingSessions' => $totalRemainingSessions,

            'upcomingBookings' => $upcomingBookings,

            'nextBooking' => $nextBooking,

            'latestProgress' => $latestProgress,

            'latestOrder' => $latestOrder,

            'latestInvoice' => $latestInvoice,

            'statistics' => $statistics,
        ]);
    }
}
