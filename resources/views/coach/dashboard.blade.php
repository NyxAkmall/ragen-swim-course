@extends('layouts.coach')

@section('title', 'Dashboard Coach')
@section('topbar-title', 'Dashboard Coach')

@push('styles')
<style>
    /*
    |--------------------------------------------------------------------------
    | Welcome Section
    |--------------------------------------------------------------------------
    */

    /* Akses cepat operasional coach */
    .coach-action-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin:18px 0 24px; }
    .coach-action-link { display:flex; flex-direction:column; gap:6px; min-width:0; padding:18px; border:1px solid #dce8f2; border-radius:16px; background:#fff; color:#123e61; text-decoration:none; transition:border-color .2s,transform .2s,box-shadow .2s; }
    .coach-action-link:hover,.coach-action-link:focus-visible { border-color:#0b78b5; box-shadow:0 10px 24px rgba(6,62,100,.09); transform:translateY(-2px); }
    .coach-action-link:focus-visible { outline:3px solid #81d6fb; outline-offset:2px; }
    .coach-action-icon { font-size:22px; line-height:1.3; }
    .coach-action-title { font-weight:800; font-size:14px; }
    .coach-action-desc { color:#647b90; font-size:12px; line-height:1.55; }
    @media(max-width:900px) { .coach-action-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:480px) { .coach-action-grid { grid-template-columns:1fr; } }

    .coach-welcome {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        overflow: hidden;
        padding: 30px;
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 90% 15%,
                rgba(103, 232, 249, 0.22),
                transparent 35%
            ),
            linear-gradient(135deg, #07355f, #086fa8);
        color: #ffffff;
        box-shadow: 0 15px 35px rgba(5, 74, 116, 0.12);
    }

    .coach-welcome-content {
        position: relative;
        z-index: 1;
        max-width: 680px;
        min-width: 0;
    }

    .coach-welcome-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 14px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #a7ebf4;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .coach-welcome h1 {
        margin: 0 0 10px;
        font-size: clamp(23px, 3vw, 31px);
        font-weight: 800;
        line-height: 1.3;
    }

    .coach-welcome p {
        max-width: 590px;
        margin: 0;
        color: #d0e7f2;
        font-size: 13px;
        line-height: 1.8;
    }

    .coach-welcome-art {
        width: 115px;
        height: 115px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.09);
    }

    .coach-welcome-logo {
        width: 78px;
        height: 78px;
        object-fit: contain;
        padding: 8px;
        background: #ffffff;
        border-radius: 18px;
    }

    .coach-welcome-art svg {
        width: 56px;
        height: 56px;
        color: #8ce6f1;
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .coach-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 22px;
    }

    .coach-stat-card {
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 0;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 17px;
        background: #ffffff;
    }

    .coach-stat-icon {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }

    .coach-stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .coach-stat-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .coach-stat-cyan {
        background: #ecfeff;
        color: #0891b2;
    }

    .coach-stat-green {
        background: #f0fdf4;
        color: #15803d;
    }

    .coach-stat-orange {
        background: #fff7ed;
        color: #c2410c;
    }

    .coach-stat-value {
        color: #082f49;
        font-size: 25px;
        font-weight: 800;
        line-height: 1.1;
    }

    .coach-stat-label {
        margin-top: 6px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Layout
    |--------------------------------------------------------------------------
    */

    .coach-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.8fr);
        align-items: start;
        gap: 20px;
        margin-top: 22px;
    }

    .coach-main-column,
    .coach-side-column {
        min-width: 0;
    }

    .coach-panel {
        padding: 23px;
        border: 1px solid #e2e8f0;
        border-radius: 19px;
        background: #ffffff;
    }

    .coach-panel + .coach-panel {
        margin-top: 20px;
    }

    .coach-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .coach-panel-title {
        margin: 0;
        color: #082f49;
        font-size: 16px;
        font-weight: 800;
    }

    .coach-panel-subtitle {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.65;
    }

    .coach-panel-count {
        flex-shrink: 0;
        padding: 7px 10px;
        border-radius: 9px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 11px;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    .coach-schedule-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .coach-schedule-item {
        display: grid;
        grid-template-columns: 61px minmax(0, 1fr) auto;
        align-items: center;
        gap: 15px;
        min-width: 0;
        padding: 17px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
    }

    .coach-schedule-today {
        border-color: #bce6ee;
        background: #fbfeff;
    }

    .coach-schedule-date {
        width: 61px;
        min-height: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #eff6ff;
    }

    .coach-schedule-day {
        color: #0369a1;
        font-size: 21px;
        font-weight: 800;
        line-height: 1;
    }

    .coach-schedule-month {
        margin-top: 6px;
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .coach-schedule-details {
        min-width: 0;
    }

    .coach-schedule-name {
        margin: 0;
        color: #082f49;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }

    .coach-schedule-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 14px;
        margin-top: 9px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }

    .coach-schedule-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .coach-schedule-meta svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    .coach-today-badge {
        display: inline-flex;
        align-items: center;
        margin-top: 10px;
        padding: 5px 9px;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d;
        font-size: 10px;
        font-weight: 800;
    }

    .coach-schedule-participants {
        min-width: 65px;
        text-align: center;
    }

    .coach-schedule-participants strong {
        display: block;
        color: #082f49;
        font-size: 21px;
        font-weight: 800;
    }

    .coach-schedule-participants span {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 10px;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .coach-empty {
        padding: 35px 20px;
        border: 1px dashed #cbd5e1;
        border-radius: 15px;
        background: #fbfdff;
        text-align: center;
    }

    .coach-empty-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 13px;
        border-radius: 14px;
        background: #ecfeff;
        color: #0891b2;
    }

    .coach-empty-icon svg {
        width: 24px;
        height: 24px;
    }

    .coach-empty strong {
        display: block;
        color: #082f49;
        font-size: 13px;
        font-weight: 800;
    }

    .coach-empty p {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.7;
    }

    /*
    |--------------------------------------------------------------------------
    | Coach Profile
    |--------------------------------------------------------------------------
    */

    .coach-profile {
        text-align: center;
    }

    .coach-profile-avatar {
        width: 76px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 3px auto 14px;
        border-radius: 22px;
        background: linear-gradient(135deg, #cffafe, #dbeafe);
        color: #0369a1;
        font-size: 27px;
        font-weight: 800;
    }

    .coach-profile-name {
        color: #082f49;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .coach-profile-specialization {
        margin-top: 6px;
        color: #0284c7;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.6;
    }

    .coach-profile-bio {
        margin-top: 15px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .coach-profile-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .coach-profile-stat strong {
        display: block;
        color: #082f49;
        font-size: 17px;
        font-weight: 800;
    }

    .coach-profile-stat span {
        display: block;
        margin-top: 5px;
        color: #64748b;
        font-size: 10px;
        line-height: 1.5;
    }

    .coach-guide {
        margin-top: 18px;
        padding: 15px;
        border-radius: 13px;
        background: #f0f9ff;
        text-align: left;
    }

    .coach-guide strong {
        display: block;
        color: #075985;
        font-size: 12px;
        font-weight: 800;
    }

    .coach-guide p {
        margin: 7px 0 0;
        color: #0369a1;
        font-size: 11px;
        line-height: 1.75;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {
        .coach-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .coach-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .coach-welcome {
            padding: 24px 20px;
        }

        .coach-welcome-art {
            display: none;
        }

        .coach-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .coach-stat-card {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
            padding: 15px;
        }

        .coach-stat-icon {
            width: 42px;
            height: 42px;
        }

        .coach-stat-value {
            font-size: 22px;
        }

        .coach-panel {
            padding: 18px;
        }

        .coach-schedule-item {
            grid-template-columns: 55px minmax(0, 1fr);
            gap: 12px;
            padding: 14px;
        }

        .coach-schedule-date {
            width: 55px;
        }

        .coach-schedule-participants {
            grid-column: 2;
            text-align: left;
        }

        .coach-schedule-participants strong,
        .coach-schedule-participants span {
            display: inline;
        }

        .coach-schedule-participants strong {
            font-size: 14px;
        }

        .coach-schedule-participants span {
            margin-left: 4px;
        }
    }

    @media (max-width: 380px) {
        .coach-stats {
            grid-template-columns: 1fr;
        }

        .coach-stat-card {
            flex-direction: row;
            align-items: center;
        }
    }
</style>
@endpush


@section('content')

@php
    $coachName = $coach->user?->name ?? 'Coach';

    $coachInitial = mb_strtoupper(
        mb_substr(trim($coachName), 0, 1)
    );

    $formatTime = function ($time) {
        if (empty($time)) {
            return '-';
        }

        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }

        return substr((string) $time, 0, 5);
    };
@endphp


{{-- =========================================================
    WELCOME
========================================================== --}}

<section class="coach-welcome">

    <div class="coach-welcome-content">

        <span class="coach-welcome-label">
            Coach Portal
        </span>

        <h1>
            Selamat datang, {{ $coachName }}!
        </h1>

        <p>
            Lihat jadwal mengajar hari ini, pantau jumlah peserta,
            dan persiapkan kegiatan latihan melalui dashboard
            Ragen Swim Course.
        </p>

    </div>

    <div class="coach-welcome-art">
        <img src="{{ (function () { $path = \App\Models\Setting::getValue('website_logo', 'branding/ragen-logo.png'); return $path && $path !== 'branding/ragen-logo.png' ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : asset('branding/ragen-logo.png'); })() }}" alt="Logo Ragen Swim Course" class="coach-welcome-logo">
    </div>

</section>


{{-- =========================================================
    STATISTIK
========================================================== --}}

<section class="coach-stats">

    <article class="coach-stat-card">

        <div class="coach-stat-icon coach-stat-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            </svg>
        </div>

        <div>
            <div class="coach-stat-value">
                {{ number_format($todayScheduleCount) }}
            </div>

            <div class="coach-stat-label">
                Kelas Hari Ini
            </div>
        </div>

    </article>


    <article class="coach-stat-card">

        <div class="coach-stat-icon coach-stat-cyan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M8 2v4M16 2v4"/>
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M3 10h18M8 15h2M14 15h2"/>
            </svg>
        </div>

        <div>
            <div class="coach-stat-value">
                {{ number_format($upcomingScheduleCount) }}
            </div>

            <div class="coach-stat-label">
                Jadwal Mendatang
            </div>
        </div>

    </article>


    <article class="coach-stat-card">

        <div class="coach-stat-icon coach-stat-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="9" cy="7" r="4"/>
                <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                <path d="M17 11h5M19.5 8.5v5"/>
            </svg>
        </div>

        <div>
            <div class="coach-stat-value">
                {{ number_format($activeParticipantCount) }}
            </div>

            <div class="coach-stat-label">
                Peserta Aktif
            </div>
        </div>

    </article>


    <article class="coach-stat-card">

        <div class="coach-stat-icon coach-stat-orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>

        <div>
            <div class="coach-stat-value">
                {{ number_format($completedScheduleCount) }}
            </div>

            <div class="coach-stat-label">
                Kelas Selesai
            </div>
        </div>

    </article>

</section>


{{-- =========================================================
    JADWAL DAN PROFIL
========================================================== --}}

{{-- AKSES CEPAT COACH --}}
<nav class="coach-action-grid" aria-label="Akses cepat kegiatan coach">
    <a class="coach-action-link" href="{{ route('coach.schedules.index') }}">
        <span class="coach-action-icon" aria-hidden="true">📅</span>
        <span class="coach-action-title">Jadwal Mengajar</span>
        <span class="coach-action-desc">Lihat kelas hari ini dan mendatang.</span>
    </a>
    <a class="coach-action-link" href="{{ route('coach.attendance.index') }}">
        <span class="coach-action-icon" aria-hidden="true">✓</span>
        <span class="coach-action-title">Absensi Peserta</span>
        <span class="coach-action-desc">Catat kehadiran melalui daftar kelas.</span>
    </a>
    <a class="coach-action-link" href="{{ route('coach.progress.index') }}">
        <span class="coach-action-icon" aria-hidden="true">↗</span>
        <span class="coach-action-title">Progres Latihan</span>
        <span class="coach-action-desc">Tinjau perkembangan peserta.</span>
    </a>
    <a class="coach-action-link" href="{{ route('coach.profile.edit') }}">
        <span class="coach-action-icon" aria-hidden="true">◉</span>
        <span class="coach-action-title">Profil Coach</span>
        <span class="coach-action-desc">Perbarui foto profil pribadi.</span>
    </a>
</nav>

<div class="coach-dashboard-grid">

    <div class="coach-main-column">

        {{-- JADWAL HARI INI --}}
        <section class="coach-panel">

            <div class="coach-panel-header">

                <div>
                    <h2 class="coach-panel-title">
                        Jadwal Hari Ini
                    </h2>

                    <p class="coach-panel-subtitle">
                        Daftar kelas yang dijadwalkan untuk Anda hari ini.
                    </p>
                </div>

                <span class="coach-panel-count">
                    {{ $todaySchedules->count() }} Kelas
                </span>

            </div>


            @forelse ($todaySchedules as $schedule)

                @if ($loop->first)
                    <div class="coach-schedule-list">
                @endif

                    <article class="coach-schedule-item coach-schedule-today">

                        <div class="coach-schedule-date">

                            <span class="coach-schedule-day">
                                {{ $schedule->class_date->format('d') }}
                            </span>

                            <span class="coach-schedule-month">
                                {{ $schedule->class_date->translatedFormat('M') }}
                            </span>

                        </div>


                        <div class="coach-schedule-details">

                            <h3 class="coach-schedule-name">
                                {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                            </h3>

                            <div class="coach-schedule-meta">

                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    {{ $formatTime($schedule->start_time) }}
                                    –
                                    {{ $formatTime($schedule->end_time) }}
                                </span>

                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M12 21s6-5.2 6-11a6 6 0 0 0-12 0c0 5.8 6 11 6 11Z"/>
                                        <circle cx="12" cy="10" r="2"/>
                                    </svg>

                                    {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}
                                </span>

                                <span>
                                    {{ $schedule->program?->name ?? 'Program belum ditentukan' }}
                                </span>

                            </div>

                            <span class="coach-today-badge">
                                Hari Ini
                            </span>

                        </div>


                        <div class="coach-schedule-participants">

                            <strong>
                                {{ $schedule->active_bookings_count }}
                            </strong>

                            <span>
                                booking aktif
                            </span>

                        </div>

                    </article>

                @if ($loop->last)
                    </div>
                @endif

            @empty

                <div class="coach-empty">

                    <div class="coach-empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>
                    </div>

                    <strong>
                        Tidak Ada Kelas Hari Ini
                    </strong>

                    <p>
                        Belum ada jadwal mengajar untuk hari ini.
                        Silakan periksa kembali jadwal mendatang.
                    </p>

                </div>

            @endforelse

        </section>


        {{-- JADWAL MENDATANG --}}
        <section class="coach-panel">

            <div class="coach-panel-header">

                <div>
                    <h2 class="coach-panel-title">
                        Jadwal Mendatang
                    </h2>

                    <p class="coach-panel-subtitle">
                        Lima jadwal latihan terdekat setelah hari ini.
                    </p>
                </div>

                <span class="coach-panel-count">
                    {{ $upcomingScheduleCount }} Total
                </span>

            </div>


            @forelse ($upcomingSchedules as $schedule)

                @if ($loop->first)
                    <div class="coach-schedule-list">
                @endif

                    <article class="coach-schedule-item">

                        <div class="coach-schedule-date">

                            <span class="coach-schedule-day">
                                {{ $schedule->class_date->format('d') }}
                            </span>

                            <span class="coach-schedule-month">
                                {{ $schedule->class_date->translatedFormat('M') }}
                            </span>

                        </div>


                        <div class="coach-schedule-details">

                            <h3 class="coach-schedule-name">
                                {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                            </h3>

                            <div class="coach-schedule-meta">

                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                                        <path d="M16 3v4M8 3v4M3 10h18"/>
                                    </svg>

                                    {{ $schedule->class_date->translatedFormat('l, d M Y') }}
                                </span>

                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                    {{ $formatTime($schedule->start_time) }}
                                    –
                                    {{ $formatTime($schedule->end_time) }}
                                </span>

                                <span>
                                    {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}
                                </span>

                            </div>

                        </div>


                        <div class="coach-schedule-participants">

                            <strong>
                                {{ $schedule->active_bookings_count }}
                            </strong>

                            <span>
                                booking aktif
                            </span>

                        </div>

                    </article>

                @if ($loop->last)
                    </div>
                @endif

            @empty

                <div class="coach-empty">

                    <div class="coach-empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>
                    </div>

                    <strong>
                        Belum Ada Jadwal Mendatang
                    </strong>

                    <p>
                        Jadwal latihan berikutnya akan muncul
                        ketika admin menambahkan jadwal baru.
                    </p>

                </div>

            @endforelse

        </section>

    </div>


    {{-- PROFIL COACH --}}
    <aside class="coach-side-column">

        <section class="coach-panel coach-profile">

            <div class="coach-profile-avatar">
                {{ $coachInitial ?: 'C' }}
            </div>

            <div class="coach-profile-name">
                {{ $coachName }}
            </div>

            <div class="coach-profile-specialization">
                {{ $coach->specialization ?: 'Pelatih Ragen Swim Course' }}
            </div>


            @if ($coach->bio)
                <p class="coach-profile-bio">
                    {{ $coach->bio }}
                </p>
            @endif


            <div class="coach-profile-stats">

                <div class="coach-profile-stat">

                    <strong>
                        {{ $coach->experience_years ?? 0 }}
                    </strong>

                    <span>
                        Tahun Pengalaman
                    </span>

                </div>


                <div class="coach-profile-stat">

                    <strong>
                        {{ $upcomingScheduleCount }}
                    </strong>

                    <span>
                        Jadwal Mendatang
                    </span>

                </div>

            </div>


            <div class="coach-guide">

                <strong>
                    Informasi untuk Coach
                </strong>

                <p>
                    Pastikan Anda memeriksa jadwal latihan
                    sebelum sesi dimulai. Gunakan menu yang
                    tersedia pada dashboard untuk mengelola
                    aktivitas kelas sesuai kewenangan Anda.
                </p>

            </div>

        </section>

    </aside>

</div>

@endsection
