@extends('layouts.admin')

@section('title', 'Riwayat Invoice')
@section('topbar-title', 'Riwayat Invoice')

@push('styles')
<style>
    .invoice-page {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .invoice-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .invoice-title {
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        letter-spacing: -0.6px;
    }

    .invoice-subtitle {
        max-width: 650px;
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.7;
    }

    .invoice-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 15px;
    }

    .invoice-summary-card {
        min-width: 0;
        padding: 19px;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: var(--white);
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .invoice-summary-label {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .invoice-summary-value {
        margin-top: 9px;
        color: var(--navy);
        font-size: 25px;
        font-weight: 850;
    }

    .invoice-summary-card.unpaid .invoice-summary-value {
        color: #c2410c;
    }

    .invoice-summary-card.paid .invoice-summary-value {
        color: #15803d;
    }

    .invoice-summary-card.cancelled .invoice-summary-value {
        color: #dc2626;
    }

    .invoice-summary-card.refunded .invoice-summary-value {
        color: #7e22ce;
    }

    .invoice-content-card {
        overflow: hidden;
    }

    .invoice-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        padding: 19px 21px;
        border-bottom: 1px solid var(--border);
    }

    .invoice-card-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .invoice-card-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.6;
    }

    .invoice-filter-form {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 180px 180px auto;
        gap: 12px;
        align-items: end;
        padding: 19px 21px;
        border-bottom: 1px solid var(--border);
        background: #fbfdff;
    }

    .invoice-form-group {
        min-width: 0;
    }

    .invoice-form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .invoice-form-input,
    .invoice-form-select {
        width: 100%;
        min-height: 41px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        color: var(--text);
        font: inherit;
        font-size: 11px;
        outline: none;
    }

    .invoice-form-input:focus,
    .invoice-form-select:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.08);
    }

    .invoice-filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .invoice-button {
        min-height: 41px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 15px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .invoice-button-primary {
        background: var(--blue);
        color: #fff;
    }

    .invoice-button-primary:hover {
        background: var(--blue-dark);
    }

    .invoice-button-outline {
        border-color: var(--border);
        background: #fff;
        color: var(--navy);
    }

    .invoice-button-outline:hover {
        background: #f1f5f9;
    }

    .invoice-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .invoice-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .invoice-table th {
        padding: 14px 17px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 9px;
        font-weight: 850;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .invoice-table td {
        padding: 17px;
        border-bottom: 1px solid #eef2f7;
        color: var(--text);
        font-size: 11px;
        vertical-align: middle;
    }

    .invoice-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .invoice-table tbody tr:hover {
        background: #fbfdff;
    }

    .invoice-number {
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
        word-break: break-word;
    }

    .invoice-secondary {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.5;
    }

    .invoice-customer {
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
    }

    .invoice-amount {
        color: var(--navy);
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .invoice-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 26px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 850;
        white-space: nowrap;
    }

    .invoice-status-unpaid {
        background: #fff7ed;
        color: #c2410c;
    }

    .invoice-status-paid {
        background: #f0fdf4;
        color: #15803d;
    }

    .invoice-status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .invoice-status-refunded {
        background: #faf5ff;
        color: #7e22ce;
    }

    .invoice-status-default {
        background: #f1f5f9;
        color: #64748b;
    }

    .invoice-table-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .invoice-action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 10px;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 9px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .invoice-action-link:hover {
        background: #e0f2fe;
    }

    .invoice-action-link.secondary {
        border-color: var(--border);
        background: #fff;
        color: var(--text-soft);
    }

    .invoice-action-link.secondary:hover {
        background: #f8fafc;
        color: var(--navy);
    }

    .invoice-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .invoice-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        margin: 0 auto 15px;
        border-radius: 18px;
        background: #f0f9ff;
        color: #0284c7;
    }

    .invoice-empty-icon svg {
        width: 30px;
        height: 30px;
    }

    .invoice-empty-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 850;
    }

    .invoice-empty-description {
        max-width: 400px;
        margin: 7px auto 0;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.7;
    }

    .invoice-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        padding: 17px 21px;
        border-top: 1px solid var(--border);
    }

    .invoice-result-count {
        color: var(--text-soft);
        font-size: 10px;
    }

    .invoice-pagination {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .invoice-page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        min-height: 32px;
        padding: 6px 9px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #fff;
        color: var(--text);
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
    }

    .invoice-page-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .invoice-page-link.active {
        border-color: var(--blue);
        background: var(--blue);
        color: #fff;
    }

    .invoice-page-link.disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    @media (max-width: 1200px) {
        .invoice-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .invoice-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .invoice-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .invoice-filter-form {
            grid-template-columns: 1fr;
        }

        .invoice-filter-actions {
            flex-wrap: wrap;
        }

        .invoice-header {
            display: block;
        }

        .invoice-title {
            font-size: 23px;
        }
    }

    @media (max-width: 480px) {
        .invoice-summary-grid {
            grid-template-columns: 1fr;
        }

        .invoice-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .invoice-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush


@section('content')

@php
    $statusOptions = [
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
        'refunded' => 'Dikembalikan',
    ];
@endphp

<div class="invoice-page">

    {{-- HEADER --}}
    <div class="invoice-header">

        <div>
            <h1 class="invoice-title">
                Riwayat Invoice
            </h1>

            <p class="invoice-subtitle">
                Kelola dan pantau seluruh invoice transaksi
                pembelian paket renang dari customer.
                Gunakan pencarian atau filter untuk menemukan
                invoice tertentu.
            </p>
        </div>

    </div>


    {{-- STATISTICS --}}
    <div class="invoice-summary-grid">

        <div class="invoice-summary-card">
            <div class="invoice-summary-label">
                Total Invoice
            </div>

            <div class="invoice-summary-value">
                {{ number_format($statistics['total'], 0, ',', '.') }}
            </div>
        </div>

        <div class="invoice-summary-card unpaid">
            <div class="invoice-summary-label">
                Belum Dibayar
            </div>

            <div class="invoice-summary-value">
                {{ number_format($statistics['unpaid'], 0, ',', '.') }}
            </div>
        </div>

        <div class="invoice-summary-card paid">
            <div class="invoice-summary-label">
                Lunas
            </div>

            <div class="invoice-summary-value">
                {{ number_format($statistics['paid'], 0, ',', '.') }}
            </div>
        </div>

        <div class="invoice-summary-card cancelled">
            <div class="invoice-summary-label">
                Dibatalkan
            </div>

            <div class="invoice-summary-value">
                {{ number_format($statistics['cancelled'], 0, ',', '.') }}
            </div>
        </div>

        <div class="invoice-summary-card refunded">
            <div class="invoice-summary-label">
                Dikembalikan
            </div>

            <div class="invoice-summary-value">
                {{ number_format($statistics['refunded'], 0, ',', '.') }}
            </div>
        </div>

    </div>


    {{-- INVOICE LIST --}}
    <div class="card invoice-content-card">

        <div class="invoice-card-header">

            <div>
                <h2 class="invoice-card-title">
                    Daftar Invoice
                </h2>

                <p class="invoice-card-description">
                    Seluruh invoice yang diterbitkan oleh sistem.
                </p>
            </div>

        </div>


        {{-- FILTER --}}
        <form
            method="GET"
            action="{{ route('admin.invoices.index') }}"
            class="invoice-filter-form"
        >

            <div class="invoice-form-group">

                <label
                    for="search"
                    class="invoice-form-label"
                >
                    Cari Invoice
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    class="invoice-form-input"
                    placeholder="Nomor invoice, kode order, nama, atau email"
                >

            </div>


            <div class="invoice-form-group">

                <label
                    for="status"
                    class="invoice-form-label"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="invoice-form-select"
                >

                    <option value="">
                        Semua Status
                    </option>

                    @foreach ($statusOptions as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected($status === $value)
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="invoice-form-group">

                <label
                    for="date"
                    class="invoice-form-label"
                >
                    Tanggal Terbit
                </label>

                <input
                    type="date"
                    id="date"
                    name="date"
                    value="{{ $date }}"
                    class="invoice-form-input"
                >

            </div>


            <div class="invoice-filter-actions">

                <button
                    type="submit"
                    class="invoice-button invoice-button-primary"
                >
                    Cari
                </button>

                <a
                    href="{{ route('admin.invoices.index') }}"
                    class="invoice-button invoice-button-outline"
                >
                    Reset
                </a>

            </div>

        </form>


        {{-- TABLE --}}
        @if ($invoices->count() > 0)

            <div class="invoice-table-wrapper">

                <table class="invoice-table">

                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Tanggal Terbit</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($invoices as $invoice)

                            @php
                                $invoiceStatusClass = in_array(
                                    $invoice->status,
                                    ['unpaid', 'paid', 'cancelled', 'refunded'],
                                    true
                                )
                                    ? $invoice->status
                                    : 'default';
                            @endphp

                            <tr>

                                <td>

                                    <div class="invoice-number">
                                        {{ $invoice->invoice_number }}
                                    </div>

                                    <div class="invoice-secondary">
                                        Order:
                                        {{ $invoice->order?->order_code ?? '-' }}
                                    </div>

                                </td>


                                <td>

                                    <div class="invoice-customer">
                                        {{ $invoice->order?->user?->name ?? '-' }}
                                    </div>

                                    <div class="invoice-secondary">
                                        {{ $invoice->order?->user?->email ?? '-' }}
                                    </div>

                                </td>


                                <td>
                                    {{ $invoice->formatted_issued_at }}
                                </td>


                                <td>
                                    <div class="invoice-amount">
                                        {{ $invoice->formatted_total }}
                                    </div>
                                </td>


                                <td>

                                    <span class="invoice-status invoice-status-{{ $invoiceStatusClass }}">
                                        {{ $statusOptions[$invoice->status]
                                            ?? $invoice->status_label }}
                                    </span>

                                </td>


                                <td>

                                    <div class="invoice-table-actions">

                                        <a
                                            href="{{ route(
                                                'admin.invoices.show',
                                                $invoice
                                            ) }}"
                                            class="invoice-action-link"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            Lihat / Cetak
                                        </a>

                                        @if ($invoice->order)

                                            <a
                                                href="{{ route(
                                                    'admin.orders.show',
                                                    $invoice->order
                                                ) }}"
                                                class="invoice-action-link secondary"
                                            >
                                                Order
                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="invoice-table-footer">

                <div class="invoice-result-count">
                    Menampilkan
                    {{ $invoices->firstItem() }}
                    –
                    {{ $invoices->lastItem() }}
                    dari
                    {{ $invoices->total() }}
                    invoice.
                </div>

                @if ($invoices->hasPages())

                    <nav
                        class="invoice-pagination"
                        aria-label="Navigasi halaman invoice"
                    >

                        @if ($invoices->onFirstPage())

                            <span class="invoice-page-link disabled">
                                ←
                            </span>

                        @else

                            <a
                                href="{{ $invoices->previousPageUrl() }}"
                                class="invoice-page-link"
                                aria-label="Halaman sebelumnya"
                            >
                                ←
                            </a>

                        @endif


                        @foreach ($invoices->getUrlRange(
                            max(1, $invoices->currentPage() - 2),
                            min(
                                $invoices->lastPage(),
                                $invoices->currentPage() + 2
                            )
                        ) as $page => $url)

                            <a
                                href="{{ $url }}"
                                class="invoice-page-link {{
                                    $page === $invoices->currentPage()
                                        ? 'active'
                                        : ''
                                }}"
                                @if ($page === $invoices->currentPage())
                                    aria-current="page"
                                @endif
                            >
                                {{ $page }}
                            </a>

                        @endforeach


                        @if ($invoices->hasMorePages())

                            <a
                                href="{{ $invoices->nextPageUrl() }}"
                                class="invoice-page-link"
                                aria-label="Halaman berikutnya"
                            >
                                →
                            </a>

                        @else

                            <span class="invoice-page-link disabled">
                                →
                            </span>

                        @endif

                    </nav>

                @endif

            </div>

        @else

            {{-- EMPTY STATE --}}
            <div class="invoice-empty">

                <div class="invoice-empty-icon">

                    <svg viewBox="0 0 24 24" fill="none">

                        <path
                            d="M7 3.5H14L19 8.5V20C19 20.55 18.55 21 18 21H7C5.9 21 5 20.1 5 19V5.5C5 4.4 5.9 3.5 7 3.5Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M14 3.5V8.5H19M8.5 12H15.5M8.5 15.5H13"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                    </svg>

                </div>

                <h3 class="invoice-empty-title">
                    Invoice Tidak Ditemukan
                </h3>

                <p class="invoice-empty-description">

                    @if ($search !== '' || $status || $date)

                        Tidak ada invoice yang sesuai dengan
                        pencarian atau filter yang dipilih.
                        Coba ubah filter atau tampilkan seluruh invoice.

                    @else

                        Belum ada invoice yang diterbitkan
                        oleh sistem.

                    @endif

                </p>

                @if ($search !== '' || $status || $date)

                    <a
                        href="{{ route('admin.invoices.index') }}"
                        class="invoice-button invoice-button-outline"
                        style="margin-top: 17px;"
                    >
                        Tampilkan Semua Invoice
                    </a>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection
