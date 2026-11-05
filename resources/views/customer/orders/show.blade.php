
@extends('layouts.customer')

@section('title', 'Detail Pesanan')
@section('topbar-title', 'Detail Pesanan')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Informasi Pembayaran
    |--------------------------------------------------------------------------
    */

    $latestPayment = $order->payments
        ->sortByDesc('id')
        ->first();

    $manualPayment = $order->payments
        ->filter(fn ($payment) =>
            $payment->isPending()
            && $payment->isManualTransfer()
        )
        ->sortByDesc('id')
        ->first();

    $hasPaymentProof = $manualPayment?->hasProof() ?? false;

    $proofRejected = $manualPayment
        ? (
            $manualPayment->isPending()
            && $manualPayment->hasRejection()
        )
        : false;

    $waitingVerification = $manualPayment?->isWaitingVerification()
        ?? false;

    $canUploadProof = $manualPayment
        && (
            $manualPayment->isWaitingForProof()
            || $proofRejected
        );

    /*
    |--------------------------------------------------------------------------
    | Rekening Pembayaran
    |--------------------------------------------------------------------------
    */

    $bankName = $bankAccount['bank_name'] ?? null;

    $bankAccountNumber =
        $bankAccount['account_number'] ?? null;

    $bankAccountHolder =
        $bankAccount['account_holder'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | Biaya Pendaftaran
    |--------------------------------------------------------------------------
    |
    | Menggunakan nilai yang tersimpan di order,
    | bukan tarif terbaru dari pengaturan admin.
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

<div class="order-page">

    {{-- SUCCESS MESSAGE --}}

    @if (session('success'))
        <div class="order-alert success">
            <div class="alert-icon">✓</div>
            <div>
                <strong>Berhasil</strong>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- WARNING MESSAGE --}}

    @if (session('warning'))
        <div class="order-alert warning">
            <div class="alert-icon">!</div>
            <div>
                <strong>Perhatian</strong>
                <p>{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}

    @if (session('error'))
        <div class="order-alert error">
            <div class="alert-icon">!</div>
            <div>
                <strong>Terjadi Kesalahan</strong>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}

    @if ($errors->any())
        <div class="order-alert error">
            <div class="alert-icon">!</div>
            <div>
                <strong>Periksa Kembali Data</strong>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- HEADER --}}

    <div class="order-header">

        <div>
            <a
                href="{{ route('customer.packages.index') }}"
                class="back-link"
            >
                ← Kembali ke Paket Saya
            </a>

            <h1 class="page-title">
                Detail Pesanan
            </h1>

            <p class="page-description">
                Periksa status dan rincian transaksi paket
                Ragen Swim Course.
            </p>
        </div>

        <div class="order-status-wrapper">

            @switch($order->status)

                @case('pending')

                    @if ($waitingVerification)
                        <span class="order-status verification">
                            Menunggu Verifikasi
                        </span>
                    @elseif ($proofRejected)
                        <span class="order-status rejected">
                            Bukti Ditolak
                        </span>
                    @else
                        <span class="order-status pending">
                            Menunggu Pembayaran
                        </span>
                    @endif

                    @break

                @case('paid')
                    <span class="order-status paid">
                        Lunas
                    </span>
                    @break

                @case('cancelled')
                    <span class="order-status cancelled">
                        Dibatalkan
                    </span>
                    @break

                @case('expired')
                    <span class="order-status expired">
                        Kedaluwarsa
                    </span>
                    @break

                @case('refunded')
                    <span class="order-status refunded">
                        Dikembalikan
                    </span>
                    @break

                @default
                    <span class="order-status">
                        {{ ucfirst($order->status) }}
                    </span>

            @endswitch

        </div>

    </div>

    <div class="order-layout">

        {{-- LEFT SIDE --}}

        <div class="order-main">

            {{-- INFORMASI PESANAN --}}

            <section class="order-card">

                <div class="card-heading">
                    <div class="heading-icon">🧾</div>
                    <div>
                        <span class="heading-label">Transaksi</span>
                        <h2>Informasi Pesanan</h2>
                    </div>
                </div>

                <div class="order-information">

                    <div class="information-item">
                        <span>Kode Pesanan</span>
                        <strong class="order-code">
                            {{ $order->order_code }}
                        </strong>
                    </div>

                    <div class="information-item">
                        <span>Tanggal Pesanan</span>
                        <strong>
                            {{
                                $order->ordered_at
                                    ? $order->ordered_at->format('d M Y, H:i')
                                    : '-'
                            }}
                        </strong>
                    </div>

                    <div class="information-item">
                        <span>Customer</span>
                        <strong>
                            {{ $order->user?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="information-item">
                        <span>Status</span>
                        <strong>
                            {{ $order->status_label }}
                        </strong>
                    </div>

                </div>

            </section>

            {{-- PAKET YANG DIBELI --}}

            <section class="order-card">

                <div class="card-heading">
                    <div class="heading-icon">🏊</div>
                    <div>
                        <span class="heading-label">Paket</span>
                        <h2>Paket yang Dibeli</h2>
                    </div>
                </div>

                <div class="order-items">

                    @forelse ($order->items as $item)

                        <article class="order-item">

                            <div class="item-top">

                                <div class="item-program">
                                    <span>
                                        {{
                                            $item->package?->program?->name
                                            ?? 'Program Renang'
                                        }}
                                    </span>

                                    <h3>
                                        {{ $item->package_name }}
                                    </h3>
                                </div>

                                <div class="item-price">
                                    <span>Harga</span>
                                    <strong>
                                        Rp {{
                                            number_format(
                                                (float) $item->unit_price,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}
                                    </strong>
                                </div>

                            </div>

                            <div class="participant-information">

                                <div class="participant-avatar">
                                    {{
                                        strtoupper(
                                            mb_substr(
                                                $item->participant?->full_name
                                                ?? 'P',
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </div>

                                <div>
                                    <span>Peserta</span>
                                    <strong>
                                        {{
                                            $item->participant?->full_name
                                            ?? 'Data peserta tidak tersedia'
                                        }}
                                    </strong>
                                </div>

                            </div>

                            <div class="item-details">

                                <div>
                                    <span>Jumlah</span>
                                    <strong>
                                        {{ $item->quantity }} Paket
                                    </strong>
                                </div>

                                <div>
                                    <span>Pertemuan</span>
                                    <strong>
                                        @if ($item->package)
                                            {{
                                                (int) $item->package->total_sessions
                                                * (int) $item->quantity
                                            }}
                                            Sesi
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </div>

                                <div>
                                    <span>Masa Berlaku</span>
                                    <strong>
                                        @if (
                                            $item->package
                                            && $item->package->validity_days !== null
                                        )
                                            {{ $item->package->validity_days }}
                                            Hari
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </div>

                                <div>
                                    <span>Subtotal</span>
                                    <strong>
                                        Rp {{
                                            number_format(
                                                (float) $item->subtotal,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}
                                    </strong>
                                </div>

                            </div>

                        </article>

                    @empty

                        <div class="empty-order-item">
                            Data item pesanan tidak ditemukan.
                        </div>

                    @endforelse

                </div>

            </section>

            {{-- CATATAN --}}

            @if (!empty($order->notes))

                <section class="order-card">

                    <div class="card-heading">
                        <div class="heading-icon">📝</div>
                        <div>
                            <span class="heading-label">Catatan</span>
                            <h2>Catatan Pesanan</h2>
                        </div>
                    </div>

                    <div class="order-notes">
                        {{ $order->notes }}
                    </div>

                </section>

            @endif

            {{-- STATUS PEMBAYARAN --}}

            <section class="order-card">

                <div class="card-heading">
                    <div class="heading-icon">💳</div>
                    <div>
                        <span class="heading-label">Pembayaran</span>
                        <h2>Status Pembayaran</h2>
                    </div>
                </div>

                @if ($order->isPending())

                    @if ($waitingVerification)

                        <div class="payment-state verification">
                            <div class="payment-state-icon">⏳</div>
                            <div>
                                <strong>
                                    Menunggu Verifikasi Admin
                                </strong>
                                <p>
                                    Bukti pembayaran sudah dikirim.
                                    Admin Ragen Swim Course akan memeriksa
                                    pembayaran sebelum paket peserta diaktifkan.
                                </p>
                            </div>
                        </div>

                    @elseif ($proofRejected)

                        <div class="payment-state rejected">
                            <div class="payment-state-icon">!</div>
                            <div>
                                <strong>
                                    Bukti Pembayaran Ditolak
                                </strong>
                                <p>
                                    Bukti transfer belum dapat diterima.
                                    Periksa alasan penolakan dari Admin,
                                    kemudian kirim bukti pembayaran baru.
                                </p>
                            </div>
                        </div>

                    @else

                        <div class="payment-state pending">
                            <div class="payment-state-icon">⏳</div>
                            <div>
                                <strong>
                                    Menunggu Pembayaran
                                </strong>
                                <p>
                                    Pembayaran dapat dilakukan melalui
                                    transfer bank ke rekening resmi
                                    Ragen Swim Course.
                                </p>
                            </div>
                        </div>

                    @endif

                @elseif ($order->isPaid())

                    <div class="payment-state paid">
                        <div class="payment-state-icon">✓</div>
                        <div>
                            <strong>Pembayaran Berhasil</strong>
                            <p>
                                Pembayaran telah diterima.
                                Paket peserta sudah diaktifkan
                                dan dapat digunakan untuk proses
                                booking kelas.
                            </p>
                        </div>
                    </div>

                @elseif ($order->isCancelled())

                    <div class="payment-state cancelled">
                        <div class="payment-state-icon">×</div>
                        <div>
                            <strong>Pesanan Dibatalkan</strong>
                            <p>
                                Pesanan ini sudah dibatalkan dan
                                tidak dapat dilanjutkan ke pembayaran.
                            </p>
                        </div>
                    </div>

                @elseif ($order->isExpired())

                    <div class="payment-state cancelled">
                        <div class="payment-state-icon">!</div>
                        <div>
                            <strong>Pesanan Kedaluwarsa</strong>
                            <p>
                                Batas waktu pembayaran pesanan ini
                                sudah berakhir.
                            </p>
                        </div>
                    </div>

                @elseif ($order->isRefunded())

                    <div class="payment-state refunded">
                        <div class="payment-state-icon">↩</div>
                        <div>
                            <strong>Pembayaran Dikembalikan</strong>
                            <p>
                                Transaksi ini telah masuk ke status
                                pengembalian dana.
                            </p>
                        </div>
                    </div>

                @endif

            </section>

            {{-- TRANSFER MANUAL --}}

            @if ($order->isPending())

                <section class="order-card">

                    <div class="card-heading">
                        <div class="heading-icon">🏦</div>
                        <div>
                            <span class="heading-label">Transfer Bank</span>
                            <h2>Pembayaran Transfer Manual</h2>
                        </div>
                    </div>

                    @if (!$bankAccountIsConfigured)

                        <div class="account-unavailable-notice">
                            <strong>
                                Pembayaran Transfer Belum Tersedia
                            </strong>
                            <p>
                                Informasi rekening resmi Ragen Swim Course
                                belum dikonfigurasi. Pembayaran transfer
                                akan tersedia setelah rekening resmi
                                diaktifkan oleh Admin.
                            </p>
                        </div>

                    @elseif (!$manualPayment)

                        <div class="transfer-intro">
                            <strong>
                                Bayar melalui transfer bank
                            </strong>
                            <p>
                                Pilih transfer bank untuk menyiapkan
                                pembayaran dan mengunggah bukti transfer.
                            </p>
                        </div>

                        <form
                            action="{{ route('customer.payments.manual-transfer', $order) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="payment-button primary-payment-button"
                            >
                                Pilih Transfer Bank
                            </button>
                        </form>

                    @else

                        <div class="bank-information">

                            <div class="bank-information-header">
                                <div>
                                    <span>Rekening Tujuan</span>
                                    <strong>{{ $bankName }}</strong>
                                </div>
                                <div class="bank-badge">
                                    Transfer Manual
                                </div>
                            </div>

                            <div class="bank-details">

                                <div>
                                    <span>Nomor Rekening</span>
                                    <strong class="bank-account">
                                        {{ $bankAccountNumber }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Atas Nama</span>
                                    <strong>
                                        {{ $bankAccountHolder }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Nominal Transfer</span>
                                    <strong class="bank-amount">
                                        {{ $order->formatted_total }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                        @if ($proofRejected)

                            <div class="rejection-box">
                                <strong>
                                    Alasan Penolakan Admin
                                </strong>
                                <p>
                                    {{ $manualPayment->rejection_reason }}
                                </p>
                            </div>

                        @endif

                        @if ($hasPaymentProof)

                            <div class="proof-information">

                                <div class="proof-header">

                                    <div>
                                        <span>Bukti Pembayaran</span>
                                        <strong>
                                            {{
                                                $proofRejected
                                                    ? 'Bukti sebelumnya ditolak'
                                                    : 'Bukti berhasil dikirim'
                                            }}
                                        </strong>
                                    </div>

                                    @if ($waitingVerification)
                                        <span class="proof-status waiting">
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif ($proofRejected)
                                        <span class="proof-status rejected">
                                            Ditolak
                                        </span>
                                    @endif

                                </div>

                                <a
                                    href="{{ route('payment-proofs.show', ['order' => $order->id, 'payment' => $manualPayment->id]) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="proof-preview-link"
                                >
                                    <img
                                        src="{{ route('payment-proofs.show', ['order' => $order->id, 'payment' => $manualPayment->id]) }}"
                                        alt="Bukti pembayaran {{ $manualPayment->payment_code }}"
                                        class="proof-preview"
                                    >
                                </a>

                                @if ($manualPayment->proof_uploaded_at)

                                    <p class="proof-upload-time">
                                        Dikirim pada
                                        {{
                                            $manualPayment
                                                ->proof_uploaded_at
                                                ->format('d M Y, H:i')
                                        }}
                                    </p>

                                @endif

                            </div>

                        @endif

                        @if ($canUploadProof)

                            <div class="upload-section">

                                <div class="upload-heading">
                                    <strong>
                                        {{
                                            $proofRejected
                                                ? 'Upload Ulang Bukti Pembayaran'
                                                : 'Upload Bukti Pembayaran'
                                        }}
                                    </strong>

                                    <p>
                                        Gunakan foto atau screenshot bukti
                                        transfer. Format JPG, JPEG, PNG,
                                        atau WEBP dengan ukuran maksimal 5 MB.
                                    </p>
                                </div>

                                <form
                                    action="{{ route('customer.payments.upload-proof', $order) }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                    class="proof-form"
                                    id="proof-upload-form"
                                >
                                    @csrf

                                    <label
                                        for="proof_image"
                                        class="upload-box"
                                    >
                                        <span class="upload-icon">↑</span>

                                        <strong>
                                            Pilih Bukti Transfer
                                        </strong>

                                        <small id="proof-file-name">
                                            JPG, JPEG, PNG, WEBP • Maks. 5 MB
                                        </small>
                                    </label>

                                    <input
                                        type="file"
                                        name="proof_image"
                                        id="proof_image"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        class="proof-input"
                                        required
                                    >

                                    <button
                                        type="submit"
                                        class="payment-button primary-payment-button"
                                        id="upload-proof-button"
                                    >
                                        {{
                                            $proofRejected
                                                ? 'Kirim Bukti Baru'
                                                : 'Kirim Bukti Pembayaran'
                                        }}
                                    </button>

                                </form>

                            </div>

                        @elseif ($waitingVerification)

                            <div class="verification-note">
                                <div>✓</div>
                                <p>
                                    Bukti sudah diterima sistem.
                                    Tidak perlu mengirim ulang selama
                                    pembayaran diperiksa oleh Admin.
                                </p>
                            </div>

                        @endif

                    @endif

                </section>

            @endif

            {{-- RIWAYAT PEMBAYARAN --}}

            @if ($order->payments->isNotEmpty())

                <section class="order-card">

                    <div class="card-heading">
                        <div class="heading-icon">💰</div>
                        <div>
                            <span class="heading-label">Riwayat</span>
                            <h2>Informasi Pembayaran</h2>
                        </div>
                    </div>

                    <div class="payment-history">

                        @foreach (
                            $order->payments->sortByDesc('id')
                            as $payment
                        )

                            <div class="payment-history-item">

                                <div>
                                    <span>Kode Pembayaran</span>
                                    <strong>
                                        {{ $payment->payment_code }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Metode</span>
                                    <strong>
                                        {{ $payment->payment_display }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Nominal</span>
                                    <strong>
                                        {{ $payment->formatted_amount }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Status</span>
                                    <strong>
                                        {{ $payment->status_label }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Bukti</span>
                                    <strong>
                                        @if (
                                            $payment->isPending()
                                            && $payment->hasRejection()
                                        )
                                            Ditolak
                                        @elseif ($payment->hasProof())
                                            Sudah Dikirim
                                        @else
                                            Belum Dikirim
                                        @endif
                                    </strong>
                                </div>

                                @if ($payment->paid_at)
                                    <div>
                                        <span>Dibayar</span>
                                        <strong>
                                            {{
                                                $payment
                                                    ->paid_at
                                                    ->format('d M Y, H:i')
                                            }}
                                        </strong>
                                    </div>
                                @endif

                            </div>

                        @endforeach

                    </div>

                </section>

            @endif

            {{-- INFORMASI INVOICE --}}

            @if ($order->invoice)

                <section class="order-card">

                    <div class="card-heading">
                        <div class="heading-icon">📄</div>
                        <div>
                            <span class="heading-label">Invoice</span>
                            <h2>Informasi Invoice</h2>
                        </div>
                    </div>

                    <div class="invoice-information">

                        <div>
                            <span>Nomor Invoice</span>
                            <strong>
                                {{ $order->invoice->invoice_number }}
                            </strong>
                        </div>

                        <div>
                            <span>Diterbitkan</span>
                            <strong>
                                {{ $order->invoice->formatted_issued_at }}
                            </strong>
                        </div>

                        <div>
                            <span>Status</span>
                            <strong>
                                {{ $order->invoice->status_label }}
                            </strong>
                        </div>

                        <div>
                            <span>Total</span>
                            <strong>
                                {{ $order->invoice->formatted_total }}
                            </strong>
                        </div>

                    </div>

                    <a
                        href="{{ route('customer.invoices.show', $order->invoice) }}"
                        class="invoice-print-button"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        📄 Lihat / Cetak Invoice
                    </a>

                </section>

            @endif

        </div>

        {{-- RIGHT SIDE --}}

        <aside class="order-sidebar">

            <div class="payment-summary">

                <h2>Ringkasan Pembayaran</h2>

                <div class="summary-lines">

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <strong>
                            {{ $order->formatted_subtotal }}
                        </strong>
                    </div>

                    <div class="summary-line">
                        <span>Diskon</span>
                        <strong>
                            @if ((float) $order->discount_amount > 0)
                                - {{ $order->formatted_discount }}
                            @else
                                Rp 0
                            @endif
                        </strong>
                    </div>

                    {{-- BIAYA PENDAFTARAN --}}

                    <div class="summary-line registration-fee-line">
                        <span>Biaya Pendaftaran</span>
                        <strong>
                            {{ $formattedRegistrationFee }}
                        </strong>
                    </div>

                    @if ($hasRegistrationFee)

                        <div class="registration-fee-information">

                            @if ($order->isPaid())

                                <strong>
                                    ✓ Biaya Pendaftaran Telah Dibayar
                                </strong>

                                <p>
                                    Biaya pendaftaran peserta telah
                                    termasuk dalam pembayaran pesanan ini.
                                    Pembelian paket berikutnya tidak
                                    dikenakan biaya pendaftaran lagi.
                                </p>

                            @elseif ($order->isPending())

                                <strong>
                                    Biaya Pendaftaran Satu Kali
                                </strong>

                                <p>
                                    Biaya ini dikenakan untuk pendaftaran
                                    pertama peserta. Status pendaftaran
                                    menjadi lunas setelah pembayaran
                                    berhasil diverifikasi.
                                </p>

                            @else

                                <strong>
                                    Rincian Biaya Pendaftaran
                                </strong>

                                <p>
                                    Biaya pendaftaran tercatat pada
                                    pesanan ini. Status pendaftaran
                                    mengikuti hasil pembayaran.
                                </p>

                            @endif

                        </div>

                    @endif

                </div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <strong>
                        {{ $order->formatted_total }}
                    </strong>
                </div>

                @if ($order->isPending())

                    @if ($waitingVerification)

                        <div class="sidebar-status verification">
                            <div class="sidebar-status-icon">⏳</div>
                            <div>
                                <strong>
                                    Menunggu Verifikasi
                                </strong>
                                <p>
                                    Bukti pembayaran sedang diperiksa Admin.
                                </p>
                            </div>
                        </div>

                    @elseif ($proofRejected)

                        <div class="sidebar-status rejected">
                            <div class="sidebar-status-icon">!</div>
                            <div>
                                <strong>Bukti Ditolak</strong>
                                <p>
                                    Silakan upload bukti pembayaran baru.
                                </p>
                            </div>
                        </div>

                    @elseif (!$bankAccountIsConfigured)

                        <div class="payment-warning">
                            <div>ℹ️</div>
                            <p>
                                Pembayaran transfer bank belum tersedia
                                karena rekening resmi belum dikonfigurasi.
                            </p>
                        </div>

                    @elseif ($manualPayment)

                        <div class="payment-warning">
                            <div>ℹ️</div>
                            <p>
                                Transfer sesuai total pembayaran kemudian
                                upload bukti transfer pada formulir
                                di halaman ini.
                            </p>
                        </div>

                    @else

                        <div class="payment-warning">
                            <div>ℹ️</div>
                            <p>
                                Paket belum ditambahkan ke akun peserta
                                sampai pembayaran diverifikasi dan
                                dinyatakan berhasil.
                            </p>
                        </div>

                        <form
                            action="{{ route('customer.payments.manual-transfer', $order) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="payment-button primary-payment-button"
                            >
                                Bayar via Transfer Bank
                            </button>
                        </form>

                    @endif

                    <p class="payment-coming-soon">
                        Pembayaran manual memerlukan verifikasi Admin
                        sebelum paket peserta aktif.
                    </p>

                @elseif ($order->isPaid())

                    <div class="paid-summary-box">
                        <div class="paid-check">✓</div>
                        <div>
                            <strong>Pembayaran Lunas</strong>
                            <p>
                                Paket peserta telah diaktifkan.
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('customer.packages.index') }}"
                        class="package-button"
                    >
                        Lihat Paket Saya
                    </a>

                @endif

                <div class="transaction-security">
                    <span>🔒</span>
                    <p>
                        Informasi transaksi dan bukti pembayaran
                        disimpan oleh sistem Ragen Swim Course.
                    </p>
                </div>

            </div>

        </aside>

    </div>

</div>

@endsection

@push('styles')

<style>
    .order-page {
        width: 100%;
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 12px;
        color: var(--blue-dark);
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .order-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 11px;
    }

    .order-alert.success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .order-alert.warning {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .order-alert.error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .alert-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.7);
        font-size: 11px;
        font-weight: 900;
    }

    .order-alert strong {
        display: block;
        font-size: 10px;
        font-weight: 850;
    }

    .order-alert p {
        margin-top: 2px;
        font-size: 9px;
        line-height: 1.5;
    }

    .order-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
    }

    .order-status-wrapper {
        flex-shrink: 0;
    }

    .order-status {
        display: inline-flex;
        padding: 7px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 9px;
        font-weight: 800;
    }

    .order-status.pending {
        background: #fef3c7;
        color: #b45309;
    }

    .order-status.verification {
        background: #e0f2fe;
        color: #0369a1;
    }

    .order-status.rejected {
        background: #fee2e2;
        color: #b91c1c;
    }

    .order-status.paid {
        background: #dcfce7;
        color: #15803d;
    }

    .order-status.cancelled,
    .order-status.expired {
        background: #fee2e2;
        color: #b91c1c;
    }

    .order-status.refunded {
        background: #e0f2fe;
        color: #0369a1;
    }

    .order-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        align-items: start;
        gap: 20px;
    }

    .order-main {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .order-sidebar {
        position: sticky;
        top: 90px;
    }

    .order-card,
    .payment-summary {
        border: 1px solid var(--border);
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .order-card {
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

    .heading-label {
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

    .order-information {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .information-item {
        padding: 12px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .information-item span {
        display: block;
        color: var(--text-light);
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .information-item strong {
        display: block;
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 10px;
        font-weight: 800;
    }

    .information-item .order-code {
        color: #0369a1;
    }

    .order-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .order-item {
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding-bottom: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    .item-program span {
        display: block;
        margin-bottom: 3px;
        color: #0369a1;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .item-program h3 {
        color: var(--navy);
        font-size: 13px;
        font-weight: 850;
    }

    .item-price {
        flex-shrink: 0;
        text-align: right;
    }

    .item-price span {
        display: block;
        color: var(--text-light);
        font-size: 8px;
    }

    .item-price strong {
        display: block;
        margin-top: 2px;
        color: #0369a1;
        font-size: 12px;
        font-weight: 900;
    }

    .participant-information {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-top: 13px;
        padding: 10px;
        border-radius: 9px;
        background: #f0f9ff;
    }

    .participant-avatar {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        color: #0369a1;
        font-size: 11px;
        font-weight: 900;
    }

    .participant-information span {
        display: block;
        color: #0284c7;
        font-size: 8px;
    }

    .participant-information strong {
        display: block;
        margin-top: 2px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .item-details {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }

    .item-details > div {
        padding: 9px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .item-details span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .item-details strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 9px;
        font-weight: 800;
    }

    .empty-order-item {
        padding: 20px;
        border-radius: 10px;
        background: #f8fafc;
        color: var(--text-soft);
        font-size: 9px;
        text-align: center;
    }

    .order-notes {
        padding: 13px;
        border-radius: 9px;
        background: #f8fafc;
        color: #475569;
        font-size: 10px;
        line-height: 1.65;
        white-space: pre-line;
    }

    .payment-state {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 14px;
        border-radius: 11px;
    }

    .payment-state.pending {
        background: #fffbeb;
        color: #92400e;
    }

    .payment-state.verification {
        background: #f0f9ff;
        color: #075985;
    }

    .payment-state.rejected {
        background: #fef2f2;
        color: #991b1b;
    }

    .payment-state.paid {
        background: #f0fdf4;
        color: #166534;
    }

    .payment-state.cancelled {
        background: #fef2f2;
        color: #991b1b;
    }

    .payment-state.refunded {
        background: #f0f9ff;
        color: #075985;
    }

    .payment-state-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        font-weight: 900;
    }

    .payment-state strong {
        display: block;
        font-size: 10px;
        font-weight: 850;
    }

    .payment-state p {
        max-width: 550px;
        margin-top: 3px;
        font-size: 9px;
        line-height: 1.6;
    }

    .transfer-intro {
        padding: 14px;
        border: 1px solid #bae6fd;
        border-radius: 11px;
        background: #f0f9ff;
    }

    .transfer-intro strong {
        display: block;
        color: #075985;
        font-size: 11px;
        font-weight: 850;
    }

    .transfer-intro p {
        margin-top: 4px;
        color: #0369a1;
        font-size: 9px;
        line-height: 1.6;
    }

    .account-unavailable-notice {
        padding: 14px;
        border: 1px solid #fde68a;
        border-radius: 11px;
        background: #fffbeb;
        color: #92400e;
    }

    .account-unavailable-notice strong {
        display: block;
        font-size: 10px;
        font-weight: 850;
    }

    .account-unavailable-notice p {
        margin-top: 4px;
        font-size: 9px;
        line-height: 1.6;
    }

    .bank-information {
        overflow: hidden;
        border: 1px solid #bae6fd;
        border-radius: 13px;
        background: #f8fdff;
    }

    .bank-information-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px;
        border-bottom: 1px solid #e0f2fe;
    }

    .bank-information-header span,
    .bank-details span {
        display: block;
        color: #64748b;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .bank-information-header strong {
        display: block;
        margin-top: 2px;
        color: #075985;
        font-size: 16px;
        font-weight: 900;
    }

    .bank-badge {
        padding: 5px 8px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 8px;
        font-weight: 800;
    }

    .bank-details {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1px;
        background: #e0f2fe;
    }

    .bank-details > div {
        padding: 14px;
        background: #ffffff;
    }

    .bank-details strong {
        display: block;
        margin-top: 4px;
        color: #0f172a;
        font-size: 10px;
        font-weight: 850;
        overflow-wrap: anywhere;
    }

    .bank-details .bank-account {
        color: #0369a1;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .bank-details .bank-amount {
        color: #0369a1;
        font-size: 12px;
    }

    .rejection-box {
        margin-top: 14px;
        padding: 13px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
    }

    .rejection-box strong {
        display: block;
        font-size: 10px;
        font-weight: 850;
    }

    .rejection-box p {
        margin-top: 4px;
        font-size: 9px;
        line-height: 1.6;
    }

    .proof-information {
        margin-top: 14px;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #f8fafc;
    }

    .proof-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .proof-header span {
        display: block;
        color: #64748b;
        font-size: 8px;
    }

    .proof-header strong {
        display: block;
        margin-top: 2px;
        color: #0f172a;
        font-size: 10px;
        font-weight: 850;
    }

    .proof-status {
        display: inline-flex !important;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 8px !important;
        font-weight: 800;
        white-space: nowrap;
    }

    .proof-status.waiting {
        background: #e0f2fe;
        color: #0369a1;
    }

    .proof-status.rejected {
        background: #fee2e2;
        color: #b91c1c;
    }

    .proof-preview-link {
        display: block;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
    }

    .proof-preview {
        display: block;
        width: 100%;
        max-height: 330px;
        object-fit: contain;
        background: #ffffff;
    }

    .proof-upload-time {
        margin-top: 8px;
        color: #64748b;
        font-size: 8px;
    }

    .upload-section {
        margin-top: 14px;
    }

    .upload-heading strong {
        display: block;
        color: #0f172a;
        font-size: 11px;
        font-weight: 850;
    }

    .upload-heading p {
        margin-top: 3px;
        color: #64748b;
        font-size: 9px;
        line-height: 1.6;
    }

    .proof-form {
        margin-top: 11px;
    }

    .proof-input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    .upload-box {
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;
        padding: 16px;
        border: 1.5px dashed #7dd3fc;
        border-radius: 11px;
        background: #f8fdff;
        color: #075985;
        text-align: center;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .upload-box:hover {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .upload-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e0f2fe;
        font-size: 15px;
        font-weight: 900;
    }

    .upload-box strong {
        font-size: 10px;
        font-weight: 850;
    }

    .upload-box small {
        color: #64748b;
        font-size: 8px;
        line-height: 1.5;
    }

    .verification-note {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 14px;
        padding: 11px;
        border-radius: 9px;
        background: #f0f9ff;
        color: #075985;
    }

    .verification-note > div {
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e0f2fe;
        font-size: 9px;
        font-weight: 900;
    }

    .verification-note p {
        margin: 0;
        font-size: 8px;
        line-height: 1.6;
    }

    .payment-history {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .payment-history-item {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
        padding: 12px;
        border-radius: 10px;
        background: #f8fafc;
    }

    .payment-history-item span,
    .invoice-information span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .payment-history-item strong,
    .invoice-information strong {
        display: block;
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 9px;
        font-weight: 800;
    }

    .invoice-information {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
    }

    .invoice-information > div {
        padding: 11px;
        border-radius: 9px;
        background: #f8fafc;
    }

    .invoice-print-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        margin-top: 16px;
        padding: 10px 16px;
        border: 1px solid #0284c7;
        border-radius: 9px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 10px;
        font-weight: 850;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .invoice-print-button:hover {
        background: #0284c7;
        color: #ffffff;
    }

    .payment-summary {
        padding: 20px;
    }

    .payment-summary > h2 {
        color: var(--navy);
        font-size: 14px;
        font-weight: 850;
    }

    .summary-lines {
        padding: 16px 0;
    }

    .summary-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
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
        color: #334155;
        font-size: 9px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Biaya Pendaftaran
    |--------------------------------------------------------------------------
    */

    .registration-fee-line {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
    }

    .registration-fee-line span {
        color: #0369a1;
        font-weight: 800;
    }

    .registration-fee-line strong {
        color: #0369a1;
        font-weight: 900;
    }

    .registration-fee-information {
        margin-top: 11px;
        padding: 11px 12px;
        border: 1px solid #bae6fd;
        border-radius: 9px;
        background: #f0f9ff;
    }

    .registration-fee-information strong {
        display: block;
        color: #075985;
        font-size: 9px;
        font-weight: 850;
    }

    .registration-fee-information p {
        margin-top: 4px;
        color: #0369a1;
        font-size: 8px;
        line-height: 1.65;
    }

    .summary-total {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }

    .summary-total span {
        color: #475569;
        font-size: 9px;
        font-weight: 700;
    }

    .summary-total strong {
        color: #0369a1;
        font-size: 18px;
        font-weight: 900;
    }

    .payment-warning {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 14px;
        padding: 9px;
        border-radius: 8px;
        background: #fffbeb;
        color: #92400e;
    }

    .payment-warning p {
        margin: 0;
        font-size: 8px;
        line-height: 1.55;
    }

    .payment-button,
    .package-button {
        width: 100%;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 14px;
        padding: 10px 13px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 10px;
        font-weight: 850;
        text-decoration: none;
    }

    .payment-button {
        border: 0;
    }

    .primary-payment-button {
        background: linear-gradient(
            135deg,
            var(--blue),
            var(--blue-dark)
        );
        color: #ffffff;
        cursor: pointer;
        transition: 0.2s ease;
        box-shadow: 0 6px 15px rgba(2, 132, 199, 0.16);
    }

    .primary-payment-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(2, 132, 199, 0.22);
    }

    .primary-payment-button:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
    }

    .package-button {
        border: 0;
        background: linear-gradient(
            135deg,
            var(--blue),
            var(--blue-dark)
        );
        color: #ffffff;
        transition: 0.2s ease;
    }

    .package-button:hover {
        transform: translateY(-1px);
    }

    .payment-coming-soon {
        margin-top: 7px;
        color: var(--text-light);
        font-size: 8px;
        line-height: 1.5;
        text-align: center;
    }

    .sidebar-status {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 14px;
        padding: 10px;
        border-radius: 9px;
    }

    .sidebar-status.verification {
        background: #f0f9ff;
        color: #075985;
    }

    .sidebar-status.rejected {
        background: #fef2f2;
        color: #991b1b;
    }

    .sidebar-status-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.7);
        font-size: 10px;
        font-weight: 900;
    }

    .sidebar-status strong {
        display: block;
        font-size: 9px;
        font-weight: 850;
    }

    .sidebar-status p {
        margin-top: 2px;
        font-size: 8px;
        line-height: 1.5;
    }

    .paid-summary-box {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-top: 14px;
        padding: 10px;
        border-radius: 9px;
        background: #f0fdf4;
        color: #166534;
    }

    .paid-check {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #dcfce7;
        font-size: 11px;
        font-weight: 900;
    }

    .paid-summary-box strong {
        display: block;
        font-size: 9px;
        font-weight: 850;
    }

    .paid-summary-box p {
        margin-top: 2px;
        font-size: 8px;
    }

    .transaction-security {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 15px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    .transaction-security span {
        flex-shrink: 0;
        font-size: 9px;
    }

    .transaction-security p {
        margin: 0;
        color: var(--text-light);
        font-size: 8px;
        line-height: 1.5;
    }

    @media (max-width: 1050px) {
        .payment-history-item {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .invoice-information {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 950px) {
        .order-layout {
            grid-template-columns: 1fr;
        }

        .order-sidebar {
            position: static;
        }
    }

    @media (max-width: 650px) {
        .order-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .order-information {
            grid-template-columns: 1fr;
        }

        .item-details {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .payment-history-item {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .bank-details {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .order-card,
        .payment-summary {
            padding: 16px;
        }

        .item-top {
            flex-direction: column;
        }

        .item-price {
            text-align: left;
        }

        .item-details,
        .payment-history-item,
        .invoice-information {
            grid-template-columns: 1fr;
        }

        .proof-header,
        .bank-information-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .invoice-print-button {
            width: 100%;
        }
    }
</style>

@endpush

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const proofInput =
            document.getElementById('proof_image');

        const fileName =
            document.getElementById('proof-file-name');

        const uploadForm =
            document.getElementById('proof-upload-form');

        const uploadButton =
            document.getElementById('upload-proof-button');

        if (proofInput && fileName) {

            proofInput.addEventListener('change', function () {

                if (
                    !this.files
                    || this.files.length === 0
                ) {
                    fileName.textContent =
                        'JPG, JPEG, PNG, WEBP • Maks. 5 MB';

                    return;
                }

                const file = this.files[0];

                fileName.textContent =
                    'Dipilih: ' + file.name;
            });

        }

        if (uploadForm && uploadButton) {

            uploadForm.addEventListener('submit', function (event) {

                if (!uploadForm.checkValidity()) {
                    return;
                }

                if (uploadButton.disabled) {
                    event.preventDefault();
                    return;
                }

                uploadButton.disabled = true;
                uploadButton.textContent = 'Mengirim Bukti...';

            });

        }

    });
</script>

@endpush
