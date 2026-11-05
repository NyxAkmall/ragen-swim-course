
@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')
@section('topbar-title', 'Manajemen Lokasi')

@push('styles')
<style>
    .location-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .location-header-actions svg {
        width: 18px;
        height: 18px;
    }

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat-card {
        padding: 20px;
    }

    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 7px;
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .stat-icon.success {
        background: var(--success-bg);
        color: var(--success);
    }

    .stat-icon.inactive {
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

    .filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 210px auto;
        gap: 12px;
        align-items: end;
    }

    .form-group {
        min-width: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .form-control {
        width: 100%;
        min-height: 42px;
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
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.09);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .table-card {
        overflow: hidden;
    }

    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }

    .table-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .table-subtitle {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 12px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .location-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .location-table th {
        padding: 13px 16px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        white-space: nowrap;
    }

    .location-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text);
        font-size: 13px;
        vertical-align: middle;
    }

    .location-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .location-table tbody tr:hover {
        background: #fbfdff;
    }

    .location-name {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .location-address {
        max-width: 300px;
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .city-text {
        font-weight: 700;
    }

    .schedule-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-soft);
        font-weight: 700;
    }

    .schedule-count svg {
        width: 16px;
        height: 16px;
    }

    /* Tarif masuk kolam */
    .entry-fee-list {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 145px;
    }

    .entry-fee-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .entry-fee-type {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 650;
    }

    .entry-fee-amount {
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .entry-fee-amount.free {
        color: var(--success);
    }

    .entry-fee-note {
        margin-top: 5px;
        color: var(--text-light);
        font-size: 10px;
    }

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

    .table-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .action-link {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
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

    .action-link svg {
        width: 14px;
        height: 14px;
    }

    .empty-state {
        padding: 60px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
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
        max-width: 440px;
        margin: 7px auto 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .empty-action {
        margin-top: 18px;
    }

    /* Pagination custom */
    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 17px 22px;
        border-top: 1px solid var(--border);
    }

    .pagination-info {
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .pagination-info strong {
        color: var(--navy);
        font-weight: 800;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: wrap;
    }

    .pagination-btn {
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 11px;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: var(--white);
        color: var(--navy);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        transition:
            background 0.2s ease,
            border-color 0.2s ease,
            color 0.2s ease;
    }

    a.pagination-btn:hover {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: var(--blue-dark);
    }

    .pagination-btn.active {
        background: var(--navy);
        border-color: var(--navy);
        color: #ffffff;
    }

    .pagination-btn.disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .pagination-btn.dots {
        border-color: transparent;
        background: transparent;
        color: var(--text-soft);
    }

    .pagination-btn:focus-visible {
        outline: 2px solid var(--blue);
        outline-offset: 2px;
    }

    @media (max-width: 1100px) {
        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            width: 100%;
        }

        .filter-actions .btn-primary,
        .filter-actions .btn-secondary {
            flex: 1;
        }
    }

    @media (max-width: 650px) {
        .page-header {
            display: block;
        }

        .location-header-actions {
            margin-top: 17px;
        }

        .location-header-actions .btn-primary {
            width: 100%;
        }

        .statistics-grid {
            grid-template-columns: 1fr 1fr;
            gap: 11px;
        }

        .stat-card {
            padding: 16px;
        }

        .stat-value {
            font-size: 23px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
        }

        .table-header {
            padding: 17px;
        }

        .pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
            padding: 15px;
        }

        .pagination-info {
            text-align: center;
        }

        .pagination-controls {
            justify-content: center;
        }
    }

    @media (max-width: 420px) {
        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .pagination-btn {
            min-width: 32px;
            height: 34px;
            padding: 0 8px;
            font-size: 11px;
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

<div class="page-header">
    <div>
        <h1 class="page-title">
            Manajemen Lokasi
        </h1>

        <p class="page-description">
            Kelola lokasi kolam renang, biaya masuk per sesi,
            dan jadwal Ragen Swim Course.
        </p>
    </div>

    <div class="location-header-actions">
        <a
            href="{{ route('admin.locations.create') }}"
            class="btn-primary"
        >
            <svg
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

            Tambah Lokasi
        </a>
    </div>
</div>

{{-- STATISTIK --}}
<div class="statistics-grid">

    <div class="card stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">
                    Total Lokasi
                </div>

                <div class="stat-value">
                    {{ number_format($statistics['total']) }}
                </div>
            </div>

            <div class="stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M12 21C12 21 18 15.8 18 10C18 6.7 15.3 4 12 4C8.7 4 6 6.7 6 10C6 15.8 12 21 12 21Z"
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
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">
                    Lokasi Aktif
                </div>

                <div class="stat-value">
                    {{ number_format($statistics['active']) }}
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
        <div class="stat-card-top">
            <div>
                <div class="stat-label">
                    Lokasi Nonaktif
                </div>

                <div class="stat-value">
                    {{ number_format($statistics['inactive']) }}
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
                        d="M8.5 8.5L15.5 15.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-card-top">
            <div>
                <div class="stat-label">
                    Total Jadwal
                </div>

                <div class="stat-value">
                    {{ number_format($statistics['schedules']) }}
                </div>
            </div>

            <div class="stat-icon aqua">
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

</div>

{{-- FILTER --}}
<div class="card filter-card">

    <form
        method="GET"
        action="{{ route('admin.locations.index') }}"
        class="filter-form"
    >

        <div class="form-group">
            <label for="search" class="form-label">
                Cari Lokasi
            </label>

            <input
                type="text"
                id="search"
                name="search"
                class="form-control"
                value="{{ $search }}"
                placeholder="Nama lokasi, alamat, kota..."
            >
        </div>

        <div class="form-group">
            <label for="status" class="form-label">
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
                Terapkan
            </button>

            @if ($search !== '' || $status !== '')
                <a
                    href="{{ route('admin.locations.index') }}"
                    class="btn-secondary"
                >
                    Reset
                </a>
            @endif

        </div>

    </form>

</div>

{{-- DAFTAR LOKASI --}}
<div class="card table-card">

    <div class="table-header">
        <div>
            <div class="table-title">
                Daftar Lokasi
            </div>

            <div class="table-subtitle">
                {{ $locations->total() }} lokasi ditemukan
            </div>
        </div>
    </div>

    @if ($locations->count() > 0)

        <div class="table-responsive">

            <table class="location-table">

                <thead>
                    <tr>
                        <th>Lokasi</th>
                        <th>Kota</th>
                        <th>Biaya Masuk</th>
                        <th>Jadwal</th>
                        <th>Google Maps</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($locations as $location)

                        <tr>

                            <td>
                                <div class="location-name">
                                    {{ $location->name }}
                                </div>

                                <div class="location-address">
                                    {{ $location->address }}
                                </div>
                            </td>

                            <td>
                                <span class="city-text">
                                    {{ $location->city }}
                                </span>
                            </td>

                            {{-- BIAYA MASUK --}}
                            <td>
                                <div class="entry-fee-list">

                                    <div class="entry-fee-row">
                                        <span class="entry-fee-type">
                                            Customer
                                        </span>

                                        <span class="entry-fee-amount {{ (float) $location->customer_entry_fee == 0 ? 'free' : '' }}">
                                            {{ $location->customer_entry_fee_formatted }}
                                        </span>
                                    </div>

                                    <div class="entry-fee-row">
                                        <span class="entry-fee-type">
                                            Coach
                                        </span>

                                        <span class="entry-fee-amount {{ (float) $location->coach_entry_fee == 0 ? 'free' : '' }}">
                                            {{ $location->coach_entry_fee_formatted }}
                                        </span>
                                    </div>

                                    <div class="entry-fee-note">
                                        Per orang / sesi
                                    </div>

                                </div>
                            </td>

                            <td>
                                <span class="schedule-count">
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

                                    {{ $location->schedules_count }}
                                </span>
                            </td>

                            <td>
                                @if ($location->hasGoogleMaps())

                                    <a
                                        href="{{ $location->google_maps_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="action-link"
                                    >
                                        Buka Maps

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
                                                d="M19 13V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V6C5 5.4 5 6 5 6H11"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            />
                                        </svg>
                                    </a>

                                @else

                                    <span style="color: var(--text-light);">
                                        Belum tersedia
                                    </span>

                                @endif
                            </td>

                            <td>
                                @if ($location->status === 'active')

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
                                <div class="table-actions">

                                    <a
                                        href="{{ route('admin.locations.show', $location) }}"
                                        class="action-link"
                                    >
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('admin.locations.edit', $location) }}"
                                        class="action-link"
                                    >
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.locations.destroy', $location) }}"
                                          class="delete-form"
                                          onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
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

        {{-- PAGINATION CUSTOM --}}
        @if ($locations->hasPages())

            @php
                $currentPage = $locations->currentPage();
                $lastPage = $locations->lastPage();

                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);

                if ($currentPage <= 3) {
                    $endPage = min($lastPage, 5);
                }

                if ($currentPage >= $lastPage - 2) {
                    $startPage = max(1, $lastPage - 4);
                }
            @endphp

            <nav
                class="pagination-wrapper"
                aria-label="Navigasi halaman lokasi"
            >

                <div class="pagination-info">
                    Menampilkan
                    <strong>{{ $locations->firstItem() }}</strong>
                    –
                    <strong>{{ $locations->lastItem() }}</strong>
                    dari
                    <strong>{{ $locations->total() }}</strong>
                    lokasi
                </div>

                <div class="pagination-controls">

                    @if ($locations->onFirstPage())

                        <span
                            class="pagination-btn disabled"
                            aria-disabled="true"
                        >
                            Sebelumnya
                        </span>

                    @else

                        <a
                            href="{{ $locations->previousPageUrl() }}"
                            class="pagination-btn"
                            rel="prev"
                        >
                            Sebelumnya
                        </a>

                    @endif

                    @if ($startPage > 1)

                        <a
                            href="{{ $locations->url(1) }}"
                            class="pagination-btn"
                            aria-label="Halaman 1"
                        >
                            1
                        </a>

                        @if ($startPage > 2)
                            <span
                                class="pagination-btn dots"
                                aria-hidden="true"
                            >
                                …
                            </span>
                        @endif

                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)

                        @if ($page === $currentPage)

                            <span
                                class="pagination-btn active"
                                aria-current="page"
                            >
                                {{ $page }}
                            </span>

                        @else

                            <a
                                href="{{ $locations->url($page) }}"
                                class="pagination-btn"
                                aria-label="Halaman {{ $page }}"
                            >
                                {{ $page }}
                            </a>

                        @endif

                    @endfor

                    @if ($endPage < $lastPage)

                        @if ($endPage < $lastPage - 1)
                            <span
                                class="pagination-btn dots"
                                aria-hidden="true"
                            >
                                …
                            </span>
                        @endif

                        <a
                            href="{{ $locations->url($lastPage) }}"
                            class="pagination-btn"
                            aria-label="Halaman {{ $lastPage }}"
                        >
                            {{ $lastPage }}
                        </a>

                    @endif

                    @if ($locations->hasMorePages())

                        <a
                            href="{{ $locations->nextPageUrl() }}"
                            class="pagination-btn"
                            rel="next"
                        >
                            Berikutnya
                        </a>

                    @else

                        <span
                            class="pagination-btn disabled"
                            aria-disabled="true"
                        >
                            Berikutnya
                        </span>

                    @endif

                </div>

            </nav>

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

            <div class="empty-title">
                @if ($search !== '' || $status !== '')
                    Lokasi tidak ditemukan
                @else
                    Belum ada lokasi
                @endif
            </div>

            <div class="empty-description">
                @if ($search !== '' || $status !== '')
                    Tidak ada lokasi yang sesuai dengan pencarian
                    atau filter yang digunakan.
                @else
                    Tambahkan lokasi kolam renang pertama yang akan
                    digunakan untuk jadwal kelas Ragen Swim Course.
                @endif
            </div>

            <div class="empty-action">

                @if ($search !== '' || $status !== '')

                    <a
                        href="{{ route('admin.locations.index') }}"
                        class="btn-secondary"
                    >
                        Reset Pencarian
                    </a>

                @else

                    <a
                        href="{{ route('admin.locations.create') }}"
                        class="btn-primary"
                    >
                        Tambah Lokasi
                    </a>

                @endif

            </div>

        </div>

    @endif

</div>

@endsection
