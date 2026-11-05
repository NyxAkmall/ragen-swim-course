@extends('layouts.auth')

@section('title', 'Login | Ragen Swim Course')

@section('content')

<div class="auth-heading">
    <h2>Selamat Datang Kembali 👋</h2>

    <p>
        Masuk ke akun Ragen Swim Course untuk
        mengakses jadwal, paket kelas, booking,
        dan perkembangan peserta.
    </p>
</div>

<form
    method="POST"
    action="{{ route('login.process') }}"
>
    @csrf

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
            @error('email')
                aria-invalid="true"
                aria-describedby="email-error"
            @enderror
            required
            autofocus
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
                autocomplete="current-password"
                @error('password')
                    aria-invalid="true"
                    aria-describedby="password-error"
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
                        d="M2.5 12C4.4 8.6 7.7 6.5 12 6.5C16.3 6.5 19.6 8.6 21.5 12C19.6 15.4 16.3 17.5 12 17.5C7.7 17.5 4.4 15.4 2.5 12 17.5C7.7 17.5 4.4 15.4 2.5 12Z"
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

    {{-- LUPA PASSWORD --}}
    <div style="text-align: right; margin: -4px 0 20px;">
        <a
            href="{{ route('password.request') }}"
            style="font-size: 14px; font-weight: 600;"
        >
            Lupa Password?
        </a>
    </div>

    {{-- LOGIN BUTTON --}}
    <button
        type="submit"
        class="btn-primary"
    >
        Masuk ke Akun
    </button>

</form>

{{-- REGISTER LINK --}}
<div class="auth-switch">

    Belum punya akun?

    <a href="{{ route('register') }}">
        Daftar Sekarang
    </a>

</div>

@endsection
