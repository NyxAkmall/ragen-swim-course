@extends('layouts.admin')

@section('title', 'Detail Waiting List')
@section('topbar-title', 'Detail Waiting List')

@push('styles')
<style>
    .wl-detail-container {
        max-width: 1150px;
    }

    .wl-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        color: var(--blue-dark);
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .wl-back-link:hover {
        text-decoration: underline;
    }

    .wl-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(290px, 1fr);
        gap: 20px;
        align-items: start;
    }

    .wl-panel {
        padding: 22px;
        margin-bottom: 20px;
    }

    .wl-panel-title {
        margin: 0 0 20px;
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .wl-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .wl-label {
        margin-bottom: 6px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .wl-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 750;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .wl-muted {
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 500;
    }

    .wl-status {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 850;
    }

    .wl-status-waiting {
        background: #fff7ed;
        color: #c2410c;
    }

    .wl-status-offered {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .wl-status-accepted {
        background: #f0fdf4;
        color: #15803d;
    }

    .wl-status-expired {
        background: #f1f5f9;
        color: #475569;
    }

    .wl-status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .wl-queue-box {
        padding: 22px;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        background: #eff6ff;
        text-align: center;
    }

    .wl-queue-label {
        color: #1e40af;
        font-size: 12px;
        font-weight: 750;
    }

    .wl-queue-value {
        margin: 8px 0;
        color: #1d4ed8;
        font-size: 38px;
        font-weight: 900;
    }

    .wl-queue-description {
        color: #1e40af;
        font-size: 11px;
        line-height: 1.6;
    }

    .wl-note {
        margin-top: 16px;
        padding: 14px;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        background: #f0f9ff;
        color: #075985;
        font-size: 12px;
        line-height: 1.7;
    }

    .wl-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .wl-action-button {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 42px;
        padding: 10px 14px;
        border: 0;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 850;
        cursor: pointer;
    }

    .wl-action-offer {
        background: #0284c7;
        color: #fff;
    }

    .wl-action-offer:hover {
        background: #0369a1;
    }

    .wl-action-cancel {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .wl-action-cancel:hover {
        background: #fee2e2;
    }

    .wl-alert {
        padding: 14px 16px;
        margin-bottom: 18px;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.7;
    }

    .wl-alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .wl-alert-info {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .wl-alert-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    @media (max-width: 850px) {
        .wl-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .wl-info-grid {
            grid-template-columns: 1fr;
        }

        .wl-panel {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')

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
        'offered' => 'Slot Ditawarkan',
        'accepted' => 'Diterima',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
        default => 'Tidak Diketahui',
    };

    $canOffer = $waitingList->status === 'waiting'
        && $schedule !== null
        && $schedule->isScheduled()
        && !$schedule->hasStarted();

    $canCancel = $waitingList->status === 'waiting'
        || (
            $waitingList->status === 'offered'
            && !$isExpiredOffer
        );
@endphp

<div class="wl-detail-container">

    <a
        href="{{ route('admin.waiting-lists.index') }}"
        class="wl-back-link"
    >
        &larr; Kembali ke Waiting List
    </a>

    <div class="page-header">
        <div>
            <h1 class="page-title">
                Detail Waiting List
            </h1>

            <p class="page-description">
                Informasi antrean peserta, jadwal kelas,
                status penawaran, dan tindakan admin.
            </p>
        </div>
    </div>

    {{-- PESAN SISTEM --}}

    @if (session('success'))
        <div class="wl-alert wl-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="wl-alert wl-alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="wl-alert wl-alert-error">
            <strong>Permintaan tidak dapat diproses.</strong>

            <ul style="margin: 8px 0 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="wl-detail-grid">

        {{-- KOLOM KIRI --}}

        <div>

            <div class="card wl-panel">
                <h2 class="wl-panel-title">
                    Informasi Peserta
                </h2>

                <div class="wl-info-grid">

                    <div>
                        <div class="wl-label">Nama Peserta</div>
                        <div class="wl-value">
                            {{ $participant?->full_name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Customer / Orang Tua</div>
                        <div class="wl-value">
                            {{ $participant?->user?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Level Renang</div>
                        <div class="wl-value">
                            {{ ucfirst($participant?->swimming_level ?? 'unknown') }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Tanggal Mendaftar Antrean</div>
                        <div class="wl-value">
                            {{ $waitingList->created_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="card wl-panel">
                <h2 class="wl-panel-title">
                    Informasi Jadwal Kelas
                </h2>

                <div class="wl-info-grid">

                    <div>
                        <div class="wl-label">Program</div>
                        <div class="wl-value">
                            {{ $schedule?->program?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Nama Kelas</div>
                        <div class="wl-value">
                            {{ $schedule?->title ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Tanggal Kelas</div>
                        <div class="wl-value">
                            {{ $schedule?->class_date?->format('d M Y') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Jam Kelas</div>
                        <div class="wl-value">
                            @if ($schedule?->start_time)
                                {{ substr($schedule->start_time, 0, 5) }}

                                @if ($schedule->end_time)
                                    -
                                    {{ substr($schedule->end_time, 0, 5) }}
                                @endif
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Coach</div>
                        <div class="wl-value">
                            {{ $schedule?->coach?->user?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Lokasi</div>
                        <div class="wl-value">
                            {{ $schedule?->location?->name ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="card wl-panel">
                <h2 class="wl-panel-title">
                    Informasi Penawaran
                </h2>

                <div class="wl-info-grid">

                    <div>
                        <div class="wl-label">Status</div>
                        <div class="wl-value">
                            <span class="wl-status wl-status-{{ $displayStatus }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Nomor Antrean</div>
                        <div class="wl-value">
                            #{{ $waitingList->queue_number }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Waktu Penawaran</div>
                        <div class="wl-value">
                            {{ $waitingList->offered_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">Batas Konfirmasi</div>
                        <div class="wl-value">
                            {{ $waitingList->expires_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>

                </div>

                @if ($displayStatus === 'offered')
                    <div class="wl-note">
                        Slot sedang ditawarkan kepada peserta ini.
                        Customer memiliki waktu hingga
                        <strong>
                            {{ $waitingList->expires_at?->format('d M Y, H:i') }}
                        </strong>
                        untuk memberikan konfirmasi.
                    </div>
                @elseif ($displayStatus === 'expired')
                    <div class="wl-note">
                        Penawaran sudah kedaluwarsa.
                        Slot yang tersedia tidak akan ditawarkan
                        secara otomatis kepada peserta berikutnya.
                    </div>
                @endif
            </div>

        </div>

        {{-- KOLOM KANAN --}}

        <div>

            <div class="wl-queue-box">
                <div class="wl-queue-label">
                    Nomor Antrean
                </div>

                <div class="wl-queue-value">
                    #{{ $waitingList->queue_number }}
                </div>

                <div class="wl-queue-description">
                    @if ($queuePosition !== null)
                        Posisi saat ini dalam daftar peserta
                        yang masih menunggu:
                        <strong>{{ $queuePosition }}</strong>
                    @else
                        Peserta tidak sedang berada dalam
                        antrean aktif yang menunggu.
                    @endif
                </div>
            </div>

            <div class="card wl-panel" style="margin-top: 20px;">
                <h2 class="wl-panel-title">
                    Pengelolaan Antrean
                </h2>

                <div class="wl-info-grid" style="margin-bottom: 20px;">
                    <div>
                        <div class="wl-label">
                            Penawaran Aktif
                        </div>

                        <div class="wl-value">
                            {{ $reservedOffers }}
                        </div>
                    </div>

                    <div>
                        <div class="wl-label">
                            Status Jadwal
                        </div>

                        <div class="wl-value">
                            {{ $schedule?->status ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="wl-actions">

                    @if ($canOffer)
                        <form
                            method="POST"
                            action="{{ route('admin.waiting-lists.offer', $waitingList) }}"
                            onsubmit="return confirm('Tawarkan slot kepada peserta ini?')"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="wl-action-button wl-action-offer"
                            >
                                Tawarkan Slot ke Peserta Ini
                            </button>
                        </form>
                    @endif

                    @if ($canCancel)
                        <form
                            method="POST"
                            action="{{ route('admin.waiting-lists.cancel', $waitingList) }}"
                            onsubmit="return confirm('Yakin ingin membatalkan Waiting List peserta ini?')"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="wl-action-button wl-action-cancel"
                            >
                                Batalkan Waiting List
                            </button>
                        </form>
                    @endif

                    @if (!$canOffer && !$canCancel)
                        <p class="wl-muted">
                            Tidak ada tindakan yang tersedia
                            untuk status antrean ini.
                        </p>
                    @endif

                </div>

                <div class="wl-note">
                    Penawaran slot dilakukan secara manual oleh Admin kepada
                    peserta yang sedang ditampilkan pada halaman ini.
                    Nomor antrean hanya sebagai informasi urutan pendaftaran;
                    Admin dapat memilih peserta mana pun yang masih menunggu
                    selama tersedia slot pada jadwal tersebut.
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
