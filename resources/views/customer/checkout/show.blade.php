@extends('layouts.customer')

@section('title', 'Checkout Paket')
@section('topbar-title', 'Checkout')

@section('content')

<div class="checkout-page">

    <div class="checkout-header">

        <a
            href="{{ route('customer.packages.browse') }}"
            class="back-link"
        >
            ← Kembali ke Program & Paket
        </a>

        <h1 class="page-title">
            Checkout Paket
        </h1>

        <p class="page-description">
            Periksa kembali peserta dan paket yang dipilih
            sebelum membuat pesanan.
        </p>

    </div>

    @if ($errors->any())

        <div class="checkout-alert error">

            <strong>
                Checkout belum dapat diproses.
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form
        action="{{ route('customer.checkout.store') }}"
        method="POST"
        class="checkout-layout"
    >

        @csrf

        <input
            type="hidden"
            name="participant_id"
            value="{{ $participant->id }}"
        >

        <input
            type="hidden"
            name="package_id"
            value="{{ $package->id }}"
        >

        @if ($promoCode)
            <input
                type="hidden"
                name="promo_code"
                value="{{ $promoCode->code }}"
            >
        @endif

        {{-- =====================================================
            BAGIAN KIRI
        ====================================================== --}}

        <div class="checkout-main">

            {{-- PESERTA --}}

            <section class="checkout-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        👤
                    </div>

                    <div>
                        <span class="heading-step">
                            Peserta
                        </span>

                        <h2>
                            Data Peserta
                        </h2>
                    </div>

                </div>

                <div class="participant-box">

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

                    <div class="participant-content">

                        <strong>
                            {{ $participant->full_name }}
                        </strong>

                        <div class="participant-meta">

                            <span>

                                @if ($participant->participant_type === 'child')
                                    Anak
                                @else
                                    Diri Sendiri
                                @endif

                            </span>

                            <span class="dot">
                                •
                            </span>

                            <span>

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

                            </span>

                        </div>

                    </div>

                    <div class="verified-badge">
                        ✓ Terpilih
                    </div>

                </div>

            </section>

            {{-- PAKET --}}

            <section class="checkout-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        🏊
                    </div>

                    <div>
                        <span class="heading-step">
                            Paket
                        </span>

                        <h2>
                            Detail Paket
                        </h2>
                    </div>

                </div>

                <div class="package-box">

                    <div class="program-name">
                        {{
                            $package->program?->name
                            ?? 'Program Renang'
                        }}
                    </div>

                    <h3>
                        {{ $package->name }}
                    </h3>

                    @if (!empty($package->description))

                        <p class="package-description">
                            {{ $package->description }}
                        </p>

                    @endif

                    <div class="package-specifications">

                        <div class="specification">

                            <div class="spec-icon">
                                🏊
                            </div>

                            <div>

                                <span>
                                    Pertemuan
                                </span>

                                <strong>
                                    {{ $package->total_sessions }}
                                    sesi
                                </strong>

                            </div>

                        </div>

                        <div class="specification">

                            <div class="spec-icon">
                                📅
                            </div>

                            <div>

                                <span>
                                    Masa Berlaku
                                </span>

                                <strong>
                                    {{ $package->validity_days }}
                                    hari
                                </strong>

                            </div>

                        </div>

                        @if (
                            isset($package->session_duration_minutes)
                            && $package->session_duration_minutes
                        )

                            <div class="specification">

                                <div class="spec-icon">
                                    ⏱
                                </div>

                                <div>

                                    <span>
                                        Durasi
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

                </div>

            </section>

            {{-- CATATAN --}}

            <section class="checkout-card">

                <div class="card-heading">

                    <div class="heading-icon">
                        📝
                    </div>

                    <div>
                        <span class="heading-step">
                            Opsional
                        </span>

                        <h2>
                            Catatan Pesanan
                        </h2>
                    </div>

                </div>

                <label
                    for="notes"
                    class="form-label"
                >
                    Catatan untuk Ragen Swim Course
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    maxlength="2000"
                    class="notes-input"
                    placeholder="Contoh: Peserta masih takut memasukkan kepala ke dalam air."
                >{{ old('notes') }}</textarea>

                <p class="field-help">
                    Catatan ini bersifat opsional dan dapat digunakan
                    untuk memberikan informasi tambahan mengenai peserta.
                </p>

            </section>

        </div>

        {{-- =====================================================
            BAGIAN KANAN - RINGKASAN
        ====================================================== --}}

        <aside class="checkout-sidebar">

            <div class="summary-card">

                <h2>
                    Ringkasan Pesanan
                </h2>

                <div class="summary-product">

                    <div class="summary-product-icon">
                        🏊
                    </div>

                    <div>

                        <span>
                            {{
                                $package->program?->name
                                ?? 'Program Renang'
                            }}
                        </span>

                        <strong>
                            {{ $package->name }}
                        </strong>

                    </div>

                </div>

                <div class="summary-lines">

                    <div class="summary-line">

                        <span>
                            Peserta
                        </span>

                        <strong>
                            {{ $participant->full_name }}
                        </strong>

                    </div>

                    <div class="summary-line">

                        <span>
                            Jumlah
                        </span>

                        <strong>
                            1 Paket
                        </strong>

                    </div>

                    {{-- HARGA PROMO PAKET --}}
                    @if ($package->hasActivePromo())
                        <div class="summary-line"><span>Harga Normal</span><strong style="text-decoration:line-through;opacity:0.65">Rp{{ number_format((float) $package->price, 0, ',', '.') }}</strong></div>
                        <div class="summary-line"><span>{{ $package->promo_name ?: 'Harga Promo' }}</span><strong style="color:#15803d">Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    @else
                        <div class="summary-line"><span>Harga Paket</span><strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    @endif

                    <div class="summary-line">

                        <span>
                            Diskon
                        </span>

                        <strong>
                            @if ($discountAmount > 0)

                                - Rp{{ number_format(
                                    $discountAmount,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            @else

                                Rp0

                            @endif
                        </strong>

                    </div>

                    {{-- BIAYA PENDAFTARAN --}}

                    <div class="summary-line">

                        <span>
                            Biaya Pendaftaran
                        </span>

                        <strong>
                            Rp{{ number_format(
                                $registrationFee,
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                    </div>

                    @if ($registrationFee > 0)

                        <div class="registration-fee-note">

                            <strong>
                                Biaya Pendaftaran Satu Kali
                            </strong>

                            <p>
                                Biaya pendaftaran hanya dikenakan
                                satu kali untuk setiap peserta.
                                Setelah pembayaran berhasil
                                diverifikasi, pembelian paket
                                berikutnya tidak dikenakan
                                biaya pendaftaran lagi.
                            </p>

                        </div>

                    @elseif ($participant->hasPaidRegistration())

                        <div class="registration-fee-note paid">

                            <strong>
                                ✓ Pendaftaran Sudah Lunas
                            </strong>

                            <p>
                                Peserta ini sudah melunasi biaya
                                pendaftaran sehingga tidak perlu
                                membayar biaya pendaftaran lagi.
                            </p>

                        </div>

                    @elseif ($participant->isRegistrationFeeExempt())

                        <div class="registration-fee-note paid">

                            <strong>
                                ✓ Bebas Biaya Pendaftaran
                            </strong>

                            <p>
                                Peserta ini mendapatkan pembebasan
                                biaya pendaftaran.
                            </p>

                        </div>

                    @endif

                </div>

                <div class="summary-total">

                    <div>

                        <span>
                            Total Pembayaran
                        </span>

                        <strong>
                            Rp{{ number_format(
                                $totalAmount,
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                    </div>

                </div>

                <div class="security-note">

                    <span class="security-icon">
                        🔒
                    </span>

                    <p>
                        Harga paket, diskon, dan biaya pendaftaran
                        dihitung langsung oleh sistem.
                        Nominal akhir akan diperiksa kembali
                        saat pesanan dibuat.
                    </p>

                </div>

                <button
                    type="submit"
                    class="checkout-button"
                >
                    Buat Pesanan
                </button>

                <p class="checkout-disclaimer">
                    Dengan membuat pesanan, transaksi akan berstatus
                    menunggu pembayaran. Paket belum aktif sampai
                    pembayaran berhasil dikonfirmasi.
                </p>

            </div>

        </aside>

    </form>

</div>

@endsection

@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

    .checkout-page {
        width: 100%;
    }

    .checkout-header {
        margin-bottom: 24px;
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
    | Alert
    |--------------------------------------------------------------------------
    */

    .checkout-alert {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 11px;
        font-size: 10px;
        line-height: 1.6;
    }

    .checkout-alert.error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .checkout-alert strong {
        display: block;
        margin-bottom: 4px;
    }

    .checkout-alert ul {
        margin: 0;
        padding-left: 17px;
    }

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    .checkout-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        align-items: start;
        gap: 20px;
    }

    .checkout-main {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Cards
    |--------------------------------------------------------------------------
    */

    .checkout-card,
    .summary-card {
        border: 1px solid var(--border);
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .checkout-card {
        padding: 20px;
    }

    .card-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .heading-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0f2fe, #cffafe);
        font-size: 15px;
    }

    .heading-step {
        display: block;
        margin-bottom: 2px;
        color: var(--blue-dark);
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .card-heading h2 {
        color: var(--navy);
        font-size: 14px;
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
        gap: 11px;
        padding: 14px;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        background: #f0f9ff;
    }

    .participant-avatar {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        color: #0369a1;
        font-size: 13px;
        font-weight: 900;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.08);
    }

    .participant-content {
        min-width: 0;
        flex: 1;
    }

    .participant-content > strong {
        display: block;
        overflow: hidden;
        color: var(--navy);
        font-size: 12px;
        font-weight: 850;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .participant-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 9px;
    }

    .participant-meta .dot {
        color: #94a3b8;
    }

    .verified-badge {
        display: inline-flex;
        flex-shrink: 0;
        padding: 5px 8px;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d;
        font-size: 8px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Package
    |--------------------------------------------------------------------------
    */

    .package-box {
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .program-name {
        margin-bottom: 4px;
        color: #0369a1;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .package-box h3 {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .package-description {
        max-width: 650px;
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.65;
    }

    .package-specifications {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 9px;
        margin-top: 15px;
    }

    .specification {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .spec-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        background: #ffffff;
        font-size: 12px;
    }

    .specification span {
        display: block;
        color: var(--text-light);
        font-size: 8px;
    }

    .specification strong {
        display: block;
        margin-top: 1px;
        color: #334155;
        font-size: 9px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 10px;
        font-weight: 750;
    }

    .notes-input {
        width: 100%;
        resize: vertical;
        min-height: 100px;
        padding: 11px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        background: #ffffff;
        color: #334155;
        font-family: inherit;
        font-size: 10px;
        line-height: 1.6;
        transition: 0.2s ease;
    }

    .notes-input:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
    }

    .field-help {
        margin-top: 6px;
        color: var(--text-light);
        font-size: 8px;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    .checkout-sidebar {
        position: sticky;
        top: 90px;
    }

    .summary-card {
        overflow: hidden;
        padding: 20px;
    }

    .summary-card > h2 {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .summary-product {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-top: 15px;
        padding: 11px;
        border-radius: 10px;
        background: #f0f9ff;
    }

    .summary-product-icon {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #ffffff;
        font-size: 13px;
    }

    .summary-product span {
        display: block;
        color: #0369a1;
        font-size: 8px;
        font-weight: 700;
    }

    .summary-product strong {
        display: block;
        margin-top: 2px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .summary-lines {
        padding: 15px 0;
    }

    .summary-line {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 10px;
    }

    .summary-line:last-child {
        margin-bottom: 0;
    }

    .summary-line span {
        color: var(--text-soft);
        font-size: 9px;
    }

    .summary-line strong {
        max-width: 160px;
        color: #334155;
        font-size: 9px;
        font-weight: 750;
        text-align: right;
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Fee
    |--------------------------------------------------------------------------
    */

    .registration-fee-note {
        margin-top: 12px;
        padding: 11px 12px;
        border: 1px solid #bae6fd;
        border-radius: 9px;
        background: #f0f9ff;
    }

    .registration-fee-note strong {
        display: block;
        color: #075985;
        font-size: 9px;
        font-weight: 850;
    }

    .registration-fee-note p {
        margin: 4px 0 0;
        color: #0369a1;
        font-size: 8px;
        line-height: 1.65;
    }

    .registration-fee-note.paid {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .registration-fee-note.paid strong,
    .registration-fee-note.paid p {
        color: #166534;
    }

    /*
    |--------------------------------------------------------------------------
    | Total
    |--------------------------------------------------------------------------
    */

    .summary-total {
        padding: 15px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }

    .summary-total > div {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 15px;
    }

    .summary-total span {
        color: #475569;
        font-size: 10px;
        font-weight: 700;
    }

    .summary-total strong {
        color: #0369a1;
        font-size: 18px;
        font-weight: 900;
    }

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    .security-note {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 14px;
        padding: 9px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .security-icon {
        flex-shrink: 0;
        font-size: 10px;
    }

    .security-note p {
        margin: 0;
        color: var(--text-soft);
        font-size: 8px;
        line-height: 1.55;
    }

    /*
    |--------------------------------------------------------------------------
    | Button
    |--------------------------------------------------------------------------
    */

    .checkout-button {
        width: 100%;
        min-height: 42px;
        margin-top: 15px;
        padding: 10px 14px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(
            135deg,
            var(--blue),
            var(--blue-dark)
        );
        color: #ffffff;
        font-family: inherit;
        font-size: 10px;
        font-weight: 850;
        cursor: pointer;
        box-shadow: 0 6px 15px rgba(2, 132, 199, 0.15);
        transition: 0.2s ease;
    }

    .checkout-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(2, 132, 199, 0.2);
    }

    .checkout-disclaimer {
        margin-top: 9px;
        color: var(--text-light);
        font-size: 8px;
        line-height: 1.55;
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 950px) {

        .checkout-layout {
            grid-template-columns: 1fr;
        }

        .checkout-sidebar {
            position: static;
        }

    }

    @media (max-width: 650px) {

        .package-specifications {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 480px) {

        .checkout-card,
        .summary-card {
            padding: 16px;
        }

        .participant-box {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .verified-badge {
            margin-left: 53px;
        }

        .summary-total > div {
            align-items: flex-start;
        }

    }

</style>

@endpush
