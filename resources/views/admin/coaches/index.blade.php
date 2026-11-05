@extends('layouts.admin')

@section('title', 'Coach')
@section('topbar-title', 'Coach')

@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Manajemen Coach
        </h1>

        <p class="page-description">
            Kelola data pelatih Ragen Swim Course, lihat aktivitas mengajar,
            status akun, serta informasi coach yang terdaftar di sistem.
        </p>
    </div>

    <a
        href="{{ route('admin.coaches.create') }}"
        class="btn-primary add-coach-button"
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

        Tambah Coach
    </a>

</div>


{{-- =========================================================
    STATISTICS
========================================================== --}}

<div class="coach-statistics">

    <div class="stat-card">

        <div class="stat-icon stat-icon-blue">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <circle
                    cx="12"
                    cy="7"
                    r="3.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                />

                <path
                    d="M5 20C5.5 15.8 8 14 12 14C16 14 18.5 15.8 19 20"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <div class="stat-label">
                Total Coach
            </div>

            <div class="stat-value">
                {{ number_format($statistics['total']) }}
            </div>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon-green">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M5 12L10 17L19 8"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

        </div>

        <div>
            <div class="stat-label">
                Coach Aktif
            </div>

            <div class="stat-value">
                {{ number_format($statistics['active']) }}
            </div>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon-red">

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
                    d="M8.5 8.5L15.5 15.5M15.5 8.5L8.5 15.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <div class="stat-label">
                Tidak Aktif
            </div>

            <div class="stat-value">
                {{ number_format($statistics['inactive']) }}
            </div>
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-icon stat-icon-aqua">

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
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <div class="stat-label">
                Total Jadwal
            </div>

            <div class="stat-value">
                {{ number_format($statistics['total_schedules']) }}
            </div>
        </div>

    </div>

</div>


{{-- =========================================================
    FILTER
========================================================== --}}

<div class="card filter-card">

    <form
        method="GET"
        action="{{ route('admin.coaches.index') }}"
        class="filter-form"
    >

        <div class="search-field">

            <label for="search">
                Cari Coach
            </label>

            <div class="search-input-wrapper">

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
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Nama, email, nomor telepon, atau spesialisasi..."
                >

            </div>

        </div>


        <div class="status-field">

            <label for="status">
                Status
            </label>

            <select
                id="status"
                name="status"
            >
                <option value="">
                    Semua Status
                </option>

                <option
                    value="active"
                    @selected($status === 'active')
                >
                    Aktif
                </option>

                <option
                    value="inactive"
                    @selected($status === 'inactive')
                >
                    Tidak Aktif
                </option>
            </select>

        </div>


        <div class="filter-actions">

            <button
                type="submit"
                class="btn-primary filter-button"
            >
                Terapkan
            </button>

            @if ($search !== '' || $status !== '')

                <a
                    href="{{ route('admin.coaches.index') }}"
                    class="btn-secondary reset-button"
                >
                    Reset
                </a>

            @endif

        </div>

    </form>

</div>


{{-- =========================================================
    COACH TABLE
========================================================== --}}

<div class="card coach-table-card">

    <div class="table-heading">

        <div>

            <h2>
                Daftar Coach
            </h2>

            <p>
                Menampilkan {{ $coaches->count() }}
                dari {{ $coaches->total() }} coach.
            </p>

        </div>

    </div>


    @if ($coaches->isNotEmpty())

        <div class="table-responsive">

            <table class="coach-table">

                <thead>
                    <tr>
                        <th>Coach</th>
                        <th>Kontak</th>
                        <th>Spesialisasi</th>
                        <th>Pengalaman</th>
                        <th>Aktivitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($coaches as $coach)

                        @php
                            $coachName =
                                $coach->user?->name
                                ?? 'Coach';

                            $coachInitials = collect(
                                preg_split(
                                    '/\s+/',
                                    trim($coachName)
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
                        @endphp

                        <tr>

                            {{-- COACH --}}
                            <td>

                                <div class="coach-profile">

                                    <div class="coach-avatar">

                                        @if ($coach->photo)

                                            <img
                                                src="{{ asset('storage/' . $coach->photo) }}"
                                                alt="{{ $coachName }}"
                                            >

                                        @else

                                            {{ $coachInitials ?: 'C' }}

                                        @endif

                                    </div>


                                    <div class="coach-profile-info">

                                        <div class="coach-name">
                                            {{ $coachName }}
                                        </div>

                                        <div class="coach-id">
                                            Coach #{{ $coach->id }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- KONTAK --}}
                            <td>

                                <div class="contact-info">

                                    <div class="contact-main">
                                        {{ $coach->user?->email ?? '-' }}
                                    </div>

                                    <div class="contact-secondary">
                                        {{ $coach->user?->phone ?? '-' }}
                                    </div>

                                </div>

                            </td>


                            {{-- SPESIALISASI --}}
                            <td>

                                @if ($coach->specialization)

                                    <span class="specialization-badge">
                                        {{ $coach->specialization }}
                                    </span>

                                @else

                                    <span class="empty-value">
                                        Belum diisi
                                    </span>

                                @endif

                            </td>


                            {{-- PENGALAMAN --}}
                            <td>

                                <div class="experience-value">
                                    {{ $coach->experience_years ?? 0 }}
                                    tahun
                                </div>

                            </td>


                            {{-- AKTIVITAS --}}
                            <td>

                                <div class="activity-info">

                                    <div>
                                        <strong>
                                            {{ number_format($coach->schedules_count) }}
                                        </strong>
                                        jadwal
                                    </div>

                                    <div>
                                        <strong>
                                            {{ number_format($coach->progress_records_count) }}
                                        </strong>
                                        perkembangan
                                    </div>

                                </div>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if ($coach->status === 'active')

                                    <span class="status-badge status-active">
                                        <span class="status-dot"></span>
                                        Aktif
                                    </span>

                                @else

                                    <span class="status-badge status-inactive">
                                        <span class="status-dot"></span>
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td>

                                <a
                                    href="{{ route('admin.coaches.show', $coach) }}"
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
                                    action="{{ route('admin.coaches.destroy', $coach) }}"
                                    method="POST"
                                    style="display:inline-block;margin:6px 0 0 6px"
                                    onsubmit="return confirm('Yakin ingin menghapus coach ini secara permanen? Akun coach juga akan dihapus.')"
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


        {{-- =====================================================
            PAGINATION
        ====================================================== --}}

        @if ($coaches->hasPages())

            <div class="pagination-wrapper">

                <div class="pagination-info">
                    Halaman
                    <strong>{{ $coaches->currentPage() }}</strong>
                    dari
                    <strong>{{ $coaches->lastPage() }}</strong>
                </div>


                <div class="pagination-buttons">

                    @if ($coaches->onFirstPage())

                        <span class="pagination-button disabled">
                            Sebelumnya
                        </span>

                    @else

                        <a
                            href="{{ $coaches->previousPageUrl() }}"
                            class="pagination-button"
                        >
                            Sebelumnya
                        </a>

                    @endif


                    <span class="pagination-current">
                        {{ $coaches->currentPage() }}
                    </span>


                    @if ($coaches->hasMorePages())

                        <a
                            href="{{ $coaches->nextPageUrl() }}"
                            class="pagination-button"
                        >
                            Berikutnya
                        </a>

                    @else

                        <span class="pagination-button disabled">
                            Berikutnya
                        </span>

                    @endif

                </div>

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
                    <circle
                        cx="12"
                        cy="7"
                        r="3.5"
                        stroke="currentColor"
                        stroke-width="1.7"
                    />

                    <path
                        d="M5 20C5.5 15.8 8 14 12 14C16 14 18.5 15.8 19 20"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <h3>
                Coach tidak ditemukan
            </h3>

            <p>
                Belum ada coach yang sesuai dengan pencarian
                atau filter yang digunakan.
            </p>

            @if ($search !== '' || $status !== '')

                <a
                    href="{{ route('admin.coaches.index') }}"
                    class="btn-secondary"
                >
                    Hapus Filter
                </a>

            @else

                <a
                    href="{{ route('admin.coaches.create') }}"
                    class="btn-primary"
                >
                    Tambah Coach Pertama
                </a>

            @endif

        </div>

    @endif

</div>

@endsection


@push('styles')

<style>
    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .add-coach-button {
        flex-shrink: 0;
    }

    .add-coach-button svg {
        width: 18px;
        height: 18px;
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .coach-statistics {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));

        gap: 16px;

        margin-bottom: 22px;
    }

    .stat-card {
        min-height: 112px;

        display: flex;
        align-items: center;

        gap: 14px;

        padding: 20px;

        border: 1px solid var(--border);
        border-radius: 16px;

        background: var(--white);

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .stat-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 13px;
    }

    .stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .stat-icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .stat-icon-green {
        background: #f0fdf4;
        color: #15803d;
    }

    .stat-icon-red {
        background: #fef2f2;
        color: #dc2626;
    }

    .stat-icon-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-label {
        color: var(--text-soft);

        font-size: 12px;
        font-weight: 650;
    }

    .stat-value {
        margin-top: 4px;

        color: var(--navy);

        font-size: 25px;
        font-weight: 850;

        letter-spacing: -0.5px;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    .filter-card {
        padding: 20px;

        margin-bottom: 22px;
    }

    .filter-form {
        display: grid;
        grid-template-columns:
            minmax(260px, 1fr)
            190px
            auto;

        align-items: end;

        gap: 14px;
    }

    .filter-form label {
        display: block;

        margin-bottom: 7px;

        color: var(--text);

        font-size: 12px;
        font-weight: 750;
    }

    .search-input-wrapper {
        position: relative;
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

        transform: translateY(-50%);

        color: var(--text-light);
    }

    .search-icon svg {
        width: 18px;
        height: 18px;
    }

    .search-input-wrapper input,
    .status-field select {
        width: 100%;
        height: 44px;

        border: 1px solid var(--border);
        border-radius: 10px;

        background: var(--white);
        color: var(--text);

        outline: none;

        font-size: 13px;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .search-input-wrapper input {
        padding: 0 13px 0 42px;
    }

    .status-field select {
        padding: 0 12px;
    }

    .search-input-wrapper input:focus,
    .status-field select:focus {
        border-color: #38bdf8;

        box-shadow:
            0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .filter-actions {
        display: flex;
        align-items: center;

        gap: 9px;
    }

    .filter-button,
    .reset-button {
        height: 44px;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .coach-table-card {
        overflow: hidden;
    }

    .table-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding: 21px 22px;

        border-bottom: 1px solid var(--border);
    }

    .table-heading h2 {
        color: var(--navy);

        font-size: 16px;
        font-weight: 800;
    }

    .table-heading p {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 12px;
    }

    .table-responsive {
        width: 100%;

        overflow-x: auto;
    }

    .coach-table {
        width: 100%;
        min-width: 1080px;

        border-collapse: collapse;
    }

    .coach-table th {
        padding: 13px 17px;

        border-bottom: 1px solid var(--border);

        background: #f8fafc;
        color: var(--text-soft);

        text-align: left;

        font-size: 10px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.65px;
    }

    .coach-table td {
        padding: 16px 17px;

        border-bottom: 1px solid #f1f5f9;

        vertical-align: middle;

        font-size: 13px;
    }

    .coach-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .coach-table tbody tr {
        transition: background 0.15s ease;
    }

    .coach-table tbody tr:hover {
        background: #fbfdff;
    }


    /*
    |--------------------------------------------------------------------------
    | Coach Profile
    |--------------------------------------------------------------------------
    */

    .coach-profile {
        display: flex;
        align-items: center;

        gap: 11px;

        min-width: 185px;
    }

    .coach-avatar {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        overflow: hidden;

        border-radius: 12px;

        background:
            linear-gradient(
                135deg,
                #0284c7,
                #06b6d4
            );

        color: var(--white);

        font-size: 13px;
        font-weight: 800;
    }

    .coach-avatar img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }

    .coach-profile-info {
        min-width: 0;
    }

    .coach-name {
        max-width: 180px;

        overflow: hidden;

        color: var(--text);

        font-weight: 750;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .coach-id {
        margin-top: 3px;

        color: var(--text-light);

        font-size: 10px;
        font-weight: 650;
    }


    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    .contact-info {
        min-width: 175px;
    }

    .contact-main {
        color: var(--text);

        font-size: 12px;
        font-weight: 650;
    }

    .contact-secondary {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 11px;
    }


    /*
    |--------------------------------------------------------------------------
    | Specialization
    |--------------------------------------------------------------------------
    */

    .specialization-badge {
        display: inline-flex;

        max-width: 180px;

        padding: 6px 9px;

        border-radius: 8px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 11px;
        font-weight: 700;

        line-height: 1.35;
    }

    .empty-value {
        color: var(--text-light);

        font-size: 11px;
        font-style: italic;
    }


    /*
    |--------------------------------------------------------------------------
    | Experience
    |--------------------------------------------------------------------------
    */

    .experience-value {
        color: var(--text);

        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Activity
    |--------------------------------------------------------------------------
    */

    .activity-info {
        min-width: 120px;

        color: var(--text-soft);

        font-size: 11px;
        line-height: 1.7;
    }

    .activity-info strong {
        color: var(--navy);
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

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }


    /*
    |--------------------------------------------------------------------------
    | Detail Button
    |--------------------------------------------------------------------------
    */

    .detail-button {
        display: inline-flex;
        align-items: center;

        gap: 4px;

        padding: 7px 10px;

        border: 1px solid #bae6fd;
        border-radius: 8px;

        background: #f0f9ff;
        color: #0369a1;

        text-decoration: none;

        font-size: 11px;
        font-weight: 750;

        transition:
            background 0.2s ease,
            border-color 0.2s ease;
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

    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding: 17px 22px;

        border-top: 1px solid var(--border);
    }

    .pagination-info {
        color: var(--text-soft);

        font-size: 11px;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;

        gap: 7px;
    }

    .pagination-button,
    .pagination-current {
        min-height: 34px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 7px 11px;

        border: 1px solid var(--border);
        border-radius: 8px;

        background: var(--white);

        text-decoration: none;

        font-size: 11px;
        font-weight: 700;
    }

    .pagination-button {
        color: var(--navy);
    }

    .pagination-button:hover {
        background: #f8fafc;
    }

    .pagination-button.disabled {
        cursor: not-allowed;

        color: var(--text-light);

        background: #f8fafc;
    }

    .pagination-current {
        min-width: 34px;

        border-color: var(--blue);

        background: var(--blue);
        color: var(--white);
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 65px 20px;

        text-align: center;
    }

    .empty-icon {
        width: 62px;
        height: 62px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 15px;

        border-radius: 18px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .empty-icon svg {
        width: 30px;
        height: 30px;
    }

    .empty-state h3 {
        color: var(--navy);

        font-size: 16px;
        font-weight: 800;
    }

    .empty-state p {
        max-width: 430px;

        margin: 7px auto 18px;

        color: var(--text-soft);

        font-size: 12px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1100px) {

        .coach-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .filter-form {
            grid-template-columns:
                minmax(240px, 1fr)
                180px;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }


    @media (max-width: 700px) {

        .page-header {
            display: flex;
            flex-direction: column;
        }

        .add-coach-button {
            width: 100%;
        }

        .coach-statistics {
            grid-template-columns: 1fr;
        }

        .stat-card {
            min-height: 95px;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            grid-column: auto;
        }

        .filter-button,
        .reset-button {
            flex: 1;
        }

        .pagination-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }

        .pagination-buttons {
            width: 100%;
        }

        .pagination-button {
            flex: 1;
        }
    }
</style>

@endpush
