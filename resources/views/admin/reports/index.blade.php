@extends('layouts.admin')

@section('title', 'Laporan Transaksi')

@section('topbar-title', 'Laporan Transaksi')

@push('styles')
<style>
    .report-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .report-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .report-heading h1 {
        color: #082f49;
        font-size: 27px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .report-heading p {
        margin-top: 7px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .report-filter,
    .report-table-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .report-filter {
        padding: 22px;
    }

    .report-section-title {
        margin-bottom: 17px;
        color: #082f49;
        font-size: 16px;
        font-weight: 800;
    }

    .report-filter-form {
        display: flex;
        align-items: flex-end;
        gap: 15px;
        flex-wrap: wrap;
    }

    .report-field {
        flex: 1 1 200px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .report-field label {
        color: #334155;
        font-size: 12px;
        font-weight: 750;
    }

    .report-field input {
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        font-size: 13px;
    }

    .report-field input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .report-filter-actions {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }

    .report-button {
        min-height: 43px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 17px;
        border: 1px solid transparent;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 750;
        cursor: pointer;
    }

    .report-button-primary {
        background: #0284c7;
        color: #ffffff;
    }

    .report-button-primary:hover {
        background: #0369a1;
    }

    .report-button-secondary {
        border-color: #e2e8f0;
        background: #ffffff;
        color: #334155;
    }

    .report-button-secondary:hover {
        background: #f8fafc;
    }

    .report-button-export {
        background: #15803d;
        color: #ffffff;
    }

    .report-button-export:hover {
        background: #166534;
    }

    .report-period {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border: 1px solid #bae6fd;
        border-radius: 9px;
        background: #f0f9ff;
        color: #075985;
        font-size: 12px;
        font-weight: 700;
    }

    .report-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .report-stat {
        position: relative;
        overflow: hidden;
        min-height: 125px;
        padding: 21px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .report-stat::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #0284c7;
        content: '';
    }

    .report-stat.green::before {
        background: #16a34a;
    }

    .report-stat.amber::before {
        background: #d97706;
    }

    .report-stat.red::before {
        background: #dc2626;
    }

    .report-stat-label {
        margin-bottom: 12px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
    }

    .report-stat-value {
        overflow-wrap: anywhere;
        color: #082f49;
        font-size: 25px;
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    .report-stat-value.money {
        font-size: 22px;
    }

    .report-stat-note {
        margin-top: 9px;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.5;
    }

    .report-table-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        padding: 22px;
        border-bottom: 1px solid #e2e8f0;
    }

    .report-table-heading h2 {
        color: #082f49;
        font-size: 17px;
        font-weight: 800;
    }

    .report-table-heading p {
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
    }

    .report-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .report-table thead {
        background: #f8fafc;
    }

    .report-table th {
        padding: 15px 18px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    .report-table td {
        padding: 17px 18px;
        border-top: 1px solid #f1f5f9;
        color: #334155;
        font-size: 12px;
        vertical-align: middle;
    }

    .report-table tbody tr:hover {
        background: #f8fafc;
    }

    .report-order-code {
        color: #0369a1;
        font-weight: 800;
        text-decoration: none;
    }

    .report-order-code:hover {
        text-decoration: underline;
    }

    .report-customer-name {
        color: #0f172a;
        font-weight: 750;
    }

    .report-customer-email {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .report-money {
        color: #082f49;
        font-weight: 800;
        white-space: nowrap;
    }

    .report-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .report-badge.paid {
        background: #dcfce7;
        color: #166534;
    }

    .report-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .report-badge.cancelled,
    .report-badge.expired,
    .report-badge.refunded {
        background: #fee2e2;
        color: #991b1b;
    }

    .report-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .report-action-link {
        color: #0284c7;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
    }

    .report-action-link:hover {
        text-decoration: underline;
    }

    .report-empty {
        padding: 60px 22px;
        text-align: center;
    }

    .report-empty-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        border-radius: 16px;
        background: #f0f9ff;
        color: #0284c7;
    }

    .report-empty h3 {
        margin-bottom: 7px;
        color: #082f49;
        font-size: 16px;
    }

    .report-empty p {
        color: #64748b;
        font-size: 13px;
    }

    .report-pagination {
        padding: 18px 22px;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 1150px) {
        .report-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .report-heading h1 {
            font-size: 23px;
        }

        .report-filter {
            padding: 17px;
        }

        .report-filter-actions,
        .report-filter-actions .report-button {
            width: 100%;
        }

        .report-stats {
            grid-template-columns: 1fr;
        }

        .report-stat {
            min-height: 105px;
        }

        .report-table-heading {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')

@php
    $statusLabels = [
        'pending' => 'Menunggu',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Refund',
    ];

    $formatRupiah = fn ($amount) =>
        'Rp ' . number_format((float) $amount, 0, ',', '.');

    $formattedStartDate = \Carbon\Carbon::parse($startDate)
        ->translatedFormat('d F Y');

    $formattedEndDate = \Carbon\Carbon::parse($endDate)
        ->translatedFormat('d F Y');
@endphp

<div class="report-page">

    <div class="report-heading">
        <div>
            <h1>Laporan Transaksi</h1>
            <p>
                Pantau transaksi, status order, dan pendapatan
                Ragen Swim Course berdasarkan periode.
            </p>
        </div>

        <div class="report-period">
            {{ $formattedStartDate }} — {{ $formattedEndDate }}
        </div>
    </div>


    {{-- FILTER PERIODE --}}

    <section class="report-filter">
        <h2 class="report-section-title">Filter Periode</h2>

        <form
            action="{{ route('admin.reports.index') }}"
            method="GET"
            class="report-filter-form"
        >
            <div class="report-field">
                <label for="start_date">Tanggal Mulai</label>

                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ $startDate }}"
                    required
                >
            </div>

            <div class="report-field">
                <label for="end_date">Tanggal Akhir</label>

                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ $endDate }}"
                    required
                >
            </div>

            <div class="report-filter-actions">
                <button
                    type="submit"
                    class="report-button report-button-primary"
                >
                    Terapkan Filter
                </button>

                <a
                    href="{{ route('admin.reports.index') }}"
                    class="report-button report-button-secondary"
                >
                    Reset
                </a>

                <button
                    type="submit"
                    formaction="{{ route('admin.reports.export') }}"
                    class="report-button report-button-export"
                >
                    ↓ Unduh CSV
                </button>
            </div>
        </form>
    </section>


    {{-- RINGKASAN TRANSAKSI --}}

    <section>
        <h2 class="report-section-title">Ringkasan Transaksi</h2>

        <div class="report-stats">

            <div class="report-stat green">
                <div class="report-stat-label">
                    Pendapatan Periode
                </div>

                <div class="report-stat-value money">
                    {{ $formatRupiah($statistics['period_revenue']) }}
                </div>

                <div class="report-stat-note">
                    Order lunas berdasarkan tanggal pembayaran
                </div>
            </div>

            <div class="report-stat">
                <div class="report-stat-label">
                    Total Order Dibuat
                </div>

                <div class="report-stat-value">
                    {{ number_format($statistics['total_orders'], 0, ',', '.') }}
                </div>

                <div class="report-stat-note">
                    Order yang dibuat dalam periode terpilih
                </div>
            </div>

            <div class="report-stat green">
                <div class="report-stat-label">
                    Order Lunas
                </div>

                <div class="report-stat-value">
                    {{ number_format($statistics['paid_orders'], 0, ',', '.') }}
                </div>

                <div class="report-stat-note">
                    Berdasarkan tanggal pembuatan order
                </div>
            </div>

            <div class="report-stat amber">
                <div class="report-stat-label">
                    Order Menunggu
                </div>

                <div class="report-stat-value">
                    {{ number_format($statistics['pending_orders'], 0, ',', '.') }}
                </div>

                <div class="report-stat-note">
                    Belum berstatus lunas
                </div>
            </div>

            <div class="report-stat">
                <div class="report-stat-label">
                    Nilai Order Lunas
                </div>

                <div class="report-stat-value money">
                    {{ $formatRupiah($statistics['paid_order_value']) }}
                </div>

                <div class="report-stat-note">
                    Order dibuat dalam periode terpilih
                </div>
            </div>

            <div class="report-stat green">
                <div class="report-stat-label">
                    Pembayaran Berhasil
                </div>

                <div class="report-stat-value">
                    {{ number_format($statistics['period_paid_orders'], 0, ',', '.') }}
                </div>

                <div class="report-stat-note">
                    Order lunas dengan tanggal pembayaran dalam periode
                </div>
            </div>

            <div class="report-stat red">
                <div class="report-stat-label">
                    Order Dibatalkan
                </div>

                <div class="report-stat-value">
                    {{ number_format($statistics['cancelled_orders'], 0, ',', '.') }}
                </div>

                <div class="report-stat-note">
                    Berdasarkan tanggal pembuatan order
                </div>
            </div>

            <div class="report-stat">
                <div class="report-stat-label">
                    Kedaluwarsa / Refund
                </div>

                <div class="report-stat-value">
                    {{
                        number_format(
                            $statistics['expired_orders']
                            + $statistics['refunded_orders'],
                            0,
                            ',',
                            '.'
                        )
                    }}
                </div>

                <div class="report-stat-note">
                    {{ $statistics['expired_orders'] }} kedaluwarsa,
                    {{ $statistics['refunded_orders'] }} refund
                </div>
            </div>

        </div>
    </section>


    {{-- TABEL TRANSAKSI --}}

    <section class="report-table-card">

        <div class="report-table-heading">
            <div>
                <h2>Daftar Transaksi</h2>

                <p>
                    {{ number_format($orders->total(), 0, ',', '.') }}
                    order ditemukan dalam periode ini.
                </p>
            </div>
        </div>

        @if ($orders->count() > 0)

            <div class="report-table-wrap">
                <table class="report-table">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal Order</th>
                            <th>Kode Order</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)

                            <tr>
                                <td>
                                    {{ $orders->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{
                                        $order->ordered_at
                                            ? \Carbon\Carbon::parse(
                                                $order->ordered_at
                                            )->format('d/m/Y H:i')
                                            : '-'
                                    }}
                                </td>

                                <td>
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="report-order-code"
                                    >
                                        {{ $order->order_code }}
                                    </a>
                                </td>

                                <td>
                                    <div class="report-customer-name">
                                        {{ $order->user?->name ?? 'Customer tidak tersedia' }}
                                    </div>

                                    <div class="report-customer-email">
                                        {{ $order->user?->email ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="report-money">
                                        {{ $formatRupiah($order->total) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="report-badge {{ $order->status }}">
                                        {{
                                            $statusLabels[$order->status]
                                            ?? ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $order->status
                                                )
                                            )
                                        }}
                                    </span>
                                </td>

                                <td>
                                    {{
                                        $order->paid_at
                                            ? \Carbon\Carbon::parse(
                                                $order->paid_at
                                            )->format('d/m/Y H:i')
                                            : '-'
                                    }}
                                </td>

                                <td>
                                    <div class="report-actions">

                                        <a
                                            href="{{ route('admin.orders.show', $order) }}"
                                            class="report-action-link"
                                        >
                                            Detail
                                        </a>

                                        @if ($order->invoice)
                                            <a
                                                href="{{ route('admin.invoices.show', $order->invoice) }}"
                                                class="report-action-link"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                Invoice ↗
                                            </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>

            @if ($orders->hasPages())
                <div class="report-pagination">
                    {{ $orders->links() }}
                </div>
            @endif

        @else

            <div class="report-empty">
                <div class="report-empty-icon">
                    <svg
                        width="28"
                        height="28"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <rect
                            x="4"
                            y="3"
                            width="16"
                            height="18"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M8 8H16M8 12H16M8 16H13"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h3>Belum ada transaksi</h3>

                <p>
                    Tidak ditemukan order dalam periode yang dipilih.
                    Silakan ubah filter tanggal.
                </p>
            </div>

        @endif

    </section>

</div>

@endsection
