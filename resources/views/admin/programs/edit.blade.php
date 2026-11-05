@extends('layouts.admin')

@section('title', 'Edit Program')
@section('topbar-title', 'Edit Program')

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
        min-height: 120px;
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
    | Side Information
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

    .program-summary {
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
        letter-spacing: 0.5px;
    }

    .summary-value {
        margin-top: 4px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
        line-height: 1.5;
    }

    .slug-value {
        display: inline-flex;
        max-width: 100%;
        padding: 6px 8px;
        border-radius: 7px;
        background: #f8fafc;
        color: #475569;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 10px;
        word-break: break-all;
    }

    .info-note {
        padding: 16px;
        border: 1px solid #bae6fd;
        border-radius: 13px;
        background: #f0f9ff;
    }

    .info-note-title {
        color: var(--blue-dark);
        font-size: 12px;
        font-weight: 800;
    }

    .info-note-text {
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
                Edit Program
            </h1>

            <p class="heading-description">
                Perbarui informasi program
                <strong>{{ $program->name }}</strong>.
                Perubahan akan langsung digunakan pada data program
                Ragen Swim Course.
            </p>
        </div>

        <a
            href="{{ route('admin.programs.show', $program) }}"
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
                    Informasi Program
                </div>

                <div class="card-description">
                    Ubah data program sesuai kebutuhan.
                    Status program dikelola secara terpisah pada halaman detail.
                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.programs.update', $program) }}"
            >
                @csrf
                @method('PUT')


                <div class="card-body">

                    <div class="form-grid">

                        {{-- NAMA --}}
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
                                value="{{ old('name', $program->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                maxlength="100"
                                required
                                autofocus
                            >

                            <div class="form-help">
                                Jika nama program diubah, slug akan
                                diperbarui otomatis oleh sistem.
                            </div>

                            @error('name')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- SHORT DESCRIPTION --}}
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
                                value="{{ old('short_description', $program->short_description) }}"
                                class="form-control @error('short_description') is-invalid @enderror"
                                maxlength="255"
                                placeholder="Deskripsi singkat program..."
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


                        {{-- DESCRIPTION --}}
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
                                maxlength="5000"
                                placeholder="Deskripsi lengkap program..."
                            >{{ old('description', $program->description) }}</textarea>

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
                                value="{{ old('duration_minutes', $program->duration_minutes) }}"
                                class="form-control @error('duration_minutes') is-invalid @enderror"
                                min="15"
                                max="480"
                                step="1"
                                required
                            >

                            <div class="form-help">
                                Durasi satu sesi dalam menit.
                            </div>

                            @error('duration_minutes')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- CURRENT STATUS --}}
                        <div class="form-group">

                            <label class="form-label">
                                Status Saat Ini
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $program->status === 'active' ? 'Aktif' : 'Nonaktif' }}"
                                disabled
                            >

                            <div class="form-help">
                                Status diubah melalui halaman Detail Program.
                            </div>

                        </div>

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="{{ route('admin.programs.show', $program) }}"
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


        {{-- INFORMATION --}}
        <div class="info-column">

            <div class="card info-card">

                <div class="info-title">
                    Data Program
                </div>

                <div class="info-description">
                    Ringkasan program sebelum perubahan disimpan.
                </div>


                <div class="program-summary">

                    <div class="summary-item">
                        <div class="summary-label">
                            Program
                        </div>

                        <div class="summary-value">
                            {{ $program->name }}
                        </div>
                    </div>


                    <div class="summary-item">
                        <div class="summary-label">
                            Slug Saat Ini
                        </div>

                        <div class="summary-value">
                            <span class="slug-value">
                                {{ $program->slug }}
                            </span>
                        </div>
                    </div>


                    <div class="summary-item">
                        <div class="summary-label">
                            Durasi
                        </div>

                        <div class="summary-value">
                            {{ $program->duration_minutes }} menit
                        </div>
                    </div>


                    <div class="summary-item">
                        <div class="summary-label">
                            Status
                        </div>

                        <div class="summary-value">
                            {{ $program->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </div>
                    </div>

                </div>

            </div>


            <div class="info-note">

                <div class="info-note-title">
                    Perubahan nama & slug
                </div>

                <div class="info-note-text">
                    Jika nama program diganti, sistem akan membuat
                    slug baru secara otomatis. Paket yang sudah
                    terhubung tetap berada pada program yang sama.
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
