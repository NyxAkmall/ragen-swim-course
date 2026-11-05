@extends('layouts.admin')

@section('title', 'Waiting List')
@section('topbar-title', 'Manajemen Waiting List')

@push('styles')
<style>
    .waiting-stats {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .waiting-stat-card {
        padding: 18px;
    }

    .waiting-stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .waiting-stat-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        line-height: 1;
    }

    .waiting-filter-card {
        padding: 18px;
        margin-bottom: 22px;
    }

    .waiting-filter-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 190px 190px auto;
        gap: 12px;
        align-items: end;
    }

    .waiting-form-group {
        min-width: 0;
    }

    .waiting-form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--navy);
        font-size: 12px;
        font-weight: 750;
    }

    .waiting-form-control {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 13px;
    }

    .waiting-form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .10);
    }

    .waiting-filter-actions {
        display: flex;
        gap: 8px;
    }

    .waiting-info {
        margin-bottom: 22px;
        padding: 15px 18px;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        background: #f0f9ff;
        color: #075985;
        font-size: 12px;
        line-height: 1.7;
    }

    .waiting-alert {
        margin-bottom: 18px;
        padding: 13px 16px;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.6;
    }

    .waiting-alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .waiting-alert-info {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .waiting-alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .waiting-table-card {
        overflow: hidden;
    }

    .waiting-table-wrapper {
        overflow-x: auto;
    }

    .waiting-table {
        width: 100%;
        min-width: 1160px;
        border-collapse: collapse;
    }

    .waiting-table th {
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

    .waiting-table td {
        padding: 15px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        font-size: 13px;
    }

    .waiting-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .waiting-table tbody tr:hover {
        background: #fbfdff;
    }

    .waiting-main-text {
        color: var(--text);
        font-weight: 750;
    }

    .waiting-sub-text {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .waiting-queue-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        min-height: 36px;
        padding: 7px 10px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 850;
    }

    .waiting-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 27px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .waiting-status-waiting {
        background: #fff7ed;
        color: #c2410c;
    }

    .waiting-status-offered {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .waiting-status-accepted {
        background: #f0fdf4;
        color: #15803d;
    }

    .waiting-status-expired {
        background: #f1f5f9;
        color: #475569;
    }

    .waiting-status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .waiting-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .waiting-detail-link {
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

    .waiting-detail-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .waiting-offer-button {
        min-height: 34px;
        padding: 7px 11px;
        border: 0;
        border-radius: 8px;
        background: #0284c7;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
    }

    .waiting-offer-button:hover {
        background: #0369a1;
    }

    .waiting-offer-button:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
    }

    .waiting-empty {
        padding: 55px 25px;
        text-align: center;
    }

    .waiting-empty-icon {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border-radius: 15px;
        background: #f0f9ff;
        color: #0284c7;
        font-size: 26px;
        font-weight: 800;
    }

    .waiting-empty-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .waiting-empty-description {
        max-width: 430px;
        margin: 7px auto 0;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    .waiting-pagination {
        padding: 16px 18px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 1250px) {
        .waiting-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .waiting-filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .waiting-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .waiting-filter-form {
            grid-template-columns: 1fr;
        }

        .waiting-filter-actions {
            width: 100%;
        }

        .waiting-filter-actions .btn-primary,
        .waiting-filter-actions .btn-secondary {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            Waiting List
        </h1>

        <p class="page-description">
            Kelola daftar tunggu peserta dan tawarkan slot kelas
            secara manual dengan memilih peserta yang akan menerima penawaran.
        </p>
    </div>
</div>

{{-- INFORMASI SISTEM --}}

<div class="waiting-info">
    <strong>Waiting List Manual</strong>

    <div>
        Peserta masuk antrean ketika kelas penuh.
        Penawaran slot hanya dilakukan oleh admin.
        Admin memilih peserta yang akan menerima penawaran,
        dan peserta memiliki waktu konfirmasi selama 2 jam.
        Tidak ada penawaran otomatis kepada peserta berikutnya.
    </div>
</div>

{{-- PESAN --}}

@if (session('success'))
    <div class="waiting-alert waiting-alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('info'))
    <div class="waiting-alert waiting-alert-info">
        {{ session('info') }}
    </div>
@endif

@if ($errors->any())
    <div class="waiting-alert waiting-alert-error">
        <strong>Permintaan tidak dapat diproses.</strong>

        <ul style="margin: 8px 0 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- STATISTIK --}}

<div class="waiting-stats">

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Total Antrean</div>
        <div class="waiting-stat-value">
            {{ $statistics['total'] }}
        </div>
    </div>

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Menunggu</div>
        <div class="waiting-stat-value">
            {{ $statistics['waiting'] }}
        </div>
    </div>

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Ditawarkan</div>
        <div class="waiting-stat-value">
            {{ $statistics['offered'] }}
        </div>
    </div>

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Diterima</div>
        <div class="waiting-stat-value">
            {{ $statistics['accepted'] }}
        </div>
    </div>

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Kedaluwarsa</div>
        <div class="waiting-stat-value">
            {{ $statistics['expired'] }}
        </div>
    </div>

    <div class="card waiting-stat-card">
        <div class="waiting-stat-label">Dibatalkan</div>
        <div class="waiting-stat-value">
            {{ $statistics['cancelled'] }}
        </div>
    </div>

</div>

{{-- FILTER --}}

<div class="card waiting-filter-card">

    <form
        method="GET"
        action="{{ route('admin.waiting-lists.index') }}"
        class="waiting-filter-form"
    >

        <div class="waiting-form-group">
            <label for="search" class="waiting-form-label">
                Cari Antrean
            </label>

            <input
                type="text"
                id="search"
                name="search"
                class="waiting-form-control"
                value="{{ $search }}"
                placeholder="Nama peserta, customer, atau program..."
            >
        </div>

        <div class="waiting-form-group">
            <label for="status" class="waiting-form-label">
                Status
            </label>

            <select
                id="status"
                name="status"
                class="waiting-form-control"
            >
                <option value="">Semua Status</option>

                <option value="waiting" @selected($status === 'waiting')>
                    Dalam Antrean
                </option>

                <option value="offered" @selected($status === 'offered')>
                    Ditawarkan
                </option>

                <option value="accepted" @selected($status === 'accepted')>
                    Diterima
                </option>

                <option value="expired" @selected($status === 'expired')>
                    Kedaluwarsa
                </option>

                <option value="cancelled" @selected($status === 'cancelled')>
                    Dibatalkan
                </option>
            </select>
        </div>

        <div class="waiting-form-group">
            <label for="date" class="waiting-form-label">
                Tanggal Kelas
            </label>

            <input
                type="date"
                id="date"
                name="date"
                class="waiting-form-control"
                value="{{ $date }}"
            >
        </div>

        <div class="waiting-filter-actions">
            <button type="submit" class="btn-primary">
                Terapkan
            </button>

            <a
                href="{{ route('admin.waiting-lists.index') }}"
                class="btn-secondary"
            >
                Reset
            </a>
        </div>

    </form>

</div>

{{-- DAFTAR ANTREAN --}}

<div class="card waiting-table-card">

    @if ($waitingLists->count() > 0)

        <div class="waiting-table-wrapper">

            <table class="waiting-table">

                <thead>
                    <tr>
                        <th>No. Antrean</th>
                        <th>Peserta</th>
                        <th>Program & Jadwal</th>
                        <th>Coach & Lokasi</th>
                        <th>Status</th>
                        <th>Waktu Penawaran</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($waitingLists as $waitingList)

                        @php
                            $participant = $waitingList->participant;
                            $schedule = $waitingList->classSchedule;

                            $isExpiredOffer = $waitingList->status === 'offered'
                                && (
                                    $waitingList->expires_at === null
                                    || $waitingList->expires_at->lte(now())
                                );

                            $displayStatus = $isExpiredOffer
                                ? 'expired'
                                : $waitingList->status;

                            $statusLabel = match ($displayStatus) {
                                'waiting' => 'Dalam Antrean',
                                'offered' => 'Ditawarkan',
                                'accepted' => 'Diterima',
                                'expired' => 'Kedaluwarsa',
                                'cancelled' => 'Dibatalkan',
                                default => 'Tidak Diketahui',
                            };

                            $canOffer = $waitingList->status === 'waiting'
                                && $schedule !== null
                                && $schedule->isScheduled()
                                && !$schedule->hasStarted();
                        @endphp

                        <tr>

                            <td>
                                <span class="waiting-queue-number">
                                    #{{ $waitingList->queue_number }}
                                </span>

                                <div class="waiting-sub-text">
                                    Masuk:
                                    {{ $waitingList->created_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="waiting-main-text">
                                    {{ $participant?->full_name ?? '-' }}
                                </div>

                                <div class="waiting-sub-text">
                                    Customer:
                                    {{ $participant?->user?->name ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="waiting-main-text">
                                    {{ $schedule?->program?->name ?? '-' }}
                                </div>

                                <div class="waiting-sub-text">
                                    {{ $schedule?->title ?? '-' }}
                                </div>

                                <div class="waiting-sub-text">
                                    {{ $schedule?->class_date?->format('d M Y') ?? '-' }}

                                    @if ($schedule?->start_time)
                                        |
                                        {{ substr($schedule->start_time, 0, 5) }}

                                        @if ($schedule->end_time)
                                            -
                                            {{ substr($schedule->end_time, 0, 5) }}
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="waiting-main-text">
                                    {{ $schedule?->coach?->user?->name ?? '-' }}
                                </div>

                                <div class="waiting-sub-text">
                                    {{ $schedule?->location?->name ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <span
                                    class="waiting-status waiting-status-{{ $displayStatus }}"
                                >
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td>
                                @if ($displayStatus === 'offered')
                                    <div class="waiting-main-text">
                                        {{ $waitingList->offered_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>

                                    <div class="waiting-sub-text">
                                        Batas:
                                        {{ $waitingList->expires_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>
                                @elseif ($isExpiredOffer)
                                    <div class="waiting-main-text">
                                        Penawaran berakhir
                                    </div>

                                    <div class="waiting-sub-text">
                                        {{ $waitingList->expires_at?->format('d M Y, H:i') ?? '-' }}
                                    </div>
                                @else
                                    <span class="waiting-sub-text">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="waiting-actions">

                                    <a
                                        href="{{ route('admin.waiting-lists.show', $waitingList) }}"
                                        class="waiting-detail-link"
                                    >
                                        Detail
                                    </a>

                                    @if ($canOffer)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.waiting-lists.offer', $waitingList) }}"
                                            onsubmit="return confirm('Tawarkan slot kepada peserta ini?')"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="waiting-offer-button"
                                            >
                                                Tawarkan ke Peserta Ini
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @if ($waitingLists->hasPages())
            <div class="waiting-pagination">
                {{ $waitingLists->links() }}
            </div>
        @endif

    @else

        <div class="waiting-empty">

            <div class="waiting-empty-icon">
                WL
            </div>

            <div class="waiting-empty-title">
                Waiting List tidak ditemukan
            </div>

            <div class="waiting-empty-description">
                Belum ada peserta yang masuk daftar tunggu,
                atau tidak ada data yang sesuai dengan filter.
            </div>

        </div>

    @endif

</div>

@endsection
