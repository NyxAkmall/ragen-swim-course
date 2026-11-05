@extends('layouts.admin')

@section('title', 'Detail Order')
@section('topbar-title', 'Detail Order')

@push('styles')
<style>
    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .detail-title {
        color: var(--navy);
        font-size: 27px;
        font-weight: 850;
        letter-spacing: -0.6px;
    }

    .detail-subtitle {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    .back-button {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 9px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: var(--white);
        color: var(--navy);
        text-decoration: none;
        font-size: 12px;
        font-weight: 750;
        white-space: nowrap;
    }

    .back-button:hover {
        background: #f8fafc;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .summary-card {
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: var(--white);
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .summary-label {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .summary-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
        line-height: 1.45;
        word-break: break-word;
    }

    .summary-value.money {
        font-size: 19px;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(300px, 0.8fr);
        gap: 20px;
        align-items: start;
    }

    .section-card {
        overflow: hidden;
        margin-bottom: 20px;
    }

    .section-header {
        padding: 17px 19px;
        border-bottom: 1px solid var(--border);
        background: #fbfdff;
    }

    .section-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .section-description {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .section-body {
        padding: 19px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px 20px;
    }

    .info-item {
        min-width: 0;
    }

    .info-label {
        margin-bottom: 5px;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 750;
    }

    .info-value {
        color: var(--text);
        font-size: 12px;
        font-weight: 650;
        line-height: 1.55;
        word-break: break-word;
    }

    .info-value.strong {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
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

    .status-cancelled,
    .status-failed {
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

    .status-verification {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-rejected {
        background: #fef2f2;
        color: #b91c1c;
    }

    .alert-box {
        padding: 13px 15px;
        margin-bottom: 18px;
        border: 1px solid;
        border-radius: 11px;
        font-size: 11px;
        font-weight: 650;
        line-height: 1.6;
    }

    .alert-success {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .alert-warning {
        border-color: #fed7aa;
        background: #fff7ed;
        color: #9a3412;
    }

    .alert-error {
        border-color: #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .items-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .items-table {
        width: 100%;
        min-width: 690px;
        border-collapse: collapse;
    }

    .items-table th {
        padding: 11px 13px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 9px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .items-table td {
        padding: 14px 13px;
        border-bottom: 1px solid #eef2f7;
        color: var(--text);
        font-size: 11px;
        vertical-align: top;
    }

    .items-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .item-title {
        color: var(--navy);
        font-size: 12px;
        font-weight: 800;
    }

    .item-subtitle {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .money {
        font-weight: 800;
        white-space: nowrap;
    }

    .totals {
        padding: 17px 19px;
        border-top: 1px solid var(--border);
        background: #fbfdff;
    }

    .total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 5px 0;
        color: var(--text-soft);
        font-size: 11px;
    }

    .total-row strong {
        color: var(--text);
    }

    .total-row.final {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 1px dashed var(--border);
        color: var(--navy);
        font-size: 14px;
        font-weight: 850;
    }

    .payment-card {
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fbfdff;
    }

    .payment-card + .payment-card {
        margin-top: 12px;
    }

    .payment-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .payment-code {
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
        word-break: break-word;
    }

    .payment-method {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .payment-amount {
        margin-top: 12px;
        color: var(--navy);
        font-size: 16px;
        font-weight: 850;
    }

    .payment-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
        padding-top: 13px;
        border-top: 1px solid var(--border);
    }

    .meta-label {
        color: var(--text-light);
        font-size: 9px;
        font-weight: 750;
    }

    .meta-value {
        margin-top: 3px;
        color: var(--text);
        font-size: 10px;
        line-height: 1.45;
        word-break: break-word;
    }

    .proof-section {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--border);
    }

    .proof-title {
        margin-bottom: 10px;
        color: var(--navy);
        font-size: 11px;
        font-weight: 800;
    }

    .proof-preview {
        display: block;
        width: 100%;
        max-width: 420px;
        max-height: 440px;
        object-fit: contain;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
    }

    .proof-link {
        display: inline-flex;
        margin-top: 9px;
        color: #0369a1;
        font-size: 10px;
        font-weight: 750;
        text-decoration: none;
    }

    .proof-link:hover {
        text-decoration: underline;
    }

    .rejection-box {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 10px;
        line-height: 1.6;
    }

    .rejection-box strong {
        display: block;
        margin-bottom: 4px;
    }

    .verification-box {
        margin-top: 15px;
        padding: 14px;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        background: #eff6ff;
    }

    .verification-title {
        color: #1e3a8a;
        font-size: 11px;
        font-weight: 800;
    }

    .verification-text {
        margin-top: 5px;
        color: #475569;
        font-size: 10px;
        line-height: 1.6;
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 14px;
    }

    .verify-button,
    .reject-button {
        width: 100%;
        min-height: 41px;
        border: 0;
        border-radius: 9px;
        cursor: pointer;
        font-size: 10px;
        font-weight: 800;
    }

    .verify-button {
        background: #16a34a;
        color: #fff;
    }

    .verify-button:hover {
        background: #15803d;
    }

    .reject-button {
        background: #fee2e2;
        color: #b91c1c;
    }

    .reject-button:hover {
        background: #fecaca;
    }

    .verify-button:disabled,
    .reject-button:disabled,
    .reject-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .reject-form {
        display: none;
        margin-top: 13px;
        padding-top: 13px;
        border-top: 1px solid #bfdbfe;
    }

    .reject-form.active {
        display: block;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .form-textarea {
        width: 100%;
        min-height: 95px;
        resize: vertical;
        padding: 10px 11px;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: #fff;
        color: var(--text);
        font: inherit;
        font-size: 11px;
        line-height: 1.55;
        outline: none;
    }

    .form-textarea:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.08);
    }

    .reject-submit {
        width: 100%;
        min-height: 39px;
        margin-top: 9px;
        border: 0;
        border-radius: 9px;
        background: #dc2626;
        color: #fff;
        cursor: pointer;
        font-size: 10px;
        font-weight: 800;
    }

    .reject-submit:hover {
        background: #b91c1c;
    }

    .field-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 9px;
        font-weight: 650;
    }

    .verified-box {
        margin-top: 15px;
        padding: 12px;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        background: #f0fdf4;
        color: #166534;
        font-size: 10px;
        line-height: 1.6;
    }

    .invoice-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 42px;
        margin-top: 19px;
        padding: 11px 15px;
        border: 1px solid #0284c7;
        border-radius: 10px;
        background: #0284c7;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .invoice-action:hover {
        background: #0369a1;
    }

    .invoice-action-note {
        margin-top: 9px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.6;
        text-align: center;
    }

    .timeline {
        position: relative;
    }

    .timeline-item {
        position: relative;
        padding: 0 0 18px 24px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        bottom: -5px;
        width: 1px;
        background: var(--border);
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        position: absolute;
        top: 4px;
        left: 0;
        width: 11px;
        height: 11px;
        border: 2px solid #bae6fd;
        border-radius: 50%;
        background: var(--white);
    }

    .timeline-title {
        color: var(--text);
        font-size: 11px;
        font-weight: 750;
    }

    .timeline-time {
        margin-top: 3px;
        color: var(--text-light);
        font-size: 9px;
    }

    .timeline-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .empty-box {
        padding: 22px;
        border: 1px dashed var(--border);
        border-radius: 12px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
        text-align: center;
        background: #fbfdff;
    }

    .notes-box {
        padding: 13px;
        border-radius: 10px;
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.65;
        white-space: pre-line;
    }

    @media (max-width: 1150px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .detail-header {
            display: block;
        }

        .back-button {
            margin-top: 15px;
        }

        .summary-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }

        .detail-title {
            font-size: 23px;
        }

        .payment-meta,
        .action-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


@section('content')

@php
    $orderStatusLabels = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ];

    $pendingManualPayment = $order->payments
        ->where('status', 'pending')
        ->where('payment_method', 'bank_transfer')
        ->where('payment_channel', 'manual_transfer')
        ->sortByDesc('id')
        ->first();

    $orderWaitingVerification = $order->status === 'pending'
        && $pendingManualPayment
        && !empty($pendingManualPayment->proof_image)
        && empty($pendingManualPayment->rejection_reason);

    $orderProofRejected = $order->status === 'pending'
        && $pendingManualPayment
        && !empty($pendingManualPayment->rejection_reason);

    $displayOrderStatus = $orderStatusLabels[$order->status]
        ?? ucfirst($order->status);

    $displayOrderStatusClass = $order->status;

    if ($orderWaitingVerification) {
        $displayOrderStatus = 'Menunggu Verifikasi';
        $displayOrderStatusClass = 'verification';
    } elseif ($orderProofRejected) {
        $displayOrderStatus = 'Bukti Ditolak';
        $displayOrderStatusClass = 'rejected';
    }
@endphp


@if (session('success'))
    <div class="alert-box alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert-box alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if (session('error'))
    <div class="alert-box alert-error">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-box alert-error">
        <strong>Periksa kembali data yang dikirim.</strong>

        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif


<div class="detail-header">

    <div>
        <h1 class="detail-title">
            Detail Order
        </h1>

        <p class="detail-subtitle">
            Informasi lengkap transaksi
            <strong>{{ $order->order_code }}</strong>.
            Admin dapat memantau transaksi dan memverifikasi
            pembayaran transfer manual yang dikirim customer.
        </p>
    </div>

    <a
        href="{{ route('admin.orders.index') }}"
        class="back-button"
    >
        ← Kembali ke Order
    </a>

</div>


<div class="summary-grid">

    <div class="summary-card">
        <div class="summary-label">
            Kode Order
        </div>

        <div class="summary-value">
            {{ $order->order_code }}
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-label">
            Status Order
        </div>

        <div class="summary-value">
            <span class="status-badge status-{{ $displayOrderStatusClass }}">
                {{ $displayOrderStatus }}
            </span>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-label">
            Total Pembayaran
        </div>

        <div class="summary-value money">
            {{ $order->formatted_total }}
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-label">
            Tanggal Order
        </div>

        <div class="summary-value">
            @if ($order->ordered_at)
                {{ $order->ordered_at->format('d M Y') }}
                <br>
                {{ $order->ordered_at->format('H:i') }} WIB
            @else
                -
            @endif
        </div>
    </div>

</div>


<div class="content-grid">

    <div>

        {{-- =====================================================
            CUSTOMER
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Informasi Customer
                </div>

                <div class="section-description">
                    Pemilik akun yang melakukan transaksi.
                </div>
            </div>

            <div class="section-body">

                <div class="info-grid">

                    <div class="info-item">
                        <div class="info-label">
                            Nama Customer
                        </div>

                        <div class="info-value strong">
                            {{ $order->user?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            Email
                        </div>

                        <div class="info-value">
                            {{ $order->user?->email ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            Nomor Telepon
                        </div>

                        <div class="info-value">
                            {{ $order->user?->phone ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            Status Akun
                        </div>

                        <div class="info-value">
                            {{ ucfirst($order->user?->status ?? '-') }}
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            ITEM PEMBELIAN
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Item Pembelian
                </div>

                <div class="section-description">
                    Paket renang dan peserta yang terdapat dalam order.
                </div>
            </div>

            @if ($order->items->count() > 0)

                <div class="items-table-wrapper">

                    <table class="items-table">

                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Paket</th>
                                <th>Program</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($order->items as $item)

                                <tr>

                                    <td>
                                        <div class="item-title">
                                            {{ $item->participant?->full_name ?? '-' }}
                                        </div>

                                        <div class="item-subtitle">
                                            {{ ucfirst($item->participant?->swimming_level ?? '-') }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="item-title">
                                            {{ $item->package_name }}
                                        </div>

                                        @if ($item->package)
                                            <div class="item-subtitle">
                                                {{ $item->package->total_sessions }} sesi per paket
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="item-title">
                                            {{ $item->package?->program?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format(
                                            (float) $item->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format(
                                            (float) $item->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="totals">

                    <div class="total-row">
                        <span>Subtotal</span>

                        <strong>
                            {{ $order->formatted_subtotal }}
                        </strong>
                    </div>

                    <div class="total-row">
                        <span>Diskon</span>

                        <strong>
                            {{ $order->formatted_discount }}
                        </strong>
                    </div>

                    @if ($order->promoCode)
                        <div class="total-row">
                            <span>Promo</span>

                            <strong>
                                {{ $order->promoCode->code ?? '-' }}
                            </strong>
                        </div>
                    @endif

                    <div class="total-row final">
                        <span>Total</span>

                        <span>
                            {{ $order->formatted_total }}
                        </span>
                    </div>

                </div>

            @else

                <div class="section-body">
                    <div class="empty-box">
                        Tidak ada item pembelian pada order ini.
                    </div>
                </div>

            @endif

        </div>


        {{-- =====================================================
            RIWAYAT DAN VERIFIKASI PEMBAYARAN
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Riwayat & Verifikasi Pembayaran
                </div>

                <div class="section-description">
                    Bukti transfer, status pembayaran, dan proses
                    verifikasi pembayaran customer.
                </div>
            </div>

            <div class="section-body">

                @forelse ($order->payments->sortByDesc('id') as $payment)

                    @php
                        $isManualTransfer =
                            $payment->payment_method === 'bank_transfer'
                            && $payment->payment_channel === 'manual_transfer';

                        $hasProof = !empty($payment->proof_image);

                        $isRejected =
                            $isManualTransfer
                            && $payment->status === 'pending'
                            && !empty($payment->rejection_reason);

                        $waitingVerification =
                            $isManualTransfer
                            && $payment->status === 'pending'
                            && $hasProof
                            && empty($payment->rejection_reason)
                            && $payment->verified_at === null;

                        $canVerify =
                            $order->status === 'pending'
                            && $waitingVerification;

                        if ($payment->status === 'paid') {
                            $paymentBadgeClass = 'paid';
                            $paymentStatusLabel = 'Berhasil Dibayar';
                        } elseif ($payment->status === 'failed') {
                            $paymentBadgeClass = 'failed';
                            $paymentStatusLabel = 'Gagal';
                        } elseif ($payment->status === 'expired') {
                            $paymentBadgeClass = 'expired';
                            $paymentStatusLabel = 'Kedaluwarsa';
                        } elseif ($payment->status === 'refunded') {
                            $paymentBadgeClass = 'refunded';
                            $paymentStatusLabel = 'Refund';
                        } elseif ($isRejected) {
                            $paymentBadgeClass = 'rejected';
                            $paymentStatusLabel = 'Bukti Ditolak';
                        } elseif ($waitingVerification) {
                            $paymentBadgeClass = 'verification';
                            $paymentStatusLabel = 'Menunggu Verifikasi';
                        } elseif (
                            $payment->status === 'pending'
                            && $isManualTransfer
                        ) {
                            $paymentBadgeClass = 'pending';
                            $paymentStatusLabel = 'Menunggu Bukti';
                        } elseif ($payment->status === 'pending') {
                            $paymentBadgeClass = 'pending';
                            $paymentStatusLabel = 'Menunggu Pembayaran';
                        } else {
                            $paymentBadgeClass = $payment->status;
                            $paymentStatusLabel = ucfirst($payment->status);
                        }
                    @endphp


                    <div class="payment-card">

                        <div class="payment-top">

                            <div>
                                <div class="payment-code">
                                    {{ $payment->payment_code }}
                                </div>

                                <div class="payment-method">
                                    {{ $payment->payment_display }}
                                </div>
                            </div>

                            <span class="status-badge status-{{ $paymentBadgeClass }}">
                                {{ $paymentStatusLabel }}
                            </span>

                        </div>


                        <div class="payment-amount">
                            {{ $payment->formatted_amount }}
                        </div>


                        <div class="payment-meta">

                            <div>
                                <div class="meta-label">
                                    Gateway Reference
                                </div>

                                <div class="meta-value">
                                    {{ $payment->gateway_reference ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Bukti Diupload
                                </div>

                                <div class="meta-value">
                                    {{
                                        $payment->proof_uploaded_at
                                            ? $payment->proof_uploaded_at->format('d M Y H:i') . ' WIB'
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Dibayar Pada
                                </div>

                                <div class="meta-value">
                                    {{
                                        $payment->paid_at
                                            ? $payment->paid_at->format('d M Y H:i') . ' WIB'
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Kedaluwarsa
                                </div>

                                <div class="meta-value">
                                    {{
                                        $payment->expired_at
                                            ? $payment->expired_at->format('d M Y H:i') . ' WIB'
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Diverifikasi Pada
                                </div>

                                <div class="meta-value">
                                    {{
                                        $payment->verified_at
                                            ? $payment->verified_at->format('d M Y H:i') . ' WIB'
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Diverifikasi Oleh
                                </div>

                                <div class="meta-value">
                                    {{ $payment->verifier?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Log Pembayaran
                                </div>

                                <div class="meta-value">
                                    {{ $payment->logs->count() }} log
                                </div>
                            </div>

                            <div>
                                <div class="meta-label">
                                    Refund
                                </div>

                                <div class="meta-value">
                                    {{ $payment->refunds->count() }} data
                                </div>
                            </div>

                        </div>


                        {{-- =====================================
                            BUKTI TRANSFER
                        ====================================== --}}

                        @if ($hasProof)

                            <div class="proof-section">

                                <div class="proof-title">
                                    Bukti Transfer Customer
                                </div>

                                <a
                                    href="{{ route('payment-proofs.show', ['order' => $order->id, 'payment' => $payment->id]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <img
                                        src="{{ route('payment-proofs.show', ['order' => $order->id, 'payment' => $payment->id]) }}"
                                        alt="Bukti pembayaran {{ $payment->payment_code }}"
                                        class="proof-preview"
                                    >
                                </a>

                                <a
                                    href="{{ route('payment-proofs.show', ['order' => $order->id, 'payment' => $payment->id]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="proof-link"
                                >
                                    Buka bukti ukuran penuh ↗
                                </a>

                            </div>

                        @elseif (
                            $payment->status === 'pending'
                            && $isManualTransfer
                        )

                            <div class="proof-section">
                                <div class="empty-box">
                                    Customer belum mengupload bukti pembayaran.
                                </div>
                            </div>

                        @endif


                        {{-- =====================================
                            ALASAN PENOLAKAN
                        ====================================== --}}

                        @if ($isRejected)

                            <div class="rejection-box">

                                <strong>
                                    Bukti pembayaran ditolak.
                                </strong>

                                <div>
                                    Alasan: {{ $payment->rejection_reason }}
                                </div>

                                <div style="margin-top: 8px;">
                                    Customer dapat mengupload bukti
                                    pembayaran baru dari halaman detail order.
                                </div>

                            </div>

                        @endif


                        {{-- =====================================
                            PEMBAYARAN TERVERIFIKASI
                        ====================================== --}}

                        @if (
                            $payment->status === 'paid'
                            && $payment->verified_at
                        )

                            <div class="verified-box">

                                <strong>
                                    Pembayaran sudah diverifikasi.
                                </strong>

                                <br>

                                Diverifikasi
                                {{ $payment->verified_at->format('d M Y H:i') }}
                                WIB

                                @if ($payment->verifier)
                                    oleh {{ $payment->verifier->name }}.
                                @endif

                            </div>

                        @endif


                        {{-- =====================================
                            FORM VERIFIKASI ADMIN
                        ====================================== --}}

                        @if ($canVerify)

                            <div class="verification-box">

                                <div class="verification-title">
                                    Verifikasi Pembayaran Manual
                                </div>

                                <div class="verification-text">
                                    Pastikan nominal, rekening tujuan,
                                    tanggal transfer, dan bukti pembayaran
                                    sudah sesuai sebelum menyetujui transaksi.

                                    Setelah disetujui, order menjadi lunas,
                                    invoice diterbitkan, dan paket peserta
                                    otomatis diaktifkan.
                                </div>


                                <div class="action-grid">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.orders.payments.verify',
                                            [
                                                'order' => $order->id,
                                                'payment' => $payment->id,
                                            ]
                                        ) }}"
                                        class="verify-payment-form"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="verify-button"
                                        >
                                            ✓ Verifikasi Pembayaran
                                        </button>

                                    </form>


                                    <button
                                        type="button"
                                        class="reject-button"
                                        data-reject-toggle="{{ $payment->id }}"
                                        aria-controls="reject-form-{{ $payment->id }}"
                                        aria-expanded="{{
                                            old('payment_id') == $payment->id
                                            && $errors->has('rejection_reason')
                                                ? 'true'
                                                : 'false'
                                        }}"
                                    >
                                        Tolak Bukti
                                    </button>

                                </div>


                                <div
                                    class="reject-form {{
                                        old('payment_id') == $payment->id
                                        && $errors->has('rejection_reason')
                                            ? 'active'
                                            : ''
                                    }}"
                                    id="reject-form-{{ $payment->id }}"
                                >

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.orders.payments.reject',
                                            [
                                                'order' => $order->id,
                                                'payment' => $payment->id,
                                            ]
                                        ) }}"
                                        class="reject-payment-form"
                                    >
                                        @csrf

                                        <input
                                            type="hidden"
                                            name="payment_id"
                                            value="{{ $payment->id }}"
                                        >


                                        <label
                                            for="rejection_reason_{{ $payment->id }}"
                                            class="form-label"
                                        >
                                            Alasan Penolakan
                                        </label>


                                        <textarea
                                            id="rejection_reason_{{ $payment->id }}"
                                            name="rejection_reason"
                                            class="form-textarea"
                                            required
                                            minlength="3"
                                            maxlength="1000"
                                            placeholder="Contoh: nominal transfer tidak sesuai atau bukti transfer kurang jelas."
                                        >{{ old('payment_id') == $payment->id ? old('rejection_reason') : '' }}</textarea>


                                        @if (
                                            old('payment_id') == $payment->id
                                            && $errors->has('rejection_reason')
                                        )

                                            <div class="field-error">
                                                {{ $errors->first('rejection_reason') }}
                                            </div>

                                        @endif


                                        <button
                                            type="submit"
                                            class="reject-submit"
                                        >
                                            Konfirmasi Tolak Bukti
                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="empty-box">
                        Belum ada data pembayaran untuk order ini.
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- =========================================================
        KOLOM KANAN
    ========================================================== --}}

    <div>

        {{-- =====================================================
            INVOICE
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Invoice
                </div>

                <div class="section-description">
                    Informasi invoice transaksi.
                </div>
            </div>

            <div class="section-body">

                @if ($order->invoice)

                    <div class="info-grid">

                        <div class="info-item">
                            <div class="info-label">
                                Nomor Invoice
                            </div>

                            <div class="info-value strong">
                                {{ $order->invoice->invoice_number }}
                            </div>
                        </div>


                        <div class="info-item">
                            <div class="info-label">
                                Status
                            </div>

                            <div class="info-value">

                                <span class="status-badge status-{{
                                    $order->invoice->status === 'unpaid'
                                        ? 'pending'
                                        : $order->invoice->status
                                }}">
                                    {{ ucfirst($order->invoice->status) }}
                                </span>

                            </div>
                        </div>


                        <div class="info-item">
                            <div class="info-label">
                                Diterbitkan
                            </div>

                            <div class="info-value">
                                {{
                                    $order->invoice->issued_at
                                        ? $order->invoice->issued_at->format('d M Y H:i') . ' WIB'
                                        : '-'
                                }}
                            </div>
                        </div>


                        <div class="info-item">
                            <div class="info-label">
                                Jatuh Tempo
                            </div>

                            <div class="info-value">
                                {{
                                    $order->invoice->due_at
                                        ? $order->invoice->due_at->format('d M Y H:i') . ' WIB'
                                        : '-'
                                }}
                            </div>
                        </div>

                    </div>


                    <a
                        href="{{ route(
                            'admin.invoices.show',
                            $order->invoice
                        ) }}"
                        class="invoice-action"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        ↗ Lihat / Cetak Invoice
                    </a>


                    <p class="invoice-action-note">
                        Invoice dibuka di tab baru.
                        Gunakan tombol cetak untuk mencetak
                        atau menyimpannya sebagai PDF.
                    </p>

                @else

                    <div class="empty-box">
                        Invoice belum tersedia untuk order ini.
                    </div>

                @endif

            </div>

        </div>


        {{-- =====================================================
            TIMELINE
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Ringkasan Status
                </div>

                <div class="section-description">
                    Waktu penting dalam proses transaksi.
                </div>
            </div>

            <div class="section-body">

                <div class="timeline">

                    <div class="timeline-item">

                        <span class="timeline-dot"></span>

                        <div class="timeline-title">
                            Order dibuat
                        </div>

                        <div class="timeline-time">
                            {{
                                $order->ordered_at
                                    ? $order->ordered_at->format('d M Y H:i') . ' WIB'
                                    : '-'
                            }}
                        </div>

                        <div class="timeline-description">
                            Customer membuat transaksi pembelian paket.
                        </div>

                    </div>


                    @foreach (
                        $order->payments
                            ->whereNotNull('proof_uploaded_at')
                            ->sortBy('proof_uploaded_at')
                        as $payment
                    )

                        <div class="timeline-item">

                            <span class="timeline-dot"></span>

                            <div class="timeline-title">
                                Bukti pembayaran diupload
                            </div>

                            <div class="timeline-time">
                                {{
                                    $payment->proof_uploaded_at
                                        ->format('d M Y H:i')
                                }}
                                WIB
                            </div>

                            <div class="timeline-description">

                                Customer mengirim bukti transfer untuk
                                pembayaran {{ $payment->payment_code }}.

                                @if (
                                    $payment->status === 'pending'
                                    && !empty($payment->rejection_reason)
                                )

                                    <br>
                                    <strong>
                                        Status: Bukti ditolak.
                                    </strong>

                                @endif

                            </div>

                        </div>

                    @endforeach


                    @if ($order->paid_at)

                        <div class="timeline-item">

                            <span class="timeline-dot"></span>

                            <div class="timeline-title">
                                Pembayaran berhasil
                            </div>

                            <div class="timeline-time">
                                {{ $order->paid_at->format('d M Y H:i') }}
                                WIB
                            </div>

                            <div class="timeline-description">
                                Order telah tercatat sebagai transaksi lunas.
                            </div>

                        </div>

                    @endif


                    <div class="timeline-item">

                        <span class="timeline-dot"></span>

                        <div class="timeline-title">
                            Status saat ini
                        </div>

                        <div class="timeline-time">
                            {{ $displayOrderStatus }}
                        </div>

                        <div class="timeline-description">
                            Status order terbaru yang tersimpan di sistem.
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            CATATAN
        ====================================================== --}}

        <div class="card section-card">

            <div class="section-header">
                <div class="section-title">
                    Catatan Order
                </div>
            </div>

            <div class="section-body">

                @if ($order->notes)

                    <div class="notes-box">
                        {{ $order->notes }}
                    </div>

                @else

                    <div class="empty-box">
                        Tidak ada catatan tambahan.
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Buka / Tutup Form Penolakan
        |--------------------------------------------------------------------------
        */

        const rejectButtons = document.querySelectorAll(
            '[data-reject-toggle]'
        );

        rejectButtons.forEach(function (button) {

            button.addEventListener('click', function () {

                const paymentId = button.getAttribute(
                    'data-reject-toggle'
                );

                const form = document.getElementById(
                    'reject-form-' + paymentId
                );

                if (!form) {
                    return;
                }

                form.classList.toggle('active');

                const isActive = form.classList.contains('active');

                button.setAttribute(
                    'aria-expanded',
                    isActive ? 'true' : 'false'
                );

                if (isActive) {

                    const textarea = form.querySelector('textarea');

                    if (textarea) {
                        textarea.focus();
                    }

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Konfirmasi Verifikasi
        |--------------------------------------------------------------------------
        */

        const verifyForms = document.querySelectorAll(
            '.verify-payment-form'
        );

        verifyForms.forEach(function (form) {

            form.addEventListener('submit', function (event) {

                const confirmed = window.confirm(
                    'Verifikasi pembayaran ini? Setelah disetujui, order akan menjadi lunas, invoice diterbitkan, dan paket peserta langsung diaktifkan.'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Memproses...';
                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Konfirmasi Penolakan
        |--------------------------------------------------------------------------
        */

        const rejectForms = document.querySelectorAll(
            '.reject-payment-form'
        );

        rejectForms.forEach(function (form) {

            form.addEventListener('submit', function (event) {

                /*
                | Biarkan validasi HTML berjalan terlebih dahulu.
                */

                if (!form.checkValidity()) {
                    return;
                }

                const confirmed = window.confirm(
                    'Tolak bukti pembayaran ini? Customer akan diminta mengupload bukti baru.'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Memproses...';
                }

            });

        });

    });
</script>
@endpush
