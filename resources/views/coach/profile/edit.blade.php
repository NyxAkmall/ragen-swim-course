@extends('layouts.coach')

@section('title', 'Profil & Pengaturan Akun Coach')
@section('topbar-title', 'Pengaturan Akun')

@push('styles')
<style>
    .profile-shell {
        max-width: 760px;
        margin: 0 auto;
        display: grid;
        gap: 24px;
    }

    .profile-heading h1 {
        font-size: clamp(1.5rem, 3vw, 2rem);
        color: #082f49;
        letter-spacing: -.04em;
        margin-bottom: 8px;
    }

    .profile-heading p {
        color: #64748b;
        line-height: 1.65;
    }

    .profile-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: clamp(20px, 4vw, 36px);
        box-shadow: 0 12px 35px rgba(8,47,73,.05);
    }

    .profile-panel h2 {
        color: #082f49;
        font-size: 20px;
        margin: 0 0 8px;
    }

    .profile-description {
        color: #64748b;
        font-size: 14px;
        line-height: 1.65;
        margin-bottom: 24px;
    }

    .profile-preview {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .profile-preview img,
    .profile-placeholder {
        width: 104px;
        height: 104px;
        object-fit: cover;
        border-radius: 22px;
        border: 3px solid #e0f2fe;
    }

    .profile-placeholder {
        display: grid;
        place-items: center;
        background: #f0f9ff;
        color: #075985;
        font-size: 34px;
        font-weight: 800;
    }

    .profile-name {
        font-weight: 800;
        color: #082f49;
        font-size: 19px;
    }

    .profile-subtitle {
        color: #64748b;
        font-size: 13px;
        margin-top: 5px;
    }

    .profile-form {
        display: grid;
        gap: 20px;
    }

    .profile-field {
        display: grid;
        gap: 10px;
    }

    .profile-field label {
        font-weight: 750;
        color: #0f172a;
        font-size: 14px;
    }

    .profile-field input {
        width: 100%;
        padding: 13px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        font: inherit;
        box-sizing: border-box;
    }

    .profile-field input:focus {
        outline: 2px solid #7dd3fc;
        outline-offset: 1px;
        border-color: #0284c7;
    }

    .profile-help {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    .profile-submit {
        border: 0;
        border-radius: 12px;
        background: #075985;
        color: #fff;
        padding: 13px 22px;
        font-weight: 750;
        cursor: pointer;
        justify-self: start;
    }

    .profile-submit:hover {
        background: #082f49;
    }

    .profile-alert {
        padding: 14px 18px;
        border-radius: 12px;
        background: #ecfdf5;
        color: #166534;
        border: 1px solid #bbf7d0;
        line-height: 1.5;
    }

    .profile-error {
        color: #b91c1c;
        font-size: 13px;
        margin: 0;
    }

    .profile-warning {
        padding: 14px 16px;
        border-radius: 12px;
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
        font-size: 13px;
        line-height: 1.6;
    }

    .profile-password-wrap {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }

    .profile-password-wrap input {
        flex: 1;
        min-width: 0;
    }

    .profile-toggle {
        border: 1px solid #cbd5e1;
        background: #fff;
        border-radius: 12px;
        padding: 0 14px;
        cursor: pointer;
        color: #075985;
        font-weight: 700;
        white-space: nowrap;
    }

    .profile-toggle:hover {
        background: #f0f9ff;
    }

    @media (max-width: 480px) {
        .profile-preview {
            align-items: flex-start;
        }

        .profile-submit {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-shell">

    <header class="profile-heading">
        <h1>Profil & Pengaturan Akun</h1>
        <p>
            Kelola foto profil, informasi akun, dan keamanan
            password Coach Ragen Swim Course.
        </p>
    </header>

    @if(session('success'))
        <div class="profile-alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- FOTO PROFIL COACH --}}
    {{-- ========================================================= --}}

    <section class="profile-panel">
        <h2>Foto Profil</h2>

        <p class="profile-description">
            Foto ini digunakan pada profil Coach, daftar pelatih,
            dan halaman publik Ragen Swim Course.
        </p>

        <div class="profile-preview">
            @if($coach->photo)
                <img
                    src="{{ asset('storage/' . $coach->photo) }}"
                    alt="Foto profil saat ini"
                >
            @else
                <div
                    class="profile-placeholder"
                    aria-label="Belum ada foto profil"
                >
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif

            <div>
                <div class="profile-name">
                    {{ auth()->user()->name }}
                </div>

                <div class="profile-subtitle">
                    Pelatih Ragen Swim Course
                </div>
            </div>
        </div>

        <form
            action="{{ route('coach.profile.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="profile-form"
        >
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="coach-photo">
                    Pilih foto profil baru
                </label>

                <input
                    id="coach-photo"
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >

                <p class="profile-help">
                    Format JPG, PNG, atau WebP.
                    Ukuran maksimal 2 MB.
                    Pilih foto yang jelas dan sopan.
                </p>

                @error('photo')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit" class="profile-submit">
                Simpan Foto Profil
            </button>
        </form>
    </section>

    {{-- ========================================================= --}}
    {{-- INFORMASI AKUN COACH --}}
    {{-- ========================================================= --}}

    <section class="profile-panel">
        <h2>Informasi Akun</h2>

        <p class="profile-description">
            Perbarui nama lengkap dan alamat email yang digunakan
            untuk login ke akun Coach.
        </p>

        <form
            action="{{ route('coach.profile.account.update') }}"
            method="POST"
            class="profile-form"
        >
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="coach-name">
                    Nama Lengkap
                </label>

                <input
                    id="coach-name"
                    type="text"
                    name="name"
                    value="{{ old('name', auth()->user()->name) }}"
                    maxlength="255"
                    autocomplete="name"
                    required
                >

                @error('name')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="profile-field">
                <label for="coach-email">
                    Alamat Email
                </label>

                <input
                    id="coach-email"
                    type="email"
                    name="email"
                    value="{{ old('email', auth()->user()->email) }}"
                    maxlength="150"
                    autocomplete="email"
                    required
                >

                @error('email')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="profile-field">
                <label for="coach-account-current-password">
                    Password Saat Ini
                </label>

                <div class="profile-password-wrap">
                    <input
                        id="coach-account-current-password"
                        type="password"
                        name="current_password"
                        autocomplete="current-password"
                    >

                    <button
                        type="button"
                        class="profile-toggle"
                        data-password-target="coach-account-current-password"
                        aria-label="Tampilkan password saat ini"
                    >
                        Lihat
                    </button>
                </div>

                <p class="profile-help">
                    Hanya wajib diisi jika Anda mengubah alamat email.
                    Jika hanya mengganti nama, kolom ini boleh dikosongkan.
                </p>

                @error('current_password', 'account')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                @error('current_password')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="profile-warning">
                Jika alamat email diubah, Anda akan otomatis logout
                dan perlu login kembali menggunakan alamat email baru.
            </div>

            <button type="submit" class="profile-submit">
                Simpan Informasi Akun
            </button>
        </form>
    </section>

    {{-- ========================================================= --}}
    {{-- GANTI PASSWORD COACH --}}
    {{-- ========================================================= --}}

    <section class="profile-panel">
        <h2>Ganti Password</h2>

        <p class="profile-description">
            Gunakan password yang kuat dan tidak digunakan
            pada akun lain.
        </p>

        <form
            action="{{ route('coach.profile.password.update') }}"
            method="POST"
            class="profile-form"
        >
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="coach-password-current">
                    Password Saat Ini
                </label>

                <div class="profile-password-wrap">
                    <input
                        id="coach-password-current"
                        type="password"
                        name="current_password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="profile-toggle"
                        data-password-target="coach-password-current"
                        aria-label="Tampilkan password saat ini"
                    >
                        Lihat
                    </button>
                </div>
            </div>

            <div class="profile-field">
                <label for="coach-password-new">
                    Password Baru
                </label>

                <div class="profile-password-wrap">
                    <input
                        id="coach-password-new"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <button
                        type="button"
                        class="profile-toggle"
                        data-password-target="coach-password-new"
                        aria-label="Tampilkan password baru"
                    >
                        Lihat
                    </button>
                </div>

                <p class="profile-help">
                    Minimal 8 karakter dan harus mengandung
                    huruf serta angka.
                </p>

                @error('password')
                    <p class="profile-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="profile-field">
                <label for="coach-password-confirmation">
                    Konfirmasi Password Baru
                </label>

                <div class="profile-password-wrap">
                    <input
                        id="coach-password-confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <button
                        type="button"
                        class="profile-toggle"
                        data-password-target="coach-password-confirmation"
                        aria-label="Tampilkan konfirmasi password"
                    >
                        Lihat
                    </button>
                </div>
            </div>

            <button type="submit" class="profile-submit">
                Simpan Password Baru
            </button>
        </form>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.getAttribute('data-password-target');
            const input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
            button.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    });
});
</script>
@endpush
