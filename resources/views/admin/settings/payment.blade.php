@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran')
@section('topbar-title', 'Pengaturan Pembayaran')

@push('styles')
<style>
    .settings-page {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 24px;
        align-items: start;
    }

    .settings-main {
        display: flex;
        flex-direction: column;
        gap: 22px;
        min-width: 0;
    }

    .settings-card {
        overflow: hidden;
    }

    .settings-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid var(--border);
    }

    .settings-card-title {
        color: var(--navy);
        font-size: 17px;
        font-weight: 800;
    }

    .settings-card-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    .settings-card-body {
        padding: 24px;
    }

    .form-group + .form-group {
        margin-top: 21px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: var(--text);
        font-size: 13px;
        font-weight: 750;
    }

    .required-mark {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 14px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .form-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, .1);
    }

    .form-control.is-invalid {
        border-color: #fca5a5;
    }

    .form-help {
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .field-error {
        margin-top: 7px;
        color: var(--danger);
        font-size: 12px;
        font-weight: 650;
    }

    .settings-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 27px;
        padding-top: 22px;
        border-top: 1px solid var(--border);
    }

    .settings-actions .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 44px;
    }

    .save-icon {
        width: 17px;
        height: 17px;
    }

    .status-card {
        padding: 22px;
    }

    .status-heading {
        margin-bottom: 17px;
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .status-box {
        padding: 15px;
        border-radius: 12px;
    }

    .status-box.configured {
        border: 1px solid #bbf7d0;
        background: var(--success-bg);
    }

    .status-box.unconfigured {
        border: 1px solid #fde68a;
        background: var(--warning-bg);
    }

    .status-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 800;
    }

    .status-box.configured .status-title {
        color: var(--success);
    }

    .status-box.unconfigured .status-title {
        color: var(--warning);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        flex-shrink: 0;
        border-radius: 50%;
        background: currentColor;
    }

    .status-description {
        margin-top: 8px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .preview-section {
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .preview-title {
        margin-bottom: 13px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .7px;
    }

    .preview-item + .preview-item {
        margin-top: 13px;
    }

    .preview-label {
        color: var(--text-soft);
        font-size: 11px;
    }

    .preview-value {
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: var(--text);
        font-size: 13px;
        font-weight: 750;
    }

    .preview-empty {
        color: var(--text-light);
        font-weight: 600;
    }

    .info-card {
        margin-top: 20px;
        padding: 18px;
        border: 1px solid #bae6fd;
        border-radius: 14px;
        background: #f0f9ff;
    }

    .info-title {
        color: var(--blue-dark);
        font-size: 13px;
        font-weight: 800;
    }

    .info-list {
        margin-top: 10px;
        padding-left: 18px;
        color: #475569;
        font-size: 12px;
        line-height: 1.75;
    }

    .registration-fee-highlight {
        margin-bottom: 22px;
        padding: 18px;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        background: #f0f9ff;
    }

    .registration-fee-highlight span {
        display: block;
        color: #0369a1;
        font-size: 12px;
        font-weight: 700;
    }

    .registration-fee-highlight strong {
        display: block;
        margin-top: 7px;
        color: #0c4a6e;
        font-size: 26px;
        font-weight: 900;
    }

    .registration-fee-note {
        margin-top: 17px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        line-height: 1.7;
    }

    @media (max-width: 950px) {
        .settings-page {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .settings-card-header,
        .settings-card-body {
            padding: 19px;
        }

        .settings-actions {
            display: block;
        }

        .settings-actions .btn-primary {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            Pengaturan Pembayaran
        </h1>

        <p class="page-description">
            Kelola rekening transfer bank dan biaya pendaftaran
            peserta Ragen Swim Course.
        </p>
    </div>
</div>

<div class="settings-page">

    <div class="settings-main">

        {{-- REKENING BANK --}}

        <div class="card settings-card">

            <div class="settings-card-header">
                <div class="settings-card-title">
                    Rekening Transfer Bank
                </div>

                <div class="settings-card-description">
                    Informasi rekening resmi yang digunakan customer
                    untuk melakukan pembayaran melalui transfer bank manual.
                </div>
            </div>

            <div class="settings-card-body">

                <form
                    method="POST"
                    action="{{ route('admin.settings.payment.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="bank_name" class="form-label">
                            Nama Bank
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="bank_name"
                            name="bank_name"
                            value="{{ old('bank_name', $bankAccount['bank_name']) }}"
                            class="form-control {{ $errors->has('bank_name') ? 'is-invalid' : '' }}"
                            placeholder="Contoh: BCA"
                            maxlength="100"
                            autocomplete="off"
                            required
                        >

                        @error('bank_name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="account_number" class="form-label">
                            Nomor Rekening
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="account_number"
                            name="account_number"
                            value="{{ old('account_number', $bankAccount['account_number']) }}"
                            class="form-control {{ $errors->has('account_number') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan nomor rekening"
                            maxlength="50"
                            inputmode="numeric"
                            autocomplete="off"
                            required
                        >

                        @error('account_number')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="account_holder" class="form-label">
                            Nama Pemilik Rekening
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="account_holder"
                            name="account_holder"
                            value="{{ old('account_holder', $bankAccount['account_holder']) }}"
                            class="form-control {{ $errors->has('account_holder') ? 'is-invalid' : '' }}"
                            placeholder="Nama sesuai rekening bank"
                            maxlength="150"
                            autocomplete="off"
                            required
                        >

                        @error('account_holder')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="settings-actions">
                        <button type="submit" class="btn-primary">
                            <svg
                                class="save-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M5 4H17L20 7V20H5V4Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M8 4V9H16V4"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M8 20V14H17V20"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            Simpan Rekening Bank
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- BIAYA PENDAFTARAN --}}

        <div class="card settings-card">

            <div class="settings-card-header">
                <div class="settings-card-title">
                    Biaya Pendaftaran Peserta
                </div>

                <div class="settings-card-description">
                    Atur biaya pendaftaran yang dikenakan satu kali
                    untuk setiap peserta renang baru.
                </div>
            </div>

            <div class="settings-card-body">

                <div class="registration-fee-highlight">
                    <span>Biaya Pendaftaran Saat Ini</span>

                    <strong>
                        Rp{{ number_format($registrationFee, 0, ',', '.') }}
                    </strong>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.settings.registration-fee.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="registration_fee" class="form-label">
                            Nominal Biaya Pendaftaran (Rp)
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="number"
                            id="registration_fee"
                            name="registration_fee"
                            value="{{ old('registration_fee', $registrationFee) }}"
                            class="form-control {{ $errors->has('registration_fee') ? 'is-invalid' : '' }}"
                            min="0"
                            max="9999999999"
                            step="1"
                            placeholder="Contoh: 50000"
                            required
                        >

                        <div class="form-help">
                            Masukkan nominal dalam Rupiah tanpa titik
                            atau koma. Contoh: 50000 untuk Rp50.000.
                        </div>

                        @error('registration_fee')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="registration-fee-note">
                        Biaya pendaftaran berlaku satu kali per peserta,
                        bukan per akun customer. Peserta yang sudah melunasi
                        biaya pendaftaran tidak dikenakan biaya yang sama
                        ketika membeli paket berikutnya.
                    </div>

                    <div class="settings-actions">
                        <button type="submit" class="btn-primary">
                            Simpan Biaya Pendaftaran
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    {{-- SIDEBAR --}}

    <div>

        <div class="card status-card">

            <div class="status-heading">
                Status Rekening
            </div>

            @if ($isConfigured)

                <div class="status-box configured">
                    <div class="status-title">
                        <span class="status-dot"></span>
                        Rekening Aktif
                    </div>

                    <div class="status-description">
                        Data rekening sudah lengkap dan dapat digunakan
                        untuk pembayaran transfer bank.
                    </div>
                </div>

            @else

                <div class="status-box unconfigured">
                    <div class="status-title">
                        <span class="status-dot"></span>
                        Belum Dikonfigurasi
                    </div>

                    <div class="status-description">
                        Lengkapi informasi rekening bank
                        untuk menerima pembayaran customer.
                    </div>
                </div>

            @endif

            <div class="preview-section">
                <div class="preview-title">
                    Rekening Saat Ini
                </div>

                <div class="preview-item">
                    <div class="preview-label">Bank</div>

                    <div class="preview-value">
                        @if ($bankAccount['bank_name'] !== '')
                            {{ $bankAccount['bank_name'] }}
                        @else
                            <span class="preview-empty">Belum diisi</span>
                        @endif
                    </div>
                </div>

                <div class="preview-item">
                    <div class="preview-label">Nomor Rekening</div>

                    <div class="preview-value">
                        @if ($bankAccount['account_number'] !== '')
                            {{ $bankAccount['account_number'] }}
                        @else
                            <span class="preview-empty">Belum diisi</span>
                        @endif
                    </div>
                </div>

                <div class="preview-item">
                    <div class="preview-label">Atas Nama</div>

                    <div class="preview-value">
                        @if ($bankAccount['account_holder'] !== '')
                            {{ $bankAccount['account_holder'] }}
                        @else
                            <span class="preview-empty">Belum diisi</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="info-card">
            <div class="info-title">
                Informasi Pengaturan
            </div>

            <ul class="info-list">
                <li>
                    Pastikan informasi rekening bank sudah benar.
                </li>
                <li>
                    Biaya pendaftaran berlaku satu kali per peserta.
                </li>
                <li>
                    Nominal Rp0 berarti tidak ada biaya pendaftaran.
                </li>
                <li>
                    Integrasi biaya pendaftaran ke checkout
                    akan dilakukan pada tahap berikutnya.
                </li>
            </ul>
        </div>

    </div>

</div>

@endsection
