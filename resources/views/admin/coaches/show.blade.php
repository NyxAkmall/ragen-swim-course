@extends('layouts.admin')

@section('title', 'Detail Coach')
@section('topbar-title', 'Detail Coach')

@section('content')

@php
    $coachName = $coach->user?->name ?? 'Coach';

    $coachInitials = collect(
        preg_split('/\s+/', trim($coachName))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return strtoupper(
                mb_substr($word, 0, 1)
            );
        })
        ->implode('');

    $latestSchedules = $coach->schedules
        ->take(8);
@endphp


{{-- =========================================================
    PAGE HEADER
========================================================== --}}

<div class="page-header">

    <div>

        <a
            href="{{ route('admin.coaches.index') }}"
            class="back-link"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M15 18L9 12L15 6"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

            Kembali ke Daftar Coach
        </a>

        <h1 class="page-title">
            Detail Coach
        </h1>

        <p class="page-description">
            Lihat informasi akun, profil, aktivitas mengajar,
            dan status coach Ragen Swim Course.
        </p>

    </div>


    <a
        href="{{ route('admin.coaches.edit', $coach) }}"
        class="btn-primary edit-button"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M13.5 6.5L17.5 10.5M5 19L8.5 18.3L18.5 8.3C19.3 7.5 19.3 6.2 18.5 5.4C17.7 4.6 16.4 4.6 15.6 5.4L5.6 15.4L5 19Z"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>

        Edit Coach
    </a>

</div>


{{-- =========================================================
    PROFILE HERO
========================================================== --}}

<div class="card coach-hero">

    <div class="coach-hero-main">

        <div class="coach-large-avatar">

            @if ($coach->photo)

                <img
                    src="{{ asset('storage/' . $coach->photo) }}"
                    alt="{{ $coachName }}"
                >

            @else

                {{ $coachInitials ?: 'C' }}

            @endif

        </div>


        <div class="coach-hero-content">

            <div class="coach-title-row">

                <h2>
                    {{ $coachName }}
                </h2>


                @if ($coach->status === 'active')

                    <span class="status-badge status-active">
                        <span class="status-dot"></span>
                        Aktif
                    </span>

                @else

                    <span class="status-badge status-inactive">
                        <span class="status-dot"></span>
                        Tidak Aktif
                    </span>

                @endif

            </div>


            <div class="coach-code">
                Coach #{{ $coach->id }}
            </div>


            @if ($coach->specialization)

                <div class="specialization">
                    {{ $coach->specialization }}
                </div>

            @endif


            <div class="coach-contact-list">

                <div class="coach-contact-item">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />

                        <path
                            d="M4 7L12 13L20 7"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    {{ $coach->user?->email ?? '-' }}

                </div>


                <div class="coach-contact-item">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M7 3H10L11.5 7L9.5 8.5C10.6 11.1 12.9 13.4 15.5 14.5L17 12.5L21 14V17C21 18.7 19.7 20 18 20C10.3 20 4 13.7 4 6C4 4.3 5.3 3 7 3Z"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    {{ $coach->user?->phone ?? '-' }}

                </div>

            </div>

        </div>

    </div>


    <div class="coach-hero-meta">

        <div class="hero-meta-item">
            <span>
                Pengalaman
            </span>

            <strong>
                {{ $coach->experience_years ?? 0 }} Tahun
            </strong>
        </div>

        <div class="hero-meta-divider"></div>

        <div class="hero-meta-item">
            <span>
                Terdaftar
            </span>

            <strong>
                {{ $coach->created_at?->format('d M Y') ?? '-' }}
            </strong>
        </div>

    </div>

</div>


{{-- =========================================================
    STATISTICS
========================================================== --}}

<div class="coach-statistics">

    <div class="stat-card">

        <div class="stat-label">
            Total Jadwal
        </div>

        <div class="stat-value">
            {{ number_format($coach->schedules_count) }}
        </div>

        <div class="stat-description">
            Jadwal kelas yang terhubung dengan coach.
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Perkembangan
        </div>

        <div class="stat-value">
            {{ number_format($coach->progress_records_count) }}
        </div>

        <div class="stat-description">
            Catatan perkembangan peserta yang dibuat.
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Assessment
        </div>

        <div class="stat-value">
            {{ number_format($coach->assessments_count) }}
        </div>

        <div class="stat-description">
            Assessment peserta yang terhubung.
        </div>

    </div>

</div>


{{-- =========================================================
    MAIN GRID
========================================================== --}}

<div class="detail-grid">

    <div class="detail-main">

        {{-- =====================================================
            BIO
        ====================================================== --}}

        <div class="card information-card">

            <div class="card-heading">

                <div>
                    <h2>
                        Profil Coach
                    </h2>

                    <p>
                        Informasi profesional coach.
                    </p>
                </div>

            </div>


            <div class="information-content">

                <div class="information-row">

                    <div class="information-label">
                        Nama Lengkap
                    </div>

                    <div class="information-value">
                        {{ $coachName }}
                    </div>

                </div>


                <div class="information-row">

                    <div class="information-label">
                        Spesialisasi
                    </div>

                    <div class="information-value">
                        {{ $coach->specialization ?: 'Belum diisi' }}
                    </div>

                </div>


                <div class="information-row">

                    <div class="information-label">
                        Pengalaman
                    </div>

                    <div class="information-value">
                        {{ $coach->experience_years ?? 0 }} tahun
                    </div>

                </div>


                <div class="information-row bio-row">

                    <div class="information-label">
                        Bio
                    </div>

                    <div class="information-value bio-value">
                        {{ $coach->bio ?: 'Belum ada bio coach.' }}
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            SCHEDULES
        ====================================================== --}}

        <div class="card schedule-card">

            <div class="card-heading">

                <div>
                    <h2>
                        Riwayat Jadwal Kelas
                    </h2>

                    <p>
                        Maksimal 8 jadwal terbaru coach.
                    </p>
                </div>

                <div class="schedule-count">
                    {{ number_format($coach->schedules_count) }}
                    Jadwal
                </div>

            </div>


            @if ($latestSchedules->isNotEmpty())

                <div class="schedule-list">

                    @foreach ($latestSchedules as $schedule)

                        <div class="schedule-item">

                            <div class="schedule-date">

                                <span class="schedule-day">
                                    {{ $schedule->class_date?->format('d') ?? '-' }}
                                </span>

                                <span class="schedule-month">
                                    {{ $schedule->class_date?->format('M Y') ?? '-' }}
                                </span>

                            </div>


                            <div class="schedule-content">

                                <div class="schedule-title">
                                    {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                                </div>

                                <div class="schedule-details">

                                    <span>
                                        {{ $schedule->program?->name ?? 'Program tidak tersedia' }}
                                    </span>

                                    <span class="detail-separator">
                                        •
                                    </span>

                                    <span>
                                        {{ $schedule->location?->name ?? 'Lokasi tidak tersedia' }}
                                    </span>

                                </div>


                                <div class="schedule-time">

                                    {{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}

                                    -

                                    {{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}

                                    WIB

                                </div>

                            </div>


                            <div class="schedule-side">

                                @if ($schedule->status === 'scheduled')

                                    <span class="schedule-status scheduled">
                                        Terjadwal
                                    </span>

                                @elseif ($schedule->status === 'completed')

                                    <span class="schedule-status completed">
                                        Selesai
                                    </span>

                                @else

                                    <span class="schedule-status cancelled">
                                        Dibatalkan
                                    </span>

                                @endif


                                <span class="booking-count">
                                    {{ number_format($schedule->bookings_count) }}
                                    booking
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-schedule">

                    <div class="empty-schedule-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="3.5"
                                y="5"
                                width="17"
                                height="15"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />

                            <path
                                d="M7 3V7M17 3V7M3.5 10H20.5"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <h3>
                        Belum ada jadwal
                    </h3>

                    <p>
                        Coach ini belum memiliki jadwal kelas.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        SIDEBAR DETAIL
    ========================================================== --}}

    <div class="detail-sidebar">

        {{-- ACCOUNT --}}
        <div class="card side-card">

            <div class="side-card-heading">
                Akun Coach
            </div>


            <div class="account-row">

                <span>
                    Email
                </span>

                <strong>
                    {{ $coach->user?->email ?? '-' }}
                </strong>

            </div>


            <div class="account-row">

                <span>
                    Nomor Telepon
                </span>

                <strong>
                    {{ $coach->user?->phone ?? '-' }}
                </strong>

            </div>


            <div class="account-row">

                <span>
                    Role
                </span>

                <strong>
                    Coach
                </strong>

            </div>


            <div class="account-row">

                <span>
                    Status Profil
                </span>

                <strong class="{{ $coach->status === 'active' ? 'text-success' : 'text-danger' }}">
                    {{ $coach->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </strong>

            </div>


            <div class="account-row">

                <span>
                    Status Akun
                </span>

                <strong class="{{ $coach->user?->status === 'active' ? 'text-success' : 'text-danger' }}">
                    {{ $coach->user?->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </strong>

            </div>

        </div>


        {{-- STATUS MANAGEMENT --}}
        <div class="card side-card">

            <div class="side-card-heading">
                Kelola Status
            </div>


            @if ($coach->status === 'active')

                <div class="status-information status-information-warning">

                    <strong>
                        Coach sedang aktif
                    </strong>

                    <p>
                        Jika dinonaktifkan, coach tidak dapat
                        mengakses dashboard coach sampai
                        diaktifkan kembali.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.coaches.status.update', $coach) }}"
                    onsubmit="return confirm('Yakin ingin menonaktifkan coach ini? Coach tidak akan dapat login sampai diaktifkan kembali.')"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="inactive"
                    >

                    <button
                        type="submit"
                        class="status-action-button danger-button"
                    >
                        Nonaktifkan Coach
                    </button>

                </form>

            @else

                <div class="status-information status-information-success">

                    <strong>
                        Coach sedang tidak aktif
                    </strong>

                    <p>
                        Aktifkan kembali agar coach dapat
                        mengakses sistem dan menjalankan
                        aktivitas mengajar.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.coaches.status.update', $coach) }}"
                    onsubmit="return confirm('Aktifkan kembali coach ini?')"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="active"
                    >

                    <button
                        type="submit"
                        class="status-action-button success-button"
                    >
                        Aktifkan Coach
                    </button>

                </form>

            @endif

        </div>

	@include('admin.coaches.password-reset-action')

    </div>

</div>

@endsection


@push('styles')

<style>
    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .back-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        margin-bottom: 10px;

        color: #0284c7;

        text-decoration: none;

        font-size: 12px;
        font-weight: 750;
    }

    .back-link:hover {
        color: #0369a1;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }

    .edit-button {
        flex-shrink: 0;
    }

    .edit-button svg {
        width: 17px;
        height: 17px;
    }


    /*
    |--------------------------------------------------------------------------
    | Coach Hero
    |--------------------------------------------------------------------------
    */

    .coach-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 25px;

        padding: 26px;

        margin-bottom: 20px;
    }

    .coach-hero-main {
        display: flex;
        align-items: center;

        gap: 20px;

        min-width: 0;
    }

    .coach-large-avatar {
        width: 86px;
        height: 86px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        overflow: hidden;

        border-radius: 22px;

        background:
            linear-gradient(
                135deg,
                #0284c7,
                #06b6d4
            );

        color: #ffffff;

        font-size: 27px;
        font-weight: 850;
    }

    .coach-large-avatar img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }

    .coach-hero-content {
        min-width: 0;
    }

    .coach-title-row {
        display: flex;
        align-items: center;

        flex-wrap: wrap;

        gap: 10px;
    }

    .coach-title-row h2 {
        color: var(--navy);

        font-size: 23px;
        font-weight: 850;

        letter-spacing: -0.4px;
    }

    .coach-code {
        margin-top: 4px;

        color: var(--text-light);

        font-size: 11px;
        font-weight: 700;
    }

    .specialization {
        display: inline-flex;

        margin-top: 10px;

        padding: 6px 9px;

        border-radius: 8px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 11px;
        font-weight: 750;
    }

    .coach-contact-list {
        display: flex;
        align-items: center;

        flex-wrap: wrap;

        gap: 13px;

        margin-top: 13px;
    }

    .coach-contact-item {
        display: flex;
        align-items: center;

        gap: 6px;

        color: var(--text-soft);

        font-size: 11px;
    }

    .coach-contact-item svg {
        width: 15px;
        height: 15px;

        color: #0284c7;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;

        gap: 6px;

        padding: 6px 9px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 800;
    }

    .status-dot {
        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    .status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }

    .coach-hero-meta {
        display: flex;
        align-items: center;

        flex-shrink: 0;

        gap: 18px;

        padding-left: 25px;

        border-left: 1px solid var(--border);
    }

    .hero-meta-item {
        min-width: 95px;
    }

    .hero-meta-item span {
        display: block;

        color: var(--text-soft);

        font-size: 10px;
        font-weight: 650;
    }

    .hero-meta-item strong {
        display: block;

        margin-top: 4px;

        color: var(--navy);

        font-size: 13px;
        font-weight: 800;
    }

    .hero-meta-divider {
        width: 1px;
        height: 38px;

        background: var(--border);
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .coach-statistics {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 16px;

        margin-bottom: 20px;
    }

    .stat-card {
        padding: 19px 20px;

        border: 1px solid var(--border);
        border-radius: 15px;

        background: var(--white);

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .stat-label {
        color: var(--text-soft);

        font-size: 11px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 5px;

        color: var(--navy);

        font-size: 26px;
        font-weight: 850;

        letter-spacing: -0.5px;
    }

    .stat-description {
        margin-top: 5px;

        color: var(--text-light);

        font-size: 10px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Detail Grid
    |--------------------------------------------------------------------------
    */

    .detail-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            320px;

        align-items: start;

        gap: 20px;
    }

    .detail-main,
    .detail-sidebar {
        display: flex;
        flex-direction: column;

        gap: 20px;
    }


    /*
    |--------------------------------------------------------------------------
    | Card Heading
    |--------------------------------------------------------------------------
    */

    .card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 19px 21px;

        border-bottom: 1px solid var(--border);
    }

    .card-heading h2 {
        color: var(--navy);

        font-size: 15px;
        font-weight: 800;
    }

    .card-heading p {
        margin-top: 4px;

        color: var(--text-soft);

        font-size: 11px;
    }


    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    */

    .information-content {
        padding: 5px 21px;
    }

    .information-row {
        display: grid;
        grid-template-columns:
            160px
            minmax(0, 1fr);

        gap: 20px;

        padding: 15px 0;

        border-bottom: 1px solid #f1f5f9;
    }

    .information-row:last-child {
        border-bottom: 0;
    }

    .information-label {
        color: var(--text-soft);

        font-size: 11px;
        font-weight: 650;
    }

    .information-value {
        color: var(--text);

        font-size: 12px;
        font-weight: 700;

        line-height: 1.6;
    }

    .bio-value {
        font-weight: 500;

        white-space: pre-line;
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    .schedule-count {
        padding: 5px 8px;

        border-radius: 7px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 10px;
        font-weight: 800;
    }

    .schedule-list {
        padding: 0 20px;
    }

    .schedule-item {
        display: grid;
        grid-template-columns:
            62px
            minmax(0, 1fr)
            auto;

        align-items: center;

        gap: 15px;

        padding: 17px 0;

        border-bottom: 1px solid #f1f5f9;
    }

    .schedule-item:last-child {
        border-bottom: 0;
    }

    .schedule-date {
        width: 58px;

        padding: 8px 5px;

        border-radius: 10px;

        background: #f0f9ff;

        text-align: center;
    }

    .schedule-day {
        display: block;

        color: #0369a1;

        font-size: 18px;
        font-weight: 850;
    }

    .schedule-month {
        display: block;

        margin-top: 1px;

        color: #0284c7;

        font-size: 8px;
        font-weight: 750;

        text-transform: uppercase;
    }

    .schedule-content {
        min-width: 0;
    }

    .schedule-title {
        overflow: hidden;

        color: var(--text);

        font-size: 12px;
        font-weight: 800;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .schedule-details {
        display: flex;
        align-items: center;

        flex-wrap: wrap;

        gap: 5px;

        margin-top: 4px;

        color: var(--text-soft);

        font-size: 10px;
    }

    .detail-separator {
        color: #cbd5e1;
    }

    .schedule-time {
        margin-top: 5px;

        color: #0369a1;

        font-size: 10px;
        font-weight: 750;
    }

    .schedule-side {
        display: flex;
        align-items: flex-end;
        flex-direction: column;

        gap: 6px;
    }

    .schedule-status {
        display: inline-flex;

        padding: 5px 7px;

        border-radius: 999px;

        font-size: 9px;
        font-weight: 800;
    }

    .schedule-status.scheduled {
        background: #eff6ff;
        color: #2563eb;
    }

    .schedule-status.completed {
        background: #f0fdf4;
        color: #15803d;
    }

    .schedule-status.cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .booking-count {
        color: var(--text-light);

        font-size: 9px;
        font-weight: 650;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Schedule
    |--------------------------------------------------------------------------
    */

    .empty-schedule {
        padding: 45px 20px;

        text-align: center;
    }

    .empty-schedule-icon {
        width: 50px;
        height: 50px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 12px;

        border-radius: 14px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .empty-schedule-icon svg {
        width: 24px;
        height: 24px;
    }

    .empty-schedule h3 {
        color: var(--navy);

        font-size: 13px;
        font-weight: 800;
    }

    .empty-schedule p {
        margin-top: 5px;

        color: var(--text-soft);

        font-size: 11px;
    }


    /*
    |--------------------------------------------------------------------------
    | Sidebar Card
    |--------------------------------------------------------------------------
    */

    .side-card {
        padding: 20px;
    }

    .side-card-heading {
        margin-bottom: 16px;

        color: var(--navy);

        font-size: 14px;
        font-weight: 800;
    }

    .account-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 15px;

        padding: 11px 0;

        border-bottom: 1px solid #f1f5f9;
    }

    .account-row:last-child {
        border-bottom: 0;
    }

    .account-row span {
        flex-shrink: 0;

        color: var(--text-soft);

        font-size: 10px;
    }

    .account-row strong {
        max-width: 180px;

        overflow-wrap: anywhere;

        color: var(--text);

        text-align: right;

        font-size: 10px;
        font-weight: 750;
    }

    .account-row .text-success {
        color: #15803d;
    }

    .account-row .text-danger {
        color: #dc2626;
    }


    /*
    |--------------------------------------------------------------------------
    | Status Management
    |--------------------------------------------------------------------------
    */

    .status-information {
        padding: 13px;

        margin-bottom: 13px;

        border-radius: 10px;
    }

    .status-information strong {
        display: block;

        font-size: 11px;
        font-weight: 800;
    }

    .status-information p {
        margin-top: 5px;

        font-size: 10px;
        line-height: 1.55;
    }

    .status-information-warning {
        border: 1px solid #fde68a;

        background: #fffbeb;
        color: #a16207;
    }

    .status-information-success {
        border: 1px solid #bbf7d0;

        background: #f0fdf4;
        color: #15803d;
    }

    .status-action-button {
        width: 100%;
        min-height: 40px;

        border-radius: 9px;

        font-size: 11px;
        font-weight: 800;
    }

    .danger-button {
        border: 1px solid #fecaca;

        background: #ffffff;
        color: #dc2626;
    }

    .danger-button:hover {
        background: #fef2f2;
    }

    .success-button {
        border: 0;

        background: #15803d;
        color: #ffffff;
    }

    .success-button:hover {
        background: #166534;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1100px) {

        .coach-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .coach-hero-meta {
            width: 100%;

            padding-top: 18px;
            padding-left: 0;

            border-top: 1px solid var(--border);
            border-left: 0;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-sidebar {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }


    @media (max-width: 700px) {

        .page-header {
            display: flex;
            flex-direction: column;
        }

        .edit-button {
            width: 100%;
        }

        .coach-hero-main {
            align-items: flex-start;
            flex-direction: column;
        }

        .coach-large-avatar {
            width: 72px;
            height: 72px;

            border-radius: 18px;

            font-size: 22px;
        }

        .coach-title-row h2 {
            font-size: 20px;
        }

        .coach-hero-meta {
            align-items: flex-start;
            flex-direction: column;

            gap: 12px;
        }

        .hero-meta-divider {
            display: none;
        }

        .coach-statistics {
            grid-template-columns: 1fr;
        }

        .information-row {
            grid-template-columns: 1fr;

            gap: 5px;
        }

        .schedule-item {
            grid-template-columns:
                55px
                minmax(0, 1fr);
        }

        .schedule-side {
            grid-column: 2;

            align-items: flex-start;
            flex-direction: row;

            flex-wrap: wrap;
        }

        .detail-sidebar {
            display: flex;
        }
    }
</style>

@endpush
