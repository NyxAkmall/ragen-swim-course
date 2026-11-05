@extends('layouts.admin')

@section('title', 'Tambah Program')
@section('topbar-title', 'Tambah Program')

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
        max-width: 700px;
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
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    textarea.form-control {
        min-height: 115px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .form-control.is-invalid {
        border-color: #fca5a5;
    }

    .form-control.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
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
    | Sidebar Information
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
        font-size: 12px;
        line-height: 1.65;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 16px;
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

    .info-item-text {
        padding-top: 2px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.55;
    }

    .status-note {
        padding: 16px;
        border: 1px solid #bae6fd;
        border-radius: 13px;
        background: #f0f9ff;
    }

    .status-note-title {
        color: var(--blue-dark);
        font-size: 12px;
        font-weight: 800;
    }

    .status-note-text {
        margin-top: 5px;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    @media (max-width: 950px) {
        .form-layout {
            grid-template-columns: 1fr;
        }

        .info-column {
            order: -1;
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

    <div class="page-heading">

        <div>
            <h1 class="heading-title">
                Tambah Program
            </h1>

            <p class="heading-description">
                Tambahkan jenis program pembelajaran renang baru.
                Setelah program dibuat, paket pertemuan dapat ditambahkan
                secara terpisah ke dalam program tersebut.
            </p>
        </div>

        <a
            href="{{ route('admin.programs.index') }}"
            class="btn-secondary"
        >
            <svg
                class="button-icon"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
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

        <div class="card form-card">

            <div class="card-header">

                <div class="card-title">
                    Informasi Program
                </div>

                <div class="card-description">
                    Isi informasi utama program yang akan tersedia
                    di Ragen Swim Course.
                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.programs.store') }}"
            >
                @csrf

                <div class="card-body">

                    <div class="form-grid">

                        {{-- NAMA PROGRAM --}}
                        <div class="form-group full">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Nama Program
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Kelas Private"
                                maxlength="100"
                                required
                                autofocus
                            >

                            <div class="form-help">
                                Gunakan nama program yang singkat dan mudah dipahami customer.
                            </div>

                            @error('name')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- DESKRIPSI SINGKAT --}}
                        <div class="form-group full">

                            <label
                                for="short_description"
                                class="form-label"
                            >
                                Deskripsi Singkat
                            </label>

                            <input
                                type="text"
                                id="short_description"
                                name="short_description"
                                value="{{ old('short_description') }}"
                                class="form-control @error('short_description') is-invalid @enderror"
                                placeholder="Contoh: Program latihan privat dengan fokus sesuai kemampuan peserta."
                                maxlength="255"
                            >

                            <div class="form-help">
                                Maksimal 255 karakter.
                            </div>

                            @error('short_description')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- DESKRIPSI LENGKAP --}}
                        <div class="form-group full">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Deskripsi Lengkap
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Jelaskan tujuan, metode pembelajaran, target peserta, atau informasi penting lainnya..."
                                maxlength="5000"
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


                        {{-- DURASI --}}
                        <div class="form-group">

                            <label
                                for="duration_minutes"
                                class="form-label"
                            >
                                Durasi Pertemuan
                                <span class="required">*</span>
                            </label>

                            <input
                                type="number"
                                id="duration_minutes"
                                name="duration_minutes"
                                value="{{ old('duration_minutes', 60) }}"
                                class="form-control @error('duration_minutes') is-invalid @enderror"
                                min="15"
                                max="480"
                                step="1"
                                required
                            >

                            <div class="form-help">
                                Durasi satu sesi dalam menit. Contoh: 60 menit.
                            </div>

                            @error('duration_minutes')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- STATUS --}}
                        <div class="form-group">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status Program
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
                                Program aktif dapat digunakan untuk pengelolaan layanan.
                            </div>

                            @error('status')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="{{ route('admin.programs.index') }}"
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
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M5 12L10 17L19 7"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        Simpan Program
                    </button>

                </div>

            </form>

        </div>


        {{-- =====================================================
            INFORMATION
        ====================================================== --}}

        <div class="info-column">

            <div class="card info-card">

                <div class="info-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M12 11V16"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />

                        <circle
                            cx="12"
                            cy="8"
                            r="1"
                            fill="currentColor"
                        />
                    </svg>

                </div>

                <div class="info-title">
                    Tentang Program
                </div>

                <div class="info-description">
                    Program merupakan kategori utama layanan renang.
                    Setiap program nantinya dapat memiliki satu atau
                    beberapa paket pembelian.
                </div>


                <div class="info-list">

                    <div class="info-item">
                        <div class="info-number">1</div>

                        <div class="info-item-text">
                            Buat program berdasarkan jenis layanan
                            pembelajaran.
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-number">2</div>

                        <div class="info-item-text">
                            Tentukan durasi standar untuk satu kali
                            pertemuan.
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-number">3</div>

                        <div class="info-item-text">
                            Setelah tersimpan, tambahkan paket seperti
                            4, 8, atau 12 pertemuan.
                        </div>
                    </div>

                </div>

            </div>


            <div class="status-note">

                <div class="status-note-title">
                    Slug dibuat otomatis
                </div>

                <div class="status-note-text">
                    Kamu tidak perlu mengisi slug. Sistem akan membuat
                    slug unik secara otomatis berdasarkan nama program.
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
