@extends('layouts.admin')

@section('title', 'Customer & Peserta')
@section('topbar-title', 'Customer & Peserta')

@section('content')

@php
    $statusLabels = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'suspended' => 'Ditangguhkan',
    ];
@endphp


{{-- =========================================================
    PAGE HEADER
========================================================== --}}

<div class="page-header">

    <div>
        <h1 class="page-title">
            Customer & Peserta
        </h1>

        <p class="page-description">
            Kelola dan pantau akun customer beserta peserta
            yang terdaftar di Ragen Swim Course.
        </p>
    </div>

</div>


{{-- =========================================================
    STATISTICS
========================================================== --}}

<section class="customer-statistics">

    {{-- TOTAL CUSTOMER --}}
    <div class="customer-stat-card">

        <div class="customer-stat-icon icon-blue">
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

        <div>
            <div class="customer-stat-label">
                Total Customer
            </div>

            <div class="customer-stat-value">
                {{ number_format($statistics['total'] ?? 0) }}
            </div>

            <div class="customer-stat-description">
                Seluruh akun customer
            </div>
        </div>

    </div>


    {{-- CUSTOMER AKTIF --}}
    <div class="customer-stat-card">

        <div class="customer-stat-icon icon-green">
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

        <div>
            <div class="customer-stat-label">
                Customer Aktif
            </div>

            <div class="customer-stat-value">
                {{ number_format($statistics['active'] ?? 0) }}
            </div>

            <div class="customer-stat-description">
                Akun berstatus aktif
            </div>
        </div>

    </div>


    {{-- SUSPENDED --}}
    <div class="customer-stat-card">

        <div class="customer-stat-icon icon-red">
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

        <div>
            <div class="customer-stat-label">
                Ditangguhkan
            </div>

            <div class="customer-stat-value">
                {{ number_format($statistics['suspended'] ?? 0) }}
            </div>

            <div class="customer-stat-description">
                Akun customer suspended
            </div>
        </div>

    </div>


    {{-- PESERTA --}}
    <div class="customer-stat-card">

        <div class="customer-stat-icon icon-aqua">
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

        <div>
            <div class="customer-stat-label">
                Total Peserta
            </div>

            <div class="customer-stat-value">
                {{ number_format($statistics['participants'] ?? 0) }}
            </div>

            <div class="customer-stat-description">
                Profil peserta terdaftar
            </div>
        </div>

    </div>

</section>


{{-- =========================================================
    CUSTOMER TABLE CARD
========================================================== --}}

<section class="customer-card">

    {{-- FILTER --}}
    <div class="customer-card-header">

        <div>
            <h2>
                Daftar Customer
            </h2>

            <p>
                Cari customer berdasarkan nama, email,
                nomor telepon, atau status akun.
            </p>
        </div>

    </div>


    <form
        method="GET"
        action="{{ route('admin.customers.index') }}"
        class="customer-filter"
    >

        <div class="search-wrapper">

            <span class="search-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="11"
                        cy="11"
                        r="6"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M16 16L20 20"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="filter-input search-input"
                placeholder="Cari nama, email, atau nomor telepon..."
            >

        </div>


        <select
            name="status"
            class="filter-input status-select"
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
                value="suspended"
                {{ $status === 'suspended' ? 'selected' : '' }}
            >
                Ditangguhkan
            </option>

            <option
                value="inactive"
                {{ $status === 'inactive' ? 'selected' : '' }}
            >
                Tidak Aktif
            </option>
        </select>


        <button
            type="submit"
            class="filter-button"
        >
            Terapkan
        </button>


        @if ($search !== '' || $status !== '')

            <a
                href="{{ route('admin.customers.index') }}"
                class="reset-button"
            >
                Reset
            </a>

        @endif

    </form>


    {{-- =====================================================
        TABLE
    ====================================================== --}}

    @if ($customers->isEmpty())

        <div class="customer-empty">

            <div class="customer-empty-icon">
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

            <strong>
                Customer tidak ditemukan
            </strong>

            <span>
                Coba ubah kata pencarian atau filter status.
            </span>

        </div>

    @else

        <div class="customer-table-wrapper">

            <table class="customer-table">

                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Kontak</th>
                        <th>Peserta</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th class="action-column">Aksi</th>
                    </tr>
                </thead>


                <tbody>

                    @foreach ($customers as $customer)

                        @php
                            $initials = collect(
                                preg_split(
                                    '/\s+/',
                                    trim($customer->name)
                                )
                            )
                                ->filter()
                                ->take(2)
                                ->map(function ($word) {
                                    return strtoupper(
                                        mb_substr(
                                            $word,
                                            0,
                                            1
                                        )
                                    );
                                })
                                ->implode('');

                            $customerStatus =
                                $statusLabels[$customer->status]
                                ?? ucfirst($customer->status);
                        @endphp

                        <tr>

                            {{-- CUSTOMER --}}
                            <td>

                                <div class="customer-profile">

                                    <div class="customer-avatar">
                                        {{ $initials ?: 'C' }}
                                    </div>


                                    <div class="customer-profile-info">

                                        <strong>
                                            {{ $customer->name }}
                                        </strong>

                                        <span>
                                            ID Customer #{{ $customer->id }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- KONTAK --}}
                            <td>

                                <div class="contact-info">

                                    <span class="contact-email">
                                        {{ $customer->email }}
                                    </span>

                                    <span class="contact-phone">
                                        {{ $customer->phone ?: 'Belum ada nomor telepon' }}
                                    </span>

                                </div>

                            </td>


                            {{-- PESERTA --}}
                            <td>

                                <div class="participant-count">

                                    <strong>
                                        {{ $customer->participants_count }}
                                    </strong>

                                    <span>
                                        peserta
                                    </span>

                                </div>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if ($customer->status === 'active')

                                    <span class="customer-status status-active">
                                        <span class="status-dot"></span>
                                        {{ $customerStatus }}
                                    </span>

                                @elseif ($customer->status === 'suspended')

                                    <span class="customer-status status-suspended">
                                        <span class="status-dot"></span>
                                        {{ $customerStatus }}
                                    </span>

                                @else

                                    <span class="customer-status status-inactive">
                                        <span class="status-dot"></span>
                                        {{ $customerStatus }}
                                    </span>

                                @endif

                            </td>


                            {{-- CREATED --}}
                            <td>

                                <div class="created-date">

                                    <strong>
                                        {{
                                            $customer->created_at
                                                ? $customer->created_at->translatedFormat('d M Y')
                                                : '-'
                                        }}
                                    </strong>

                                    <span>
                                        {{
                                            $customer->created_at
                                                ? $customer->created_at->format('H:i')
                                                : '-'
                                        }}
                                    </span>

                                </div>

                            </td>


                            {{-- ACTION --}}
                            <td class="action-column">

                                <a
                                    href="{{ route('admin.customers.show', $customer) }}"
                                    class="detail-button"
                                >
                                    Detail

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M9 6L15 12L9 18"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </a>

                                <form
                                    action="{{ route('admin.customers.destroy', $customer) }}"
                                    method="POST"
                                    style="display:inline-block;margin:6px 0 0 6px"
                                    onsubmit="return confirm('Yakin ingin menghapus customer ini secara permanen?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        style="background:#fff;color:#dc2626;border:1px solid #fecaca;border-radius:8px;padding:9px 13px;font-weight:700;cursor:pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- =================================================
            PAGINATION
        ================================================== --}}

        <div class="customer-pagination">

            <div class="pagination-info">
                Menampilkan
                <strong>
                    {{ $customers->firstItem() ?? 0 }}
                </strong>
                -
                <strong>
                    {{ $customers->lastItem() ?? 0 }}
                </strong>
                dari
                <strong>
                    {{ $customers->total() }}
                </strong>
                customer
            </div>


            @if ($customers->hasPages())

                <div class="pagination-buttons">

                    @if ($customers->onFirstPage())

                        <span class="pagination-button disabled">
                            Sebelumnya
                        </span>

                    @else

                        <a
                            href="{{ $customers->previousPageUrl() }}"
                            class="pagination-button"
                        >
                            Sebelumnya
                        </a>

                    @endif


                    <span class="pagination-current">
                        Halaman {{ $customers->currentPage() }}
                        dari {{ $customers->lastPage() }}
                    </span>


                    @if ($customers->hasMorePages())

                        <a
                            href="{{ $customers->nextPageUrl() }}"
                            class="pagination-button"
                        >
                            Selanjutnya
                        </a>

                    @else

                        <span class="pagination-button disabled">
                            Selanjutnya
                        </span>

                    @endif

                </div>

            @endif

        </div>

    @endif

</section>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .customer-statistics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));

        gap: 15px;

        margin-bottom: 22px;
    }

    .customer-stat-card {
        min-width: 0;

        display: flex;
        align-items: center;

        gap: 14px;

        padding: 20px;

        border: 1px solid #e2e8f0;
        border-radius: 16px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .customer-stat-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 13px;
    }

    .customer-stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .icon-green {
        background: #f0fdf4;
        color: #16a34a;
    }

    .icon-red {
        background: #fef2f2;
        color: #dc2626;
    }

    .icon-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .customer-stat-label {
        color: #64748b;

        font-size: 10px;
        font-weight: 750;
    }

    .customer-stat-value {
        margin-top: 2px;

        color: #082f49;

        font-size: 25px;
        font-weight: 850;

        letter-spacing: -0.5px;
    }

    .customer-stat-description {
        margin-top: 2px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Card
    |--------------------------------------------------------------------------
    */

    .customer-card {
        overflow: hidden;

        border: 1px solid #e2e8f0;
        border-radius: 17px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .customer-card-header {
        padding: 21px 23px;

        border-bottom: 1px solid #eef2f7;
    }

    .customer-card-header h2 {
        color: #082f49;

        font-size: 16px;
        font-weight: 800;
    }

    .customer-card-header p {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 11px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    .customer-filter {
        display: flex;
        align-items: center;

        gap: 10px;

        padding: 17px 23px;

        border-bottom: 1px solid #eef2f7;

        background: #fafcff;
    }

    .search-wrapper {
        position: relative;

        flex: 1;
        min-width: 220px;
    }

    .search-icon {
        position: absolute;

        top: 50%;
        left: 13px;

        width: 18px;
        height: 18px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #94a3b8;

        transform: translateY(-50%);

        pointer-events: none;
    }

    .search-icon svg {
        width: 18px;
        height: 18px;
    }

    .filter-input {
        height: 42px;

        border: 1px solid #dbe3ed;
        border-radius: 10px;

        outline: none;

        background: #ffffff;
        color: #334155;

        font-size: 12px;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .filter-input:focus {
        border-color: #38bdf8;

        box-shadow:
            0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .search-input {
        width: 100%;

        padding: 0 14px 0 41px;
    }

    .status-select {
        width: 170px;

        padding: 0 12px;
    }

    .filter-button,
    .reset-button {
        height: 42px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0 16px;

        border-radius: 10px;

        font-size: 11px;
        font-weight: 750;

        text-decoration: none;
    }

    .filter-button {
        border: 0;

        background: #0284c7;
        color: #ffffff;
    }

    .filter-button:hover {
        background: #0369a1;
    }

    .reset-button {
        border: 1px solid #dbe3ed;

        background: #ffffff;
        color: #64748b;
    }

    .reset-button:hover {
        background: #f8fafc;
        color: #0f172a;
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .customer-table-wrapper {
        width: 100%;

        overflow-x: auto;
    }

    .customer-table {
        width: 100%;

        border-collapse: collapse;
    }

    .customer-table th {
        padding: 12px 18px;

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
        padding: 15px 18px;

        border-bottom: 1px solid #f1f5f9;

        color: #475569;

        font-size: 11px;

        vertical-align: middle;
    }

    .customer-table tbody tr:hover {
        background: #fbfdff;
    }

    .customer-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .action-column {
        text-align: right !important;
    }


    /*
    |--------------------------------------------------------------------------
    | Customer Profile
    |--------------------------------------------------------------------------
    */

    .customer-profile {
        min-width: 190px;

        display: flex;
        align-items: center;

        gap: 11px;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 11px;

        background:
            linear-gradient(
                135deg,
                #e0f2fe,
                #cffafe
            );

        color: #0369a1;

        font-size: 12px;
        font-weight: 850;
    }

    .customer-profile-info {
        min-width: 0;
    }

    .customer-profile-info strong {
        display: block;

        max-width: 190px;

        overflow: hidden;

        color: #0f172a;

        font-size: 11px;
        font-weight: 750;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .customer-profile-info span {
        display: block;

        margin-top: 3px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    .contact-info {
        min-width: 185px;
    }

    .contact-info span {
        display: block;
    }

    .contact-email {
        color: #334155;

        font-size: 10px;
        font-weight: 650;
    }

    .contact-phone {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant
    |--------------------------------------------------------------------------
    */

    .participant-count {
        min-width: 80px;
    }

    .participant-count strong {
        color: #082f49;

        font-size: 14px;
        font-weight: 850;
    }

    .participant-count span {
        margin-left: 3px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .customer-status {
        display: inline-flex;
        align-items: center;

        gap: 6px;

        padding: 5px 9px;

        border-radius: 999px;

        font-size: 9px;
        font-weight: 800;

        white-space: nowrap;
    }

    .status-dot {
        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    .status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-suspended {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }


    /*
    |--------------------------------------------------------------------------
    | Created
    |--------------------------------------------------------------------------
    */

    .created-date {
        min-width: 90px;
    }

    .created-date strong {
        display: block;

        color: #475569;

        font-size: 10px;
        font-weight: 650;
    }

    .created-date span {
        display: block;

        margin-top: 3px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Detail Button
    |--------------------------------------------------------------------------
    */

    .detail-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 4px;

        min-height: 34px;

        padding: 7px 10px;

        border: 1px solid #bae6fd;
        border-radius: 8px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 10px;
        font-weight: 750;

        text-decoration: none;
    }

    .detail-button:hover {
        border-color: #7dd3fc;

        background: #e0f2fe;
    }

    .detail-button svg {
        width: 14px;
        height: 14px;
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    .customer-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 16px 22px;

        border-top: 1px solid #eef2f7;

        background: #fafcff;
    }

    .pagination-info {
        color: #64748b;

        font-size: 10px;
    }

    .pagination-info strong {
        color: #334155;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;

        gap: 8px;
    }

    .pagination-button {
        min-height: 33px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 7px 11px;

        border: 1px solid #dbe3ed;
        border-radius: 8px;

        background: #ffffff;
        color: #475569;

        font-size: 9px;
        font-weight: 700;

        text-decoration: none;
    }

    .pagination-button:hover {
        border-color: #bae6fd;

        background: #f0f9ff;
        color: #0369a1;
    }

    .pagination-button.disabled {
        cursor: not-allowed;

        opacity: 0.45;
    }

    .pagination-current {
        color: #64748b;

        font-size: 9px;
        font-weight: 650;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty
    |--------------------------------------------------------------------------
    */

    .customer-empty {
        min-height: 300px;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        padding: 35px;

        text-align: center;
    }

    .customer-empty-icon {
        width: 54px;
        height: 54px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 13px;

        border-radius: 15px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .customer-empty-icon svg {
        width: 26px;
        height: 26px;
    }

    .customer-empty strong {
        color: #334155;

        font-size: 13px;
        font-weight: 750;
    }

    .customer-empty span {
        margin-top: 5px;

        color: #94a3b8;

        font-size: 10px;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1100px) {

        .customer-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }


    @media (max-width: 750px) {

        .customer-filter {
            align-items: stretch;
            flex-direction: column;
        }

        .search-wrapper,
        .status-select {
            width: 100%;
        }

        .customer-pagination {
            align-items: flex-start;
            flex-direction: column;
        }
    }


    @media (max-width: 520px) {

        .customer-statistics {
            grid-template-columns: 1fr;
        }

        .customer-stat-card {
            padding: 17px;
        }

        .pagination-buttons {
            width: 100%;

            justify-content: space-between;
        }

        .pagination-current {
            text-align: center;
        }
    }

</style>

@endpush
