@extends('layouts.admin')

@section('title', 'Manajemen Paket')
@section('topbar-title', 'Program & Paket')

@push('styles')
<style>
    .package-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .package-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .package-header-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .package-header-description {
        max-width: 720px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .button-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */

    .management-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        max-width: 100%;
        padding: 6px;
        border: 1px solid var(--border);
        border-radius: 13px;
        background: var(--white);
    }

    .management-tab {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 15px;
        border-radius: 9px;
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        font-weight: 750;
    }

    .management-tab:hover {
        background: #f8fafc;
        color: var(--navy);
    }

    .management-tab.active {
        background: var(--navy);
        color: var(--white);
    }

    .management-tab-count {
        min-width: 23px;
        height: 23px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .management-tab.active .management-tab-count {
        background: rgba(255, 255, 255, 0.15);
        color: var(--white);
    }

    .management-tab:not(.active) .management-tab-count {
        background: #f1f5f9;
        color: #475569;
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
        min-height: 125px;
        padding: 19px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.6px;
    }

    .stat-description {
        margin-top: 4px;
        color: var(--text-light);
        font-size: 11px;
        line-height: 1.5;
    }

    .stat-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .stat-icon.success {
        background: #f0fdf4;
        color: var(--success);
    }

    .stat-icon.muted {
        background: #f8fafc;
        color: var(--text-soft);
    }

    .stat-icon.aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-icon svg {
        width: 21px;
        height: 21px;
    }

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    .filter-card {
        padding: 18px;
    }

    .filter-form {
        display: grid;
        grid-template-columns:
            minmax(220px, 1fr)
            minmax(180px, 240px)
            minmax(150px, 190px)
            auto;
        gap: 12px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-label {
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .form-control {
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 13px;
    }

    .form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    .list-card {
        overflow: hidden;
    }

    .list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .list-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .list-subtitle {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .package-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .package-table th {
        padding: 12px 18px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.55px;
    }

    .package-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 13px;
    }

    .package-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .package-table tbody tr:hover {
        background: #fcfdff;
    }

    .package-name {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .package-description {
        max-width: 270px;
        margin-top: 4px;
        color: var(--text-light);
        font-size: 10px;
        line-height: 1.5;
    }

    .program-name {
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .number-value {
        color: var(--text);
        font-weight: 750;
        white-space: nowrap;
    }

    .price-value {
        color: var(--navy);
        font-weight: 850;
        white-space: nowrap;
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
        padding: 6px 9px;
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
    | Actions
    |--------------------------------------------------------------------------
    */

    .row-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .action-link {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--white);
        color: var(--navy);
        text-decoration: none;
        font-size: 11px;
        font-weight: 750;
        white-space: nowrap;
    }

    .action-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: var(--blue-dark);
    }

    /*
    |--------------------------------------------------------------------------
    | Empty
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 55px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .empty-icon svg {
        width: 28px;
        height: 28px;
    }

    .empty-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 430px;
        margin: 6px auto 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .pagination-area {
        padding: 17px 20px;
        border-top: 1px solid var(--border);
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1200px) {
        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 700px) {
        .package-header {
            flex-direction: column;
        }

        .package-header-title {
            font-size: 24px;
        }

        .package-header .btn-primary {
            width: 100%;
        }

        .management-tabs {
            width: 100%;
        }

        .management-tab {
            flex: 1;
        }

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            grid-column: auto;
            flex-direction: column;
        }

        .filter-actions .btn-primary,
        .filter-actions .btn-secondary {
            width: 100%;
        }
    }

    /* Tombol hapus data */
    .delete-form {
        display: inline-flex;
        margin: 0;
    }

    .delete-button {
        min-height: 34px;
        padding: 7px 10px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fff7f7;
        color: #dc2626;
        font-size: 11px;
        font-weight: 750;
        cursor: pointer;
        white-space: nowrap;
    }

    .delete-button:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
    }
</style>
@endpush


@section('content')

<div class="package-page">

    {{-- HEADER --}}
    <div class="package-header">

        <div>
            <h1 class="package-header-title">
                Manajemen Paket
            </h1>

            <p class="package-header-description">
                Kelola paket pertemuan, jumlah sesi, harga, masa aktif,
                serta program yang terhubung dengan setiap paket.
            </p>
        </div>


        <a
            href="{{ route('admin.packages.create') }}"
            class="btn-primary"
        >
            <svg
                class="button-icon"
                viewBox="0 0 24 24"
                fill="none"
            >
                <path
                    d="M12 5V19M5 12H19"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>

            Tambah Paket
        </a>

    </div>


    {{-- TABS --}}
    <div class="management-tabs">

        <a
            href="{{ route('admin.programs.index') }}"
            class="management-tab"
        >
            Program
        </a>

        <a
            href="{{ route('admin.packages.index') }}"
            class="management-tab active"
        >
            Paket

            <span class="management-tab-count">
                {{ $statistics['total'] }}
            </span>
        </a>

    </div>


    {{-- STATISTICS --}}
    <div class="statistics-grid">

        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Total Paket
                </div>

                <div class="stat-value">
                    {{ $statistics['total'] }}
                </div>

                <div class="stat-description">
                    Seluruh paket renang
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
                <div class="stat-label">
                    Paket Aktif
                </div>

                <div class="stat-value">
                    {{ $statistics['active'] }}
                </div>

                <div class="stat-description">
                    Dapat ditawarkan saat ini
                </div>
            </div>

            <div class="stat-icon success">
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
                <div class="stat-label">
                    Paket Nonaktif
                </div>

                <div class="stat-value">
                    {{ $statistics['inactive'] }}
                </div>

                <div class="stat-description">
                    Tidak ditawarkan sementara
                </div>
            </div>

            <div class="stat-icon muted">
                <svg viewBox="0 0 24 24" fill="none">
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


        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Program dengan Paket
                </div>

                <div class="stat-value">
                    {{ $statistics['programs'] }}
                </div>

                <div class="stat-description">
                    Program yang memiliki paket
                </div>
            </div>

            <div class="stat-icon aqua">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M4 12L12 15.5L20 12"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </div>

        </div>

    </div>


    {{-- FILTER --}}
    <div class="card filter-card">

        <form
            method="GET"
            action="{{ route('admin.packages.index') }}"
            class="filter-form"
        >

            <div class="form-group">

                <label
                    for="search"
                    class="form-label"
                >
                    Cari Paket
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Cari nama paket, program, atau deskripsi..."
                >

            </div>


            <div class="form-group">

                <label
                    for="program"
                    class="form-label"
                >
                    Program
                </label>

                <select
                    id="program"
                    name="program"
                    class="form-control"
                >
                    <option value="">
                        Semua Program
                    </option>

                    @foreach ($programs as $program)

                        <option
                            value="{{ $program->id }}"
                            {{ (int) $programId === (int) $program->id ? 'selected' : '' }}
                        >
                            {{ $program->name }}
                        </option>

                    @endforeach
                </select>

            </div>


            <div class="form-group">

                <label
                    for="status"
                    class="form-label"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="form-control"
                >
                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="active"
                        {{ $status === 'active' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="inactive"
                        {{ $status === 'inactive' ? 'selected' : '' }}
                    >
                        Nonaktif
                    </option>
                </select>

            </div>


            <div class="filter-actions">

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Terapkan Filter
                </button>

                @if (
                    $search !== ''
                    || $status !== ''
                    || $programId > 0
                )

                    <a
                        href="{{ route('admin.packages.index') }}"
                        class="btn-secondary"
                    >
                        Reset
                    </a>

                @endif

            </div>

        </form>

    </div>


    {{-- LIST --}}
    <div class="card list-card">

        <div class="list-header">

            <div>
                <div class="list-title">
                    Daftar Paket
                </div>

                <div class="list-subtitle">
                    {{ $packages->total() }} paket ditemukan
                </div>
            </div>

        </div>


        @if ($packages->count() > 0)

            <div class="table-wrapper">

                <table class="package-table">

                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Program</th>
                            <th>Sesi</th>
                            <th>Masa Aktif</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($packages as $package)

                            <tr>

                                <td>

                                    <div class="package-name">
                                        {{ $package->name }}
                                    </div>

                                    <div class="package-description">
                                        {{ \Illuminate\Support\Str::limit(
                                            $package->description ?: 'Belum ada deskripsi paket.',
                                            70
                                        ) }}
                                    </div>

                                </td>


                                <td>
                                    <div class="program-name">
                                        {{ $package->program?->name ?? '-' }}
                                    </div>
                                </td>


                                <td>
                                    <span class="number-value">
                                        {{ $package->total_sessions }} sesi
                                    </span>
                                </td>


                                <td>
                                    <span class="number-value">

                                        @if ($package->validity_days)
                                            {{ $package->validity_days }} hari
                                        @else
                                            -
                                        @endif

                                    </span>
                                </td>


                                <td>
                                    <span class="price-value">
                                        {{ $package->formatted_price }}
                                    </span>
                                </td>


                                <td>

                                    @if ($package->status === 'active')

                                        <span class="status-badge status-active">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="status-badge status-inactive">
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="row-actions">

                                        <a
                                            href="{{ route('admin.packages.show', $package) }}"
                                            class="action-link"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('admin.packages.edit', $package) }}"
                                            class="action-link"
                                        >
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.packages.destroy', $package) }}"
                                              class="delete-form"
                                              onsubmit="return confirm('Yakin ingin menghapus paket ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-button">
                                                Delete
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            @if ($packages->hasPages())

                <div class="pagination-area">
                    <nav aria-label="Navigasi halaman paket" style="display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:12px;padding:14px 0;">
                        @if ($packages->onFirstPage())
                            <span style="padding:10px 16px;border:1px solid #e2e8f0;border-radius:10px;color:#94a3b8;">&larr; Sebelumnya</span>
                        @else
                            <a href="{{ $packages->previousPageUrl() }}" rel="prev" style="padding:10px 16px;border:1px solid #cbd5e1;border-radius:10px;color:#075985;text-decoration:none;font-weight:600;">&larr; Sebelumnya</a>
                        @endif
                        <span style="color:#475569;font-size:14px;">Halaman {{ $packages->currentPage() }} dari {{ $packages->lastPage() }}</span>
                        @if ($packages->hasMorePages())
                            <a href="{{ $packages->nextPageUrl() }}" rel="next" style="padding:10px 16px;border:1px solid #cbd5e1;border-radius:10px;color:#075985;text-decoration:none;font-weight:600;">Berikutnya &rarr;</a>
                        @else
                            <span style="padding:10px 16px;border:1px solid #e2e8f0;border-radius:10px;color:#94a3b8;">Berikutnya &rarr;</span>
                        @endif
                    </nav>
                </div>

            @endif

        @else

            <div class="empty-state">

                <div class="empty-icon">

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

                <div class="empty-title">
                    Paket tidak ditemukan
                </div>

                <div class="empty-description">

                    @if (
                        $search !== ''
                        || $status !== ''
                        || $programId > 0
                    )

                        Tidak ada paket yang sesuai dengan
                        pencarian atau filter yang digunakan.

                    @else

                        Belum ada paket pembelajaran yang tersimpan.
                        Tambahkan paket pertama pada salah satu program.

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection
