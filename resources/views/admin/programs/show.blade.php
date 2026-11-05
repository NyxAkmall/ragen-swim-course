@extends('layouts.admin')

@section('title', 'Detail Program')
@section('topbar-title', 'Detail Program')

@push('styles')
<style>
    .detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .detail-header-main {
        min-width: 0;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 8px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 650;
    }

    .breadcrumb a {
        color: var(--blue);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .detail-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .detail-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .detail-description {
        max-width: 750px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .detail-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-shrink: 0;
    }

    .button-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-active::before {
        background: #22c55e;
    }

    .status-inactive {
        background: #f8fafc;
        color: #64748b;
    }

    .status-inactive::before {
        background: #94a3b8;
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .stat-card {
        min-height: 118px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 19px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 7px;
        color: var(--navy);
        font-size: 26px;
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    .stat-description {
        margin-top: 4px;
        color: var(--text-light);
        font-size: 10px;
        line-height: 1.5;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .stat-icon.aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-icon.green {
        background: #f0fdf4;
        color: #15803d;
    }

    .stat-icon.slate {
        background: #f8fafc;
        color: #64748b;
    }

    .stat-icon svg {
        width: 21px;
        height: 21px;
    }

    /*
    |--------------------------------------------------------------------------
    | Main Layout
    |--------------------------------------------------------------------------
    */

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 20px;
        align-items: start;
    }

    .main-column,
    .side-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /*
    |--------------------------------------------------------------------------
    | Card
    |--------------------------------------------------------------------------
    */

    .section-card {
        overflow: hidden;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .section-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .section-subtitle {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .section-body {
        padding: 20px;
    }

    /*
    |--------------------------------------------------------------------------
    | Program Information
    |--------------------------------------------------------------------------
    */

    .information-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
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
        letter-spacing: 0.55px;
    }

    .information-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.6;
    }

    .information-value.regular {
        font-weight: 450;
        color: #475569;
        white-space: pre-line;
    }

    .slug-box {
        display: inline-flex;
        padding: 6px 9px;
        border-radius: 7px;
        background: #f8fafc;
        color: #475569;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        font-weight: 650;
    }

    /*
    |--------------------------------------------------------------------------
    | Package Table
    |--------------------------------------------------------------------------
    */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        min-width: 690px;
        border-collapse: collapse;
    }

    .data-table th {
        padding: 11px 16px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 9px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.55px;
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--text);
        font-size: 12px;
    }

    .data-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .table-name {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .table-muted {
        margin-top: 3px;
        color: var(--text-light);
        font-size: 10px;
    }

    .price-text {
        color: var(--navy);
        font-weight: 800;
        white-space: nowrap;
    }

    .mini-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
    }

    .mini-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .mini-inactive {
        background: #f8fafc;
        color: #64748b;
    }

    .table-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 6px 9px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--white);
        color: var(--navy);
        text-decoration: none;
        font-size: 10px;
        font-weight: 750;
    }

    .table-action:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: var(--blue-dark);
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    .schedule-list {
        display: flex;
        flex-direction: column;
    }

    .schedule-item {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(150px, .8fr) minmax(150px, .8fr);
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .schedule-item:last-child {
        border-bottom: 0;
    }

    .schedule-title {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .schedule-meta {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .schedule-label {
        margin-bottom: 4px;
        color: var(--text-light);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.45px;
    }

    .schedule-value {
        color: var(--text);
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Status Action
    |--------------------------------------------------------------------------
    */

    .status-card {
        padding: 20px;
    }

    .status-card-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .status-card-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .status-current {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 17px;
        padding: 13px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #f8fafc;
    }

    .status-current-label {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 700;
    }

    .status-form {
        margin-top: 14px;
    }

    .status-button {
        width: 100%;
        min-height: 41px;
        padding: 9px 13px;
        border-radius: 9px;
        font-size: 11px;
        font-weight: 800;
    }

    .status-button.deactivate {
        border: 1px solid #fecaca;
        background: #fff;
        color: #dc2626;
    }

    .status-button.deactivate:hover {
        background: #fef2f2;
    }

    .status-button.activate {
        border: 1px solid #bbf7d0;
        background: #fff;
        color: #15803d;
    }

    .status-button.activate:hover {
        background: #f0fdf4;
    }

    /*
    |--------------------------------------------------------------------------
    | Quick Action
    |--------------------------------------------------------------------------
    */

    .quick-card {
        padding: 20px;
    }

    .quick-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .quick-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 9px;
        margin-top: 16px;
    }

    .quick-actions .btn-primary,
    .quick-actions .btn-secondary {
        width: 100%;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 35px 20px;
        text-align: center;
    }

    .empty-title {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 430px;
        margin: 5px auto 0;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {
        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .detail-header {
            flex-direction: column;
        }

        .detail-actions {
            width: 100%;
            flex-direction: column;
        }

        .detail-actions .btn-primary,
        .detail-actions .btn-secondary {
            width: 100%;
        }

        .detail-title {
            font-size: 24px;
        }

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .information-list {
            grid-template-columns: 1fr;
        }

        .information-item.full {
            grid-column: auto;
        }

        .schedule-item {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


@section('content')

<div class="detail-page">

    {{-- HEADER --}}
    <div class="detail-header">

        <div class="detail-header-main">

            <div class="breadcrumb">
                <a href="{{ route('admin.programs.index') }}">
                    Program & Paket
                </a>

                <span>/</span>

                <span>Detail Program</span>
            </div>

            <div class="detail-title-row">

                <h1 class="detail-title">
                    {{ $program->name }}
                </h1>

                @if ($program->status === 'active')
                    <span class="status-badge status-active">
                        Aktif
                    </span>
                @else
                    <span class="status-badge status-inactive">
                        Nonaktif
                    </span>
                @endif

            </div>

            <p class="detail-description">
                {{ $program->short_description ?: 'Belum ada deskripsi singkat untuk program ini.' }}
            </p>

        </div>


        <div class="detail-actions">

            <a
                href="{{ route('admin.programs.index') }}"
                class="btn-secondary"
            >
                <svg
                    class="button-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M15 18L9 12L15 6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                Kembali
            </a>

            <a
                href="{{ route('admin.programs.edit', $program) }}"
                class="btn-primary"
            >
                Edit Program
            </a>

        </div>

    </div>


    {{-- STATISTICS --}}
    <div class="statistics-grid">

        <div class="card stat-card">
            <div>
                <div class="stat-label">Total Paket</div>

                <div class="stat-value">
                    {{ $statistics['packages'] }}
                </div>

                <div class="stat-description">
                    Paket dalam program
                </div>
            </div>

            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="4"
                        y="5"
                        width="16"
                        height="14"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M8 10H16M8 14H13"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
        </div>


        <div class="card stat-card">
            <div>
                <div class="stat-label">Paket Aktif</div>

                <div class="stat-value">
                    {{ $statistics['active_packages'] }}
                </div>

                <div class="stat-description">
                    Sedang dapat ditawarkan
                </div>
            </div>

            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="8"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M8.5 12L11 14.5L15.5 9.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>


        <div class="card stat-card">
            <div>
                <div class="stat-label">Total Jadwal</div>

                <div class="stat-value">
                    {{ $statistics['schedules'] }}
                </div>

                <div class="stat-description">
                    Seluruh jadwal program
                </div>
            </div>

            <div class="stat-icon aqua">
                <svg viewBox="0 0 24 24" fill="none">
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
        </div>


        <div class="card stat-card">
            <div>
                <div class="stat-label">Durasi Pertemuan</div>

                <div class="stat-value">
                    {{ $statistics['duration_minutes'] }}
                </div>

                <div class="stat-description">
                    Menit per sesi
                </div>
            </div>

            <div class="stat-icon slate">
                <svg viewBox="0 0 24 24" fill="none">
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
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>

    </div>


    <div class="detail-grid">

        <div class="main-column">

            {{-- INFORMASI PROGRAM --}}
            <div class="card section-card">

                <div class="section-header">
                    <div>
                        <div class="section-title">
                            Informasi Program
                        </div>

                        <div class="section-subtitle">
                            Informasi utama dan deskripsi program pembelajaran.
                        </div>
                    </div>
                </div>


                <div class="section-body">

                    <div class="information-list">

                        <div class="information-item">
                            <div class="information-label">
                                Nama Program
                            </div>

                            <div class="information-value">
                                {{ $program->name }}
                            </div>
                        </div>


                        <div class="information-item">
                            <div class="information-label">
                                Slug
                            </div>

                            <div class="information-value">
                                <span class="slug-box">
                                    {{ $program->slug }}
                                </span>
                            </div>
                        </div>


                        <div class="information-item">
                            <div class="information-label">
                                Durasi
                            </div>

                            <div class="information-value">
                                {{ $program->duration_minutes }} menit / pertemuan
                            </div>
                        </div>


                        <div class="information-item">
                            <div class="information-label">
                                Status
                            </div>

                            <div class="information-value">
                                {{ $program->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </div>


                        <div class="information-item full">
                            <div class="information-label">
                                Deskripsi Singkat
                            </div>

                            <div class="information-value regular">
                                {{ $program->short_description ?: 'Belum ada deskripsi singkat.' }}
                            </div>
                        </div>


                        <div class="information-item full">
                            <div class="information-label">
                                Deskripsi Lengkap
                            </div>

                            <div class="information-value regular">
                                {{ $program->description ?: 'Belum ada deskripsi lengkap.' }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{-- PAKET PROGRAM --}}
            <div class="card section-card">

                <div class="section-header">

                    <div>
                        <div class="section-title">
                            Paket Program
                        </div>

                        <div class="section-subtitle">
                            Paket pembelian yang terhubung dengan program ini.
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.packages.create', ['program' => $program->id]) }}"
                        class="btn-secondary"
                    >
                        + Tambah Paket
                    </a>

                </div>


                @if ($program->packages->count() > 0)

                    <div class="table-wrapper">

                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Paket</th>
                                    <th>Sesi</th>
                                    <th>Masa Aktif</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($program->packages as $package)

                                    <tr>

                                        <td>
                                            <div class="table-name">
                                                {{ $package->name }}
                                            </div>

                                            <div class="table-muted">
                                                {{ \Illuminate\Support\Str::limit($package->description ?: 'Tanpa deskripsi', 55) }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ $package->total_sessions }} sesi
                                        </td>

                                        <td>
                                            @if ($package->validity_days)
                                                {{ $package->validity_days }} hari
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            <span class="price-text">
                                                {{ $package->formatted_price }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($package->status === 'active')
                                                <span class="mini-status mini-active">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="mini-status mini-inactive">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <a
                                                href="{{ route('admin.packages.show', $package) }}"
                                                class="table-action"
                                            >
                                                Detail
                                            </a>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-title">
                            Belum ada paket
                        </div>

                        <div class="empty-description">
                            Program ini belum memiliki paket pembelian.
                            Tambahkan paket agar nantinya dapat ditawarkan
                            kepada customer.
                        </div>

                    </div>

                @endif

            </div>


            {{-- JADWAL TERBARU --}}
            <div class="card section-card">

                <div class="section-header">

                    <div>
                        <div class="section-title">
                            Jadwal Terbaru
                        </div>

                        <div class="section-subtitle">
                            Maksimal 10 jadwal terakhir yang terhubung
                            dengan program ini.
                        </div>
                    </div>

                </div>


                @if ($program->schedules->count() > 0)

                    <div class="schedule-list">

                        @foreach ($program->schedules as $schedule)

                            <div class="schedule-item">

                                <div>
                                    <div class="schedule-title">
                                        {{ $schedule->title }}
                                    </div>

                                    <div class="schedule-meta">
                                        {{ \Carbon\Carbon::parse($schedule->class_date)->format('d M Y') }}
                                        ·
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </div>
                                </div>


                                <div>
                                    <div class="schedule-label">
                                        Coach
                                    </div>

                                    <div class="schedule-value">
                                        {{ $schedule->coach?->user?->name ?? '-' }}
                                    </div>
                                </div>


                                <div>
                                    <div class="schedule-label">
                                        Lokasi
                                    </div>

                                    <div class="schedule-value">
                                        {{ $schedule->location?->name ?? '-' }}
                                    </div>

                                    <div class="schedule-meta">
                                        {{ $schedule->bookings_count ?? 0 }}
                                        booking
                                    </div>
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-title">
                            Belum ada jadwal
                        </div>

                        <div class="empty-description">
                            Belum ada jadwal kelas yang terhubung
                            dengan program ini.
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- SIDE COLUMN --}}
        <div class="side-column">

            {{-- QUICK ACTION --}}
            <div class="card quick-card">

                <div class="quick-title">
                    Aksi Cepat
                </div>

                <div class="quick-description">
                    Kelola program dan paket yang terhubung dari sini.
                </div>

                <div class="quick-actions">

                    <a
                        href="{{ route('admin.programs.edit', $program) }}"
                        class="btn-primary"
                    >
                        Edit Program
                    </a>

                    <a
                        href="{{ route('admin.packages.create', ['program' => $program->id]) }}"
                        class="btn-secondary"
                    >
                        Tambah Paket
                    </a>

                    <a
                        href="{{ route('admin.packages.index', ['program' => $program->id]) }}"
                        class="btn-secondary"
                    >
                        Lihat Semua Paket
                    </a>

                </div>

            </div>


            {{-- STATUS --}}
            <div class="card status-card">

                <div class="status-card-title">
                    Status Program
                </div>

                <div class="status-card-description">
                    Atur apakah program ini masih aktif digunakan
                    dalam layanan Ragen Swim Course.
                </div>


                <div class="status-current">

                    <div class="status-current-label">
                        Status saat ini
                    </div>

                    @if ($program->status === 'active')

                        <span class="status-badge status-active">
                            Aktif
                        </span>

                    @else

                        <span class="status-badge status-inactive">
                            Nonaktif
                        </span>

                    @endif

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.programs.status.update', $program) }}"
                    class="status-form"
                    onsubmit="return confirm('Yakin ingin mengubah status program ini?')"
                >
                    @csrf
                    @method('PATCH')

                    @if ($program->status === 'active')

                        <input
                            type="hidden"
                            name="status"
                            value="inactive"
                        >

                        <button
                            type="submit"
                            class="status-button deactivate"
                        >
                            Nonaktifkan Program
                        </button>

                    @else

                        <input
                            type="hidden"
                            name="status"
                            value="active"
                        >

                        <button
                            type="submit"
                            class="status-button activate"
                        >
                            Aktifkan Program
                        </button>

                    @endif

                </form>

            </div>

        </div>

    </div>

</div>

@endsection
