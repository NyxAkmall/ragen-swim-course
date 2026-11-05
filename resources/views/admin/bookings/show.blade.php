@extends('layouts.admin')

@section('title', 'Detail Booking')
@section('topbar-title', 'Detail Booking')

@push('styles')
<style>
    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .detail-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-code-large {
        margin-top: 8px;
        color: var(--blue);
        font-size: 14px;
        font-weight: 800;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-confirmed {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-completed {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-rescheduled {
        background: #f5f3ff;
        color: #7c3aed;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
        gap: 20px;
        align-items: start;
    }

    .detail-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detail-card {
        overflow: hidden;
    }

    .card-header {
        padding: 17px 20px;
        border-bottom: 1px solid var(--border);
        background: #fbfdff;
    }

    .card-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .card-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .card-body {
        padding: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 24px;
    }

    .info-item {
        min-width: 0;
    }

    .info-item.full {
        grid-column: 1 / -1;
    }

    .info-label {
        margin-bottom: 5px;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .55px;
    }

    .info-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 750;
        line-height: 1.55;
        word-break: break-word;
    }

    .info-secondary {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .session-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 18px;
    }

    .session-box {
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #f8fafc;
        text-align: center;
    }

    .session-number {
        color: var(--navy);
        font-size: 21px;
        font-weight: 850;
    }

    .session-label {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 700;
    }

    .state-box {
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fafc;
    }

    .state-box + .state-box {
        margin-top: 12px;
    }

    .state-title {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .state-text {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .state-success {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .state-success .state-title {
        color: #15803d;
    }

    .state-danger {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .state-danger .state-title {
        color: #dc2626;
    }

    .state-warning {
        border-color: #fde68a;
        background: #fffbeb;
    }

    .state-warning .state-title {
        color: #b45309;
    }

    .state-blue {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .state-blue .state-title {
        color: #0369a1;
    }

    .history-link {
        display: block;
        margin-top: 9px;
        color: var(--blue-dark);
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
    }

    .history-link:hover {
        text-decoration: underline;
    }

    .notes-box {
        padding: 14px;
        border-radius: 11px;
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .skill-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .skill-item {
        padding: 11px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #f8fafc;
    }

    .skill-name {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 750;
    }

    .skill-score {
        margin-top: 3px;
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    /* =====================================================
       AKSI ADMIN
    ====================================================== */

    .admin-action-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .admin-action-box {
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fafc;
    }

    .admin-action-title {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .admin-action-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .admin-action-form {
        margin-top: 13px;
    }

    .admin-action-form textarea {
        width: 100%;
        min-height: 95px;
        padding: 11px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        color: var(--text);
        font: inherit;
        font-size: 12px;
        line-height: 1.6;
        resize: vertical;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
        box-sizing: border-box;
    }

    .admin-action-form textarea:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .10);
    }

    .action-button {
        width: 100%;
        margin-top: 10px;
        border: 0;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .15s ease, opacity .15s ease;
    }

    .action-button:hover {
        transform: translateY(-1px);
    }

    .action-button:disabled {
        cursor: not-allowed;
        opacity: .6;
        transform: none;
    }

    .action-confirm {
        background: #0f766e;
        color: #fff;
    }

    .action-cancel {
        background: #dc2626;
        color: #fff;
    }

    .form-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .action-note {
        margin-top: 10px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.6;
    }

    @media (max-width: 1000px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .detail-header {
            display: block;
        }

        .detail-header-actions {
            margin-top: 16px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full {
            grid-column: auto;
        }

        .session-summary {
            grid-template-columns: 1fr;
        }

        .skill-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


@section('content')

@php
    $participant = $booking->participant;
    $customer = $participant?->user;

    $participantPackage = $booking->participantPackage;
    $package = $participantPackage?->package;

    $schedule = $booking->classSchedule;
    $coach = $schedule?->coach;
    $location = $schedule?->location;

    $attendance = $booking->attendance;
    $progress = $booking->progressRecord;

    $statusLabel = match ($booking->status) {
        'pending' => 'Pending',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rescheduled' => 'Reschedule',
        default => ucfirst($booking->status),
    };

    $attendanceLabel = match ($attendance?->status) {
        'present' => 'Hadir',
        'permission' => 'Izin',
        'sick' => 'Sakit',
        'absent' => 'Tidak Hadir',
        default => 'Belum Dicatat',
    };

    $levelLabel = match ($participant?->swimming_level) {
        'beginner' => 'Beginner',
        'basic' => 'Basic',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        default => '-',
    };

    $progressLevelLabel = match ($progress?->level) {
        'beginner' => 'Beginner',
        'basic' => 'Basic',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        default => '-',
    };

    $canConfirm =
        $booking->status === 'pending'
        && !$booking->session_deducted
        && $attendance === null;

    $canCancel =
        in_array($booking->status, ['pending', 'confirmed'], true)
        && !$booking->session_deducted
        && $attendance === null;
@endphp


<div class="detail-header">

    <div>

        <h1 class="page-title">
            Detail Booking
        </h1>

        <div class="booking-code-large">
            {{ $booking->booking_code }}
        </div>

        <p class="page-description">
            Informasi lengkap booking peserta, paket, jadwal kelas,
            kehadiran, penggunaan sesi, dan perkembangan peserta.
        </p>

    </div>


    <div class="detail-header-actions">

        <span class="status-badge status-{{ $booking->status }}">
            {{ $statusLabel }}
        </span>

        <a
            href="{{ route('admin.bookings.index') }}"
            class="btn-secondary"
        >
            Kembali
        </a>

    </div>

</div>


<div class="detail-grid">

    {{-- =====================================================
        KOLOM UTAMA
    ====================================================== --}}

    <div class="detail-column">

        {{-- PESERTA & CUSTOMER --}}
        <div class="card detail-card">

            <div class="card-header">

                <div class="card-title">
                    Peserta & Customer
                </div>

                <div class="card-description">
                    Pemilik booking dan data peserta yang mengikuti kelas.
                </div>

            </div>


            <div class="card-body">

                <div class="info-grid">

                    <div class="info-item">
                        <div class="info-label">
                            Nama Peserta
                        </div>

                        <div class="info-value">
                            {{ $participant?->full_name ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Level Renang
                        </div>

                        <div class="info-value">
                            {{ $levelLabel }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Customer / Orang Tua
                        </div>

                        <div class="info-value">
                            {{ $customer?->name ?? '-' }}
                        </div>

                        <div class="info-secondary">
                            {{ $customer?->email ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Nomor Telepon
                        </div>

                        <div class="info-value">
                            {{ $customer?->phone ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Tipe Peserta
                        </div>

                        <div class="info-value">
                            @if ($participant?->participant_type === 'child')
                                Anak
                            @elseif ($participant?->participant_type === 'self')
                                Diri Sendiri
                            @else
                                -
                            @endif
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Status Peserta
                        </div>

                        <div class="info-value">
                            {{ $participant?->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- JADWAL KELAS --}}
        <div class="card detail-card">

            <div class="card-header">

                <div class="card-title">
                    Jadwal Kelas
                </div>

                <div class="card-description">
                    Informasi kelas yang dipilih pada booking ini.
                </div>

            </div>


            <div class="card-body">

                <div class="info-grid">

                    <div class="info-item full">
                        <div class="info-label">
                            Nama Jadwal
                        </div>

                        <div class="info-value">
                            {{ $schedule?->title ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Program
                        </div>

                        <div class="info-value">
                            {{ $schedule?->program?->name ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Level Kelas
                        </div>

                        <div class="info-value">
                            {{
                                match ($schedule?->swimming_level) {
                                    'all' => 'Semua Level',
                                    'beginner' => 'Beginner',
                                    'basic' => 'Basic',
                                    'intermediate' => 'Intermediate',
                                    'advanced' => 'Advanced',
                                    default => '-',
                                }
                            }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Tanggal
                        </div>

                        <div class="info-value">
                            {{ $schedule?->class_date?->format('d M Y') ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Waktu
                        </div>

                        <div class="info-value">
                            @if ($schedule?->start_time)
                                {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                -
                                {{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Coach
                        </div>

                        <div class="info-value">
                            {{ $coach?->user?->name ?? '-' }}
                        </div>

                        <div class="info-secondary">
                            {{ $coach?->specialization ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Lokasi
                        </div>

                        <div class="info-value">
                            {{ $location?->name ?? '-' }}
                        </div>

                        <div class="info-secondary">
                            {{ $location?->city ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- PAKET --}}
        <div class="card detail-card">

            <div class="card-header">

                <div class="card-title">
                    Paket Peserta
                </div>

                <div class="card-description">
                    Paket aktif yang digunakan ketika melakukan booking.
                </div>

            </div>


            <div class="card-body">

                <div class="info-grid">

                    <div class="info-item">
                        <div class="info-label">
                            Nama Paket
                        </div>

                        <div class="info-value">
                            {{ $package?->name ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Program Paket
                        </div>

                        <div class="info-value">
                            {{ $package?->program?->name ?? '-' }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Status Paket Peserta
                        </div>

                        <div class="info-value">
                            {{ ucfirst($participantPackage?->status ?? '-') }}
                        </div>
                    </div>


                    <div class="info-item">
                        <div class="info-label">
                            Masa Berlaku
                        </div>

                        <div class="info-value">
                            @if ($participantPackage?->start_date)
                                {{ $participantPackage->start_date->format('d M Y') }}
                            @else
                                -
                            @endif

                            -

                            @if ($participantPackage?->expiry_date)
                                {{ $participantPackage->expiry_date->format('d M Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                </div>


                @if ($participantPackage)

                    <div class="session-summary">

                        <div class="session-box">
                            <div class="session-number">
                                {{ $participantPackage->total_sessions }}
                            </div>

                            <div class="session-label">
                                Total Sesi
                            </div>
                        </div>


                        <div class="session-box">
                            <div class="session-number">
                                {{ $participantPackage->used_sessions }}
                            </div>

                            <div class="session-label">
                                Sudah Digunakan
                            </div>
                        </div>


                        <div class="session-box">
                            <div class="session-number">
                                {{ $participantPackage->remaining_sessions }}
                            </div>

                            <div class="session-label">
                                Sisa Sesi
                            </div>
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- PROGRESS --}}
        <div class="card detail-card">

            <div class="card-header">

                <div class="card-title">
                    Perkembangan Peserta
                </div>

                <div class="card-description">
                    Penilaian coach yang terhubung dengan booking ini.
                </div>

            </div>


            <div class="card-body">

                @if ($progress)

                    <div class="info-grid">

                        <div class="info-item">
                            <div class="info-label">
                                Level Penilaian
                            </div>

                            <div class="info-value">
                                {{ $progressLevelLabel }}
                            </div>
                        </div>


                        <div class="info-item">
                            <div class="info-label">
                                Coach Penilai
                            </div>

                            <div class="info-value">
                                {{ $progress->coach?->user?->name ?? '-' }}
                            </div>
                        </div>


                        <div class="info-item">
                            <div class="info-label">
                                Tanggal Penilaian
                            </div>

                            <div class="info-value">
                                {{ $progress->assessment_date?->format('d M Y') ?? '-' }}
                            </div>
                        </div>

                    </div>


                    <div class="skill-grid">

                        @foreach ([
                            'Adaptasi Air' => $progress->water_adaptation,
                            'Floating' => $progress->floating,
                            'Pernapasan' => $progress->breathing,
                            'Kicking' => $progress->kicking,
                            'Freestyle' => $progress->freestyle,
                            'Breaststroke' => $progress->breaststroke,
                        ] as $skillName => $score)

                            <div class="skill-item">

                                <div class="skill-name">
                                    {{ $skillName }}
                                </div>

                                <div class="skill-score">
                                    {{ $score ?? '-' }}
                                    @if ($score)
                                        / 5
                                    @endif
                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div style="margin-top: 16px;">

                        <div class="info-label">
                            Catatan Coach
                        </div>

                        <div class="notes-box">
                            {{ $progress->coach_notes ?: 'Tidak ada catatan coach.' }}
                        </div>

                    </div>

                @else

                    <div class="state-box">
                        <div class="state-title">
                            Belum Ada Data Perkembangan
                        </div>

                        <div class="state-text">
                            Coach belum membuat laporan perkembangan untuk
                            booking ini.
                        </div>
                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =====================================================
        KOLOM SAMPING
    ====================================================== --}}

    <div class="detail-column">

        {{-- STATUS BOOKING --}}
        <div class="card detail-card">

            <div class="card-header">
                <div class="card-title">
                    Status Booking
                </div>
            </div>


            <div class="card-body">

                <div class="info-item">

                    <div class="info-label">
                        Status Saat Ini
                    </div>

                    <span class="status-badge status-{{ $booking->status }}">
                        {{ $statusLabel }}
                    </span>

                </div>


                <div style="margin-top: 20px;" class="info-item">

                    <div class="info-label">
                        Booking Dibuat
                    </div>

                    <div class="info-value">
                        {{ $booking->booked_at?->format('d M Y, H:i') ?? '-' }}
                    </div>

                </div>


                <div style="margin-top: 20px;" class="info-item">

                    <div class="info-label">
                        Penggunaan Sesi
                    </div>

                    <div class="info-value">
                        {{ $booking->session_deducted ? 'Sudah Dipotong' : 'Belum Dipotong' }}
                    </div>

                    <div class="info-secondary">
                        Pengurangan sesi dilakukan melalui proses kehadiran,
                        bukan secara manual dari halaman Admin Booking.
                    </div>

                </div>

            </div>

        </div>


        {{-- AKSI ADMIN --}}
        @if (in_array($booking->status, ['pending', 'confirmed'], true))

            <div class="card detail-card">

                <div class="card-header">

                    <div class="card-title">
                        Aksi Admin
                    </div>

                    <div class="card-description">
                        Kelola status booking sebelum proses kehadiran dilakukan.
                    </div>

                </div>


                <div class="card-body">

                    <div class="admin-action-list">

                        {{-- KONFIRMASI BOOKING --}}
                        @if ($canConfirm)

                            <div class="admin-action-box">

                                <div class="admin-action-title">
                                    Konfirmasi Booking
                                </div>

                                <div class="admin-action-description">
                                    Konfirmasi bahwa booking peserta telah
                                    diterima dan siap mengikuti jadwal kelas.
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('admin.bookings.confirm', $booking) }}"
                                    class="admin-action-form js-confirm-booking-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="action-button action-confirm"
                                    >
                                        Konfirmasi Booking
                                    </button>
                                </form>

                                <div class="action-note">
                                    Konfirmasi booking tidak mengurangi sesi paket.
                                    Pengurangan sesi tetap dilakukan melalui
                                    proses kehadiran Coach.
                                </div>

                            </div>

                        @endif


                        {{-- BATALKAN BOOKING --}}
                        @if ($canCancel)

                            <div class="admin-action-box">

                                <div class="admin-action-title">
                                    Batalkan Booking
                                </div>

                                <div class="admin-action-description">
                                    Gunakan pembatalan hanya jika booking memang
                                    tidak akan dilanjutkan. Alasan pembatalan
                                    akan tersimpan pada riwayat booking.
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('admin.bookings.cancel', $booking) }}"
                                    class="admin-action-form js-cancel-booking-form"
                                >
                                    @csrf

                                    <label
                                        for="cancellation_reason"
                                        class="info-label"
                                    >
                                        Alasan Pembatalan
                                    </label>

                                    <textarea
                                        id="cancellation_reason"
                                        name="cancellation_reason"
                                        minlength="3"
                                        maxlength="1000"
                                        required
                                        placeholder="Contoh: Jadwal dibatalkan atas permintaan customer."
                                    >{{ old('cancellation_reason') }}</textarea>

                                    @error('cancellation_reason')
                                        <div class="form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <button
                                        type="submit"
                                        class="action-button action-cancel"
                                    >
                                        Batalkan Booking
                                    </button>
                                </form>

                                <div class="action-note">
                                    Pembatalan dari halaman ini tidak mengurangi
                                    sesi paket peserta.
                                </div>

                            </div>

                        @endif


                        @if (!$canConfirm && !$canCancel)

                            <div class="state-box state-warning">

                                <div class="state-title">
                                    Aksi Tidak Tersedia
                                </div>

                                <div class="state-text">
                                    Booking ini sudah memiliki data kehadiran
                                    atau sesi paket sudah digunakan sehingga
                                    status tidak dapat diubah dari halaman ini.
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endif


        {{-- KEHADIRAN --}}
        <div class="card detail-card">

            <div class="card-header">
                <div class="card-title">
                    Kehadiran
                </div>
            </div>


            <div class="card-body">

                @if ($attendance)

                    @if (in_array($attendance->status, ['present', 'absent'], true))

                        <div class="state-box state-success">

                            <div class="state-title">
                                {{ $attendanceLabel }}
                            </div>

                            <div class="state-text">
                                Status kehadiran ini menggunakan satu sesi
                                paket peserta.
                            </div>

                        </div>

                    @else

                        <div class="state-box state-blue">

                            <div class="state-title">
                                {{ $attendanceLabel }}
                            </div>

                            <div class="state-text">
                                Status kehadiran ini tidak mengurangi sesi
                                paket peserta.
                            </div>

                        </div>

                    @endif


                    <div style="margin-top: 16px;" class="info-item">

                        <div class="info-label">
                            Dicatat Pada
                        </div>

                        <div class="info-value">
                            {{ $attendance->checked_at?->format('d M Y, H:i') ?? '-' }}
                        </div>

                    </div>


                    @if ($attendance->notes)

                        <div style="margin-top: 16px;">

                            <div class="info-label">
                                Catatan Kehadiran
                            </div>

                            <div class="notes-box">
                                {{ $attendance->notes }}
                            </div>

                        </div>

                    @endif

                @else

                    <div class="state-box state-warning">

                        <div class="state-title">
                            Belum Ada Kehadiran
                        </div>

                        <div class="state-text">
                            Coach belum mencatat kehadiran peserta untuk
                            booking ini.
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- RIWAYAT RESCHEDULE --}}
        <div class="card detail-card">

            <div class="card-header">
                <div class="card-title">
                    Riwayat Reschedule
                </div>
            </div>


            <div class="card-body">

                @if ($booking->previousBooking)

                    <div class="state-box state-blue">

                        <div class="state-title">
                            Booking Sebelumnya
                        </div>

                        <div class="state-text">
                            Booking ini merupakan hasil pemindahan dari
                            {{ $booking->previousBooking->booking_code }}.
                        </div>

                        <a
                            href="{{ route('admin.bookings.show', $booking->previousBooking) }}"
                            class="history-link"
                        >
                            Lihat booking sebelumnya →
                        </a>

                    </div>

                @endif


                @if ($booking->rescheduledBooking)

                    <div class="state-box state-blue">

                        <div class="state-title">
                            Dipindahkan ke Booking Baru
                        </div>

                        <div class="state-text">
                            Booking ini telah dipindahkan ke
                            {{ $booking->rescheduledBooking->booking_code }}.
                        </div>

                        <a
                            href="{{ route('admin.bookings.show', $booking->rescheduledBooking) }}"
                            class="history-link"
                        >
                            Lihat booking pengganti →
                        </a>

                    </div>

                @endif


                @if (
                    !$booking->previousBooking
                    && !$booking->rescheduledBooking
                )

                    <div class="state-box">

                        <div class="state-title">
                            Tidak Ada Reschedule
                        </div>

                        <div class="state-text">
                            Booking ini tidak memiliki riwayat pemindahan
                            jadwal.
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- PEMBATALAN --}}
        @if ($booking->isCancelled())

            <div class="card detail-card">

                <div class="card-header">
                    <div class="card-title">
                        Informasi Pembatalan
                    </div>
                </div>


                <div class="card-body">

                    <div class="state-box state-danger">

                        <div class="state-title">
                            Booking Dibatalkan
                        </div>

                        <div class="state-text">
                            Booking ini sudah tidak memegang slot kelas.
                        </div>

                    </div>


                    <div style="margin-top: 16px;" class="info-item">

                        <div class="info-label">
                            Waktu Pembatalan
                        </div>

                        <div class="info-value">
                            {{ $booking->cancelled_at?->format('d M Y, H:i') ?? '-' }}
                        </div>

                    </div>


                    <div style="margin-top: 16px;">

                        <div class="info-label">
                            Alasan Pembatalan
                        </div>

                        <div class="notes-box">
                            {{ $booking->cancellation_reason ?: 'Tidak ada alasan pembatalan.' }}
                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- CATATAN BOOKING --}}
        @if ($booking->notes)

            <div class="card detail-card">

                <div class="card-header">
                    <div class="card-title">
                        Catatan Booking
                    </div>
                </div>

                <div class="card-body">

                    <div class="notes-box">
                        {{ $booking->notes }}
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmForm = document.querySelector('.js-confirm-booking-form');

        if (confirmForm) {
            confirmForm.addEventListener('submit', function (event) {
                const approved = window.confirm(
                    'Konfirmasi booking ini?\n\n' +
                    'Status booking akan berubah dari Pending menjadi Dikonfirmasi. ' +
                    'Sesi paket tidak akan dipotong pada proses ini.'
                );

                if (!approved) {
                    event.preventDefault();
                    return;
                }

                const button = confirmForm.querySelector('button[type="submit"]');

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Memproses...';
                }
            });
        }

        const cancelForm = document.querySelector('.js-cancel-booking-form');

        if (cancelForm) {
            cancelForm.addEventListener('submit', function (event) {
                const reason = cancelForm.querySelector(
                    'textarea[name="cancellation_reason"]'
                );

                if (!reason || reason.value.trim().length < 3) {
                    event.preventDefault();

                    window.alert(
                        'Alasan pembatalan wajib diisi minimal 3 karakter.'
                    );

                    if (reason) {
                        reason.focus();
                    }

                    return;
                }

                const approved = window.confirm(
                    'Yakin ingin membatalkan booking ini?\n\n' +
                    'Booking akan berubah menjadi Dibatalkan dan tidak lagi memegang slot kelas. ' +
                    'Sesi paket tidak akan dipotong.'
                );

                if (!approved) {
                    event.preventDefault();
                    return;
                }

                const button = cancelForm.querySelector('button[type="submit"]');

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Membatalkan...';
                }
            });
        }
    });
</script>
@endpush
