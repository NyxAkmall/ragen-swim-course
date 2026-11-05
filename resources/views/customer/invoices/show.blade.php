
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Invoice {{ $invoice->invoice_number }} - Ragen Swim Course</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }

        .invoice-actions {
            max-width: 850px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 0 auto 18px;
        }

        .back-link {
            color: #0369a1;
            font-weight: 700;
            text-decoration: none;
        }

        .print-button {
            padding: 11px 20px;
            border: none;
            border-radius: 8px;
            background: #0284c7;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .invoice-paper {
            max-width: 850px;
            min-height: 900px;
            margin: auto;
            padding: 45px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 10px 35px rgba(15, 23, 42, .08);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 25px;
            border-bottom: 3px solid #0284c7;
        }

        .brand {
            color: #075985;
            font-size: 23px;
            font-weight: 900;
        }

        .brand-description {
            margin-top: 5px;
            color: #64748b;
        }

        .invoice-heading {
            text-align: right;
        }

        .invoice-heading h1 {
            margin: 0;
            color: #0284c7;
            font-size: 30px;
            letter-spacing: 2px;
        }

        .invoice-number {
            margin-top: 5px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 32px;
        }

        .section-label {
            display: block;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .customer-name {
            font-size: 16px;
            font-weight: 800;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 7px;
        }

        .detail-row span {
            color: #64748b;
        }

        .detail-row strong {
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 800;
        }

        .status-badge.unpaid {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.cancelled,
        .status-badge.refunded {
            background: #fee2e2;
            color: #991b1b;
        }

        .items-section {
            margin-top: 38px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 13px 10px;
            background: #e0f2fe;
            color: #075985;
            font-size: 11px;
            text-align: left;
        }

        .items-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .items-table .right {
            text-align: right;
        }

        .package-name {
            display: block;
            font-weight: 800;
        }

        .package-detail {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 11px;
        }

        .invoice-totals {
            width: 310px;
            max-width: 100%;
            margin: 25px 0 0 auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 10px;
        }

        .total-row span {
            color: #64748b;
        }

        .total-row strong {
            text-align: right;
        }

        /*
        |--------------------------------------------------------------------------
        | Biaya Pendaftaran
        |--------------------------------------------------------------------------
        */

        .registration-fee-row {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
        }

        .registration-fee-row span,
        .registration-fee-row strong {
            color: #0369a1;
            font-weight: 800;
        }

        .registration-fee-note {
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            background: #f0f9ff;
            color: #075985;
            font-size: 10px;
            line-height: 1.6;
        }

        .registration-fee-note strong {
            display: block;
            margin-bottom: 3px;
        }

        .registration-fee-note p {
            margin: 0;
        }

        .grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #0284c7;
            color: #075985;
            font-size: 17px;
            font-weight: 900;
        }

        .grand-total span,
        .grand-total strong {
            color: #075985;
        }

        .invoice-note {
            margin-top: 50px;
            padding: 17px;
            border-radius: 9px;
            background: #f0f9ff;
            color: #075985;
            font-size: 11px;
        }

        .invoice-footer {
            margin-top: 35px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px 8px;
            }

            .invoice-paper {
                min-height: auto;
                padding: 23px 16px;
            }

            .invoice-header,
            .invoice-details {
                grid-template-columns: 1fr;
                flex-direction: column;
            }

            .invoice-heading {
                text-align: left;
            }

            .items-section {
                overflow-x: auto;
            }

            .items-table {
                min-width: 540px;
            }
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                padding: 0;
                background: white;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .invoice-actions {
                display: none;
            }

            .invoice-paper {
                max-width: none;
                min-height: auto;
                padding: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .items-section {
                overflow: visible;
            }

            .items-table {
                min-width: 0;
            }

            .registration-fee-note,
            .invoice-note {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    @php
        /*
        |--------------------------------------------------------------------------
        | Nilai Biaya Pendaftaran dari Pesanan
        |--------------------------------------------------------------------------
        |
        | Tidak mengambil tarif terbaru dari Setting.
        | Nominal pada invoice mengikuti snapshot pesanan.
        */

        $registrationFee = (float) ($order->registration_fee ?? 0);

        $hasRegistrationFee = $registrationFee > 0;

        $formattedRegistrationFee =
            'Rp ' . number_format(
                $registrationFee,
                0,
                ',',
                '.'
            );
    @endphp

    <div class="invoice-actions">
        <a
            href="{{ route('customer.orders.show', $order) }}"
            class="back-link"
        >
            ← Kembali ke Detail Pesanan
        </a>

        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            Cetak / Simpan PDF
        </button>
    </div>

    <main class="invoice-paper">

        <header class="invoice-header">
            <div>
                <div class="brand">
                    RAGEN SWIM COURSE
                </div>

                <div class="brand-description">
                    Layanan kursus dan pelatihan renang
                </div>
            </div>

            <div class="invoice-heading">
                <h1>INVOICE</h1>

                <div class="invoice-number">
                    {{ $invoice->invoice_number }}
                </div>
            </div>
        </header>

        <section class="invoice-details">

            <div>
                <span class="section-label">
                    Ditagihkan Kepada
                </span>

                <div class="customer-name">
                    {{ $order->user->name }}
                </div>

                <div>
                    {{ $order->user->email }}
                </div>

                @if ($order->user->phone)
                    <div>
                        {{ $order->user->phone }}
                    </div>
                @endif
            </div>

            <div>
                <span class="section-label">
                    Detail Invoice
                </span>

                <div class="detail-row">
                    <span>Kode Pesanan</span>
                    <strong>{{ $order->order_code }}</strong>
                </div>

                <div class="detail-row">
                    <span>Tanggal Terbit</span>
                    <strong>
                        {{ $invoice->formatted_issued_at }}
                    </strong>
                </div>

                @if ($invoice->due_at)
                    <div class="detail-row">
                        <span>Jatuh Tempo</span>
                        <strong>
                            {{ $invoice->formatted_due_at }}
                        </strong>
                    </div>
                @endif

                <div class="detail-row">
                    <span>Status</span>

                    <strong>
                        <span class="status-badge {{ $invoice->status }}">
                            {{ $invoice->status_label }}
                        </span>
                    </strong>
                </div>

                @if ($order->paid_at)
                    <div class="detail-row">
                        <span>Tanggal Dibayar</span>
                        <strong>
                            {{ $order->paid_at->format('d-m-Y H:i') }}
                        </strong>
                    </div>
                @endif
            </div>

        </section>

        <section class="items-section">

            <span class="section-label">
                Rincian Pembelian
            </span>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Program / Paket</th>
                        <th>Peserta</th>
                        <th class="right">Jumlah</th>
                        <th class="right">Harga</th>
                        <th class="right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <span class="package-name">
                                    {{ $item->package_name }}
                                </span>

                                <span class="package-detail">
                                    {{
                                        $item->package?->program?->name
                                        ?? 'Program Renang'
                                    }}
                                </span>
                            </td>

                            <td>
                                {{
                                    $item->participant?->full_name
                                    ?? '-'
                                }}
                            </td>

                            <td class="right">
                                {{ $item->quantity }}
                            </td>

                            <td class="right">
                                {{ $item->formatted_unit_price }}
                            </td>

                            <td class="right">
                                {{ $item->formatted_subtotal }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="invoice-totals">

                {{-- SUBTOTAL PAKET --}}

                <div class="total-row">
                    <span>Subtotal Paket</span>
                    <strong>
                        {{ $invoice->formatted_subtotal }}
                    </strong>
                </div>

                {{-- DISKON PAKET --}}

                <div class="total-row">
                    <span>Diskon Paket</span>

                    <strong>
                        @if ((float) $order->discount_amount > 0)
                            - {{ $invoice->formatted_discount }}
                        @else
                            Rp 0
                        @endif
                    </strong>
                </div>

                {{-- BIAYA PENDAFTARAN --}}

                <div class="total-row registration-fee-row">
                    <span>Biaya Pendaftaran</span>

                    <strong>
                        {{ $formattedRegistrationFee }}
                    </strong>
                </div>

                @if ($hasRegistrationFee)

                    <div class="registration-fee-note">

                        @if ($invoice->isPaid())

                            <strong>
                                Biaya Pendaftaran Telah Dibayar
                            </strong>

                            <p>
                                Biaya pendaftaran satu kali ini
                                telah termasuk dalam pembayaran
                                dan dicatat pada transaksi peserta.
                            </p>

                        @else

                            <strong>
                                Biaya Pendaftaran Peserta
                            </strong>

                            <p>
                                Biaya ini merupakan pendaftaran awal
                                peserta dan akan dinyatakan lunas
                                setelah pembayaran diverifikasi.
                            </p>

                        @endif

                    </div>

                @endif

                {{-- TOTAL INVOICE --}}

                <div class="total-row grand-total">
                    <span>Total Pembayaran</span>

                    <strong>
                        {{ $invoice->formatted_total }}
                    </strong>
                </div>

            </div>

        </section>

        <div class="invoice-note">
            @if ($invoice->isPaid())

                Pembayaran telah diterima. Invoice ini merupakan
                bukti transaksi pembelian paket Ragen Swim Course.

                @if ($hasRegistrationFee)
                    Biaya pendaftaran peserta telah termasuk
                    dalam total pembayaran yang tercantum.
                @endif

            @else

                Invoice ini menampilkan rincian transaksi sesuai
                status pembayaran yang tercatat dalam sistem.

            @endif
        </div>

        <footer class="invoice-footer">
            Terima kasih telah memilih Ragen Swim Course.
            <br>
            Dokumen ini dihasilkan secara otomatis oleh sistem.
        </footer>

    </main>

</body>
</html>
