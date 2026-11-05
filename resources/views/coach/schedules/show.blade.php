@extends('layouts.coach')

@section('title', 'Detail Kelas')
@section('topbar-title', 'Detail Kelas')

@push('styles')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--ocean);
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .class-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(
            135deg,
            #073c66 0%,
            #0a6093 55%,
            #1499c7 100%
        );
        border-radius: 24px;
        padding: 27px;
        color: white;
        margin-bottom: 20px;
    }

    .class-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        right: -60px;
        top: -90px;
    }

    .class-hero::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 50%;
        right: -90px;
        bottom: -220px;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .hero-label {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        font-size: 9px;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .hero-title {
        font-size: 24px;
        line-height: 1.3;
        font-weight: 800;
        max-width: 600px;
    }

    .hero-program {
        font-size: 11px;
        opacity: .78;
        margin-top: 7px;
    }

    .hero-status {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 25px;
    }

    .hero-info {
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 15px;
        padding: 13px;
    }

    .hero-info-label {
        display: block;
        font-size: 8px;
        opacity: .65;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 5px;
    }

    .hero-info-value {
        display: block;
        font-size: 10px;
        line-height: 1.5;
        font-weight: 700;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 18px;
    }

    .summary-label {
        color: var(--muted);
        font-size: 9px;
        margin-bottom: 8px;
    }

    .summary-value {
        color: var(--navy);
        font-size: 20px;
        font-weight: 800;
    }

    .summary-description {
        color: var(--muted);
        font-size: 9px;
        margin-top: 5px;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        gap: 20px;
        align-items: start;
    }

    .panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 21px;
        padding: 22px;
    }

    .panel + .panel {
        margin-top: 17px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .panel-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .panel-description {
        color: var(--muted);
        font-size: 10px;
        line-height: 1.6;
        margin-top: 4px;
    }

    .count-badge {
        padding: 6px 9px;
        border-radius: 999px;
        background: var(--aqua-soft);
        color: var(--ocean);
        font-size: 8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .participant-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .participant-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 15px;
        display: grid;
        grid-template-columns: 43px minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
    }

    .participant-avatar {
        width: 43px;
        height: 43px;
        border-radius: 13px;
        background: var(--blue-soft);
        color: var(--ocean);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
    }

    .participant-name {
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
    }

    .participant-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 5px 12px;
        color: var(--muted);
        font-size: 9px;
        margin-top: 5px;
    }

    .participant-package {
        color: var(--ocean);
        font-size: 9px;
        font-weight: 700;
        margin-top: 5px;
    }

    .attendance-area {
        min-width: 145px;
        text-align: right;
    }

    .attendance-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .attendance-empty {
        background: #f3f7f9;
        color: var(--muted);
    }

    .attendance-present {
        background: #eaf8f0;
        color: #18834b;
    }

    .attendance-permission {
        background: #fff7e5;
        color: #a66a00;
    }

    .attendance-sick {
        background: #fff1e8;
        color: #b65b1c;
    }

    .attendance-absent {
        background: #fff0f0;
        color: #c13b3b;
    }

    .attendance-button {
        border: 0;
        min-height: 34px;
        padding: 0 11px;
        border-radius: 10px;
        background: var(--navy);
        color: white;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s ease;
    }

    .attendance-button:hover {
        background: var(--ocean);
    }

    .attendance-recorded {
        display: block;
        color: var(--muted);
        font-size: 8px;
        line-height: 1.5;
    }

    .attendance-unavailable {
        display: block;
        max-width: 180px;
        margin-left: auto;
        color: var(--muted);
        font-size: 8px;
        font-weight: 700;
        line-height: 1.5;
    }

    .deducted-text {
        display: block;
        margin-top: 4px;
        color: #18834b;
        font-size: 8px;
        font-weight: 700;
    }

    .not-deducted-text {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 8px;
        font-weight: 700;
    }

    .progress-action {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }

    .progress-status {
        display: block;
        margin-bottom: 6px;
        font-size: 8px;
        font-weight: 800;
    }

    .progress-status.completed {
        color: #18834b;
    }

    .progress-status.pending {
        color: #a66a00;
    }

    .progress-score {
        display: block;
        color: var(--muted);
        font-size: 8px;
        line-height: 1.5;
        margin-bottom: 7px;
    }

    .progress-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0 10px;
        border-radius: 9px;
        background: #eef9fd;
        border: 1px solid #cfeaf4;
        color: var(--ocean);
        font-size: 8px;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .progress-button:hover {
        background: var(--ocean);
        border-color: var(--ocean);
        color: white;
    }

    .info-list {
        display: flex;
        flex-direction: column;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    .info-label {
        color: var(--muted);
        font-size: 9px;
    }

    .info-value {
        color: var(--navy);
        font-size: 9px;
        line-height: 1.5;
        font-weight: 700;
        text-align: right;
    }

    .location-box {
        margin-top: 14px;
        padding: 14px;
        border-radius: 14px;
        background: #f8fcfd;
        border: 1px solid var(--border);
    }

    .location-box strong {
        display: block;
        color: var(--navy);
        font-size: 10px;
    }

    .location-box p {
        color: var(--muted);
        font-size: 9px;
        line-height: 1.6;
        margin-top: 5px;
    }

    .maps-link {
        display: inline-flex;
        margin-top: 8px;
        color: var(--ocean);
        font-size: 9px;
        font-weight: 700;
    }

    .notice-box {
        padding: 14px;
        border-radius: 14px;
        background: #f3fbfd;
        border: 1px solid #d5edf3;
        color: var(--muted);
        font-size: 9px;
        line-height: 1.65;
    }

    .notice-box strong {
        color: var(--navy);
    }

    .empty-state {
        padding: 35px 20px;
        border: 1px dashed #c8dfe8;
        border-radius: 16px;
        text-align: center;
        background: #fbfdfe;
    }

    .empty-state strong {
        display: block;
        color: var(--navy);
        font-size: 11px;
    }

    .empty-state p {
        color: var(--muted);
        font-size: 9px;
        line-height: 1.6;
        margin-top: 5px;
    }

    .alert-success,
    .alert-error {
        border-radius: 14px;
        padding: 13px 15px;
        margin-bottom: 18px;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.6;
    }

    .alert-success {
        background: #eaf8f0;
        border: 1px solid #c7ead5;
        color: #187544;
    }

    .alert-error {
        background: #fff0f0;
        border: 1px solid #f3cccc;
        color: #b83232;
    }

    .attendance-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .attendance-modal.show {
        display: flex;
    }

    .modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(4, 31, 51, .58);
        backdrop-filter: blur(3px);
    }

    .modal-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 520px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        background: white;
        border-radius: 22px;
        box-shadow: 0 25px 70px rgba(0, 37, 62, .24);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        padding: 21px 22px 16px;
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .modal-subtitle {
        color: var(--muted);
        font-size: 9px;
        line-height: 1.6;
        margin-top: 4px;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 10px;
        background: #f2f7f9;
        color: var(--navy);
        font-size: 17px;
        cursor: pointer;
    }

    .modal-body {
        padding: 21px 22px 22px;
    }

    .selected-participant {
        padding: 13px;
        border-radius: 13px;
        background: #f6fbfd;
        border: 1px solid var(--border);
        margin-bottom: 18px;
    }

    .selected-participant-label {
        color: var(--muted);
        font-size: 8px;
        margin-bottom: 4px;
    }

    .selected-participant-name {
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
    }

    .status-label {
        display: block;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .status-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 9px;
    }

    .status-option {
        position: relative;
    }

    .status-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .status-option label {
        display: block;
        border: 1px solid var(--border);
        border-radius: 13px;
        padding: 12px;
        cursor: pointer;
        transition: .2s ease;
        min-height: 72px;
    }

    .status-option label:hover {
        border-color: #9bcfe0;
    }

    .status-option input:checked + label {
        border-color: var(--ocean);
        background: #f1fbfe;
        box-shadow: 0 0 0 2px rgba(12, 137, 186, .08);
    }

    .option-title {
        display: block;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .option-description {
        display: block;
        color: var(--muted);
        font-size: 8px;
        line-height: 1.5;
        margin-top: 4px;
    }

    .deduction-notice {
        margin-top: 13px;
        padding: 12px 13px;
        border-radius: 12px;
        font-size: 9px;
        line-height: 1.6;
    }

    .deduction-notice.deduct {
        background: #fff8e8;
        border: 1px solid #f1dfa9;
        color: #8a6308;
    }

    .deduction-notice.no-deduct {
        background: #edf9f2;
        border: 1px solid #cbe9d7;
        color: #247246;
    }

    .form-group {
        margin-top: 17px;
    }

    .form-label {
        display: block;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .form-textarea {
        width: 100%;
        min-height: 90px;
        resize: vertical;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 11px 12px;
        color: var(--navy);
        font: inherit;
        font-size: 10px;
        outline: none;
    }

    .form-textarea:focus {
        border-color: var(--ocean);
        box-shadow: 0 0 0 3px rgba(12, 137, 186, .07);
    }

    .form-help {
        color: var(--muted);
        font-size: 8px;
        margin-top: 5px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 20px;
    }

    .button-secondary,
    .button-primary {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 800;
        cursor: pointer;
    }

    .button-secondary {
        background: white;
        border: 1px solid var(--border);
        color: var(--navy);
    }

    .button-primary {
        border: 0;
        background: var(--navy);
        color: white;
    }

    .button-primary:hover {
        background: var(--ocean);
    }

    .button-primary:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    @media (max-width: 950px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .hero-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 650px) {
        .hero-top {
            flex-direction: column;
        }

        .hero-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .participant-card {
            grid-template-columns: 43px minmax(0, 1fr);
        }

        .attendance-area {
            grid-column: 2;
            text-align: left;
        }

        .attendance-unavailable {
            margin-left: 0;
        }

        .status-options {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Booking yang ditampilkan
    |--------------------------------------------------------------------------
    |
    | Pending, confirmed, dan completed tetap ditampilkan supaya Coach
    | dapat melihat peserta, attendance, serta progress sebelumnya.
    |
    */
    $activeBookings = $schedule->bookings
        ->whereIn('status', ['pending', 'confirmed', 'completed'])
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Booking yang sedang menggunakan slot
    |--------------------------------------------------------------------------
    */
    $slotBookings = $activeBookings
        ->whereIn('status', ['pending', 'confirmed'])
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Statistik Kehadiran
    |--------------------------------------------------------------------------
    */
    $attendedCount = $activeBookings
        ->filter(fn ($booking) => $booking->attendance !== null)
        ->count();

    $remainingAttendance = $activeBookings
        ->filter(fn ($booking) => $booking->attendance === null)
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Waktu Kelas
    |--------------------------------------------------------------------------
    |
    | UI hanya menawarkan pencatatan kehadiran setelah waktu mulai kelas.
    | Controller tetap menjadi pengaman utama pada sisi server.
    |
    */
    $classStartAt = \Carbon\Carbon::parse(
        $schedule->class_date->format('Y-m-d')
        . ' '
        . $schedule->start_time
    );

    $classHasStarted = now()->greaterThanOrEqualTo($classStartAt);

    $attendanceAvailable =
        $schedule->status === 'scheduled'
        && $classHasStarted;

    $scheduleStatusLabel = match ($schedule->status) {
        'scheduled' => 'Terjadwal',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => ucfirst($schedule->status),
    };
@endphp

<a
    href="{{ route('coach.schedules.index') }}"
    class="back-link"
>
    ← Kembali ke Jadwal Saya
</a>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<section class="class-hero">

    <div class="hero-content">

        <div class="hero-top">

            <div>
                <span class="hero-label">
                    {{ $schedule->program?->name ?? 'Program Renang' }}
                </span>

                <h1 class="hero-title">
                    {{ $schedule->title ?: 'Kelas Renang' }}
                </h1>

                <p class="hero-program">
                    Ditangani oleh
                    {{ $schedule->coach?->user?->name ?? $coach->user->name }}
                </p>
            </div>

            <span class="hero-status">
                {{ $scheduleStatusLabel }}
            </span>

        </div>

        <div class="hero-grid">

            <div class="hero-info">
                <span class="hero-info-label">Tanggal</span>

                <span class="hero-info-value">
                    {{ $schedule->class_date->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div class="hero-info">
                <span class="hero-info-label">Waktu</span>

                <span class="hero-info-value">
                    {{ substr($schedule->start_time, 0, 5) }}
                    -
                    {{ substr($schedule->end_time, 0, 5) }}
                </span>
            </div>

            <div class="hero-info">
                <span class="hero-info-label">Lokasi</span>

                <span class="hero-info-value">
                    {{ $schedule->location?->name ?? 'Belum ditentukan' }}
                </span>
            </div>

            <div class="hero-info">
                <span class="hero-info-label">Level</span>

                <span class="hero-info-value">
                    {{ $schedule->swimming_level === 'all'
                        ? 'Semua Level'
                        : ucfirst($schedule->swimming_level) }}
                </span>
            </div>

        </div>

    </div>

</section>

<div class="summary-grid">

    <div class="summary-card">
        <div class="summary-label">
            Slot Terisi
        </div>

        <div class="summary-value">
            {{ $slotBookings->count() }}
        </div>

        <div class="summary-description">
            dari kapasitas {{ $schedule->capacity }} peserta
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-label">
            Sudah Diabsen
        </div>

        <div class="summary-value">
            {{ $attendedCount }}
        </div>

        <div class="summary-description">
            peserta memiliki data kehadiran
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-label">
            Belum Diabsen
        </div>

        <div class="summary-value">
            {{ $remainingAttendance }}
        </div>

        <div class="summary-description">
            menunggu pencatatan kehadiran
        </div>
    </div>

</div>

<div class="content-grid">

    <div>

        <section class="panel">

            <div class="panel-header">

                <div>
                    <h2 class="panel-title">
                        Daftar Peserta
                    </h2>

                    <p class="panel-description">
                        Catat kehadiran peserta setelah kelas dimulai.
                    </p>
                </div>

                <span class="count-badge">
                    {{ $activeBookings->count() }} peserta
                </span>

            </div>

            @if($activeBookings->isEmpty())

                <div class="empty-state">

                    <strong>
                        Belum ada peserta
                    </strong>

                    <p>
                        Belum ada peserta untuk jadwal kelas ini.
                    </p>

                </div>

            @else

                <div class="participant-list">

                    @foreach($activeBookings as $booking)

                        @php
                            $participantName =
                                $booking->participant?->full_name
                                ?? $booking->participant?->name
                                ?? 'Peserta';

                            $initials = collect(explode(' ', $participantName))
                                ->filter()
                                ->take(2)
                                ->map(
                                    fn ($word) =>
                                        strtoupper(substr($word, 0, 1))
                                )
                                ->implode('');

                            $attendanceClass = match ($booking->attendance?->status) {
                                'present' => 'attendance-present',
                                'permission' => 'attendance-permission',
                                'sick' => 'attendance-sick',
                                'absent' => 'attendance-absent',
                                default => 'attendance-empty',
                            };

                            $attendanceLabel =
                                $booking->attendance?->status_label
                                ?? 'Belum Diabsen';

                            $canRecordAttendance =
                                $booking->attendance === null
                                && $booking->status === 'confirmed'
                                && $attendanceAvailable;
                        @endphp

                        <article class="participant-card">

                            <div class="participant-avatar">
                                {{ $initials ?: 'P' }}
                            </div>

                            <div>

                                <div class="participant-name">
                                    {{ $participantName }}
                                </div>

                                <div class="participant-meta">

                                    <span>
                                        {{ $booking->booking_code }}
                                    </span>

                                    <span>
                                        {{ ucfirst($booking->status) }}
                                    </span>

                                </div>

                                <div class="participant-package">

                                    {{ $booking->participantPackage?->package?->name
                                        ?? 'Paket tidak tersedia' }}

                                    ·

                                    {{ $booking->participantPackage?->remaining_sessions ?? 0 }}
                                    sesi tersisa

                                </div>

                            </div>

                            <div class="attendance-area">

                                <span class="attendance-badge {{ $attendanceClass }}">
                                    {{ $attendanceLabel }}
                                </span>

                                @if($booking->attendance)

                                    <span class="attendance-recorded">
                                        Dicatat
                                        {{ $booking->attendance->checked_at
                                            ? $booking->attendance->checked_at->format('d-m-Y H:i')
                                            : '-' }}
                                    </span>

                                    @if($booking->session_deducted)

                                        <span class="deducted-text">
                                            1 sesi terpakai
                                        </span>

                                    @else

                                        <span class="not-deducted-text">
                                            Sesi tidak dipotong
                                        </span>

                                    @endif

                                @elseif($canRecordAttendance)

                                    <button
                                        type="button"
                                        class="attendance-button open-attendance-modal"
                                        data-booking-id="{{ $booking->id }}"
                                        data-participant-name="{{ $participantName }}"
                                        data-booking-code="{{ $booking->booking_code }}"
                                        data-package-name="{{ $booking->participantPackage?->package?->name ?? 'Paket' }}"
                                        data-remaining-sessions="{{ $booking->participantPackage?->remaining_sessions ?? 0 }}"
                                    >
                                        Catat Kehadiran
                                    </button>

                                @elseif($booking->status === 'pending')

                                    <span class="attendance-unavailable">
                                        Menunggu konfirmasi booking dari Admin.
                                    </span>

                                @elseif($booking->status === 'confirmed' && !$classHasStarted)

                                    <span class="attendance-unavailable">
                                        Kehadiran dapat dicatat setelah kelas dimulai.
                                    </span>

                                @elseif($schedule->status === 'cancelled')

                                    <span class="attendance-unavailable">
                                        Kelas telah dibatalkan.
                                    </span>

                                @elseif($schedule->status === 'completed')

                                    <span class="attendance-unavailable">
                                        Kelas telah selesai.
                                    </span>

                                @elseif($booking->status === 'completed')

                                    <span class="attendance-unavailable">
                                        Booking telah selesai.
                                    </span>

                                @else

                                    <span class="attendance-unavailable">
                                        Pencatatan kehadiran tidak tersedia.
                                    </span>

                                @endif

                                @if($booking->attendance?->status === 'present')

                                    <div class="progress-action">

                                        @if($booking->progressRecord)

                                            <span class="progress-status completed">
                                                ✓ Sudah Dinilai
                                            </span>

                                            <span class="progress-score">
                                                Level {{ $booking->progressRecord->level_label }}
                                                ·
                                                Rata-rata {{ number_format($booking->progressRecord->average_score, 1) }}/5
                                            </span>

                                            <a
                                                href="{{ route('coach.progress.edit', $booking->id) }}"
                                                class="progress-button"
                                            >
                                                Lihat / Edit Perkembangan
                                            </a>

                                        @else

                                            <span class="progress-status pending">
                                                Belum Dinilai
                                            </span>

                                            <a
                                                href="{{ route('coach.progress.edit', $booking->id) }}"
                                                class="progress-button"
                                            >
                                                Isi Perkembangan
                                            </a>

                                        @endif

                                    </div>

                                @endif

                            </div>

                        </article>

                    @endforeach

                </div>

            @endif

        </section>

    </div>

    <aside>

        <section class="panel">

            <div class="panel-header">

                <div>
                    <h2 class="panel-title">
                        Informasi Kelas
                    </h2>

                    <p class="panel-description">
                        Detail jadwal latihan.
                    </p>
                </div>

            </div>

            <div class="info-list">

                <div class="info-row">
                    <span class="info-label">
                        Program
                    </span>

                    <span class="info-value">
                        {{ $schedule->program?->name ?? '-' }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        Pelatih
                    </span>

                    <span class="info-value">
                        {{ $schedule->coach?->user?->name ?? $coach->user->name }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        Level
                    </span>

                    <span class="info-value">
                        {{ $schedule->swimming_level === 'all'
                            ? 'Semua Level'
                            : ucfirst($schedule->swimming_level) }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        Kapasitas
                    </span>

                    <span class="info-value">
                        {{ $schedule->capacity }} peserta
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        Status
                    </span>

                    <span class="info-value">
                        {{ $scheduleStatusLabel }}
                    </span>
                </div>

            </div>

        </section>

        <section class="panel">

            <div class="panel-header">
                <div>
                    <h2 class="panel-title">
                        Lokasi Latihan
                    </h2>
                </div>
            </div>

            @if($schedule->location)

                <div class="location-box">

                    <strong>
                        {{ $schedule->location->name }}
                    </strong>

                    <p>
                        {{ $schedule->location->address }}
                    </p>

                    @if($schedule->location->city)

                        <p>
                            {{ $schedule->location->city }}
                        </p>

                    @endif

                    @if($schedule->location->google_maps_url)

                        <a
                            href="{{ $schedule->location->google_maps_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="maps-link"
                        >
                            Buka Google Maps →
                        </a>

                    @endif

                </div>

            @else

                <div class="notice-box">
                    Lokasi kelas belum tersedia.
                </div>

            @endif

        </section>

        <section class="panel">

            <div class="notice-box">

                <strong>
                    Ketentuan Kehadiran
                </strong>

                <br><br>

                <b>Hadir</b> dan <b>Tidak Hadir</b>
                menggunakan 1 sesi paket.

                <br><br>

                <b>Izin</b> dan <b>Sakit</b>
                tidak memotong sesi.

                <br><br>

                Kehadiran hanya dapat dicatat untuk booking
                yang sudah dikonfirmasi dan setelah waktu
                mulai kelas tercapai.

            </div>

        </section>

    </aside>

</div>


{{-- ========================================================= --}}
{{-- MODAL ATTENDANCE                                          --}}
{{-- ========================================================= --}}

<div
    class="attendance-modal"
    id="attendanceModal"
>

    <div
        class="modal-backdrop"
        id="attendanceModalBackdrop"
    ></div>

    <div class="modal-card">

        <div class="modal-header">

            <div>

                <h2 class="modal-title">
                    Catat Kehadiran
                </h2>

                <p class="modal-subtitle">
                    Pilih status kehadiran peserta dengan benar.
                    Setelah tersimpan, data kehadiran tidak dapat
                    dicatat ulang.
                </p>

            </div>

            <button
                type="button"
                class="modal-close"
                id="attendanceModalClose"
            >
                ×
            </button>

        </div>

        <div class="modal-body">

            <div class="selected-participant">

                <div class="selected-participant-label">
                    Peserta
                </div>

                <div
                    class="selected-participant-name"
                    id="modalParticipantName"
                >
                    -
                </div>

                <div
                    class="modal-subtitle"
                    id="modalParticipantDetail"
                >
                    -
                </div>

            </div>

            <form
                method="POST"
                action=""
                id="attendanceForm"
            >

                @csrf

                <span class="status-label">
                    Status Kehadiran
                </span>

                <div class="status-options">

                    <div class="status-option">

                        <input
                            type="radio"
                            name="status"
                            value="present"
                            id="statusPresent"
                            required
                        >

                        <label for="statusPresent">

                            <span class="option-title">
                                ✓ Hadir
                            </span>

                            <span class="option-description">
                                Peserta mengikuti latihan.
                                Sesi akan terpotong 1.
                            </span>

                        </label>

                    </div>

                    <div class="status-option">

                        <input
                            type="radio"
                            name="status"
                            value="permission"
                            id="statusPermission"
                            required
                        >

                        <label for="statusPermission">

                            <span class="option-title">
                                Izin
                            </span>

                            <span class="option-description">
                                Peserta memiliki izin.
                                Sesi tidak dipotong.
                            </span>

                        </label>

                    </div>

                    <div class="status-option">

                        <input
                            type="radio"
                            name="status"
                            value="sick"
                            id="statusSick"
                            required
                        >

                        <label for="statusSick">

                            <span class="option-title">
                                Sakit
                            </span>

                            <span class="option-description">
                                Peserta tidak hadir karena sakit.
                                Sesi tidak dipotong.
                            </span>

                        </label>

                    </div>

                    <div class="status-option">

                        <input
                            type="radio"
                            name="status"
                            value="absent"
                            id="statusAbsent"
                            required
                        >

                        <label for="statusAbsent">

                            <span class="option-title">
                                × Tidak Hadir
                            </span>

                            <span class="option-description">
                                Peserta tidak hadir.
                                Sesi akan terpotong 1.
                            </span>

                        </label>

                    </div>

                </div>

                <div
                    class="deduction-notice no-deduct"
                    id="deductionNotice"
                >
                    Pilih status kehadiran untuk melihat
                    informasi penggunaan sesi.
                </div>

                <div class="form-group">

                    <label
                        for="attendanceNotes"
                        class="form-label"
                    >
                        Catatan
                    </label>

                    <textarea
                        name="notes"
                        id="attendanceNotes"
                        class="form-textarea"
                        maxlength="1000"
                        placeholder="Contoh: Peserta mengikuti latihan dengan baik."
                    ></textarea>

                    <div class="form-help">
                        Opsional. Maksimal 1000 karakter.
                    </div>

                </div>

                <div class="modal-actions">

                    <button
                        type="button"
                        class="button-secondary"
                        id="attendanceCancelButton"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="button-primary"
                        id="attendanceSubmitButton"
                    >
                        Simpan Kehadiran
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('attendanceModal');
    const backdrop = document.getElementById('attendanceModalBackdrop');
    const closeButton = document.getElementById('attendanceModalClose');
    const cancelButton = document.getElementById('attendanceCancelButton');

    const form = document.getElementById('attendanceForm');

    const participantName =
        document.getElementById('modalParticipantName');

    const participantDetail =
        document.getElementById('modalParticipantDetail');

    const deductionNotice =
        document.getElementById('deductionNotice');

    const submitButton =
        document.getElementById('attendanceSubmitButton');

    const attendanceButtons =
        document.querySelectorAll('.open-attendance-modal');

    const statusInputs =
        document.querySelectorAll('input[name="status"]');


    function openModal(button) {

        const bookingId =
            button.dataset.bookingId;

        const name =
            button.dataset.participantName;

        const bookingCode =
            button.dataset.bookingCode;

        const packageName =
            button.dataset.packageName;

        const remainingSessions =
            button.dataset.remainingSessions;

        participantName.textContent = name;

        participantDetail.textContent =
            bookingCode +
            ' · ' +
            packageName +
            ' · ' +
            remainingSessions +
            ' sesi tersisa';

        form.action =
            '{{ url('/coach/bookings') }}/' +
            bookingId +
            '/attendance';

        form.reset();

        deductionNotice.className =
            'deduction-notice no-deduct';

        deductionNotice.textContent =
            'Pilih status kehadiran untuk melihat informasi penggunaan sesi.';

        submitButton.disabled = false;
        submitButton.textContent = 'Simpan Kehadiran';

        modal.classList.add('show');

        document.body.style.overflow = 'hidden';
    }


    function closeModal() {

        modal.classList.remove('show');

        document.body.style.overflow = '';

        form.reset();
    }


    attendanceButtons.forEach(function (button) {

        button.addEventListener('click', function () {
            openModal(button);
        });

    });


    statusInputs.forEach(function (input) {

        input.addEventListener('change', function () {

            if (
                input.value === 'present' ||
                input.value === 'absent'
            ) {

                deductionNotice.className =
                    'deduction-notice deduct';

                deductionNotice.textContent =
                    'Status ini akan menggunakan 1 sesi dari paket peserta.';

            } else {

                deductionNotice.className =
                    'deduction-notice no-deduct';

                deductionNotice.textContent =
                    'Status ini tidak akan mengurangi sisa sesi paket peserta.';

            }

        });

    });


    form.addEventListener('submit', function (event) {

        const selectedStatus =
            document.querySelector(
                'input[name="status"]:checked'
            );

        if (!selectedStatus) {
            event.preventDefault();
            alert('Silakan pilih status kehadiran.');
            return;
        }

        let confirmationMessage =
            'Simpan data kehadiran peserta ini?';

        if (
            selectedStatus.value === 'present' ||
            selectedStatus.value === 'absent'
        ) {

            confirmationMessage =
                'Status ini akan memotong 1 sesi paket peserta. Yakin ingin menyimpan?';
        }

        if (!confirm(confirmationMessage)) {
            event.preventDefault();
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
    });


    closeButton.addEventListener(
        'click',
        closeModal
    );

    cancelButton.addEventListener(
        'click',
        closeModal
    );

    backdrop.addEventListener(
        'click',
        closeModal
    );


    document.addEventListener('keydown', function (event) {

        if (
            event.key === 'Escape' &&
            modal.classList.contains('show')
        ) {
            closeModal();
        }

    });

});
</script>
@endpush
