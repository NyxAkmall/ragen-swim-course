@extends('layouts.auth')

@section('title', 'Daftar Akun | Ragen Swim Course')

@section('content')

<div class="auth-heading">
    <h2>Buat Akun Baru</h2>

    <p>
        Daftar sebagai customer Ragen Swim Course
        untuk mulai memilih program, mengatur peserta,
        dan melakukan booking kelas.
    </p>
</div>

<form
    method="POST"
    action="{{ route('register.process') }}"
>
    @csrf

    {{-- NAMA LENGKAP --}}
    <div class="form-group">

        <label
            for="name"
            class="form-label"
        >
            Nama Lengkap
        </label>

        <input
            type="text"
            id="name"
            name="name"
            class="form-input @error('name') is-invalid @enderror"
            value="{{ old('name') }}"
            placeholder="Masukkan nama lengkap"
            autocomplete="name"
            maxlength="100"
            @error('name')
                aria-invalid="true"
                aria-describedby="name-error"
            @enderror
            required
            autofocus
        >

        @error('name')
            <div
                class="form-error"
                id="name-error"
                role="alert"
            >
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- EMAIL --}}
    <div class="form-group">

        <label
            for="email"
            class="form-label"
        >
            Alamat Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            class="form-input @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            placeholder="contoh@email.com"
            autocomplete="email"
            inputmode="email"
            maxlength="150"
            @error('email')
                aria-invalid="true"
                aria-describedby="email-error"
            @enderror
            required
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

    {{-- NOMOR WHATSAPP --}}
    <div class="form-group">

        <label
            for="phone"
            class="form-label"
        >
            Nomor WhatsApp
        </label>

        <input
            type="tel"
            id="phone"
            name="phone"
            class="form-input @error('phone') is-invalid @enderror"
            value="{{ old('phone') }}"
            placeholder="Contoh: 081234567890"
            autocomplete="tel"
            inputmode="tel"
            maxlength="20"
            @error('phone')
                aria-invalid="true"
                aria-describedby="phone-error"
            @enderror
            required
        >

        @error('phone')
            <div
                class="form-error"
                id="phone-error"
                role="alert"
            >
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- PASSWORD --}}
    <div class="form-group">

        <label
            for="password"
            class="form-label"
        >
            Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="password"
                name="password"
                class="form-input @error('password') is-invalid @enderror"
                placeholder="Masukkan password"
                autocomplete="new-password"
                minlength="8"
                aria-describedby="password-help @error('password') password-error @enderror"
                @error('password')
                    aria-invalid="true"
                @enderror
                required
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password"
                aria-label="Tampilkan password"
                aria-pressed="false"
                title="Tampilkan atau sembunyikan password"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M2.5 12C4.4 8.6 7.7 6.5 12 6.5C16.3 6.5 19.6 8.6 21.5 12C19.6 15.4 16.3 17.5 12 17.5C7.7 17.5 4.4 15.4 2.5 12Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </button>

        </div>

        <div
            id="password-help"
            style="
                margin-top: 7px;
                color: #64748b;
                font-size: 12px;
                line-height: 1.5;
            "
        >
            Minimal 8 karakter dan harus mengandung
            huruf serta angka.
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
            Konfirmasi Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input @error('password_confirmation') is-invalid @enderror"
                placeholder="Ulangi password"
                autocomplete="new-password"
                minlength="8"
                @error('password_confirmation')
                    aria-invalid="true"
                    aria-describedby="password-confirmation-error"
                @enderror
                required
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password_confirmation"
                aria-label="Tampilkan password"
                aria-pressed="false"
                title="Tampilkan atau sembunyikan password"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M2.5 12C4.4 8.6 7.7 6.5 12 6.5C16.3 6.5 19.6 8.6 21.5 12C19.6 15.4 16.3 17.5 12 17.5C7.7 17.5 4.4 15.4 2.5 12Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </button>

        </div>

        @error('password_confirmation')
            <div
                class="form-error"
                id="password-confirmation-error"
                role="alert"
            >
                {{ $message }}
            </div>
        @enderror

    </div>

    {{-- REGISTER BUTTON --}}
    <button
        type="submit"
        class="btn-primary"
    >
        Buat Akun
    </button>

</form>

{{-- LOGIN LINK --}}
<div class="auth-switch">

    Sudah punya akun?

    <a href="{{ route('login') }}">
        Masuk Sekarang
    </a>

</div>

@endsection
