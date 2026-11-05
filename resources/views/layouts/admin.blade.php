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
        @yield('title', 'Dashboard Admin') | Ragen Swim Course
    </title>

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

            --white: #ffffff;

            --text: #0f172a;
            --text-soft: #64748b;
            --text-light: #94a3b8;

            --border: #e2e8f0;

            --success: #15803d;
            --success-bg: #f0fdf4;

            --danger: #dc2626;
            --danger-bg: #fef2f2;

            --warning: #d97706;
            --warning-bg: #fffbeb;

            --sidebar-width: 275px;
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

            background: #f8fafc;
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

        /*
        |--------------------------------------------------------------------------
        | Wrapper
        |--------------------------------------------------------------------------
        */

        .dashboard-wrapper {
            min-height: 100vh;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;

            z-index: 1000;

            width: var(--sidebar-width);
            height: 100vh;

            display: flex;
            flex-direction: column;

            padding: 24px 18px;

            overflow-y: auto;

            background:
                radial-gradient(
                    circle at 20% 10%,
                    rgba(103, 232, 249, 0.16),
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

            color: var(--white);

            text-decoration: none;
        }

        .sidebar-logo-icon {
            width: 43px;
            height: 43px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 13px;

            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar-logo-icon svg {
            width: 25px;
            height: 25px;
        }

        .sidebar-logo-content {
            min-width: 0;
        }

        .sidebar-logo-text {
            display: block;

            font-size: 17px;
            font-weight: 800;

            letter-spacing: -0.3px;
        }

        .sidebar-logo-role {
            display: block;

            margin-top: 2px;

            color: rgba(255, 255, 255, 0.55);

            font-size: 10px;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-label {
            padding: 10px 12px 8px;

            color: rgba(255, 255, 255, 0.5);

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

            color: rgba(255, 255, 255, 0.75);

            text-decoration: none;

            font-size: 14px;
            font-weight: 650;

            transition:
                background 0.2s ease,
                color 0.2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.09);
            color: var(--white);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.14);
            color: var(--white);
        }

        .sidebar-link.disabled {
            cursor: not-allowed;

            color: rgba(255, 255, 255, 0.35);
        }

        .sidebar-link.disabled:hover {
            background: transparent;
            color: rgba(255, 255, 255, 0.35);
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

        .sidebar-coming-soon {
            margin-left: auto;

            padding: 3px 6px;

            border-radius: 999px;

            background: rgba(255, 255, 255, 0.08);

            font-size: 8px;
            font-weight: 800;

            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .sidebar-user {
            padding: 14px;

            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 13px;

            background: rgba(255, 255, 255, 0.07);
        }

        .sidebar-user-role {
            margin-bottom: 4px;

            color: #67e8f9;

            font-size: 9px;
            font-weight: 800;

            text-transform: uppercase;
            letter-spacing: 1px;
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

            color: rgba(255, 255, 255, 0.55);

            font-size: 11px;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /*
        |--------------------------------------------------------------------------
        | Main
        |--------------------------------------------------------------------------
        */

        .main-wrapper {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        /*
        |--------------------------------------------------------------------------
        | Topbar
        |--------------------------------------------------------------------------
        */

        .topbar {
            position: sticky;
            top: 0;

            z-index: 500;

            height: 74px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            padding: 0 32px;

            border-bottom: 1px solid var(--border);

            background: rgba(255, 255, 255, 0.94);

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
        }

        .topbar-profile {
            display: flex;
            align-items: center;

            gap: 10px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

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
            max-width: 190px;
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
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: var(--white);
            color: var(--text-soft);

            transition:
                background 0.2s ease,
                color 0.2s ease,
                border-color 0.2s ease;
        }

        .logout-btn:hover {
            border-color: #fecaca;

            background: var(--danger-bg);
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

        /*
        |--------------------------------------------------------------------------
        | Page
        |--------------------------------------------------------------------------
        */

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

            letter-spacing: -0.7px;
        }

        .page-description {
            max-width: 720px;

            margin-top: 7px;

            color: var(--text-soft);

            font-size: 14px;
            line-height: 1.65;
        }

        /*
        |--------------------------------------------------------------------------
        | Reusable Components
        |--------------------------------------------------------------------------
        */

        .card {
            border: 1px solid var(--border);
            border-radius: 16px;

            background: var(--white);

            box-shadow:
                0 4px 15px rgba(15, 23, 42, 0.035);
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

        .alert-warning {
            border: 1px solid #fde68a;

            background: var(--warning-bg);
            color: var(--warning);
        }

        .btn-primary {
            min-height: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 10px 16px;

            border: 0;
            border-radius: 10px;

            background: var(--blue);
            color: var(--white);

            text-decoration: none;

            font-size: 13px;
            font-weight: 750;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
        }

        .btn-secondary {
            min-height: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 10px 16px;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: var(--white);
            color: var(--navy);

            text-decoration: none;

            font-size: 13px;
            font-weight: 750;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        /*
        |--------------------------------------------------------------------------
        | Overlay
        |--------------------------------------------------------------------------
        */

        .sidebar-overlay {
            position: fixed;
            inset: 0;

            z-index: 900;

            display: none;

            background: rgba(15, 23, 42, 0.45);
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1050px) {

            .sidebar {
                transform: translateX(-100%);

                transition: transform 0.25s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .mobile-menu-button {
                display: flex;
            }

            .topbar {
                padding: 0 22px;
            }

            .page-content {
                padding: 26px 22px 45px;
            }
        }

        @media (max-width: 650px) {

            .topbar {
                height: 68px;

                padding: 0 16px;
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

         /* Ragen branding & consistent sidebar rhythm */
         .ragen-official-logo {display:block;width:100%;height:100%;object-fit:contain;}
         .sidebar-logo-icon {overflow:hidden;background:#fff;border-color:rgba(255,255,255,.3);padding:3px;}
         .sidebar-menu {margin:0;padding:0;}
         .sidebar-menu li {list-style:none;margin:0;padding:0;}
         .sidebar-menu .sidebar-link {width:100%;line-height:1.4;}
         .sidebar-label {margin-top:12px;}
         .sidebar-label + .sidebar-menu {margin-top:4px;}
         .sidebar-menu + .sidebar-menu {margin-top:5px;}

    </style>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/ragen-polish.css') }}">
</head>

<body>

@php
    $currentUser = auth()->user();

    $initials = collect(
        preg_split('/\s+/', trim($currentUser->name))
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


<div class="dashboard-wrapper">

    {{-- SIDEBAR --}}
    <aside
        class="sidebar"
        id="adminSidebar"
    >

        <a
            href="{{ route('admin.dashboard') }}"
            class="sidebar-logo"
        >

            <span class="sidebar-logo-icon">
                <img src="{{ $ragenOfficialLogoUrl }}" alt="Logo Ragen Swim Course" class="ragen-official-logo">
            </span>

            <span class="sidebar-logo-content">
                <span class="sidebar-logo-text">
                    Ragen Swim Course
                </span>

                <span class="sidebar-logo-role">
                    Admin Panel
                </span>
            </span>

        </a>


        <div class="sidebar-label">
            Overview
        </div>

        <ul class="sidebar-menu">

            <li>
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
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

        </ul>


        <ul class="sidebar-menu">
            <li>
                <a class="sidebar-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                    <span class="sidebar-icon" aria-hidden="true">▣</span>
                    <span>Galeri, Sponsor & Logo</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-label">
            Manajemen
        </div>

        <ul class="sidebar-menu">

            <li>
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle
                                cx="9"
                                cy="8"
                                r="3"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M3.5 19C4 15.6 6 14 9 14C12 14 14 15.6 14.5 19"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <path
                                d="M16 8.5C18.3 8.7 19.5 10.2 19.5 12C19.5 13.8 18.3 15.3 16 15.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    Customer & Peserta
                </a>
            </li>


            <li>
                <a
                    href="{{ route('admin.coaches.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
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
                    </span>

                    Coach
                </a>
            </li>


            <li>
                <a
                    href="{{ route('admin.programs.index') }}"
                    class="sidebar-link {{
                        request()->routeIs('admin.programs.*')
                        || request()->routeIs('admin.packages.*')
                            ? 'active'
                            : ''
                    }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M4 12L12 15.5L20 12"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M4 16.5L12 20L20 16.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>

                    Program & Paket
                </a>
            </li>


            <li>
                <a
                    href="{{ route('admin.locations.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M12 21C12 21 18 15.8 18 10C18 6.7 15.3 4 12 4C8.7 4 6 6.7 6 10C6 15.8 12 21 12 21Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <circle
                                cx="12"
                                cy="10"
                                r="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                        </svg>
                    </span>

                    Lokasi
                </a>
            </li>


            <li>
                <a
                    href="{{ route('admin.class-schedules.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.class-schedules.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
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

                    Jadwal Kelas
                </a>
            </li>


            {{-- BOOKING --}}
            <li>
                <a
                    href="{{ route('admin.bookings.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M5 5H19V19H5V5Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M8 12L10.5 14.5L16 9"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>

                    Booking
                </a>
            </li>

        </ul>


        <div class="sidebar-label">
            Transaksi
        </div>

        <ul class="sidebar-menu">

            {{-- ORDER & PEMBAYARAN --}}
            <li>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none">
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
                                d="M3 9H21"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M7 15H10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    Order & Pembayaran
                </a>
            </li>


            {{-- RIWAYAT INVOICE --}}
            <li>
                <a
                    href="{{ route('admin.invoices.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M7 3.5H14L19 8.5V20C19 20.55 18.55 21 18 21H7C5.9 21 5 20.1 5 19V5.5C5 4.4 5.9 3.5 7 3.5Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M14 3.5V8.5H19"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M8.5 12H15.5M8.5 15.5H13"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>

                    Riwayat Invoice
                </a>
            </li>


            {{-- LAPORAN TRANSAKSI --}}
            <li>
                <a
                    href="{{ route('admin.reports.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4 20V12M10 20V8M16 20V4M22 20H2"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>

                    Laporan Transaksi
                </a>
            </li>


            {{-- PENGATURAN PEMBAYARAN --}}
            <li>
                <a
                    href="{{ route('admin.settings.payment') }}"
                    class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="3"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M19.4 15A1.7 1.7 0 0 0 19.74 16.88L19.8 16.94A2 2 0 1 1 16.97 19.77L16.91 19.71A1.7 1.7 0 0 0 15.03 19.37A1.7 1.7 0 0 0 14 20.93V21A2 2 0 1 1 10 21V20.91A1.7 1.7 0 0 0 8.9 19.36A1.7 1.7 0 0 0 7.03 19.7L6.97 19.76A2 2 0 1 1 4.14 16.93L4.2 16.87A1.7 1.7 0 0 0 4.54 14.99A1.7 1.7 0 0 0 3 14H2.93A2 2 0 1 1 2.93 10H3.02A1.7 1.7 0 0 0 4.57 8.9A1.7 1.7 0 0 0 4.23 7.03L4.17 6.97A2 2 0 1 1 7 4.14L7.06 4.2A1.7 1.7 0 0 0 8.94 4.54H9A1.7 1.7 0 0 0 10 3V2.93A2 2 0 1 1 14 2.93V3.02A1.7 1.7 0 0 0 15.1 4.57A1.7 1.7 0 0 0 16.97 4.23L17.03 4.17A2 2 0 1 1 19.86 7L19.8 7.06A1.7 1.7 0 0 0 19.46 8.94V9A1.7 1.7 0 0 0 21 10H21.07A2 2 0 1 1 21.07 14H20.98A1.7 1.7 0 0 0 19.4 15Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>

                    Pengaturan Pembayaran
                </a>
            </li>

        </ul>


        {{-- =====================================================
            KONTEN WEBSITE
        ====================================================== --}}

        <div class="sidebar-label">
            Konten Website
        </div>

        <ul class="sidebar-menu">

            {{-- KELOLA FAQ --}}
            <li>
                <a
                    href="{{ route('admin.faqs.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M9.5 9A2.5 2.5 0 0 1 12 6.5C13.5 6.5 14.5 7.5 14.5 9C14.5 11 12 11.5 12 13.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <circle
                                cx="12"
                                cy="17"
                                r="1"
                                fill="currentColor"
                            />
                        </svg>
                    </span>

                    Kelola FAQ
                </a>
            </li>

        </ul>


                <div class="sidebar-label">
            Akun
        </div>

        <ul class="sidebar-menu">
            <li>
                <a
                    href="{{ route('admin.profile.edit') }}"
                    class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                >
                    <span class="sidebar-icon" aria-hidden="true">
                        &#9881;
                    </span>

                    <span>Pengaturan Akun</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">

            <div class="sidebar-user">

                <div class="sidebar-user-role">
                    Administrator
                </div>

                <div class="sidebar-user-name">
                    {{ $currentUser->name }}
                </div>

                <div class="sidebar-user-email">
                    {{ $currentUser->email }}
                </div>

            </div>

        </div>

    </aside>


    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <div class="main-wrapper">

        <header class="topbar">

            <div class="topbar-heading">

                <button
                    type="button"
                    class="mobile-menu-button"
                    id="mobileMenuButton"
                    aria-label="Buka menu"
                >
                    <svg viewBox="0 0 24 24" fill="none">
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
                        @yield('topbar-title', 'Dashboard Admin')
                    </div>

                    <div class="topbar-subtitle">
                        Ragen Swim Course
                    </div>

                </div>

            </div>


            <div class="topbar-right">

                <div class="topbar-profile">

                    <div class="profile-avatar">
                        {{ $initials ?: 'AD' }}
                    </div>

                    <div class="profile-info">

                        <div class="profile-name">
                            {{ $currentUser->name }}
                        </div>

                        <div class="profile-role">
                            Administrator
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
                        title="Logout"
                        aria-label="Logout"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <path
                                d="M14 8L18 12L14 16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M9 12H18"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>

                </form>

            </div>

        </header>


        <main class="page-content">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif


            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif


            @if ($errors->any())

                <div class="alert alert-danger">

                    @foreach ($errors->all() as $error)
                        <div>
                            {{ $error }}
                        </div>
                    @endforeach

                </div>

            @endif


            @yield('content')

        </main>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const sidebar =
            document.getElementById('adminSidebar');

        const overlay =
            document.getElementById('sidebarOverlay');

        const menuButton =
            document.getElementById('mobileMenuButton');


        function openSidebar() {

            if (!sidebar || !overlay) {
                return;
            }

            sidebar.classList.add('open');
            overlay.classList.add('open');

            document.body.style.overflow = 'hidden';
        }


        function closeSidebar() {

            if (!sidebar || !overlay) {
                return;
            }

            sidebar.classList.remove('open');
            overlay.classList.remove('open');

            document.body.style.overflow = '';
        }


        if (menuButton) {

            menuButton.addEventListener(
                'click',
                function () {

                    if (
                        sidebar
                        &&
                        sidebar.classList.contains('open')
                    ) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }

                }
            );

        }


        if (overlay) {

            overlay.addEventListener(
                'click',
                function () {
                    closeSidebar();
                }
            );

        }


        window.addEventListener(
            'resize',
            function () {

                if (window.innerWidth > 1050) {
                    closeSidebar();
                }

            }
        );

    });
</script>

@stack('scripts')

</body>
</html>
