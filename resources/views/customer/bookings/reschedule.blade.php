@extends('layouts.customer')

@section('title', 'Reschedule Booking')
@section('topbar-title', 'Reschedule Booking')

@section('content')

@php
    $oldSchedule = $booking->classSchedule;
    $participant = $booking->participant;
    $participantPackage = $booking->participantPackage;
    $package = $participantPackage?->package;
    $program = $package?->program ?? $oldSchedule?->program;
@endphp

<div class="reschedule-page">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="page-header">

        <div>

            <a
                href="{{ route('customer.bookings.show', $booking) }}"
                class="back-link"
            >
                ← Kembali ke Detail Booking
            </a>

            <span class="page-eyebrow">
                UBAH JADWAL
            </span>

            <h1>
                Reschedule Booking
            </h1>

            <p>
                Pilih jadwal baru untuk peserta.
                Jadwal sebelumnya akan disimpan sebagai riwayat.
            </p>

        </div>

    </div>


    {{-- =========================================================
        ERROR
    ========================================================== --}}

    @if ($errors->any())

        <div class="error-alert">

            <div class="error-icon">
                !
            </div>

            <div>

                <strong>
                    Reschedule belum dapat diproses
                </strong>

                @foreach ($errors->all() as $error)
                    <p>
                        {{ $error }}
                    </p>
                @endforeach

            </div>

        </div>

    @endif


    {{-- =========================================================
        INFORMATION
    ========================================================== --}}

    <div class="information-grid">

        <section class="information-card">

            <span class="information-label">
                PESERTA
            </span>

            <div class="participant-row">

                <div class="participant-avatar">
                    {{
                        strtoupper(
                            mb_substr(
                                $participant->full_name,
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
                        {{ $participant->full_name }}
                    </strong>

                </div>

            </div>

        </section>


        <section class="information-card">

            <span class="information-label">
                PAKET
            </span>

            <h3>
                {{ $package?->name ?? 'Paket Renang' }}
            </h3>

            <p>
                {{ $program?->name ?? 'Program Renang' }}
            </p>

            <div class="package-session">

                <span>
                    Sisa Sesi
                </span>

                <strong>
                    {{ $participantPackage?->remaining_sessions ?? 0 }}
                </strong>

            </div>

        </section>


        <section class="information-card">

            <span class="information-label">
                KODE BOOKING
            </span>

            <h3 class="booking-code">
                {{ $booking->booking_code }}
            </h3>

            <p>
                Booking yang akan dijadwalkan ulang.
            </p>

        </section>

    </div>


    {{-- =========================================================
        CURRENT SCHEDULE
    ========================================================== --}}

    <section class="current-schedule-card">

        <div class="current-heading">

            <div class="current-icon">
                📅
            </div>

            <div>
                <span>
                    JADWAL SAAT INI
                </span>

                <h2>
                    {{
                        $oldSchedule?->title
                        ?: ($program?->name ?? 'Kelas Renang')
                    }}
                </h2>
            </div>

        </div>


        <div class="current-details">

            <div>

                <span>
                    Tanggal
                </span>

                <strong>
                    {{
                        $oldSchedule?->class_date
                            ?->translatedFormat('d F Y')
                        ?? '-'
                    }}
                </strong>

            </div>


            <div>

                <span>
                    Waktu
                </span>

                <strong>
                    @if ($oldSchedule)

                        {{
                            substr(
                                $oldSchedule->start_time,
                                0,
                                5
                            )
                        }}
                        -
                        {{
                            substr(
                                $oldSchedule->end_time,
                                0,
                                5
                            )
                        }}
                        WIB

                    @else

                        -

                    @endif
                </strong>

            </div>


            <div>

                <span>
                    Pelatih
                </span>

                <strong>
                    {{
                        $oldSchedule?->coach?->name
                        ?? 'Belum ditentukan'
                    }}
                </strong>

            </div>


            <div>

                <span>
                    Lokasi
                </span>

                <strong>
                    {{
                        $oldSchedule?->location?->name
                        ?? 'Belum ditentukan'
                    }}
                </strong>

            </div>

        </div>


        <div class="current-notice">

            <span>ℹ️</span>

            <p>
                Setelah reschedule berhasil, booking ini
                akan berstatus
                <strong>Dijadwalkan Ulang</strong>
                dan sistem akan membuat kode booking baru.
            </p>

        </div>

    </section>


    {{-- =========================================================
        NEW SCHEDULE
    ========================================================== --}}

    <section class="schedule-section">

        <div class="section-heading">

            <div>

                <span class="page-eyebrow">
                    JADWAL BARU
                </span>

                <h2>
                    Pilih Jadwal Pengganti
                </h2>

                <p>
                    Hanya jadwal dari program yang sama dengan
                    paket peserta yang ditampilkan.
                </p>

            </div>


            <div class="schedule-counter">

                <span>
                    Tersedia
                </span>

                <strong>
                    {{ $schedules->count() }}
                </strong>

                <small>
                    jadwal
                </small>

            </div>

        </div>


        @if ($schedules->isEmpty())

            <div class="empty-state">

                <div class="empty-icon">
                    🗓️
                </div>

                <h3>
                    Belum Ada Jadwal Pengganti
                </h3>

                <p>
                    Saat ini belum tersedia jadwal lain untuk
                    program
                    <strong>
                        {{ $program?->name ?? 'ini' }}
                    </strong>.
                    Silakan kembali dan periksa lagi setelah
                    jadwal baru tersedia.
                </p>


                <a
                    href="{{ route('customer.bookings.show', $booking) }}"
                    class="back-detail-button"
                >
                    Kembali ke Detail Booking
                </a>

            </div>

        @else

            <form
                action="{{ route(
                    'customer.bookings.reschedule.process',
                    $booking
                ) }}"
                method="POST"
                id="rescheduleForm"
            >

                @csrf


                <input
                    type="hidden"
                    name="class_schedule_id"
                    id="selectedScheduleId"
                    value="{{ old('class_schedule_id') }}"
                >


                <div class="schedule-grid">

                    @foreach ($schedules as $schedule)

                        @php
                            $remainingSlots = max(
                                0,
                                (int) $schedule->capacity
                                -
                                (int) $schedule->active_bookings_count
                                - (int) ($schedule->active_offers_count ?? 0)
                            );

                            $isFull =
                                $remainingSlots <= 0;

                            $isOldSelected =
                                (string) old(
                                    'class_schedule_id'
                                )
                                ===
                                (string) $schedule->id;

                            $levelLabel = match (
                                $schedule->swimming_level
                            ) {
                                'all' => 'Semua Level',
                                'beginner' => 'Beginner',
                                'basic' => 'Basic',
                                'intermediate' => 'Intermediate',
                                'advanced' => 'Advanced',
                                default => ucfirst(
                                    $schedule->swimming_level
                                ),
                            };
                        @endphp


                        <button
                            type="button"
                            class="
                                schedule-card
                                {{ $isFull ? 'full' : '' }}
                                {{ $isOldSelected ? 'selected' : '' }}
                            "
                            data-schedule-card
                            data-schedule-id="{{ $schedule->id }}"
                            data-schedule-title="{{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}"
                            data-schedule-date="{{ $schedule->class_date->translatedFormat('d F Y') }}"
                            data-schedule-time="{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }} WIB"
                            data-schedule-location="{{ $schedule->location?->name ?? 'Belum ditentukan' }}"
                            data-schedule-coach="{{ $schedule->coach?->name ?? 'Belum ditentukan' }}"
                            {{ $isFull ? 'disabled' : '' }}
                        >

                            <div class="selected-check">
                                ✓
                            </div>


                            <div class="schedule-top">

                                <div>

                                    <span class="schedule-program">
                                        {{
                                            $schedule
                                                ->program
                                                ?->name
                                            ?? 'Program Renang'
                                        }}
                                    </span>

                                    <h3>
                                        {{
                                            $schedule->title
                                            ?: (
                                                $schedule
                                                    ->program
                                                    ?->name
                                                ?? 'Kelas Renang'
                                            )
                                        }}
                                    </h3>

                                </div>


                                @if ($isFull)

                                    <span class="slot-badge full">
                                        Penuh
                                    </span>

                                @else

                                    <span class="slot-badge available">
                                        {{ $remainingSlots }}
                                        Slot
                                    </span>

                                @endif

                            </div>


                            <div class="schedule-date-row">

                                <div class="date-box">

                                    <span>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat('D')
                                        }}
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->format('d')
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat('M')
                                        }}
                                    </small>

                                </div>


                                <div class="date-information">

                                    <span>
                                        Tanggal & Waktu
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat(
                                                    'd F Y'
                                                )
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            substr(
                                                $schedule->start_time,
                                                0,
                                                5
                                            )
                                        }}
                                        -
                                        {{
                                            substr(
                                                $schedule->end_time,
                                                0,
                                                5
                                            )
                                        }}
                                        WIB
                                    </small>

                                </div>

                            </div>


                            <div class="schedule-information">

                                <div>

                                    <span>
                                        🧑‍🏫 Pelatih
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->coach
                                                ?->name
                                            ?? 'Belum ditentukan'
                                        }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        📍 Lokasi
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->location
                                                ?->name
                                            ?? 'Belum ditentukan'
                                        }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        🏊 Level
                                    </span>

                                    <strong>
                                        {{ $levelLabel }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        👥 Kapasitas
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->active_bookings_count
                                        }}
                                        /
                                        {{ $schedule->capacity }}
                                    </strong>

                                </div>

                            </div>


                            @if ($schedule->notes)

                                <div class="schedule-note">
                                    {{ $schedule->notes }}
                                </div>

                            @endif


                            <div class="select-label">

                                @if ($isFull)

                                    Jadwal Penuh

                                @else

                                    Pilih Jadwal Ini

                                @endif

                            </div>

                        </button>

                    @endforeach

                </div>


                {{-- =================================================
                    SELECTED SUMMARY
                ================================================== --}}

                <div
                    class="selected-summary"
                    id="selectedSummary"
                    @if (!old('class_schedule_id'))
                        hidden
                    @endif
                >

                    <div class="selected-summary-heading">

                        <div>

                            <span>
                                JADWAL BARU TERPILIH
                            </span>

                            <h3 id="summaryTitle">
                                -
                            </h3>

                        </div>


                        <span class="ready-badge">
                            Siap Reschedule
                        </span>

                    </div>


                    <div class="summary-grid">

                        <div>
                            <span>Tanggal</span>

                            <strong id="summaryDate">
                                -
                            </strong>
                        </div>


                        <div>
                            <span>Waktu</span>

                            <strong id="summaryTime">
                                -
                            </strong>
                        </div>


                        <div>
                            <span>Pelatih</span>

                            <strong id="summaryCoach">
                                -
                            </strong>
                        </div>


                        <div>
                            <span>Lokasi</span>

                            <strong id="summaryLocation">
                                -
                            </strong>
                        </div>

                    </div>


                    <div class="change-preview">

                        <div class="preview-schedule old">

                            <span>
                                Jadwal Lama
                            </span>

                            <strong>
                                {{
                                    $oldSchedule?->class_date
                                        ?->translatedFormat(
                                            'd M Y'
                                        )
                                    ?? '-'
                                }}
                            </strong>

                            <small>
                                @if ($oldSchedule)

                                    {{
                                        substr(
                                            $oldSchedule->start_time,
                                            0,
                                            5
                                        )
                                    }}
                                    WIB

                                @else

                                    -

                                @endif
                            </small>

                        </div>


                        <div class="preview-arrow">
                            →
                        </div>


                        <div class="preview-schedule new">

                            <span>
                                Jadwal Baru
                            </span>

                            <strong id="previewNewDate">
                                -
                            </strong>

                            <small id="previewNewTime">
                                -
                            </small>

                        </div>

                    </div>


                    <div class="important-notice">

                        <span>
                            ℹ️
                        </span>

                        <p>
                            Reschedule tidak mengurangi sesi paket.
                            Setelah dikonfirmasi, sistem membuat
                            booking baru dan menyimpan booking lama
                            sebagai riwayat.
                        </p>

                    </div>


                    <div class="form-actions">

                        <a
                            href="{{ route(
                                'customer.bookings.show',
                                $booking
                            ) }}"
                            class="cancel-reschedule-button"
                        >
                            Batal
                        </a>


                        <button
                            type="submit"
                            class="confirm-reschedule-button"
                            id="confirmRescheduleButton"
                        >
                            Konfirmasi Reschedule
                        </button>

                    </div>

                </div>

            </form>

        @endif

    </section>

</div>

@endsection


@push('styles')

<style>

    .reschedule-page {
        width: 100%;
    }

    .page-header {
        margin-bottom: 18px;
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 13px;
        color: #0284c7;
        font-size: 8px;
        font-weight: 800;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .page-eyebrow,
    .information-label {
        display: block;
        margin-bottom: 4px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        letter-spacing: .6px;
    }

    .page-header h1 {
        color: var(--navy);
        font-size: 24px;
        font-weight: 900;
    }

    .page-header p {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    */

    .error-alert {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-bottom: 15px;
        padding: 12px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #b91c1c;
    }

    .error-icon {
        width: 27px;
        height: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        font-size: 10px;
        font-weight: 900;
    }

    .error-alert strong {
        display: block;
        font-size: 9px;
        font-weight: 850;
    }

    .error-alert p {
        margin-top: 2px;
        font-size: 8px;
    }


    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    */

    .information-grid {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
        gap: 11px;
        margin-bottom: 14px;
    }

    .information-card {
        min-width: 0;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #ffffff;
    }

    .information-card h3 {
        overflow-wrap: anywhere;
        color: var(--navy);
        font-size: 10px;
        font-weight: 850;
    }

    .information-card > p {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 7px;
    }

    .participant-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .participant-avatar {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 11px;
        font-weight: 900;
    }

    .participant-row span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
    }

    .participant-row strong {
        display: block;
        margin-top: 2px;
        color: var(--navy);
        font-size: 9px;
        font-weight: 850;
    }

    .package-session {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 9px;
        padding: 7px 9px;
        border-radius: 8px;
        background: #f0f9ff;
    }

    .package-session span {
        color: #0369a1;
        font-size: 7px;
    }

    .package-session strong {
        color: #0284c7;
        font-size: 14px;
        font-weight: 900;
    }

    .booking-code {
        color: #0369a1 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | Current Schedule
    |--------------------------------------------------------------------------
    */

    .current-schedule-card {
        margin-bottom: 16px;
        padding: 16px;
        border: 1px solid #fde68a;
        border-radius: 12px;
        background: #fffbeb;
    }

    .current-heading {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .current-icon {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #ffffff;
        font-size: 14px;
    }

    .current-heading span {
        display: block;
        color: #a16207;
        font-size: 7px;
        font-weight: 850;
    }

    .current-heading h2 {
        margin-top: 2px;
        color: #78350f;
        font-size: 12px;
        font-weight: 850;
    }

    .current-details {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 7px;
        margin-top: 12px;
    }

    .current-details > div {
        padding: 9px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .72);
    }

    .current-details span {
        display: block;
        color: #a16207;
        font-size: 7px;
    }

    .current-details strong {
        display: block;
        margin-top: 2px;
        color: #78350f;
        font-size: 8px;
        font-weight: 800;
    }

    .current-notice {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #fde68a;
    }

    .current-notice p {
        color: #92400e;
        font-size: 7px;
        line-height: 1.55;
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule Section
    |--------------------------------------------------------------------------
    */

    .schedule-section {
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #ffffff;
    }

    .section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
    }

    .section-heading h2 {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .section-heading p {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 8px;
    }

    .schedule-counter {
        min-width: 75px;
        padding: 8px;
        border-radius: 9px;
        background: #f0f9ff;
        text-align: center;
    }

    .schedule-counter span,
    .schedule-counter small {
        display: block;
        color: #0284c7;
        font-size: 6px;
    }

    .schedule-counter strong {
        display: block;
        color: #0369a1;
        font-size: 17px;
        font-weight: 900;
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule Cards
    |--------------------------------------------------------------------------
    */

    .schedule-grid {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .schedule-card {
        position: relative;
        width: 100%;
        padding: 13px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #ffffff;
        font-family: inherit;
        text-align: left;
        cursor: pointer;
        transition: .2s ease;
    }

    .schedule-card:not(.full):hover {
        border-color: #7dd3fc;
        transform: translateY(-1px);
        box-shadow:
            0 7px 18px rgba(2, 132, 199, .06);
    }

    .schedule-card.selected {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow:
            0 0 0 2px rgba(2, 132, 199, .08);
    }

    .schedule-card.full {
        opacity: .62;
        cursor: not-allowed;
    }

    .selected-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 19px;
        height: 19px;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #0284c7;
        color: #ffffff;
        font-size: 7px;
        font-weight: 900;
    }

    .schedule-card.selected .selected-check {
        display: flex;
    }

    .schedule-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        padding-right: 15px;
    }

    .schedule-program {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .schedule-top h3 {
        margin-top: 2px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 850;
    }

    .slot-badge {
        padding: 4px 7px;
        flex-shrink: 0;
        border-radius: 999px;
        font-size: 6px;
        font-weight: 850;
    }

    .slot-badge.available {
        background: #dcfce7;
        color: #15803d;
    }

    .slot-badge.full {
        background: #fee2e2;
        color: #b91c1c;
    }

    .schedule-date-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 11px;
        padding: 9px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .date-box {
        width: 42px;
        flex-shrink: 0;
        padding: 5px;
        border-radius: 7px;
        background: #ffffff;
        text-align: center;
    }

    .date-box span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 800;
    }

    .date-box strong {
        display: block;
        color: var(--navy);
        font-size: 16px;
        line-height: 1.1;
        font-weight: 900;
    }

    .date-box small {
        display: block;
        color: var(--text-soft);
        font-size: 6px;
    }

    .date-information span {
        display: block;
        color: var(--text-light);
        font-size: 6px;
    }

    .date-information strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .date-information small {
        display: block;
        margin-top: 2px;
        color: #0284c7;
        font-size: 7px;
    }

    .schedule-information {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 6px;
        margin-top: 8px;
    }

    .schedule-information > div {
        min-width: 0;
        padding: 7px;
        border-radius: 7px;
        background: #f8fafc;
    }

    .schedule-information span {
        display: block;
        color: var(--text-light);
        font-size: 6px;
    }

    .schedule-information strong {
        display: block;
        margin-top: 2px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 7px;
        font-weight: 800;
    }

    .schedule-note {
        margin-top: 8px;
        padding: 7px;
        border-radius: 7px;
        background: #fffbeb;
        color: #92400e;
        font-size: 6px;
        line-height: 1.5;
    }

    .select-label {
        margin-top: 9px;
        padding: 8px;
        border-radius: 7px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 7px;
        font-weight: 850;
        text-align: center;
    }

    .schedule-card.selected .select-label {
        background: #0284c7;
        color: #ffffff;
    }

    .schedule-card.full .select-label {
        background: #f1f5f9;
        color: #94a3b8;
    }


    /*
    |--------------------------------------------------------------------------
    | Selected Summary
    |--------------------------------------------------------------------------
    */

    .selected-summary {
        margin-top: 16px;
        padding: 15px;
        border: 1px solid #7dd3fc;
        border-radius: 11px;
        background:
            linear-gradient(
                135deg,
                #f0f9ff,
                #ecfeff
            );
    }

    .selected-summary-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .selected-summary-heading span:first-child {
        display: block;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
    }

    .selected-summary-heading h3 {
        margin-top: 2px;
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }

    .ready-badge {
        padding: 5px 8px;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d !important;
        font-size: 6px !important;
        font-weight: 850;
    }

    .summary-grid {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 7px;
        margin-top: 11px;
    }

    .summary-grid > div {
        padding: 8px;
        border-radius: 7px;
        background: rgba(255, 255, 255, .78);
    }

    .summary-grid span {
        display: block;
        color: var(--text-light);
        font-size: 6px;
    }

    .summary-grid strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 7px;
        font-weight: 800;
    }

    .change-preview {
        display: grid;
        grid-template-columns:
            1fr auto 1fr;
        align-items: center;
        gap: 10px;
        margin-top: 11px;
    }

    .preview-schedule {
        padding: 9px;
        border-radius: 8px;
    }

    .preview-schedule.old {
        background: #fff7ed;
    }

    .preview-schedule.new {
        background: #ecfdf5;
    }

    .preview-schedule span {
        display: block;
        font-size: 6px;
        font-weight: 800;
    }

    .preview-schedule.old span {
        color: #c2410c;
    }

    .preview-schedule.new span {
        color: #15803d;
    }

    .preview-schedule strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 8px;
        font-weight: 850;
    }

    .preview-schedule small {
        display: block;
        margin-top: 1px;
        color: var(--text-soft);
        font-size: 6px;
    }

    .preview-arrow {
        color: #0284c7;
        font-size: 17px;
        font-weight: 900;
    }

    .important-notice {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 11px;
        padding: 8px;
        border-radius: 8px;
        background: #ffffff;
        color: #0369a1;
    }

    .important-notice p {
        font-size: 7px;
        line-height: 1.55;
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 8px;
        margin-top: 12px;
    }

    .cancel-reschedule-button,
    .confirm-reschedule-button {
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 8px;
        font-weight: 850;
    }

    .cancel-reschedule-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        text-decoration: none;
    }

    .confirm-reschedule-button {
        border: 0;
        background:
            linear-gradient(
                135deg,
                var(--blue),
                var(--blue-dark)
            );
        color: #ffffff;
        cursor: pointer;
    }

    .confirm-reschedule-button:disabled {
        opacity: .65;
        cursor: not-allowed;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 32px 20px;
        border: 1px dashed #cbd5e1;
        border-radius: 11px;
        background: #f8fafc;
        text-align: center;
    }

    .empty-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        border-radius: 11px;
        background: #ffffff;
        font-size: 18px;
    }

    .empty-state h3 {
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }

    .empty-state p {
        max-width: 450px;
        margin: 4px auto 0;
        color: var(--text-soft);
        font-size: 8px;
        line-height: 1.6;
    }

    .back-detail-button {
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 13px;
        padding: 8px 13px;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 8px;
        font-weight: 850;
        text-decoration: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1050px) {

        .schedule-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .current-details,
        .summary-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

    }


    @media (max-width: 750px) {

        .information-grid {
            grid-template-columns: 1fr;
        }

        .schedule-grid {
            grid-template-columns: 1fr;
        }

        .section-heading {
            align-items: flex-start;
        }

    }


    @media (max-width: 550px) {

        .current-details,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .schedule-information {
            grid-template-columns: 1fr;
        }

        .change-preview {
            grid-template-columns: 1fr;
        }

        .preview-arrow {
            transform: rotate(90deg);
            text-align: center;
        }

        .form-actions {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const scheduleCards =
        document.querySelectorAll(
            '[data-schedule-card]'
        );

    const selectedScheduleInput =
        document.getElementById(
            'selectedScheduleId'
        );

    const selectedSummary =
        document.getElementById(
            'selectedSummary'
        );

    const rescheduleForm =
        document.getElementById(
            'rescheduleForm'
        );

    const confirmButton =
        document.getElementById(
            'confirmRescheduleButton'
        );


    /*
    |--------------------------------------------------------------------------
    | Fill Summary
    |--------------------------------------------------------------------------
    */

    function fillSummary(card) {

        if (!card) {
            return;
        }


        const scheduleId =
            card.dataset.scheduleId;

        const title =
            card.dataset.scheduleTitle;

        const date =
            card.dataset.scheduleDate;

        const time =
            card.dataset.scheduleTime;

        const location =
            card.dataset.scheduleLocation;

        const coach =
            card.dataset.scheduleCoach;


        if (selectedScheduleInput) {
            selectedScheduleInput.value =
                scheduleId;
        }


        document
            .getElementById(
                'summaryTitle'
            )
            .textContent =
                title;

        document
            .getElementById(
                'summaryDate'
            )
            .textContent =
                date;

        document
            .getElementById(
                'summaryTime'
            )
            .textContent =
                time;

        document
            .getElementById(
                'summaryCoach'
            )
            .textContent =
                coach;

        document
            .getElementById(
                'summaryLocation'
            )
            .textContent =
                location;

        document
            .getElementById(
                'previewNewDate'
            )
            .textContent =
                date;

        document
            .getElementById(
                'previewNewTime'
            )
            .textContent =
                time;


        if (selectedSummary) {

            selectedSummary.hidden =
                false;

            selectedSummary.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Select Schedule
    |--------------------------------------------------------------------------
    */

    scheduleCards.forEach(function (card) {

        card.addEventListener(
            'click',
            function () {

                if (
                    card.disabled
                    ||
                    card.classList.contains(
                        'full'
                    )
                ) {
                    return;
                }


                scheduleCards.forEach(
                    function (item) {

                        item.classList.remove(
                            'selected'
                        );

                    }
                );


                card.classList.add(
                    'selected'
                );


                fillSummary(card);

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Restore old selection after validation error
    |--------------------------------------------------------------------------
    */

    const previouslySelected =
        document.querySelector(
            '[data-schedule-card].selected'
        );


    if (previouslySelected) {

        fillSummary(
            previouslySelected
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Submit Protection
    |--------------------------------------------------------------------------
    */

    if (rescheduleForm) {

        rescheduleForm.addEventListener(
            'submit',
            function (event) {

                if (
                    !selectedScheduleInput
                    ||
                    !selectedScheduleInput.value
                ) {

                    event.preventDefault();

                    alert(
                        'Silakan pilih jadwal baru terlebih dahulu.'
                    );

                    return;
                }


                if (confirmButton) {

                    confirmButton.disabled =
                        true;

                    confirmButton.textContent =
                        'Memproses Reschedule...';

                }

            }
        );

    }

});

</script>

@endpush
