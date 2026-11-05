@extends('layouts.customer')

@section('title', 'Detail Paket')

@section('content')

<style>
    .package-detail-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 30px 24px 50px;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-header h1 {
        margin: 0;
        color: #0b2942;
        font-size: 26px;
        font-weight: 800;
    }

    .page-header p {
        margin: 7px 0 0;
        color: #718096;
        font-size: 13px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px solid #dce7ed;
        background: #ffffff;
        color: #0b2942;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: .2s ease;
    }

    .back-button:hover {
        background: #f4f9fb;
    }

    /* =========================
       PACKAGE HERO
    ========================== */

    .package-hero {
        background: linear-gradient(135deg, #0b2942, #126a8c);
        border-radius: 20px;
        padding: 28px;
        color: white;
        margin-bottom: 20px;
    }

    .package-hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .package-label {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .package-hero h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
    }

    .package-program {
        margin-top: 7px;
        font-size: 12px;
        color: rgba(255,255,255,.78);
    }

    .package-status {
        display: inline-flex;
        align-items: center;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .package-price {
        margin-top: 22px;
        font-size: 24px;
        font-weight: 800;
    }

    .package-price-label {
        margin-top: 3px;
        color: rgba(255,255,255,.72);
        font-size: 10px;
    }

    /* =========================
       CONTENT
    ========================== */

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr);
        gap: 20px;
    }

    .panel {
        background: #ffffff;
        border: 1px solid #e1ebf0;
        border-radius: 18px;
        padding: 22px;
    }

    .panel + .panel {
        margin-top: 20px;
    }

    .panel-title {
        margin: 0;
        color: #0b2942;
        font-size: 15px;
        font-weight: 800;
    }

    .panel-description {
        margin: 5px 0 18px;
        color: #718096;
        font-size: 11px;
        line-height: 1.6;
    }

    /* =========================
       SESSION
    ========================== */

    .session-box {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 18px;
    }

    .session-item {
        padding: 15px 12px;
        border-radius: 14px;
        background: #f5fafc;
        text-align: center;
        border: 1px solid #e2eef2;
    }

    .session-number {
        color: #126a8c;
        font-size: 20px;
        font-weight: 800;
    }

    .session-label {
        margin-top: 4px;
        color: #718096;
        font-size: 9px;
        font-weight: 600;
    }

    /* =========================
       PROGRESS
    ========================== */

    .progress-wrapper {
        margin-top: 20px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 7px;
    }

    .progress-label {
        color: #0b2942;
        font-size: 10px;
        font-weight: 700;
    }

    .progress-value {
        color: #718096;
        font-size: 10px;
    }

    .progress-track {
        width: 100%;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #e8f0f3;
    }

    .progress-bar {
        height: 100%;
        border-radius: inherit;
        background: #126a8c;
    }

    /* =========================
       INFO
    ========================== */

    .info-list {
        display: flex;
        flex-direction: column;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding: 13px 0;
        border-bottom: 1px solid #edf2f5;
    }

    .info-row:first-child {
        padding-top: 0;
    }

    .info-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        color: #718096;
        font-size: 11px;
    }

    .info-value {
        color: #0b2942;
        font-size: 11px;
        font-weight: 700;
        text-align: right;
    }

    /* =========================
       DESCRIPTION
    ========================== */

    .description-box {
        color: #526477;
        font-size: 12px;
        line-height: 1.8;
    }

    /* =========================
       ACTION
    ========================== */

    .action-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 0 16px;
        border-radius: 11px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        transition: .2s ease;
    }

    .action-primary {
        background: #0b2942;
        color: white;
    }

    .action-primary:hover {
        background: #126a8c;
    }

    .action-secondary {
        background: #f3f8fa;
        color: #0b2942;
        border: 1px solid #dce8ed;
    }

    .action-secondary:hover {
        background: #e9f3f6;
    }

    .notice {
        margin-top: 16px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f7fbfc;
        border: 1px solid #e1edf1;
        color: #718096;
        font-size: 10px;
        line-height: 1.6;
    }

    /* =========================
       BOOKINGS
    ========================== */

    .booking-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .booking-card {
        padding: 16px;
        border: 1px solid #e4edf1;
        border-radius: 14px;
        background: #fafdfe;
    }

    .booking-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
    }

    .booking-code {
        color: #0b2942;
        font-size: 12px;
        font-weight: 800;
    }

    .booking-status {
        padding: 5px 9px;
        border-radius: 999px;
        background: #edf7fa;
        color: #126a8c;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .booking-date {
        margin-top: 6px;
        color: #526477;
        font-size: 11px;
        line-height: 1.6;
    }

    .booking-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 13px;
        padding-top: 13px;
        border-top: 1px solid #e8eef1;
    }

    .booking-detail-label {
        color: #8a99a8;
        font-size: 9px;
        margin-bottom: 3px;
    }

    .booking-detail-value {
        color: #0b2942;
        font-size: 10px;
        font-weight: 700;
    }

    .booking-attendance {
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f3f8fa;
        color: #526477;
        font-size: 10px;
    }

    .empty-booking {
        padding: 25px;
        border: 1px dashed #d7e5eb;
        border-radius: 14px;
        text-align: center;
        color: #718096;
        font-size: 11px;
        background: #fbfdfe;
    }

    /* =========================
       RESPONSIVE
    ========================== */

    @media (max-width: 800px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .package-hero-top {
            flex-direction: column;
        }
    }

    @media (max-width: 600px) {
        .package-detail-page {
            padding: 22px 15px 40px;
        }

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .page-header h1 {
            font-size: 22px;
        }

        .package-hero {
            padding: 22px;
        }

        .package-hero h2 {
            font-size: 20px;
        }

        .session-box {
            grid-template-columns: 1fr;
        }

        .info-row {
            flex-direction: column;
            gap: 5px;
        }

        .info-value {
            text-align: left;
        }

        .booking-details {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="package-detail-page">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="page-header">

        <div>
            <h1>Detail Paket</h1>

            <p>
                Informasi lengkap paket latihan yang dimiliki peserta.
            </p>
        </div>

        <a
            href="{{ route('customer.packages.index') }}"
            class="back-button"
        >
            ← Kembali ke Paket Saya
        </a>

    </div>


    {{-- =========================
         DATA
    ========================== --}}

    @php
        $package = $participantPackage->package;
        $program = $package?->program;
        $participant = $participantPackage->participant;

        $totalSessions = (int) ($participantPackage->total_sessions ?? 0);

        $usedSessions = (int) (
            $participantPackage->used_sessions ?? 0
        );

        $remainingSessions = (int) (
            $participantPackage->remaining_sessions ?? 0
        );

        $progressPercentage = $totalSessions > 0
            ? min(
                100,
                max(
                    0,
                    round(
                        ($usedSessions / $totalSessions) * 100
                    )
                )
            )
            : 0;

        $statusLabel = match (
            $participantPackage->status ?? null
        ) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $participantPackage->status
                    ?? 'Tidak diketahui'
                )
            ),
        };

        $participantIsActive = $participant?->status === 'active';

        $canBook = $participantIsActive
            && $participantPackage->canBeUsed();
    @endphp


    {{-- =========================
         PACKAGE HERO
    ========================== --}}

    <section class="package-hero">

        <div class="package-hero-top">

            <div>

                <div class="package-label">
                    PAKET LATIHAN
                </div>

                <h2>
                    {{ $package?->name ?? 'Paket tidak tersedia' }}
                </h2>

                <div class="package-program">
                    Program:
                    {{ $program?->name ?? 'Program tidak tersedia' }}
                </div>

            </div>

            <div class="package-status">
                {{ $statusLabel }}
            </div>

        </div>


        @if($package?->price !== null)

            <div class="package-price">
                @if ($package->hasActivePromo())
                    <div style="font-size:14px;color:#64748b;text-decoration:line-through;margin-bottom:5px;">{{ $package->formatted_price }}</div>
                    <div style="color:#15803d;">Rp {{ number_format($package->getEffectivePrice(), 0, ",", ".") }}</div>
                @else
                    {{ $package->formatted_price }}
                @endif
            </div>

            <div class="package-price-label">
                @if ($package->hasActivePromo())
                    {{ $package->promo_name ?: "Harga Promo Paket" }}
                @else
                    Harga paket
                @endif
            </div>

        @endif

    </section>


    <div class="content-grid">

        {{-- =========================
             LEFT CONTENT
        ========================== --}}

        <div>

            {{-- =========================
                 PESERTA
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Informasi Peserta
                </h2>

                <p class="panel-description">
                    Peserta yang menggunakan paket latihan ini.
                </p>

                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Nama Peserta
                        </span>

                        <span class="info-value">
                            {{ $participant?->full_name ?? '-' }}
                        </span>

                    </div>

                </div>

            </section>


            {{-- =========================
                 SESSION SUMMARY
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Ringkasan Paket
                </h2>

                <p class="panel-description">
                    Berikut informasi penggunaan paket latihan peserta.
                </p>


                <div class="session-box">

                    <div class="session-item">

                        <div class="session-number">
                            {{ $totalSessions }}
                        </div>

                        <div class="session-label">
                            Total Sesi
                        </div>

                    </div>


                    <div class="session-item">

                        <div class="session-number">
                            {{ $usedSessions }}
                        </div>

                        <div class="session-label">
                            Sesi Terpakai
                        </div>

                    </div>


                    <div class="session-item">

                        <div class="session-number">
                            {{ $remainingSessions }}
                        </div>

                        <div class="session-label">
                            Sesi Tersisa
                        </div>

                    </div>

                </div>


                <div class="progress-wrapper">

                    <div class="progress-header">

                        <span class="progress-label">
                            Progress penggunaan paket
                        </span>

                        <span class="progress-value">
                            {{ $progressPercentage }}%
                        </span>

                    </div>

                    <div class="progress-track">

                        <div
                            class="progress-bar"
                            style="width: {{ $progressPercentage }}%;"
                        ></div>

                    </div>

                </div>

            </section>


            {{-- =========================
                 PACKAGE INFORMATION
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Informasi Paket
                </h2>

                <p class="panel-description">
                    Detail paket yang digunakan peserta.
                </p>


                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Nama Paket
                        </span>

                        <span class="info-value">
                            {{ $package?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Program
                        </span>

                        <span class="info-value">
                            {{ $program?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Jumlah Pertemuan
                        </span>

                        <span class="info-value">
                            {{ $package?->total_sessions ?? $totalSessions }}
                            sesi
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Durasi / Pertemuan
                        </span>

                        <span class="info-value">
                            {{ $program?->duration_minutes ?? '-' }}
                            menit
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Masa Berlaku
                        </span>

                        <span class="info-value">
                            @if($package?->validity_days)
                                {{ $package->validity_days }} hari
                            @else
                                Menyesuaikan
                            @endif
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Status
                        </span>

                        <span class="info-value">
                            {{ $statusLabel }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Tanggal Mulai
                        </span>

                        <span class="info-value">
                            {{ $participantPackage->start_date?->format('d-m-Y') ?? '-' }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Tanggal Berakhir
                        </span>

                        <span class="info-value">
                            {{ $participantPackage->expiry_date?->format('d-m-Y') ?? 'Tidak dibatasi' }}
                        </span>

                    </div>

                </div>

            </section>


            {{-- =========================
                 DESCRIPTION
            ========================== --}}

            @if($package?->description)

                <section class="panel">

                    <h2 class="panel-title">
                        Deskripsi Paket
                    </h2>

                    <p class="panel-description">
                        Informasi mengenai paket latihan.
                    </p>

                    <div class="description-box">
                        {!! nl2br(e($package->description)) !!}
                    </div>

                </section>

            @endif


            {{-- =========================
                 BOOKING HISTORY
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Riwayat Latihan
                </h2>

                <p class="panel-description">
                    Riwayat jadwal latihan yang menggunakan paket ini.
                </p>


                @forelse($participantPackage->bookings as $booking)

                    @php
                        $schedule = $booking->classSchedule;
                        $coach = $schedule?->coach;
                        $location = $schedule?->location;
                        $attendance = $booking->attendance;
                    @endphp


                    <div class="booking-list">

                        <article class="booking-card">

                            <div class="booking-top">

                                <div>

                                    <div class="booking-code">
                                        {{ $booking->booking_code ?? 'Booking #' . $booking->id }}
                                    </div>

                                    @if($schedule?->class_date)

                                        <div class="booking-date">

                                            {{ $schedule->class_date->translatedFormat('d F Y') }}

                                            @if($schedule->start_time)
                                                • {{ $schedule->start_time }}
                                            @endif

                                            @if($schedule->end_time)
                                                - {{ $schedule->end_time }}
                                            @endif

                                        </div>

                                    @endif

                                </div>


                                <span class="booking-status">
                                    {{ $booking->status_label }}
                                </span>

                            </div>


                            <div class="booking-details">

                                <div>

                                    <div class="booking-detail-label">
                                        Kelas
                                    </div>

                                    <div class="booking-detail-value">
                                        {{ $schedule?->title ?? $schedule?->program?->name ?? '-' }}
                                    </div>

                                </div>


                                <div>

                                    <div class="booking-detail-label">
                                        Pelatih
                                    </div>

                                    <div class="booking-detail-value">
                                        {{ $coach?->user?->name ?? '-' }}
                                    </div>

                                </div>


                                <div>

                                    <div class="booking-detail-label">
                                        Lokasi
                                    </div>

                                    <div class="booking-detail-value">
                                        {{ $location?->name ?? '-' }}
                                    </div>

                                </div>


                                <div>

                                    <div class="booking-detail-label">
                                        Booking
                                    </div>

                                    <div class="booking-detail-value">
                                        {{ $booking->booked_at?->format('d-m-Y H:i') ?? '-' }}
                                    </div>

                                </div>

                            </div>


                            @if($attendance)

                                <div class="booking-attendance">

                                    <strong>
                                        Kehadiran:
                                    </strong>

                                    {{ $attendance->status_label }}

                                    @if($attendance->checked_at)

                                        • Dicatat
                                        {{ $attendance->checked_at->format('d-m-Y H:i') }}

                                    @endif

                                </div>

                            @endif

                        </article>

                    </div>

                @empty

                    <div class="empty-booking">
                        Belum ada riwayat booking untuk paket ini.
                    </div>

                @endforelse

            </section>

        </div>


        {{-- =========================
             RIGHT CONTENT
        ========================== --}}

        <aside>

            {{-- =========================
                 ACTION
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Aksi
                </h2>

                <p class="panel-description">
                    Lanjutkan pengaturan latihan peserta.
                </p>


                <div class="action-box">

                    @if($canBook)

                        <a
                            href="{{ route('customer.bookings.index') }}"
                            class="action-button action-primary"
                        >
                            Atur Jadwal Latihan
                        </a>

                    @endif


                    <a
                        href="{{ route('customer.packages.index') }}"
                        class="action-button action-secondary"
                    >
                        Lihat Paket Saya
                    </a>


                    <a
                        href="{{ route('customer.participants.index') }}"
                        class="action-button action-secondary"
                    >
                        Kelola Peserta
                    </a>

                </div>


                @if(!$participantIsActive)

                    <div class="notice">
                        Peserta sedang tidak aktif. Riwayat paket tetap
                        dapat dilihat, tetapi peserta belum dapat
                        membuat booking latihan baru.
                    </div>

                @elseif($participantPackage->isExpired())

                    <div class="notice">
                        Masa berlaku paket ini sudah berakhir.
                        Paket tidak dapat digunakan untuk booking baru.
                    </div>

                @elseif($participantPackage->isCompleted())

                    <div class="notice">
                        Paket ini sudah tidak memiliki sesi latihan
                        yang dapat digunakan.
                    </div>

                @elseif(!$participantPackage->isActive())

                    <div class="notice">
                        Paket ini tidak sedang berstatus aktif.
                    </div>

                @elseif(!$participantPackage->hasStarted())

                    <div class="notice">
                        Paket belum memasuki tanggal mulai sehingga
                        belum dapat digunakan untuk booking.
                    </div>

                @endif

            </section>


            {{-- =========================
                 SESSION INFORMATION
            ========================== --}}

            <section class="panel">

                <h2 class="panel-title">
                    Penggunaan Sesi
                </h2>

                <p class="panel-description">
                    Ringkasan penggunaan sesi paket.
                </p>


                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Total sesi
                        </span>

                        <span class="info-value">
                            {{ $totalSessions }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Sudah digunakan
                        </span>

                        <span class="info-value">
                            {{ $usedSessions }}
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Sisa sesi
                        </span>

                        <span class="info-value">
                            {{ $remainingSessions }}
                        </span>

                    </div>

                </div>

            </section>

        </aside>

    </div>

</div>

@endsection
