@extends('layouts.admin')

@section('title', 'Tambah Coach')
@section('topbar-title', 'Tambah Coach')

@section('content')
<style>
.page-header:has(.coach-back-button){display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap}
.coach-back-button{display:inline-flex;align-items:center;justify-content:center;padding:12px 17px;border:1px solid #dbe5ef;border-radius:12px;background:#fff;color:#082b45;text-decoration:none;font-size:14px;font-weight:700;white-space:nowrap}
.coach-back-button:hover{background:#f1f7fc}
@media(max-width:640px){.coach-back-button{width:100%}}
</style>


<div class="page-header">

    <div>

        <h1 class="page-title">
            Tambah Coach
        </h1>

        <p class="page-description">
            Tambahkan akun dan profil coach baru untuk Ragen Swim Course.
            Coach dapat menggunakan akun ini untuk mengakses dashboard,
            melihat jadwal, mencatat kehadiran, dan perkembangan peserta.
        </p>

    </div>

    <a href="{{ route('admin.coaches.index') }}" class="coach-back-button">Kembali ke Coach</a>
</div>


<form
    method="POST"
    action="{{ route('admin.coaches.store') }}"
    id="coachForm"
>
    @csrf

    <div class="form-layout">

        {{-- =====================================================
            MAIN FORM
        ====================================================== --}}

        <div class="form-main">

            {{-- ACCOUNT INFORMATION --}}
            <div class="card form-card">

                <div class="form-card-heading">

                    <div class="heading-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="12"
                                cy="7"
                                r="3.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M5 20C5.5 15.8 8 14 12 14C16 14 18.5 15.8 19 20"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <div>
                        <h2>
                            Informasi Akun
                        </h2>

                        <p>
                            Data utama yang digunakan coach untuk masuk ke sistem.
                        </p>
                    </div>

                </div>


                <div class="form-card-body">

                    <div class="form-group">

                        <label for="name">
                            Nama Lengkap
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Andi Pratama"
                            maxlength="150"
                            required
                            autofocus
                            class="@error('name') input-error @enderror"
                        >

                        @error('name')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="form-grid">

                        <div class="form-group">

                            <label for="email">
                                Email
                                <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="coach@example.com"
                                maxlength="150"
                                required
                                class="@error('email') input-error @enderror"
                            >

                            <div class="field-help">
                                Email digunakan untuk login sebagai coach.
                            </div>

                            @error('email')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="form-group">

                            <label for="phone">
                                Nomor Telepon
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="Contoh: 081234567890"
                                maxlength="30"
                                required
                                class="@error('phone') input-error @enderror"
                            >

                            <div class="field-help">
                                Gunakan nomor telepon aktif coach.
                            </div>

                            @error('phone')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>


            {{-- PASSWORD --}}
            <div class="card form-card">

                <div class="form-card-heading">

                    <div class="heading-icon heading-icon-green">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="5"
                                y="10"
                                width="14"
                                height="10"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <circle
                                cx="12"
                                cy="15"
                                r="1.2"
                                fill="currentColor"
                            />
                        </svg>

                    </div>

                    <div>
                        <h2>
                            Password Akun
                        </h2>

                        <p>
                            Buat password awal yang akan digunakan coach untuk login.
                        </p>
                    </div>

                </div>


                <div class="form-card-body">

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="password">
                                Password
                                <span class="required">*</span>
                            </label>

                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    autocomplete="new-password"
                                    class="@error('password') input-error @enderror"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="password"
                                    aria-label="Tampilkan password"
                                >
                                    <svg
                                        class="eye-open"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M2.5 12C4.7 7.8 8 5.5 12 5.5C16 5.5 19.3 7.8 21.5 12C19.3 16.2 16 18.5 12 18.5C8 18.5 4.7 16.2 2.5 12Z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linejoin="round"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        />
                                    </svg>

                                    <svg
                                        class="eye-closed"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M4 4L20 20"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />

                                        <path
                                            d="M9.5 6C10.3 5.7 11.1 5.5 12 5.5C16 5.5 19.3 7.8 21.5 12C20.8 13.3 20 14.4 19 15.3M14.5 17.9C13.7 18.3 12.9 18.5 12 18.5C8 18.5 4.7 16.2 2.5 12C3.3 10.5 4.2 9.3 5.3 8.3"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </button>

                            </div>

                            <div class="field-help">
                                Minimal 8 karakter dan harus mengandung huruf serta angka.
                            </div>

                            @error('password')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="form-group">

                            <label for="password_confirmation">
                                Konfirmasi Password
                                <span class="required">*</span>
                            </label>

                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Ketik ulang password"
                                    required
                                    autocomplete="new-password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="password_confirmation"
                                    aria-label="Tampilkan konfirmasi password"
                                >
                                    <svg
                                        class="eye-open"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M2.5 12C4.7 7.8 8 5.5 12 5.5C16 5.5 19.3 7.8 21.5 12C19.3 16.2 16 18.5 12 18.5C8 18.5 4.7 16.2 2.5 12Z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linejoin="round"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        />
                                    </svg>

                                    <svg
                                        class="eye-closed"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M4 4L20 20"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />

                                        <path
                                            d="M9.5 6C10.3 5.7 11.1 5.5 12 5.5C16 5.5 19.3 7.8 21.5 12C20.8 13.3 20 14.4 19 15.3M14.5 17.9C13.7 18.3 12.9 18.5 12 18.5C8 18.5 4.7 16.2 2.5 12C3.3 10.5 4.2 9.3 5.3 8.3"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PROFESSIONAL PROFILE --}}
            <div class="card form-card">

                <div class="form-card-heading">

                    <div class="heading-icon heading-icon-aqua">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M7 10V15C7 17 9.2 18.5 12 18.5C14.8 18.5 17 17 17 15V10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <div>
                        <h2>
                            Profil Profesional
                        </h2>

                        <p>
                            Informasi pengalaman dan keahlian coach.
                        </p>
                    </div>

                </div>


                <div class="form-card-body">

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="specialization">
                                Spesialisasi
                            </label>

                            <input
                                type="text"
                                id="specialization"
                                name="specialization"
                                value="{{ old('specialization') }}"
                                placeholder="Contoh: Kids & Beginner Swimming"
                                maxlength="255"
                                class="@error('specialization') input-error @enderror"
                            >

                            <div class="field-help">
                                Contoh: Kids Swimming, Beginner, Freestyle, atau Competitive.
                            </div>

                            @error('specialization')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="form-group">

                            <label for="experience_years">
                                Pengalaman
                            </label>

                            <div class="input-suffix-wrapper">

                                <input
                                    type="number"
                                    id="experience_years"
                                    name="experience_years"
                                    value="{{ old('experience_years', 0) }}"
                                    min="0"
                                    max="80"
                                    step="1"
                                    class="@error('experience_years') input-error @enderror"
                                >

                                <span class="input-suffix">
                                    Tahun
                                </span>

                            </div>

                            @error('experience_years')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="bio">
                            Bio Coach
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            rows="6"
                            maxlength="3000"
                            placeholder="Tuliskan profil singkat, pengalaman, pendekatan mengajar, atau informasi profesional coach..."
                            class="@error('bio') input-error @enderror"
                        >{{ old('bio') }}</textarea>

                        <div class="textarea-footer">

                            <div class="field-help">
                                Maksimal 3.000 karakter.
                            </div>

                            <div
                                class="character-count"
                                id="bioCharacterCount"
                            >
                                0 / 3000
                            </div>

                        </div>

                        @error('bio')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            SIDE FORM
        ====================================================== --}}

        <div class="form-sidebar">

            {{-- STATUS --}}
            <div class="card side-card">

                <div class="side-card-heading">
                    Status Coach
                </div>

                <p class="side-card-description">
                    Tentukan apakah akun coach langsung dapat digunakan setelah dibuat.
                </p>


                <div class="status-options">

                    <label
                        class="status-option {{ old('status', 'active') === 'active' ? 'selected' : '' }}"
                        data-status-option
                    >

                        <input
                            type="radio"
                            name="status"
                            value="active"
                            @checked(old('status', 'active') === 'active')
                        >

                        <span class="status-radio"></span>

                        <span class="status-content">

                            <strong>
                                Aktif
                            </strong>

                            <small>
                                Coach dapat langsung login dan menggunakan sistem.
                            </small>

                        </span>

                    </label>


                    <label
                        class="status-option {{ old('status') === 'inactive' ? 'selected' : '' }}"
                        data-status-option
                    >

                        <input
                            type="radio"
                            name="status"
                            value="inactive"
                            @checked(old('status') === 'inactive')
                        >

                        <span class="status-radio"></span>

                        <span class="status-content">

                            <strong>
                                Tidak Aktif
                            </strong>

                            <small>
                                Akun dibuat tetapi coach belum dapat login.
                            </small>

                        </span>

                    </label>

                </div>

                @error('status')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- INFORMATION --}}
            <div class="card side-card">

                <div class="side-card-heading">
                    Informasi
                </div>

                <div class="info-box">

                    <div class="info-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />

                            <path
                                d="M12 11V16"
                                stroke="currentColor"
                                stroke-width="1.7"
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

                    <div>
                        <strong>
                            Akun dibuat otomatis
                        </strong>

                        <p>
                            Saat data disimpan, sistem akan membuat akun
                            dengan role Coach sekaligus profil coach.
                        </p>
                    </div>

                </div>


                <div class="info-list">

                    <div class="info-list-item">
                        <span class="info-check">
                            ✓
                        </span>

                        Email digunakan untuk login.
                    </div>

                    <div class="info-list-item">
                        <span class="info-check">
                            ✓
                        </span>

                        Email dan nomor telepon harus unik.
                    </div>

                    <div class="info-list-item">
                        <span class="info-check">
                            ✓
                        </span>

                        Password minimal 8 karakter.
                    </div>

                    <div class="info-list-item">
                        <span class="info-check">
                            ✓
                        </span>

                        Password wajib memiliki huruf dan angka.
                    </div>

                </div>

            </div>


            {{-- ACTION --}}
            <div class="card action-card">

                <button
                    type="submit"
                    class="save-button"
                    id="saveCoachButton"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M5 4H16L20 8V20H5V4Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M8 4V10H16V4"
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

                    Simpan Coach
                </button>


                <a
                    href="{{ route('admin.coaches.index') }}"
                    class="cancel-button"
                >
                    Batal
                </a>

            </div>

        </div>

    </div>

</form>

@endsection


@push('styles')

<style>
    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .back-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        margin-bottom: 10px;

        color: #0284c7;

        text-decoration: none;

        font-size: 12px;
        font-weight: 750;
    }

    .back-link:hover {
        color: #0369a1;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }


    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    .form-layout {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            320px;

        align-items: start;

        gap: 20px;
    }

    .form-main,
    .form-sidebar {
        display: flex;
        flex-direction: column;

        gap: 20px;
    }


    /*
    |--------------------------------------------------------------------------
    | Form Card
    |--------------------------------------------------------------------------
    */

    .form-card {
        overflow: hidden;
    }

    .form-card-heading {
        display: flex;
        align-items: center;

        gap: 13px;

        padding: 19px 21px;

        border-bottom: 1px solid var(--border);
    }

    .heading-icon {
        width: 40px;
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 11px;

        background: #eff6ff;
        color: #2563eb;
    }

    .heading-icon-green {
        background: #f0fdf4;
        color: #15803d;
    }

    .heading-icon-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .heading-icon svg {
        width: 20px;
        height: 20px;
    }

    .form-card-heading h2 {
        color: var(--navy);

        font-size: 15px;
        font-weight: 800;
    }

    .form-card-heading p {
        margin-top: 3px;

        color: var(--text-soft);

        font-size: 11px;
        line-height: 1.5;
    }

    .form-card-body {
        padding: 22px;
    }


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    .form-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 17px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-grid .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;

        margin-bottom: 7px;

        color: var(--text);

        font-size: 12px;
        font-weight: 750;
    }

    .required {
        color: #dc2626;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;

        border: 1px solid var(--border);
        border-radius: 10px;

        background: var(--white);
        color: var(--text);

        outline: none;

        font-size: 13px;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .form-group input {
        height: 44px;

        padding: 0 13px;
    }

    .form-group textarea {
        min-height: 130px;

        padding: 12px 13px;

        resize: vertical;

        line-height: 1.6;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #94a3b8;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #38bdf8;

        box-shadow:
            0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .form-group input.input-error,
    .form-group textarea.input-error {
        border-color: #fca5a5;
    }

    .form-group input.input-error:focus,
    .form-group textarea.input-error:focus {
        border-color: #ef4444;

        box-shadow:
            0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .field-help {
        margin-top: 6px;

        color: var(--text-light);

        font-size: 10px;
        line-height: 1.5;
    }

    .field-error {
        margin-top: 6px;

        color: #dc2626;

        font-size: 10px;
        font-weight: 650;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 48px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 7px;

        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        transform: translateY(-50%);

        border: 0;
        border-radius: 8px;

        background: transparent;
        color: var(--text-soft);
    }

    .password-toggle:hover {
        background: #f1f5f9;
        color: var(--navy);
    }

    .password-toggle svg {
        width: 18px;
        height: 18px;
    }

    .password-toggle .eye-closed {
        display: none;
    }

    .password-toggle.showing .eye-open {
        display: none;
    }

    .password-toggle.showing .eye-closed {
        display: block;
    }


    /*
    |--------------------------------------------------------------------------
    | Input Suffix
    |--------------------------------------------------------------------------
    */

    .input-suffix-wrapper {
        position: relative;
    }

    .input-suffix-wrapper input {
        padding-right: 65px;
    }

    .input-suffix {
        position: absolute;
        top: 50%;
        right: 13px;

        transform: translateY(-50%);

        color: var(--text-soft);

        font-size: 10px;
        font-weight: 700;

        pointer-events: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Textarea Footer
    |--------------------------------------------------------------------------
    */

    .textarea-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;
    }

    .character-count {
        margin-top: 6px;

        color: var(--text-light);

        font-size: 9px;
        font-weight: 650;
    }


    /*
    |--------------------------------------------------------------------------
    | Side Card
    |--------------------------------------------------------------------------
    */

    .side-card {
        padding: 20px;
    }

    .side-card-heading {
        color: var(--navy);

        font-size: 14px;
        font-weight: 800;
    }

    .side-card-description {
        margin-top: 6px;

        color: var(--text-soft);

        font-size: 10px;
        line-height: 1.55;
    }


    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    */

    .status-options {
        display: flex;
        flex-direction: column;

        gap: 9px;

        margin-top: 16px;
    }

    .status-option {
        position: relative;

        display: flex;
        align-items: flex-start;

        gap: 10px;

        padding: 13px;

        border: 1px solid var(--border);
        border-radius: 10px;

        cursor: pointer;

        transition:
            border-color 0.2s ease,
            background 0.2s ease;
    }

    .status-option:hover {
        border-color: #bae6fd;
        background: #f8fdff;
    }

    .status-option.selected {
        border-color: #38bdf8;
        background: #f0f9ff;
    }

    .status-option input {
        position: absolute;

        opacity: 0;

        pointer-events: none;
    }

    .status-radio {
        width: 17px;
        height: 17px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        margin-top: 1px;

        border: 1.5px solid #cbd5e1;
        border-radius: 50%;

        background: #ffffff;
    }

    .status-option.selected .status-radio {
        border: 5px solid #0284c7;
    }

    .status-content {
        min-width: 0;
    }

    .status-content strong {
        display: block;

        color: var(--text);

        font-size: 11px;
        font-weight: 800;
    }

    .status-content small {
        display: block;

        margin-top: 3px;

        color: var(--text-soft);

        font-size: 9px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    */

    .info-box {
        display: flex;
        align-items: flex-start;

        gap: 9px;

        padding: 12px;

        margin-top: 15px;

        border: 1px solid #bae6fd;
        border-radius: 10px;

        background: #f0f9ff;
    }

    .info-icon {
        width: 20px;
        height: 20px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        color: #0284c7;
    }

    .info-icon svg {
        width: 18px;
        height: 18px;
    }

    .info-box strong {
        display: block;

        color: #075985;

        font-size: 10px;
        font-weight: 800;
    }

    .info-box p {
        margin-top: 4px;

        color: #0369a1;

        font-size: 9px;
        line-height: 1.55;
    }

    .info-list {
        display: flex;
        flex-direction: column;

        gap: 9px;

        margin-top: 16px;
    }

    .info-list-item {
        display: flex;
        align-items: flex-start;

        gap: 8px;

        color: var(--text-soft);

        font-size: 10px;
        line-height: 1.5;
    }

    .info-check {
        width: 17px;
        height: 17px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        background: #f0fdf4;
        color: #15803d;

        font-size: 9px;
        font-weight: 900;
    }


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .action-card {
        padding: 16px;
    }

    .save-button {
        width: 100%;
        min-height: 44px;

        display: flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        border: 0;
        border-radius: 10px;

        background: #0284c7;
        color: #ffffff;

        font-size: 12px;
        font-weight: 800;

        transition: background 0.2s ease;
    }

    .save-button:hover {
        background: #0369a1;
    }

    .save-button:disabled {
        cursor: not-allowed;

        opacity: 0.65;
    }

    .save-button svg {
        width: 17px;
        height: 17px;
    }

    .cancel-button {
        width: 100%;
        min-height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-top: 8px;

        border: 1px solid var(--border);
        border-radius: 10px;

        background: #ffffff;
        color: var(--text-soft);

        text-decoration: none;

        font-size: 11px;
        font-weight: 750;
    }

    .cancel-button:hover {
        background: #f8fafc;
        color: var(--navy);
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1000px) {

        .form-layout {
            grid-template-columns: 1fr;
        }

        .form-sidebar {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .action-card {
            grid-column: 1 / -1;
        }
    }


    @media (max-width: 700px) {

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-sidebar {
            display: flex;
        }

        .form-card-body {
            padding: 18px;
        }

        .form-card-heading {
            padding: 17px 18px;
        }
    }
</style>

@endpush


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Password Visibility
        |--------------------------------------------------------------------------
        */

        const passwordButtons =
            document.querySelectorAll('.password-toggle');

        passwordButtons.forEach(function (button) {

            button.addEventListener('click', function () {

                const targetId =
                    button.getAttribute('data-target');

                const input =
                    document.getElementById(targetId);

                if (!input) {
                    return;
                }

                if (input.type === 'password') {

                    input.type = 'text';

                    button.classList.add('showing');

                } else {

                    input.type = 'password';

                    button.classList.remove('showing');

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Status Selector
        |--------------------------------------------------------------------------
        */

        const statusOptions =
            document.querySelectorAll('[data-status-option]');

        function refreshStatusOptions() {

            statusOptions.forEach(function (option) {

                const radio =
                    option.querySelector(
                        'input[type="radio"]'
                    );

                if (
                    radio
                    &&
                    radio.checked
                ) {
                    option.classList.add('selected');
                } else {
                    option.classList.remove('selected');
                }

            });

        }

        statusOptions.forEach(function (option) {

            const radio =
                option.querySelector(
                    'input[type="radio"]'
                );

            if (!radio) {
                return;
            }

            radio.addEventListener(
                'change',
                refreshStatusOptions
            );

        });

        refreshStatusOptions();


        /*
        |--------------------------------------------------------------------------
        | Bio Character Counter
        |--------------------------------------------------------------------------
        */

        const bio =
            document.getElementById('bio');

        const bioCharacterCount =
            document.getElementById(
                'bioCharacterCount'
            );

        function updateBioCount() {

            if (
                !bio
                ||
                !bioCharacterCount
            ) {
                return;
            }

            bioCharacterCount.textContent =
                bio.value.length
                + ' / 3000';

        }

        if (bio) {

            bio.addEventListener(
                'input',
                updateBioCount
            );

            updateBioCount();

        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Double Submit
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById('coachForm');

        const saveButton =
            document.getElementById(
                'saveCoachButton'
            );

        if (
            form
            &&
            saveButton
        ) {

            form.addEventListener(
                'submit',
                function () {

                    if (!form.checkValidity()) {
                        return;
                    }

                    saveButton.disabled = true;

                    saveButton.innerHTML =
                        'Menyimpan...';

                }
            );

        }

    });
</script>

@endpush
