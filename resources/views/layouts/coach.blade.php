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
        @yield('title', 'Coach Portal') | Ragen Swim Course
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
            --muted: #64748b;

            --border: #e2e8f0;

            --success: #15803d;
            --success-bg: #f0fdf4;
            --success-soft: #f0fdf4;

            --warning: #b45309;
            --warning-soft: #fffbeb;

            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-soft: #fef2f2;

            --ocean: #0284c7;
            --ocean-dark: #0369a1;
            --blue-soft: #f0f9ff;
            --background: #f8fafc;

            --sidebar-width: 270px;

            --shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background: var(--background);
            color: var(--text);
        }

        body.sidebar-locked {
            overflow: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
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

            display: flex;
            flex-direction: column;

            width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;

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

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;

            padding: 5px 8px 25px;
            color: var(--white);
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 43px;
            height: 43px;
            flex-shrink: 0;

            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 13px;

            background: rgba(255, 255, 255, 0.12);
        }

        .brand-icon svg {
            width: 25px;
            height: 25px;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.3px;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #a5f3fc;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
        }

        .sidebar-label {
            padding: 10px 12px 8px;

            color: rgba(255, 255, 255, 0.55);

            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;

            width: 100%;
            min-height: 46px;
            padding: 11px 13px;

            border: 0;
            border-radius: 11px;

            background: transparent;
            color: rgba(255, 255, 255, 0.75);

            font-size: 13px;
            font-weight: 650;
            text-align: left;

            transition:
                background 0.2s ease,
                color 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.09);
            color: var(--white);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.14);
            color: var(--white);
            box-shadow: inset 3px 0 0 #67e8f9;
        }

        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 21px;
            height: 21px;
            flex-shrink: 0;
        }

        .nav-icon svg {
            width: 20px;
            height: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar User
        |--------------------------------------------------------------------------
        */

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .coach-mini {
            display: flex;
            align-items: center;
            gap: 11px;

            min-width: 0;
            padding: 13px;
            margin-bottom: 10px;

            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 13px;

            background: rgba(255, 255, 255, 0.07);
        }

        .coach-avatar {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 40px;
            height: 40px;
            flex-shrink: 0;

            border-radius: 12px;

            background: rgba(103, 232, 249, 0.18);
            color: #a5f3fc;

            font-size: 14px;
            font-weight: 800;
        }

        .coach-mini-info {
            min-width: 0;
        }

        .coach-mini-name {
            overflow: hidden;

            color: var(--white);

            font-size: 12px;
            font-weight: 750;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .coach-mini-role {
            margin-top: 4px;

            color: rgba(255, 255, 255, 0.6);

            font-size: 10px;
        }

        .logout-button {
            color: rgba(255, 255, 255, 0.8);
        }

        .logout-button:hover {
            background: rgba(254, 226, 226, 0.12);
            color: #fecaca;
        }

        /*
        |--------------------------------------------------------------------------
        | Main
        |--------------------------------------------------------------------------
        */

        .main {
            min-width: 0;
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

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;

            min-height: 74px;
            padding: 12px 32px;

            border-bottom: 1px solid var(--border);

            background: rgba(255, 255, 255, 0.94);

            backdrop-filter: blur(12px);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .topbar-heading {
            min-width: 0;
        }

        .mobile-menu {
            display: none;
            align-items: center;
            justify-content: center;

            width: 42px;
            height: 42px;
            flex-shrink: 0;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: var(--white);
            color: var(--navy);
        }

        .mobile-menu svg {
            width: 21px;
            height: 21px;
        }

        .topbar-title {
            color: var(--navy);

            font-size: 18px;
            font-weight: 800;
        }

        .topbar-subtitle {
            margin-top: 3px;

            color: var(--text-soft);

            font-size: 11px;
            line-height: 1.5;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 11px;
            flex-shrink: 0;
        }

        .topbar-avatar {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 40px;
            height: 40px;
            flex-shrink: 0;

            border-radius: 12px;

            background: var(--aqua-light);
            color: var(--blue-dark);

            font-size: 13px;
            font-weight: 800;
        }

        .topbar-user-info {
            max-width: 180px;
        }

        .topbar-user-name {
            overflow: hidden;

            color: var(--navy);

            font-size: 12px;
            font-weight: 750;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .topbar-user-role {
            margin-top: 3px;

            color: var(--text-soft);

            font-size: 10px;
        }

        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        .content {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 30px 32px 50px;
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
            box-shadow: var(--shadow);
        }

        .alert {
            margin-bottom: 22px;
            padding: 14px 16px;

            border-radius: 12px;

            font-size: 13px;
            line-height: 1.6;
        }

        .alert-success {
            border: 1px solid #bbf7d0;
            background: var(--success-bg);
            color: var(--success);
        }

        .alert-error,
        .alert-danger {
            border: 1px solid #fecaca;
            background: var(--danger-bg);
            color: var(--danger);
        }

        .alert ul {
            padding-left: 20px;
        }

        .alert li + li {
            margin-top: 5px;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile Sidebar
        |--------------------------------------------------------------------------
        */

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 900;

            display: none;

            background: rgba(15, 23, 42, 0.5);
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

            .sidebar-overlay.show {
                display: block;
            }

            .main {
                margin-left: 0;
            }

            .mobile-menu {
                display: flex;
            }

            .topbar {
                padding: 12px 22px;
            }

            .content {
                padding: 26px 22px 45px;
            }
        }

        @media (max-width: 650px) {
            .topbar {
                min-height: 68px;
                padding: 10px 16px;
            }

            .topbar-title {
                font-size: 15px;
            }

            .topbar-subtitle {
                display: none;
            }

            .topbar-user-info {
                display: none;
            }

            .topbar-avatar {
                width: 37px;
                height: 37px;
            }

            .content {
                padding: 22px 16px 40px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
            }
        }

        /* Official Ragen Swim Course identity */
        .coach-avatar img, .topbar-avatar img {width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;}
        .ragen-official-logo {display:block;width:100%;height:100%;object-fit:contain;}
        .brand-icon,.brand-logo-icon,.mobile-logo-icon,.sidebar-logo-icon {overflow:hidden;background:#fff;padding:3px;}

    </style>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/ragen-polish.css') }}">
</head>

<body>

@php
    $coachUser = auth()->user();
    $coachProfilePhoto = \App\Models\Coach::where('user_id', $coachUser->id)->value('photo');

    $coachInitials = collect(
        preg_split('/\s+/', trim($coachUser->name ?? ''))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return mb_strtoupper(mb_substr($word, 0, 1));
        })
        ->implode('');
@endphp

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    aria-hidden="true"
></div>

<aside
    class="sidebar"
    id="coachSidebar"
    aria-label="Navigasi Coach"
>

    {{-- BRAND --}}
    <a
        href="{{ route('coach.dashboard') }}"
        class="brand"
    >
        <span class="brand-icon">
            <img src="{{ $ragenOfficialLogoUrl }}" alt="Logo Ragen Swim Course" class="ragen-official-logo">
        </span>

        <span>
            <span class="brand-name" style="display:block;">
                Ragen Swim Course
            </span>

            <span class="brand-subtitle" style="display:block;">
                COACH PORTAL
            </span>
        </span>
    </a>

    {{-- MENU UTAMA --}}
    <div class="sidebar-label">
        Menu Utama
    </div>

    <nav class="nav" aria-label="Menu utama Coach">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('coach.dashboard') }}"
            class="nav-link {{ request()->routeIs('coach.dashboard') ? 'active' : '' }}"
            @if (request()->routeIs('coach.dashboard'))
                aria-current="page"
            @endif
        >
            <span class="nav-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <rect x="3" y="3" width="7" height="7" rx="2" />
                    <rect x="14" y="3" width="7" height="7" rx="2" />
                    <rect x="3" y="14" width="7" height="7" rx="2" />
                    <rect x="14" y="14" width="7" height="7" rx="2" />
                </svg>
            </span>

            Dashboard
        </a>

        {{-- JADWAL --}}
        <a
            href="{{ route('coach.schedules.index') }}"
            class="nav-link {{ request()->routeIs('coach.schedules.*') ? 'active' : '' }}"
            @if (request()->routeIs('coach.schedules.*'))
                aria-current="page"
            @endif
        >
            <span class="nav-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
            </span>

            Jadwal Saya
        </a>

        {{-- KEHADIRAN --}}
        <a
            href="{{ route('coach.attendance.index') }}"
            class="nav-link {{ request()->routeIs('coach.attendance.*') ? 'active' : '' }}"
            @if (request()->routeIs('coach.attendance.*'))
                aria-current="page"
            @endif
        >
            <span class="nav-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M9 11l3 3L22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
            </span>

            Kehadiran
        </a>

        {{-- PERKEMBANGAN --}}
        <a
            href="{{ route('coach.progress.index') }}"
            class="nav-link {{ request()->routeIs('coach.progress.*') ? 'active' : '' }}"
            @if (request()->routeIs('coach.progress.*'))
                aria-current="page"
            @endif
        >
            <span class="nav-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M4 19V9M10 19V5M16 19v-7M22 19V3" />
                </svg>
            </span>

            Perkembangan
        </a>

        <a href="{{ route('coach.profile.edit') }}" class="nav-link {{ request()->routeIs('coach.profile.*') ? 'active' : '' }}" @if(request()->routeIs('coach.profile.*')) aria-current="page" @endif>
            <span class="nav-icon" aria-hidden="true">👤</span>
            Foto Profil
        </a>
    </nav>

    {{-- AKUN COACH --}}
    <div class="sidebar-bottom">

        <div class="coach-mini">
            <div class="coach-avatar">
                @if ($coachProfilePhoto)
                    <img src="{{ asset('storage/' . $coachProfilePhoto) }}" alt="Foto profil coach">
                @else
                    {{ $coachInitials ?: 'RS' }}
                @endif
            </div>

            <div class="coach-mini-info">
                <div class="coach-mini-name">
                    {{ $coachUser->name }}
                </div>

                <div class="coach-mini-role">
                    Pelatih
                </div>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="nav-link logout-button"
            >
                <span class="nav-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path d="M10 17l5-5-5-5" />
                        <path d="M15 12H3" />
                        <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5" />
                    </svg>
                </span>

                Keluar
            </button>
        </form>

    </div>

</aside>

{{-- MAIN CONTENT --}}
<main class="main">

    <header class="topbar">

        <div class="topbar-left">

            <button
                class="mobile-menu"
                id="mobileMenuButton"
                type="button"
                aria-label="Buka menu navigasi"
                aria-controls="coachSidebar"
                aria-expanded="false"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="topbar-heading">
                <div class="topbar-title">
                    @yield('topbar-title', 'Coach Portal')
                </div>

                <div class="topbar-subtitle">
                    @yield(
                        'topbar-subtitle',
                        'Kelola jadwal, kehadiran, dan perkembangan peserta.'
                    )
                </div>
            </div>

        </div>

        <div class="topbar-user">

            <div class="topbar-avatar">
                @if ($coachProfilePhoto)
                    <img src="{{ asset('storage/' . $coachProfilePhoto) }}" alt="Foto profil coach">
                @else
                    {{ $coachInitials ?: 'RS' }}
                @endif
            </div>

            <div class="topbar-user-info">
                <div class="topbar-user-name">
                    {{ $coachUser->name }}
                </div>

                <div class="topbar-user-role">
                    Coach
                </div>
            </div>

        </div>

    </header>

    <div class="content">

        {{-- PESAN SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- PESAN ERROR --}}
        @if (session('error'))
            <div class="alert alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- VALIDASI --}}
        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- KONTEN HALAMAN --}}
        @yield('content')

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const sidebar = document.getElementById('coachSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuButton = document.getElementById('mobileMenuButton');

        if (!sidebar || !overlay || !menuButton) {
            return;
        }

        const mobileBreakpoint = 1050;

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');

            document.body.classList.add('sidebar-locked');

            menuButton.setAttribute('aria-expanded', 'true');
            menuButton.setAttribute('aria-label', 'Tutup menu navigasi');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');

            document.body.classList.remove('sidebar-locked');

            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Buka menu navigasi');
        }

        menuButton.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (event) {
            if (
                event.key === 'Escape' &&
                sidebar.classList.contains('open')
            ) {
                closeSidebar();
                menuButton.focus();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > mobileBreakpoint) {
                closeSidebar();
            }
        });

        sidebar.querySelectorAll('a.nav-link, a.brand').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= mobileBreakpoint) {
                    closeSidebar();
                }
            });
        });

    });
</script>

@stack('scripts')

</body>
</html>
