
@extends('layouts.customer')

@section('title', 'Riwayat Invoice')
@section('topbar-title', 'Riwayat Invoice')

@section('content')

@php
    $totalInvoices = $invoices->total();
@endphp

<div class="invoice-page">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="invoice-page-header">

        <div class="invoice-header-content">

            <span class="invoice-eyebrow">
                TRANSAKSI CUSTOMER
            </span>

            <h1 class="invoice-page-title">
                Riwayat Invoice
            </h1>

            <p class="invoice-page-description">
                Lihat seluruh invoice pembelian paket renang Anda.
                Invoice dapat dibuka dan dicetak kapan saja.
            </p>

        </div>

        <a
            href="{{ route('customer.orders.index') }}"
            class="invoice-back-button"
        >
            ← Riwayat Pesanan
        </a>

    </div>


    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div class="invoice-summary-card">

        <div class="invoice-summary-icon">
            📄
        </div>

        <div class="invoice-summary-content">

            <span>
                Total Invoice
            </span>

            <strong>
                {{ number_format($totalInvoices, 0, ',', '.') }}
            </strong>

            <p>
                Invoice yang tersedia di akun Anda
            </p>

        </div>

    </div>


    {{-- =========================================================
        INVOICE LIST
    ========================================================== --}}

    <section class="invoice-list-card">

        <div class="invoice-section-heading">

            <div>

                <span class="invoice-section-label">
                    DOKUMEN PEMBAYARAN
                </span>

                <h2>
                    Daftar Invoice
                </h2>

                <p>
                    Invoice diurutkan dari yang terbaru.
                </p>

            </div>

        </div>


        @if ($invoices->isNotEmpty())

            <div class="invoice-table-wrapper">

                <table class="invoice-table">

                    <thead>

                        <tr>

                            <th>
                                Invoice
                            </th>

                            <th>
                                Pesanan
                            </th>

                            <th>
                                Tanggal Terbit
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="invoice-amount-heading">
                                Total
                            </th>

                            <th class="invoice-action-heading">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($invoices as $invoice)

                            <tr>

                                <td>

                                    <div class="invoice-number-cell">

                                        <span class="invoice-document-icon">
                                            📄
                                        </span>

                                        <div>

                                            <strong>
                                                {{ $invoice->invoice_number }}
                                            </strong>

                                            <span>
                                                Invoice Pembayaran
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="invoice-order-cell">

                                        <strong>
                                            {{ $invoice->order?->order_code ?? '-' }}
                                        </strong>

                                        @if ($invoice->order)

                                            <a
                                                href="{{ route('customer.orders.show', $invoice->order) }}"
                                            >
                                                Detail Pesanan
                                            </a>

                                        @endif

                                    </div>

                                </td>


                                <td>

                                    <span class="invoice-date">
                                        {{ $invoice->formatted_issued_at }}
                                    </span>

                                </td>


                                <td>

                                    @switch($invoice->status)

                                        @case('paid')

                                            <span class="invoice-status paid">
                                                <span class="status-dot"></span>
                                                Lunas
                                            </span>

                                            @break


                                        @case('unpaid')

                                            <span class="invoice-status unpaid">
                                                <span class="status-dot"></span>
                                                Belum Dibayar
                                            </span>

                                            @break


                                        @case('cancelled')

                                            <span class="invoice-status cancelled">
                                                <span class="status-dot"></span>
                                                Dibatalkan
                                            </span>

                                            @break


                                        @case('refunded')

                                            <span class="invoice-status refunded">
                                                <span class="status-dot"></span>
                                                Dikembalikan
                                            </span>

                                            @break


                                        @default

                                            <span class="invoice-status default">
                                                <span class="status-dot"></span>
                                                {{ $invoice->status_label }}
                                            </span>

                                    @endswitch

                                </td>


                                <td class="invoice-amount-cell">

                                    <strong>
                                        {{ $invoice->formatted_total }}
                                    </strong>

                                </td>


                                <td class="invoice-action-cell">

                                    <a
                                        href="{{ route('customer.invoices.show', $invoice) }}"
                                        class="invoice-view-button"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <span>
                                            📄
                                        </span>

                                        Lihat / Cetak

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                PAGINATION
            ================================================== --}}

            @if ($invoices->hasPages())

                <div class="invoice-pagination">

                    <div class="pagination-information">

                        Menampilkan

                        <strong>
                            {{ $invoices->firstItem() }}
                        </strong>

                        sampai

                        <strong>
                            {{ $invoices->lastItem() }}
                        </strong>

                        dari

                        <strong>
                            {{ $invoices->total() }}
                        </strong>

                        invoice

                    </div>


                    <div class="pagination-buttons">

                        @if ($invoices->onFirstPage())

                            <span class="pagination-button disabled">
                                ← Sebelumnya
                            </span>

                        @else

                            <a
                                href="{{ $invoices->previousPageUrl() }}"
                                class="pagination-button"
                            >
                                ← Sebelumnya
                            </a>

                        @endif


                        <span class="pagination-current">

                            Halaman
                            {{ $invoices->currentPage() }}
                            dari
                            {{ $invoices->lastPage() }}

                        </span>


                        @if ($invoices->hasMorePages())

                            <a
                                href="{{ $invoices->nextPageUrl() }}"
                                class="pagination-button"
                            >
                                Berikutnya →
                            </a>

                        @else

                            <span class="pagination-button disabled">
                                Berikutnya →
                            </span>

                        @endif

                    </div>

                </div>

            @endif


        @else

            {{-- =================================================
                EMPTY STATE
            ================================================== --}}

            <div class="invoice-empty-state">

                <div class="invoice-empty-icon">
                    📄
                </div>

                <h3>
                    Belum Ada Invoice
                </h3>

                <p>
                    Invoice akan muncul di halaman ini setelah
                    invoice pembayaran paket renang diterbitkan.
                </p>

                <a
                    href="{{ route('customer.orders.index') }}"
                    class="invoice-empty-button"
                >
                    Lihat Riwayat Pesanan
                </a>

            </div>

        @endif

    </section>


    {{-- =========================================================
        INFORMATION
    ========================================================== --}}

    <div class="invoice-information-box">

        <div class="invoice-information-icon">
            ℹ️
        </div>

        <div>

            <strong>
                Informasi Invoice
            </strong>

            <p>
                Klik tombol Lihat / Cetak untuk membuka invoice
                pada tab baru. Anda dapat mencetaknya atau
                menyimpannya sebagai PDF melalui menu cetak browser.
            </p>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

    /* =========================================================
        PAGE
    ========================================================== */

    .invoice-page {
        width: 100%;
        padding-bottom: 30px;
    }


    /* =========================================================
        HEADER
    ========================================================== */

    .invoice-page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .invoice-header-content {
        min-width: 0;
    }

    .invoice-eyebrow {
        display: block;
        margin-bottom: 7px;
        color: #0284c7;
        font-size: 9px;
        font-weight: 850;
        letter-spacing: 0.8px;
    }

    .invoice-page-title {
        margin: 0;
        color: var(--navy, #0f172a);
        font-size: 25px;
        font-weight: 900;
        line-height: 1.3;
    }

    .invoice-page-description {
        max-width: 580px;
        margin-top: 7px;
        color: var(--text-soft, #64748b);
        font-size: 11px;
        line-height: 1.7;
    }

    .invoice-back-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 39px;
        flex-shrink: 0;
        padding: 9px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #334155;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .invoice-back-button:hover {
        border-color: #0284c7;
        background: #f0f9ff;
        color: #0369a1;
    }


    /* =========================================================
        SUMMARY
    ========================================================== */

    .invoice-summary-card {
        display: flex;
        align-items: center;
        gap: 15px;
        max-width: 350px;
        margin-bottom: 20px;
        padding: 19px;
        border: 1px solid #bae6fd;
        border-radius: 14px;
        background: linear-gradient(
            135deg,
            #f0f9ff,
            #ffffff
        );
    }

    .invoice-summary-icon {
        width: 53px;
        height: 53px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 13px;
        background: #e0f2fe;
        font-size: 23px;
    }

    .invoice-summary-content > span {
        display: block;
        color: #0369a1;
        font-size: 9px;
        font-weight: 800;
    }

    .invoice-summary-content > strong {
        display: block;
        margin-top: 3px;
        color: #0f172a;
        font-size: 25px;
        font-weight: 900;
        line-height: 1.2;
    }

    .invoice-summary-content > p {
        margin-top: 4px;
        color: #64748b;
        font-size: 9px;
        line-height: 1.5;
    }


    /* =========================================================
        LIST CARD
    ========================================================== */

    .invoice-list-card {
        overflow: hidden;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .invoice-section-heading {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .invoice-section-label {
        display: block;
        margin-bottom: 5px;
        color: #0284c7;
        font-size: 8px;
        font-weight: 850;
        letter-spacing: 0.6px;
    }

    .invoice-section-heading h2 {
        margin: 0;
        color: var(--navy, #0f172a);
        font-size: 15px;
        font-weight: 900;
    }

    .invoice-section-heading p {
        margin-top: 5px;
        color: #64748b;
        font-size: 9px;
    }


    /* =========================================================
        TABLE
    ========================================================== */

    .invoice-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .invoice-table thead {
        background: #f8fafc;
    }

    .invoice-table th {
        padding: 13px 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 8px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .invoice-table td {
        padding: 16px 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .invoice-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .invoice-table tbody tr:hover {
        background: #fafcff;
    }


    /* =========================================================
        INVOICE NUMBER
    ========================================================== */

    .invoice-number-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 155px;
    }

    .invoice-document-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #e0f2fe;
        font-size: 15px;
    }

    .invoice-number-cell strong {
        display: block;
        color: #0f172a;
        font-size: 10px;
        font-weight: 850;
        overflow-wrap: anywhere;
    }

    .invoice-number-cell > div > span {
        display: block;
        margin-top: 4px;
        color: #94a3b8;
        font-size: 8px;
    }


    /* =========================================================
        ORDER
    ========================================================== */

    .invoice-order-cell strong {
        display: block;
        color: #334155;
        font-size: 9px;
        font-weight: 850;
        overflow-wrap: anywhere;
    }

    .invoice-order-cell a {
        display: inline-block;
        margin-top: 5px;
        color: #0284c7;
        font-size: 8px;
        font-weight: 800;
        text-decoration: none;
    }

    .invoice-order-cell a:hover {
        text-decoration: underline;
    }


    /* =========================================================
        DATE
    ========================================================== */

    .invoice-date {
        color: #475569;
        font-size: 9px;
        font-weight: 750;
        white-space: nowrap;
    }


    /* =========================================================
        STATUS
    ========================================================== */

    .invoice-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 850;
        white-space: nowrap;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        flex-shrink: 0;
        border-radius: 50%;
        background: currentColor;
    }

    .invoice-status.paid {
        background: #dcfce7;
        color: #15803d;
    }

    .invoice-status.unpaid {
        background: #fef3c7;
        color: #b45309;
    }

    .invoice-status.cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .invoice-status.refunded {
        background: #e0f2fe;
        color: #0369a1;
    }

    .invoice-status.default {
        background: #f1f5f9;
        color: #64748b;
    }


    /* =========================================================
        AMOUNT
    ========================================================== */

    .invoice-amount-heading,
    .invoice-amount-cell {
        text-align: right;
    }

    .invoice-amount-cell strong {
        color: #0f172a;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }


    /* =========================================================
        ACTION
    ========================================================== */

    .invoice-action-heading,
    .invoice-action-cell {
        text-align: center;
    }

    .invoice-view-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 35px;
        padding: 8px 11px;
        border: 1px solid #0284c7;
        border-radius: 8px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 9px;
        font-weight: 850;
        text-decoration: none;
        white-space: nowrap;
        transition: 0.2s ease;
    }

    .invoice-view-button:hover {
        background: #0284c7;
        color: #ffffff;
    }

    .invoice-view-button span {
        font-size: 12px;
    }


    /* =========================================================
        PAGINATION
    ========================================================== */

    .invoice-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 17px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .pagination-information {
        color: #64748b;
        font-size: 9px;
        line-height: 1.5;
    }

    .pagination-information strong {
        color: #334155;
        font-weight: 850;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pagination-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 8px 11px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 9px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    a.pagination-button:hover {
        border-color: #0284c7;
        background: #f0f9ff;
        color: #0369a1;
    }

    .pagination-button.disabled {
        background: #f8fafc;
        color: #cbd5e1;
        cursor: not-allowed;
    }

    .pagination-current {
        color: #64748b;
        font-size: 9px;
        font-weight: 750;
        white-space: nowrap;
    }


    /* =========================================================
        EMPTY STATE
    ========================================================== */

    .invoice-empty-state {
        display: flex;
        align-items: center;
        flex-direction: column;
        padding: 65px 20px;
        text-align: center;
    }

    .invoice-empty-icon {
        width: 75px;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        border-radius: 20px;
        background: #f0f9ff;
        font-size: 33px;
    }

    .invoice-empty-state h3 {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .invoice-empty-state p {
        max-width: 370px;
        margin-top: 8px;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .invoice-empty-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        margin-top: 20px;
        padding: 10px 16px;
        border-radius: 9px;
        background: linear-gradient(
            135deg,
            var(--blue, #0284c7),
            var(--blue-dark, #0369a1)
        );
        color: #ffffff;
        font-size: 10px;
        font-weight: 850;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .invoice-empty-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(2, 132, 199, 0.2);
    }


    /* =========================================================
        INFORMATION
    ========================================================== */

    .invoice-information-box {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-top: 18px;
        padding: 15px;
        border: 1px solid #bae6fd;
        border-radius: 11px;
        background: #f0f9ff;
    }

    .invoice-information-icon {
        width: 29px;
        height: 29px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        background: #e0f2fe;
        font-size: 13px;
    }

    .invoice-information-box strong {
        display: block;
        color: #075985;
        font-size: 10px;
        font-weight: 850;
    }

    .invoice-information-box p {
        max-width: 700px;
        margin-top: 4px;
        color: #0369a1;
        font-size: 9px;
        line-height: 1.7;
    }


    /* =========================================================
        RESPONSIVE
    ========================================================== */

    @media (max-width: 1050px) {

        .invoice-table {
            min-width: 850px;
        }

    }


    @media (max-width: 650px) {

        .invoice-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .invoice-page-title {
            font-size: 22px;
        }

        .invoice-summary-card {
            max-width: none;
        }

        .invoice-pagination {
            align-items: flex-start;
            flex-direction: column;
        }

        .pagination-buttons {
            width: 100%;
            justify-content: space-between;
        }

    }


    @media (max-width: 480px) {

        .invoice-section-heading {
            padding: 16px;
        }

        .invoice-summary-card {
            padding: 15px;
        }

        .invoice-information-box {
            padding: 12px;
        }

        .pagination-buttons {
            flex-wrap: wrap;
        }

        .pagination-current {
            order: -1;
            width: 100%;
            text-align: center;
        }

    }

</style>

@endpush
