@extends('layouts.admin')

@section('title', 'Program & Paket')
@section('topbar-title', 'Program & Paket')

@push('styles')
<style>
    .program-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .program-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .program-header-content {
        min-width: 0;
    }

    .program-header-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .program-header-description {
        max-width: 720px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .program-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
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
        padding: 6px;
        width: fit-content;
        max-width: 100%;
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
        transition:
            background 0.2s ease,
            color 0.2s ease;
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
        background: rgba(255, 255, 255, 0.15);
        font-size: 10px;
        font-weight: 800;
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
        grid-template-columns: minmax(250px, 1fr) 220px auto;
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
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
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
    | Program List
    |--------------------------------------------------------------------------
    */

    .program-list-card {
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

    .program-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .program-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .program-table th {
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

    .program-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 13px;
    }

    .program-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .program-table tbody tr:hover {
        background: #fcfdff;
    }

    .program-name {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .program-slug {
        margin-top: 4px;
        color: var(--text-light);
        font-size: 10px;
    }

    .program-description {
        max-width: 320px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.55;
    }

    .number-value {
        color: var(--text);
        font-weight: 750;
    }

    /*
    |--------------------------------------------------------------------------
    | Badge
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
        max-width: 420px;
        margin: 6px auto 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    .pagination-area {
        padding: 17px 20px;
        border-top: 1px solid var(--border);
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

        .filter-form {
            grid-template-columns: 1fr 200px;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 700px) {
        .program-header {
            flex-direction: column;
        }

        .program-header-actions {
            width: 100%;
        }

        .program-header-actions .btn-primary {
            width: 100%;
        }

        .program-header-title {
            font-size: 24px;
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

<div class="program-page">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="program-header">

        <div class="program-header-content">

            <h1 class="program-header-title">
                Program & Paket
            </h1>

            <p class="program-header-description">
                Kelola program pembelajaran renang beserta paket
                pertemuan yang dapat dibeli oleh customer Ragen Swim Course.
            </p>

        </div>


        <div class="program-header-actions">

            <a
                href="{{ route('admin.programs.create') }}"
                class="btn-primary"
            >
                <svg
                    class="button-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M12 5V19M5 12H19"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>

                Tambah Program
            </a>

        </div>

    </div>


    {{-- =========================================================
        TABS
    ========================================================== --}}

    <div class="management-tabs">

        <a
            href="{{ route('admin.programs.index') }}"
            class="management-tab active"
        >
            Program

            <span class="management-tab-count">
                {{ $statistics['total'] }}
            </span>
        </a>


        <a
            href="{{ route('admin.packages.index') }}"
            class="management-tab"
        >
            Paket

            <span
                class="management-tab-count"
                style="background:#f1f5f9;color:#475569;"
            >
                {{ $statistics['packages'] }}
            </span>
        </a>

    </div>


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}

    <div class="statistics-grid">

        {{-- TOTAL PROGRAM --}}
        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Total Program
                </div>

                <div class="stat-value">
                    {{ $statistics['total'] }}
                </div>

                <div class="stat-description">
                    Seluruh program renang
                </div>
            </div>


            <div class="stat-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
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

                    <path
                        d="M4 16.5L12 20L20 16.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>

            </div>

        </div>


        {{-- PROGRAM AKTIF --}}
        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Program Aktif
                </div>

                <div class="stat-value">
                    {{ $statistics['active'] }}
                </div>

                <div class="stat-description">
                    Dapat digunakan saat ini
                </div>
            </div>


            <div class="stat-icon success">

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
                        d="M8.5 12L11 14.5L15.5 9.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            </div>

        </div>


        {{-- PROGRAM NONAKTIF --}}
        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Program Nonaktif
                </div>

                <div class="stat-value">
                    {{ $statistics['inactive'] }}
                </div>

                <div class="stat-description">
                    Tidak ditawarkan sementara
                </div>
            </div>


            <div class="stat-icon muted">

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


        {{-- TOTAL PAKET --}}
        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Total Paket
                </div>

                <div class="stat-value">
                    {{ $statistics['packages'] }}
                </div>

                <div class="stat-description">
                    Paket dari seluruh program
                </div>
            </div>


            <div class="stat-icon aqua">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M5 6H19V18H5V6Z"
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

    </div>


    {{-- =========================================================
        FILTER
    ========================================================== --}}

    <div class="card filter-card">

        <form
            method="GET"
            action="{{ route('admin.programs.index') }}"
            class="filter-form"
        >

            <div class="form-group">

                <label
                    for="search"
                    class="form-label"
                >
                    Cari Program
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Cari nama, slug, atau deskripsi program..."
                >

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

                @if ($search !== '' || $status !== '')

                    <a
                        href="{{ route('admin.programs.index') }}"
                        class="btn-secondary"
                    >
                        Reset
                    </a>

                @endif

            </div>

        </form>

    </div>


    {{-- =========================================================
        PROGRAM LIST
    ========================================================== --}}

    <div class="card program-list-card">

        <div class="list-header">

            <div>
                <div class="list-title">
                    Daftar Program
                </div>

                <div class="list-subtitle">
                    {{ $programs->total() }} program ditemukan
                </div>
            </div>

        </div>


        @if ($programs->count() > 0)

            <div class="program-table-wrapper">

                <table class="program-table">

                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Deskripsi</th>
                            <th>Durasi</th>
                            <th>Paket</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($programs as $program)

                            <tr>

                                <td>
                                    <div class="program-name">
                                        {{ $program->name }}
                                    </div>

                                    <div class="program-slug">
                                        {{ $program->slug }}
                                    </div>
                                </td>


                                <td>
                                    <div class="program-description">
                                        {{ $program->short_description ?: 'Belum ada deskripsi singkat.' }}
                                    </div>
                                </td>


                                <td>
                                    <span class="number-value">
                                        {{ $program->duration_minutes }} menit
                                    </span>
                                </td>


                                <td>
                                    <span class="number-value">
                                        {{ $program->packages_count }}
                                    </span>
                                </td>


                                <td>
                                    <span class="number-value">
                                        {{ $program->schedules_count }}
                                    </span>
                                </td>


                                <td>

                                    @if ($program->status === 'active')

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
                                            href="{{ route('admin.programs.show', $program) }}"
                                            class="action-link"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('admin.programs.edit', $program) }}"
                                            class="action-link"
                                        >
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.programs.destroy', $program) }}"
                                              class="delete-form"
                                              onsubmit="return confirm('Yakin ingin menghapus program ini?')">
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


            @if ($programs->hasPages())

                <div class="pagination-area">
                    {{ $programs->links() }}
                </div>

            @endif

        @else

            <div class="empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
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

                        <path
                            d="M4 16.5L12 20L20 16.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                    </svg>

                </div>


                <div class="empty-title">
                    Program tidak ditemukan
                </div>

                <div class="empty-description">

                    @if ($search !== '' || $status !== '')

                        Tidak ada program yang sesuai dengan
                        pencarian atau filter yang digunakan.

                    @else

                        Belum ada program renang yang tersimpan.
                        Tambahkan program pertama untuk mulai
                        membuat paket pembelajaran.

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>

@endsection
