@extends('layouts.auth')

@section('title', 'Reset Password | Ragen Swim Course')

@section('content')

<div class="auth-heading">
    <h2>Buat Password Baru</h2>

    <p>
        Silakan masukkan password baru untuk
        akun Ragen Swim Course Anda.
        Gunakan minimal 8 karakter yang
        mengandung huruf dan angka.
    </p>
</div>

@if ($errors->any())
    <div
        role="alert"
        style="padding: 14px; margin-bottom: 20px; border-radius: 8px; background: #fef2f2; color: #991b1b;"
    >
        Silakan periksa kembali informasi yang Anda masukkan.
    </div>
@endif

<form
    method="POST"
    action="{{ route('password.update') }}"
>
    @csrf

    {{-- TOKEN RESET --}}
    <input
        type="hidden"
        name="token"
        value="{{ $token }}"
    >

    {{-- EMAIL --}}
    <div class="form-group">
        <label for="email" class="form-label">
            Alamat Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            class="form-input @error('email') is-invalid @enderror"
            value="{{ old('email', $email) }}"
            placeholder="contoh@email.com"
            autocomplete="email"
            maxlength="150"
            required
            @error('email')
                aria-invalid="true"
                aria-describedby="email-error"
            @enderror
        >

        @error('email')
            <div
                class="form-error"
                id="email-error"
                role="alert"
            >
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- PASSWORD BARU --}}
    <div class="form-group">
        <label for="password" class="form-label">
            Password Baru
        </label>

        <div class="password-wrapper">
            <input
                type="password"
                id="password"
                name="password"
                class="form-input @error('password') is-invalid @enderror"
                placeholder="Masukkan password baru"
                autocomplete="new-password"
                minlength="8"
                required
                @error('password')
                    aria-invalid="true"
                    aria-describedby="password-error"
                @enderror
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password"
                aria-label="Tampilkan password"
                aria-pressed="false"
                title="Tampilkan atau sembunyikan password"
            >
                Tampilkan
            </button>
        </div>

        @error('password')
            <div
                class="form-error"
                id="password-error"
                role="alert"
            >
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- KONFIRMASI PASSWORD --}}
    <div class="form-group">
        <label
            for="password_confirmation"
            class="form-label"
        >
            Konfirmasi Password Baru
        </label>

        <div class="password-wrapper">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input"
                placeholder="Ulangi password baru"
                autocomplete="new-password"
                minlength="8"
                required
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password_confirmation"
                aria-label="Tampilkan konfirmasi password"
                aria-pressed="false"
                title="Tampilkan atau sembunyikan password"
            >
                Tampilkan
            </button>
        </div>
    </div>

    {{-- TOMBOL SIMPAN --}}
    <button
        type="submit"
        class="btn-primary"
    >
        Simpan Password Baru
    </button>
</form>

<div class="auth-switch">
    Tautan reset sudah kedaluwarsa?

    <a href="{{ route('password.request') }}">
        Minta Tautan Baru
    </a>
</div>

@endsection
