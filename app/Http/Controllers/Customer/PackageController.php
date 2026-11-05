<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Participant;
use App\Models\ParticipantPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Menampilkan seluruh paket milik customer,
     * termasuk paket dari peserta yang nonaktif.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Seluruh peserta milik customer
        |--------------------------------------------------------------------------
        |
        | Peserta nonaktif tetap ditampilkan agar
        | riwayat paketnya tidak hilang.
        |
        */

        $participants = $user
            ->participants()
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Seluruh paket milik peserta customer
        |--------------------------------------------------------------------------
        |
        | Kepemilikan dibatasi melalui relasi peserta
        | dengan user yang sedang login.
        |
        */

        $participantPackages = ParticipantPackage::query()
            ->with([
                'participant',
                'package.program',
                'orderItem',
            ])
            ->whereHas(
                'participant',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )
            ->orderByRaw("
                CASE
                    WHEN status = 'active' THEN 1
                    WHEN status = 'completed' THEN 2
                    WHEN status = 'expired' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Paket Aktif
        |--------------------------------------------------------------------------
        */

        $activePackages = $participantPackages
            ->filter(function ($participantPackage) {
                return $participantPackage->status === 'active';
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Riwayat Paket
        |--------------------------------------------------------------------------
        |
        | Semua paket selain status active tetap
        | dapat dilihat sebagai riwayat.
        |
        */

        $packageHistory = $participantPackages
            ->filter(function ($participantPackage) {
                return $participantPackage->status !== 'active';
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Statistik Paket
        |--------------------------------------------------------------------------
        */

        $totalActivePackages = $activePackages->count();

        $totalRemainingSessions = $activePackages->sum(
            'remaining_sessions'
        );

        $totalUsedSessions = $participantPackages->sum(
            'used_sessions'
        );

        return view(
            'customer.packages.index',
            compact(
                'participants',
                'participantPackages',
                'activePackages',
                'packageHistory',
                'totalActivePackages',
                'totalRemainingSessions',
                'totalUsedSessions'
            )
        );
    }

    /**
     * Menampilkan detail paket peserta.
     */
    public function show(
        Request $request,
        ParticipantPackage $participantPackage
    ): View {
        /*
        |--------------------------------------------------------------------------
        | Validasi Kepemilikan
        |--------------------------------------------------------------------------
        */

        $this->ensureOwnership(
            $request,
            $participantPackage
        );

        /*
        |--------------------------------------------------------------------------
        | Memuat Informasi Paket dan Booking
        |--------------------------------------------------------------------------
        */

        $participantPackage->load([
            'participant',
            'package.program',
            'orderItem',
            'bookings' => function ($query) {
                $query
                    ->with([
                        'classSchedule.program',
                        'classSchedule.coach.user',
                        'classSchedule.location',
                        'attendance',
                        'progressRecord',
                    ])
                    ->orderByDesc('created_at');
            },
        ]);

        return view(
            'customer.packages.show',
            compact('participantPackage')
        );
    }

    /**
     * Menampilkan paket yang tersedia untuk dibeli.
     */
    public function browse(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Hanya Peserta Aktif untuk Pembelian
        |--------------------------------------------------------------------------
        */

        $participants = $request->user()
            ->participants()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Paket Aktif dari Program Aktif
        |--------------------------------------------------------------------------
        */

        $packages = Package::query()
            ->with('program')
            ->where('status', 'active')
            ->whereHas(
                'program',
                function ($query) {
                    $query->where('status', 'active');
                }
            )
            ->orderBy('program_id')
            ->orderBy('price')
            ->orderBy('id')
            ->get();

        return view(
            'customer.packages.browse',
            compact(
                'participants',
                'packages'
            )
        );
    }

    /**
     * Memastikan paket benar-benar dimiliki
     * customer yang sedang login.
     */
    private function ensureOwnership(
        Request $request,
        ParticipantPackage $participantPackage
    ): void {
        $participant = Participant::query()
            ->where(
                'id',
                $participantPackage->participant_id
            )
            ->where(
                'user_id',
                $request->user()->id
            )
            ->first();

        abort_if(
            $participant === null,
            403,
            'Anda tidak memiliki akses ke paket peserta ini.'
        );
    }
}
