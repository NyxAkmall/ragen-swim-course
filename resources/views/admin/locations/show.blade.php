
@extends('layouts.admin')

@section('title', 'Detail Lokasi')
@section('topbar-title', 'Detail Lokasi')

@push('styles')
<style>
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 12px;
        color: var(--text-soft);
        font-size: 12px;
    }

    .breadcrumb a {
        color: var(--blue);
        text-decoration: none;
        font-weight: 700;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-light);
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .header-actions svg {
        width: 17px;
        height: 17px;
    }

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .stat-card {
        padding: 19px;
    }

    .stat-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 13px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 6px;
        color: var(--navy);
        font-size: 26px;
        font-weight: 850;
        letter-spacing: -0.6px;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .stat-icon.success {
        background: var(--success-bg);
        color: var(--success);
    }

    .stat-icon.warning {
        background: var(--warning-bg);
        color: var(--warning);
    }

    .stat-icon.inactive {
        background: #f1f5f9;
        color: var(--text-soft);
    }

    .stat-icon svg {
        width: 20px;
        height: 20px;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 22px;
        align-items: start;
    }

    .content-column {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .detail-card {
        overflow: hidden;
    }

    .card-header-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }

    .card-title-custom {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .card-subtitle-custom {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .card-body-custom {
        padding: 22px;
    }

    .location-heading {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .location-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .location-icon svg {
        width: 27px;
        height: 27px;
    }

    .location-heading-content {
        min-width: 0;
        flex: 1;
    }

    .location-name {
        color: var(--navy);
        font-size: 21px;
        font-weight: 850;
        letter-spacing: -0.4px;
    }

    .location-city {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 650;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        content: "";
    }

    .status-active {
        background: var(--success-bg);
        color: var(--success);
    }

    .status-active::before {
        background: #22c55e;
    }

    .status-inactive {
        background: #f1f5f9;
        color: var(--text-soft);
    }

    .status-inactive::before {
        background: #94a3b8;
    }

    .information-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 25px;
        margin-top: 24px;
        padding-top: 22px;
        border-top: 1px solid var(--border);
    }

    .information-item.full {
        grid-column: 1 / -1;
    }

    .information-label {
        margin-bottom: 6px;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .information-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 650;
        line-height: 1.65;
        overflow-wrap: anywhere;
    }

    .information-value.normal {
        font-weight: 500;
    }

    .maps-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--blue);
        text-decoration: none;
        font-size: 12px;
        font-weight: 750;
    }

    .maps-link:hover {
        text-decoration: underline;
    }

    .maps-link svg {
        width: 15px;
        height: 15px;
    }

    .empty-value {
        color: var(--text-light);
        font-style: italic;
        font-weight: 500;
    }

    /* BIAYA MASUK KOLAM */
    .entry-fee-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .entry-fee-box {
        padding: 20px;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: #f8fbff;
    }

    .entry-fee-box.coach {
        border-color: #d1fae5;
        background: #f7fefb;
    }

    .entry-fee-label {
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 750;
    }

    .entry-fee-value {
        margin-top: 9px;
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        letter-spacing: -0.7px;
        overflow-wrap: anywhere;
    }

    .entry-fee-caption {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .entry-fee-note {
        margin-top: 17px;
        padding: 12px 14px;
        border-radius: 10px;
        background: #f1f5f9;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.7;
    }

    /* TABEL JADWAL */
    .schedule-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .schedule-table th {
        padding: 12px 17px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .schedule-table td {
        padding: 15px 17px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text);
        font-size: 12px;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .schedule-title {
        color: var(--navy);
        font-weight: 800;
    }

    .schedule-program {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 10px;
    }

    .schedule-date {
        font-weight: 700;
    }

    .schedule-time {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 10px;
    }

    .schedule-status {
        display: inline-flex;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
    }

    .schedule-status.scheduled {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .schedule-status.completed {
        background: var(--success-bg);
        color: var(--success);
    }

    .schedule-status.cancelled {
        background: var(--danger-bg);
        color: var(--danger);
    }

    .booking-count {
        color: var(--text-soft);
        font-weight: 700;
    }

    .empty-schedule {
        padding: 45px 22px;
        text-align: center;
    }

    .empty-schedule-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        border-radius: 14px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .empty-schedule-icon svg {
        width: 23px;
        height: 23px;
    }

    .empty-schedule-title {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .empty-schedule-text {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.55;
    }

    /* SIDEBAR */
    .side-card {
        padding: 21px;
    }

    .side-card + .side-card {
        margin-top: 17px;
    }

    .side-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .side-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .side-action {
        margin-top: 16px;
    }

    .side-action .btn-primary,
    .side-action .btn-secondary {
        width: 100%;
    }

    .status-form {
        margin-top: 16px;
    }

    .status-button {
        width: 100%;
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 750;
        cursor: pointer;
    }

    .status-button.deactivate {
        border: 1px solid #fed7aa;
        background: #fff7ed;
        color: #c2410c;
    }

    .status-button.deactivate:hover {
        background: #ffedd5;
    }

    .status-button.activate {
        border: 1px solid #bbf7d0;
        background: var(--success-bg);
        color: var(--success);
    }

    .status-button.activate:hover {
        background: #dcfce7;
    }

    .meta-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
        margin-top: 17px;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .meta-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .meta-label {
        color: var(--text-soft);
        font-size: 11px;
    }

    .meta-value {
        color: var(--text);
        font-size: 11px;
        font-weight: 750;
        text-align: right;
    }

    @media (max-width: 1100px) {
        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .header-actions {
            margin-top: 17px;
        }

        .header-actions .btn-primary,
        .header-actions .btn-secondary {
            flex: 1;
        }

        .statistics-grid {
            grid-template-columns: 1fr 1fr;
            gap: 11px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 22px;
        }

        .stat-icon {
            width: 37px;
            height: 37px;
        }

        .card-header-custom,
        .card-body-custom {
            padding: 18px;
        }

        .information-grid,
        .entry-fee-grid {
            grid-template-columns: 1fr;
        }

        .information-item.full {
            grid-column: auto;
        }

        .entry-fee-value {
            font-size: 24px;
        }
    }

    @media (max-width: 430px) {
        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: column;
        }

        .header-actions .btn-primary,
        .header-actions .btn-secondary {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('admin.locations.index') }}">
        Lokasi
    </a>

    <span class="breadcrumb-separator">/</span>

    <span>Detail Lokasi</span>
</div>

<div class="page-header">

    <div>
        <h1 class="page-title">
            {{ $location->name }}
        </h1>

        <p class="page-description">
            Informasi lengkap lokasi, biaya masuk kolam,
            dan riwayat jadwal kelas Ragen Swim Course.
        </p>
    </div>

    <div class="header-actions">

        <a
            href="{{ route('admin.locations.index') }}"
            class="btn-secondary"
        >
            Kembali
        </a>

        <a
            href="{{ route('admin.locations.edit', $location) }}"
            class="btn-primary"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M4 20H8L18.5 9.5C19.3 8.7 19.3 7.3 18.5 6.5L17.5 5.5C16.7 4.7 15.3 4.7 14.5 5.5L4 16V20Z"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />

                <path
                    d="M13.5 6.5L17.5 10.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                />
            </svg>

            Edit Lokasi
        </a>

    </div>

</div>

{{-- STATISTIK JADWAL --}}
<div class="statistics-grid">

    <div class="card stat-card">
        <div class="stat-content">
            <div>
                <div class="stat-label">Total Jadwal</div>

                <div class="stat-value">
                    {{ number_format($statistics['schedules']) }}
                </div>
            </div>

            <div class="stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        x="3.5"
                        y="5"
                        width="17"
                        height="15"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M7 3V7M17 3V7M3.5 10H20.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-content">
            <div>
                <div class="stat-label">Terjadwal</div>

                <div class="stat-value">
                    {{ number_format($statistics['upcoming']) }}
                </div>
            </div>

            <div class="stat-icon warning">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="8"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M12 8V12L15 14"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-content">
            <div>
                <div class="stat-label">Selesai</div>

                <div class="stat-value">
                    {{ number_format($statistics['completed']) }}
                </div>
            </div>

            <div class="stat-icon success">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M5 12L10 17L19 7"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-content">
            <div>
                <div class="stat-label">Dibatalkan</div>

                <div class="stat-value">
                    {{ number_format($statistics['cancelled']) }}
                </div>
            </div>

            <div class="stat-icon inactive">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="8"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M9 9L15 15M15 9L9 15"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
        </div>
    </div>

</div>

<div class="detail-layout">

    <div class="content-column">

        {{-- INFORMASI LOKASI --}}
        <div class="card detail-card">

            <div class="card-body-custom">

                <div class="location-heading">

                    <div class="location-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12 21C12 21 18 15.8 18 10C18 6.7 15.3 4 12 4C8.7 4 6.7 6.7 6.7 10C6.7 15.8 12 21 12 21Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <circle
                                cx="12"
                                cy="10"
                                r="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                        </svg>
                    </div>

                    <div class="location-heading-content">

                        <div class="location-name">
                            {{ $location->name }}
                        </div>

                        <div class="location-city">
                            {{ $location->city }}
                        </div>

                        @if ($location->status === 'active')
                            <span class="status-badge status-active">
                                Lokasi Aktif
                            </span>
                        @else
                            <span class="status-badge status-inactive">
                                Lokasi Nonaktif
                            </span>
                        @endif

                    </div>

                </div>

                <div class="information-grid">

                    <div class="information-item full">
                        <div class="information-label">
                            Alamat Lengkap
                        </div>

                        <div class="information-value">
                            {{ $location->address }}
                        </div>
                    </div>

                    <div class="information-item">
                        <div class="information-label">
                            Kota / Kabupaten
                        </div>

                        <div class="information-value">
                            {{ $location->city }}
                        </div>
                    </div>

                    <div class="information-item">
                        <div class="information-label">
                            Google Maps
                        </div>

                        <div class="information-value">

                            @if ($location->hasGoogleMaps())
                                <a
                                    href="{{ $location->google_maps_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="maps-link"
                                >
                                    Buka Google Maps

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M14 5H19V10M11 13L19 5"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />

                                        <path
                                            d="M19 13V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V6C5 5.4 5.4 5 6 5H11"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                    </svg>
                                </a>
                            @else
                                <span class="empty-value">
                                    Belum tersedia
                                </span>
                            @endif

                        </div>
                    </div>

                    <div class="information-item full">
                        <div class="information-label">
                            Deskripsi
                        </div>

                        <div class="information-value normal">
                            @if (!empty($location->description))
                                {{ $location->description }}
                            @else
                                <span class="empty-value">
                                    Belum ada deskripsi lokasi.
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="information-item full">
                        <div class="information-label">
                            Fasilitas
                        </div>

                        <div class="information-value normal">
                            @if (!empty($location->facilities))
                                {{ $location->facilities }}
                            @else
                                <span class="empty-value">
                                    Belum ada informasi fasilitas.
                                </span>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- BIAYA MASUK KOLAM --}}
        <div class="card detail-card">

            <div class="card-header-custom">
                <div>
                    <div class="card-title-custom">
                        Biaya Masuk Kolam
                    </div>

                    <div class="card-subtitle-custom">
                        Tarif dasar masuk kolam per orang untuk satu sesi latihan.
                    </div>
                </div>
            </div>

            <div class="card-body-custom">

                <div class="entry-fee-grid">

                    <div class="entry-fee-box">
                        <div class="entry-fee-label">
                            Customer
                        </div>

                        <div class="entry-fee-value">
                            {{ $location->customer_entry_fee_formatted }}
                        </div>

                        <div class="entry-fee-caption">
                            Per customer / sesi
                        </div>
                    </div>

                    <div class="entry-fee-box coach">
                        <div class="entry-fee-label">
                            Coach
                        </div>

                        <div class="entry-fee-value">
                            {{ $location->coach_entry_fee_formatted }}
                        </div>

                        <div class="entry-fee-caption">
                            Per coach / sesi
                        </div>
                    </div>

                </div>

                <div class="entry-fee-note">
                    Tarif di atas merupakan tarif dasar lokasi dan
                    tidak termasuk dalam harga paket kursus.
                    Tarif Rp0 dapat digunakan untuk akses gratis,
                    tetapi untuk lokasi yang belum diperiksa,
                    pastikan kembali ketentuan pengelola kolam.
                </div>

            </div>

        </div>

        {{-- JADWAL LOKASI --}}
        <div class="card detail-card">

            <div class="card-header-custom">
                <div>
                    <div class="card-title-custom">
                        Jadwal di Lokasi Ini
                    </div>

                    <div class="card-subtitle-custom">
                        Maksimal 10 jadwal terbaru yang menggunakan lokasi ini.
                    </div>
                </div>
            </div>

            @if ($location->schedules->count() > 0)

                <div class="schedule-table-wrapper">

                    <table class="schedule-table">

                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Tanggal & Waktu</th>
                                <th>Coach</th>
                                <th>Booking</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($location->schedules as $schedule)
                                <tr>

                                    <td>
                                        <div class="schedule-title">
                                            {{ $schedule->title }}
                                        </div>

                                        <div class="schedule-program">
                                            {{ $schedule->program?->name ?? 'Program tidak tersedia' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="schedule-date">
                                            {{ \Carbon\Carbon::parse($schedule->class_date)->translatedFormat('d M Y') }}
                                        </div>

                                        <div class="schedule-time">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            WIB
                                        </div>
                                    </td>

                                    <td>
                                        {{ $schedule->coach?->user?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="booking-count">
                                            {{ $schedule->bookings_count ?? 0 }} peserta
                                        </span>
                                    </td>

                                    <td>
                                        @if ($schedule->status === 'scheduled')
                                            <span class="schedule-status scheduled">
                                                Terjadwal
                                            </span>
                                        @elseif ($schedule->status === 'completed')
                                            <span class="schedule-status completed">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="schedule-status cancelled">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-schedule">

                    <div class="empty-schedule-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="3.5"
                                y="5"
                                width="17"
                                height="15"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M7 3V7M17 3V7M3.5 10H20.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                        </svg>
                    </div>

                    <div class="empty-schedule-title">
                        Belum ada jadwal
                    </div>

                    <div class="empty-schedule-text">
                        Belum ada kelas yang dijadwalkan menggunakan lokasi ini.
                    </div>

                </div>

            @endif

        </div>

    </div>

    {{-- SIDEBAR --}}
    <div>

        <div class="card side-card">

            <div class="side-title">
                Kelola Lokasi
            </div>

            <div class="side-description">
                Perbarui alamat, fasilitas, Google Maps,
                dan tarif dasar masuk kolam.
            </div>

            <div class="side-action">
                <a
                    href="{{ route('admin.locations.edit', $location) }}"
                    class="btn-primary"
                >
                    Edit Informasi
                </a>
            </div>

        </div>

        <div class="card side-card">

            <div class="side-title">
                Status Lokasi
            </div>

            @if ($location->status === 'active')

                <div class="side-description">
                    Lokasi saat ini aktif dan dapat digunakan
                    dalam operasional kelas.
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.locations.status.update', $location) }}"
                    class="status-form"
                    onsubmit="return confirm('Nonaktifkan lokasi ini?');"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="inactive"
                    >

                    <button
                        type="submit"
                        class="status-button deactivate"
                    >
                        Nonaktifkan Lokasi
                    </button>
                </form>

            @else

                <div class="side-description">
                    Lokasi sedang nonaktif. Aktifkan kembali jika
                    lokasi akan digunakan untuk operasional kelas.
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.locations.status.update', $location) }}"
                    class="status-form"
                    onsubmit="return confirm('Aktifkan kembali lokasi ini?');"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="active"
                    >

                    <button
                        type="submit"
                        class="status-button activate"
                    >
                        Aktifkan Lokasi
                    </button>
                </form>

            @endif

        </div>

        <div class="card side-card">

            <div class="side-title">
                Informasi Sistem
            </div>

            <div class="meta-list">

                <div class="meta-row">
                    <span class="meta-label">ID Lokasi</span>

                    <span class="meta-value">
                        #{{ $location->id }}
                    </span>
                </div>

                <div class="meta-row">
                    <span class="meta-label">Dibuat</span>

                    <span class="meta-value">
                        {{ $location->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </span>
                </div>

                <div class="meta-row">
                    <span class="meta-label">Diperbarui</span>

                    <span class="meta-value">
                        {{ $location->updated_at?->format('d/m/Y H:i') ?? '-' }}
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
