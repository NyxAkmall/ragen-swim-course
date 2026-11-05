@extends('layouts.admin')

@section('title', 'Edit FAQ')
@section('topbar-title', 'Edit FAQ')

@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Edit FAQ
        </h1>

        <p class="page-description">
            Perbarui pertanyaan dan jawaban yang tersedia
            di Ragen Swim Course.
        </p>
    </div>

    <a
        href="{{ route('admin.faqs.index') }}"
        class="btn-secondary"
    >
        Kembali ke Daftar FAQ
    </a>

</div>


<div class="faq-edit-layout">

    {{-- =====================================================
        FORM EDIT FAQ
    ====================================================== --}}

    <section class="card faq-form-card">

        <div class="faq-card-header">

            <div>
                <h2>
                    Informasi FAQ
                </h2>

                <p>
                    Perbarui informasi pertanyaan, jawaban,
                    kategori, dan pengaturan publikasi.
                </p>
            </div>

            <span class="faq-id-badge">
                FAQ #{{ $faq->id }}
            </span>

        </div>


        {{-- INFORMASI DATA --}}
        <div class="faq-metadata">

            <div class="faq-metadata-item">

                <span class="faq-metadata-label">
                    Dibuat
                </span>

                <strong>
                    {{ $faq->created_at?->format('d/m/Y H:i') ?? '-' }}
                </strong>

            </div>


            <div class="faq-metadata-item">

                <span class="faq-metadata-label">
                    Terakhir Diperbarui
                </span>

                <strong>
                    {{ $faq->updated_at?->format('d/m/Y H:i') ?? '-' }}
                </strong>

            </div>


            <div class="faq-metadata-item">

                <span class="faq-metadata-label">
                    Status Saat Ini
                </span>

                @if ($faq->status === 'published')

                    <span class="faq-status-badge faq-status-published">
                        Dipublikasikan
                    </span>

                @else

                    <span class="faq-status-badge faq-status-draft">
                        Draf
                    </span>

                @endif

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('admin.faqs.update', $faq) }}"
            class="faq-form"
        >
            @csrf
            @method('PUT')


            {{-- INFORMASI PUBLIKASI --}}
            <div class="faq-info-box">

                <span class="faq-info-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M12 11V16M12 8H12.01"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <p>
                    Perubahan pada FAQ berstatus
                    <strong>Dipublikasikan</strong> akan ditampilkan
                    pada halaman utama website setelah disimpan.
                    Jika memilih <strong>Draf</strong>, FAQ tidak
                    akan ditampilkan kepada pengunjung.
                </p>

            </div>


            {{-- PERTANYAAN --}}
            <div class="faq-field">

                <label for="question">
                    Pertanyaan FAQ
                    <span class="faq-required">*</span>
                </label>

                <input
                    type="text"
                    id="question"
                    name="question"
                    value="{{ old('question', $faq->question) }}"
                    maxlength="255"
                    placeholder="Masukkan pertanyaan FAQ"
                    class="{{ $errors->has('question') ? 'is-invalid' : '' }}"
                    required
                >

                <p class="faq-field-help">
                    Gunakan pertanyaan yang singkat, jelas,
                    dan mudah dipahami. Maksimal 255 karakter.
                </p>

                @error('question')
                    <span class="faq-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- JAWABAN --}}
            <div class="faq-field">

                <label for="answer">
                    Jawaban FAQ
                    <span class="faq-required">*</span>
                </label>

                <textarea
                    id="answer"
                    name="answer"
                    rows="8"
                    maxlength="10000"
                    placeholder="Masukkan jawaban FAQ"
                    class="{{ $errors->has('answer') ? 'is-invalid' : '' }}"
                    required
                >{{ old('answer', $faq->answer) }}</textarea>

                <p class="faq-field-help">
                    Gunakan bahasa yang jelas, informatif,
                    dan mudah dipahami. Maksimal 10.000 karakter.
                </p>

                @error('answer')
                    <span class="faq-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- KATEGORI DAN STATUS --}}
            <div class="faq-field-grid">

                <div class="faq-field">

                    <label for="category">
                        Kategori FAQ
                        <span class="faq-required">*</span>
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="{{ $errors->has('category') ? 'is-invalid' : '' }}"
                        required
                    >
                        <option value="">
                            Pilih Kategori
                        </option>

                        @foreach ($categories as $item)

                            <option
                                value="{{ $item }}"
                                @selected(old('category', $faq->category) === $item)
                            >
                                {{ match($item) {
                                    'general' => 'Umum',
                                    'registration' => 'Pendaftaran',
                                    'program' => 'Program Kelas',
                                    'schedule' => 'Jadwal',
                                    'booking' => 'Booking',
                                    'payment' => 'Pembayaran',
                                    'reschedule' => 'Reschedule',
                                    'package' => 'Paket Pertemuan',
                                    'trial' => 'Trial Class',
                                    default => ucfirst($item)
                                } }}
                            </option>

                        @endforeach

                    </select>

                    <p class="faq-field-help">
                        Pilih kategori sesuai topik pertanyaan.
                    </p>

                    @error('category')
                        <span class="faq-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <div class="faq-field">

                    <label for="status">
                        Status Publikasi
                        <span class="faq-required">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="{{ $errors->has('status') ? 'is-invalid' : '' }}"
                        required
                    >
                        <option
                            value="draft"
                            @selected(old('status', $faq->status) === 'draft')
                        >
                            Draf — Belum tampil di website
                        </option>

                        <option
                            value="published"
                            @selected(old('status', $faq->status) === 'published')
                        >
                            Dipublikasikan — Tampil di website
                        </option>
                    </select>

                    <p class="faq-field-help">
                        Tentukan apakah FAQ dapat dilihat pengunjung.
                    </p>

                    @error('status')
                        <span class="faq-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>


            {{-- URUTAN TAMPIL --}}
            <div class="faq-field">

                <label for="sort_order">
                    Urutan Tampil
                    <span class="faq-required">*</span>
                </label>

                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    value="{{ old('sort_order', $faq->sort_order) }}"
                    min="0"
                    max="999999"
                    step="1"
                    class="{{ $errors->has('sort_order') ? 'is-invalid' : '' }}"
                    required
                >

                <p class="faq-field-help">
                    Angka lebih kecil akan ditampilkan terlebih dahulu
                    pada halaman utama website.
                </p>

                @error('sort_order')
                    <span class="faq-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- TOMBOL AKSI --}}
            <div class="faq-form-actions">

                <a
                    href="{{ route('admin.faqs.index') }}"
                    class="btn-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </section>


    {{-- =====================================================
        INFORMASI SAMPING
    ====================================================== --}}

    <aside class="faq-information">

        <div class="card faq-info-card">

            <div class="faq-sidebar-icon">

                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M8 4H16L20 8V20H4V4H8Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M8 12H16M8 16H14"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <h3>
                Panduan Mengedit FAQ
            </h3>

            <p>
                Pastikan setiap perubahan informasi
                sesuai dengan layanan Ragen Swim Course.
            </p>

            <ul>
                <li>
                    Periksa kembali isi pertanyaan dan jawaban.
                </li>

                <li>
                    Pastikan kategori sesuai dengan pembahasan.
                </li>

                <li>
                    Gunakan status Draf jika informasi belum final.
                </li>

                <li>
                    Atur urutan agar FAQ penting muncul lebih awal.
                </li>

                <li>
                    Simpan perubahan setelah semua informasi benar.
                </li>
            </ul>

        </div>


        <div class="card faq-status-card">

            <h3>
                Informasi Status
            </h3>

            <div class="faq-status-info">

                <span class="faq-status-dot faq-dot-green"></span>

                <div>
                    <strong>
                        Dipublikasikan
                    </strong>

                    <p>
                        FAQ dapat dibaca pengunjung
                        pada halaman utama website.
                    </p>
                </div>

            </div>


            <div class="faq-status-info">

                <span class="faq-status-dot faq-dot-gray"></span>

                <div>
                    <strong>
                        Draf
                    </strong>

                    <p>
                        FAQ hanya tersimpan di dashboard admin
                        dan tidak ditampilkan kepada pengunjung.
                    </p>
                </div>

            </div>

        </div>

    </aside>

</div>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    .faq-edit-layout {
        display: grid;

        grid-template-columns:
            minmax(0, 1fr)
            310px;

        align-items: start;
        gap: 22px;
    }

    .faq-form-card {
        overflow: hidden;
    }

    .faq-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 16px;

        padding: 24px 27px;

        border-bottom: 1px solid #e2e8f0;
    }

    .faq-card-header h2 {
        color: #082f49;

        font-size: 17px;
        font-weight: 800;
    }

    .faq-card-header p {
        margin-top: 6px;

        color: #94a3b8;

        font-size: 12px;
        line-height: 1.7;
    }

    .faq-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        padding: 8px 12px;

        border-radius: 9px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 11px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    .faq-metadata {
        display: flex;
        flex-wrap: wrap;

        gap: 18px 30px;

        padding: 20px 27px;

        border-bottom: 1px solid #e2e8f0;

        background: #fbfdff;
    }

    .faq-metadata-item {
        display: flex;
        flex-direction: column;

        align-items: flex-start;

        gap: 7px;
    }

    .faq-metadata-label {
        color: #94a3b8;

        font-size: 10px;
        font-weight: 750;

        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .faq-metadata-item strong {
        color: #334155;

        font-size: 12px;
        font-weight: 750;
    }

    .faq-status-badge {
        display: inline-flex;
        align-items: center;

        padding: 6px 10px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 800;
    }

    .faq-status-published {
        background: #f0fdf4;
        color: #15803d;
    }

    .faq-status-draft {
        background: #f1f5f9;
        color: #64748b;
    }


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    .faq-form {
        padding: 27px;
    }

    .faq-info-box {
        display: flex;
        align-items: flex-start;

        gap: 12px;

        padding: 16px;

        margin-bottom: 27px;

        border: 1px solid #bae6fd;
        border-radius: 12px;

        background: #f0f9ff;
    }

    .faq-info-icon {
        width: 30px;
        height: 30px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 9px;

        background: #e0f2fe;
        color: #0284c7;
    }

    .faq-info-icon svg {
        width: 18px;
        height: 18px;
    }

    .faq-info-box p {
        color: #075985;

        font-size: 12px;
        line-height: 1.75;
    }

    .faq-field {
        margin-bottom: 24px;
    }

    .faq-field label {
        display: block;

        margin-bottom: 9px;

        color: #334155;

        font-size: 12px;
        font-weight: 800;
    }

    .faq-required {
        color: #dc2626;
    }

    .faq-field input,
    .faq-field select,
    .faq-field textarea {
        display: block;

        width: 100%;

        padding: 12px 14px;

        border: 1px solid #cbd5e1;
        border-radius: 10px;

        background: #ffffff;
        color: #0f172a;

        font-size: 13px;
        line-height: 1.6;

        outline: none;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .faq-field input,
    .faq-field select {
        min-height: 45px;
    }

    .faq-field textarea {
        min-height: 190px;
        resize: vertical;
    }

    .faq-field input:focus,
    .faq-field select:focus,
    .faq-field textarea:focus {
        border-color: #0284c7;

        box-shadow:
            0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .faq-field .is-invalid {
        border-color: #dc2626;
    }

    .faq-field-help {
        margin-top: 7px;

        color: #94a3b8;

        font-size: 11px;
        line-height: 1.65;
    }

    .faq-error {
        display: block;

        margin-top: 7px;

        color: #dc2626;

        font-size: 11px;
        font-weight: 700;
    }

    .faq-field-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 18px;
    }

    .faq-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 10px;

        padding-top: 22px;

        border-top: 1px solid #e2e8f0;
    }


    /*
    |--------------------------------------------------------------------------
    | Information Sidebar
    |--------------------------------------------------------------------------
    */

    .faq-information {
        display: flex;
        flex-direction: column;

        gap: 18px;
    }

    .faq-info-card,
    .faq-status-card {
        padding: 23px;
    }

    .faq-sidebar-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 16px;

        border-radius: 12px;

        background: #ecfeff;
        color: #0891b2;
    }

    .faq-sidebar-icon svg {
        width: 23px;
        height: 23px;
    }

    .faq-info-card h3,
    .faq-status-card h3 {
        color: #082f49;

        font-size: 14px;
        font-weight: 800;
    }

    .faq-info-card > p {
        margin-top: 10px;

        color: #64748b;

        font-size: 12px;
        line-height: 1.75;
    }

    .faq-info-card ul {
        margin-top: 17px;
        padding-left: 17px;
    }

    .faq-info-card li {
        margin-bottom: 10px;

        color: #64748b;

        font-size: 11px;
        line-height: 1.7;
    }

    .faq-status-card h3 {
        margin-bottom: 20px;
    }

    .faq-status-info {
        display: flex;
        align-items: flex-start;

        gap: 11px;

        margin-bottom: 19px;
    }

    .faq-status-info:last-child {
        margin-bottom: 0;
    }

    .faq-status-dot {
        width: 10px;
        height: 10px;

        flex-shrink: 0;

        margin-top: 4px;

        border-radius: 50%;
    }

    .faq-dot-green {
        background: #22c55e;
    }

    .faq-dot-gray {
        background: #94a3b8;
    }

    .faq-status-info strong {
        color: #334155;

        font-size: 12px;
        font-weight: 800;
    }

    .faq-status-info p {
        margin-top: 5px;

        color: #94a3b8;

        font-size: 11px;
        line-height: 1.7;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {

        .faq-edit-layout {
            grid-template-columns: 1fr;
        }

        .faq-information {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {

        .faq-card-header {
            flex-direction: column;
            align-items: flex-start;

            padding: 20px;
        }

        .faq-metadata {
            padding: 18px 20px;
        }

        .faq-form {
            padding: 20px;
        }

        .faq-field-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .faq-information {
            grid-template-columns: 1fr;
        }

        .faq-form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .faq-form-actions .btn-primary,
        .faq-form-actions .btn-secondary {
            width: 100%;
        }

        .page-header .btn-secondary {
            margin-top: 16px;
        }
    }

</style>

@endpush
