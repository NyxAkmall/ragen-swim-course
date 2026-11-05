@extends('layouts.admin')

@section('title', 'Tambah FAQ')
@section('topbar-title', 'Tambah FAQ')

@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Tambah FAQ
        </h1>

        <p class="page-description">
            Tambahkan pertanyaan dan jawaban baru untuk membantu
            calon peserta serta customer Ragen Swim Course.
        </p>
    </div>

    <a
        href="{{ route('admin.faqs.index') }}"
        class="btn-secondary"
    >
        Kembali ke Daftar FAQ
    </a>

</div>


<div class="faq-create-layout">

    {{-- =====================================================
        FORM TAMBAH FAQ
    ====================================================== --}}

    <section class="card faq-form-card">

        <div class="faq-card-header">

            <div>
                <h2>
                    Informasi FAQ
                </h2>

                <p>
                    Lengkapi informasi pertanyaan, jawaban,
                    kategori, dan pengaturan publikasi.
                </p>
            </div>

        </div>


        <form
            method="POST"
            action="{{ route('admin.faqs.store') }}"
            class="faq-form"
        >
            @csrf


            {{-- PERTANYAAN --}}
            <div class="faq-field">

                <label for="question">
                    Pertanyaan
                    <span class="faq-required">*</span>
                </label>

                <input
                    type="text"
                    id="question"
                    name="question"
                    value="{{ old('question') }}"
                    maxlength="255"
                    placeholder="Contoh: Bagaimana cara mendaftar kelas renang?"
                    class="{{ $errors->has('question') ? 'is-invalid' : '' }}"
                    required
                >

                <p class="faq-field-help">
                    Gunakan pertanyaan yang singkat, jelas,
                    dan mudah dipahami customer.
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
                    Jawaban
                    <span class="faq-required">*</span>
                </label>

                <textarea
                    id="answer"
                    name="answer"
                    rows="8"
                    maxlength="10000"
                    placeholder="Tuliskan jawaban lengkap untuk pertanyaan tersebut..."
                    class="{{ $errors->has('answer') ? 'is-invalid' : '' }}"
                    required
                >{{ old('answer') }}</textarea>

                <p class="faq-field-help">
                    Jelaskan informasi dengan bahasa yang ramah,
                    informatif, dan mudah dimengerti.
                </p>

                @error('answer')
                    <span class="faq-error">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- KATEGORI DAN URUTAN --}}
            <div class="faq-field-grid">

                <div class="faq-field">

                    <label for="category">
                        Kategori
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
                                @selected(old('category') === $item)
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
                        Kelompokkan FAQ berdasarkan topik pembahasan.
                    </p>

                    @error('category')
                        <span class="faq-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <div class="faq-field">

                    <label for="sort_order">
                        Urutan Tampil
                        <span class="faq-required">*</span>
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        max="999999"
                        step="1"
                        class="{{ $errors->has('sort_order') ? 'is-invalid' : '' }}"
                        required
                    >

                    <p class="faq-field-help">
                        Angka lebih kecil akan ditampilkan lebih dahulu.
                    </p>

                    @error('sort_order')
                        <span class="faq-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>


            {{-- STATUS --}}
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
                        @selected(old('status', 'draft') === 'draft')
                    >
                        Draf — Belum tampil di website
                    </option>

                    <option
                        value="published"
                        @selected(old('status', 'draft') === 'published')
                    >
                        Dipublikasikan — Tampil di website
                    </option>
                </select>

                <p class="faq-field-help">
                    Pilih Draf jika informasi belum siap ditampilkan.
                    Pilih Dipublikasikan jika FAQ sudah dapat dibaca pengunjung.
                </p>

                @error('status')
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
                    Simpan FAQ
                </button>

            </div>

        </form>

    </section>


    {{-- =====================================================
        INFORMASI TAMBAHAN
    ====================================================== --}}

    <aside class="faq-information">

        <div class="card faq-info-card">

            <div class="faq-info-icon">
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
            </div>

            <h3>
                Panduan Membuat FAQ
            </h3>

            <p>
                FAQ membantu calon peserta dan customer
                menemukan jawaban tanpa harus menghubungi admin.
            </p>

            <ul>
                <li>
                    Gunakan pertanyaan yang umum ditanyakan.
                </li>

                <li>
                    Berikan jawaban yang jelas dan sesuai layanan.
                </li>

                <li>
                    Pilih kategori yang paling relevan.
                </li>

                <li>
                    Atur urutan agar pertanyaan penting muncul lebih awal.
                </li>

                <li>
                    Publikasikan hanya informasi yang sudah benar.
                </li>
            </ul>

        </div>


        <div class="card faq-status-card">

            <h3>
                Informasi Publikasi
            </h3>

            <div class="faq-status-info">

                <span class="faq-status-dot faq-dot-green"></span>

                <div>
                    <strong>
                        Dipublikasikan
                    </strong>

                    <p>
                        FAQ akan ditampilkan pada halaman utama website.
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

    .faq-create-layout {
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


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    .faq-form {
        padding: 27px;
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

    .faq-info-icon {
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

    .faq-info-icon svg {
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

        .faq-create-layout {
            grid-template-columns: 1fr;
        }

        .faq-information {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {

        .faq-card-header {
            padding: 20px;
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
