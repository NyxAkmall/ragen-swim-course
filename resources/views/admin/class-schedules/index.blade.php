@extends('layouts.admin')

@section('title', 'Jadwal Kelas')
@section('topbar-title', 'Jadwal Kelas')

@push('styles')
<style>
    .schedule-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .schedule-page-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .schedule-page-description {
        max-width: 760px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .schedule-add-button svg {
        width: 18px;
        height: 18px;
    }

    .schedule-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .schedule-stat-card {
        padding: 20px;
    }

    .schedule-stat-label {
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .schedule-stat-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    .schedule-stat-note {
        margin-top: 4px;
        color: var(--text-light);
        font-size: 11px;
    }

    .schedule-filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .schedule-filter-form {
        display: grid;
        grid-template-columns:
            minmax(240px, 1.6fr)
            minmax(150px, 0.7fr)
            minmax(160px, 0.7fr)
            auto;
        gap: 12px;
        align-items: end;
    }

    .schedule-form-group {
        min-width: 0;
    }

    .schedule-form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .schedule-input,
    .schedule-select {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
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

    .schedule-input:focus,
    .schedule-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .schedule-filter-actions {
        display: flex;
        gap: 8px;
    }

    .schedule-filter-actions .btn-primary,
    .schedule-filter-actions .btn-secondary {
        white-space: nowrap;
    }

    .schedule-table-card {
        overflow: hidden;
    }

    .schedule-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .schedule-table-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .schedule-table-count {
        color: var(--text-soft);
        font-size: 12px;
    }

    .schedule-table-wrapper {
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .schedule-table th {
        padding: 12px 15px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.55px;
        white-space: nowrap;
    }

    .schedule-table td {
        padding: 15px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        color: var(--text);
        font-size: 13px;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .schedule-table tbody tr:hover {
        background: #fbfdff;
    }

    .schedule-name {
        max-width: 230px;
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .schedule-program {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .schedule-date {
        font-weight: 750;
        white-space: nowrap;
    }

    .schedule-time {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        white-space: nowrap;
    }

    .schedule-person {
        font-weight: 700;
    }

    .schedule-secondary {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .schedule-capacity {
        min-width: 100px;
    }

    .schedule-capacity-number {
        font-weight: 800;
    }

    .schedule-capacity-text {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .schedule-level {
        display: inline-flex;
        align-items: center;
        min-height: 27px;
        padding: 5px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .schedule-status {
        display: inline-flex;
        align-items: center;
        min-height: 27px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
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

    .schedule-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .schedule-action-link {
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
    }

    .schedule-action-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: var(--blue-dark);
    }

    .schedule-action-edit {
        color: var(--blue-dark);
    }

    .schedule-empty {
        padding: 55px 25px;
        text-align: center;
    }

    .schedule-empty-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border-radius: 16px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .schedule-empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .schedule-empty-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .schedule-empty-description {
        max-width: 430px;
        margin: 6px auto 0;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    .schedule-empty-action {
        margin-top: 17px;
    }

    .schedule-pagination {
        padding: 18px 20px;
        border-top: 1px solid var(--border);
    }

    .schedule-pagination nav {max-width:100%;}
    .schedule-pagination nav > div:first-child {font-size:13px;}
    .schedule-pagination nav > div:last-child {display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;}
    .schedule-pagination nav a, .schedule-pagination nav span {font-size:13px;}
    .schedule-pagination nav svg {width:16px !important;height:16px !important;max-width:16px;max-height:16px;display:inline-block;vertical-align:middle;}
    .schedule-pagination nav a {text-decoration:none;}
    .schedule-pagination nav a:hover {color:#0284c7;}
    .schedule-pagination nav [aria-current="page"] span {font-weight:800;}
    @media(max-width:700px){.schedule-pagination{padding:14px 12px;}.schedule-pagination nav > div:last-child{justify-content:center;}}


    @media (max-width: 1100px) {
        .schedule-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .schedule-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .schedule-page-header {
            display: block;
        }

        .schedule-page-title {
            font-size: 24px;
        }

        .schedule-add-button {
            width: 100%;
            margin-top: 17px;
        }

        .schedule-stat-grid {
            grid-template-columns: 1fr 1fr;
            gap: 11px;
        }

        .schedule-stat-card {
            padding: 16px;
        }

        .schedule-stat-value {
            font-size: 23px;
        }

        .schedule-filter-form {
            grid-template-columns: 1fr;
        }

        .schedule-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .schedule-table-header {
            display: block;
        }

        .schedule-table-count {
            margin-top: 5px;
        }
    }

    @media (max-width: 430px) {
        .schedule-stat-grid {
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
@endphp


<div class="schedule-page-header">

    <div>
        <h1 class="schedule-page-title">
            Jadwal Kelas
        </h1>

        <p class="schedule-page-description">
            Kelola jadwal kelas Ragen Swim Course, termasuk program,
            coach, lokasi, waktu pelaksanaan, kapasitas kelas, dan
            status jadwal.
        </p>
    </div>


    <a
        href="{{ route('admin.class-schedules.create') }}"
        class="btn-primary schedule-add-button"
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

        Tambah Jadwal
    </a>

</div>


{{-- =========================================================
    STATISTICS
========================================================== --}}

<div class="schedule-stat-grid">

    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Total Jadwal
        </div>

        <div class="schedule-stat-value">
            {{ number_format($statistics['total']) }}
        </div>

        <div class="schedule-stat-note">
            Seluruh jadwal kelas
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Terjadwal
        </div>

        <div class="schedule-stat-value">
            {{ number_format($statistics['scheduled']) }}
        </div>

        <div class="schedule-stat-note">
            Jadwal yang masih aktif
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Selesai
        </div>

        <div class="schedule-stat-value">
            {{ number_format($statistics['completed']) }}
        </div>

        <div class="schedule-stat-note">
            Kelas yang telah selesai
        </div>
    </div>


    <div class="card schedule-stat-card">
        <div class="schedule-stat-label">
            Dibatalkan
        </div>

        <div class="schedule-stat-value">
            {{ number_format($statistics['cancelled']) }}
        </div>

        <div class="schedule-stat-note">
            Jadwal yang dibatalkan
        </div>
    </div>

</div>


{{-- =========================================================
    FILTER
========================================================== --}}

<div class="card schedule-filter-card">

    <form
        method="GET"
        action="{{ route('admin.class-schedules.index') }}"
        class="schedule-filter-form"
    >

        <div class="schedule-form-group">

            <label
                for="search"
                class="schedule-form-label"
            >
                Cari Jadwal
            </label>

            <input
                type="text"
                name="search"
                id="search"
                class="schedule-input"
                value="{{ $search }}"
                placeholder="Judul, program, coach, atau lokasi..."
            >

        </div>


        <div class="schedule-form-group">

            <label
                for="status"
                class="schedule-form-label"
            >
                Status
            </label>

            <select
                name="status"
                id="status"
                class="schedule-select"
            >
                <option value="">
                    Semua Status
                </option>

                <option
                    value="scheduled"
                    {{ $status === 'scheduled' ? 'selected' : '' }}
                >
                    Terjadwal
                </option>

                <option
                    value="completed"
                    {{ $status === 'completed' ? 'selected' : '' }}
                >
                    Selesai
                </option>

                <option
                    value="cancelled"
                    {{ $status === 'cancelled' ? 'selected' : '' }}
                >
                    Dibatalkan
                </option>
            </select>

        </div>


        <div class="schedule-form-group">

            <label
                for="date"
                class="schedule-form-label"
            >
                Tanggal
            </label>

            <input
                type="date"
                name="date"
                id="date"
                class="schedule-input"
                value="{{ $date }}"
            >

        </div>


        <div class="schedule-filter-actions">

            <button
                type="submit"
                class="btn-primary"
            >
                Filter
            </button>

            <a
                href="{{ route('admin.class-schedules.index') }}"
                class="btn-secondary"
            >
                Reset
            </a>

        </div>

    </form>

</div>


{{-- =========================================================
    TABLE
========================================================== --}}

<div class="card schedule-table-card">

    <div class="schedule-table-header">

        <div class="schedule-table-title">
            Daftar Jadwal
        </div>

        <div class="schedule-table-count">
            Menampilkan {{ $schedules->count() }}
            dari {{ $schedules->total() }} jadwal
        </div>

    </div>


    @if ($schedules->count() > 0)

        <div class="schedule-table-wrapper">

            <table class="schedule-table">

                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tanggal & Waktu</th>
                        <th>Coach</th>
                        <th>Lokasi</th>
                        <th>Level</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @foreach ($schedules as $schedule)

                    @php
                        $activeBookings = (int) $schedule->active_bookings_count;

                        $remainingSlots = max(
                            0,
                            (int) $schedule->capacity
                            - $activeBookings
                            - (int) ($schedule->active_offers_count ?? 0)
                        );
                    @endphp

                    <tr>

                        {{-- KELAS --}}
                        <td>

                            <div class="schedule-name">
                                {{ $schedule->title }}
                            </div>

                            <div class="schedule-program">
                                {{ $schedule->program?->name ?? '-' }}
                            </div>

                        </td>


                        {{-- TANGGAL --}}
                        <td>

                            <div class="schedule-date">
                                {{ $schedule->class_date?->translatedFormat('d M Y') ?? '-' }}
                            </div>

                            <div class="schedule-time">
                                {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                -
                                {{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}
                                WIB
                            </div>

                        </td>


                        {{-- COACH --}}
                        <td>

                            <div class="schedule-person">
                                {{ $schedule->coach?->user?->name ?? '-' }}
                            </div>

                            @if ($schedule->coach?->specialization)

                                <div class="schedule-secondary">
                                    {{ $schedule->coach->specialization }}
                                </div>

                            @endif

                        </td>


                        {{-- LOCATION --}}
                        <td>

                            <div class="schedule-person">
                                {{ $schedule->location?->name ?? '-' }}
                            </div>

                            @if ($schedule->location?->city)

                                <div class="schedule-secondary">
                                    {{ $schedule->location->city }}
                                </div>

                            @endif

                        </td>


                        {{-- LEVEL --}}
                        <td>

                            <span class="schedule-level">
                                {{ $levelLabels[$schedule->swimming_level] ?? ucfirst($schedule->swimming_level) }}
                            </span>

                        </td>


                        {{-- CAPACITY --}}
                        <td>

                            <div class="schedule-capacity">

                                <div class="schedule-capacity-number">
                                    {{ $activeBookings }}
                                    /
                                    {{ $schedule->capacity }}
                                </div>

                                <div class="schedule-capacity-text">

                                    @if ($remainingSlots > 0)

                                        {{ $remainingSlots }} slot tersisa

                                    @else

                                        Kelas penuh

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span
                                class="schedule-status schedule-status-{{ $schedule->status }}"
                            >
                                {{ $statusLabels[$schedule->status] ?? ucfirst($schedule->status) }}
                            </span>

                        </td>


                        {{-- ACTION --}}
                        <td>

                            <div class="schedule-actions">

                                <a
                                    href="{{ route('admin.class-schedules.show', $schedule) }}"
                                    class="schedule-action-link"
                                >
                                    Detail
                                </a>


                                @if ($schedule->status === 'scheduled')

                                    <a
                                        href="{{ route('admin.class-schedules.edit', $schedule) }}"
                                        class="schedule-action-link schedule-action-edit"
                                    >
                                        Edit
                                    </a>

                                @endif

                                <form
                                    action="{{ route('admin.class-schedules.destroy', $schedule) }}"
                                    method="POST"
                                    style="display:inline;margin:0"
                                    onsubmit="return confirm('Yakin ingin menghapus jadwal ini? Penghapusan tidak dapat dibatalkan.')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="schedule-action-link"
                                        style="color:#dc2626;border-color:#fecaca;cursor:pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        @if ($schedules->hasPages())

            <div class="schedule-pagination">
                {{ $schedules->links() }}
            </div>

        @endif

    @else

        <div class="schedule-empty">

            <div class="schedule-empty-icon">

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

            <div class="schedule-empty-title">
                Jadwal tidak ditemukan
            </div>

            <div class="schedule-empty-description">

                @if ($search || $status || $date)

                    Tidak ada jadwal yang sesuai dengan filter yang
                    sedang digunakan. Coba ubah atau reset filter.

                @else

                    Belum ada jadwal kelas yang tersedia.
                    Tambahkan jadwal pertama untuk mulai mengatur
                    kegiatan kelas Ragen Swim Course.

                @endif

            </div>


            <div class="schedule-empty-action">

                @if ($search || $status || $date)

                    <a
                        href="{{ route('admin.class-schedules.index') }}"
                        class="btn-secondary"
                    >
                        Reset Filter
                    </a>

                @else

                    <a
                        href="{{ route('admin.class-schedules.create') }}"
                        class="btn-primary"
                    >
                        Tambah Jadwal
                    </a>

                @endif

            </div>

        </div>

    @endif

</div>

@endsection
