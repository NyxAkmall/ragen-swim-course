<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Invoice {{ $invoice->invoice_number }} - Ragen Swim Course
    </title>

    <style>
        :root {
            --navy: #17324d;
            --blue: #087ca7;
            --text: #334155;
            --text-soft: #64748b;
            --border: #e2e8f0;
            --background: #f1f5f9;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            background: var(--background);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .page-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            max-width: 850px;
            margin: 0 auto 18px;
        }

        .back-link,
        .print-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 16px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .back-link {
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--navy);
        }

        .print-button {
            border: 1px solid var(--blue);
            background: var(--blue);
            color: var(--white);
        }

        .back-link:hover {
            background: #f8fafc;
        }

        .print-button:hover {
            background: #03678c;
        }

        .invoice-sheet {
            width: 100%;
            max-width: 850px;
            min-height: 1050px;
            margin: 0 auto;
            padding: 46px 48px;
            background: var(--white);
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
        }

        .invoice-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid var(--navy);
        }

        .brand-name {
            color: var(--navy);
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -0.6px;
        }

        .brand-description {
            margin-top: 5px;
            color: var(--text-soft);
            font-size: 11px;
        }

        .invoice-heading {
            text-align: right;
        }

        .invoice-title {
            color: var(--navy);
            font-size: 29px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .invoice-number {
            margin-top: 4px;
            color: var(--text-soft);
            font-size: 11px;
            word-break: break-word;
        }

        .invoice-status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
        }

        .status-unpaid {
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

        .status-refunded {
            background: #faf5ff;
            color: #7e22ce;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            margin-top: 30px;
        }

        .info-heading {
            margin-bottom: 12px;
            color: var(--text-soft);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .customer-name {
            color: var(--navy);
            font-size: 15px;
            font-weight: 800;
        }

        .customer-detail {
            margin-top: 5px;
            color: var(--text-soft);
            font-size: 11px;
            word-break: break-word;
        }

        .invoice-meta {
            display: grid;
            grid-template-columns: 115px 1fr;
            gap: 9px 15px;
        }

        .meta-label {
            color: var(--text-soft);
            font-size: 11px;
        }

        .meta-value {
            color: var(--text);
            font-size: 11px;
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }

        .invoice-items {
            margin-top: 38px;
        }

        .section-title {
            margin-bottom: 13px;
            color: var(--navy);
            font-size: 13px;
            font-weight: 800;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 12px 10px;
            border-bottom: 2px solid var(--navy);
            color: var(--navy);
            font-size: 10px;
            font-weight: 800;
            text-align: left;
        }

        .items-table td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--border);
            font-size: 11px;
            vertical-align: top;
        }

        .items-table th:first-child,
        .items-table td:first-child {
            padding-left: 0;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            padding-right: 0;
            text-align: right;
        }

        .item-name {
            color: var(--navy);
            font-weight: 800;
        }

        .item-description {
            margin-top: 4px;
            color: var(--text-soft);
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .money {
            white-space: nowrap;
        }

        .invoice-total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }

        .invoice-totals {
            width: 100%;
            max-width: 315px;
        }

        .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 7px 0;
            font-size: 11px;
        }

        .total-label {
            color: var(--text-soft);
        }

        .total-value {
            color: var(--text);
            font-weight: 700;
            white-space: nowrap;
        }

        .total-row.final {
            margin-top: 9px;
            padding: 13px 0;
            border-top: 2px solid var(--navy);
            border-bottom: 2px solid var(--navy);
        }

        .total-row.final .total-label,
        .total-row.final .total-value {
            color: var(--navy);
            font-size: 16px;
            font-weight: 800;
        }

        .payment-section {
            margin-top: 38px;
            padding: 18px 20px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #f8fafc;
        }

        .payment-heading {
            margin-bottom: 13px;
            color: var(--navy);
            font-size: 12px;
            font-weight: 800;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px 24px;
        }

        .payment-label {
            color: var(--text-soft);
            font-size: 10px;
        }

        .payment-value {
            margin-top: 4px;
            color: var(--navy);
            font-size: 11px;
            font-weight: 700;
            word-break: break-word;
        }

        .invoice-note {
            margin-top: 30px;
            padding: 16px 18px;
            border-left: 3px solid var(--blue);
            background: #f0f9ff;
            color: #475569;
            font-size: 10px;
            line-height: 1.8;
        }

        .invoice-footer {
            margin-top: 55px;
            padding-top: 17px;
            border-top: 1px solid var(--border);
            color: var(--text-soft);
            font-size: 10px;
            text-align: center;
        }

        @media (max-width: 650px) {
            body {
                padding: 12px;
            }

            .invoice-sheet {
                min-height: auto;
                padding: 25px 18px;
            }

            .invoice-header {
                flex-direction: column;
                gap: 18px;
            }

            .invoice-heading {
                text-align: left;
            }

            .invoice-info {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .invoice-meta {
                max-width: 350px;
            }

            .invoice-title {
                font-size: 25px;
            }

            .payment-grid {
                grid-template-columns: 1fr;
            }
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-actions {
                display: none !important;
            }

            .invoice-sheet {
                max-width: none;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .table-wrapper {
                overflow: visible;
            }

            .items-table {
                width: 100%;
            }

            .items-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .invoice-total-section,
            .payment-section,
            .invoice-note {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    @php
        $statusLabels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
        ];

        $statusClass = in_array(
            $invoice->status,
            ['unpaid', 'paid', 'cancelled', 'refunded'],
            true
        ) ? $invoice->status : 'unpaid';

        $paidPayment = $order->payments
            ->where('status', 'paid')
            ->sortByDesc('paid_at')
            ->first();
    @endphp


    <div class="page-actions">

        <a
            href="{{ route('admin.invoices.index') }}"
            class="back-link"
        >
            ← Kembali ke Daftar Invoice
        </a>

        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            Cetak / Simpan PDF
        </button>

    </div>


    <main class="invoice-sheet">

        {{-- HEADER --}}
        <header class="invoice-header">

            <div>

                <div class="brand-name">
                    RAGEN SWIM COURSE
                </div>

                <div class="brand-description">
                    Layanan kursus dan pelatihan renang
                </div>

            </div>


            <div class="invoice-heading">

                <div class="invoice-title">
                    INVOICE
                </div>

                <div class="invoice-number">
                    {{ $invoice->invoice_number }}
                </div>

                <span class="invoice-status status-{{ $statusClass }}">
                    {{ $statusLabels[$invoice->status] ?? $invoice->status_label }}
                </span>

            </div>

        </header>


        {{-- CUSTOMER & INVOICE INFORMATION --}}
        <section class="invoice-info">

            <div>

                <div class="info-heading">
                    Ditagihkan Kepada
                </div>

                <div class="customer-name">
                    {{ $order->user?->name ?? '-' }}
                </div>

                <div class="customer-detail">
                    {{ $order->user?->email ?? '-' }}
                </div>

                @if ($order->user?->phone)
                    <div class="customer-detail">
                        {{ $order->user->phone }}
                    </div>
                @endif

            </div>


            <div>

                <div class="info-heading">
                    Informasi Invoice
                </div>

                <div class="invoice-meta">

                    <span class="meta-label">
                        Nomor Invoice
                    </span>

                    <span class="meta-value">
                        {{ $invoice->invoice_number }}
                    </span>


                    <span class="meta-label">
                        Kode Order
                    </span>

                    <span class="meta-value">
                        {{ $order->order_code }}
                    </span>


                    <span class="meta-label">
                        Tanggal Terbit
                    </span>

                    <span class="meta-value">
                        {{ $invoice->issued_at
                            ? $invoice->issued_at->format('d M Y')
                            : '-'
                        }}
                    </span>


                    <span class="meta-label">
                        Jatuh Tempo
                    </span>

                    <span class="meta-value">
                        {{ $invoice->due_at
                            ? $invoice->due_at->format('d M Y')
                            : '-'
                        }}
                    </span>


                    <span class="meta-label">
                        Tanggal Order
                    </span>

                    <span class="meta-value">
                        {{ $order->ordered_at
                            ? $order->ordered_at->format('d M Y')
                            : '-'
                        }}
                    </span>

                </div>

            </div>

        </section>


        {{-- ITEMS --}}
        <section class="invoice-items">

            <h2 class="section-title">
                Rincian Pembelian
            </h2>

            <div class="table-wrapper">

                <table class="items-table">

                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th>Peserta</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($order->items as $item)

                            <tr>

                                <td>

                                    <div class="item-name">
                                        {{ $item->package_name }}
                                    </div>

                                    <div class="item-description">
                                        {{ $item->package?->program?->name ?? 'Program Renang' }}

                                        @if ($item->package?->total_sessions)
                                            · {{ $item->package->total_sessions }} sesi
                                        @endif
                                    </div>

                                </td>


                                <td>
                                    {{ $item->participant?->full_name ?? '-' }}
                                </td>


                                <td class="text-center">
                                    {{ $item->quantity }}
                                </td>


                                <td class="text-right money">
                                    Rp {{ number_format(
                                        (float) $item->unit_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>


                                <td class="text-right money">
                                    Rp {{ number_format(
                                        (float) $item->subtotal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="text-center"
                                >
                                    Tidak ada rincian pembelian.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>


        {{-- TOTAL --}}
        <section class="invoice-total-section">

            <div class="invoice-totals">

                <div class="total-row">

                    <span class="total-label">
                        Subtotal
                    </span>

                    <span class="total-value">
                        {{ $invoice->formatted_subtotal }}
                    </span>

                </div>


                <div class="total-row">

                    <span class="total-label">
                        Diskon
                    </span>

                    <span class="total-value">
                        {{ $invoice->formatted_discount }}
                    </span>

                </div>


                @if ($order->promoCode)

                    <div class="total-row">

                        <span class="total-label">
                            Kode Promo
                        </span>

                        <span class="total-value">
                            {{ $order->promoCode->code }}
                        </span>

                    </div>

                @endif


                <div class="total-row final">

                    <span class="total-label">
                        TOTAL
                    </span>

                    <span class="total-value">
                        {{ $invoice->formatted_total }}
                    </span>

                </div>

            </div>

        </section>


        {{-- PAYMENT INFORMATION --}}
        <section class="payment-section">

            <div class="payment-heading">
                Informasi Pembayaran
            </div>

            <div class="payment-grid">

                <div>

                    <div class="payment-label">
                        Status Pembayaran
                    </div>

                    <div class="payment-value">
                        {{ $statusLabels[$invoice->status] ?? $invoice->status_label }}
                    </div>

                </div>


                <div>

                    <div class="payment-label">
                        Metode Pembayaran
                    </div>

                    <div class="payment-value">
                        {{ $paidPayment?->payment_display
                            ?? $order->payments->sortByDesc('id')->first()?->payment_display
                            ?? '-'
                        }}
                    </div>

                </div>


                <div>

                    <div class="payment-label">
                        Tanggal Pembayaran
                    </div>

                    <div class="payment-value">
                        {{ $order->paid_at
                            ? $order->paid_at->format('d M Y H:i') . ' WIB'
                            : '-'
                        }}
                    </div>

                </div>


                <div>

                    <div class="payment-label">
                        Kode Pembayaran
                    </div>

                    <div class="payment-value">
                        {{ $paidPayment?->payment_code
                            ?? $order->payments->sortByDesc('id')->first()?->payment_code
                            ?? '-'
                        }}
                    </div>

                </div>

            </div>

        </section>


        {{-- NOTE --}}
        <section class="invoice-note">

            <strong>Catatan:</strong>
            Invoice ini diterbitkan secara elektronik oleh
            Ragen Swim Course berdasarkan transaksi yang
            tercatat di sistem.

            @if ($invoice->status === 'paid')
                Pembayaran telah diterima dan transaksi
                berstatus lunas.
            @elseif ($invoice->status === 'unpaid')
                Invoice ini belum berstatus lunas.
                Silakan selesaikan pembayaran sesuai
                instruksi pada halaman order.
            @elseif ($invoice->status === 'cancelled')
                Transaksi terkait invoice ini telah dibatalkan.
            @elseif ($invoice->status === 'refunded')
                Transaksi terkait invoice ini berstatus pengembalian dana.
            @endif

        </section>


        {{-- FOOTER --}}
        <footer class="invoice-footer">

            Terima kasih telah menggunakan layanan
            <strong>Ragen Swim Course</strong>.

            <br>

            Dokumen ini dibuat secara otomatis oleh sistem.

        </footer>

    </main>

</body>
</html>
