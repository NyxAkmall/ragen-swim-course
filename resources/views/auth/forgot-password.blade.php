@extends('layouts.auth')

@section('title', 'Lupa Password | Ragen Swim Course')

@section('content')

<div class="auth-heading">
    <h2>Lupa Password?</h2>

    <p>
        Masukkan alamat email yang terdaftar di
        Ragen Swim Course. Kami akan mengirimkan
        tautan untuk membuat password baru.
    </p>
</div>

@if (session('status'))
    <div
        role="status"
        style="padding: 14px; margin-bottom: 20px; border-radius: 8px; background: #e8f7ef; color: #166534;"
    >
        {{ session('status') }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('password.email') }}"
>
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">
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
            required
            autofocus
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

    <button
        type="submit"
        class="btn-primary"
    >
        Kirim Tautan Reset Password
    </button>
</form>

<div class="auth-switch">
    Sudah ingat password?

    <a href="{{ route('login') }}">
        Kembali ke Login
    </a>
</div>

@endsection
