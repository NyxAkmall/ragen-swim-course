@extends('layouts.admin')

@section('title', 'Edit Paket')
@section('topbar-title', 'Edit Paket')

@push('styles')
<style>
    .form-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .page-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .heading-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .heading-description {
        max-width: 720px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 20px;
        align-items: start;
    }

    .form-card {
        overflow: hidden;
    }

    .card-header {
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .card-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.55;
    }

    .card-body {
        padding: 22px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 13px;
        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .1);
    }

    .form-control.is-invalid {
        border-color: #fca5a5;
    }

    .form-help {
        color: var(--text-light);
        font-size: 11px;
        line-height: 1.5;
    }

    .field-error {
        color: var(--danger);
        font-size: 11px;
        font-weight: 650;
    }

    .input-prefix-wrapper {
        position: relative;
    }

    .input-prefix {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 750;
        pointer-events: none;
    }

    .input-with-prefix {
        padding-left: 38px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 18px 22px;
        border-top: 1px solid var(--border);
        background: #fcfdff;
    }

    .button-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */

    .info-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .info-card {
        padding: 20px;
    }

    .info-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .info-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.65;
    }

    .package-summary {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 18px;
    }

    .summary-item {
        padding-bottom: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .summary-label {
        color: var(--text-light);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .summary-value {
        margin-top: 4px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
        line-height: 1.5;
    }

    .summary-price {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-active::before {
        background: #22c55e;
    }

    .status-inactive {
        background: #f8fafc;
        color: #64748b;
    }

    .status-inactive::before {
        background: #94a3b8;
    }

    .note-card {
        padding: 16px;
        border: 1px solid #bae6fd;
        border-radius: 13px;
        background: #f0f9ff;
    }

    .note-title {
        color: var(--blue-dark);
        font-size: 12px;
        font-weight: 800;
    }

    .note-text {
        margin-top: 5px;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    @media (max-width: 950px) {
        .form-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .page-heading {
            flex-direction: column;
        }

        .heading-title {
            font-size: 24px;
        }

        .page-heading .btn-secondary {
            width: 100%;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: auto;
        }

        .card-header,
        .card-body {
            padding: 18px;
        }

        .form-actions {
            padding: 16px 18px;
            flex-direction: column-reverse;
        }

        .form-actions .btn-primary,
        .form-actions .btn-secondary {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="form-page">

    {{-- HEADER --}}
    <div class="page-heading">

        <div>
            <h1 class="heading-title">
                Edit Paket
            </h1>

            <p class="heading-description">
                Perbarui informasi paket
                <strong>{{ $package->name }}</strong>.
                Perubahan hanya berlaku pada data paket utama.
            </p>
        </div>


        <a
            href="{{ route('admin.packages.show', $package) }}"
            class="btn-secondary"
        >
            <svg
                class="button-icon"
                viewBox="0 0 24 24"
                fill="none"
            >
                <path
                    d="M15 18L9 12L15 6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

            Kembali
        </a>

    </div>


    <div class="form-layout">

        {{-- FORM --}}
        <div class="card form-card">

            <div class="card-header">

                <div class="card-title">
                    Informasi Paket
                </div>

                <div class="card-description">
                    Ubah program, nama, jumlah sesi, harga,
                    atau masa aktif paket.
                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.packages.update', $package) }}"
            >
                @csrf
                @method('PUT')


                <div class="card-body">

                    <div class="form-grid">

                        {{-- PROGRAM --}}
                        <div class="form-group full">

                            <label
                                for="program_id"
                                class="form-label"
                            >
                                Program
                                <span class="required">*</span>
                            </label>

                            <select
                                id="program_id"
                                name="program_id"
                                class="form-control @error('program_id') is-invalid @enderror"
                                required
                            >
                                <option value="">
                                    Pilih Program
                                </option>

                                @foreach ($programs as $program)

                                    <option
                                        value="{{ $program->id }}"
                                        {{
                                            (int) old(
                                                'program_id',
                                                $package->program_id
                                            ) === (int) $program->id
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $program->name }}

                                        @if ($program->status === 'inactive')
                                            (Nonaktif)
                                        @endif
                                    </option>

                                @endforeach

                            </select>

                            <div class="form-help">
                                Program saat ini tetap tersedia di pilihan
                                meskipun program tersebut sedang nonaktif.
                            </div>

                            @error('program_id')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- NAME --}}
                        <div class="form-group full">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Nama Paket
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $package->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                maxlength="100"
                                required
                                autofocus
                            >

                            <div class="form-help">
                                Nama paket yang akan ditampilkan kepada customer.
                            </div>

                            @error('name')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- DESCRIPTION --}}
                        <div class="form-group full">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Deskripsi Paket
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                maxlength="5000"
                                placeholder="Deskripsi paket..."
                            >{{ old('description', $package->description) }}</textarea>

                            <div class="form-help">
                                Opsional. Maksimal 5.000 karakter.
                            </div>

                            @error('description')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- SESSIONS --}}
                        <div class="form-group">

                            <label
                                for="total_sessions"
                                class="form-label"
                            >
                                Jumlah Sesi
                                <span class="required">*</span>
                            </label>

                            <input
                                type="number"
                                id="total_sessions"
                                name="total_sessions"
                                value="{{ old('total_sessions', $package->total_sessions) }}"
                                class="form-control @error('total_sessions') is-invalid @enderror"
                                min="1"
                                max="999"
                                step="1"
                                required
                            >

                            <div class="form-help">
                                Jumlah sesi untuk pembelian paket baru.
                            </div>

                            @error('total_sessions')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- VALIDITY --}}
                        <div class="form-group">

                            <label
                                for="validity_days"
                                class="form-label"
                            >
                                Masa Aktif Paket
                            </label>

                            <input
                                type="number"
                                id="validity_days"
                                name="validity_days"
                                value="{{ old('validity_days', $package->validity_days) }}"
                                class="form-control @error('validity_days') is-invalid @enderror"
                                min="1"
                                max="3650"
                                step="1"
                                placeholder="Contoh: 30"
                            >

                            <div class="form-help">
                                Dalam hari. Boleh dikosongkan jika
                                tidak ditentukan.
                            </div>

                            @error('validity_days')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- PRICE --}}
                        <div class="form-group">

                            <label
                                for="price"
                                class="form-label"
                            >
                                Harga Paket
                                <span class="required">*</span>
                            </label>

                            <div class="input-prefix-wrapper">

                                <span class="input-prefix">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    value="{{ old('price', (float) $package->price) }}"
                                    class="form-control input-with-prefix @error('price') is-invalid @enderror"
                                    min="0"
                                    step="1"
                                    required
                                >

                            </div>

                            <div class="form-help">
                                Masukkan angka tanpa titik atau koma.
                            </div>

                            @error('price')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- PROMO PAKET --}}
                        <div class="form-group"><label for="promo_enabled" class="form-label">Status Promo</label><select id="promo_enabled" name="promo_enabled" class="form-control @error('promo_enabled') is-invalid @enderror"><option value="0" {{ old('promo_enabled', (int) $package->promo_enabled) == 0 ? 'selected' : '' }}>Nonaktif</option><option value="1" {{ old('promo_enabled', (int) $package->promo_enabled) == 1 ? 'selected' : '' }}>Aktif</option></select>@error('promo_enabled') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_name" class="form-label">Nama Promo</label><input type="text" id="promo_name" name="promo_name" maxlength="150" value="{{ old('promo_name', $package->promo_name) }}" class="form-control @error('promo_name') is-invalid @enderror">@error('promo_name') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_price" class="form-label">Harga Promo</label><input type="number" id="promo_price" name="promo_price" min="0" step="1" value="{{ old('promo_price', $package->promo_price) }}" class="form-control @error('promo_price') is-invalid @enderror">@error('promo_price') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_start_date" class="form-label">Tanggal Mulai Promo</label><input type="date" id="promo_start_date" name="promo_start_date" value="{{ old('promo_start_date', $package->promo_start_date?->format('Y-m-d')) }}" class="form-control @error('promo_start_date') is-invalid @enderror">@error('promo_start_date') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_end_date" class="form-label">Tanggal Berakhir Promo</label><input type="date" id="promo_end_date" name="promo_end_date" value="{{ old('promo_end_date', $package->promo_end_date?->format('Y-m-d')) }}" class="form-control @error('promo_end_date') is-invalid @enderror">@error('promo_end_date') <div class="field-error">{{ $message }}</div> @enderror</div>

                        {{-- STATUS --}}
                        <div class="form-group">

                            <label class="form-label">
                                Status Saat Ini
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $package->status === 'active' ? 'Aktif' : 'Nonaktif' }}"
                                disabled
                            >

                            <div class="form-help">
                                Status paket diubah melalui halaman Detail Paket.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ACTION --}}
                <div class="form-actions">

                    <a
                        href="{{ route('admin.packages.show', $package) }}"
                        class="btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        <svg
                            class="button-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M5 12L10 17L19 7"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>


        {{-- SIDEBAR --}}
        <div class="info-column">

            <div class="card info-card">

                <div class="info-title">
                    Data Paket
                </div>

                <div class="info-description">
                    Ringkasan data paket sebelum perubahan disimpan.
                </div>


                <div class="package-summary">

                    <div class="summary-item">

                        <div class="summary-label">
                            Paket
                        </div>

                        <div class="summary-value">
                            {{ $package->name }}
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-label">
                            Program
                        </div>

                        <div class="summary-value">
                            {{ $package->program?->name ?? '-' }}
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-label">
                            Harga
                        </div>

                        <div class="summary-value summary-price">
                            {{ $package->formatted_price }}
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-label">
                            Jumlah Sesi
                        </div>

                        <div class="summary-value">
                            {{ $package->total_sessions }} sesi
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-label">
                            Masa Aktif
                        </div>

                        <div class="summary-value">
                            @if ($package->validity_days)
                                {{ $package->validity_days }} hari
                            @else
                                Tidak ditentukan
                            @endif
                        </div>

                    </div>


                    <div class="summary-item">

                        <div class="summary-label">
                            Status
                        </div>

                        <div class="summary-value">

                            @if ($package->status === 'active')

                                <span class="status-badge status-active">
                                    Aktif
                                </span>

                            @else

                                <span class="status-badge status-inactive">
                                    Nonaktif
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            <div class="note-card">

                <div class="note-title">
                    Perubahan data paket
                </div>

                <div class="note-text">
                    Perubahan harga berlaku untuk pembelian berikutnya.
                    Data transaksi lama tetap menyimpan nama dan harga
                    paket pada order item.

                    @if ($package->orderItems()->exists())
                        <p style="margin-top: 10px;">
                            <strong>Perhatian:</strong>
                            Paket ini sudah tercatat dalam riwayat pesanan.
                            Program, jumlah sesi, dan masa aktif paket
                            tidak dapat diubah. Jika memerlukan ketentuan
                            berbeda, buat paket baru.
                        </p>
                    @else
                        <p style="margin-top: 10px;">
                            Program, jumlah sesi, dan masa aktif masih
                            dapat diubah selama paket belum tercatat
                            dalam riwayat pesanan.
                        </p>
                    @endif
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
