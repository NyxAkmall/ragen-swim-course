@extends('layouts.admin')

@section('title', 'Booking')
@section('topbar-title', 'Manajemen Booking')

@push('styles')
<style>
    .booking-stats {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .stat-card {
        padding: 18px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .stat-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        line-height: 1;
    }

    .filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 190px 190px auto;
        gap: 12px;
        align-items: end;
    }

    .form-group {
        min-width: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--navy);
        font-size: 12px;
        font-weight: 750;
    }

    .form-control {
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
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .10);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .booking-card {
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .booking-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .booking-table th {
        padding: 13px 15px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .6px;
        white-space: nowrap;
    }

    .booking-table td {
        padding: 15px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        font-size: 13px;
    }

    .booking-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .booking-table tbody tr:hover {
        background: #fbfdff;
    }

    .booking-code {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .main-text {
        color: var(--text);
        font-weight: 750;
    }

    .sub-text {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.45;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 27px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
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

    .session-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .session-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    .session-badge.deducted {
        color: var(--success);
    }

    .session-badge.deducted .session-dot {
        background: #22c55e;
    }

    .detail-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 7px 11px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--white);
        color: var(--blue-dark);
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .detail-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .empty-state {
        padding: 55px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border-radius: 15px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .empty-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 430px;
        margin: 7px auto 0;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    .pagination-wrapper {
        padding: 16px 18px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 1250px) {
        .booking-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .booking-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

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
</style>
@endpush


@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Booking
        </h1>

        <p class="page-description">
            Pantau seluruh booking peserta, jadwal kelas, paket yang digunakan,
            status booking, serta penggunaan sesi dalam satu halaman.
        </p>
    </div>

</div>


{{-- =========================================================
    STATISTIK
========================================================== --}}

<div class="booking-stats">

    <div class="card stat-card">
        <div class="stat-label">
            Total Booking
        </div>

        <div class="stat-value">
            {{ $statistics['total'] }}
        </div>
    </div>


    <div class="card stat-card">
        <div class="stat-label">
            Pending
        </div>

        <div class="stat-value">
            {{ $statistics['pending'] }}
        </div>
    </div>


    <div class="card stat-card">
        <div class="stat-label">
            Dikonfirmasi
        </div>

        <div class="stat-value">
            {{ $statistics['confirmed'] }}
        </div>
    </div>


    <div class="card stat-card">
        <div class="stat-label">
            Selesai
        </div>

        <div class="stat-value">
            {{ $statistics['completed'] }}
        </div>
    </div>


    <div class="card stat-card">
        <div class="stat-label">
            Dibatalkan
        </div>

        <div class="stat-value">
            {{ $statistics['cancelled'] }}
        </div>
    </div>


    <div class="card stat-card">
        <div class="stat-label">
            Reschedule
        </div>

        <div class="stat-value">
            {{ $statistics['rescheduled'] }}
        </div>
    </div>

</div>


{{-- =========================================================
    FILTER
========================================================== --}}

<div class="card filter-card">

    <form
        method="GET"
        action="{{ route('admin.bookings.index') }}"
        class="filter-form"
    >

        <div class="form-group">

            <label
                for="search"
                class="form-label"
            >
                Cari Booking
            </label>

            <input
                type="text"
                id="search"
                name="search"
                class="form-control"
                value="{{ $search }}"
                placeholder="Kode, peserta, customer, program, coach, lokasi..."
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
                    value="pending"
                    @selected($status === 'pending')
                >
                    Pending
                </option>

                <option
                    value="confirmed"
                    @selected($status === 'confirmed')
                >
                    Dikonfirmasi
                </option>

                <option
                    value="completed"
                    @selected($status === 'completed')
                >
                    Selesai
                </option>

                <option
                    value="cancelled"
                    @selected($status === 'cancelled')
                >
                    Dibatalkan
                </option>

                <option
                    value="rescheduled"
                    @selected($status === 'rescheduled')
                >
                    Reschedule
                </option>
            </select>

        </div>


        <div class="form-group">

            <label
                for="date"
                class="form-label"
            >
                Tanggal Kelas
            </label>

            <input
                type="date"
                id="date"
                name="date"
                class="form-control"
                value="{{ $date }}"
            >

        </div>


        <div class="filter-actions">

            <button
                type="submit"
                class="btn-primary"
            >
                Terapkan
            </button>

            <a
                href="{{ route('admin.bookings.index') }}"
                class="btn-secondary"
            >
                Reset
            </a>

        </div>

    </form>

</div>


{{-- =========================================================
    DAFTAR BOOKING
========================================================== --}}

<div class="card booking-card">

    @if ($bookings->count() > 0)

        <div class="table-wrapper">

            <table class="booking-table">

                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Peserta</th>
                        <th>Program & Paket</th>
                        <th>Jadwal</th>
                        <th>Coach & Lokasi</th>
                        <th>Status</th>
                        <th>Sesi</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($bookings as $booking)

                        @php
                            $schedule = $booking->classSchedule;
                            $participant = $booking->participant;
                            $participantPackage = $booking->participantPackage;

                            $statusLabel = match ($booking->status) {
                                'pending' => 'Pending',
                                'confirmed' => 'Dikonfirmasi',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'rescheduled' => 'Reschedule',
                                default => ucfirst($booking->status),
                            };
                        @endphp

                        <tr>

                            {{-- BOOKING --}}
                            <td>
                                <div class="booking-code">
                                    {{ $booking->booking_code }}
                                </div>

                                <div class="sub-text">
                                    Dibuat
                                    {{ $booking->booked_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </td>


                            {{-- PESERTA --}}
                            <td>
                                <div class="main-text">
                                    {{ $participant?->full_name ?? '-' }}
                                </div>

                                <div class="sub-text">
                                    Customer:
                                    {{ $participant?->user?->name ?? '-' }}
                                </div>
                            </td>


                            {{-- PROGRAM & PAKET --}}
                            <td>
                                <div class="main-text">
                                    {{
                                        $participantPackage?->package?->program?->name
                                        ?? $schedule?->program?->name
                                        ?? '-'
                                    }}
                                </div>

                                <div class="sub-text">
                                    Paket:
                                    {{ $participantPackage?->package?->name ?? '-' }}
                                </div>
                            </td>


                            {{-- JADWAL --}}
                            <td>
                                <div class="main-text">
                                    {{ $schedule?->title ?? '-' }}
                                </div>

                                <div class="sub-text">
                                    @if ($schedule?->class_date)
                                        {{ $schedule->class_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif

                                    @if ($schedule?->start_time)
                                        •
                                        {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}

                                        @if ($schedule?->end_time)
                                            -
                                            {{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}
                                        @endif
                                    @endif
                                </div>
                            </td>


                            {{-- COACH & LOKASI --}}
                            <td>
                                <div class="main-text">
                                    {{ $schedule?->coach?->user?->name ?? '-' }}
                                </div>

                                <div class="sub-text">
                                    {{ $schedule?->location?->name ?? '-' }}
                                </div>
                            </td>


                            {{-- STATUS --}}
                            <td>
                                <span
                                    class="status-badge status-{{ $booking->status }}"
                                >
                                    {{ $statusLabel }}
                                </span>
                            </td>


                            {{-- SESI --}}
                            <td>

                                <span
                                    class="session-badge {{ $booking->session_deducted ? 'deducted' : '' }}"
                                >
                                    <span class="session-dot"></span>

                                    @if ($booking->session_deducted)
                                        Sudah dipotong
                                    @else
                                        Belum dipotong
                                    @endif
                                </span>

                            </td>


                            {{-- DETAIL --}}
                            <td>
                                <a
                                    href="{{ route('admin.bookings.show', $booking) }}"
                                    class="detail-link"
                                >
                                    Detail
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        @if ($bookings->hasPages())

            <div class="pagination-wrapper">
                {{ $bookings->links() }}
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
                        d="M5 5H19V19H5V5Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M8 12L10.5 14.5L16 9"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            </div>

            <div class="empty-title">
                Booking tidak ditemukan
            </div>

            <div class="empty-description">
                Belum ada booking yang sesuai dengan pencarian atau filter
                yang sedang digunakan.
            </div>

        </div>

    @endif

</div>

@endsection
