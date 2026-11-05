@extends('layouts.admin')

@section('title', 'Tambah Paket')
@section('topbar-title', 'Tambah Paket')

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
    | Information
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

    .info-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border-radius: 12px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .info-icon svg {
        width: 21px;
        height: 21px;
    }

    .info-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .info-description {
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.65;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
        margin-top: 17px;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .info-number {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ecfeff;
        color: #0891b2;
        font-size: 10px;
        font-weight: 850;
    }

    .info-text {
        padding-top: 2px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.55;
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
                Tambah Paket
            </h1>

            <p class="heading-description">
                Buat paket pertemuan baru dan hubungkan paket tersebut
                dengan salah satu program pembelajaran Ragen Swim Course.
            </p>
        </div>

        <a
            href="{{ route('admin.packages.index') }}"
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
                    Tentukan program, jumlah sesi, harga, masa aktif,
                    dan status paket.
                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.packages.store') }}"
            >
                @csrf

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
                                                $selectedProgramId
                                            ) === (int) $program->id
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $program->name }}
                                    </option>

                                @endforeach
                            </select>

                            <div class="form-help">
                                Hanya program aktif yang dapat dipilih
                                saat membuat paket baru.
                            </div>

                            @error('program_id')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- NAMA PAKET --}}
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
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Private 4 Pertemuan"
                                maxlength="100"
                                required
                                autofocus
                            >

                            <div class="form-help">
                                Gunakan nama yang menjelaskan jenis
                                atau jumlah pertemuan paket.
                            </div>

                            @error('name')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- DESKRIPSI --}}
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
                                placeholder="Jelaskan manfaat atau ketentuan paket..."
                            >{{ old('description') }}</textarea>

                            <div class="form-help">
                                Opsional. Maksimal 5.000 karakter.
                            </div>

                            @error('description')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- TOTAL SESSION --}}
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
                                value="{{ old('total_sessions') }}"
                                class="form-control @error('total_sessions') is-invalid @enderror"
                                min="1"
                                max="999"
                                step="1"
                                placeholder="Contoh: 4"
                                required
                            >

                            <div class="form-help">
                                Total sesi yang diterima setelah
                                customer membeli paket.
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
                                value="{{ old('validity_days') }}"
                                class="form-control @error('validity_days') is-invalid @enderror"
                                min="1"
                                max="3650"
                                step="1"
                                placeholder="Contoh: 30"
                            >

                            <div class="form-help">
                                Dalam hari. Kosongkan jika tidak ingin
                                menentukan masa berlaku.
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
                                    value="{{ old('price') }}"
                                    class="form-control input-with-prefix @error('price') is-invalid @enderror"
                                    min="0"
                                    step="1"
                                    placeholder="500000"
                                    required
                                >

                            </div>

                            <div class="form-help">
                                Masukkan angka tanpa titik atau koma.
                                Contoh: 500000.
                            </div>

                            @error('price')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- PROMO PAKET --}}
                        <div class="form-group"><label for="promo_enabled" class="form-label">Status Promo</label><select id="promo_enabled" name="promo_enabled" class="form-control @error('promo_enabled') is-invalid @enderror"><option value="0" {{ old('promo_enabled', '0') == '0' ? 'selected' : '' }}>Nonaktif</option><option value="1" {{ old('promo_enabled') == '1' ? 'selected' : '' }}>Aktif</option></select>@error('promo_enabled') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_name" class="form-label">Nama Promo</label><input type="text" id="promo_name" name="promo_name" maxlength="150" value="{{ old('promo_name') }}" class="form-control @error('promo_name') is-invalid @enderror" placeholder="Contoh: Promo November">@error('promo_name') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_price" class="form-label">Harga Promo</label><div class="input-prefix-wrapper"><span class="input-prefix">Rp</span><input type="number" id="promo_price" name="promo_price" min="0" step="1" value="{{ old('promo_price') }}" class="form-control input-with-prefix @error('promo_price') is-invalid @enderror" placeholder="350000"></div><div class="form-help">Harga promo harus lebih rendah dari harga normal.</div>@error('promo_price') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_start_date" class="form-label">Tanggal Mulai Promo</label><input type="date" id="promo_start_date" name="promo_start_date" value="{{ old('promo_start_date') }}" class="form-control @error('promo_start_date') is-invalid @enderror">@error('promo_start_date') <div class="field-error">{{ $message }}</div> @enderror</div>
                        <div class="form-group"><label for="promo_end_date" class="form-label">Tanggal Berakhir Promo</label><input type="date" id="promo_end_date" name="promo_end_date" value="{{ old('promo_end_date') }}" class="form-control @error('promo_end_date') is-invalid @enderror">@error('promo_end_date') <div class="field-error">{{ $message }}</div> @enderror</div>

                        {{-- STATUS --}}
                        <div class="form-group">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status Paket
                                <span class="required">*</span>
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-control @error('status') is-invalid @enderror"
                                required
                            >

                                <option
                                    value="active"
                                    {{ old('status', 'active') === 'active' ? 'selected' : '' }}
                                >
                                    Aktif
                                </option>

                                <option
                                    value="inactive"
                                    {{ old('status') === 'inactive' ? 'selected' : '' }}
                                >
                                    Nonaktif
                                </option>

                            </select>

                            <div class="form-help">
                                Paket aktif dapat ditawarkan kepada customer.
                            </div>

                            @error('status')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- ACTION --}}
                <div class="form-actions">

                    <a
                        href="{{ route('admin.packages.index') }}"
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

                        Simpan Paket
                    </button>

                </div>

            </form>

        </div>


        {{-- INFORMATION --}}
        <div class="info-column">

            <div class="card info-card">

                <div class="info-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <rect
                            x="4"
                            y="5"
                            width="16"
                            height="14"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M8 10H16M8 14H13"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

                <div class="info-title">
                    Tentang Paket
                </div>

                <div class="info-description">
                    Paket menentukan hak sesi yang diterima customer
                    setelah transaksi berhasil dibayar.
                </div>


                <div class="info-list">

                    <div class="info-item">

                        <div class="info-number">
                            1
                        </div>

                        <div class="info-text">
                            Paket harus terhubung dengan satu program
                            pembelajaran.
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-number">
                            2
                        </div>

                        <div class="info-text">
                            Jumlah sesi akan menjadi saldo awal
                            participant package setelah pembayaran berhasil.
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-number">
                            3
                        </div>

                        <div class="info-text">
                            Masa aktif digunakan untuk menentukan
                            periode penggunaan paket peserta.
                        </div>

                    </div>

                </div>

            </div>


            <div class="note-card">

                <div class="note-title">
                    Harga paket
                </div>

                <div class="note-text">
                    Masukkan harga dalam angka penuh tanpa pemisah.
                    Contoh harga Rp800.000 ditulis sebagai
                    <strong>800000</strong>.
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
