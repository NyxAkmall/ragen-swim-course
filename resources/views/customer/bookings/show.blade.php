@extends('layouts.customer')

@section('title', 'Detail Booking')
@section('topbar-title', 'Detail Booking')

@section('content')

@php
    $schedule = $booking->classSchedule;
    $participant = $booking->participant;
    $participantPackage = $booking->participantPackage;
    $package = $participantPackage?->package;
    $program = $package?->program ?? $schedule?->program;
    $coach = $schedule?->coach;
    $location = $schedule?->location;

    $statusLabel = match ($booking->status) {
        'pending' => 'Menunggu Konfirmasi',
        'confirmed' => 'Terkonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rescheduled' => 'Dijadwalkan Ulang',
        default => ucfirst($booking->status),
    };

    $statusClass = match ($booking->status) {
        'pending' => 'status-pending',
        'confirmed' => 'status-confirmed',
        'completed' => 'status-completed',
        'cancelled' => 'status-cancelled',
        'rescheduled' => 'status-rescheduled',
        default => 'status-default',
    };

    $levelLabel = match ($schedule?->swimming_level) {
        'all' => 'Semua Level',
        'beginner' => 'Beginner',
        'basic' => 'Basic',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        default => $schedule?->swimming_level
            ? ucfirst($schedule->swimming_level)
            : '-',
    };

    $canModify = in_array(
        $booking->status,
        ['pending', 'confirmed'],
        true
    );

    $previousBooking = $booking->previousBooking;
    $newBooking = $booking->rescheduledBooking;
@endphp


<div class="booking-detail-page">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="page-header">

        <div>

            <a
                href="{{ route('customer.bookings.index') }}"
                class="back-link"
            >
                ← Kembali ke Jadwal & Booking
            </a>

            <span class="eyebrow">
                DETAIL BOOKING
            </span>

            <h1>
                {{ $schedule?->title ?: ($program?->name ?? 'Kelas Renang') }}
            </h1>

            <p>
                Informasi lengkap jadwal dan booking peserta.
            </p>

        </div>


        <span class="status-badge {{ $statusClass }}">
            {{ $statusLabel }}
        </span>

    </div>


    {{-- =========================================================
        BOOKING CODE
    ========================================================== --}}

    <section class="booking-code-card">

        <div>

            <span class="booking-code-label">
                KODE BOOKING
            </span>

            <div class="booking-code-row">

                <strong id="bookingCode">
                    {{ $booking->booking_code }}
                </strong>

                <button
                    type="button"
                    class="copy-button"
                    id="copyBookingCode"
                >
                    Salin
                </button>

            </div>

            <small>
                Simpan kode ini untuk referensi booking.
            </small>

        </div>


        <div class="booking-created">

            <span>
                Dibuat pada
            </span>

            <strong>
                {{
                    $booking->booked_at
                        ?->translatedFormat('d F Y, H:i')
                    ?? '-'
                }}
                WIB
            </strong>

        </div>

    </section>


    {{-- =========================================================
        RESCHEDULE RELATIONSHIP
    ========================================================== --}}

    @if ($booking->status === 'rescheduled' && $newBooking)

        <section class="reschedule-alert old-booking">

            <div class="reschedule-alert-icon">
                ↗
            </div>

            <div class="reschedule-alert-content">

                <span>
                    BOOKING INI TELAH DIJADWALKAN ULANG
                </span>

                <h3>
                    Booking Baru Sudah Dibuat
                </h3>

                <p>
                    Booking ini tetap disimpan sebagai riwayat.
                    Jadwal penggantinya menggunakan kode
                    <strong>
                        {{ $newBooking->booking_code }}
                    </strong>.
                </p>

            </div>

            <a
                href="{{ route('customer.bookings.show', $newBooking) }}"
                class="relation-button"
            >
                Lihat Booking Baru →
            </a>

        </section>

    @endif


    @if ($previousBooking)

        <section class="reschedule-alert new-booking">

            <div class="reschedule-alert-icon">
                ↙
            </div>

            <div class="reschedule-alert-content">

                <span>
                    HASIL RESCHEDULE
                </span>

                <h3>
                    Booking Ini Menggantikan Jadwal Sebelumnya
                </h3>

                <p>
                    Booking sebelumnya menggunakan kode
                    <strong>
                        {{ $previousBooking->booking_code }}
                    </strong>.
                    Riwayat booking lama tetap tersimpan.
                </p>

            </div>

            <a
                href="{{ route('customer.bookings.show', $previousBooking) }}"
                class="relation-button"
            >
                ← Lihat Booking Sebelumnya
            </a>

        </section>

    @endif


    {{-- =========================================================
        MAIN GRID
    ========================================================== --}}

    <div class="detail-grid">

        <div class="main-column">

            {{-- =================================================
                PARTICIPANT
            ================================================== --}}

            <section class="detail-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        👤
                    </div>

                    <div>
                        <span>DATA PESERTA</span>
                        <h2>Peserta Booking</h2>
                    </div>

                </div>


                <div class="participant-box">

                    <div class="participant-avatar">
                        {{
                            strtoupper(
                                mb_substr(
                                    $participant?->full_name ?? 'P',
                                    0,
                                    1
                                )
                            )
                        }}
                    </div>

                    <div>

                        <span>
                            Nama Peserta
                        </span>

                        <strong>
                            {{ $participant?->full_name ?? '-' }}
                        </strong>

                    </div>

                </div>

            </section>


            {{-- =================================================
                SCHEDULE
            ================================================== --}}

            <section class="detail-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        📅
                    </div>

                    <div>
                        <span>JADWAL KELAS</span>
                        <h2>Jadwal Kelas yang Dipesan</h2>
                    </div>

                </div>


                <div class="schedule-title-box">

                    <span>
                        {{ $program?->name ?? 'Program Renang' }}
                    </span>

                    <h3>
                        {{
                            $schedule?->title
                            ?: ($program?->name ?? 'Kelas Renang')
                        }}
                    </h3>

                </div>


                <div class="information-grid">

                    <div class="information-item">

                        <span>
                            Tanggal
                        </span>

                        <strong>
                            {{
                                $schedule?->class_date
                                    ?->locale('id')
                                    ?->translatedFormat('l, d F Y')
                                ?? '-'
                            }}
                        </strong>

                    </div>


                    <div class="information-item">

                        <span>
                            Waktu
                        </span>

                        <strong>
                            @if ($schedule)

                                {{ substr($schedule->start_time, 0, 5) }}
                                -
                                {{ substr($schedule->end_time, 0, 5) }}
                                WIB

                            @else
                                -
                            @endif
                        </strong>

                    </div>


                    <div class="information-item">

                        <span>
                            Level
                        </span>

                        <strong>
                            {{ $levelLabel }}
                        </strong>

                    </div>


                    <div class="information-item">

                        <span>
                            Pelatih
                        </span>

                        <strong>
                            {{ $coach?->name ?? 'Belum ditentukan' }}
                        </strong>

                    </div>

                </div>

            </section>


            {{-- =================================================
                LOCATION
            ================================================== --}}

            <section class="detail-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        📍
                    </div>

                    <div>
                        <span>LOKASI</span>
                        <h2>Lokasi Latihan</h2>
                    </div>

                </div>


                <div class="location-box">

                    <div>

                        <h3>
                            {{ $location?->name ?? 'Belum ditentukan' }}
                        </h3>

                        <p>
                            {{ $location?->address ?? '-' }}
                        </p>

                        @if ($location?->city)

                            <small>
                                {{ $location->city }}
                            </small>

                        @endif

                    </div>


                    @if ($location?->google_maps_url)

                        <a
                            href="{{ $location->google_maps_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="maps-button"
                        >
                            Buka Google Maps ↗
                        </a>

                    @endif

                </div>

            </section>


            {{-- =================================================
                NOTES
            ================================================== --}}

            @if ($booking->notes)

                <section class="detail-card">

                    <div class="card-heading">

                        <div class="heading-icon">
                            📝
                        </div>

                        <div>
                            <span>CATATAN</span>
                            <h2>Catatan Booking</h2>
                        </div>

                    </div>

                    <div class="notes-box">
                        {{ $booking->notes }}
                    </div>

                </section>

            @endif


            {{-- =================================================
                CANCELLATION INFORMATION
            ================================================== --}}

            @if ($booking->status === 'cancelled')

                <section class="cancelled-card">

                    <div class="card-heading">

                        <div class="heading-icon danger">
                            ×
                        </div>

                        <div>
                            <span>PEMBATALAN</span>
                            <h2>Booking Dibatalkan</h2>
                        </div>

                    </div>


                    <div class="cancellation-grid">

                        <div>

                            <span>
                                Waktu Pembatalan
                            </span>

                            <strong>
                                {{
                                    $booking->cancelled_at
                                        ?->translatedFormat(
                                            'd F Y, H:i'
                                        )
                                    ?? '-'
                                }}
                                WIB
                            </strong>

                        </div>


                        <div>

                            <span>
                                Alasan Pembatalan
                            </span>

                            <strong>
                                {{
                                    $booking->cancellation_reason
                                    ?? '-'
                                }}
                            </strong>

                        </div>

                    </div>

                </section>

            @endif

        </div>


        {{-- =====================================================
            SIDEBAR
        ====================================================== --}}

        <aside class="side-column">

            {{-- =================================================
                PACKAGE
            ================================================== --}}

            <section class="side-card">

                <span class="side-eyebrow">
                    PAKET PESERTA
                </span>

                <h3>
                    {{ $package?->name ?? 'Paket Renang' }}
                </h3>

                <p>
                    {{ $program?->name ?? 'Program Renang' }}
                </p>


                <div class="session-statistics">

                    <div>

                        <span>
                            Total
                        </span>

                        <strong>
                            {{ $participantPackage?->total_sessions ?? 0 }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Terpakai
                        </span>

                        <strong>
                            {{ $participantPackage?->used_sessions ?? 0 }}
                        </strong>

                    </div>


                    <div class="remaining">

                        <span>
                            Tersisa
                        </span>

                        <strong>
                            {{ $participantPackage?->remaining_sessions ?? 0 }}
                        </strong>

                    </div>

                </div>


                <div class="package-information">

                    <div>

                        <span>
                            Status Paket
                        </span>

                        <strong>
                            {{
                                match ($participantPackage?->status) {
                                    'pending' => 'Menunggu',
                                    'active' => 'Aktif',
                                    'completed' => 'Selesai',
                                    'expired' => 'Kedaluwarsa',
                                    'frozen' => 'Dibekukan',
                                    'cancelled' => 'Dibatalkan',
                                    default => ucfirst(
                                        $participantPackage?->status ?? '-'
                                    ),
                                }
                            }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Berlaku Sampai
                        </span>

                        <strong>
                            {{
                                $participantPackage?->expiry_date
                                    ?->translatedFormat('d F Y')
                                ?? '-'
                            }}
                        </strong>

                    </div>

                </div>


                @if ($participantPackage)

                    <a
                        href="{{ route(
                            'customer.packages.show',
                            $participantPackage
                        ) }}"
                        class="package-detail-button"
                    >
                        Lihat Detail Paket
                    </a>

                @endif

            </section>


            {{-- =================================================
                SESSION NOTICE
            ================================================== --}}

            <section class="session-notice">

                @if ($booking->session_deducted)

                    <div class="notice-icon used">
                        ✓
                    </div>

                    <div>

                        <strong>
                            Sesi Sudah Digunakan
                        </strong>

                        <p>
                            Sesi paket untuk booking ini sudah
                            tercatat sebagai terpakai.
                        </p>

                    </div>

                @else

                    <div class="notice-icon">
                        i
                    </div>

                    <div>

                        <strong>
                            Sesi Belum Dipotong
                        </strong>

                        <p>
                            Booking dan reschedule tidak langsung
                            mengurangi sesi. Sesi akan dicatat saat
                            proses kehadiran.
                        </p>

                    </div>

                @endif

            </section>


            {{-- =================================================
                ACTIONS
            ================================================== --}}

            @if ($canModify)

                <section class="action-card">

                    <span class="side-eyebrow">
                        KELOLA BOOKING
                    </span>

                    <h3>
                        Perlu Mengubah Jadwal?
                    </h3>

                    <p>
                        Selama kelas belum dimulai, booking dapat
                        dijadwalkan ulang atau dibatalkan sesuai
                        ketentuan yang berlaku.
                    </p>


                    <a
                        href="{{ route(
                            'customer.bookings.reschedule',
                            $booking
                        ) }}"
                        class="reschedule-button"
                    >
                        ↻ Reschedule Booking
                    </a>


                    <button
                        type="button"
                        class="cancel-booking-button"
                        id="openCancelModal"
                    >
                        Batalkan Booking
                    </button>

                </section>

            @elseif ($booking->status === 'rescheduled')

                <section class="action-card readonly">

                    <span class="side-eyebrow">
                        STATUS BOOKING
                    </span>

                    <h3>
                        Booking Sudah Dijadwalkan Ulang
                    </h3>

                    <p>
                        Booking lama tidak dapat diubah lagi karena
                        jadwal pengganti sudah dibuat.
                    </p>

                    @if ($newBooking)

                        <a
                            href="{{ route(
                                'customer.bookings.show',
                                $newBooking
                            ) }}"
                            class="reschedule-button"
                        >
                            Lihat Booking Baru →
                        </a>

                    @endif

                </section>

            @elseif ($booking->status === 'cancelled')

                <section class="action-card readonly">

                    <span class="side-eyebrow">
                        STATUS BOOKING
                    </span>

                    <h3>
                        Booking Telah Dibatalkan
                    </h3>

                    <p>
                        Booking yang sudah dibatalkan tidak dapat
                        diubah atau dijadwalkan ulang.
                    </p>

                </section>

            @endif

        </aside>

    </div>


    {{-- =========================================================
        BOOKING HISTORY
    ========================================================== --}}

    @if ($previousBooking || $newBooking)

        <section class="history-section">

            <div class="history-heading">

                <span class="eyebrow">
                    RIWAYAT RESCHEDULE
                </span>

                <h2>
                    Perjalanan Booking
                </h2>

                <p>
                    Riwayat perubahan jadwal tetap tersimpan agar
                    booking dapat ditelusuri.
                </p>

            </div>


            <div class="history-list">

                @if ($previousBooking)

                    <a
                        href="{{ route(
                            'customer.bookings.show',
                            $previousBooking
                        ) }}"
                        class="history-card"
                    >

                        <div class="history-icon">
                            ←
                        </div>

                        <div class="history-content">

                            <span>
                                BOOKING SEBELUMNYA
                            </span>

                            <strong>
                                {{ $previousBooking->booking_code }}
                            </strong>

                            <small>
                                Klik untuk melihat jadwal sebelum
                                reschedule.
                            </small>

                        </div>

                        <div class="history-arrow">
                            →
                        </div>

                    </a>

                @endif


                @if ($newBooking)

                    <a
                        href="{{ route(
                            'customer.bookings.show',
                            $newBooking
                        ) }}"
                        class="history-card current">

                        <div class="history-icon">
                            ↗
                        </div>

                        <div class="history-content">

                            <span>
                                BOOKING PENGGANTI
                            </span>

                            <strong>
                                {{ $newBooking->booking_code }}
                            </strong>

                            <small>
                                Klik untuk melihat jadwal baru hasil
                                reschedule.
                            </small>

                        </div>

                        <div class="history-arrow">
                            →
                        </div>

                    </a>

                @endif

            </div>

        </section>

    @endif

</div>


{{-- =============================================================
    CANCEL MODAL
============================================================== --}}

@if ($canModify)

    <div
        class="modal-overlay"
        id="cancelModal"
        aria-hidden="true"
    >

        <div class="modal-card">

            <button
                type="button"
                class="modal-close"
                id="closeCancelModal"
                aria-label="Tutup"
            >
                ×
            </button>


            <div class="modal-icon">
                !
            </div>


            <span class="modal-eyebrow">
                BATALKAN BOOKING
            </span>

            <h2>
                Yakin ingin membatalkan?
            </h2>

            <p class="modal-description">
                Booking
                <strong>
                    {{ $booking->booking_code }}
                </strong>
                akan dibatalkan. Sesi paket tidak berkurang
                karena sesi belum dipotong pada proses booking.
            </p>


            <div class="cancel-summary">

                <div>

                    <span>
                        Peserta
                    </span>

                    <strong>
                        {{ $participant?->full_name ?? '-' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Kelas
                    </span>

                    <strong>
                        {{
                            $schedule?->title
                            ?: ($program?->name ?? '-')
                        }}
                    </strong>

                </div>


                <div>

                    <span>
                        Jadwal
                    </span>

                    <strong>
                        {{
                            $schedule?->class_date
                                ?->translatedFormat('d M Y')
                            ?? '-'
                        }}

                        @if ($schedule)
                            •
                            {{ substr($schedule->start_time, 0, 5) }}
                            WIB
                        @endif
                    </strong>

                </div>

            </div>


            <form
                action="{{ route(
                    'customer.bookings.cancel',
                    $booking
                ) }}"
                method="POST"
                id="cancelBookingForm"
            >

                @csrf
                @method('PATCH')


                <div class="form-group">

                    <label for="cancellation_reason">
                        Alasan Pembatalan
                        <span>*</span>
                    </label>

                    <textarea
                        name="cancellation_reason"
                        id="cancellation_reason"
                        rows="4"
                        maxlength="1000"
                        minlength="3"
                        required
                        placeholder="Contoh: Ada perubahan jadwal kegiatan keluarga."
                    >{{ old('cancellation_reason') }}</textarea>

                    @error('cancellation_reason')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                <div class="modal-notice">

                    <span>
                        ℹ️
                    </span>

                    <p>
                        Pembatalan ini tidak menambah atau
                        mengurangi sisa sesi karena sesi booking
                        belum dipotong.
                    </p>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="modal-back-button"
                        id="cancelModalBack"
                    >
                        Kembali
                    </button>


                    <button
                        type="submit"
                        class="confirm-cancel-button"
                        id="confirmCancelButton"
                    >
                        Ya, Batalkan Booking
                    </button>

                </div>

            </form>

        </div>

    </div>

@endif

@endsection


@push('styles')

<style>

    .booking-detail-page {
        width: 100%;
    }

    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 15px;
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 12px;
        color: #0284c7;
        font-size: 8px;
        font-weight: 800;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .eyebrow,
    .side-eyebrow {
        display: block;
        margin-bottom: 4px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        letter-spacing: .7px;
    }

    .page-header h1 {
        color: var(--navy);
        font-size: 23px;
        font-weight: 900;
    }

    .page-header p {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 29px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 7px;
        font-weight: 850;
        white-space: nowrap;
    }

    .status-pending {
        background: #fef3c7;
        color: #a16207;
    }

    .status-confirmed {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-rescheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-default {
        background: #f1f5f9;
        color: #475569;
    }


    /*
    |--------------------------------------------------------------------------
    | Booking Code
    |--------------------------------------------------------------------------
    */

    .booking-code-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 13px;
        padding: 15px;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        background:
            linear-gradient(
                135deg,
                #f0f9ff,
                #ecfeff
            );
    }

    .booking-code-label {
        display: block;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        letter-spacing: .6px;
    }

    .booking-code-row {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 2px;
    }

    .booking-code-row strong {
        color: #075985;
        font-size: 16px;
        font-weight: 900;
    }

    .copy-button {
        padding: 4px 7px;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        background: #ffffff;
        color: #0369a1;
        font-family: inherit;
        font-size: 6px;
        font-weight: 800;
        cursor: pointer;
    }

    .booking-code-card small {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 6px;
    }

    .booking-created {
        text-align: right;
    }

    .booking-created span {
        display: block;
        color: #64748b;
        font-size: 6px;
    }

    .booking-created strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 7px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Reschedule Alert
    |--------------------------------------------------------------------------
    */

    .reschedule-alert {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 11px;
        margin-bottom: 13px;
        padding: 13px;
        border-radius: 11px;
    }

    .reschedule-alert.old-booking {
        border: 1px solid #bae6fd;
        background: #f0f9ff;
    }

    .reschedule-alert.new-booking {
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
    }

    .reschedule-alert-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #ffffff;
        color: #0284c7;
        font-size: 14px;
        font-weight: 900;
    }

    .reschedule-alert-content span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 850;
    }

    .reschedule-alert-content h3 {
        margin-top: 2px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 850;
    }

    .reschedule-alert-content p {
        margin-top: 2px;
        color: #64748b;
        font-size: 7px;
        line-height: 1.5;
    }

    .relation-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 10px;
        border: 1px solid #bae6fd;
        border-radius: 7px;
        background: #ffffff;
        color: #0369a1;
        font-size: 7px;
        font-weight: 850;
        text-decoration: none;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Main Layout
    |--------------------------------------------------------------------------
    */

    .detail-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1.7fr)
            minmax(230px, .8fr);
        gap: 13px;
        align-items: start;
    }

    .main-column,
    .side-column {
        display: flex;
        flex-direction: column;
        gap: 11px;
        min-width: 0;
    }

    .detail-card,
    .side-card,
    .action-card,
    .cancelled-card {
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #ffffff;
    }

    .card-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 11px;
    }

    .heading-icon {
        width: 31px;
        height: 31px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        background: #f0f9ff;
        font-size: 12px;
    }

    .heading-icon.danger {
        background: #fee2e2;
        color: #b91c1c;
        font-size: 16px;
        font-weight: 900;
    }

    .card-heading span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 850;
    }

    .card-heading h2 {
        margin-top: 1px;
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant
    |--------------------------------------------------------------------------
    */

    .participant-box {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 11px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .participant-avatar {
        width: 39px;
        height: 39px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 13px;
        font-weight: 900;
    }

    .participant-box span {
        display: block;
        color: #94a3b8;
        font-size: 6px;
    }

    .participant-box strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 9px;
        font-weight: 850;
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    .schedule-title-box {
        margin-bottom: 9px;
        padding: 10px;
        border-radius: 8px;
        background: #f0f9ff;
    }

    .schedule-title-box span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 800;
    }

    .schedule-title-box h3 {
        margin-top: 2px;
        color: #075985;
        font-size: 10px;
        font-weight: 850;
    }

    .information-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .information-item {
        min-width: 0;
        padding: 9px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .information-item span {
        display: block;
        color: #94a3b8;
        font-size: 6px;
    }

    .information-item strong {
        display: block;
        margin-top: 2px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Location
    |--------------------------------------------------------------------------
    */

    .location-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .location-box h3 {
        color: #334155;
        font-size: 9px;
        font-weight: 850;
    }

    .location-box p {
        margin-top: 2px;
        color: #64748b;
        font-size: 7px;
        line-height: 1.5;
    }

    .location-box small {
        display: block;
        margin-top: 2px;
        color: #94a3b8;
        font-size: 6px;
    }

    .maps-button {
        flex-shrink: 0;
        padding: 7px 9px;
        border: 1px solid #bae6fd;
        border-radius: 7px;
        background: #ffffff;
        color: #0369a1;
        font-size: 7px;
        font-weight: 800;
        text-decoration: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Notes / Cancelled
    |--------------------------------------------------------------------------
    */

    .notes-box {
        padding: 10px;
        border-radius: 8px;
        background: #f8fafc;
        color: #475569;
        font-size: 8px;
        line-height: 1.6;
    }

    .cancelled-card {
        border-color: #fecaca;
        background: #fffafa;
    }

    .cancellation-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .cancellation-grid > div {
        padding: 9px;
        border-radius: 8px;
        background: #ffffff;
    }

    .cancellation-grid span {
        display: block;
        color: #94a3b8;
        font-size: 6px;
    }

    .cancellation-grid strong {
        display: block;
        margin-top: 2px;
        color: #7f1d1d;
        font-size: 7px;
        font-weight: 800;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Package
    |--------------------------------------------------------------------------
    */

    .side-card h3,
    .action-card h3 {
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }

    .side-card > p,
    .action-card > p {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 7px;
        line-height: 1.5;
    }

    .session-statistics {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
        gap: 5px;
        margin-top: 11px;
    }

    .session-statistics > div {
        padding: 8px 5px;
        border-radius: 7px;
        background: #f8fafc;
        text-align: center;
    }

    .session-statistics .remaining {
        background: #f0f9ff;
    }

    .session-statistics span {
        display: block;
        color: #94a3b8;
        font-size: 6px;
    }

    .session-statistics strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 13px;
        font-weight: 900;
    }

    .session-statistics .remaining strong {
        color: #0284c7;
    }

    .package-information {
        margin-top: 9px;
        border-top: 1px solid #e2e8f0;
    }

    .package-information > div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .package-information span {
        color: #94a3b8;
        font-size: 6px;
    }

    .package-information strong {
        color: #334155;
        font-size: 7px;
        font-weight: 800;
        text-align: right;
    }

    .package-detail-button {
        min-height: 33px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
        padding: 7px;
        border: 1px solid #bae6fd;
        border-radius: 7px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 7px;
        font-weight: 850;
        text-decoration: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Session Notice
    |--------------------------------------------------------------------------
    */

    .session-notice {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 11px;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        background: #f0f9ff;
    }

    .notice-icon {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        color: #0284c7;
        font-size: 9px;
        font-weight: 900;
    }

    .notice-icon.used {
        color: #15803d;
    }

    .session-notice strong {
        display: block;
        color: #075985;
        font-size: 8px;
        font-weight: 850;
    }

    .session-notice p {
        margin-top: 2px;
        color: #0369a1;
        font-size: 6px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .action-card.readonly {
        background: #f8fafc;
    }

    .reschedule-button,
    .cancel-booking-button {
        width: 100%;
        min-height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
        padding: 8px;
        border-radius: 7px;
        font-family: inherit;
        font-size: 7px;
        font-weight: 850;
        text-decoration: none;
    }

    .reschedule-button {
        border: 0;
        background:
            linear-gradient(
                135deg,
                var(--blue),
                var(--blue-dark)
            );
        color: #ffffff;
    }

    .cancel-booking-button {
        margin-top: 6px;
        border: 1px solid #fecaca;
        background: #ffffff;
        color: #b91c1c;
        cursor: pointer;
    }


    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    .history-section {
        margin-top: 13px;
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #ffffff;
    }

    .history-heading h2 {
        color: var(--navy);
        font-size: 12px;
        font-weight: 850;
    }

    .history-heading p {
        margin-top: 3px;
        color: #64748b;
        font-size: 7px;
    }

    .history-list {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 11px;
    }

    .history-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #f8fafc;
        text-decoration: none;
    }

    .history-card.current {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .history-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #ffffff;
        color: #0284c7;
        font-size: 11px;
        font-weight: 900;
    }

    .history-content span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 850;
    }

    .history-content strong {
        display: block;
        margin-top: 1px;
        color: #334155;
        font-size: 8px;
        font-weight: 850;
    }

    .history-content small {
        display: block;
        margin-top: 2px;
        color: #94a3b8;
        font-size: 6px;
    }

    .history-arrow {
        color: #0284c7;
        font-size: 11px;
        font-weight: 900;
    }


    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(3px);
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-card {
        position: relative;
        width: min(460px, 100%);
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 20px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow:
            0 25px 70px rgba(15, 23, 42, .2);
    }

    .modal-close {
        position: absolute;
        top: 11px;
        right: 11px;
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        font-family: inherit;
        cursor: pointer;
    }

    .modal-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        border-radius: 50%;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 15px;
        font-weight: 900;
    }

    .modal-eyebrow {
        display: block;
        color: #dc2626;
        font-size: 6px;
        font-weight: 850;
        letter-spacing: .6px;
    }

    .modal-card h2 {
        margin-top: 2px;
        color: var(--navy);
        font-size: 15px;
        font-weight: 900;
    }

    .modal-description {
        margin-top: 5px;
        color: #64748b;
        font-size: 8px;
        line-height: 1.6;
    }

    .cancel-summary {
        margin-top: 12px;
        padding: 10px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .cancel-summary > div {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 6px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .cancel-summary > div:last-child {
        border-bottom: 0;
    }

    .cancel-summary span {
        color: #94a3b8;
        font-size: 6px;
    }

    .cancel-summary strong {
        color: #334155;
        font-size: 7px;
        font-weight: 800;
        text-align: right;
    }

    .form-group {
        margin-top: 12px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #334155;
        font-size: 7px;
        font-weight: 800;
    }

    .form-group label span {
        color: #dc2626;
    }

    .form-group textarea {
        width: 100%;
        min-height: 90px;
        resize: vertical;
        padding: 9px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        background: #ffffff;
        color: #334155;
        font-family: inherit;
        font-size: 8px;
    }

    .form-group textarea:focus {
        border-color: #38bdf8;
        box-shadow:
            0 0 0 3px rgba(56, 189, 248, .1);
    }

    .field-error {
        display: block;
        margin-top: 4px;
        color: #dc2626;
        font-size: 6px;
    }

    .modal-notice {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin-top: 9px;
        padding: 8px;
        border-radius: 8px;
        background: #f0f9ff;
        color: #0369a1;
    }

    .modal-notice p {
        font-size: 6px;
        line-height: 1.5;
    }

    .modal-actions {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 7px;
        margin-top: 12px;
    }

    .modal-back-button,
    .confirm-cancel-button {
        min-height: 36px;
        border-radius: 7px;
        font-family: inherit;
        font-size: 7px;
        font-weight: 850;
        cursor: pointer;
    }

    .modal-back-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    .confirm-cancel-button {
        border: 0;
        background: #dc2626;
        color: #ffffff;
    }

    .confirm-cancel-button:disabled {
        opacity: .65;
        cursor: not-allowed;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 900px) {

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .side-column {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .action-card {
            grid-column: 1 / -1;
        }

    }


    @media (max-width: 650px) {

        .page-header,
        .booking-code-card,
        .location-box {
            align-items: flex-start;
            flex-direction: column;
        }

        .booking-created {
            text-align: left;
        }

        .reschedule-alert {
            grid-template-columns: auto 1fr;
        }

        .relation-button {
            grid-column: 1 / -1;
        }

        .information-grid,
        .cancellation-grid,
        .history-list,
        .side-column {
            grid-template-columns: 1fr;
        }

    }


    @media (max-width: 450px) {

        .session-statistics {
            grid-template-columns: 1fr;
        }

        .modal-actions {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Copy Booking Code
    |--------------------------------------------------------------------------
    */

    const copyButton =
        document.getElementById(
            'copyBookingCode'
        );

    const bookingCode =
        document.getElementById(
            'bookingCode'
        );


    if (copyButton && bookingCode) {

        copyButton.addEventListener(
            'click',
            async function () {

                const code =
                    bookingCode.textContent.trim();

                try {

                    await navigator.clipboard
                        .writeText(code);

                    const oldText =
                        copyButton.textContent;

                    copyButton.textContent =
                        'Tersalin ✓';

                    setTimeout(
                        function () {
                            copyButton.textContent =
                                oldText;
                        },
                        1500
                    );

                } catch (error) {

                    const temporaryInput =
                        document.createElement(
                            'textarea'
                        );

                    temporaryInput.value =
                        code;

                    document.body.appendChild(
                        temporaryInput
                    );

                    temporaryInput.select();

                    document.execCommand(
                        'copy'
                    );

                    temporaryInput.remove();

                    copyButton.textContent =
                        'Tersalin ✓';

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Modal
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById(
            'cancelModal'
        );

    const openModalButton =
        document.getElementById(
            'openCancelModal'
        );

    const closeModalButton =
        document.getElementById(
            'closeCancelModal'
        );

    const modalBackButton =
        document.getElementById(
            'cancelModalBack'
        );

    const cancelForm =
        document.getElementById(
            'cancelBookingForm'
        );

    const confirmCancelButton =
        document.getElementById(
            'confirmCancelButton'
        );


    function openModal() {

        if (!modal) {
            return;
        }

        modal.classList.add(
            'active'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';

    }


    function closeModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'active'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow =
            '';

    }


    if (openModalButton) {

        openModalButton.addEventListener(
            'click',
            openModal
        );

    }


    if (closeModalButton) {

        closeModalButton.addEventListener(
            'click',
            closeModal
        );

    }


    if (modalBackButton) {

        modalBackButton.addEventListener(
            'click',
            closeModal
        );

    }


    if (modal) {

        modal.addEventListener(
            'click',
            function (event) {

                if (event.target === modal) {
                    closeModal();
                }

            }
        );

    }


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
                &&
                modal
                &&
                modal.classList.contains(
                    'active'
                )
            ) {
                closeModal();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Prevent Double Submit
    |--------------------------------------------------------------------------
    */

    if (cancelForm) {

        cancelForm.addEventListener(
            'submit',
            function () {

                if (confirmCancelButton) {

                    confirmCancelButton.disabled =
                        true;

                    confirmCancelButton.textContent =
                        'Membatalkan...';

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Reopen modal when validation fails
    |--------------------------------------------------------------------------
    */

    @if ($errors->has('cancellation_reason'))

        openModal();

    @endif

});

</script>

@endpush
