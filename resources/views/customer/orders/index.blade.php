@extends('layouts.customer')

@section('title', 'Pembayaran')
@section('topbar-title', 'Pembayaran')

@section('content')

@php
    $paidPercentage = $statistics['total_orders'] > 0
        ? round(
            ($statistics['paid_orders'] / $statistics['total_orders']) * 100
        )
        : 0;
@endphp


<div class="payment-page">

    {{-- PAGE HEADER --}}
    <div class="page-header payment-page-header">

        <div>
            <h1 class="page-title">
                Pembayaran
            </h1>

            <p class="page-description">
                Lihat riwayat transaksi pembelian paket,
                status pembayaran, dan detail pesanan kamu.
            </p>
        </div>

        <a
            href="{{ route('customer.packages.browse') }}"
            class="payment-btn payment-btn-primary"
        >
            <svg viewBox="0 0 24 24" fill="none">
                <path
                    d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
                <path
                    d="M4 12L12 15.5L20 12"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
                <path
                    d="M4 16.5L12 20L20 16.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
            </svg>

            Beli Paket
        </a>

    </div>


    {{-- STATISTICS --}}
    <div class="payment-stats">

        <div class="payment-stat-card">

            <div class="payment-stat-icon icon-blue">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M6 3H18V21L15 19L12 21L9 19L6 21V3Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M9 8H15M9 12H15"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>

            <div>
                <div class="payment-stat-label">
                    Total Transaksi
                </div>

                <div class="payment-stat-value">
                    {{ $statistics['total_orders'] }}
                </div>

                <div class="payment-stat-note">
                    Seluruh riwayat pembelian
                </div>
            </div>

        </div>


        <div class="payment-stat-card">

            <div class="payment-stat-icon icon-green">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M8 12L10.7 14.7L16 9"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>

            <div>
                <div class="payment-stat-label">
                    Transaksi Lunas
                </div>

                <div class="payment-stat-value">
                    {{ $statistics['paid_orders'] }}
                </div>

                <div class="payment-stat-note">
                    {{ $paidPercentage }}% dari total transaksi
                </div>
            </div>

        </div>


        <div class="payment-stat-card">

            <div class="payment-stat-icon icon-orange">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M12 7V12L15 14"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>

            <div>
                <div class="payment-stat-label">
                    Menunggu Pembayaran
                </div>

                <div class="payment-stat-value">
                    {{ $statistics['pending_orders'] }}
                </div>

                <div class="payment-stat-note">
                    Transaksi yang belum selesai
                </div>
            </div>

        </div>


        <div class="payment-stat-card">

            <div class="payment-stat-icon icon-purple">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="3"
                        y="6"
                        width="18"
                        height="13"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M3 10H21"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </div>

            <div>
                <div class="payment-stat-label">
                    Total Dibayarkan
                </div>

                <div class="payment-stat-value payment-stat-money">
                    Rp {{ number_format(
                        $statistics['total_spent'],
                        0,
                        ',',
                        '.'
                    ) }}
                </div>

                <div class="payment-stat-note">
                    Dari transaksi berstatus lunas
                </div>
            </div>

        </div>

    </div>


    {{-- TRANSACTION HISTORY --}}
    <section class="card payment-history-card">

        <div class="payment-section-header">

            <div>
                <div class="section-eyebrow">
                    RIWAYAT TRANSAKSI
                </div>

                <h2>
                    Semua Pembayaran
                </h2>

                <p>
                    Transaksi diurutkan dari pembelian terbaru.
                </p>
            </div>


            @if ($orders->isNotEmpty())
                <div class="transaction-count">
                    {{ $orders->count() }}
                    transaksi
                </div>
            @endif

        </div>


        @if ($orders->isNotEmpty())

            <div class="transaction-list">

                @foreach ($orders as $order)

                    @php
                        $latestPayment = $order->payments
                            ->sortByDesc('id')
                            ->first();

                        $firstItem = $order->items->first();

                        $additionalItems = max(
                            $order->items->count() - 1,
                            0
                        );

                        $orderStatusClass = match ($order->status) {
                            'paid' => 'status-success',
                            'pending' => 'status-warning',
                            'cancelled' => 'status-danger',
                            'expired' => 'status-neutral',
                            'refunded' => 'status-purple',
                            default => 'status-neutral',
                        };

                        $paymentStatusClass = match (
                            $latestPayment?->status
                        ) {
                            'paid' => 'status-success',
                            'pending' => 'status-warning',
                            'failed' => 'status-danger',
                            'expired' => 'status-neutral',
                            'refunded' => 'status-purple',
                            default => 'status-neutral',
                        };

                        $paymentStatusLabel = match (
                            $latestPayment?->status
                        ) {
                            'paid' => 'Lunas',
                            'pending' => 'Menunggu',
                            'failed' => 'Gagal',
                            'expired' => 'Kedaluwarsa',
                            'refunded' => 'Dikembalikan',
                            default => 'Belum Ada',
                        };
                    @endphp


                    <article class="transaction-item">

                        <div class="transaction-main">

                            <div class="transaction-icon">

                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M4 12L12 15.5L20 12"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                            </div>


                            <div class="transaction-content">

                                <div class="transaction-heading">

                                    <div>
                                        <div class="transaction-code">
                                            {{ $order->order_code }}
                                        </div>

                                        <div class="transaction-date">
                                            {{ $order->ordered_at
                                                ? $order
                                                    ->ordered_at
                                                    ->translatedFormat(
                                                        'd F Y, H:i'
                                                    )
                                                : '-' }}
                                        </div>
                                    </div>


                                    <span
                                        class="status-pill
                                        {{ $orderStatusClass }}"
                                    >
                                        {{ $order->status_label }}
                                    </span>

                                </div>


                                <div class="transaction-package">

                                    <div class="transaction-package-info">

                                        <div class="package-name">
                                            {{ $firstItem?->package_name
                                                ?? 'Pembelian Paket' }}
                                        </div>

                                        <div class="package-participant">

                                            @if ($firstItem?->participant)

                                                Untuk
                                                <strong>
                                                    {{ $firstItem
                                                        ->participant
                                                        ->full_name }}
                                                </strong>

                                            @else

                                                Peserta tidak tersedia

                                            @endif


                                            @if ($additionalItems > 0)

                                                <span class="additional-item">
                                                    +{{ $additionalItems }}
                                                    item lainnya
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    @if ($firstItem?->package?->program)

                                        <span class="program-badge">
                                            {{ $firstItem
                                                ->package
                                                ->program
                                                ->name }}
                                        </span>

                                    @endif

                                </div>


                                <div class="transaction-bottom">

                                    <div class="transaction-details">

                                        <div class="transaction-detail">

                                            <span class="detail-label">
                                                Pembayaran
                                            </span>

                                            <span
                                                class="status-pill
                                                {{ $paymentStatusClass }}"
                                            >
                                                {{ $paymentStatusLabel }}
                                            </span>

                                        </div>


                                        @if (
                                            $latestPayment
                                            && $latestPayment->payment_method
                                        )

                                            <div class="transaction-detail">

                                                <span class="detail-label">
                                                    Metode
                                                </span>

                                                <strong>
                                                    {{ ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $latestPayment
                                                                ->payment_method
                                                        )
                                                    ) }}
                                                </strong>

                                            </div>

                                        @endif


                                        @if ($order->invoice)

                                            <div class="transaction-detail">

                                                <span class="detail-label">
                                                    Invoice
                                                </span>

                                                <strong>
                                                    {{ $order
                                                        ->invoice
                                                        ->invoice_number }}
                                                </strong>

                                            </div>

                                        @endif

                                    </div>


                                    <div class="transaction-price">

                                        <span>
                                            Total
                                        </span>

                                        <strong>
                                            {{ $order->formatted_total }}
                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="transaction-action">

                            <a
                                href="{{ route(
                                    'customer.orders.show',
                                    $order->id
                                ) }}"
                                class="detail-button"
                            >
                                Lihat Detail

                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M9 6L15 12L9 18"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="payment-empty">

                <div class="payment-empty-icon">

                    <svg viewBox="0 0 24 24" fill="none">
                        <rect
                            x="3"
                            y="6"
                            width="18"
                            height="13"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="M3 10H21"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                    </svg>

                </div>

                <h3>
                    Belum ada transaksi
                </h3>

                <p>
                    Kamu belum melakukan pembelian paket.
                    Pilih paket renang untuk peserta dan
                    transaksi akan muncul di halaman ini.
                </p>

                <a
                    href="{{ route('customer.packages.browse') }}"
                    class="payment-btn payment-btn-primary"
                >
                    Lihat Paket Renang
                </a>

            </div>

        @endif

    </section>

</div>

@endsection


@push('styles')

<style>

    .payment-page {
        display: flex;
        flex-direction: column;

        gap: 22px;
    }


    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .payment-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;
    }


    /*
    |--------------------------------------------------------------------------
    | Button
    |--------------------------------------------------------------------------
    */

    .payment-btn {
        min-height: 40px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 8px;

        padding: 9px 15px;

        border: 1px solid transparent;
        border-radius: 10px;

        font-size: 11px;
        font-weight: 800;

        transition:
            transform .2s ease,
            background .2s ease,
            box-shadow .2s ease;
    }

    .payment-btn:hover {
        transform: translateY(-1px);
    }

    .payment-btn svg {
        width: 17px;
        height: 17px;
    }

    .payment-btn-primary {
        background: var(--blue);
        color: white;

        box-shadow:
            0 5px 14px rgba(2, 132, 199, .14);
    }

    .payment-btn-primary:hover {
        background: var(--blue-dark);
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .payment-stats {
        display: grid;

        grid-template-columns:
            repeat(4, minmax(0, 1fr));

        gap: 16px;
    }

    .payment-stat-card {
        display: flex;
        align-items: center;

        gap: 14px;

        min-width: 0;

        padding: 19px;

        border: 1px solid var(--border);
        border-radius: 15px;

        background: white;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, .025);
    }

    .payment-stat-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 13px;
    }

    .payment-stat-icon svg {
        width: 22px;
        height: 22px;
    }

    .icon-blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .icon-green {
        background: #dcfce7;
        color: #16a34a;
    }

    .icon-orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .icon-purple {
        background: #f3e8ff;
        color: #9333ea;
    }

    .payment-stat-label {
        color: var(--text-soft);

        font-size: 10px;
        font-weight: 700;
    }

    .payment-stat-value {
        margin-top: 3px;

        color: var(--navy);

        font-size: 24px;
        font-weight: 850;

        line-height: 1.15;
    }

    .payment-stat-money {
        font-size: 18px;
    }

    .payment-stat-note {
        margin-top: 5px;

        overflow: hidden;

        color: var(--text-light);

        font-size: 8px;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | History Card
    |--------------------------------------------------------------------------
    */

    .payment-history-card {
        overflow: hidden;

        padding: 0;
    }

    .payment-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding: 21px 22px;

        border-bottom: 1px solid var(--border);
    }

    .section-eyebrow {
        margin-bottom: 5px;

        color: var(--blue);

        font-size: 9px;
        font-weight: 850;

        letter-spacing: 1px;
    }

    .payment-section-header h2 {
        color: var(--navy);

        font-size: 16px;
        font-weight: 850;
    }

    .payment-section-header p {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 10px;
    }

    .transaction-count {
        padding: 6px 10px;

        border-radius: 999px;

        background: #f1f5f9;
        color: var(--text-soft);

        font-size: 9px;
        font-weight: 800;

        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Transaction List
    |--------------------------------------------------------------------------
    */

    .transaction-list {
        display: flex;
        flex-direction: column;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 22px;

        padding: 20px 22px;

        border-bottom: 1px solid var(--border);

        transition: background .2s ease;
    }

    .transaction-item:last-child {
        border-bottom: 0;
    }

    .transaction-item:hover {
        background: #fbfdff;
    }

    .transaction-main {
        display: flex;
        align-items: flex-start;

        gap: 15px;

        min-width: 0;
        flex: 1;
    }

    .transaction-icon {
        width: 43px;
        height: 43px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background: #e0f2fe;
        color: #0284c7;
    }

    .transaction-icon svg {
        width: 21px;
        height: 21px;
    }

    .transaction-content {
        min-width: 0;
        flex: 1;
    }

    .transaction-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 15px;
    }

    .transaction-code {
        color: var(--navy);

        font-size: 12px;
        font-weight: 850;
    }

    .transaction-date {
        margin-top: 4px;

        color: var(--text-light);

        font-size: 8px;
    }

    .transaction-package {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        margin-top: 12px;
        padding: 11px 13px;

        border-radius: 10px;

        background: #f8fafc;
    }

    .transaction-package-info {
        min-width: 0;
    }

    .package-name {
        overflow: hidden;

        color: var(--navy);

        font-size: 10px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .package-participant {
        margin-top: 3px;

        color: var(--text-soft);

        font-size: 8px;
    }

    .package-participant strong {
        color: var(--text);
    }

    .additional-item {
        margin-left: 5px;

        color: var(--blue);

        font-weight: 750;
    }

    .program-badge {
        flex-shrink: 0;

        padding: 5px 8px;

        border-radius: 999px;

        background: #e0f2fe;
        color: #0369a1;

        font-size: 8px;
        font-weight: 800;
    }

    .transaction-bottom {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;

        gap: 20px;

        margin-top: 12px;
    }

    .transaction-details {
        display: flex;
        flex-wrap: wrap;

        gap: 10px 20px;
    }

    .transaction-detail {
        display: flex;
        flex-direction: column;

        gap: 4px;
    }

    .detail-label {
        color: var(--text-light);

        font-size: 7px;
        font-weight: 700;

        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .transaction-detail strong {
        color: var(--text);

        font-size: 9px;
        font-weight: 750;
    }

    .transaction-price {
        flex-shrink: 0;

        text-align: right;
    }

    .transaction-price span {
        display: block;

        color: var(--text-light);

        font-size: 8px;
    }

    .transaction-price strong {
        display: block;

        margin-top: 3px;

        color: var(--navy);

        font-size: 14px;
        font-weight: 850;
    }

    .transaction-action {
        flex-shrink: 0;
    }

    .detail-button {
        min-height: 36px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 6px;

        padding: 8px 11px;

        border: 1px solid var(--border);
        border-radius: 9px;

        background: white;
        color: var(--blue);

        font-size: 9px;
        font-weight: 800;

        transition:
            background .2s ease,
            border-color .2s ease;
    }

    .detail-button:hover {
        border-color: #bae6fd;

        background: #f0f9ff;
    }

    .detail-button svg {
        width: 13px;
        height: 13px;
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .status-pill {
        min-height: 25px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 5px 9px;

        border-radius: 999px;

        font-size: 8px;
        font-weight: 850;

        white-space: nowrap;
    }

    .status-success {
        background: #dcfce7;
        color: #15803d;
    }

    .status-warning {
        background: #fef3c7;
        color: #b45309;
    }

    .status-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-purple {
        background: #f3e8ff;
        color: #7e22ce;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .payment-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        min-height: 360px;

        padding: 40px 20px;

        text-align: center;
    }

    .payment-empty-icon {
        width: 58px;
        height: 58px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 14px;

        border-radius: 16px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .payment-empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .payment-empty h3 {
        color: var(--navy);

        font-size: 14px;
        font-weight: 850;
    }

    .payment-empty p {
        max-width: 380px;

        margin: 7px auto 17px;

        color: var(--text-soft);

        font-size: 10px;
        line-height: 1.7;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1200px) {

        .payment-stats {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

    }


    @media (max-width: 800px) {

        .transaction-item {
            align-items: stretch;
            flex-direction: column;
        }

        .transaction-action {
            padding-left: 58px;
        }

        .detail-button {
            width: 100%;
        }

    }


    @media (max-width: 700px) {

        .payment-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .payment-stats {
            grid-template-columns: 1fr;
        }

        .transaction-bottom {
            align-items: flex-start;
            flex-direction: column;
        }

        .transaction-price {
            text-align: left;
        }

    }


    @media (max-width: 560px) {

        .payment-section-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .transaction-item {
            padding: 17px;
        }

        .transaction-main {
            display: block;
        }

        .transaction-icon {
            margin-bottom: 12px;
        }

        .transaction-heading {
            display: block;
        }

        .transaction-heading .status-pill {
            margin-top: 8px;
        }

        .transaction-package {
            align-items: flex-start;
            flex-direction: column;
        }

        .transaction-action {
            padding-left: 0;
        }

    }

</style>

@endpush
