@extends('layouts.customer')

@section('title', 'Paket Saya')
@section('topbar-title', 'Paket Saya')

@section('content')

<div class="package-page">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Paket Saya
            </h1>

            <p class="page-description">
                Lihat paket kelas, sisa sesi, dan masa aktif
                untuk setiap peserta.
            </p>

        </div>


        <a
            href="{{ route('customer.packages.browse') }}"
            class="btn-primary"
        >
            <span class="button-plus">+</span>
            Ambil Paket Baru
        </a>

    </div>


    {{-- =========================================================
        CUSTOMER BELUM MEMILIKI PESERTA
    ========================================================== --}}

    @if ($participants->isEmpty())

        <section class="card empty-state">

            <div class="empty-icon">
                👤
            </div>

            <h2>
                Tambahkan Peserta Terlebih Dahulu
            </h2>

            <p>
                Sebelum mengambil paket kelas, tambahkan profil
                orang yang akan mengikuti program berenang.
            </p>

            <a
                href="{{ route('customer.participants.create') }}"
                class="btn-primary"
            >
                + Tambah Peserta
            </a>

        </section>


    {{-- =========================================================
        BELUM MEMILIKI PAKET
    ========================================================== --}}

    @elseif ($participantPackages->isEmpty())

        <section class="card empty-state">

            <div class="empty-icon">
                🏊
            </div>

            <h2>
                Belum Memiliki Paket
            </h2>

            <p>
                Kamu sudah memiliki peserta.
                Sekarang pilih program dan paket berenang
                yang sesuai dengan kebutuhan peserta.
            </p>


            <div class="empty-participants">

                @foreach ($participants as $participant)

                    <div class="mini-participant">

                        <div class="mini-avatar">
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

                            <strong>
                                {{ $participant->full_name }}
                            </strong>

                            <span>
                                @if ($participant->participant_type === 'child')
                                    Anak
                                @else
                                    Diri Sendiri
                                @endif
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>


            <a
                href="{{ route('customer.packages.browse') }}"
                class="btn-primary"
            >
                Lihat Program & Paket
            </a>

        </section>


    @else

        {{-- =====================================================
            RINGKASAN PAKET
        ====================================================== --}}

        <div class="package-summary">

            <div class="summary-card">

                <div class="summary-icon">
                    📦
                </div>

                <div>

                    <div class="summary-label">
                        Paket Aktif
                    </div>

                    <div class="summary-value">
                        {{ $totalActivePackages }}
                    </div>

                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon">
                    🏊
                </div>

                <div>

                    <div class="summary-label">
                        Sisa Sesi
                    </div>

                    <div class="summary-value">
                        {{ $totalRemainingSessions }}
                    </div>

                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon">
                    ✓
                </div>

                <div>

                    <div class="summary-label">
                        Sesi Terpakai
                    </div>

                    <div class="summary-value">
                        {{ $totalUsedSessions }}
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            PAKET AKTIF
        ====================================================== --}}

        @if ($activePackages->isNotEmpty())

            <section class="package-section">

                <div class="section-title-row">

                    <div>

                        <h2>
                            Paket Aktif
                        </h2>

                        <p>
                            Paket berstatus aktif. Booking tersedia jika
                            peserta aktif, sesi tersisa, dan masa berlaku mencukupi.
                        </p>

                    </div>

                </div>


                <div class="package-grid">

                    @foreach ($activePackages as $participantPackage)

                        @php

                            $totalSessions =
                                (int) $participantPackage->total_sessions;

                            $remainingSessions =
                                (int) $participantPackage->remaining_sessions;

                            $usedSessions =
                                (int) $participantPackage->used_sessions;

                            $percentage = $totalSessions > 0
                                ? min(
                                    100,
                                    round(
                                        ($usedSessions / $totalSessions)
                                        * 100
                                    )
                                )
                                : 0;

                        @endphp


                        <article class="package-card">

                            <div class="package-card-header">

                                <div class="package-program">

                                    <span class="program-label">
                                        {{
                                            $participantPackage
                                                ->package
                                                ?->program
                                                ?->name
                                            ?? 'Program Renang'
                                        }}
                                    </span>

                                    <h3>
                                        {{
                                            $participantPackage
                                                ->package
                                                ?->name
                                            ?? 'Paket Kelas'
                                        }}
                                    </h3>

                                </div>


                                <span class="package-status active">
                                    Aktif
                                </span>

                            </div>


                            <div class="participant-box">

                                <div class="participant-avatar">

                                    {{
                                        strtoupper(
                                            mb_substr(
                                                $participantPackage
                                                    ->participant
                                                    ->full_name,
                                                0,
                                                1
                                            )
                                        )
                                    }}

                                </div>

                                <div>

                                    <span>
                                        Peserta
                                    </span>

                                    <strong>
                                        {{
                                            $participantPackage
                                                ->participant
                                                ->full_name
                                        }}
                                    </strong>

                                </div>

                            </div>


                            <div class="session-highlight">

                                <div>

                                    <span>
                                        Sisa Sesi
                                    </span>

                                    <strong>
                                        {{ $remainingSessions }}
                                    </strong>

                                </div>

                                <div class="session-total">
                                    dari {{ $totalSessions }} sesi
                                </div>

                            </div>


                            <div class="progress-wrapper">

                                <div class="progress-info">

                                    <span>
                                        Pemakaian Paket
                                    </span>

                                    <span>
                                        {{ $percentage }}%
                                    </span>

                                </div>

                                <div class="progress-track">

                                    <div
                                        class="progress-bar"
                                        style="width: {{ $percentage }}%;"
                                    ></div>

                                </div>

                            </div>


                            <div class="package-information">

                                <div class="information-item">

                                    <span class="information-label">
                                        Mulai
                                    </span>

                                    <strong>
                                        {{
                                            $participantPackage->start_date
                                                ? $participantPackage
                                                    ->start_date
                                                    ->format('d M Y')
                                                : '-'
                                        }}
                                    </strong>

                                </div>


                                <div class="information-item">

                                    <span class="information-label">
                                        Berlaku Sampai
                                    </span>

                                    <strong>
                                        {{
                                            $participantPackage->expiry_date
                                                ? $participantPackage
                                                    ->expiry_date
                                                    ->format('d M Y')
                                                : '-'
                                        }}
                                    </strong>

                                </div>

                            </div>


                            <div class="package-actions">

                                <a
                                    href="{{ route(
                                        'customer.packages.show',
                                        $participantPackage
                                    ) }}"
                                    class="btn-detail"
                                >
                                    Lihat Detail
                                </a>


                                @if (
                                    $participantPackage->canBeUsed()
                                    && $participantPackage->participant?->status === 'active'
                                )
                                    <a
                                        href="{{ route('customer.bookings.index') }}"
                                        class="btn-detail"
                                    >
                                        Booking Kelas
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        class="btn-booking-disabled"
                                        disabled
                                        title="Paket atau peserta belum memenuhi syarat booking"
                                    >
                                        Booking Tidak Tersedia
                                    </button>
                                @endif

                            </div>

                        </article>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- =====================================================
            RIWAYAT PAKET
        ====================================================== --}}

        @if ($packageHistory->isNotEmpty())

            <section class="package-section history-section">

                <div class="section-title-row">

                    <div>

                        <h2>
                            Riwayat Paket
                        </h2>

                        <p>
                            Paket yang sudah selesai atau
                            tidak lagi aktif.
                        </p>

                    </div>

                </div>


                <div class="history-list">

                    @foreach ($packageHistory as $participantPackage)

                        <div class="history-item">

                            <div class="history-main">

                                <div class="history-icon">
                                    🏊
                                </div>

                                <div>

                                    <strong>
                                        {{
                                            $participantPackage
                                                ->package
                                                ?->name
                                            ?? 'Paket Kelas'
                                        }}
                                    </strong>

                                    <span>
                                        {{
                                            $participantPackage
                                                ->participant
                                                ->full_name
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="history-sessions">

                                <span>
                                    Sesi
                                </span>

                                <strong>
                                    {{
                                        $participantPackage
                                            ->used_sessions
                                    }}
                                    /
                                    {{
                                        $participantPackage
                                            ->total_sessions
                                    }}
                                </strong>

                            </div>


                            <div>

                                @if (
                                    $participantPackage->status
                                    === 'completed'
                                )

                                    <span class="package-status completed">
                                        Selesai
                                    </span>

                                @elseif (
                                    $participantPackage->status
                                    === 'expired'
                                )

                                    <span class="package-status expired">
                                        Kedaluwarsa
                                    </span>

                                @else

                                    <span class="package-status inactive">
                                        Tidak Aktif
                                    </span>

                                @endif

                            </div>


                            <a
                                href="{{ route(
                                    'customer.packages.show',
                                    $participantPackage
                                ) }}"
                                class="history-detail"
                            >
                                Detail →
                            </a>

                        </div>

                    @endforeach

                </div>

            </section>

        @endif

    @endif

</div>

@endsection


@push('styles')

<style>

    .package-page {
        width: 100%;
    }

    .button-plus {
        font-size: 18px;
        line-height: 1;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 55px 25px;

        text-align: center;
    }

    .empty-icon {
        width: 76px;
        height: 76px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 18px;

        border-radius: 50%;

        background:
            linear-gradient(
                135deg,
                #e0f2fe,
                #cffafe
            );

        font-size: 30px;
    }

    .empty-state h2 {
        color: var(--navy);

        font-size: 20px;
        font-weight: 850;
    }

    .empty-state > p {
        max-width: 520px;

        margin: 9px auto 20px;

        color: var(--text-soft);

        font-size: 13px;
        line-height: 1.7;
    }

    .empty-participants {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;

        gap: 9px;

        max-width: 600px;

        margin: 0 auto 22px;
    }

    .mini-participant {
        display: flex;
        align-items: center;

        gap: 9px;

        padding: 9px 13px;

        border: 1px solid var(--border);
        border-radius: 11px;

        background: #f8fafc;

        text-align: left;
    }

    .mini-avatar {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        background: #e0f2fe;
        color: #0369a1;

        font-size: 11px;
        font-weight: 850;
    }

    .mini-participant strong {
        display: block;

        color: var(--navy);

        font-size: 11px;
        font-weight: 800;
    }

    .mini-participant span {
        display: block;

        margin-top: 2px;

        color: var(--text-soft);

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    .package-summary {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 14px;

        margin-bottom: 28px;
    }

    .summary-card {
        display: flex;
        align-items: center;

        gap: 12px;

        padding: 17px;

        border: 1px solid var(--border);
        border-radius: 14px;

        background: var(--white);

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .summary-icon {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 11px;

        background: #f0f9ff;

        font-size: 17px;
    }

    .summary-label {
        color: var(--text-soft);

        font-size: 10px;
        font-weight: 650;
    }

    .summary-value {
        margin-top: 2px;

        color: var(--navy);

        font-size: 20px;
        font-weight: 850;
    }


    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    .package-section {
        margin-top: 28px;
    }

    .package-section:first-of-type {
        margin-top: 0;
    }

    .section-title-row {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;

        gap: 15px;

        margin-bottom: 14px;
    }

    .section-title-row h2 {
        color: var(--navy);

        font-size: 17px;
        font-weight: 850;
    }

    .section-title-row p {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 11px;
    }


    /*
    |--------------------------------------------------------------------------
    | Active Package Cards
    |--------------------------------------------------------------------------
    */

    .package-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 17px;
    }

    .package-card {
        min-width: 0;

        padding: 20px;

        border: 1px solid var(--border);
        border-radius: 16px;

        background: var(--white);

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .package-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 12px;

        margin-bottom: 17px;
    }

    .program-label {
        display: block;

        margin-bottom: 4px;

        color: var(--blue-dark);

        font-size: 9px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .package-program h3 {
        color: var(--navy);

        font-size: 15px;
        font-weight: 850;
    }

    .package-status {
        display: inline-flex;

        padding: 6px 9px;

        border-radius: 999px;

        font-size: 9px;
        font-weight: 800;
    }

    .package-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .package-status.completed {
        background: #e0f2fe;
        color: #0369a1;
    }

    .package-status.expired,
    .package-status.inactive {
        background: #f1f5f9;
        color: #64748b;
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

        border-radius: 10px;

        background: #f8fafc;
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
        font-weight: 850;
    }

    .participant-box span {
        display: block;

        color: var(--text-light);

        font-size: 8px;
        font-weight: 700;

        text-transform: uppercase;
    }

    .participant-box strong {
        display: block;

        margin-top: 2px;

        color: var(--navy);

        font-size: 11px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Sessions
    |--------------------------------------------------------------------------
    */

    .session-highlight {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;

        gap: 10px;

        padding: 18px 0 13px;
    }

    .session-highlight span {
        display: block;

        color: var(--text-soft);

        font-size: 9px;
        font-weight: 700;
    }

    .session-highlight strong {
        display: block;

        margin-top: 2px;

        color: var(--navy);

        font-size: 27px;
        font-weight: 900;

        line-height: 1;
    }

    .session-total {
        padding-bottom: 2px;

        color: var(--text-soft);

        font-size: 10px;
    }


    /*
    |--------------------------------------------------------------------------
    | Progress
    |--------------------------------------------------------------------------
    */

    .progress-wrapper {
        margin-bottom: 18px;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;

        margin-bottom: 6px;

        color: var(--text-soft);

        font-size: 9px;
        font-weight: 650;
    }

    .progress-track {
        width: 100%;
        height: 7px;

        overflow: hidden;

        border-radius: 999px;

        background: #e2e8f0;
    }

    .progress-bar {
        height: 100%;

        border-radius: inherit;

        background:
            linear-gradient(
                90deg,
                var(--blue),
                var(--aqua)
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Package Information
    |--------------------------------------------------------------------------
    */

    .package-information {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 10px;

        padding: 14px 0;

        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .information-item {
        min-width: 0;
    }

    .information-label {
        display: block;

        color: var(--text-light);

        font-size: 8px;
        font-weight: 700;

        text-transform: uppercase;
    }

    .information-item strong {
        display: block;

        margin-top: 3px;

        color: #334155;

        font-size: 10px;
        font-weight: 750;
    }


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .package-actions {
        display: flex;

        gap: 9px;

        padding-top: 15px;
    }

    .btn-detail,
    .btn-booking-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 38px;

        padding: 8px 13px;

        border-radius: 9px;

        font-family: inherit;
        font-size: 10px;
        font-weight: 800;

        text-decoration: none;
    }

    .btn-detail {
        border: 1px solid #bae6fd;

        background: #f0f9ff;
        color: #0369a1;
    }

    .btn-detail:hover {
        background: #e0f2fe;
    }

    .btn-booking-disabled {
        border: 0;

        background: #e2e8f0;
        color: #94a3b8;

        cursor: not-allowed;
    }


    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    .history-section {
        margin-top: 32px;
    }

    .history-list {
        overflow: hidden;

        border: 1px solid var(--border);
        border-radius: 14px;

        background: var(--white);
    }

    .history-item {
        display: grid;
        align-items: center;

        grid-template-columns:
            minmax(0, 1fr)
            auto
            auto
            auto;

        gap: 18px;

        padding: 14px 16px;

        border-bottom: 1px solid #f1f5f9;
    }

    .history-item:last-child {
        border-bottom: 0;
    }

    .history-main {
        display: flex;
        align-items: center;

        gap: 10px;

        min-width: 0;
    }

    .history-icon {
        width: 37px;
        height: 37px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: #f0f9ff;

        font-size: 15px;
    }

    .history-main strong {
        display: block;

        overflow: hidden;

        color: var(--navy);

        font-size: 11px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .history-main span {
        display: block;

        margin-top: 2px;

        color: var(--text-soft);

        font-size: 9px;
    }

    .history-sessions {
        text-align: center;
    }

    .history-sessions span {
        display: block;

        color: var(--text-light);

        font-size: 8px;

        text-transform: uppercase;
    }

    .history-sessions strong {
        display: block;

        margin-top: 2px;

        color: #334155;

        font-size: 10px;
    }

    .history-detail {
        color: var(--blue-dark);

        font-size: 10px;
        font-weight: 750;

        text-decoration: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 850px) {

        .package-grid {
            grid-template-columns: 1fr;
        }

    }


    @media (max-width: 650px) {

        .page-header .btn-primary {
            margin-top: 16px;
        }

        .package-summary {
            grid-template-columns: 1fr;
        }

        .history-item {
            grid-template-columns:
                minmax(0, 1fr)
                auto;

            gap: 12px;
        }

        .history-sessions {
            display: none;
        }

    }


    @media (max-width: 480px) {

        .empty-state {
            padding: 40px 17px;
        }

        .package-card {
            padding: 17px;
        }

        .package-information {
            grid-template-columns: 1fr;
        }

        .package-actions {
            flex-direction: column;
        }

        .btn-detail,
        .btn-booking-disabled {
            width: 100%;
        }

    }

</style>

@endpush
