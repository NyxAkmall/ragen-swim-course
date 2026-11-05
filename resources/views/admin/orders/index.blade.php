@extends('layouts.admin')

@section('title', 'Order & Pembayaran')
@section('topbar-title', 'Order & Pembayaran')

@push('styles')
<style>
    .order-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .order-page-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .order-page-description {
        max-width: 760px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        padding: 20px;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--white);
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 75px;
        height: 75px;
        border-radius: 0 0 0 75px;
        background: rgba(2, 132, 199, 0.045);
    }

    .stat-label {
        position: relative;
        z-index: 1;
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 750;
    }

    .stat-value {
        position: relative;
        z-index: 1;
        margin-top: 8px;
        color: var(--navy);
        font-size: 25px;
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    .stat-value.money {
        font-size: 21px;
    }

    .stat-note {
        position: relative;
        z-index: 1;
        margin-top: 5px;
        color: var(--text-light);
        font-size: 11px;
        line-height: 1.45;
    }

    .filter-card {
        padding: 20px;
        margin-bottom: 22px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 210px 190px auto;
        gap: 12px;
        align-items: end;
    }

    .form-group {
        min-width: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .form-control {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
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

    .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .form-control::placeholder {
        color: var(--text-light);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .filter-button,
    .reset-button {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 15px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 750;
        white-space: nowrap;
    }

    .filter-button {
        border: 0;
        background: var(--blue);
        color: var(--white);
    }

    .filter-button:hover {
        background: var(--blue-dark);
    }

    .reset-button {
        border: 1px solid var(--border);
        background: var(--white);
        color: var(--text-soft);
    }

    .reset-button:hover {
        background: #f8fafc;
        color: var(--navy);
    }

    .table-card {
        overflow: hidden;
    }

    .table-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .table-heading-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .table-heading-note {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .table-result-count {
        padding: 6px 9px;
        border-radius: 999px;
        background: #f0f9ff;
        color: var(--blue-dark);
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .order-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: collapse;
    }

    .order-table th {
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

    .order-table td {
        padding: 15px;
        border-bottom: 1px solid #eef2f7;
        color: var(--text);
        font-size: 12px;
        vertical-align: top;
    }

    .order-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .order-table tbody tr:hover {
        background: #fbfdff;
    }

    .order-code {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .order-date {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.45;
    }

    .customer-name {
        color: var(--text);
        font-weight: 750;
    }

    .customer-email {
        max-width: 190px;
        margin-top: 4px;
        overflow: hidden;
        color: var(--text-soft);
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-main {
        color: var(--text);
        font-weight: 700;
    }

    .item-sub {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .more-items {
        display: inline-flex;
        margin-top: 6px;
        padding: 3px 7px;
        border-radius: 999px;
        background: #f1f5f9;
        color: var(--text-soft);
        font-size: 9px;
        font-weight: 750;
    }

    .amount {
        color: var(--navy);
        font-size: 13px;
        font-weight: 850;
        white-space: nowrap;
    }

    .discount-text {
        margin-top: 4px;
        color: var(--success);
        font-size: 10px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-paid {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-expired {
        background: #f8fafc;
        color: #64748b;
    }

    .status-refunded {
        background: #faf5ff;
        color: #7e22ce;
    }

    .status-failed {
        background: #fef2f2;
        color: #b91c1c;
    }

    .payment-name {
        margin-top: 7px;
        color: var(--text);
        font-size: 10px;
        font-weight: 650;
        line-height: 1.5;
    }

    .payment-code {
        margin-top: 3px;
        color: var(--text-light);
        font-size: 9px;
        line-height: 1.45;
    }

    .invoice-number {
        color: var(--navy);
        font-size: 10px;
        font-weight: 750;
        word-break: break-word;
    }

    .invoice-empty {
        color: var(--text-light);
        font-size: 10px;
    }

    .detail-button {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 11px;
        border: 1px solid #bae6fd;
        border-radius: 9px;
        background: #f0f9ff;
        color: var(--blue-dark);
        text-decoration: none;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .detail-button:hover {
        border-color: #7dd3fc;
        background: #e0f2fe;
    }

    .empty-state {
        padding: 55px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border-radius: 15px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .empty-icon svg {
        width: 25px;
        height: 25px;
    }

    .empty-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 420px;
        margin: 6px auto 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .pagination-wrapper {
        padding: 18px 20px;
        border-top: 1px solid var(--border);
    }

    .pagination-wrapper nav > div:first-child {
        margin-bottom: 10px;
    }

    .pagination-wrapper nav span,
    .pagination-wrapper nav a {
        font-size: 12px;
    }

    @media (max-width: 1250px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .order-page-header {
            display: block;
        }

        .order-page-title {
            font-size: 24px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-wrap: wrap;
        }

        .filter-button,
        .reset-button {
            flex: 1;
        }

        .table-heading {
            align-items: flex-start;
        }
    }
</style>
@endpush


@section('content')

@php
    $statusLabels = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ];

    $paymentStatusLabels = [
        'pending' => 'Menunggu',
        'paid' => 'Berhasil',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Refund',
    ];
@endphp


<div class="order-page-header">

    <div>
        <h1 class="order-page-title">
            Order & Pembayaran
        </h1>

        <p class="order-page-description">
            Pantau transaksi pembelian paket, status pembayaran,
            customer, peserta, invoice, dan riwayat order
            Ragen Swim Course.
        </p>
    </div>

</div>


<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-label">
            Total Order
        </div>

        <div class="stat-value">
            {{ number_format($statistics['total']) }}
        </div>

        <div class="stat-note">
            Seluruh transaksi yang tercatat.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Menunggu Pembayaran
        </div>

        <div class="stat-value">
            {{ number_format($statistics['pending']) }}
        </div>

        <div class="stat-note">
            Order yang masih berstatus pending.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Order Lunas
        </div>

        <div class="stat-value">
            {{ number_format($statistics['paid']) }}
        </div>

        <div class="stat-note">
            Transaksi yang sudah berhasil dibayar.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Pendapatan Lunas
        </div>

        <div class="stat-value money">
            Rp {{ number_format(
                (float) $statistics['paid_revenue'],
                0,
                ',',
                '.'
            ) }}
        </div>

        <div class="stat-note">
            Berdasarkan total order berstatus lunas.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Dibatalkan
        </div>

        <div class="stat-value">
            {{ number_format($statistics['cancelled']) }}
        </div>

        <div class="stat-note">
            Order yang telah dibatalkan.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Kedaluwarsa
        </div>

        <div class="stat-value">
            {{ number_format($statistics['expired']) }}
        </div>

        <div class="stat-note">
            Order yang melewati batas pembayaran.
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Refund
        </div>

        <div class="stat-value">
            {{ number_format($statistics['refunded']) }}
        </div>

        <div class="stat-note">
            Order dengan dana dikembalikan.
        </div>
    </div>

</div>


<div class="card filter-card">

    <form
        method="GET"
        action="{{ route('admin.orders.index') }}"
        class="filter-form"
    >

        <div class="form-group">
            <label
                for="search"
                class="form-label"
            >
                Pencarian
            </label>

            <input
                type="text"
                id="search"
                name="search"
                class="form-control"
                value="{{ $search }}"
                placeholder="Kode order, customer, peserta, paket..."
            >
        </div>


        <div class="form-group">
            <label
                for="status"
                class="form-label"
            >
                Status Order
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
                    {{ $status === 'pending' ? 'selected' : '' }}
                >
                    Menunggu Pembayaran
                </option>

                <option
                    value="paid"
                    {{ $status === 'paid' ? 'selected' : '' }}
                >
                    Lunas
                </option>

                <option
                    value="cancelled"
                    {{ $status === 'cancelled' ? 'selected' : '' }}
                >
                    Dibatalkan
                </option>

                <option
                    value="expired"
                    {{ $status === 'expired' ? 'selected' : '' }}
                >
                    Kedaluwarsa
                </option>

                <option
                    value="refunded"
                    {{ $status === 'refunded' ? 'selected' : '' }}
                >
                    Dikembalikan
                </option>
            </select>
        </div>


        <div class="form-group">
            <label
                for="date"
                class="form-label"
            >
                Tanggal Order
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
                class="filter-button"
            >
                Terapkan
            </button>

            <a
                href="{{ route('admin.orders.index') }}"
                class="reset-button"
            >
                Reset
            </a>

        </div>

    </form>

</div>


<div class="card table-card">

    <div class="table-heading">

        <div>
            <div class="table-heading-title">
                Daftar Transaksi
            </div>

            <div class="table-heading-note">
                Data order terbaru ditampilkan terlebih dahulu.
            </div>
        </div>

        <div class="table-result-count">
            {{ $orders->total() }} Order
        </div>

    </div>


    @if ($orders->count() > 0)

        <div class="table-responsive">

            <table class="order-table">

                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Peserta & Paket</th>
                        <th>Total</th>
                        <th>Status Order</th>
                        <th>Pembayaran</th>
                        <th>Invoice</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($orders as $order)

                        @php
                            $firstItem = $order->items->first();

                            $latestPayment = $order->payments
                                ->sortByDesc('id')
                                ->first();

                            $orderStatusClass =
                                'status-' . $order->status;

                            $paymentStatusClass = $latestPayment
                                ? 'status-' . $latestPayment->status
                                : null;
                        @endphp

                        <tr>

                            <td>
                                <div class="order-code">
                                    {{ $order->order_code }}
                                </div>

                                <div class="order-date">
                                    @if ($order->ordered_at)
                                        {{ $order->ordered_at->format('d M Y') }}
                                        <br>
                                        {{ $order->ordered_at->format('H:i') }} WIB
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>


                            <td>
                                <div class="customer-name">
                                    {{ $order->user?->name ?? '-' }}
                                </div>

                                <div class="customer-email">
                                    {{ $order->user?->email ?? '-' }}
                                </div>
                            </td>


                            <td>
                                @if ($firstItem)

                                    <div class="item-main">
                                        {{ $firstItem->participant?->full_name ?? '-' }}
                                    </div>

                                    <div class="item-sub">
                                        {{ $firstItem->package?->name ?? $firstItem->package_name ?? '-' }}

                                        @if ($firstItem->package?->program)
                                            <br>
                                            {{ $firstItem->package->program->name }}
                                        @endif
                                    </div>

                                    @if ($order->items_count > 1)
                                        <span class="more-items">
                                            +{{ $order->items_count - 1 }} item lainnya
                                        </span>
                                    @endif

                                @else

                                    <span class="invoice-empty">
                                        Tidak ada item
                                    </span>

                                @endif
                            </td>


                            <td>
                                <div class="amount">
                                    {{ $order->formatted_total }}
                                </div>

                                @if ((float) $order->discount_amount > 0)
                                    <div class="discount-text">
                                        Diskon {{ $order->formatted_discount }}
                                    </div>
                                @endif
                            </td>


                            <td>
                                <span class="status-badge {{ $orderStatusClass }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>

                                @if ($order->paid_at)
                                    <div class="order-date">
                                        Dibayar
                                        {{ $order->paid_at->format('d M Y H:i') }}
                                    </div>
                                @endif
                            </td>


                            <td>
                                @if ($latestPayment)

                                    <span class="status-badge {{ $paymentStatusClass }}">
                                        {{ $paymentStatusLabels[$latestPayment->status] ?? $latestPayment->status }}
                                    </span>

                                    <div class="payment-name">
                                        {{ $latestPayment->payment_display }}
                                    </div>

                                    <div class="payment-code">
                                        {{ $latestPayment->payment_code }}
                                    </div>

                                @else

                                    <span class="invoice-empty">
                                        Belum ada pembayaran
                                    </span>

                                @endif
                            </td>


                            <td>
                                @if ($order->invoice)

                                    <div class="invoice-number">
                                        {{ $order->invoice->invoice_number }}
                                    </div>

                                    <div class="order-date">
                                        {{ ucfirst($order->invoice->status) }}
                                    </div>

                                @else

                                    <span class="invoice-empty">
                                        Belum tersedia
                                    </span>

                                @endif
                            </td>


                            <td>
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="detail-button"
                                >
                                    Lihat Detail
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        @if ($orders->hasPages())

            <div class="pagination-wrapper">
                {{ $orders->links() }}
            </div>

        @endif

    @else

        <div class="empty-state">

            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="14"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M3 9H21"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </div>

            <div class="empty-title">
                Order tidak ditemukan
            </div>

            <div class="empty-description">
                Belum ada transaksi yang sesuai dengan
                pencarian atau filter yang dipilih.
            </div>

        </div>

    @endif

</div>

@endsection
