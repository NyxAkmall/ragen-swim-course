@php
    $ragenLogoPath = \App\Models\Setting::getValue('website_logo', 'branding/ragen-logo.png');
    $ragenOfficialLogoUrl = $ragenLogoPath && $ragenLogoPath !== 'branding/ragen-logo.png'
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($ragenLogoPath)
        : asset('branding/ragen-logo.png');
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    @include('components.favicon')
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Ragen Swim Course')
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy: #082f49;
            --navy-dark: #06243a;

            --blue: #0284c7;
            --blue-dark: #0369a1;

            --aqua: #06b6d4;
            --aqua-light: #cffafe;

            --sky-light: #f0f9ff;
            --white: #ffffff;

            --text: #0f172a;
            --text-soft: #64748b;
            --text-light: #94a3b8;

            --border: #e2e8f0;

            --danger: #dc2626;
            --danger-bg: #fef2f2;

            --success: #15803d;
            --success-bg: #f0fdf4;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;

            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background: var(--sky-light);
            color: var(--text);
        }

        a {
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        a:focus-visible,
        button:focus-visible {
            outline: 3px solid var(--aqua);
            outline-offset: 3px;
        }

        /*
        |--------------------------------------------------------------------------
        | Authentication Layout
        |--------------------------------------------------------------------------
        */

        .auth-page {
            display: grid;
            grid-template-columns:
                minmax(0, 1.05fr)
                minmax(0, 0.95fr);

            min-height: 100vh;
        }

        /*
        |--------------------------------------------------------------------------
        | Brand Section
        |--------------------------------------------------------------------------
        */

        .auth-brand {
            position: relative;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            min-height: 100vh;
            padding: 48px 56px;

            overflow: hidden;

            background:
                radial-gradient(
                    circle at 15% 15%,
                    rgba(103, 232, 249, 0.28),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 90% 75%,
                    rgba(14, 165, 233, 0.28),
                    transparent 32%
                ),
                linear-gradient(
                    145deg,
                    #082f49 0%,
                    #075985 48%,
                    #0284c7 100%
                );

            color: var(--white);
        }

        .auth-brand::before {
            content: "";

            position: absolute;
            top: -130px;
            right: -170px;

            width: 420px;
            height: 420px;

            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 50%;

            pointer-events: none;
        }

        .auth-brand::after {
            content: "";

            position: absolute;
            bottom: -140px;
            left: -150px;

            width: 320px;
            height: 320px;

            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;

            pointer-events: none;
        }

        .brand-logo,
        .brand-content,
        .brand-footer {
            position: relative;
            z-index: 2;
        }

        /*
        |--------------------------------------------------------------------------
        | Brand Logo
        |--------------------------------------------------------------------------
        */

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;

            width: fit-content;

            color: var(--white);
            text-decoration: none;
        }

        .brand-logo-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 48px;
            height: 48px;
            flex-shrink: 0;

            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;

            background: rgba(255, 255, 255, 0.14);

            backdrop-filter: blur(10px);
        }

        .brand-logo-icon svg {
            width: 28px;
            height: 28px;
        }

        .brand-logo-text {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        /*
        |--------------------------------------------------------------------------
        | Brand Content
        |--------------------------------------------------------------------------
        */

        .brand-content {
            max-width: 590px;
            padding: 70px 0;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            margin-bottom: 22px;
            padding: 8px 13px;

            border: 1px solid rgba(255, 255, 255, 0.17);
            border-radius: 999px;

            background: rgba(255, 255, 255, 0.12);

            font-size: 13px;
            font-weight: 700;
        }

        .brand-badge-dot {
            width: 7px;
            height: 7px;
            flex-shrink: 0;

            border-radius: 50%;
            background: #67e8f9;
        }

        .brand-content h1 {
            max-width: 560px;
            margin-bottom: 20px;

            font-size: clamp(36px, 4.5vw, 64px);
            line-height: 1.08;
            letter-spacing: -2px;
        }

        .brand-content p {
            max-width: 520px;

            color: rgba(255, 255, 255, 0.82);

            font-size: 16px;
            line-height: 1.85;
        }

        /*
        |--------------------------------------------------------------------------
        | Brand Features
        |--------------------------------------------------------------------------
        */

        .brand-features {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;

            margin-top: 32px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 8px;

            padding: 10px 13px;

            border-radius: 12px;

            background: rgba(255, 255, 255, 0.1);

            font-size: 13px;
            font-weight: 650;
        }

        .feature-check {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 20px;
            height: 20px;
            flex-shrink: 0;

            border-radius: 50%;

            background: rgba(103, 232, 249, 0.2);
            color: #a5f3fc;

            font-size: 12px;
            font-weight: 800;
        }

        .brand-footer {
            color: rgba(255, 255, 255, 0.65);

            font-size: 12px;
            line-height: 1.6;
        }

        /*
        |--------------------------------------------------------------------------
        | Authentication Content
        |--------------------------------------------------------------------------
        */

        .auth-content {
            display: flex;
            align-items: center;
            justify-content: center;

            min-width: 0;
            min-height: 100vh;

            padding: 50px;

            background: var(--white);
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile Logo
        |--------------------------------------------------------------------------
        */

        .mobile-logo {
            display: none;
            align-items: center;
            gap: 11px;

            margin-bottom: 38px;

            color: var(--navy);
            text-decoration: none;
        }

        .mobile-logo-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 43px;
            height: 43px;
            flex-shrink: 0;

            border-radius: 13px;

            background: linear-gradient(
                135deg,
                var(--navy),
                var(--blue)
            );

            color: var(--white);
        }

        .mobile-logo-icon svg {
            width: 25px;
            height: 25px;
        }

        .mobile-logo-text {
            font-size: 18px;
            font-weight: 800;
        }

        /*
        |--------------------------------------------------------------------------
        | Form Heading
        |--------------------------------------------------------------------------
        */

        .auth-heading {
            margin-bottom: 30px;
        }

        .auth-heading h2 {
            margin-bottom: 10px;

            color: var(--navy);

            font-size: 32px;
            line-height: 1.2;
            letter-spacing: -0.8px;
        }

        .auth-heading p {
            color: var(--text-soft);

            font-size: 15px;
            line-height: 1.7;
        }

        /*
        |--------------------------------------------------------------------------
        | Alerts
        |--------------------------------------------------------------------------
        */

        .alert {
            padding: 13px 15px;
            margin-bottom: 22px;

            border-radius: 12px;

            font-size: 14px;
            line-height: 1.55;
        }

        .alert-success {
            border: 1px solid #bbf7d0;

            background: var(--success-bg);
            color: var(--success);
        }

        .alert-danger {
            border: 1px solid #fecaca;

            background: var(--danger-bg);
            color: var(--danger);
        }

        .alert-danger ul {
            padding-left: 20px;
        }

        .alert-danger li + li {
            margin-top: 4px;
        }

        /*
        |--------------------------------------------------------------------------
        | Form Components
        |--------------------------------------------------------------------------
        */

        .form-group {
            margin-bottom: 19px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;

            color: var(--text);

            font-size: 14px;
            font-weight: 700;
        }

        .form-input {
            width: 100%;
            min-height: 50px;

            padding: 12px 15px;

            border: 1px solid #cbd5e1;
            border-radius: 12px;
            outline: none;

            background: var(--white);
            color: var(--text);

            font-size: 15px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .form-input::placeholder {
            color: var(--text-light);
        }

        .form-input:focus {
            border-color: var(--blue);

            box-shadow:
                0 0 0 4px rgba(2, 132, 199, 0.1);
        }

        .form-input.is-invalid,
        .form-input[aria-invalid="true"] {
            border-color: #ef4444;
        }

        .form-input.is-invalid:focus,
        .form-input[aria-invalid="true"]:focus {
            box-shadow:
                0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-error {
            margin-top: 7px;

            color: var(--danger);

            font-size: 13px;
            line-height: 1.5;
        }

        /*
        |--------------------------------------------------------------------------
        | Password Toggle
        |--------------------------------------------------------------------------
        */

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-input {
            padding-right: 54px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;

            transform: translateY(-50%);

            display: flex;
            align-items: center;
            justify-content: center;

            width: 36px;
            height: 36px;

            border: 0;
            border-radius: 9px;

            background: transparent;
            color: var(--text-soft);
        }

        .password-toggle:hover {
            background: #f1f5f9;
            color: var(--navy);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            width: 100%;
            min-height: 52px;

            padding: 12px 18px;

            border: 0;
            border-radius: 12px;

            background: linear-gradient(
                135deg,
                var(--blue),
                var(--blue-dark)
            );

            color: var(--white);

            font-size: 15px;
            font-weight: 800;
            text-align: center;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);

            box-shadow:
                0 10px 25px rgba(2, 132, 199, 0.22);
        }

        .btn-primary:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
            box-shadow: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Authentication Links
        |--------------------------------------------------------------------------
        */

        .auth-switch {
            margin-top: 25px;

            color: var(--text-soft);

            font-size: 14px;
            line-height: 1.6;
            text-align: center;
        }

        .auth-switch a {
            color: var(--blue-dark);

            font-weight: 800;
            text-decoration: none;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        .auth-back {
            display: flex;
            justify-content: center;

            margin-top: 22px;
        }

        .auth-back a {
            color: var(--text-soft);

            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
        }

        .auth-back a:hover {
            color: var(--blue-dark);
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {
            .auth-brand {
                padding: 40px 35px;
            }

            .auth-content {
                padding: 40px 32px;
            }

            .brand-content h1 {
                font-size: 42px;
            }
        }

        @media (max-width: 950px) {
            .auth-page {
                display: block;
            }

            .auth-brand {
                display: none;
            }

            .auth-content {
                min-height: 100vh;
                padding: 40px 24px;
            }

            .mobile-logo {
                display: inline-flex;
            }
        }

        @media (max-width: 520px) {
            .auth-content {
                align-items: flex-start;
                padding: 30px 20px 45px;
            }

            .mobile-logo {
                margin-bottom: 34px;
            }

            .auth-heading {
                margin-bottom: 25px;
            }

            .auth-heading h2 {
                font-size: 28px;
            }

            .form-input {
                min-height: 49px;
            }

            .brand-logo-text,
            .mobile-logo-text {
                font-size: 17px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .form-input,
            .btn-primary {
                transition: none;
            }
        }

        /* Official Ragen Swim Course identity */
        .ragen-official-logo {display:block;width:100%;height:100%;object-fit:contain;}
        .brand-icon,.brand-logo-icon,.mobile-logo-icon,.sidebar-logo-icon {overflow:hidden;background:#fff;padding:3px;}

    </style>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/ragen-polish.css') }}">
</head>

<body>

<div class="auth-page">

    {{-- =====================================================
        BRAND SECTION
    ====================================================== --}}

    <section
        class="auth-brand"
        aria-label="Tentang Ragen Swim Course"
    >

        <a
            href="{{ route('home') }}"
            class="brand-logo"
        >

            <span class="brand-logo-icon">

                <img src="{{ $ragenOfficialLogoUrl }}" alt="Logo Ragen Swim Course" class="ragen-official-logo">

            </span>

            <span class="brand-logo-text">
                Ragen Swim Course
            </span>

        </a>

        <div class="brand-content">

            <div class="brand-badge">
                <span class="brand-badge-dot"></span>
                Belajar • Bertumbuh • Percaya Diri
            </div>

            <h1>
                Setiap anak bisa berkembang di dalam air.
            </h1>

            <p>
                Program belajar berenang yang terarah,
                aman, dan menyenangkan dengan pendampingan
                pelatih yang membantu setiap peserta
                berkembang sesuai kemampuannya.
            </p>

            <div class="brand-features">

                <div class="brand-feature">
                    <span class="feature-check" aria-hidden="true">
                        ✓
                    </span>

                    Pelatih Berpengalaman
                </div>

                <div class="brand-feature">
                    <span class="feature-check" aria-hidden="true">
                        ✓
                    </span>

                    Progress Terpantau
                </div>

                <div class="brand-feature">
                    <span class="feature-check" aria-hidden="true">
                        ✓
                    </span>

                    Jadwal Fleksibel
                </div>

            </div>

        </div>

        <div class="brand-footer">
            © {{ date('Y') }} Ragen Swim Course.
            All rights reserved.
        </div>

    </section>

    {{-- =====================================================
        AUTHENTICATION CONTENT
    ====================================================== --}}

    <main class="auth-content">

        <div class="auth-container">

            {{-- MOBILE LOGO --}}

            <a
                href="{{ route('home') }}"
                class="mobile-logo"
            >

                <span class="mobile-logo-icon">

                    <img src="{{ $ragenOfficialLogoUrl }}" alt="Logo Ragen Swim Course" class="ragen-official-logo">

                </span>

                <span class="mobile-logo-text">
                    Ragen Swim Course
                </span>

            </a>

            {{-- SUCCESS MESSAGE --}}

            @if (session('success'))

                <div
                    class="alert alert-success"
                    role="status"
                >
                    {{ session('success') }}
                </div>

            @endif

            {{-- ERROR MESSAGE --}}

            @if (session('error'))

                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    {{ session('error') }}
                </div>

            @endif

            {{-- VALIDATION ERRORS --}}

            @if ($errors->any())

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            {{-- LOGIN / REGISTER CONTENT --}}

            @yield('content')

            {{-- BACK TO HOME --}}

            <div class="auth-back">

                <a href="{{ route('home') }}">
                    ← Kembali ke Beranda
                </a>

            </div>

        </div>

    </main>

</div>

{{-- =========================================================
    PASSWORD VISIBILITY
========================================================== --}}

<script>
    document.addEventListener('click', function (event) {

        const button = event.target.closest(
            '[data-password-toggle]'
        );

        if (!button) {
            return;
        }

        const targetId = button.getAttribute(
            'data-password-toggle'
        );

        const input = document.getElementById(targetId);

        if (!input) {
            return;
        }

        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        button.setAttribute(
            'aria-label',
            isPassword
                ? 'Sembunyikan password'
                : 'Tampilkan password'
        );

        button.setAttribute(
            'aria-pressed',
            isPassword ? 'true' : 'false'
        );

    });
</script>

@stack('scripts')

</body>
</html>
