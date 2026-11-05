@extends('layouts.customer')

@section('title', 'Tambah Peserta')
@section('topbar-title', 'Tambah Peserta')
@section('topbar-subtitle', 'Daftarkan profil peserta untuk mengikuti kelas renang.')

@section('content')

<div class="participant-form-page">

    <div class="form-page-header">

        <a
            href="{{ route('customer.participants.index') }}"
            class="back-link"
        >
            ← Kembali ke Peserta Saya
        </a>

        <h1 class="page-title">
            Tambah Peserta
        </h1>

        <p class="page-description">
            Lengkapi informasi peserta yang akan mengikuti
            program berenang di Ragen Swim Course.
        </p>

    </div>

    {{-- VALIDATION ERRORS --}}

    @if ($errors->any())

        <div class="form-error-box" role="alert">

            <div class="error-title">
                Ada data yang perlu diperbaiki
            </div>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('customer.participants.store') }}"
        class="participant-form"
    >
        @csrf

        {{-- JENIS PESERTA --}}

        <section class="form-card">

            <div class="section-heading">

                <div class="section-number">1</div>

                <div>
                    <h2>Siapa yang Akan Mengikuti Kelas?</h2>
                    <p>
                        Pilih apakah peserta adalah diri sendiri atau anak.
                    </p>
                </div>

            </div>

            <div
                class="participant-type-grid"
                role="group"
                aria-label="Jenis peserta"
            >

                <label class="type-option">

                    <input
                        type="radio"
                        name="participant_type"
                        value="self"
                        @checked(old('participant_type', 'child') === 'self')
                        required
                    >

                    <span class="type-card">

                        <span class="type-icon" aria-hidden="true">👤</span>

                        <span class="type-content">
                            <strong>Diri Sendiri</strong>
                            <small>
                                Saya sendiri yang akan mengikuti kelas.
                            </small>
                        </span>

                        <span class="radio-indicator"></span>

                    </span>

                </label>

                <label class="type-option">

                    <input
                        type="radio"
                        name="participant_type"
                        value="child"
                        @checked(old('participant_type', 'child') === 'child')
                        required
                    >

                    <span class="type-card">

                        <span class="type-icon" aria-hidden="true">🧒</span>

                        <span class="type-content">
                            <strong>Anak</strong>
                            <small>
                                Saya mendaftarkan anak untuk mengikuti kelas.
                            </small>
                        </span>

                        <span class="radio-indicator"></span>

                    </span>

                </label>

            </div>

            @error('participant_type')
                <div class="field-error" role="alert">
                    {{ $message }}
                </div>
            @enderror

        </section>

        {{-- DATA PESERTA --}}

        <section class="form-card">

            <div class="section-heading">

                <div class="section-number">2</div>

                <div>
                    <h2>Data Peserta</h2>
                    <p>
                        Isi informasi dasar peserta dengan benar.
                    </p>
                </div>

            </div>

            <div class="form-grid">

                <div class="form-group full-width">

                    <label for="full_name">
                        Nama Lengkap
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="{{ old('full_name') }}"
                        placeholder="Contoh: Ahmad Santoso"
                        maxlength="150"
                        autocomplete="name"
                        class="@error('full_name') is-invalid @enderror"
                        @error('full_name')
                            aria-invalid="true"
                        @enderror
                        required
                    >

                    @error('full_name')
                        <span class="field-error" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="gender">
                        Jenis Kelamin
                        <span class="required">*</span>
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        class="@error('gender') is-invalid @enderror"
                        required
                    >
                        <option value="">Pilih jenis kelamin</option>

                        <option
                            value="male"
                            @selected(old('gender') === 'male')
                        >
                            Laki-laki
                        </option>

                        <option
                            value="female"
                            @selected(old('gender') === 'female')
                        >
                            Perempuan
                        </option>
                    </select>

                    @error('gender')
                        <span class="field-error" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="birth_date">
                        Tanggal Lahir
                        <span class="required">*</span>
                    </label>

                    <input
                        type="date"
                        id="birth_date"
                        name="birth_date"
                        value="{{ old('birth_date') }}"
                        max="{{ now()->format('Y-m-d') }}"
                        class="@error('birth_date') is-invalid @enderror"
                        required
                    >

                    @error('birth_date')
                        <span class="field-error" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>

        </section>

        {{-- KEMAMPUAN BERENANG --}}

        <section class="form-card">

            <div class="section-heading">

                <div class="section-number">3</div>

                <div>
                    <h2>Kemampuan Berenang</h2>
                    <p>
                        Pilih tingkat kemampuan yang paling mendekati
                        kondisi peserta saat ini.
                    </p>
                </div>

            </div>

            <div
                class="level-grid"
                role="group"
                aria-label="Tingkat kemampuan berenang"
            >

                <label class="level-option">

                    <input
                        type="radio"
                        name="swimming_level"
                        value="beginner"
                        @checked(old('swimming_level', 'beginner') === 'beginner')
                        required
                    >

                    <span class="level-card">
                        <strong>Pemula</strong>
                        <small>
                            Belum bisa berenang atau baru mulai belajar.
                        </small>
                    </span>

                </label>

                <label class="level-option">

                    <input
                        type="radio"
                        name="swimming_level"
                        value="basic"
                        @checked(old('swimming_level') === 'basic')
                    >

                    <span class="level-card">
                        <strong>Dasar</strong>
                        <small>
                            Sudah mengenal pernapasan dan gerakan dasar.
                        </small>
                    </span>

                </label>

                <label class="level-option">

                    <input
                        type="radio"
                        name="swimming_level"
                        value="intermediate"
                        @checked(old('swimming_level') === 'intermediate')
                    >

                    <span class="level-card">
                        <strong>Menengah</strong>
                        <small>
                            Sudah dapat berenang dan ingin meningkatkan teknik.
                        </small>
                    </span>

                </label>

                <label class="level-option">

                    <input
                        type="radio"
                        name="swimming_level"
                        value="advanced"
                        @checked(old('swimming_level') === 'advanced')
                    >

                    <span class="level-card">
                        <strong>Lanjutan</strong>
                        <small>
                            Sudah menguasai teknik berenang dengan baik.
                        </small>
                    </span>

                </label>

            </div>

            @error('swimming_level')
                <div class="field-error" role="alert">
                    {{ $message }}
                </div>
            @enderror

        </section>

        {{-- KONTAK DARURAT --}}

        <section class="form-card">

            <div class="section-heading">

                <div class="section-number">4</div>

                <div>
                    <h2>Kontak Darurat</h2>
                    <p>
                        Kontak yang dapat dihubungi apabila diperlukan
                        saat kegiatan berlangsung.
                    </p>
                </div>

            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label for="emergency_contact_name">
                        Nama Kontak Darurat
                    </label>

                    <input
                        type="text"
                        id="emergency_contact_name"
                        name="emergency_contact_name"
                        value="{{ old('emergency_contact_name') }}"
                        placeholder="Contoh: Budi Santoso"
                        maxlength="150"
                        class="@error('emergency_contact_name') is-invalid @enderror"
                    >

                    @error('emergency_contact_name')
                        <span class="field-error" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <div class="form-group">

                    <label for="emergency_contact_phone">
                        Nomor WhatsApp / Telepon
                    </label>

                    <input
                        type="tel"
                        id="emergency_contact_phone"
                        name="emergency_contact_phone"
                        value="{{ old('emergency_contact_phone') }}"
                        placeholder="Contoh: 081234567890"
                        maxlength="30"
                        autocomplete="tel"
                        class="@error('emergency_contact_phone') is-invalid @enderror"
                    >

                    @error('emergency_contact_phone')
                        <span class="field-error" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>

            <div class="emergency-info">

                <span aria-hidden="true">ℹ️</span>

                <p>
                    Untuk peserta anak, sebaiknya isi nomor orang tua
                    atau wali yang mudah dihubungi.
                </p>

            </div>

        </section>

        {{-- INFORMASI TAMBAHAN --}}

        <section class="form-card">

            <div class="section-heading">

                <div class="section-number">5</div>

                <div>
                    <h2>Informasi Tambahan</h2>
                    <p>
                        Bagian ini tidak wajib diisi.
                    </p>
                </div>

            </div>

            <div class="form-group">

                <label for="notes">
                    Catatan untuk Ragen Swim Course
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="5"
                    maxlength="2000"
                    placeholder="Contoh: Peserta masih takut memasukkan kepala ke dalam air."
                    class="@error('notes') is-invalid @enderror"
                >{{ old('notes') }}</textarea>

                <div class="input-help">
                    Tuliskan informasi yang perlu diketahui pelatih
                    agar proses belajar lebih nyaman dan terarah.
                </div>

                @error('notes')
                    <span class="field-error" role="alert">
                        {{ $message }}
                    </span>
                @enderror

            </div>

        </section>

        {{-- ACTION BUTTONS --}}

        <div class="form-actions">

            <a
                href="{{ route('customer.participants.index') }}"
                class="btn-cancel"
            >
                Batal
            </a>

            <button
                type="submit"
                class="btn-save"
            >
                Simpan Peserta
            </button>

        </div>

    </form>

</div>

@endsection

@push('styles')

<style>

    .participant-form-page {
        max-width: 900px;
    }

    .form-page-header {
        margin-bottom: 24px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;

        margin-bottom: 16px;

        color: var(--blue-dark);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Errors
    |--------------------------------------------------------------------------
    */

    .form-error-box {
        margin-bottom: 22px;
        padding: 17px 19px;

        border: 1px solid #fecaca;
        border-radius: 12px;

        background: #fef2f2;
        color: #991b1b;
    }

    .error-title {
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 800;
    }

    .form-error-box ul {
        margin: 0;
        padding-left: 20px;

        font-size: 13px;
        line-height: 1.8;
    }

    /*
    |--------------------------------------------------------------------------
    | Form Cards
    |--------------------------------------------------------------------------
    */

    .participant-form {
        display: flex;
        flex-direction: column;
        gap: 19px;
    }

    .form-card {
        padding: 25px;

        border: 1px solid var(--border);
        border-radius: 16px;

        background: var(--white);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.035);
    }

    .section-heading {
        display: flex;
        align-items: flex-start;
        gap: 13px;

        margin-bottom: 23px;
    }

    .section-number {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 34px;
        height: 34px;
        flex-shrink: 0;

        border-radius: 10px;

        background: #e0f2fe;
        color: #0369a1;

        font-size: 13px;
        font-weight: 800;
    }

    .section-heading h2 {
        margin: 0;

        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
        line-height: 1.4;
    }

    .section-heading p {
        margin: 5px 0 0;

        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Participant Type
    |--------------------------------------------------------------------------
    */

    .participant-type-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
    }

    .type-option {
        position: relative;
        cursor: pointer;
    }

    .type-option input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .type-card {
        position: relative;

        display: flex;
        align-items: center;
        min-height: 92px;

        padding: 16px;

        border: 1px solid var(--border);
        border-radius: 12px;

        background: var(--white);
        transition: border-color 0.2s, background 0.2s;
    }

    .type-option input:checked + .type-card {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .type-option input:focus-visible + .type-card {
        outline: 3px solid #67e8f9;
        outline-offset: 3px;
    }

    .type-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 44px;
        height: 44px;
        flex-shrink: 0;

        margin-right: 12px;

        border-radius: 11px;
        background: #e0f2fe;

        font-size: 20px;
    }

    .type-content {
        display: flex;
        flex-direction: column;
        padding-right: 24px;
    }

    .type-content strong {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .type-content small {
        margin-top: 5px;

        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .radio-indicator {
        position: absolute;
        top: 50%;
        right: 14px;

        width: 17px;
        height: 17px;

        border: 2px solid #cbd5e1;
        border-radius: 50%;

        transform: translateY(-50%);
    }

    .type-option input:checked + .type-card .radio-indicator {
        border: 5px solid #0284c7;
    }

    /*
    |--------------------------------------------------------------------------
    | General Fields
    |--------------------------------------------------------------------------
    */

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .form-group label {
        margin-bottom: 9px;

        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .required {
        color: #dc2626;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 14px;

        border: 1px solid #cbd5e1;
        border-radius: 10px;

        outline: none;

        background: #ffffff;
        color: #0f172a;

        font: inherit;
        font-size: 14px;

        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input,
    .form-group select {
        min-height: 47px;
    }

    .form-group textarea {
        resize: vertical;
        line-height: 1.7;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .form-group .is-invalid {
        border-color: #ef4444;
    }

    .field-error {
        display: block;
        margin-top: 7px;

        color: #dc2626;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }

    .input-help {
        margin-top: 8px;

        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Swimming Level
    |--------------------------------------------------------------------------
    */

    .level-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .level-option {
        position: relative;
        cursor: pointer;
    }

    .level-option input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .level-card {
        display: flex;
        flex-direction: column;
        justify-content: center;

        min-height: 91px;
        padding: 16px;

        border: 1px solid var(--border);
        border-radius: 12px;

        background: var(--white);
        transition: border-color 0.2s, background 0.2s;
    }

    .level-card strong {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .level-card small {
        margin-top: 6px;

        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .level-option input:checked + .level-card {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .level-option input:focus-visible + .level-card {
        outline: 3px solid #67e8f9;
        outline-offset: 3px;
    }

    /*
    |--------------------------------------------------------------------------
    | Emergency Contact
    |--------------------------------------------------------------------------
    */

    .emergency-info {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        margin-top: 17px;
        padding: 13px 15px;

        border-radius: 10px;
        background: #f8fafc;
    }

    .emergency-info span {
        font-size: 15px;
    }

    .emergency-info p {
        margin: 0;

        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.7;
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;

        padding: 8px 0 30px;
    }

    .btn-cancel,
    .btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 46px;
        padding: 11px 21px;

        border-radius: 10px;

        font: inherit;
        font-size: 13px;
        font-weight: 800;

        text-decoration: none;
        cursor: pointer;
    }

    .btn-cancel {
        border: 1px solid var(--border);
        background: var(--white);
        color: #475569;
    }

    .btn-cancel:hover {
        background: #f8fafc;
    }

    .btn-save {
        border: 0;
        background: var(--blue-dark);
        color: #ffffff;

        box-shadow: 0 5px 14px rgba(2, 132, 199, 0.15);
    }

    .btn-save:hover {
        filter: brightness(0.95);
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 700px) {
        .participant-type-grid,
        .form-grid,
        .level-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: auto;
        }
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 18px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn-cancel,
        .btn-save {
            width: 100%;
        }
    }

</style>

@endpush
