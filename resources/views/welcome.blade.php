<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('components.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ragen Swim Course menyediakan program kursus renang yang aman, terarah, dan menyenangkan untuk berbagai tingkat kemampuan.">
    <meta name="theme-color" content="#073763">
    <title>Ragen Swim Course | Learn • Swim • Grow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    @php
        $logoPath = $websiteLogo ?: 'branding/ragen-logo.png';
        $logoUrl = $logoPath === 'branding/ragen-logo.png'
            ? asset('branding/ragen-logo.png')
            : asset('storage/' . ltrim($logoPath, '/'));

        $userRole = auth()->user()?->role;
        $dashboardRoute = match ($userRole) {
            'admin' => 'admin.dashboard',
            'coach' => 'coach.dashboard',
            default => 'customer.dashboard',
        };
    @endphp

    <style>
        :root {
            --navy: #073763;
            --navy-dark: #052a4b;
            --ocean: #0b82d4;
            --cyan: #22b8cf;
            --white: #ffffff;
            --background: #f5fbfe;
            --aqua: #e9f9fc;
            --text: #102a43;
            --muted: #64748b;
            --border: #dcecf3;
            --shadow: 0 18px 50px rgba(7, 55, 99, .09);
            --radius: 22px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
        }

        body.menu-open {
            overflow: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
            cursor: pointer;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: min(1180px, calc(100% - 40px));
            margin: auto;
        }

        section {
            scroll-margin-top: 90px;
        }

        .section {
            padding: 90px 0;
        }

        .section-soft {
            background: var(--background);
        }

        .section-heading {
            max-width: 740px;
            text-align: center;
            margin: 0 auto 44px;
        }

        .section-label {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--ocean);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            font-family: 'Manrope', sans-serif;
            line-height: 1.3;
        }

        .section-heading h2,
        .why-content h2 {
            color: var(--navy-dark);
            font-size: clamp(28px, 3.6vw, 42px);
            letter-spacing: -1px;
        }

        .section-heading p {
            margin-top: 13px;
            color: var(--muted);
            font-size: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 45px;
            padding: 11px 20px;
            border-radius: 12px;
            border: 1px solid transparent;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            transition: transform .2s, box-shadow .2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            color: white;
            background: linear-gradient(135deg, var(--navy), var(--ocean));
            box-shadow: 0 8px 22px rgba(11, 130, 212, .18);
        }

        .btn-outline {
            background: white;
            color: var(--navy);
            border-color: var(--border);
        }

        .btn-white {
            color: var(--navy);
            background: white;
        }

        .btn-glass {
            color: white;
            background: rgba(255,255,255,.10);
            border-color: rgba(255,255,255,.3);
        }

        /* NAVBAR */

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border);
        }

        .navbar.scrolled {
            box-shadow: 0 8px 30px rgba(7,55,99,.08);
        }

        .nav-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 52px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--border);
            overflow: hidden;
            padding: 5px;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-name {
            color: var(--navy);
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            font-weight: 800;
        }

        .brand-subtitle {
            margin-top: 5px;
            color: var(--ocean);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-link {
            color: #42566a;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            transition: color .2s;
        }

        .nav-link:hover {
            color: var(--ocean);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .nav-actions .btn {
            padding-left: 15px;
            padding-right: 15px;
            white-space: nowrap;
        }

        .menu-button {
            display: none;
            width: 43px;
            height: 43px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: white;
            color: var(--navy);
            font-size: 22px;
        }

        .mobile-menu {
            display: none;
        }

        /* HERO */

        .hero {
            padding: 160px 0 95px;
            background:
                radial-gradient(circle at 90% 20%, rgba(34,184,207,.16), transparent 32%),
                linear-gradient(180deg, white, #f3fbff);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            align-items: center;
            gap: 60px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 14px;
            border-radius: 30px;
            background: var(--aqua);
            color: #087aa5;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 22px;
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cyan);
        }

        .hero h1 {
            color: var(--navy-dark);
            font-size: clamp(39px, 5vw, 65px);
            line-height: 1.12;
            letter-spacing: -2px;
        }

        .hero h1 span {
            color: var(--ocean);
        }

        .hero-description {
            max-width: 620px;
            margin-top: 22px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.9;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .hero-actions .btn {
            min-height: 52px;
            padding: 12px 25px;
        }

        .hero-notes {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 25px;
        }

        .hero-note {
            color: #52677b;
            font-size: 12px;
            font-weight: 700;
        }

        .hero-note span {
            color: #16a085;
            margin-right: 5px;
        }

        .hero-visual {
            position: relative;
            min-height: 460px;
            isolation: isolate;
        }

        .pool-card {
            position: absolute;
            inset: 25px 10px 20px 25px;
            overflow: hidden;
            border-radius: 35px;
            background: radial-gradient(circle at 75% 20%, rgba(255,255,255,.28), transparent 35%), linear-gradient(145deg, #29c2dc, #0b82d4 55%, #075596);
            box-shadow: 0 35px 75px rgba(7,79,132,.2);
        }

        .pool-card::before,
        .pool-card::after {
            content: "";
            position: absolute;
            width: 130%;
            height: 100px;
            left: -15%;
            border-top: 3px solid rgba(255,255,255,.25);
            border-radius: 50%;
        }

        .pool-card::before {
            top: 110px;
            transform: rotate(-7deg);
        }

        .pool-card::after {
            top: 260px;
            transform: rotate(7deg);
        }

        .swimmer-symbol {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 145px;
            height: 145px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 50%;
            background: transparent;
            color: white;
        }

        .swimmer-symbol .swimmer-logo {
            display: block;
            width: 135px;
            height: 135px;
            max-width: 100%;
            object-fit: contain;
        }

        .swimmer-symbol svg {
            width: 85px;
            height: 85px;
        }

        .floating-card {
            position: absolute;
            z-index: 2;
            width: 205px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(255,255,255,.97);
            box-shadow: var(--shadow);
        }

        .floating-card.top {
            top: 0;
            right: 0;
        }

        .floating-card.bottom {
            bottom: 0;
            left: 0;
        }

        .floating-card strong {
            display: block;
            color: var(--navy);
            font-size: 13px;
        }

        .floating-card p {
            margin-top: 6px;
            color: var(--muted);
            font-size: 11px;
        }

        .float-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin-bottom: 12px;
            border-radius: 12px;
            background: var(--aqua);
            color: var(--ocean);
            font-size: 20px;
        }

        /* CARDS */

        .program-grid,
        .package-grid,
        .coach-grid,
        .location-grid {
            display: grid;
            justify-content: center;
            gap: 20px;
        }

        .program-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 290px));
        }

        .package-grid {
            grid-template-columns: repeat(auto-fit, minmax(290px, 375px));
        }

        .coach-grid,
        .location-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 360px));
        }

        .program-card,
        .package-card,
        .coach-card,
        .location-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
        }

        .program-card:hover,
        .package-card:hover,
        .coach-card:hover,
        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .program-card {
            padding: 27px;
        }

        .program-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 19px;
            border-radius: 15px;
            background: var(--aqua);
            color: var(--ocean);
        }

        .program-icon svg {
            width: 28px;
            height: 28px;
        }

        .program-card h3,
        .package-card h3 {
            color: var(--navy);
            font-size: 18px;
        }

        .program-card p,
        .package-description {
            margin-top: 10px;
            color: var(--muted);
            font-size: 13px;
        }

        .program-link {
            display: inline-block;
            margin-top: 18px;
            color: var(--ocean);
            font-size: 12px;
            font-weight: 800;
        }

        .package-card {
            display: flex;
            flex-direction: column;
            padding: 27px;
        }

        .package-program,
        .coach-specialization,
        .location-city {
            display: inline-block;
            align-self: flex-start;
            margin-bottom: 16px;
            padding: 7px 12px;
            border-radius: 30px;
            background: var(--aqua);
            color: var(--ocean);
            font-size: 11px;
            font-weight: 800;
        }

        .package-price {
            margin-top: 20px;
            color: var(--navy-dark);
            font-family: 'Manrope', sans-serif;
            font-size: 26px;
            font-weight: 800;
        }

        .package-meta {
            display: grid;
            gap: 12px;
            margin: 22px 0;
            padding: 18px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .package-meta-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 12px;
        }

        .package-meta-item strong {
            color: var(--navy);
            text-align: right;
        }

        .package-card .btn {
            margin-top: auto;
            width: 100%;
        }

        .coach-photo,
        .location-photo {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 220px;
            background: linear-gradient(135deg, #e9f9fc, #d7f1f7);
            color: var(--ocean);
        }

        .coach-photo img,
        .location-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 23px;
            background: white;
        }

        .photo-placeholder svg {
            width: 42px;
            height: 42px;
        }

        .coach-body,
        .location-body {
            padding: 24px;
        }

        .coach-body h3,
        .location-body h3 {
            color: var(--navy);
            font-size: 19px;
            margin-bottom: 8px;
        }

        .coach-experience,
        .location-address {
            color: var(--muted);
            font-size: 13px;
        }

        .coach-bio,
        .location-description {
            margin-top: 12px;
            color: var(--muted);
            font-size: 13px;
        }

        .location-facilities {
            margin-top: 17px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        .location-facilities strong {
            color: var(--navy);
        }

        .location-facilities p {
            color: var(--muted);
            margin-top: 5px;
        }

        .maps-link {
            display: inline-block;
            margin-top: 16px;
            color: var(--ocean);
            font-size: 13px;
            font-weight: 800;
        }

        .empty-state {
            grid-column: 1 / -1;
            padding: 36px 24px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed #b9dce9;
            border-radius: var(--radius);
            background: white;
        }

        /* WHY US */

        .why-grid {
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            align-items: center;
            gap: 65px;
        }

        .why-visual {
            position: relative;
            min-height: 480px;
            border-radius: 30px;
            overflow: hidden;
            background: linear-gradient(145deg, #063b68, #0b82d4 65%, #22b8cf);
        }

        .why-overlay {
            position: absolute;
            left: 25px;
            right: 25px;
            bottom: 25px;
            padding: 24px;
            border-radius: 18px;
            color: white;
            background: rgba(5,42,75,.6);
        }

        .why-overlay strong {
            font-family: 'Manrope', sans-serif;
            font-size: 23px;
        }

        .why-overlay p {
            margin-top: 7px;
            font-size: 13px;
            color: rgba(255,255,255,.8);
        }

        .why-description {
            margin-top: 16px;
            color: var(--muted);
            font-size: 14px;
        }

        .feature-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 19px;
            margin-top: 28px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 11px;
        }

        .feature-check {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--aqua);
            color: var(--ocean);
            font-weight: 800;
        }

        .feature-item strong {
            color: var(--navy);
            font-size: 13px;
        }

        .feature-item p {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        /* GALLERY */

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .gallery-card {
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: white;
            box-shadow: 0 8px 24px rgba(7,55,99,.05);
            transition: transform .25s, box-shadow .25s;
        }

        .gallery-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .gallery-image {
            width: 100%;
            height: 235px;
            display: block;
            object-fit: cover;
            transition: transform .35s ease;
        }

        .gallery-card:hover .gallery-image {
            transform: scale(1.035);
        }

        .gallery-caption {
            padding: 17px 19px;
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
        }

        .gallery-placeholder {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            height: 235px;
            overflow: hidden;
            background: linear-gradient(145deg, #0c96c9, #073763);
            color: white;
        }

        .gallery-placeholder::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border: 35px solid rgba(255,255,255,.07);
            border-radius: 50%;
            right: -90px;
            top: -95px;
        }

        .gallery-placeholder-logo {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            padding: 12px;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 12px 32px rgba(3, 32, 60, .16);
        }

        .gallery-placeholder-logo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .gallery-placeholder span {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .gallery-placeholder.two {
            background: linear-gradient(145deg, #1cb6d0, #086d9f);
        }

        .gallery-placeholder.three {
            background: linear-gradient(145deg, #095c9a, #052a4b);
        }

        /* SPONSORS */

        .sponsor-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .sponsor-card {
            width: 185px;
            min-height: 145px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 17px;
            background: white;
            transition: transform .2s, box-shadow .2s;
        }

        .sponsor-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .sponsor-card img {
            display: block;
            width: 100%;
            height: 85px;
            max-width: 150px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .sponsor-empty {
            width: 100%;
            padding: 32px;
            border: 1px dashed #b9dce9;
            border-radius: 20px;
            text-align: center;
            color: var(--muted);
            background: white;
            font-size: 13px;
        }

        /* STEPS */

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .step {
            text-align: center;
            padding: 12px;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            margin: 0 auto 19px;
            border-radius: 18px;
            color: white;
            background: linear-gradient(135deg, var(--navy), var(--ocean));
            font-family: 'Manrope', sans-serif;
            font-size: 20px;
            font-weight: 800;
        }

        .step h3 {
            color: var(--navy);
            font-size: 15px;
        }

        .step p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        /* SERVICES */

        .service-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .service-card {
            padding: 35px;
            min-height: 285px;
            border-radius: 25px;
        }

        .service-card.private {
            color: white;
            background: linear-gradient(135deg, #052f55, #075c9d);
        }

        .service-card.group {
            color: var(--navy);
            background: #e9f9fc;
            border: 1px solid #d3eef4;
        }

        .service-badge {
            display: inline-block;
            margin-bottom: 22px;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .private .service-badge {
            background: rgba(255,255,255,.13);
        }

        .group .service-badge {
            background: white;
            color: var(--ocean);
        }

        .service-card h3 {
            font-size: 27px;
        }

        .service-card p {
            margin-top: 12px;
            font-size: 13px;
        }

        .private p {
            color: rgba(255,255,255,.78);
        }

        .group p {
            color: var(--muted);
        }

        .service-features {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 22px;
        }

        .service-feature {
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .private .service-feature {
            background: rgba(255,255,255,.12);
        }

        .group .service-feature {
            background: white;
        }

        /* CTA */

        .cta-section {
            padding: 30px 0 90px;
        }

        .cta {
            padding: 60px 50px;
            border-radius: 30px;
            color: white;
            background: linear-gradient(135deg, #052f55, #0877bd);
        }

        .cta-content {
            max-width: 720px;
        }

        .cta h2 {
            font-size: clamp(28px, 3.8vw, 43px);
        }

        .cta p {
            margin-top: 14px;
            color: rgba(255,255,255,.8);
            font-size: 14px;
        }

        .cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 11px;
            margin-top: 27px;
        }

        /* FAQ */

        .faq-list {
            max-width: 850px;
            display: grid;
            gap: 12px;
            margin: auto;
        }

        .faq-item {
            padding: 18px 22px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: white;
        }

        .faq-item summary {
            cursor: pointer;
            color: var(--navy);
            font-size: 14px;
            font-weight: 700;
        }

        .faq-answer {
            margin-top: 12px;
            color: var(--muted);
            font-size: 13px;
            white-space: pre-line;
        }

        /* FOOTER */

        footer {
            padding: 65px 0 25px;
            background: #041f36;
            color: white;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr .8fr .8fr 1fr;
            gap: 38px;
        }

        .footer-brand .brand-name {
            color: white;
        }

        .footer-brand .brand-subtitle {
            color: #65d7e8;
        }

        .footer-description {
            max-width: 350px;
            margin-top: 18px;
            color: rgba(255,255,255,.62);
            font-size: 12px;
        }

        .footer-column h4 {
            margin-bottom: 17px;
            font-size: 13px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: rgba(255,255,255,.62);
            font-size: 12px;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 50px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,.1);
            color: rgba(255,255,255,.5);
            font-size: 11px;
        }

        /* RESPONSIVE */

        @media (max-width: 1250px) {
            .nav-links {
                gap: 12px;
            }

            .nav-link {
                font-size: 11px;
            }
        }

        @media (max-width: 1100px) {
            .nav-links {
                display: none;
            }

            .menu-button {
                display: flex;
            }

            .mobile-menu {
                position: fixed;
                top: 76px;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 999;
                overflow-y: auto;
                padding: 20px;
                background: white;
            }

            .mobile-menu.active {
                display: block;
            }

            .mobile-links {
                display: grid;
                gap: 5px;
            }

            .mobile-link {
                display: block;
                padding: 13px 15px;
                border-radius: 10px;
                color: var(--navy);
                font-size: 14px;
                font-weight: 700;
            }

            .mobile-link:hover {
                background: var(--background);
            }

            .mobile-actions {
                display: grid;
                gap: 10px;
                margin-top: 18px;
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 820px) {
            .hero-grid,
            .why-grid,
            .service-panel {
                grid-template-columns: 1fr;
            }

            .hero-grid {
                gap: 40px;
            }

            .hero-visual {
                width: 100%;
                max-width: 580px;
                margin: auto;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .steps {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-grid {
                gap: 35px;
            }

            .why-visual {
                min-height: 400px;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: calc(100% - 28px);
            }

            .nav-inner {
                min-height: 70px;
            }

            .mobile-menu {
                top: 70px;
            }

            .brand-icon {
                width: 42px;
                height: 42px;
            }

            .brand-name {
                font-size: 14px;
            }

            .brand-subtitle {
                font-size: 8px;
            }

            .nav-actions > .btn {
                display: none;
            }

            .hero {
                padding: 115px 0 75px;
            }

            .hero h1 {
                font-size: 39px;
                letter-spacing: -1.5px;
            }

            .hero-description {
                font-size: 14px;
            }

            .hero-actions,
            .cta-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .hero-actions .btn,
            .cta-actions .btn {
                width: 100%;
            }

            .hero-visual {
                min-height: 370px;
            }

            .pool-card {
                inset: 20px 10px 20px 10px;
            }

            .floating-card {
                width: 170px;
                padding: 13px;
            }

            .floating-card p {
                font-size: 10px;
            }

            .section {
                padding: 70px 0;
            }

            .program-grid,
            .package-grid,
            .coach-grid,
            .location-grid,
            .gallery-grid,
            .feature-list,
            .steps,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .gallery-image,
            .gallery-placeholder {
                height: 225px;
            }

            .why-visual {
                min-height: 350px;
            }

            .service-card {
                padding: 27px;
            }

            .cta {
                padding: 40px 25px;
            }

            .footer-bottom {
                flex-direction: column;
            }

            .sponsor-card {
                width: calc(50% - 10px);
                max-width: 185px;
                height: auto;
                min-height: 145px;
            }
        }

        /* Penyempurnaan visual dan aksesibilitas */
        :focus-visible {
            outline: 3px solid var(--cyan);
            outline-offset: 3px;
        }

        .hero-content { position: relative; z-index: 1; }
        .hero h1 { text-wrap: balance; }
        .section-heading h2 { text-wrap: balance; }
        .program-card, .package-card, .coach-card, .location-card { min-width: 0; }
        .program-card p, .package-description, .coach-bio, .location-description { overflow-wrap: anywhere; }
        .gallery-caption { overflow-wrap: anywhere; }
        .footer-links a { width: fit-content; }

        @media (min-width: 1101px) and (max-width: 1250px) {
            .nav-inner { gap: 12px; }
            .brand { gap: 8px; }
            .nav-actions .btn { padding-left: 11px; padding-right: 11px; }
        }

        @media (max-width: 600px) {
            .hero h1 { font-size: clamp(32px, 9vw, 39px); }
            .floating-card { width: min(170px, 46%); }
            .floating-card strong { font-size: 11px; }
            .hero-notes { gap: 9px 14px; }
            .sponsor-grid { gap: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *, *::before, *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }

        /* Ragen responsive carousel: one consistent system for public sections. */
        .ragen-carousel-controls{display:flex;justify-content:flex-end;gap:10px;margin:0 0 16px}
        .ragen-carousel-arrow{width:42px;height:42px;display:grid;place-items:center;border:1px solid #cbd5e1;border-radius:12px;background:#fff;color:#12355b;font-size:22px;cursor:pointer;transition:background .2s,transform .2s}
        .ragen-carousel-arrow:hover{background:#eaf3ff;transform:translateY(-2px)}
        .ragen-carousel-arrow:focus-visible{outline:3px solid #3b82f6;outline-offset:2px}
        .ragen-carousel{display:flex!important;justify-content:flex-start!important;flex-wrap:nowrap!important;gap:20px;overflow-x:auto;overscroll-behavior-inline:contain;scroll-snap-type:x mandatory;scroll-behavior:smooth;padding:5px 3px 20px;scrollbar-width:thin;scrollbar-color:#a9bfd7 transparent;align-items:stretch}
        .ragen-carousel > *{flex:0 0 calc((100% - 40px)/3);min-width:0;scroll-snap-align:start}
        .ragen-carousel.gallery-grid > *{flex-basis:calc((100% - 60px)/4)}
        .ragen-carousel.sponsor-grid > *{flex-basis:calc((100% - 80px)/5)}
        .ragen-carousel .empty-state,.ragen-carousel .sponsor-empty{flex-basis:100%}
        .ragen-carousel .package-card,.ragen-carousel .program-card,.ragen-carousel .location-card{height:auto}
        .ragen-package-match{outline:3px solid #2563eb;outline-offset:3px;box-shadow:0 14px 40px rgba(37,99,235,.24)!important;position:relative;z-index:1}
        .ragen-package-match::before{content:'Paket sesuai program pilihan';display:block;margin-bottom:12px;color:#1d4ed8;font-size:12px;font-weight:800}
        .ragen-package-hidden{display:none!important}
        .ragen-package-filter{display:none;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;margin-bottom:16px;border-radius:12px;background:#eaf3ff;color:#163d68;font-weight:600}
        .ragen-package-filter.is-active{display:flex}
        .ragen-package-reset{border:1px solid #b3cbea;border-radius:9px;padding:8px 12px;background:white;color:#163d68;cursor:pointer;font:inherit;font-size:13px}
        @media(max-width:900px){.ragen-carousel > *{flex-basis:calc((100% - 20px)/2)}.ragen-carousel.gallery-grid > *,.ragen-carousel.sponsor-grid > *{flex-basis:calc((100% - 20px)/2)}}
        @media(max-width:600px){.ragen-carousel > *,.ragen-carousel.gallery-grid > *,.ragen-carousel.sponsor-grid > *{flex-basis:88%}.ragen-carousel-controls{margin-top:6px}.ragen-package-filter{align-items:flex-start;flex-direction:column}}
        @media(prefers-reduced-motion:reduce){.ragen-carousel{scroll-behavior:auto}.ragen-carousel-arrow{transition:none}}

    </style>
    <link rel="stylesheet" href="{{ asset('css/ragen-polish.css') }}">
</head>

<body>

    <!-- NAVBAR -->

    <header class="navbar" id="navbar">
        <div class="container nav-inner">

            <a href="{{ route('home') }}" class="brand">
                <div class="brand-icon">
                    <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course">
                </div>

                <div class="brand-text">
                    <span class="brand-name">Ragen Swim Course</span>
                    <span class="brand-subtitle">Learn • Swim • Grow</span>
                </div>
            </a>

            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#program" class="nav-link">Program</a>
                <a href="#paket" class="nav-link">Paket</a>
                <a href="#pelatih" class="nav-link">Pelatih</a>
                <a href="#lokasi" class="nav-link">Lokasi</a>
                <a href="#galeri" class="nav-link">Galeri</a>
                <a href="#sponsor" class="nav-link">Partner</a>
                <a href="#cara-daftar" class="nav-link">Cara Daftar</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a href="{{ route($dashboardRoute) }}" class="btn btn-primary">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Daftar Sekarang
                    </a>
                @endauth

                <button
                    type="button"
                    class="menu-button"
                    id="menuButton"
                    aria-label="Buka menu"
                    aria-controls="mobileMenu"
                    aria-expanded="false"
                >
                    ☰
                </button>
            </div>
        </div>
    </header>

    <!-- MOBILE MENU -->

    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
        <nav class="mobile-links" aria-label="Navigasi mobile">
            <a href="#beranda" class="mobile-link">Beranda</a>
            <a href="#program" class="mobile-link">Program Kursus</a>
            <a href="#paket" class="mobile-link">Paket & Harga</a>
            <a href="#pelatih" class="mobile-link">Pelatih Kami</a>
            <a href="#lokasi" class="mobile-link">Lokasi Latihan</a>
            <a href="#keunggulan" class="mobile-link">Keunggulan</a>
            <a href="#galeri" class="mobile-link">Galeri</a>
            <a href="#sponsor" class="mobile-link">Sponsor & Partner</a>
            <a href="#cara-daftar" class="mobile-link">Cara Daftar</a>
            <a href="#layanan" class="mobile-link">Layanan</a>

            @if($faqs->isNotEmpty())
                <a href="#faq" class="mobile-link">FAQ</a>
            @endif
        </nav>

        <div class="mobile-actions">
            @auth
                <a href="{{ route($dashboardRoute) }}" class="btn btn-primary">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Daftar Sekarang
                </a>
            @endauth
        </div>
    </div>

    <main>

        <!-- HERO -->

        <section class="hero" id="beranda">
            <div class="container hero-grid">

                <div class="hero-content">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        Ragen Swim Course
                    </div>

                    <h1>
                        Belajar Berenang Lebih
                        <span>Aman, Terarah,</span>
                        dan Menyenangkan
                    </h1>

                    <p class="hero-description">
                        Program kursus renang dengan pembelajaran bertahap
                        yang disesuaikan dengan kemampuan peserta.
                        Mulai dari pengenalan air, teknik dasar,
                        hingga pengembangan kemampuan berenang.
                    </p>

                    <div class="hero-actions">
                        <a href="#program" class="btn btn-primary">
                            Lihat Program →
                        </a>

                        @auth
                            @if($userRole === 'customer')
                                <a href="#paket" class="btn btn-outline">
                                    Lihat Paket
                                </a>
                            @else
                                <a href="{{ route($dashboardRoute) }}" class="btn btn-outline">
                                    Buka Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="btn btn-outline">
                                Daftar Sekarang
                            </a>
                        @endauth
                    </div>

                    <div class="hero-notes">
                        <div class="hero-note"><span>✓</span> Pembelajaran Bertahap</div>
                        <div class="hero-note"><span>✓</span> Jadwal Fleksibel</div>
                        <div class="hero-note"><span>✓</span> Progress Tercatat</div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="pool-card">
                        <div class="swimmer-symbol">
                            <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course" class="swimmer-logo">
                        </div>
                    </div>

                    <div class="floating-card top">
                        <div class="float-icon">★</div>
                        <strong>Pelatih Berpengalaman</strong>
                        <p>Pendampingan latihan sesuai kemampuan setiap peserta.</p>
                    </div>

                    <div class="floating-card bottom">
                        <div class="float-icon">✓</div>
                        <strong>Booking Lebih Mudah</strong>
                        <p>Pilih paket dan jadwal melalui customer portal.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- PROGRAM -->

        <section class="section" id="program">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Program Kursus</span>
                    <h2>Program Renang untuk Setiap Tahap Kemampuan</h2>
                    <p>
                        Pilih program sesuai kebutuhan peserta.
                        Informasi program mengikuti data aktif Ragen Swim Course.
                    </p>
                </div>

                <div class="ragen-carousel-controls" aria-label="Navigasi program">
                    <button type="button" class="ragen-carousel-arrow" data-ragen-prev="program" aria-label="Geser program sebelumnya">&#8592;</button>
                    <button type="button" class="ragen-carousel-arrow" data-ragen-next="program" aria-label="Geser program berikutnya">&#8594;</button>
                </div>
                <div class="program-grid ragen-carousel" data-ragen-carousel="program" tabindex="0" aria-label="Daftar program yang dapat digeser">
                    @forelse($programs as $program)
                        <article class="program-card">
                            <div class="program-icon">
                                <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course" style="width:38px;height:38px;object-fit:contain;display:block;">
                            </div>

                            <h3>{{ $program->name }}</h3>

                            <p>
                                {{ $program->short_description ?: ($program->description ?: 'Program latihan renang dengan pembelajaran yang disesuaikan dengan kemampuan peserta.') }}
                            </p>

                            <a href="#paket" class="program-link" data-program-target="{{ $program->id }}">
                                Lihat paket program →
                            </a>
                        </article>
                    @empty
                        <div class="empty-state">
                            Program kursus belum tersedia.
                            Silakan cek kembali nanti.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- PAKET -->

        <section class="section section-soft" id="paket">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Paket & Harga</span>
                    <h2>Pilih Paket Latihan Sesuai Kebutuhan</h2>
                    <p>
                        Harga, jumlah pertemuan, dan masa berlaku paket
                        mengikuti data aktif pada sistem.
                    </p>
                </div>

                <p style="text-align:center;margin:0 0 20px"><a href="{{ route('catalog.index') }}" style="display:inline-block;padding:11px 19px;background:#123e70;color:#fff;border-radius:12px;font-weight:700;text-decoration:none">Lihat Semua Paket &amp; Filter Program →</a></p>
                <div class="ragen-carousel-controls" aria-label="Navigasi package">
                    <button type="button" class="ragen-carousel-arrow" data-ragen-prev="package" aria-label="Geser package sebelumnya">&#8592;</button>
                    <button type="button" class="ragen-carousel-arrow" data-ragen-next="package" aria-label="Geser package berikutnya">&#8594;</button>
                </div>
                <div id="ragenPackageFilter" class="ragen-package-filter" role="status" aria-live="polite">
                    <span id="ragenPackageFilterText">Menampilkan paket program pilihan</span>
                    <button type="button" class="ragen-package-reset" id="ragenPackageReset">Tampilkan Semua Paket</button>
                </div>
                <div class="package-grid ragen-carousel" data-ragen-carousel="package" tabindex="0" aria-label="Daftar package yang dapat digeser">
                    @forelse($packages as $package)
                        <article class="package-card" data-package-program="{{ $package->program_id }}">
                            <span class="package-program">
                                {{ $package->program?->name ?? 'Program Renang' }}
                            </span>

                            <h3>{{ $package->name }}</h3>

                            <p class="package-description">
                                {{ $package->description ?: 'Paket latihan Ragen Swim Course untuk membantu peserta berlatih secara terarah dan konsisten.' }}
                            </p>

                            <div class="package-price">
                                {{ $package->formatted_price }}
                            </div>

                            <div class="package-meta">
                                <div class="package-meta-item">
                                    <span>Jumlah Pertemuan</span>
                                    <strong>{{ $package->total_sessions }} sesi</strong>
                                </div>

                                <div class="package-meta-item">
                                    <span>Durasi / Pertemuan</span>
                                    <strong>{{ $package->program?->duration_minutes ?? '-' }} menit</strong>
                                </div>

                                <div class="package-meta-item">
                                    <span>Masa Berlaku</span>
                                    <strong>
                                        {{ $package->validity_days ? $package->validity_days . ' hari' : 'Menyesuaikan' }}
                                    </strong>
                                </div>
                            </div>

                            @auth
                                @if($userRole === 'customer')
                                    <a href="{{ route('customer.packages.browse') }}"
                                       class="btn btn-primary">
                                        Pilih Paket
                                    </a>
                                @else
                                    <a href="{{ route($dashboardRoute) }}"
                                       class="btn btn-outline">
                                        Buka Dashboard
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}"
                                   class="btn btn-primary">
                                    Daftar untuk Memilih Paket
                                </a>
                            @endauth
                        </article>
                    @empty
                        <div class="empty-state">
                            Paket kursus belum tersedia.
                            Silakan cek kembali nanti.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- PELATIH -->

        <section class="section" id="pelatih">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Pelatih Kami</span>
                    <h2>Didampingi Pelatih Sesuai Kebutuhan Peserta</h2>
                    <p>
                        Kenali pelatih aktif Ragen Swim Course
                        beserta spesialisasi dan pengalaman mereka.
                    </p>
                </div>

                <div class="coach-grid">
                    @forelse($coaches as $coach)
                        <article class="coach-card">
                            <div class="coach-photo">
                                @if($coach->photo)
                                    <img
                                        loading="lazy"
                                        src="{{ asset('storage/' . ltrim($coach->photo, '/')) }}"
                                        alt="Foto {{ $coach->user?->name ?? 'Pelatih Ragen Swim Course' }}"
                                    >
                                @else
                                    <div class="photo-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7">
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M4 21c0-4.2 3.6-7 8-7s8 2.8 8 7"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="coach-body">
                                @if($coach->specialization)
                                    <span class="coach-specialization">
                                        {{ $coach->specialization }}
                                    </span>
                                @endif

                                <h3>
                                    {{ $coach->user?->name ?? 'Pelatih Ragen Swim Course' }}
                                </h3>

                                <p class="coach-experience">
                                    {{ $coach->experience_years }} tahun pengalaman
                                </p>

                                @if($coach->bio)
                                    <p class="coach-bio">{{ $coach->bio }}</p>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            Informasi pelatih belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- LOKASI -->

        <section class="section section-soft" id="lokasi">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Lokasi Latihan</span>
                    <h2>Tempat Latihan Ragen Swim Course</h2>
                    <p>
                        Informasi lokasi latihan aktif beserta fasilitas
                        dan petunjuk lokasinya.
                    </p>
                </div>

                <div class="ragen-carousel-controls" aria-label="Navigasi location">
                    <button type="button" class="ragen-carousel-arrow" data-ragen-prev="location" aria-label="Geser location sebelumnya">&#8592;</button>
                    <button type="button" class="ragen-carousel-arrow" data-ragen-next="location" aria-label="Geser location berikutnya">&#8594;</button>
                </div>
                <div class="location-grid ragen-carousel" data-ragen-carousel="location" tabindex="0" aria-label="Daftar location yang dapat digeser">
                    @forelse($locations as $location)
                        <article class="location-card">
                            <div class="location-photo">
                                @if($location->image)
                                    <img
                                        loading="lazy"
                                        src="{{ asset('storage/' . ltrim($location->image, '/')) }}"
                                        alt="{{ $location->name }}"
                                    >
                                @else
                                    <div class="photo-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7">
                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                            <circle cx="12" cy="10" r="2.5"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="location-body">
                                @if($location->city)
                                    <span class="location-city">
                                        {{ $location->city }}
                                    </span>
                                @endif

                                <h3>{{ $location->name }}</h3>

                                <p class="location-address">
                                    {{ $location->address }}
                                </p>

                                @if($location->description)
                                    <p class="location-description">
                                        {{ $location->description }}
                                    </p>
                                @endif

                                @if($location->facilities)
                                    <div class="location-facilities">
                                        <strong>Fasilitas</strong>
                                        <p>{{ $location->facilities }}</p>
                                    </div>
                                @endif

                                @if($location->google_maps_url)
                                    <a
                                        href="{{ $location->google_maps_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="maps-link"
                                    >
                                        Lihat di Google Maps →
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            Informasi lokasi latihan belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- KEUNGGULAN -->

        <section class="section" id="keunggulan">
            <div class="container why-grid">
                <div class="why-visual">
                    <div class="swimmer-symbol">
                        <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course" class="swimmer-logo">
                    </div>

                    <div class="why-overlay">
                        <strong>Learn • Swim • Grow</strong>
                        <p>
                            Membantu peserta berkembang melalui latihan
                            yang konsisten dan sesuai kemampuan.
                        </p>
                    </div>
                </div>

                <div class="why-content">
                    <span class="section-label">Mengapa Ragen Swim Course?</span>

                    <h2>Lebih dari Sekadar Belajar Berenang</h2>

                    <p class="why-description">
                        Kami ingin setiap peserta memiliki proses belajar
                        yang jelas, mulai dari memilih program hingga
                        memantau perkembangan kemampuan.
                    </p>

                    <div class="feature-list">
                        @foreach([
                            ['Pembelajaran Terarah', 'Latihan disesuaikan dengan tingkat kemampuan peserta.'],
                            ['Pelatih Berpengalaman', 'Peserta mendapatkan pendampingan selama latihan.'],
                            ['Jadwal Fleksibel', 'Pilih jadwal dari kelas yang tersedia.'],
                            ['Progress Tercatat', 'Perkembangan peserta dapat dipantau melalui sistem.'],
                            ['Booking Online', 'Pemilihan paket dan jadwal lebih praktis.'],
                            ['Multi Peserta', 'Satu akun dapat mengelola beberapa peserta.'],
                        ] as $feature)
                            <div class="feature-item">
                                <div class="feature-check">✓</div>
                                <div>
                                    <strong>{{ $feature[0] }}</strong>
                                    <p>{{ $feature[1] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- GALLERY -->

        <section class="section section-soft" id="galeri">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Galeri Kegiatan</span>
                    <h2>Momen Bersama Ragen Swim Course</h2>
                    <p>
                        Dokumentasi latihan, kegiatan, dan perkembangan
                        peserta bersama Ragen Swim Course.
                    </p>
                </div>

                <div class="ragen-carousel-controls" aria-label="Navigasi gallery">
                    <button type="button" class="ragen-carousel-arrow" data-ragen-prev="gallery" aria-label="Geser gallery sebelumnya">&#8592;</button>
                    <button type="button" class="ragen-carousel-arrow" data-ragen-next="gallery" aria-label="Geser gallery berikutnya">&#8594;</button>
                </div>
                <div class="gallery-grid ragen-carousel" data-ragen-carousel="gallery" tabindex="0" aria-label="Daftar gallery yang dapat digeser">
                    @forelse($galleries as $gallery)
                        <figure class="gallery-card">
                            <img
                                class="gallery-image"
                                loading="lazy"
                                src="{{ asset('storage/' . ltrim($gallery->image, '/')) }}"
                                alt="{{ $gallery->title ?: 'Dokumentasi kegiatan renang' }}"
                            >

                            <figcaption class="gallery-caption">
                                <div>{{ $gallery->title ?: $gallery->category_label }}</div>
                                @if(filled($gallery->description))
                                    <p style="margin:8px 0 0;color:#64748b;font-size:12px;font-weight:400;line-height:1.6;overflow-wrap:anywhere;">{{ $gallery->description }}</p>
                                @endif
                            </figcaption>
                        </figure>
                    @empty
                        @foreach([
                            ['Kegiatan Latihan Renang', 'one'],
                            ['Pembelajaran Teknik Renang', 'two'],
                            ['Momen Bersama Peserta', 'three'],
                        ] as $placeholder)
                            <figure class="gallery-card">
                                <div class="gallery-placeholder {{ $placeholder[1] }}">
                                    <div class="gallery-placeholder-logo">
                                        <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course">
                                    </div>
                                    <span>Ragen Swim Course</span>
                                </div>

                                <figcaption class="gallery-caption">
                                    {{ $placeholder[0] }}
                                </figcaption>
                            </figure>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <!-- SPONSOR & PARTNER -->

        <section class="section" id="sponsor">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Sponsor & Partner</span>
                    <h2>Mitra Ragen Swim Course</h2>
                    <p>
                        Dukungan dan kolaborasi bersama mitra
                        dalam pengembangan kegiatan Ragen Swim Course.
                    </p>
                </div>

                <div class="ragen-carousel-controls" aria-label="Navigasi sponsor">
                    <button type="button" class="ragen-carousel-arrow" data-ragen-prev="sponsor" aria-label="Geser sponsor sebelumnya">&#8592;</button>
                    <button type="button" class="ragen-carousel-arrow" data-ragen-next="sponsor" aria-label="Geser sponsor berikutnya">&#8594;</button>
                </div>
                <div class="sponsor-grid ragen-carousel" data-ragen-carousel="sponsor" tabindex="0" aria-label="Daftar sponsor yang dapat digeser">
                    @forelse($sponsors as $sponsor)
                        @if($sponsor->website_url)
                            <a
                                class="sponsor-card"
                                href="{{ $sponsor->website_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $sponsor->name }}"
                            >
                                <img
                                    loading="lazy"
                                    src="{{ asset('storage/' . ltrim($sponsor->logo, '/')) }}"
                                    alt="Logo {{ $sponsor->name }}"
                                ><span style="margin-top:10px;text-align:center;font-size:12px;font-weight:700;color:#123b5d;line-height:1.4;overflow-wrap:anywhere;">{{ $sponsor->name }}</span>

                            </a>
                        @else
                            <div class="sponsor-card">
                                <img
                                    loading="lazy"
                                    src="{{ asset('storage/' . ltrim($sponsor->logo, '/')) }}"
                                    alt="Logo {{ $sponsor->name }}"
                                ><span style="margin-top:10px;text-align:center;font-size:12px;font-weight:700;color:#123b5d;line-height:1.4;overflow-wrap:anywhere;">{{ $sponsor->name }}</span>

                            </div>
                        @endif
                    @empty
                        <div class="sponsor-empty">
                            Informasi sponsor dan partner akan ditampilkan
                            setelah tersedia dan dipublikasikan oleh Admin.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- CARA DAFTAR -->

        <section class="section section-soft" id="cara-daftar">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Cara Bergabung</span>
                    <h2>Mulai Kursus Renang dalam Beberapa Langkah</h2>
                    <p>
                        Proses pendaftaran dibuat sederhana
                        agar peserta dapat segera memulai latihan.
                    </p>
                </div>

                <div class="steps">
                    @foreach([
                        ['01', 'Buat Akun', 'Daftar sebagai customer Ragen Swim Course.'],
                        ['02', 'Tambah Peserta', 'Lengkapi profil peserta yang akan mengikuti latihan.'],
                        ['03', 'Pilih Paket', 'Pilih program dan paket sesuai kebutuhan.'],
                        ['04', 'Booking Jadwal', 'Setelah paket aktif, pilih jadwal yang tersedia.'],
                    ] as $step)
                        <div class="step">
                            <div class="step-number">{{ $step[0] }}</div>
                            <h3>{{ $step[1] }}</h3>
                            <p>{{ $step[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- LAYANAN -->

        <section class="section" id="layanan">
            <div class="container">
                <div class="section-heading">
                    <span class="section-label">Pilihan Layanan</span>
                    <h2>Pilih Pengalaman Belajar yang Sesuai</h2>
                    <p>
                        Pembelajaran dapat disesuaikan dengan
                        kebutuhan dan kenyamanan peserta.
                    </p>
                </div>

                <div class="service-panel">
                    <article class="service-card private">
                        <span class="service-badge">Pendampingan Personal</span>
                        <h3>Private Class</h3>
                        <p>
                            Program latihan dengan perhatian lebih personal,
                            cocok untuk peserta yang membutuhkan pembelajaran
                            lebih fokus sesuai kemampuan individu.
                        </p>

                        <div class="service-features">
                            <span class="service-feature">Lebih Personal</span>
                            <span class="service-feature">Fokus Teknik</span>
                            <span class="service-feature">Progress Terarah</span>
                        </div>
                    </article>

                    <article class="service-card group">
                        <span class="service-badge">Belajar Bersama</span>
                        <h3>Group Class</h3>
                        <p>
                            Belajar berenang bersama peserta lain dalam
                            suasana aktif dan menyenangkan dengan
                            kapasitas kelas yang terkontrol.
                        </p>

                        <div class="service-features">
                            <span class="service-feature">Interaktif</span>
                            <span class="service-feature">Menyenangkan</span>
                            <span class="service-feature">Terjadwal</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA -->

        <section class="cta-section">
            <div class="container">
                <div class="cta">
                    <div class="cta-content">
                        <h2>
                            Siap Memulai Perjalanan Berenang Bersama Ragen?
                        </h2>

                        <p>
                            Buat akun, tambahkan peserta, pilih program
                            yang sesuai, dan atur jadwal latihan melalui
                            Ragen Swim Course.
                        </p>

                        <div class="cta-actions">
                            @auth
                                <a href="{{ route($dashboardRoute) }}"
                                   class="btn btn-white">
                                    Buka Dashboard
                                </a>

                                @if($userRole === 'customer')
                                    <a href="{{ route('customer.packages.browse') }}"
                                       class="btn btn-glass">
                                        Lihat Paket
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}"
                                   class="btn btn-white">
                                    Daftar Sekarang
                                </a>

                                <a href="{{ route('login') }}"
                                   class="btn btn-glass">
                                    Sudah Punya Akun
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->

        @if($faqs->isNotEmpty())
            <section class="section section-soft" id="faq">
                <div class="container">
                    <div class="section-heading">
                        <span class="section-label">Pertanyaan Umum</span>
                        <h2>Frequently Asked Questions</h2>
                        <p>
                            Informasi seputar program dan layanan
                            Ragen Swim Course.
                        </p>
                    </div>

                    <div class="faq-list">
                        @foreach($faqs as $faq)
                            <details class="faq-item">
                                <summary>{{ $faq->question }}</summary>
                                <div class="faq-answer">
                                    {{ $faq->answer }}
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

    </main>

    <!-- FOOTER -->

    <footer>
        <div class="container">
            <div class="footer-grid">

                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="brand">
                        <div class="brand-icon">
                            <img src="{{ $logoUrl }}" alt="Logo Ragen Swim Course">
                        </div>

                        <div class="brand-text">
                            <span class="brand-name">Ragen Swim Course</span>
                            <span class="brand-subtitle">Learn • Swim • Grow</span>
                        </div>
                    </a>

                    <p class="footer-description">
                        Program kursus renang dengan proses pembelajaran
                        bertahap untuk membantu peserta belajar dan
                        mengembangkan kemampuan berenang.
                    </p>
                </div>

                <div class="footer-column">
                    <h4>Menu</h4>
                    <div class="footer-links">
                        <a href="#beranda">Beranda</a>
                        <a href="#program">Program</a>
                        <a href="#paket">Paket & Harga</a>
                        <a href="#pelatih">Pelatih</a>
                        <a href="#lokasi">Lokasi</a>
                        <a href="#galeri">Galeri</a>
                        <a href="#sponsor">Sponsor & Partner</a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Layanan</h4>
                    <div class="footer-links">
                        <a href="#program">Program Renang</a>
                        <a href="#layanan">Private Class</a>
                        <a href="#layanan">Group Class</a>
                        <a href="#keunggulan">Keunggulan</a>
                        <a href="#cara-daftar">Cara Daftar</a>

                        @if($faqs->isNotEmpty())
                            <a href="#faq">FAQ</a>
                        @endif
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Akun</h4>
                    <div class="footer-links">
                        @auth
                            <a href="{{ route($dashboardRoute) }}">
                                Dashboard
                            </a>

                            @if($userRole === 'customer')
                                <a href="{{ route('customer.participants.index') }}">
                                    Peserta Saya
                                </a>

                                <a href="{{ route('customer.packages.index') }}">
                                    Paket Saya
                                </a>

                                <a href="{{ route('customer.bookings.index') }}">
                                    Jadwal & Booking
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}">Masuk</a>
                            <a href="{{ route('register') }}">Daftar Akun</a>
                        @endauth
                    </div>
                </div>

            </div>

            <div class="footer-bottom">
                <span>
                    © {{ date('Y') }} Ragen Swim Course.
                    All rights reserved.
                </span>

                <span>Learn • Swim • Grow</span>
            </div>
        </div>
    </footer>

    <script defer src="{{ asset('js/sponsor-infinite.js') }}"></script>

    <!-- JAVASCRIPT -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.getElementById('navbar');
            const menuButton = document.getElementById('menuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            function updateNavbar() {
                if (navbar) {
                    navbar.classList.toggle('scrolled', window.scrollY > 20);
                }
            }

            function setMenu(open) {
                if (!menuButton || !mobileMenu) {
                    return;
                }

                mobileMenu.classList.toggle('active', open);
                mobileMenu.setAttribute('aria-hidden', String(!open));
                document.body.classList.toggle('menu-open', open);
                menuButton.setAttribute('aria-expanded', String(open));
                menuButton.setAttribute(
                    'aria-label',
                    open ? 'Tutup menu' : 'Buka menu'
                );
                menuButton.textContent = open ? '✕' : '☰';
            }


            // Carousel navigable by buttons, keyboard, and touch; no extra dependency.
            document.querySelectorAll('[data-ragen-carousel]').forEach(function (track) {
                const name = track.dataset.ragenCarousel;
                function slide(direction) {
                    const first = Array.from(track.children).find(function(el){return getComputedStyle(el).display !== 'none';});
                    const distance = first ? first.getBoundingClientRect().width + 20 : track.clientWidth * .8;
                    track.scrollBy({left:distance * direction,behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'});
                }
                document.querySelector('[data-ragen-prev="' + name + '"]')?.addEventListener('click',function(){slide(-1)});
                document.querySelector('[data-ragen-next="' + name + '"]')?.addEventListener('click',function(){slide(1)});
                track.addEventListener('keydown',function(event){
                    if(event.key === 'ArrowRight' || event.key === 'ArrowLeft'){
                        event.preventDefault();slide(event.key === 'ArrowRight' ? 1 : -1);
                    }
                });
            });
            // A program CTA filters all matching packages, highlights them, and scrolls into view.
            const packageCards = Array.from(document.querySelectorAll('[data-package-program]'));
            const packageTrack = document.querySelector('[data-ragen-carousel="package"]');
            const filterBar = document.getElementById('ragenPackageFilter');
            const filterText = document.getElementById('ragenPackageFilterText');
            function showProgramPackages(programId, programName) {
                const matches = packageCards.filter(function(card){return card.dataset.packageProgram === String(programId)});
                if(!matches.length){return false;}
                packageCards.forEach(function(card){
                    const selected = card.dataset.packageProgram === String(programId);
                    card.classList.toggle('ragen-package-hidden',!selected);
                    card.classList.toggle('ragen-package-match',selected);
                });
                filterText.textContent = 'Menampilkan ' + matches.length + ' paket untuk ' + programName;
                filterBar.classList.add('is-active');
                packageTrack.scrollLeft = 0;
                document.getElementById('paket')?.scrollIntoView({behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',block:'start'});
                return true;
            }
            document.querySelectorAll('[data-program-target]').forEach(function(link){
                link.addEventListener('click',function(event){
                    const programName = link.closest('.program-card')?.querySelector('h3')?.textContent.trim() || 'program pilihan';
                    if(showProgramPackages(link.dataset.programTarget,programName)){
                        event.preventDefault();
                        history.replaceState(null,'','#paket');
                    }
                });
            });
            document.getElementById('ragenPackageReset')?.addEventListener('click',function(){
                packageCards.forEach(function(card){card.classList.remove('ragen-package-hidden','ragen-package-match')});
                filterBar.classList.remove('is-active');
                if(packageTrack){packageTrack.scrollLeft=0;}
            });

            updateNavbar();
            window.addEventListener('scroll', updateNavbar, { passive: true });

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function () {
                    setMenu(!mobileMenu.classList.contains('active'));
                });

                mobileLinks.forEach(function (link) {
                    link.addEventListener('click', function () {
                        setMenu(false);
                    });
                });

                document.addEventListener('click', function (event) {
                    if (mobileMenu.classList.contains('active') &&
                        !mobileMenu.contains(event.target) &&
                        !menuButton.contains(event.target)) {
                        setMenu(false);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setMenu(false);
                    }
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth > 1100) {
                        setMenu(false);
                    }
                });
            }
        });
    </script>

</body>
</html>
