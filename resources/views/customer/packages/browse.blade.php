@extends('layouts.customer')

@section('title', 'Program & Paket')
@section('topbar-title', 'Program & Paket')

@section('content')

<div class="browse-page">

    <div class="browse-header">

        <div>
            <a
                href="{{ route('customer.packages.index') }}"
                class="back-link"
            >
                ← Kembali ke Paket Saya
            </a>

            <h1 class="page-title">
                Pilih Program & Paket
            </h1>

            <p class="page-description">
                Pilih paket berenang yang sesuai dengan kebutuhan
                peserta Ragen Swim Course.
            </p>
        </div>

    </div>


    {{-- =========================================================
        TIDAK ADA PESERTA
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
                Kamu perlu memiliki minimal satu peserta aktif
                sebelum memilih dan membeli paket kelas.
            </p>

            <a
                href="{{ route('customer.participants.create') }}"
                class="btn-primary"
            >
                + Tambah Peserta
            </a>

        </section>


    {{-- =========================================================
        TIDAK ADA PAKET
    ========================================================== --}}

    @elseif ($packages->isEmpty())

        <section class="card empty-state">

            <div class="empty-icon">
                🏊
            </div>

            <h2>
                Paket Belum Tersedia
            </h2>

            <p>
                Saat ini belum ada paket kelas aktif yang tersedia.
                Silakan cek kembali setelah admin menambahkan paket.
            </p>

            <a
                href="{{ route('customer.packages.index') }}"
                class="btn-secondary"
            >
                Kembali ke Paket Saya
            </a>

        </section>


    @else

        {{-- =====================================================
            PILIH PESERTA
        ====================================================== --}}

        <section class="participant-section">

            <div class="section-heading">

                <div class="section-number">
                    1
                </div>

                <div>
                    <h2>
                        Pilih Peserta
                    </h2>

                    <p>
                        Tentukan peserta yang akan menggunakan
                        paket berenang.
                    </p>
                </div>

            </div>


            <div class="participant-selector">

                @foreach ($participants as $participant)

                    <label class="participant-option">

                        <input
                            type="radio"
                            name="selected_participant"
                            value="{{ $participant->id }}"
                            data-participant-name="{{ $participant->full_name }}"
                            @checked($loop->first)
                        >

                        <span class="participant-select-card">

                            <span class="participant-avatar">
                                {{
                                    strtoupper(
                                        mb_substr(
                                            $participant->full_name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>

                            <span class="participant-info">

                                <strong>
                                    {{ $participant->full_name }}
                                </strong>

                                <small>

                                    @if ($participant->participant_type === 'child')
                                        Anak
                                    @else
                                        Diri Sendiri
                                    @endif

                                    <span>•</span>

                                    @switch($participant->swimming_level)

                                        @case('beginner')
                                            Pemula
                                            @break

                                        @case('basic')
                                            Dasar
                                            @break

                                        @case('intermediate')
                                            Menengah
                                            @break

                                        @case('advanced')
                                            Lanjutan
                                            @break

                                        @default
                                            Level belum ditentukan

                                    @endswitch

                                </small>

                            </span>

                            <span class="participant-check">
                                ✓
                            </span>

                        </span>

                    </label>

                @endforeach

            </div>

        </section>


        {{-- =====================================================
            PILIH PAKET
        ====================================================== --}}

        <section class="packages-section">

            <div class="section-heading">

                <div class="section-number">
                    2
                </div>

                <div>
                    <h2>
                        Pilih Paket
                    </h2>

                    <p>
                        Bandingkan program, jumlah pertemuan,
                        masa berlaku, dan harga paket.
                    </p>
                </div>

            </div>


            @php
                $packagesByProgram = $packages->groupBy(
                    fn ($package) =>
                        $package->program?->name ?? 'Program Lainnya'
                );
            @endphp


            <div class="program-list">

                @foreach ($packagesByProgram as $programName => $programPackages)

                    @php
                        $firstPackage = $programPackages->first();
                        $program = $firstPackage?->program;

                        $programKey = strtolower(
                            str_replace(
                                [' ', '&'],
                                ['-', 'dan'],
                                $programName
                            )
                        );
                    @endphp


                    <section class="program-group">

                        <div class="program-heading">

                            <div class="program-icon">

                                @if (str_contains(strtolower($programName), 'private'))
                                    🏊
                                @elseif (str_contains(strtolower($programName), 'kebutuhan'))
                                    💙
                                @else
                                    👥
                                @endif

                            </div>


                            <div class="program-heading-content">

                                <div class="program-title-row">

                                    <h3>
                                        {{ $programName }}
                                    </h3>

                                    <span class="program-badge">
                                        Program
                                    </span>

                                </div>


                                @if (!empty($program?->description))

                                    <p>
                                        {{ $program->description }}
                                    </p>

                                @else

                                    <p>
                                        Program pembelajaran berenang
                                        Ragen Swim Course.
                                    </p>

                                @endif

                            </div>

                        </div>


                        <div class="package-grid">

                            @foreach ($programPackages as $package)

                                <article id="paket-{{ $package->id }}" class="package-card @if(request()->query('package') == (string) $package->id) package-card--linked @endif">

                                    <div class="package-top">

                                        <div>

                                            <span class="package-label">
                                                Paket Kelas
                                            </span>

                                            <h4>
                                                {{ $package->name }}
                                            </h4>

                                        </div>


                                        @if ($loop->first)

                                            <span class="available-badge">
                                                Tersedia
                                            </span>

                                        @endif

                                    </div>


                                    @if ($package->hasActivePromo())
                                        <div style="font-size:12px;color:#64748b;margin-top:12px;">
                                            <span style="text-decoration:line-through;">Rp{{ number_format((float) $package->price, 0, ",", ".") }}</span>
                                            <span style="color:#15803d;font-weight:700;margin-left:6px;">{{ $package->promo_name ?: "Promo Paket" }}</span>
                                        </div>
                                    @endif

                                    <div class="package-price">

                                        <strong>
                                            Rp{{ number_format(
                                                $package->getEffectivePrice(),
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>

                                        <span>
                                            / paket
                                        </span>

                                    </div>


                                    <div class="package-details">

                                        <div class="detail-item">

                                            <span class="detail-icon">
                                                🏊
                                            </span>

                                            <div>

                                                <span>
                                                    Jumlah Pertemuan
                                                </span>

                                                <strong>
                                                    {{
                                                        $package->total_sessions
                                                    }}
                                                    sesi
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="detail-item">

                                            <span class="detail-icon">
                                                📅
                                            </span>

                                            <div>

                                                <span>
                                                    Masa Berlaku
                                                </span>

                                                <strong>
                                                    {{
                                                        $package->validity_days
                                                    }}
                                                    hari
                                                </strong>

                                            </div>

                                        </div>


                                        @if (
                                            isset($package->session_duration_minutes)
                                            && $package->session_duration_minutes
                                        )

                                            <div class="detail-item">

                                                <span class="detail-icon">
                                                    ⏱
                                                </span>

                                                <div>

                                                    <span>
                                                        Durasi Kelas
                                                    </span>

                                                    <strong>
                                                        {{
                                                            $package
                                                                ->session_duration_minutes
                                                        }}
                                                        menit
                                                    </strong>

                                                </div>

                                            </div>

                                        @endif

                                    </div>


                                    @if (!empty($package->description))

                                        <div class="package-description">
                                            {{ $package->description }}
                                        </div>

                                    @endif


                                    <button
                                        type="button"
                                        class="choose-package-button"
                                        data-package-id="{{ $package->id }}"
                                        data-package-name="{{ $package->name }}"
                                        data-program-name="{{ $programName }}"
                                        data-package-price="{{ $package->getEffectivePrice() }}"
                                    >
                                        Pilih Paket
                                    </button>

                                </article>

                            @endforeach

                        </div>

                    </section>

                @endforeach

            </div>

        </section>


        {{-- =====================================================
            INFO CHECKOUT
        ====================================================== --}}

        <section class="checkout-information">

            <div class="checkout-info-icon">
                🔒
            </div>

            <div>

                <strong>
                    Pembelian paket akan melalui proses checkout
                </strong>

                <p>
                    Setelah memilih paket, kamu akan melihat kembali
                    peserta, program, harga, dan rincian pesanan
                    sebelum melakukan pembayaran.
                </p>

            </div>

        </section>


        {{-- =====================================================
            MODAL PILIH PAKET
        ====================================================== --}}

        <div
            class="package-modal-overlay"
            id="packageModal"
            aria-hidden="true"
        >

            <div class="package-modal">

                <button
                    type="button"
                    class="modal-close"
                    id="modalClose"
                    aria-label="Tutup"
                >
                    ×
                </button>


                <div class="modal-icon">
                    🏊
                </div>


                <h2>
                    Konfirmasi Pilihan Paket
                </h2>


                <p class="modal-description">
                    Pastikan peserta dan paket yang dipilih
                    sudah benar.
                </p>


                <div class="modal-summary">

                    <div class="modal-summary-item">

                        <span>
                            Peserta
                        </span>

                        <strong id="modalParticipant">
                            -
                        </strong>

                    </div>


                    <div class="modal-summary-item">

                        <span>
                            Program
                        </span>

                        <strong id="modalProgram">
                            -
                        </strong>

                    </div>


                    <div class="modal-summary-item">

                        <span>
                            Paket
                        </span>

                        <strong id="modalPackage">
                            -
                        </strong>

                    </div>


                    <div class="modal-summary-item price">

                        <span>
                            Harga
                        </span>

                        <strong id="modalPrice">
                            -
                        </strong>

                    </div>

                </div>


                <div class="modal-notice">

                    <span>
                        ℹ️
                    </span>

                    <p>
                        Setelah melanjutkan, kamu akan masuk ke halaman
                        checkout untuk memeriksa rincian pesanan sebelum
                        membuat pesanan dan melakukan pembayaran.
                    </p>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="modal-cancel"
                        id="modalCancel"
                    >
                        Batal
                    </button>


                    <button
                        type="button"
                        class="modal-continue"
                        disabled
                    >
                        Lanjut Checkout
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    .browse-page {
        width: 100%;
    }

    .browse-header {
        margin-bottom: 28px;
    }

    .back-link {
        display: inline-flex;

        margin-bottom: 13px;

        color: var(--blue-dark);

        font-size: 11px;
        font-weight: 750;

        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
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

    .empty-state p {
        max-width: 520px;

        margin: 9px auto 20px;

        color: var(--text-soft);

        font-size: 13px;
        line-height: 1.7;
    }


    /*
    |--------------------------------------------------------------------------
    | Section Heading
    |--------------------------------------------------------------------------
    */

    .participant-section,
    .packages-section {
        margin-bottom: 34px;
    }

    .section-heading {
        display: flex;
        align-items: flex-start;

        gap: 12px;

        margin-bottom: 16px;
    }

    .section-number {
        width: 32px;
        height: 32px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: #e0f2fe;
        color: #0369a1;

        font-size: 11px;
        font-weight: 850;
    }

    .section-heading h2 {
        color: var(--navy);

        font-size: 16px;
        font-weight: 850;
    }

    .section-heading p {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 11px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant Selector
    |--------------------------------------------------------------------------
    */

    .participant-selector {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 11px;
    }

    .participant-option {
        cursor: pointer;
    }

    .participant-option input {
        position: absolute;

        opacity: 0;
        pointer-events: none;
    }

    .participant-select-card {
        position: relative;

        display: flex;
        align-items: center;

        min-height: 70px;

        padding: 12px 40px 12px 12px;

        border: 1px solid var(--border);
        border-radius: 12px;

        background: var(--white);

        transition: 0.2s ease;
    }

    .participant-option input:checked
    + .participant-select-card {
        border-color: #38bdf8;

        background: #f0f9ff;

        box-shadow:
            0 0 0 2px rgba(56, 189, 248, 0.08);
    }

    .participant-avatar {
        width: 39px;
        height: 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        margin-right: 10px;

        border-radius: 50%;

        background: #e0f2fe;
        color: #0369a1;

        font-size: 12px;
        font-weight: 850;
    }

    .participant-info {
        min-width: 0;
    }

    .participant-info strong {
        display: block;

        overflow: hidden;

        color: var(--navy);

        font-size: 11px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .participant-info small {
        display: flex;
        align-items: center;

        gap: 5px;

        margin-top: 3px;

        color: var(--text-soft);

        font-size: 9px;
    }

    .participant-check {
        position: absolute;

        top: 50%;
        right: 13px;

        width: 20px;
        height: 20px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 1px solid #cbd5e1;
        border-radius: 50%;

        color: transparent;

        font-size: 10px;
        font-weight: 900;

        transform: translateY(-50%);
    }

    .participant-option input:checked
    + .participant-select-card
    .participant-check {
        border-color: #0284c7;

        background: #0284c7;
        color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Program
    |--------------------------------------------------------------------------
    */

    .program-list {
        display: flex;
        flex-direction: column;

        gap: 22px;
    }

    .program-group {
        padding: 20px;

        border: 1px solid var(--border);
        border-radius: 16px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .program-heading {
        display: flex;
        align-items: flex-start;

        gap: 12px;

        padding-bottom: 17px;

        border-bottom: 1px solid #f1f5f9;
    }

    .program-icon {
        width: 43px;
        height: 43px;

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

        font-size: 18px;
    }

    .program-heading-content {
        min-width: 0;
    }

    .program-title-row {
        display: flex;
        align-items: center;

        flex-wrap: wrap;

        gap: 7px;
    }

    .program-title-row h3 {
        color: var(--navy);

        font-size: 15px;
        font-weight: 850;
    }

    .program-badge {
        display: inline-flex;

        padding: 4px 7px;

        border-radius: 999px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
    }

    .program-heading-content p {
        max-width: 700px;

        margin-top: 5px;

        color: var(--text-soft);

        font-size: 10px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Packages
    |--------------------------------------------------------------------------
    */

    .package-grid {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 13px;

        padding-top: 17px;
    }

    .package-card {
        display: flex;
        flex-direction: column;

        min-width: 0;

        padding: 17px;

        border: 1px solid #e2e8f0;
        border-radius: 13px;

        background: #ffffff;

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            border-color 0.2s ease;
    }

    .package-card--linked {
        border: 2px solid #1769aa;
        box-shadow: 0 0 0 4px rgba(23, 105, 170, .12);
        scroll-margin-top: 110px;
    }

    .package-card:hover {
        border-color: #bae6fd;

        transform: translateY(-2px);

        box-shadow:
            0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .package-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 8px;
    }

    .package-label {
        display: block;

        margin-bottom: 4px;

        color: var(--text-light);

        font-size: 8px;
        font-weight: 750;

        text-transform: uppercase;
    }

    .package-top h4 {
        color: var(--navy);

        font-size: 13px;
        font-weight: 850;
    }

    .available-badge {
        display: inline-flex;

        flex-shrink: 0;

        padding: 4px 7px;

        border-radius: 999px;

        background: #dcfce7;
        color: #15803d;

        font-size: 8px;
        font-weight: 800;
    }

    .package-price {
        display: flex;
        align-items: baseline;

        gap: 4px;

        padding: 16px 0;
    }

    .package-price strong {
        color: #0369a1;

        font-size: 20px;
        font-weight: 900;

        letter-spacing: -0.4px;
    }

    .package-price span {
        color: var(--text-light);

        font-size: 8px;
    }

    .package-details {
        display: flex;
        flex-direction: column;

        gap: 9px;

        padding: 12px;

        border-radius: 10px;

        background: #f8fafc;
    }

    .detail-item {
        display: flex;
        align-items: center;

        gap: 8px;
    }

    .detail-icon {
        width: 27px;
        height: 27px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 7px;

        background: #ffffff;

        font-size: 11px;
    }

    .detail-item span:not(.detail-icon) {
        display: block;

        color: var(--text-light);

        font-size: 8px;
    }

    .detail-item strong {
        display: block;

        margin-top: 1px;

        color: #334155;

        font-size: 9px;
        font-weight: 750;
    }

    .package-description {
        margin-top: 11px;

        color: var(--text-soft);

        font-size: 9px;
        line-height: 1.6;
    }

    .choose-package-button {
        width: 100%;
        min-height: 38px;

        margin-top: auto;
        padding: 9px 12px;

        border: 0;
        border-radius: 9px;

        background: var(--blue);
        color: #ffffff;

        font-family: inherit;
        font-size: 10px;
        font-weight: 800;

        cursor: pointer;

        transition: 0.2s ease;
    }

    .package-description
    + .choose-package-button {
        margin-top: 14px;
    }

    .package-details
    + .choose-package-button {
        margin-top: 14px;
    }

    .choose-package-button:hover {
        background: var(--blue-dark);
    }


    /*
    |--------------------------------------------------------------------------
    | Checkout Information
    |--------------------------------------------------------------------------
    */

    .checkout-information {
        display: flex;
        align-items: flex-start;

        gap: 11px;

        margin-top: 28px;
        padding: 15px 17px;

        border: 1px solid #bae6fd;
        border-radius: 12px;

        background: #f0f9ff;
    }

    .checkout-info-icon {
        width: 33px;
        height: 33px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 8px;

        background: #ffffff;

        font-size: 13px;
    }

    .checkout-information strong {
        color: #075985;

        font-size: 10px;
        font-weight: 800;
    }

    .checkout-information p {
        margin-top: 3px;

        color: #0369a1;

        font-size: 9px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    .package-modal-overlay {
        position: fixed;
        inset: 0;

        z-index: 5000;

        display: none;
        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(15, 23, 42, 0.55);

        backdrop-filter: blur(3px);
    }

    .package-modal-overlay.open {
        display: flex;
    }

    .package-modal {
        position: relative;

        width: 100%;
        max-width: 450px;

        padding: 27px;

        border-radius: 17px;

        background: #ffffff;

        box-shadow:
            0 20px 60px rgba(15, 23, 42, 0.18);
    }

    .modal-close {
        position: absolute;

        top: 12px;
        right: 14px;

        width: 32px;
        height: 32px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 0;
        border-radius: 8px;

        background: #f8fafc;
        color: #64748b;

        font-size: 20px;

        cursor: pointer;
    }

    .modal-icon {
        width: 52px;
        height: 52px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 14px;

        border-radius: 13px;

        background:
            linear-gradient(
                135deg,
                #e0f2fe,
                #cffafe
            );

        font-size: 21px;
    }

    .package-modal h2 {
        color: var(--navy);

        font-size: 18px;
        font-weight: 850;
    }

    .modal-description {
        margin-top: 5px;

        color: var(--text-soft);

        font-size: 10px;
        line-height: 1.6;
    }

    .modal-summary {
        margin-top: 18px;

        overflow: hidden;

        border: 1px solid var(--border);
        border-radius: 11px;
    }

    .modal-summary-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 11px 13px;

        border-bottom: 1px solid #f1f5f9;
    }

    .modal-summary-item:last-child {
        border-bottom: 0;
    }

    .modal-summary-item span {
        color: var(--text-soft);

        font-size: 9px;
    }

    .modal-summary-item strong {
        color: #334155;

        font-size: 10px;
        font-weight: 800;

        text-align: right;
    }

    .modal-summary-item.price {
        background: #f8fafc;
    }

    .modal-summary-item.price strong {
        color: #0369a1;

        font-size: 13px;
        font-weight: 900;
    }

    .modal-notice {
        display: flex;
        align-items: flex-start;

        gap: 8px;

        margin-top: 14px;
        padding: 10px 11px;

        border-radius: 9px;

        background: #f8fafc;
    }

    .modal-notice p {
        margin: 0;

        color: var(--text-soft);

        font-size: 9px;
        line-height: 1.55;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;

        gap: 8px;

        margin-top: 18px;
    }

    .modal-cancel,
    .modal-continue {
        min-height: 39px;

        padding: 9px 14px;

        border-radius: 9px;

        font-family: inherit;
        font-size: 10px;
        font-weight: 800;

        cursor: pointer;
    }

    .modal-cancel {
        border: 1px solid var(--border);

        background: #ffffff;
        color: #475569;
    }

    .modal-continue {
        border: 0;

        background: #e2e8f0;
        color: #94a3b8;

        cursor: not-allowed;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1000px) {

        .package-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .participant-selector {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

    }


    @media (max-width: 650px) {

        .participant-selector,
        .package-grid {
            grid-template-columns: 1fr;
        }

        .program-group {
            padding: 16px;
        }

    }


    @media (max-width: 480px) {

        .program-heading {
            align-items: flex-start;
        }

        .package-modal {
            padding: 22px 17px;
        }

        .modal-actions {
            flex-direction: column-reverse;
        }

        .modal-cancel,
        .modal-continue {
            width: 100%;
        }

    }

</style>

@endpush


@push('scripts')

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            /*
            |--------------------------------------------------------------------------
            | Element
            |--------------------------------------------------------------------------
            */

            const modal =
                document.getElementById('packageModal');

            const modalClose =
                document.getElementById('modalClose');

            const modalCancel =
                document.getElementById('modalCancel');

            const modalContinue =
                document.querySelector('.modal-continue');

            const modalParticipant =
                document.getElementById('modalParticipant');

            const modalProgram =
                document.getElementById('modalProgram');

            const modalPackage =
                document.getElementById('modalPackage');

            const modalPrice =
                document.getElementById('modalPrice');


            /*
            |--------------------------------------------------------------------------
            | Package yang sedang dipilih
            |--------------------------------------------------------------------------
            */

            let selectedPackageId = null;


            /*
            |--------------------------------------------------------------------------
            | Ambil Peserta Terpilih
            |--------------------------------------------------------------------------
            */

            function getSelectedParticipant() {

                return document.querySelector(
                    'input[name="selected_participant"]:checked'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Format Rupiah
            |--------------------------------------------------------------------------
            */

            function formatRupiah(value) {

                const number =
                    Number(value || 0);

                return new Intl.NumberFormat(
                    'id-ID',
                    {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }
                ).format(number);

            }


            /*
            |--------------------------------------------------------------------------
            | Buka Modal
            |--------------------------------------------------------------------------
            */

            function openModal(button) {

                const participant =
                    getSelectedParticipant();


                if (!participant) {

                    alert(
                        'Silakan pilih peserta terlebih dahulu.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Simpan package ID
                |--------------------------------------------------------------------------
                */

                selectedPackageId =
                    button.dataset.packageId;


                /*
                |--------------------------------------------------------------------------
                | Isi Modal
                |--------------------------------------------------------------------------
                */

                modalParticipant.textContent =
                    participant.dataset.participantName;


                modalProgram.textContent =
                    button.dataset.programName;


                modalPackage.textContent =
                    button.dataset.packageName;


                modalPrice.textContent =
                    formatRupiah(
                        button.dataset.packagePrice
                    );


                /*
                |--------------------------------------------------------------------------
                | Aktifkan Tombol Checkout
                |--------------------------------------------------------------------------
                */

                if (modalContinue) {

                    modalContinue.disabled = false;

                    modalContinue.classList.add(
                        'active'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Tampilkan Modal
                |--------------------------------------------------------------------------
                */

                modal.classList.add('open');

                modal.setAttribute(
                    'aria-hidden',
                    'false'
                );


                document.body.style.overflow =
                    'hidden';

            }


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal
            |--------------------------------------------------------------------------
            */

            function closeModal() {

                if (!modal) {
                    return;
                }


                modal.classList.remove('open');

                modal.setAttribute(
                    'aria-hidden',
                    'true'
                );


                document.body.style.overflow =
                    '';


                selectedPackageId = null;


                if (modalContinue) {

                    modalContinue.disabled = true;

                    modalContinue.classList.remove(
                        'active'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Tombol Pilih Paket
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll(
                    '.choose-package-button'
                )
                .forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            openModal(button);

                        }
                    );

                });


            /*
            |--------------------------------------------------------------------------
            | Lanjut Checkout
            |--------------------------------------------------------------------------
            */

            if (modalContinue) {

                modalContinue.addEventListener(
                    'click',
                    function () {

                        const participant =
                            getSelectedParticipant();


                        if (
                            !participant
                            || !selectedPackageId
                        ) {

                            alert(
                                'Peserta atau paket belum dipilih.'
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Buat URL Checkout
                        |--------------------------------------------------------------------------
                        |
                        | Kita hanya mengirim:
                        |
                        | participant_id
                        | package_id
                        |
                        | Harga TIDAK dikirim.
                        |
                        | Backend akan mengambil harga paket
                        | langsung dari database.
                        |
                        */

                        const checkoutUrl =
                            @json(
                                route(
                                    'customer.checkout.show'
                                )
                            );


                        const params =
                            new URLSearchParams({
                                participant_id:
                                    participant.value,

                                package_id:
                                    selectedPackageId
                            });


                        window.location.href =
                            checkoutUrl
                            + '?'
                            + params.toString();

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal
            |--------------------------------------------------------------------------
            */

            if (modalClose) {

                modalClose.addEventListener(
                    'click',
                    closeModal
                );

            }


            if (modalCancel) {

                modalCancel.addEventListener(
                    'click',
                    closeModal
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Klik Area Gelap
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Tombol Escape
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Escape'
                        && modal
                        && modal.classList.contains('open')
                    ) {

                        closeModal();

                    }

                }
            );

        }
    );

</script>

<script>
    // URL fragment tidak ikut terkirim ke server saat login.
    // Gunakan query ?package=... untuk menemukan kartu setelah redirect.
    document.addEventListener('DOMContentLoaded', function () {
        const selected = document.querySelector('.package-card--linked');
        if (!selected) return;

        // Beri waktu browser menyelesaikan tata letak sebelum menggulir.
        window.requestAnimationFrame(function () {
            selected.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'center'
            });
        });
    });
</script>


<style>

    /*
    |--------------------------------------------------------------------------
    | Tombol Lanjut Checkout Aktif
    |--------------------------------------------------------------------------
    */

    .modal-continue.active {
        background:
            linear-gradient(
                135deg,
                var(--blue),
                var(--blue-dark)
            );

        color: #ffffff;

        cursor: pointer;

        box-shadow:
            0 5px 14px rgba(
                2,
                132,
                199,
                0.18
            );
    }


    .modal-continue.active:hover {
        transform: translateY(-1px);

        box-shadow:
            0 7px 17px rgba(
                2,
                132,
                199,
                0.23
            );
    }

</style>

@endpush
