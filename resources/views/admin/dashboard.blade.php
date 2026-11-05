@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('topbar-title', 'Dashboard Admin')

@section('content')

@php
    $admin = auth()->user();

    $paidRevenue = (float) ($statistics['revenue'] ?? 0);

    $formatRupiah = function ($amount) {
        return 'Rp ' . number_format(
            (float) $amount,
            0,
            ',',
            '.'
        );
    };

    $bookingStatusLabels = [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rescheduled' => 'Dijadwal Ulang',
    ];

    $orderStatusLabels = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ];
@endphp


{{-- =========================================================
    PAGE HEADER
========================================================== --}}

<div class="page-header">

    <div>

        <h1 class="page-title">
            Dashboard Admin
        </h1>

        <p class="page-description">
            Pantau aktivitas utama Ragen Swim Course mulai dari
            customer, peserta, coach, booking kelas, hingga transaksi.
        </p>

    </div>

</div>


{{-- =========================================================
    WELCOME PANEL
========================================================== --}}

<section class="admin-welcome">

    <div class="admin-welcome-content">

        <div class="admin-welcome-label">
            ADMIN PANEL
        </div>

        <h2>
            Halo, {{ $admin->name }}
        </h2>

        <p>
            Berikut ringkasan data dan aktivitas terbaru
            Ragen Swim Course.
        </p>

    </div>


    <div class="admin-welcome-decoration" aria-hidden="true">
        <img src="{{ (function () { $path = \App\Models\Setting::getValue('website_logo', 'branding/ragen-logo.png'); return $path && $path !== 'branding/ragen-logo.png' ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : asset('branding/ragen-logo.png'); })() }}" alt="" loading="lazy">
    </div>

</section>


{{-- =========================================================
    ACTION CENTER — pekerjaan yang membutuhkan perhatian admin
========================================================== --}}
<section class="ragen-action-center" aria-labelledby="ragen-action-title">
    <div class="ragen-action-heading">
        <div>
            <span class="ragen-action-eyebrow">OPERASIONAL HARI INI</span>
            <h2 id="ragen-action-title">Perlu Ditindaklanjuti</h2>
            <p>Ringkasan pekerjaan dan kelas yang perlu dipantau.</p>
        </div>
        <span class="ragen-action-date">{{ now()->translatedFormat('d F Y') }}</span>
    </div>
    <div class="ragen-action-grid">
        <a class="ragen-action-item" href="{{ route('admin.bookings.index') }}">
            <span class="ragen-action-number">{{ number_format($actionCenter['pending_bookings'] ?? 0, 0, ',', '.') }}</span>
            <span class="ragen-action-label">Booking menunggu konfirmasi</span>
            <span class="ragen-action-link">Lihat booking &rarr;</span>
        </a>
        <a class="ragen-action-item" href="{{ route('admin.waiting-lists.index') }}">
            <span class="ragen-action-number">{{ number_format($actionCenter['waiting_list'] ?? 0, 0, ',', '.') }}</span>
            <span class="ragen-action-label">Waiting list perlu keputusan</span>
            <span class="ragen-action-link">Kelola manual &rarr;</span>
        </a>
        <a class="ragen-action-item" href="{{ route('admin.orders.index') }}">
            <span class="ragen-action-number">{{ number_format($actionCenter['pending_payments'] ?? 0, 0, ',', '.') }}</span>
            <span class="ragen-action-label">Pembayaran berstatus pending</span>
            <span class="ragen-action-link">Lihat transaksi &rarr;</span>
        </a>
        <a class="ragen-action-item" href="{{ route('admin.packages.index') }}">
            <span class="ragen-action-number">{{ number_format($actionCenter['expiring_packages'] ?? 0, 0, ',', '.') }}</span>
            <span class="ragen-action-label">Paket peserta berakhir ≤ 7 hari</span>
            <span class="ragen-action-link">Lihat paket &rarr;</span>
        </a>
        <a class="ragen-action-item" href="{{ route('admin.class-schedules.index') }}">
            <span class="ragen-action-number">{{ number_format($actionCenter['today_schedules'] ?? 0, 0, ',', '.') }}</span>
            <span class="ragen-action-label">Kelas terjadwal hari ini</span>
            <span class="ragen-action-link">Lihat jadwal &rarr;</span>
        </a>
    </div>
</section>

{{-- =========================================================
    AGENDA OPERASIONAL 7 HARI
========================================================== --}}
<section class="ragen-agenda" aria-labelledby="ragen-agenda-title">
    <div class="ragen-agenda-head">
        <div>
            <span class="ragen-action-eyebrow">KALENDER OPERASIONAL</span>
            <h2 id="ragen-agenda-title">Jadwal 7 Hari ke Depan</h2>
            <p>Kelas terjadwal dari hari ini hingga enam hari berikutnya.</p>
        </div>
        <a href="{{ route('admin.class-schedules.index') }}" class="ragen-agenda-all">Kelola jadwal &rarr;</a>
    </div>

    @if ($upcomingSchedules->isEmpty())
        <div class="ragen-agenda-empty">Belum ada kelas terjadwal dalam tujuh hari ke depan.</div>
    @else
        <div class="ragen-agenda-list">
            @foreach ($upcomingSchedules->groupBy(fn ($schedule) => $schedule->class_date->format('Y-m-d')) as $date => $daySchedules)
                <div class="ragen-agenda-day">
                    <div class="ragen-agenda-day-heading">
                        <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</strong>
                        <span>{{ $daySchedules->count() }} kelas</span>
                    </div>
                    @foreach ($daySchedules as $schedule)
                        <a class="ragen-agenda-entry" href="{{ route('admin.class-schedules.show', $schedule) }}">
                            <span class="ragen-agenda-time">{{ substr((string) $schedule->start_time, 0, 5) }}–{{ substr((string) $schedule->end_time, 0, 5) }}</span>
                            <span class="ragen-agenda-info">
                                <strong>{{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}</strong>
                                <small>{{ $schedule->coach?->user?->name ?? 'Coach belum ditentukan' }} · {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}</small>
                            </span>
                            <span class="ragen-agenda-arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</section>

<style>
.ragen-agenda{margin:24px 0 30px;padding:24px;background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 8px 24px rgba(15,35,68,.04)}
.ragen-agenda-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px}
.ragen-agenda-head h2{margin:5px 0;font-size:1.25rem;color:#102b4e}
.ragen-agenda-head p{margin:0;color:#64748b;font-size:.9rem}
.ragen-agenda-all{font-size:.875rem;font-weight:700;color:#1556a2;text-decoration:none;white-space:nowrap}
.ragen-agenda-day+.ragen-agenda-day{margin-top:22px}
.ragen-agenda-day-heading{display:flex;justify-content:space-between;gap:12px;padding:10px 14px;background:#edf4fb;border-radius:10px;color:#163c67;font-size:.875rem}
.ragen-agenda-day-heading span{font-weight:500;color:#64748b}
.ragen-agenda-entry{display:flex;align-items:center;gap:16px;padding:15px 12px;border-bottom:1px solid #edf2f7;color:inherit;text-decoration:none;transition:background .15s ease}
.ragen-agenda-entry:hover,.ragen-agenda-entry:focus-visible{background:#f7faff;outline-offset:2px}
.ragen-agenda-time{flex:0 0 110px;color:#0c4a80;font-size:.875rem;font-weight:700;font-variant-numeric:tabular-nums}
.ragen-agenda-info{flex:1;min-width:0;display:grid;gap:4px}
.ragen-agenda-info strong{font-size:.92rem;color:#17345a}
.ragen-agenda-info small{font-size:.8rem;color:#64748b}
.ragen-agenda-arrow{color:#2b67a6}
.ragen-agenda-empty{padding:24px;text-align:center;background:#f8fafc;color:#64748b;border-radius:12px}
@media(max-width:640px){.ragen-agenda{padding:16px}.ragen-agenda-head{align-items:flex-start;flex-direction:column}.ragen-agenda-entry{gap:10px}.ragen-agenda-time{flex-basis:88px;font-size:.8rem}.ragen-agenda-arrow{display:none}}
</style>

{{-- =========================================================
    STATISTICS
========================================================== --}}

<section class="statistics-grid">

    {{-- CUSTOMER --}}
    <div class="stat-card">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-blue">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="9"
                        cy="8"
                        r="3"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M3.5 19C4 15.6 6 14 9 14C12 14 14 15.6 14.5 19"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />

                    <path
                        d="M16 8.5C18.3 8.7 19.5 10.2 19.5 12C19.5 13.8 18.3 15.3 16 15.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Akun Customer
            </span>

        </div>

        <div class="stat-value">
            {{ number_format($statistics['customers'] ?? 0) }}
        </div>

        <div class="stat-description">
            Total akun customer terdaftar
        </div>

    </div>


    {{-- PESERTA --}}
    <div class="stat-card">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-aqua">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="8"
                        r="3.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Peserta
            </span>

        </div>

        <div class="stat-value">
            {{ number_format($statistics['participants'] ?? 0) }}
        </div>

        <div class="stat-description">
            Total profil peserta renang
        </div>

    </div>


    {{-- COACH --}}
    <div class="stat-card">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-purple">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="7"
                        r="3.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M5 20C5.5 15.8 8 14 12 14C16 14 18.5 15.8 19 20"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Coach Aktif
            </span>

        </div>

        <div class="stat-value">
            {{ number_format($statistics['coaches'] ?? 0) }}
        </div>

        <div class="stat-description">
            Coach dengan status aktif
        </div>

    </div>


    {{-- BOOKING --}}
    <div class="stat-card">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-orange">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        x="4"
                        y="5"
                        width="16"
                        height="15"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M8 3V7M16 3V7M4 10H20"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Total Booking
            </span>

        </div>

        <div class="stat-value">
            {{ number_format($statistics['bookings'] ?? 0) }}
        </div>

        <div class="stat-description">
            Seluruh riwayat booking kelas
        </div>

    </div>


    {{-- ORDER LUNAS --}}
    <div class="stat-card">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-green">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M5 5H19V19H5V5Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M8 12L10.5 14.5L16 9"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Transaksi Lunas
            </span>

        </div>

        <div class="stat-value">
            {{ number_format($statistics['paid_orders'] ?? 0) }}
        </div>

        <div class="stat-description">
            Order dengan status lunas
        </div>

    </div>


    {{-- REVENUE --}}
    <div class="stat-card stat-card-revenue">

        <div class="stat-card-top">

            <div class="stat-icon stat-icon-green">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="14"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M3 9H21"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M7 15H11"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <span class="stat-caption">
                Pendapatan
            </span>

        </div>

        <div class="stat-value stat-value-money">
            {{ $formatRupiah($paidRevenue) }}
        </div>

        <div class="stat-description">
            Total pembayaran berstatus lunas
        </div>

    </div>

</section>


{{-- =========================================================
    BOOKING + TRANSACTION
========================================================== --}}

<div class="dashboard-grid">

    {{-- =====================================================
        BOOKING TERBARU
    ====================================================== --}}

    <section class="dashboard-card">

        <div class="dashboard-card-header">

            <div>

                <h3>
                    Booking Terbaru
                </h3>

                <p>
                    Aktivitas booking peserta terbaru.
                </p>

            </div>

        </div>


        @if ($latestBookings->isEmpty())

            <div class="empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="4"
                            y="5"
                            width="16"
                            height="15"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />

                        <path
                            d="M8 3V7M16 3V7M4 10H20"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

                <strong>
                    Belum ada booking
                </strong>

                <span>
                    Data booking terbaru akan tampil di sini.
                </span>

            </div>

        @else

            <div class="activity-list">

                @foreach ($latestBookings as $booking)

                    @php
                        $schedule = $booking->classSchedule;

                        $bookingStatus =
                            $bookingStatusLabels[$booking->status]
                            ?? ucfirst($booking->status);

                        $bookingStatusClass =
                            match ($booking->status) {
                                'confirmed' => 'status-blue',
                                'completed' => 'status-green',
                                'cancelled' => 'status-red',
                                'rescheduled' => 'status-orange',
                                default => 'status-yellow',
                            };
                    @endphp

                    <div class="activity-item">

                        <div class="activity-main">

                            <div class="activity-avatar">
                                {{
                                    strtoupper(
                                        mb_substr(
                                            $booking->participant?->full_name ?? 'P',
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </div>


                            <div class="activity-content">

                                <div class="activity-title">
                                    {{ $booking->participant?->full_name ?? 'Peserta tidak tersedia' }}
                                </div>

                                <div class="activity-meta">

                                    @if ($schedule)

                                        <span>
                                            {{ $schedule->title }}
                                        </span>

                                        <span class="meta-dot"></span>

                                        <span>
                                            {{
                                                $schedule->class_date
                                                    ? $schedule->class_date->translatedFormat('d M Y')
                                                    : '-'
                                            }}
                                        </span>

                                    @else

                                        <span>
                                            Jadwal tidak tersedia
                                        </span>

                                    @endif

                                </div>


                                @if ($schedule)

                                    <div class="activity-secondary">

                                        {{ $schedule->coach?->user?->name ?? 'Coach belum ditentukan' }}

                                        @if ($schedule->location)

                                            · {{ $schedule->location->name }}

                                        @endif

                                    </div>

                                @endif

                            </div>

                        </div>


                        <div class="activity-side">

                            <span class="status-badge {{ $bookingStatusClass }}">
                                {{ $bookingStatus }}
                            </span>

                            <span class="activity-code">
                                {{ $booking->booking_code }}
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>


    {{-- =====================================================
        TRANSAKSI TERBARU
    ====================================================== --}}

    <section class="dashboard-card">

        <div class="dashboard-card-header">

            <div>

                <h3>
                    Transaksi Terbaru
                </h3>

                <p>
                    Order pembelian paket terbaru.
                </p>

            </div>

        </div>


        @if ($latestOrders->isEmpty())

            <div class="empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />

                        <path
                            d="M3 9H21"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />
                    </svg>

                </div>

                <strong>
                    Belum ada transaksi
                </strong>

                <span>
                    Transaksi terbaru akan tampil di sini.
                </span>

            </div>

        @else

            <div class="activity-list">

                @foreach ($latestOrders as $order)

                    @php
                        $orderStatus =
                            $orderStatusLabels[$order->status]
                            ?? ucfirst($order->status);

                        $orderStatusClass =
                            match ($order->status) {
                                'paid' => 'status-green',
                                'cancelled' => 'status-red',
                                'expired' => 'status-gray',
                                'refunded' => 'status-blue',
                                default => 'status-yellow',
                            };

                        $latestPayment = $order->payments
                            ->sortByDesc('id')
                            ->first();
                    @endphp

                    <div class="activity-item">

                        <div class="activity-main">

                            <div class="activity-avatar activity-avatar-order">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <rect
                                        x="4"
                                        y="4"
                                        width="16"
                                        height="16"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />

                                    <path
                                        d="M8 9H16M8 13H16M8 17H12"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>

                            </div>


                            <div class="activity-content">

                                <div class="activity-title">
                                    {{ $order->user?->name ?? 'Customer tidak tersedia' }}
                                </div>

                                <div class="activity-meta">

                                    <span>
                                        {{ $order->order_code }}
                                    </span>

                                    <span class="meta-dot"></span>

                                    <span>
                                        {{
                                            $order->ordered_at
                                                ? $order->ordered_at->translatedFormat('d M Y')
                                                : '-'
                                        }}
                                    </span>

                                </div>


                                <div class="activity-secondary">

                                    {{ $order->items->count() }}
                                    item

                                    @if ($latestPayment)

                                        · Pembayaran:
                                        {{ ucfirst($latestPayment->status) }}

                                    @endif

                                </div>

                            </div>

                        </div>


                        <div class="activity-side">

                            <span class="status-badge {{ $orderStatusClass }}">
                                {{ $orderStatus }}
                            </span>

                            <strong class="activity-amount">
                                {{ $formatRupiah($order->total_amount) }}
                            </strong>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>

</div>


{{-- =========================================================
    CUSTOMER TERBARU
========================================================== --}}

<section class="dashboard-card customer-section">

    <div class="dashboard-card-header">

        <div>

            <h3>
                Customer Terbaru
            </h3>

            <p>
                Akun customer yang terakhir terdaftar di sistem.
            </p>

        </div>

    </div>


    @if ($latestCustomers->isEmpty())

        <div class="empty-state">

            <div class="empty-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="8"
                        r="3.5"
                        stroke="currentColor"
                        stroke-width="1.7"
                    />

                    <path
                        d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <strong>
                Belum ada customer
            </strong>

            <span>
                Customer baru akan tampil di sini.
            </span>

        </div>

    @else

        <div class="customer-table-wrapper">

            <table class="customer-table">

                <thead>

                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach ($latestCustomers as $customer)

                        <tr>

                            <td>

                                <div class="customer-name-cell">

                                    <div class="customer-avatar">
                                        {{
                                            strtoupper(
                                                mb_substr(
                                                    $customer->name,
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </div>

                                    <div>

                                        <strong>
                                            {{ $customer->name }}
                                        </strong>

                                        <span>
                                            Customer
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                {{ $customer->email }}
                            </td>


                            <td>
                                {{ $customer->phone ?: '-' }}
                            </td>


                            <td>

                                @if ($customer->status === 'active')

                                    <span class="status-badge status-green">
                                        Aktif
                                    </span>

                                @elseif ($customer->status === 'suspended')

                                    <span class="status-badge status-red">
                                        Ditangguhkan
                                    </span>

                                @else

                                    <span class="status-badge status-gray">
                                        {{ ucfirst($customer->status) }}
                                    </span>

                                @endif

                            </td>


                            <td>
                                {{
                                    $customer->created_at
                                        ? $customer->created_at->translatedFormat('d M Y')
                                        : '-'
                                }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</section>

@endsection


@push('styles')

<style>
    .ragen-action-center { margin: 0 0 26px; padding: 22px; border: 1px solid #dce5f1; border-radius: 18px; background: #fff; box-shadow: 0 8px 28px rgba(15, 36, 70, .04); }
    .ragen-action-heading { display: flex; justify-content: space-between; align-items: start; gap: 14px; margin-bottom: 18px; }
    .ragen-action-eyebrow { display: block; color: #315c9a; font-size: 11px; font-weight: 800; letter-spacing: .12em; margin-bottom: 5px; }
    .ragen-action-heading h2 { margin: 0 0 4px; font-size: clamp(19px, 2vw, 24px); color: #142c51; }
    .ragen-action-heading p { margin: 0; font-size: 13px; color: #64748b; }
    .ragen-action-date { color: #526581; font-size: 12px; white-space: nowrap; }
    .ragen-action-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
    .ragen-action-item { display: flex; flex-direction: column; gap: 8px; min-height: 132px; padding: 16px; border: 1px solid #dce6f2; border-radius: 14px; background: #f8fbff; text-decoration: none; transition: transform .18s, border-color .18s, box-shadow .18s; }
    .ragen-action-item:hover, .ragen-action-item:focus-visible { transform: translateY(-2px); border-color: #5c8ed1; box-shadow: 0 8px 22px rgba(26, 66, 126, .10); outline-offset: 3px; }
    .ragen-action-number { color: #143969; font-size: 29px; font-weight: 800; line-height: 1; }
    .ragen-action-label { color: #33445f; font-size: 13px; line-height: 1.45; font-weight: 600; }
    .ragen-action-link { margin-top: auto; color: #2563a6; font-size: 12px; font-weight: 700; }
    @media (max-width: 1250px) { .ragen-action-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 740px) { .ragen-action-center { padding: 16px; } .ragen-action-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .ragen-action-heading { flex-wrap: wrap; } }
    @media (max-width: 440px) { .ragen-action-grid { grid-template-columns: 1fr; } .ragen-action-item { min-height: 105px; } }
    @media (prefers-reduced-motion: reduce) { .ragen-action-item { transition: none; } }


    /*
    |--------------------------------------------------------------------------
    | Welcome
    |--------------------------------------------------------------------------
    */

    .admin-welcome {
        position: relative;

        min-height: 175px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 30px;

        margin-bottom: 24px;
        padding: 28px 32px;

        overflow: hidden;

        border-radius: 18px;

        background:
            radial-gradient(
                circle at 85% 30%,
                rgba(103, 232, 249, 0.24),
                transparent 28%
            ),
            linear-gradient(
                135deg,
                #082f49 0%,
                #0369a1 100%
            );

        color: #ffffff;

        box-shadow:
            0 12px 30px rgba(8, 47, 73, 0.12);
    }

    .admin-welcome-content {
        position: relative;
        z-index: 2;
    }

    .admin-welcome-label {
        margin-bottom: 8px;

        color: #67e8f9;

        font-size: 10px;
        font-weight: 850;

        text-transform: uppercase;
        letter-spacing: 1.4px;
    }

    .admin-welcome h2 {
        font-size: 26px;
        font-weight: 850;

        letter-spacing: -0.5px;
    }

    .admin-welcome p {
        max-width: 580px;

        margin-top: 8px;

        color: rgba(255, 255, 255, 0.72);

        font-size: 13px;
        line-height: 1.7;
    }

    .admin-welcome-decoration {
        width: 135px;
        height: 135px;

        flex-shrink: 0;

        color: rgba(255, 255, 255, 0.65);
    }

    .admin-welcome-decoration img {
        display: block;
        object-fit: contain;
        width: 100%;
        height: 100%;
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .statistics-grid {
        display: grid;

        grid-template-columns:
            repeat(6, minmax(0, 1fr));

        gap: 14px;

        margin-bottom: 24px;
    }

    .stat-card {
        min-width: 0;

        padding: 18px;

        border: 1px solid #e2e8f0;
        border-radius: 16px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .stat-card-top {
        display: flex;
        align-items: center;

        gap: 9px;

        margin-bottom: 17px;
    }

    .stat-icon {
        width: 36px;
        height: 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;
    }

    .stat-icon svg {
        width: 19px;
        height: 19px;
    }

    .stat-icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .stat-icon-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-icon-purple {
        background: #faf5ff;
        color: #9333ea;
    }

    .stat-icon-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .stat-icon-green {
        background: #f0fdf4;
        color: #16a34a;
    }

    .stat-caption {
        overflow: hidden;

        color: #64748b;

        font-size: 11px;
        font-weight: 750;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .stat-value {
        overflow: hidden;

        color: #082f49;

        font-size: 27px;
        font-weight: 850;

        letter-spacing: -0.7px;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .stat-value-money {
        font-size: 20px;
    }

    .stat-description {
        margin-top: 5px;

        color: #94a3b8;

        font-size: 10px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Main Grid
    |--------------------------------------------------------------------------
    */

    .dashboard-grid {
        display: grid;

        grid-template-columns:
            minmax(0, 1fr)
            minmax(0, 1fr);

        gap: 20px;

        margin-bottom: 20px;
    }

    .dashboard-card {
        min-width: 0;

        overflow: hidden;

        border: 1px solid #e2e8f0;
        border-radius: 16px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .dashboard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 20px 22px;

        border-bottom: 1px solid #eef2f7;
    }

    .dashboard-card-header h3 {
        color: #082f49;

        font-size: 15px;
        font-weight: 800;
    }

    .dashboard-card-header p {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 11px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Activity
    |--------------------------------------------------------------------------
    */

    .activity-list {
        padding: 0 22px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 18px;

        padding: 17px 0;

        border-bottom: 1px solid #f1f5f9;
    }

    .activity-item:last-child {
        border-bottom: 0;
    }

    .activity-main {
        min-width: 0;

        display: flex;
        align-items: center;

        gap: 12px;
    }

    .activity-avatar {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background: #f0f9ff;
        color: #0284c7;

        font-size: 14px;
        font-weight: 850;
    }

    .activity-avatar-order {
        background: #f0fdf4;
        color: #15803d;
    }

    .activity-avatar-order svg {
        width: 20px;
        height: 20px;
    }

    .activity-content {
        min-width: 0;
    }

    .activity-title {
        overflow: hidden;

        color: #0f172a;

        font-size: 13px;
        font-weight: 750;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .activity-meta {
        display: flex;
        align-items: center;

        gap: 7px;

        margin-top: 4px;

        color: #64748b;

        font-size: 10px;
    }

    .activity-meta span {
        overflow: hidden;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .meta-dot {
        width: 3px;
        height: 3px;

        flex-shrink: 0;

        border-radius: 50%;

        background: #cbd5e1;
    }

    .activity-secondary {
        margin-top: 4px;

        overflow: hidden;

        color: #94a3b8;

        font-size: 10px;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .activity-side {
        display: flex;
        align-items: flex-end;
        flex-direction: column;

        gap: 6px;

        flex-shrink: 0;
    }

    .activity-code {
        color: #94a3b8;

        font-size: 9px;
        font-weight: 650;
    }

    .activity-amount {
        color: #082f49;

        font-size: 11px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 24px;

        padding: 4px 8px;

        border-radius: 999px;

        font-size: 9px;
        font-weight: 800;

        white-space: nowrap;
    }

    .status-green {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .status-yellow {
        background: #fffbeb;
        color: #b45309;
    }

    .status-orange {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-red {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-gray {
        background: #f1f5f9;
        color: #64748b;
    }


    /*
    |--------------------------------------------------------------------------
    | Customer Table
    |--------------------------------------------------------------------------
    */

    .customer-section {
        margin-bottom: 20px;
    }

    .customer-table-wrapper {
        width: 100%;

        overflow-x: auto;
    }

    .customer-table {
        width: 100%;

        border-collapse: collapse;
    }

    .customer-table th {
        padding: 12px 20px;

        border-bottom: 1px solid #e2e8f0;

        background: #f8fafc;
        color: #64748b;

        font-size: 9px;
        font-weight: 800;

        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;

        white-space: nowrap;
    }

    .customer-table td {
        padding: 15px 20px;

        border-bottom: 1px solid #f1f5f9;

        color: #475569;

        font-size: 11px;

        white-space: nowrap;
    }

    .customer-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .customer-table tbody tr:hover {
        background: #fafcff;
    }

    .customer-name-cell {
        display: flex;
        align-items: center;

        gap: 11px;
    }

    .customer-avatar {
        width: 36px;
        height: 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;

        background: #f0f9ff;
        color: #0284c7;

        font-size: 12px;
        font-weight: 850;
    }

    .customer-name-cell strong {
        display: block;

        color: #0f172a;

        font-size: 11px;
        font-weight: 750;
    }

    .customer-name-cell span {
        display: block;

        margin-top: 2px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-state {
        min-height: 210px;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        padding: 30px;

        text-align: center;
    }

    .empty-icon {
        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 12px;

        border-radius: 13px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .empty-icon svg {
        width: 23px;
        height: 23px;
    }

    .empty-state strong {
        color: #334155;

        font-size: 13px;
        font-weight: 750;
    }

    .empty-state span {
        margin-top: 5px;

        color: #94a3b8;

        font-size: 11px;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1350px) {

        .statistics-grid {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }
    }


    @media (max-width: 900px) {

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }


    @media (max-width: 700px) {

        .statistics-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .admin-welcome {
            padding: 24px;
        }

        .admin-welcome-decoration {
            display: none;
        }

        .admin-welcome h2 {
            font-size: 22px;
        }

        .activity-item {
            align-items: flex-start;
            flex-direction: column;
        }

        .activity-side {
            width: 100%;

            align-items: center;
            justify-content: space-between;
            flex-direction: row;

            padding-left: 54px;
        }
    }


    @media (max-width: 480px) {

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .stat-value-money {
            font-size: 24px;
        }

        .activity-side {
            padding-left: 0;
        }
    }

</style>

@endpush
