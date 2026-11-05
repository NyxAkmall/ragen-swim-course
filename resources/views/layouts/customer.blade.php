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

    <title>@yield('title', 'Dashboard Customer') | Ragen Swim Course</title>

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

            --success: #15803d;
            --success-bg: #f0fdf4;

            --danger: #dc2626;
            --danger-bg: #fef2f2;

            --sidebar-width: 270px;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui,
                -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
            background: #f8fafc;
            color: var(--text);
        }

        body.sidebar-is-open {
            overflow: hidden;
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

        button:focus-visible,
        a:focus-visible {
            outline: 3px solid #67e8f9;
            outline-offset: 3px;
        }

        .dashboard-wrapper {
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;

            width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;

            display: flex;
            flex-direction: column;

            padding: 24px 18px;
            overflow-y: auto;

            background:
                radial-gradient(
                    circle at 20% 10%,
                    rgba(103, 232, 249, .16),
                    transparent 25%
                ),
                linear-gradient(
                    180deg,
                    #082f49 0%,
                    #075985 100%
                );

            color: var(--white);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 5px 8px 25px;
            text-decoration: none;
        }

        .sidebar-logo-icon {
            width: 43px;
            height: 43px;
            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .sidebar-logo-icon svg {
            width: 25px;
            height: 25px;
        }

        .sidebar-logo-text {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .sidebar-label {
            padding: 10px 12px 8px;
            color: rgba(255, 255, 255, .5);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
            list-style: none;
        }

        .sidebar-menu + .sidebar-label {
            margin-top: 17px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;

            min-height: 46px;
            padding: 11px 13px;

            border-radius: 11px;
            color: rgba(255, 255, 255, .75);
            text-decoration: none;

            font-size: 14px;
            font-weight: 650;

            transition:
                background .2s ease,
                color .2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .09);
            color: var(--white);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, .14);
            color: var(--white);
            box-shadow: inset 3px 0 0 #67e8f9;
        }

        .sidebar-icon {
            width: 21px;
            height: 21px;
            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-icon svg {
            width: 20px;
            height: 20px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .sidebar-user {
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 13px;
            background: rgba(255, 255, 255, .07);
        }

        .sidebar-user-name {
            overflow: hidden;
            color: var(--white);
            font-size: 13px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-user-email {
            margin-top: 3px;
            overflow: hidden;
            color: rgba(255, 255, 255, .55);
            font-size: 11px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* MAIN */

        .main-wrapper {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            min-width: 0;
        }

        /* TOPBAR */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 500;

            min-height: 74px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;

            padding: 12px 32px;

            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
        }

        .topbar-heading {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .topbar-left {
            min-width: 0;
        }

        .topbar-title {
            overflow: hidden;
            color: var(--navy);
            font-size: 18px;
            font-weight: 800;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .topbar-subtitle {
            margin-top: 2px;
            color: var(--text-soft);
            font-size: 12px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;
            background: linear-gradient(
                135deg,
                var(--blue),
                var(--aqua)
            );

            color: var(--white);
            font-size: 14px;
            font-weight: 800;
        }

        .profile-info {
            max-width: 180px;
        }

        .profile-name {
            overflow: hidden;
            color: var(--text);
            font-size: 13px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-role {
            margin-top: 2px;
            color: var(--text-soft);
            font-size: 11px;
        }

        .logout-form {
            display: inline-flex;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 40px;
            height: 40px;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: var(--white);
            color: var(--text-soft);

            transition:
                background .2s ease,
                color .2s ease,
                border-color .2s ease;
        }

        .logout-btn:hover {
            background: var(--danger-bg);
            border-color: #fecaca;
            color: var(--danger);
        }

        .logout-btn svg {
            width: 19px;
            height: 19px;
        }

        .mobile-menu-button {
            display: none;
            width: 42px;
            height: 42px;

            align-items: center;
            justify-content: center;
            flex-shrink: 0;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: var(--white);
            color: var(--navy);
        }

        .mobile-menu-button svg {
            width: 21px;
            height: 21px;
        }

        .sidebar-close-button {
            display: none;
        }

        /* CONTENT */

        .page-content {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 30px 32px 50px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 27px;
        }

        .page-title {
            color: var(--navy);
            font-size: 28px;
            font-weight: 850;
            letter-spacing: -.7px;
        }

        .page-description {
            max-width: 700px;
            margin-top: 7px;
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.65;
        }

        /* REUSABLE COMPONENTS */

        .card {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--white);
            box-shadow: 0 4px 15px rgba(15, 23, 42, .035);
        }

        .alert {
            padding: 14px 16px;
            margin-bottom: 24px;
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

        .alert ul {
            padding-left: 20px;
        }

        .alert li + li {
            margin-top: 4px;
        }

        .btn-primary,
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            min-height: 42px;
            padding: 10px 16px;

            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 750;
        }

        .btn-primary {
            border: 0;
            background: var(--blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--blue-dark);
        }

        .btn-secondary {
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--navy);
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        /* OVERLAY */

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 900;

            display: none;
            background: rgba(15, 23, 42, .45);
        }

        /* RESPONSIVE */

        @media (max-width: 1050px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .sidebar-close-button {
                display: flex;
                align-items: center;
                justify-content: center;

                width: 35px;
                height: 35px;
                flex-shrink: 0;

                margin-left: auto;

                border: 1px solid rgba(255, 255, 255, .2);
                border-radius: 9px;

                background: rgba(255, 255, 255, .1);
                color: white;
                cursor: pointer;
            }

            .sidebar-close-button svg {
                width: 19px;
                height: 19px;
            }

            .sidebar-brand-row {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .sidebar-brand-row .sidebar-logo {
                flex: 1;
                min-width: 0;
            }

            .sidebar-brand-row .sidebar-close-button {
                margin-bottom: 20px;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .mobile-menu-button {
                display: flex;
            }

            .topbar {
                padding: 12px 22px;
            }

            .page-content {
                padding: 26px 22px 45px;
            }
        }

        @media (max-width: 650px) {
            .topbar {
                min-height: 68px;
                padding: 10px 16px;
                gap: 10px;
            }

            .topbar-title {
                font-size: 15px;
            }

            .topbar-subtitle {
                display: none;
            }

            .profile-info {
                display: none;
            }

            .profile-avatar,
            .logout-btn {
                width: 37px;
                height: 37px;
            }

            .topbar-right {
                gap: 8px;
            }

            .page-content {
                padding: 22px 16px 40px;
            }

            .page-header {
                display: block;
            }

            .page-title {
                font-size: 24px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .sidebar,
            .sidebar-link,
            .logout-btn {
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

@php
    $currentUser = auth()->user();

    $initials = collect(
        preg_split('/\s+/', trim($currentUser->name ?? ''))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return mb_strtoupper(mb_substr($word, 0, 1));
        })
        ->implode('');
@endphp

<div class="dashboard-wrapper">

    {{-- SIDEBAR --}}

    <aside
        class="sidebar"
        id="customerSidebar"
        aria-label="Navigasi Customer"
    >

        <div class="sidebar-brand-row">

            <a
                href="{{ route('customer.dashboard') }}"
                class="sidebar-logo"
            >
                <span class="sidebar-logo-icon">
                    <img src="{{ $ragenOfficialLogoUrl }}" alt="Logo Ragen Swim Course" class="ragen-official-logo">
                </span>

                <span class="sidebar-logo-text">
                    Ragen Swim Course
                </span>
            </a>

            <button
                type="button"
                class="sidebar-close-button"
                id="sidebarCloseButton"
                aria-label="Tutup menu"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M5 5L19 19M19 5L5 19"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
            </button>

        </div>

        <div class="sidebar-label">
            Menu Utama
        </div>

        <nav aria-label="Menu utama Customer">
            <ul class="sidebar-menu">

                <li>
                    <a
                        href="{{ route('customer.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.dashboard')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M4 13H10V4H4V13ZM4 20H10V16H4V20ZM14 20H20V11H14V20ZM14 4V8H20V4H14Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.participants.index') }}"
                        class="sidebar-link {{ request()->routeIs('customer.participants.*') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.participants.*')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                                <path
                                    d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Peserta
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.packages.index') }}"
                        class="sidebar-link {{
                            request()->routeIs('customer.packages.*')
                            || request()->routeIs('customer.checkout.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M4 12L12 15.5L20 12M4 16.5L12 20L20 16.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                        Paket Saya
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.bookings.index') }}"
                        class="sidebar-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.bookings.*')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect
                                    x="3.5"
                                    y="5"
                                    width="17"
                                    height="15"
                                    rx="2"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                                <path
                                    d="M7 3V7M17 3V7M3.5 10H20.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Jadwal & Booking
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.progress.index') }}"
                        class="sidebar-link {{ request()->routeIs('customer.progress.*') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.progress.*')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M5 19V13M12 19V9M19 19V5M3 20H21"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Progress
                    </a>
                </li>

            </ul>
        </nav>

        <div class="sidebar-label">
            Akun & Transaksi
        </div>

        <nav aria-label="Menu akun dan transaksi">
            <ul class="sidebar-menu">

                <li>
                    <a
                        href="{{ route('customer.orders.index') }}"
                        class="sidebar-link {{
                            request()->routeIs('customer.orders.*')
                            || request()->routeIs('customer.payments.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                                <path
                                    d="M3 9H21M7 15H10"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Pembayaran
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.invoices.index') }}"
                        class="sidebar-link {{ request()->routeIs('customer.invoices.*') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.invoices.*')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M7 3.5H14L19 8.5V20C19 20.55 18.55 21 18 21H7C5.9 21 5 20.1 5 19V5.5C5 4.4 5.9 3.5 7 3.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M14 3.5V8.5H19M8.5 12H15.5M8.5 15.5H15.5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Riwayat Invoice
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('customer.profile.edit') }}"
                        class="sidebar-link {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}"
                        @if(request()->routeIs('customer.profile.*')) aria-current="page" @endif
                    >
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                                <path
                                    d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </span>
                        Profil Akun
                    </a>
                </li>

            </ul>
        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="sidebar-user-name">
                    {{ $currentUser->name }}
                </div>

                <div class="sidebar-user-email">
                    {{ $currentUser->email }}
                </div>
            </div>
        </div>

    </aside>

    {{-- OVERLAY --}}

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        aria-hidden="true"
    ></div>

    {{-- MAIN AREA --}}

    <div class="main-wrapper">

        <header class="topbar">

            <div class="topbar-heading">

                <button
                    type="button"
                    class="mobile-menu-button"
                    id="mobileMenuButton"
                    aria-label="Buka menu navigasi"
                    aria-controls="customerSidebar"
                    aria-expanded="false"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path
                            d="M4 7H20M4 12H20M4 17H20"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </button>

                <div class="topbar-left">
                    <div class="topbar-title">
                        @yield('topbar-title', 'Dashboard')
                    </div>

                    <div class="topbar-subtitle">
                        Ragen Swim Course
                    </div>
                </div>

            </div>

            <div class="topbar-right">

                <div class="topbar-profile">

                    <div class="profile-avatar">
                        {{ $initials ?: 'RS' }}
                    </div>

                    <div class="profile-info">
                        <div class="profile-name">
                            {{ $currentUser->name }}
                        </div>

                        <div class="profile-role">
                            Customer
                        </div>
                    </div>

                </div>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="logout-btn"
                        title="Keluar dari akun"
                        aria-label="Keluar dari akun"
                    >
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <path
                                d="M14 8L18 12L14 16M9 12H18"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </form>

            </div>

        </header>

        <main class="page-content" id="mainContent">

            @if (session('success'))
                <div class="alert alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const sidebar = document.getElementById('customerSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuButton = document.getElementById('mobileMenuButton');
        const closeButton = document.getElementById('sidebarCloseButton');

        if (!sidebar || !overlay || !menuButton) {
            return;
        }

        let previousFocus = null;

        function isMobile() {
            return window.innerWidth <= 1050;
        }

        function isSidebarOpen() {
            return sidebar.classList.contains('open');
        }

        function openSidebar() {
            if (!isMobile()) {
                return;
            }

            previousFocus = document.activeElement;

            sidebar.classList.add('open');
            overlay.classList.add('open');

            document.body.classList.add('sidebar-is-open');

            menuButton.setAttribute('aria-expanded', 'true');
            menuButton.setAttribute('aria-label', 'Tutup menu navigasi');

            if (closeButton) {
                closeButton.focus();
            }
        }

        function closeSidebar(restoreFocus = true) {
            const wasOpen = isSidebarOpen();

            sidebar.classList.remove('open');
            overlay.classList.remove('open');

            document.body.classList.remove('sidebar-is-open');

            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Buka menu navigasi');

            if (
                wasOpen &&
                restoreFocus &&
                previousFocus &&
                typeof previousFocus.focus === 'function'
            ) {
                previousFocus.focus();
            }
        }

        menuButton.addEventListener('click', function () {
            if (isSidebarOpen()) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                closeSidebar();
            });
        }

        overlay.addEventListener('click', function () {
            closeSidebar();
        });

        document.addEventListener('keydown', function (event) {
            if (!isSidebarOpen()) {
                return;
            }

            if (event.key === 'Escape') {
                closeSidebar();
                return;
            }

            if (event.key === 'Tab') {
                const focusableElements = Array.from(
                    sidebar.querySelectorAll(
                        'a[href], button:not([disabled])'
                    )
                ).filter(function (element) {
                    return element.getClientRects().length > 0;
                });

                if (!focusableElements.length) {
                    return;
                }

                const firstElement = focusableElements[0];
                const lastElement = focusableElements[
                    focusableElements.length - 1
                ];

                if (
                    event.shiftKey &&
                    document.activeElement === firstElement
                ) {
                    event.preventDefault();
                    lastElement.focus();
                } else if (
                    !event.shiftKey &&
                    document.activeElement === lastElement
                ) {
                    event.preventDefault();
                    firstElement.focus();
                }
            }
        });

        window.addEventListener('resize', function () {
            if (!isMobile() && isSidebarOpen()) {
                closeSidebar(false);
            }
        });

        sidebar.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobile()) {
                    closeSidebar(false);
                }
            });
        });

    });
</script>

@stack('scripts')

</body>
</html>
