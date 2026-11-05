@extends('layouts.admin')

@section('title', 'Detail Jadwal Kelas')
@section('topbar-title', 'Detail Jadwal Kelas')

@push('styles')
<style>
    .schedule-flash {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 11px;
        font-size: 12px;
        line-height: 1.6;
    }

    .schedule-flash-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .schedule-flash-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .schedule-flash-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        font-size: 11px;
        font-weight: 900;
    }

    .schedule-flash-content {
        flex: 1;
    }

    .schedule-flash-content strong {
        display: block;
        margin-bottom: 2px;
        font-weight: 850;
    }

    .schedule-detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .schedule-detail-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .schedule-detail-description {
        max-width: 760px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .schedule-header-actions {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }

    .schedule-status {
        display: inline-flex;
        align-items: center;
        min-height: 29px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .schedule-status-scheduled {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .schedule-status-completed {
        background: var(--success-bg);
        color: var(--success);
    }

    .schedule-status-cancelled {
        background: var(--danger-bg);
        color: var(--danger);
    }

    .schedule-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .schedule-stat-card {
        padding: 19px;
    }

    .schedule-stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 750;
    }

    .schedule-stat-value {
        margin-top: 7px;
        color: var(--navy);
        font-size: 25px;
        font-weight: 850;
    }

    .schedule-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 22px;
        align-items: start;
    }

    .schedule-main-card,
    .schedule-side-card {
        padding: 22px;
    }

    .schedule-main-card + .schedule-main-card,
    .schedule-side-card + .schedule-side-card {
        margin-top: 18px;
    }

    .schedule-card-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .schedule-card-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .schedule-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 28px;
        margin-top: 19px;
    }

    .schedule-info-item {
        padding: 14px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .schedule-info-label {
        color: var(--text-light);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .schedule-info-value {
        margin-top: 5px;
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.55;
    }

    .schedule-notes {
        margin-top: 18px;
        padding: 15px;
        border-radius: 11px;
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .schedule-table-wrapper {
        margin-top: 18px;
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .schedule-table th {
        padding: 11px 12px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.45px;
    }

    .schedule-table td {
        padding: 13px 12px;
        border-bottom: 1px solid #eef2f7;
        color: var(--text);
        font-size: 12px;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .participant-name {
        color: var(--navy);
        font-weight: 800;
    }

    .participant-secondary {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 10px;
    }

    .booking-code {
        font-weight: 750;
    }

    .booking-status {
        display: inline-flex;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .booking-status-pending {
        background: var(--warning-bg);
        color: var(--warning);
    }

    .booking-status-confirmed {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .booking-status-completed {
        background: var(--success-bg);
        color: var(--success);
    }

    .booking-status-cancelled,
    .booking-status-rescheduled {
        background: #f1f5f9;
        color: #64748b;
    }

    .schedule-empty {
        margin-top: 18px;
        padding: 34px 20px;
        border: 1px dashed var(--border);
        border-radius: 12px;
        background: #fbfdff;
        text-align: center;
    }

    .schedule-empty-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .schedule-empty-text {
        max-width: 450px;
        margin: 5px auto 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .schedule-side-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .schedule-side-list {
        margin-top: 14px;
    }

    .schedule-side-item {
        padding: 11px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .schedule-side-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .schedule-side-label {
        color: var(--text-light);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.45px;
    }

    .schedule-side-value {
        margin-top: 4px;
        color: var(--text);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.55;
    }

    .schedule-status-actions {
        display: grid;
        gap: 9px;
        margin-top: 16px;
    }

    .schedule-status-form {
        width: 100%;
    }

    .schedule-status-button {
        width: 100%;
        min-height: 42px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 750;
        cursor: pointer;
    }

    .schedule-complete-button {
        border: 1px solid #bbf7d0;
        background: var(--success-bg);
        color: var(--success);
    }

    .schedule-complete-button:hover {
        background: #dcfce7;
    }

    .schedule-cancel-button {
        border: 1px solid #fecaca;
        background: var(--danger-bg);
        color: var(--danger);
    }

    .schedule-cancel-button:hover {
        background: #fee2e2;
    }

    .schedule-warning {
        margin-top: 14px;
        padding: 12px;
        border: 1px solid #fde68a;
        border-radius: 10px;
        background: var(--warning-bg);
        color: #92400e;
        font-size: 11px;
        line-height: 1.6;
    }

    .schedule-completion-info {
        margin-top: 14px;
        padding: 12px;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        background: #f0f9ff;
        color: #075985;
        font-size: 11px;
        line-height: 1.6;
    }

    .schedule-completion-info strong {
        font-weight: 850;
    }

    .schedule-completion-info ul {
        margin: 7px 0 0 17px;
        padding: 0;
    }

    .schedule-completion-info li + li {
        margin-top: 4px;
    }

    @media (max-width: 1100px) {
        .schedule-layout {
            grid-template-columns: 1fr;
        }

        .schedule-side-column {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .schedule-side-card + .schedule-side-card {
            margin-top: 0;
        }
    }

    @media (max-width: 750px) {
        .schedule-detail-header {
            display: block;
        }

        .schedule-detail-title {
            font-size: 24px;
        }

        .schedule-header-actions {
            margin-top: 17px;
        }

        .schedule-header-actions .btn-primary,
        .schedule-header-actions .btn-secondary {
            flex: 1;
        }

        .schedule-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .schedule-info-grid {
            grid-template-columns: 1fr;
        }

        .schedule-side-column {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 430px) {
        .schedule-stats {
            grid-template-columns: 1fr;
        }

        .schedule-header-actions {
            display: grid;
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


@section('content')

@php
    $levelLabels = [
        'all' => 'Semua Level',
        'beginner' => 'Pemula',
        'basic' => 'Dasar',
        'intermediate' => 'Menengah',
        'advanced' => 'Lanjutan',
    ];

    $statusLabels = [
        'scheduled' => 'Terjadwal',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    $bookingStatusLabels = [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rescheduled' => 'Dijadwalkan Ulang',
    ];

    $attendanceLabels = [
        'present' => 'Hadir',
        'permission' => 'Izin',
        'sick' => 'Sakit',
        'absent' => 'Tidak Hadir',
    ];

    $classEndAt = null;

    if ($classSchedule->class_date && $classSchedule->end_time) {
        $classEndAt = \Carbon\Carbon::parse(
            $classSchedule->class_date->format('Y-m-d')
            . ' '
            . $classSchedule->end_time
        );
    }

    $classHasEnded = $classEndAt
        ? now()->greaterThanOrEqualTo($classEndAt)
        : false;

    $pendingBookingsCount = $classSchedule
        ->bookings
        ->where('status', 'pending')
        ->count();

    $confirmedBookingsCount = $classSchedule
        ->bookings
        ->where('status', 'confirmed')
        ->count();

    $canCompleteSchedule =
        $classSchedule->status === 'scheduled'
        && $classHasEnded
        && $pendingBookingsCount === 0
        && $confirmedBookingsCount === 0;
@endphp


{{-- =========================================================
    FLASH MESSAGES
========================================================== --}}

@if (session('success'))

    <div class="schedule-flash schedule-flash-success">

        <div class="schedule-flash-icon">
            ✓
        </div>

        <div class="schedule-flash-content">
            <strong>Berhasil</strong>
            {{ session('success') }}
        </div>

    </div>

@endif


@if (session('error'))

    <div class="schedule-flash schedule-flash-error">

        <div class="schedule-flash-icon">
            !
        </div>

        <div class="schedule-flash-content">
            <strong>Tindakan Tidak Dapat Dilakukan</strong>
            {{ session('error') }}
        </div>

    </div>

@endif


@if ($errors->any())

    <div class="schedule-flash schedule-flash-error">

        <div class="schedule-flash-icon">
            !
        </div>

        <div class="schedule-flash-content">

            <strong>
                Terdapat kesalahan
            </strong>

            {{ $errors->first() }}

        </div>

    </div>

@endif


{{-- =========================================================
    HEADER
========================================================== --}}

<div class="schedule-detail-header">

    <div>

        <div style="margin-bottom: 9px;">
            <span
                class="schedule-status schedule-status-{{ $classSchedule->status }}"
            >
                {{ $statusLabels[$classSchedule->status] ?? ucfirst($classSchedule->status) }}
            </span>
        </div>

        <h1 class="schedule-detail-title">
            {{ $classSchedule->title }}
        </h1>

        <p class="schedule-detail-description">
            Detail jadwal kelas, peserta yang melakukan booking,
            kapasitas kelas, serta pengelolaan status pelaksanaan.
        </p>

    </div>


    <div class="schedule-header-actions">

        <a
            href="{{ route('admin.class-schedules.index') }}"
            class="btn-secondary"
        >
            Kembali
        </a>

        @if ($classSchedule->status === 'scheduled')

            <a
                href="{{ route('admin.class-schedules.edit', $classSchedule) }}"
                class="btn-primary"
            >
                Edit Jadwal
            </a>

        @endif

        @if ($classSchedule->weeklySchedule)
            @if ($classSchedule->weeklySchedule->is_active)
                <form
                    method="POST"
                    action="{{ route('admin.weekly-schedules.status.update', $classSchedule->weeklySchedule) }}"
                    onsubmit="return confirm('Hentikan pengulangan? Jadwal mendatang tanpa booking akan dibatalkan. Jadwal dengan booking tetap tersedia.');"
                >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="stop">

                    <button type="submit" class="btn-secondary">
                        Hentikan Pengulangan
                    </button>
                </form>
            @else
                <form
                    method="POST"
                    action="{{ route('admin.weekly-schedules.status.update', $classSchedule->weeklySchedule) }}"
                    onsubmit="return confirm('Aktifkan kembali pengulangan mingguan untuk kelas ini?');"
                >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="start">

                    <button type="submit" class="btn-primary">
                        Aktifkan Kembali Pengulangan
                    </button>
                </form>
            @endif
        @endif

    </div>

</div>


{{-- =========================================================
    STATISTICS
========================================================== --}}

<div class="schedule-stats">

    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Kapasitas
        </div>

        <div class="schedule-stat-value">
            {{ $classSchedule->capacity }}
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Booking Aktif
        </div>

        <div class="schedule-stat-value">
            {{ $statistics['active_bookings'] }}
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Penawaran Waiting List Aktif
        </div>

        <div class="schedule-stat-value">
            {{ $statistics['active_offers'] }}
        </div>
    </div>

    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Slot Tersisa
        </div>

        <div class="schedule-stat-value">
            {{ $statistics['remaining_slots'] }}
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Total Riwayat Booking
        </div>

        <div class="schedule-stat-value">
            {{ $statistics['total_bookings'] }}
        </div>
    </div>

</div>


<div class="schedule-layout">

    {{-- =========================================================
        MAIN COLUMN
    ========================================================== --}}

    <div>

        {{-- DETAIL INFORMATION --}}
        <div class="card schedule-main-card">

            <div class="schedule-card-title">
                Informasi Kelas
            </div>

            <div class="schedule-card-description">
                Informasi utama mengenai pelaksanaan jadwal kelas.
            </div>


            <div class="schedule-info-grid">

                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Program
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->program?->name ?? '-' }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Level
                    </div>

                    <div class="schedule-info-value">
                        {{ $levelLabels[$classSchedule->swimming_level] ?? ucfirst($classSchedule->swimming_level) }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Tanggal
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->class_date?->translatedFormat('d F Y') ?? '-' }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Waktu
                    </div>

                    <div class="schedule-info-value">
                        {{ \Illuminate\Support\Str::substr($classSchedule->start_time, 0, 5) }}
                        -
                        {{ \Illuminate\Support\Str::substr($classSchedule->end_time, 0, 5) }}
                        WIB
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Coach
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->coach?->user?->name ?? '-' }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Spesialisasi Coach
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->coach?->specialization ?: '-' }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Lokasi
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->location?->name ?? '-' }}
                    </div>
                </div>


                <div class="schedule-info-item">
                    <div class="schedule-info-label">
                        Kota
                    </div>

                    <div class="schedule-info-value">
                        {{ $classSchedule->location?->city ?: '-' }}
                    </div>
                </div>

            </div>


            @if ($classSchedule->notes)

                <div class="schedule-notes">
                    <strong>Catatan:</strong><br>
                    {{ $classSchedule->notes }}
                </div>

            @endif

        </div>


        {{-- BOOKINGS --}}
        <div class="card schedule-main-card">

            <div class="schedule-card-title">
                Peserta & Booking
            </div>

            <div class="schedule-card-description">
                Seluruh riwayat booking yang terkait dengan jadwal
                kelas ini.
            </div>


            @if ($classSchedule->bookings->count() > 0)

                <div class="schedule-table-wrapper">

                    <table class="schedule-table">

                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Kode Booking</th>
                                <th>Paket</th>
                                <th>Status</th>
                                <th>Kehadiran</th>
                                <th>Progress</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach ($classSchedule->bookings as $booking)

                            <tr>

                                <td>
                                    <div class="participant-name">
                                        {{ $booking->participant?->full_name ?? '-' }}
                                    </div>

                                    <div class="participant-secondary">
                                        {{ $booking->participant?->user?->name ?? 'Customer' }}
                                    </div>
                                </td>


                                <td>
                                    <div class="booking-code">
                                        {{ $booking->booking_code }}
                                    </div>
                                </td>


                                <td>
                                    {{ $booking->participantPackage?->package?->name ?? '-' }}
                                </td>


                                <td>
                                    <span
                                        class="booking-status booking-status-{{ $booking->status }}"
                                    >
                                        {{ $bookingStatusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                    </span>
                                </td>


                                <td>
                                    @if ($booking->attendance)

                                        {{ $attendanceLabels[$booking->attendance->status] ?? ucfirst($booking->attendance->status) }}

                                    @else

                                        <span style="color: var(--text-light);">
                                            Belum dicatat
                                        </span>

                                    @endif
                                </td>


                                <td>
                                    @if ($booking->progressRecord)

                                        {{ number_format(
                                            (float) $booking->progressRecord->average_score,
                                            2,
                                            ',',
                                            '.'
                                        ) }}/5

                                    @else

                                        <span style="color: var(--text-light);">
                                            Belum dinilai
                                        </span>

                                    @endif
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="schedule-empty">

                    <div class="schedule-empty-title">
                        Belum Ada Booking
                    </div>

                    <div class="schedule-empty-text">
                        Belum ada peserta yang terdaftar pada jadwal
                        kelas ini.
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        SIDE COLUMN
    ========================================================== --}}

    <div class="schedule-side-column">

        {{-- LOCATION --}}
        <div class="card schedule-side-card">

            <div class="schedule-side-title">
                Informasi Lokasi
            </div>


            <div class="schedule-side-list">

                <div class="schedule-side-item">
                    <div class="schedule-side-label">
                        Nama Lokasi
                    </div>

                    <div class="schedule-side-value">
                        {{ $classSchedule->location?->name ?? '-' }}
                    </div>
                </div>


                <div class="schedule-side-item">
                    <div class="schedule-side-label">
                        Alamat
                    </div>

                    <div class="schedule-side-value">
                        {{ $classSchedule->location?->address ?: '-' }}
                    </div>
                </div>


                <div class="schedule-side-item">
                    <div class="schedule-side-label">
                        Fasilitas
                    </div>

                    <div class="schedule-side-value">
                        {{ $classSchedule->location?->facilities ?: '-' }}
                    </div>
                </div>

            </div>

        </div>


        {{-- STATUS MANAGEMENT --}}
        <div class="card schedule-side-card">

            <div class="schedule-side-title">
                Status Jadwal
            </div>


            <div class="schedule-side-list">

                <div class="schedule-side-item">

                    <div class="schedule-side-label">
                        Status Saat Ini
                    </div>

                    <div class="schedule-side-value">

                        <span
                            class="schedule-status schedule-status-{{ $classSchedule->status }}"
                        >
                            {{ $statusLabels[$classSchedule->status] ?? ucfirst($classSchedule->status) }}
                        </span>

                    </div>

                </div>

            </div>


            @if ($classSchedule->status === 'scheduled')

                <div class="schedule-status-actions">

                    <form
                        method="POST"
                        action="{{ route('admin.class-schedules.status.update', $classSchedule) }}"
                        class="schedule-status-form"
                        onsubmit="return confirm('Tandai jadwal ini sebagai selesai?');"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="completed"
                        >

                        <button
                            type="submit"
                            class="schedule-status-button schedule-complete-button"
                        >
                            Tandai Selesai
                        </button>

                    </form>


                    <form
                        method="POST"
                        action="{{ route('admin.class-schedules.status.update', $classSchedule) }}"
                        class="schedule-status-form"
                        onsubmit="return confirm('Liburkan pertemuan ini? Semua booking aktif pada tanggal ini akan dibatalkan.');"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="cancelled"
                        >

                        <label
                            for="cancellation_reason"
                            style="display:block; margin-bottom:8px; font-weight:600;"
                        >
                            Alasan Libur Pertemuan
                        </label>

                        <textarea
                            id="cancellation_reason"
                            name="cancellation_reason"
                            required
                            maxlength="1000"
                            rows="3"
                            placeholder="Contoh: Coach sakit atau kolam sedang dalam perbaikan."
                            style="display:block; width:100%; box-sizing:border-box; padding:10px; margin-bottom:10px; border:1px solid #d1d5db; border-radius:8px;"
                        >{{ old('cancellation_reason') }}</textarea>

                        <p style="font-size:12px; line-height:1.6; margin:0 0 12px;">
                            Pertemuan pada tanggal ini akan dibatalkan.
                            Booking aktif ikut dibatalkan, tetapi
                            pengulangan kelas mingguan tetap berjalan.
                        </p>

                        <button
                            type="submit"
                            class="schedule-status-button schedule-cancel-button"
                        >
                            Liburkan Pertemuan
                        </button>

                    </form>

                </div>


                <div class="schedule-completion-info">

                    <strong>
                        Ketentuan Menyelesaikan Jadwal
                    </strong>

                    <ul>
                        <li>
                            Kelas harus sudah melewati waktu selesai.
                        </li>

                        <li>
                            Tidak boleh ada booking yang masih menunggu
                            konfirmasi.
                        </li>

                        <li>
                            Seluruh booking terkonfirmasi harus sudah
                            diproses kehadirannya oleh coach.
                        </li>
                    </ul>

                    @if (!$classHasEnded && $classEndAt)

                        <div style="margin-top: 9px;">
                            Kelas ini berakhir pada
                            <strong>
                                {{ $classEndAt->translatedFormat('d F Y, H:i') }}
                                WIB
                            </strong>.
                        </div>

                    @elseif ($pendingBookingsCount > 0)

                        <div style="margin-top: 9px;">
                            Saat ini masih terdapat
                            <strong>
                                {{ $pendingBookingsCount }}
                                booking menunggu konfirmasi
                            </strong>.
                        </div>

                    @elseif ($confirmedBookingsCount > 0)

                        <div style="margin-top: 9px;">
                            Saat ini masih terdapat
                            <strong>
                                {{ $confirmedBookingsCount }}
                                booking terkonfirmasi
                            </strong>
                            yang belum diproses kehadirannya.
                        </div>

                    @elseif ($canCompleteSchedule)

                        <div style="margin-top: 9px;">
                            <strong>
                                Jadwal ini sudah memenuhi syarat
                                untuk ditandai selesai.
                            </strong>
                        </div>

                    @endif

                </div>


                <div class="schedule-warning">
                    Membatalkan jadwal akan membatalkan booking
                    berstatus menunggu atau dikonfirmasi pada jadwal
                    tersebut. Sesi peserta tidak akan dikurangi.
                </div>

            @elseif ($classSchedule->status === 'completed')

                <div class="schedule-warning">
                    Jadwal ini sudah ditandai selesai dan tidak dapat
                    diedit atau diaktifkan kembali.
                </div>

            @elseif ($classSchedule->status === 'cancelled')

                <div class="schedule-warning">
                    Jadwal ini telah dibatalkan. Status pembatalan
                    bersifat final dan jadwal tidak dapat diaktifkan
                    kembali.
                </div>

            @endif

        </div>

    </div>

</div>

@endsection
