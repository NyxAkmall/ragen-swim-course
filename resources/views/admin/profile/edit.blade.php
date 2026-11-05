@extends('layouts.admin')

@section('title', 'Pengaturan Akun Admin')
@section('topbar-title', 'Pengaturan Akun')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan Akun</h1>
        <p class="page-description">
            Kelola informasi pribadi dan keamanan akun
            Administrator Ragen Swim Course.
        </p>
    </div>
</div>

<div class="admin-profile-container">

    {{-- Informasi Akun --}}
    <section class="admin-profile-card">

        <div class="admin-profile-card-header">
            <div class="admin-profile-icon">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                </svg>
            </div>

            <div>
                <h2>Informasi Akun</h2>
                <p>Perbarui nama dan alamat email administrator.</p>
            </div>
        </div>

        <form
            action="{{ route('admin.profile.update') }}"
            method="POST"
            class="admin-profile-form"
        >
            @csrf
            @method('PUT')

            <div class="admin-profile-field">
                <label for="admin_name">Nama Lengkap</label>

                <input
                    type="text"
                    id="admin_name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="255"
                    autocomplete="name"
                    required
                    class="@error('name') is-invalid @enderror"
                >

                @error('name')
                    <span class="admin-profile-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="admin-profile-field">
                <label for="admin_email">Alamat Email</label>

                <input
                    type="email"
                    id="admin_email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    maxlength="150"
                    autocomplete="email"
                    required
                    class="@error('email') is-invalid @enderror"
                >

                @error('email')
                    <span class="admin-profile-error">
                        {{ $message }}
                    </span>
                @enderror

                <small>
                    Jika email diubah, Anda akan diminta
                    login kembali menggunakan email baru.
                </small>
            </div>

            <div class="admin-profile-field">
                <label for="admin_current_password_email">
                    Password Saat Ini
                </label>

                <input
                    type="password"
                    id="admin_current_password_email"
                    name="current_password"
                    autocomplete="current-password"
                    placeholder="Wajib jika mengganti email"
                    class="@error('current_password') is-invalid @enderror"
                >

                @error('current_password')
                    <span class="admin-profile-error">
                        {{ $message }}
                    </span>
                @enderror

                <small>
                    Kosongkan jika hanya mengganti nama.
                </small>
            </div>

            <div class="admin-profile-actions">
                <button type="submit" class="admin-profile-button">
                    Simpan Informasi Akun
                </button>
            </div>
        </form>

    </section>

    {{-- Keamanan Akun --}}
    <section class="admin-profile-card">

        <div class="admin-profile-card-header">
            <div class="admin-profile-icon admin-profile-icon-security">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     aria-hidden="true">
                    <rect x="5" y="10" width="14" height="11" rx="2"/>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    <path d="M12 14v3"/>
                </svg>
            </div>

            <div>
                <h2>Keamanan Akun</h2>
                <p>Ganti password untuk menjaga keamanan akun.</p>
            </div>
        </div>

        <form
            action="{{ route('admin.profile.password.update') }}"
            method="POST"
            class="admin-profile-form"
        >
            @csrf
            @method('PUT')

            <div class="admin-profile-field">
                <label for="admin_password_current">
                    Password Saat Ini
                </label>

                <input
                    type="password"
                    id="admin_password_current"
                    name="current_password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="admin-profile-field">
                <label for="admin_password_new">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="admin_password_new"
                    name="password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                    class="@error('password') is-invalid @enderror"
                >

                @error('password')
                    <span class="admin-profile-error">
                        {{ $message }}
                    </span>
                @enderror

                <small>
                    Minimal 8 karakter, mengandung huruf dan angka.
                </small>
            </div>

            <div class="admin-profile-field">
                <label for="admin_password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    id="admin_password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <div class="admin-profile-actions">
                <button type="submit" class="admin-profile-button">
                    Perbarui Password
                </button>
            </div>
        </form>

    </section>

</div>

@endsection

@push('styles')
<style>
    .admin-profile-container {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
        gap: 24px;
        margin-bottom: 32px;
    }

    .admin-profile-card {
        min-width: 0;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(15, 35, 68, 0.04);
    }

    .admin-profile-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 24px;
        border-bottom: 1px solid #edf2f7;
    }

    .admin-profile-icon {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 14px;
    }

    .admin-profile-icon-security {
        background: #ecfeff;
        color: #0891b2;
    }

    .admin-profile-icon svg {
        width: 24px;
        height: 24px;
    }

    .admin-profile-card-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: #082f49;
    }

    .admin-profile-card-header p {
        margin: 5px 0 0;
        font-size: 12px;
        line-height: 1.6;
        color: #64748b;
    }

    .admin-profile-form {
        padding: 24px;
    }

    .admin-profile-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 22px;
    }

    .admin-profile-field label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .admin-profile-field input {
        width: 100%;
        min-height: 46px;
        padding: 11px 14px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        color: #0f172a;
        font: inherit;
        font-size: 13px;
        box-sizing: border-box;
        transition: border-color .2s, box-shadow .2s;
    }

    .admin-profile-field input:focus {
        outline: none;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, .12);
    }

    .admin-profile-field input.is-invalid {
        border-color: #dc2626;
    }

    .admin-profile-field small {
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .admin-profile-error {
        color: #dc2626;
        font-size: 12px;
        line-height: 1.5;
    }

    .admin-profile-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 8px;
    }

    .admin-profile-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 11px 20px;
        border: none;
        border-radius: 10px;
        background: #0369a1;
        color: #ffffff;
        font: inherit;
        font-size: 13px;
        font-weight: 750;
        cursor: pointer;
        transition: background .2s;
    }

    .admin-profile-button:hover {
        background: #075985;
    }

    .admin-profile-button:focus-visible {
        outline: 3px solid #7dd3fc;
        outline-offset: 3px;
    }

    @media (max-width: 1000px) {
        .admin-profile-container {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .admin-profile-container {
            gap: 16px;
        }

        .admin-profile-card-header,
        .admin-profile-form {
            padding: 18px;
        }

        .admin-profile-actions {
            display: block;
        }

        .admin-profile-button {
            width: 100%;
        }
    }
</style>
@endpush
