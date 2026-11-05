@extends('layouts.customer')

@section('title', 'Profil Akun')
@section('topbar-title', 'Profil Akun')

@section('content')

@php
    $profileInitials = collect(
        preg_split('/\s+/', trim($user->name))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return strtoupper(
                mb_substr($word, 0, 1)
            );
        })
        ->implode('');
@endphp

<div class="profile-page">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Profil Akun
            </h1>

            <p class="page-description">
                Kelola informasi akun dan keamanan akun
                Ragen Swim Course kamu.
            </p>
        </div>
    </div>

    {{-- PROFILE SUMMARY --}}
    <section class="card profile-summary">

        <div class="profile-summary-main">

            <div class="profile-large-avatar">
                {{ $profileInitials ?: 'RS' }}
            </div>

            <div class="profile-summary-info">

                <h2>
                    {{ $user->name }}
                </h2>

                <p>
                    {{ $user->email }}
                </p>

                <div class="profile-badges">

                    <span class="profile-badge profile-badge-role">
                        Customer
                    </span>

                    @if ($user->status === 'active')
                        <span class="profile-badge profile-badge-active">
                            Akun Aktif
                        </span>
                    @else
                        <span class="profile-badge profile-badge-inactive">
                            {{ ucfirst($user->status) }}
                        </span>
                    @endif

                </div>
            </div>
        </div>

        <div class="profile-summary-meta">

            <div class="summary-meta-item">
                <span class="summary-meta-label">
                    Nomor Telepon
                </span>

                <strong>
                    {{ $user->phone ?: 'Belum diisi' }}
                </strong>
            </div>

            <div class="summary-meta-item">
                <span class="summary-meta-label">
                    Bergabung Sejak
                </span>

                <strong>
                    {{ $user->created_at
                        ? $user->created_at->translatedFormat('d F Y')
                        : '-' }}
                </strong>
            </div>

        </div>
    </section>

    {{-- PROFILE GRID --}}
    <div class="profile-grid">

        {{-- INFORMASI AKUN --}}
        <section class="card profile-card">

            <div class="profile-card-header">

                <div class="profile-card-icon icon-blue">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle
                            cx="12"
                            cy="8"
                            r="3.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <div>
                    <h2>Informasi Akun</h2>

                    <p>
                        Perbarui nama, email, dan nomor telepon akun.
                    </p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('customer.profile.update') }}"
                class="profile-form"
            >
                @csrf
                @method('PUT')

                {{-- NAME --}}
                <div class="form-group">

                    <label for="name" class="form-label">
                        Nama Lengkap
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- EMAIL --}}
                <div class="form-group">

                    <label for="email" class="form-label">
                        Email
                        <span>*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="contoh@email.com"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="field-help">
                        Email digunakan sebagai identitas akun
                        dan untuk proses login.
                    </div>

                </div>

                {{-- PHONE --}}
                <div class="form-group">

                    <label for="phone" class="form-label">
                        Nomor Telepon / WhatsApp
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                        placeholder="Contoh: 081234567890"
                        autocomplete="tel"
                    >

                    @error('phone')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="field-help">
                        Gunakan nomor yang aktif agar mudah dihubungi
                        terkait jadwal dan kelas renang.
                    </div>

                </div>

                {{-- KONFIRMASI PASSWORD UNTUK PERUBAHAN EMAIL --}}
                <div class="form-group">

                    <label
                        for="profile_current_password"
                        class="form-label"
                    >
                        Password Saat Ini
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="profile_current_password"
                            name="current_password"
                            class="form-control password-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                            placeholder="Isi jika ingin mengganti email"
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="profile_current_password"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M2.5 12C4.5 8.5 7.7 6.5 12 6.5C16.3 6.5 19.5 8.5 21.5 12C19.5 15.5 16.3 17.5 12 17.5C7.7 17.5 4.5 15.5 2.5 12Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                            </svg>
                        </button>

                    </div>

                    @error('current_password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="field-help">
                        <strong>
                            Hanya wajib jika kamu mengganti alamat email.
                        </strong>
                        Jika hanya mengubah nama atau nomor WhatsApp,
                        kolom ini boleh dikosongkan.
                    </div>

                </div>

                <div class="security-notice profile-email-notice">

                    <div class="security-notice-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M12 10V16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <circle
                                cx="12"
                                cy="7"
                                r="1"
                                fill="currentColor"
                            />
                        </svg>
                    </div>

                    <div>
                        <strong>
                            Keamanan Perubahan Email
                        </strong>

                        <p>
                            Jika alamat email diganti, kamu akan
                            otomatis keluar dari akun. Silakan login
                            kembali menggunakan email baru.
                        </p>
                    </div>

                </div>

                <div class="form-actions">

                    <button
                        type="submit"
                        class="profile-button profile-button-primary"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M5 4H16L19 7V20H5V4Z"
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
                                d="M8 20V14H16V20"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                        </svg>

                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </section>

        {{-- KEAMANAN AKUN --}}
        <section class="card profile-card">

            <div class="profile-card-header">

                <div class="profile-card-icon icon-purple">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect
                            x="4"
                            y="10"
                            width="16"
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
                            r="1.3"
                            fill="currentColor"
                        />
                    </svg>
                </div>

                <div>
                    <h2>Keamanan Akun</h2>

                    <p>
                        Ganti password untuk menjaga keamanan akun.
                    </p>
                </div>

            </div>

            <div class="security-notice">

                <div class="security-notice-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M12 10V16"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />

                        <circle
                            cx="12"
                            cy="7"
                            r="1"
                            fill="currentColor"
                        />
                    </svg>
                </div>

                <div>
                    <strong>Keamanan Password</strong>

                    <p>
                        Kamu harus memasukkan password saat ini
                        sebelum membuat password baru.
                    </p>
                </div>

            </div>

            <form
                method="POST"
                action="{{ route('customer.profile.password.update') }}"
                class="profile-form"
            >
                @csrf
                @method('PUT')

                {{-- CURRENT PASSWORD --}}
                <div class="form-group">

                    <label
                        for="password_current"
                        class="form-label"
                    >
                        Password Saat Ini
                        <span>*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password_current"
                            name="current_password"
                            class="form-control password-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan password saat ini"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="password_current"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M2.5 12C4.5 8.5 7.7 6.5 12 6.5C16.3 6.5 19.5 8.5 21.5 12C19.5 15.5 16.3 17.5 12 17.5C7.7 17.5 4.5 15.5 2.5 12Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                            </svg>
                        </button>

                    </div>

                    @error('current_password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- NEW PASSWORD --}}
                <div class="form-group">

                    <label for="password" class="form-label">
                        Password Baru
                        <span>*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control password-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="password"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M2.5 12C4.5 8.5 7.7 6.5 12 6.5C16.3 6.5 19.5 8.5 21.5 12C19.5 15.5 16.3 17.5 12 17.5C7.7 17.5 4.5 15.5 2.5 12Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                            </svg>
                        </button>

                    </div>

                    @error('password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="field-help">
                        Gunakan minimal 8 karakter yang
                        mengandung huruf dan angka.
                    </div>

                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="form-group">

                    <label
                        for="password_confirmation"
                        class="form-label"
                    >
                        Konfirmasi Password Baru
                        <span>*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control password-input"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle="password_confirmation"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M2.5 12C4.5 8.5 7.7 6.5 12 6.5C16.3 6.5 19.5 8.5 21.5 12C19.5 15.5 16.3 17.5 12 17.5C7.7 17.5 4.5 15.5 2.5 12Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                            </svg>
                        </button>

                    </div>

                </div>

                <div class="form-actions">

                    <button
                        type="submit"
                        class="profile-button profile-button-dark"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M8 11V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V11"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <rect
                                x="5"
                                y="11"
                                width="14"
                                height="9"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                        </svg>

                        Ganti Password
                    </button>

                </div>

            </form>

        </section>

    </div>

    {{-- ACCOUNT INFO --}}
    <section class="card account-info-card">

        <div class="account-info-icon">
            <svg viewBox="0 0 24 24" fill="none">
                <circle
                    cx="12"
                    cy="12"
                    r="9"
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
                    cy="7.5"
                    r="1"
                    fill="currentColor"
                />
            </svg>
        </div>

        <div>
            <h3>Tentang data peserta</h3>

            <p>
                Data pada halaman ini merupakan informasi akun
                customer. Nama peserta renang, tanggal lahir,
                kondisi khusus, dan data peserta lainnya dapat
                dikelola melalui menu
                <a href="{{ route('customer.participants.index') }}">
                    Peserta
                </a>.
            </p>
        </div>

    </section>

</div>

@endsection

@push('styles')

<style>

    .profile-page {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Summary
    |--------------------------------------------------------------------------
    */

    .profile-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        padding: 22px;
    }

    .profile-summary-main {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .profile-large-avatar {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 18px;
        background:
            linear-gradient(
                135deg,
                var(--blue),
                var(--aqua)
            );
        color: white;
        font-size: 19px;
        font-weight: 850;
        box-shadow:
            0 8px 20px rgba(2, 132, 199, .16);
    }

    .profile-summary-info {
        min-width: 0;
    }

    .profile-summary-info h2 {
        overflow: hidden;
        color: var(--navy);
        font-size: 18px;
        font-weight: 850;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profile-summary-info > p {
        margin-top: 4px;
        overflow: hidden;
        color: var(--text-soft);
        font-size: 11px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profile-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 850;
    }

    .profile-badge-role {
        background: #e0f2fe;
        color: #0369a1;
    }

    .profile-badge-active {
        background: #dcfce7;
        color: #15803d;
    }

    .profile-badge-inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .profile-summary-meta {
        display: flex;
        align-items: center;
        gap: 35px;
        flex-shrink: 0;
    }

    .summary-meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .summary-meta-label {
        color: var(--text-light);
        font-size: 8px;
        font-weight: 750;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .summary-meta-item strong {
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Grid
    |--------------------------------------------------------------------------
    */

    .profile-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .profile-card {
        overflow: hidden;
        padding: 0;
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 20px 21px;
        border-bottom: 1px solid var(--border);
    }

    .profile-card-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
    }

    .profile-card-icon svg {
        width: 21px;
        height: 21px;
    }

    .icon-blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .icon-purple {
        background: #f3e8ff;
        color: #9333ea;
    }

    .profile-card-header h2 {
        color: var(--navy);
        font-size: 14px;
        font-weight: 850;
    }

    .profile-card-header p {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    .profile-form {
        padding: 21px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
    }

    .form-label span {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        outline: none;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: white;
        color: var(--text);
        font-size: 11px;
        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .form-control:focus {
        border-color: #38bdf8;
        box-shadow:
            0 0 0 3px rgba(14, 165, 233, .09);
    }

    .form-control.is-invalid {
        border-color: #fca5a5;
    }

    .form-control::placeholder {
        color: #cbd5e1;
    }

    .field-help {
        margin-top: 6px;
        color: var(--text-light);
        font-size: 8px;
        line-height: 1.5;
    }

    .field-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 8px;
        font-weight: 700;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 3px;
    }

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    .password-wrapper {
        position: relative;
    }

    .password-input {
        padding-right: 45px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 8px;
        width: 31px;
        height: 31px;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: var(--text-light);
        cursor: pointer;
    }

    .password-toggle:hover {
        background: #f1f5f9;
        color: var(--blue);
    }

    .password-toggle svg {
        width: 17px;
        height: 17px;
    }

    .security-notice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 18px 21px 0;
        padding: 12px;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        background: #f8faff;
    }

    .profile-email-notice {
        margin: 0 0 18px;
    }

    .security-notice-icon {
        width: 27px;
        height: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        background: #eef2ff;
        color: #6366f1;
    }

    .security-notice-icon svg {
        width: 15px;
        height: 15px;
    }

    .security-notice strong {
        display: block;
        color: #3730a3;
        font-size: 9px;
        font-weight: 850;
    }

    .security-notice p {
        margin-top: 3px;
        color: #6366f1;
        font-size: 8px;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------
    */

    .profile-button {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 14px;
        border: 0;
        border-radius: 9px;
        font-size: 10px;
        font-weight: 850;
        cursor: pointer;
        transition:
            transform .2s ease,
            background .2s ease;
    }

    .profile-button:hover {
        transform: translateY(-1px);
    }

    .profile-button svg {
        width: 16px;
        height: 16px;
    }

    .profile-button-primary {
        background: var(--blue);
        color: white;
    }

    .profile-button-primary:hover {
        background: var(--blue-dark);
    }

    .profile-button-dark {
        background: var(--navy);
        color: white;
    }

    .profile-button-dark:hover {
        background: var(--navy-dark);
    }

    /*
    |--------------------------------------------------------------------------
    | Account Information
    |--------------------------------------------------------------------------
    */

    .account-info-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 17px 19px;
    }

    .account-info-icon {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0284c7;
    }

    .account-info-icon svg {
        width: 18px;
        height: 18px;
    }

    .account-info-card h3 {
        color: var(--navy);
        font-size: 10px;
        font-weight: 850;
    }

    .account-info-card p {
        max-width: 800px;
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.6;
    }

    .account-info-card a {
        color: var(--blue);
        font-weight: 800;
        text-decoration: none;
    }

    .account-info-card a:hover {
        text-decoration: underline;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1000px) {

        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-summary-meta {
            width: 100%;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
    }

    @media (max-width: 600px) {

        .profile-summary-main {
            align-items: flex-start;
        }

        .profile-large-avatar {
            width: 54px;
            height: 54px;
            border-radius: 15px;
            font-size: 16px;
        }

        .profile-summary-meta {
            align-items: flex-start;
            flex-direction: column;
            gap: 14px;
        }

        .form-actions {
            display: block;
        }

        .profile-button {
            width: 100%;
        }
    }

</style>

@endpush

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const passwordButtons =
            document.querySelectorAll('[data-password-toggle]');

        passwordButtons.forEach(function (button) {

            button.addEventListener('click', function () {

                const inputId =
                    button.getAttribute('data-password-toggle');

                const input =
                    document.getElementById(inputId);

                if (!input) {
                    return;
                }

                if (input.type === 'password') {

                    input.type = 'text';

                    button.setAttribute(
                        'aria-label',
                        'Sembunyikan password'
                    );

                    button.setAttribute(
                        'aria-pressed',
                        'true'
                    );

                } else {

                    input.type = 'password';

                    button.setAttribute(
                        'aria-label',
                        'Tampilkan password'
                    );

                    button.setAttribute(
                        'aria-pressed',
                        'false'
                    );
                }
            });
        });
    });
</script>

@endpush
