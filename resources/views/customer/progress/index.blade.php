@extends('layouts.customer')

@section('title', 'Perkembangan Peserta')
@section('topbar-title', 'Perkembangan')

@push('styles')
<style>
    .progress-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .progress-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
    }

    .progress-header-copy {
        min-width: 0;
    }

    .progress-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .progress-description {
        max-width: 720px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.7;
    }

    .participant-filter {
        width: 290px;
        flex-shrink: 0;
    }

    .participant-filter-label {
        display: block;
        margin-bottom: 7px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .participant-select {
        width: 100%;
        height: 46px;
        padding: 0 38px 0 14px;
        border: 1px solid var(--border);
        border-radius: 11px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .participant-select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .empty-state {
        padding: 60px 30px;
        text-align: center;
    }

    .empty-icon {
        width: 68px;
        height: 68px;
        margin: 0 auto 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        background: var(--sky-light);
        color: var(--blue);
    }

    .empty-icon svg {
        width: 32px;
        height: 32px;
    }

    .empty-title {
        color: var(--navy);
        font-size: 18px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 500px;
        margin: 8px auto 0;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.7;
    }

    .empty-action {
        margin-top: 20px;
    }

    .participant-hero {
        position: relative;
        overflow: hidden;
        padding: 26px;
        border-radius: 18px;
        background:
            radial-gradient(
                circle at 90% 15%,
                rgba(103, 232, 249, 0.2),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #082f49 0%,
                #075985 55%,
                #0284c7 100%
            );
        color: var(--white);
    }

    .participant-hero::after {
        content: "";
        position: absolute;
        right: -45px;
        bottom: -75px;
        width: 210px;
        height: 210px;
        border: 1px solid rgba(255, 255, 255, 0.09);
        border-radius: 50%;
    }

    .participant-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .participant-profile {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .participant-avatar {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 17px;
        background: rgba(255, 255, 255, 0.12);
        color: var(--white);
        font-size: 19px;
        font-weight: 850;
    }

    .participant-name {
        overflow: hidden;
        font-size: 21px;
        font-weight: 850;
        letter-spacing: -0.4px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .participant-meta {
        margin-top: 5px;
        color: rgba(255, 255, 255, 0.68);
        font-size: 12px;
        line-height: 1.5;
    }

    .hero-level {
        min-width: 145px;
        padding: 13px 16px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 13px;
        background: rgba(255, 255, 255, 0.09);
        text-align: right;
    }

    .hero-level-label {
        color: rgba(255, 255, 255, 0.58);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .hero-level-value {
        margin-top: 5px;
        font-size: 17px;
        font-weight: 850;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;
        padding: 21px;
    }

    .summary-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .summary-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.55px;
        text-transform: uppercase;
    }

    .summary-icon {
        width: 39px;
        height: 39px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: var(--sky-light);
        color: var(--blue);
    }

    .summary-icon svg {
        width: 19px;
        height: 19px;
    }

    .summary-value {
        margin-top: 10px;
        color: var(--navy);
        font-size: 26px;
        font-weight: 850;
        letter-spacing: -0.6px;
    }

    .summary-value small {
        color: var(--text-soft);
        font-size: 13px;
        font-weight: 700;
    }

    .summary-note {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-top: 3px;
    }

    .section-title {
        color: var(--navy);
        font-size: 18px;
        font-weight: 850;
        letter-spacing: -0.3px;
    }

    .section-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .latest-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(280px, 0.75fr);
        gap: 18px;
        align-items: start;
    }

    .skills-card {
        padding: 24px;
    }

    .skills-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding-bottom: 19px;
        border-bottom: 1px solid var(--border);
    }

    .assessment-title {
        color: var(--navy);
        font-size: 17px;
        font-weight: 850;
    }

    .assessment-meta {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .assessment-score {
        flex-shrink: 0;
        padding: 10px 13px;
        border-radius: 11px;
        background: var(--sky-light);
        color: var(--blue-dark);
        text-align: center;
    }

    .assessment-score-value {
        font-size: 18px;
        font-weight: 850;
    }

    .assessment-score-label {
        margin-top: 2px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .skills-list {
        display: grid;
        gap: 18px;
        margin-top: 22px;
    }

    .skill-row {
        display: grid;
        grid-template-columns: 150px minmax(0, 1fr) 42px;
        align-items: center;
        gap: 14px;
    }

    .skill-name {
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .skill-track {
        position: relative;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #e2e8f0;
    }

    .skill-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(
            90deg,
            var(--blue),
            var(--aqua)
        );
    }

    .skill-score {
        color: var(--navy);
        font-size: 12px;
        font-weight: 850;
        text-align: right;
    }

    .skill-empty {
        color: var(--text-light);
    }

    .coach-card {
        overflow: hidden;
    }

    .coach-card-header {
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .coach-card-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 850;
    }

    .coach-card-body {
        padding: 20px;
    }

    .coach-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .coach-avatar {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--sky-light);
        color: var(--blue);
        font-size: 13px;
        font-weight: 850;
    }

    .coach-name {
        color: var(--text);
        font-size: 13px;
        font-weight: 800;
    }

    .coach-role {
        margin-top: 2px;
        color: var(--text-soft);
        font-size: 10px;
    }

    .coach-notes {
        margin-top: 17px;
        padding: 15px;
        border-radius: 11px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        line-height: 1.7;
    }

    .coach-notes-title {
        margin-bottom: 6px;
        color: var(--navy);
        font-size: 10px;
        font-weight: 850;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .class-info {
        display: grid;
        gap: 12px;
        margin-top: 18px;
        padding-top: 17px;
        border-top: 1px solid var(--border);
    }

    .class-info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .class-info-label {
        color: var(--text-soft);
        font-size: 10px;
    }

    .class-info-value {
        max-width: 170px;
        color: var(--text);
        font-size: 10px;
        font-weight: 750;
        line-height: 1.5;
        text-align: right;
    }

    .history-list {
        display: grid;
        gap: 14px;
    }

    .history-card {
        padding: 20px;
    }

    .history-main {
        display: grid;
        grid-template-columns: 120px minmax(0, 1fr) auto;
        align-items: center;
        gap: 20px;
    }

    .history-date {
        padding-right: 18px;
        border-right: 1px solid var(--border);
    }

    .history-date-day {
        color: var(--navy);
        font-size: 13px;
        font-weight: 850;
    }

    .history-date-time {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 10px;
    }

    .history-content {
        min-width: 0;
    }

    .history-title {
        overflow: hidden;
        color: var(--text);
        font-size: 13px;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .history-meta {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 10px;
        line-height: 1.5;
    }

    .history-badges {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .level-badge {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        background: var(--sky-light);
        color: var(--blue-dark);
        font-size: 10px;
        font-weight: 800;
    }

    .score-badge {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        background: var(--success-bg);
        color: var(--success);
        font-size: 10px;
        font-weight: 850;
    }

    @media (max-width: 1100px) {
        .latest-grid {
            grid-template-columns: 1fr;
        }

        .coach-card {
            width: 100%;
        }
    }

    @media (max-width: 850px) {
        .progress-header {
            display: block;
        }

        .participant-filter {
            width: 100%;
            margin-top: 18px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .participant-hero-content {
            align-items: flex-start;
        }

        .hero-level {
            min-width: 130px;
        }
    }

    @media (max-width: 650px) {
        .progress-title {
            font-size: 24px;
        }

        .participant-hero {
            padding: 21px;
        }

        .participant-hero-content {
            display: block;
        }

        .participant-profile {
            align-items: flex-start;
        }

        .participant-name {
            white-space: normal;
        }

        .hero-level {
            width: 100%;
            margin-top: 18px;
            text-align: left;
        }

        .skills-card {
            padding: 19px;
        }

        .skills-card-header {
            display: block;
        }

        .assessment-score {
            display: inline-block;
            margin-top: 13px;
        }

        .skill-row {
            grid-template-columns: 110px minmax(0, 1fr) 35px;
            gap: 10px;
        }

        .history-main {
            display: block;
        }

        .history-date {
            padding: 0 0 10px;
            border-right: 0;
            border-bottom: 1px solid var(--border);
        }

        .history-content {
            margin-top: 12px;
        }

        .history-badges {
            justify-content: flex-start;
            margin-top: 13px;
        }
    }
</style>
@endpush


@section('content')

@php
    $participantInitials = '';

    if ($selectedParticipant) {
        $participantName = $selectedParticipant->full_name ?? 'Peserta';

        $participantInitials = collect(
            preg_split('/\s+/', trim($participantName))
        )
            ->filter()
            ->take(2)
            ->map(function ($word) {
                return strtoupper(
                    mb_substr($word, 0, 1)
                );
            })
            ->implode('');
    }

    $latestCoachName =
        $latestProgress?->coach?->user?->name
        ?? 'Pelatih';

    $latestCoachInitials = collect(
        preg_split('/\s+/', trim($latestCoachName))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return strtoupper(
                mb_substr($word, 0, 1)
            );
        })
        ->implode('');

    $latestSchedule =
        $latestProgress?->booking?->classSchedule;

    $latestProgram =
        $latestSchedule?->program;

    $latestLocation =
        $latestSchedule?->location;

    $skills = $latestProgress
        ? [
            [
                'label' => 'Adaptasi Air',
                'score' => $latestProgress->water_adaptation,
            ],
            [
                'label' => 'Mengapung',
                'score' => $latestProgress->floating,
            ],
            [
                'label' => 'Pernapasan',
                'score' => $latestProgress->breathing,
            ],
            [
                'label' => 'Gerakan Kaki',
                'score' => $latestProgress->kicking,
            ],
            [
                'label' => 'Gaya Bebas',
                'score' => $latestProgress->freestyle,
            ],
            [
                'label' => 'Gaya Dada',
                'score' => $latestProgress->breaststroke,
            ],
        ]
        : [];
@endphp


<div class="progress-page">

    <div class="progress-header">

        <div class="progress-header-copy">

            <h1 class="progress-title">
                Perkembangan Peserta
            </h1>

            <p class="progress-description">
                Pantau hasil penilaian kemampuan renang,
                level peserta, serta catatan yang diberikan
                oleh pelatih setelah sesi latihan.
            </p>

        </div>


        @if ($participants->isNotEmpty())

            <form
                method="GET"
                action="{{ route('customer.progress.index') }}"
                class="participant-filter"
            >

                <label
                    for="participant"
                    class="participant-filter-label"
                >
                    Pilih Peserta
                </label>

                <select
                    name="participant"
                    id="participant"
                    class="participant-select"
                    onchange="this.form.submit()"
                >

                    @foreach ($participants as $participant)

                        <option
                            value="{{ $participant->id }}"
                            @selected(
                                $selectedParticipant
                                && $selectedParticipant->id === $participant->id
                            )
                        >
                            {{ $participant->full_name }}
                        </option>

                    @endforeach

                </select>

            </form>

        @endif

    </div>


    @if ($participants->isEmpty())

        <div class="card empty-state">

            <div class="empty-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="12"
                        cy="8"
                        r="3.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M5 20C5.5 15.9 8 14 12 14C16 14 18.5 15.9 19 20"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

            <div class="empty-title">
                Belum Ada Peserta
            </div>

            <div class="empty-description">
                Tambahkan data peserta terlebih dahulu
                agar laporan perkembangan dapat ditampilkan.
            </div>

            <div class="empty-action">

                <a
                    href="{{ route('customer.participants.create') }}"
                    class="btn-primary"
                >
                    Tambah Peserta
                </a>

            </div>

        </div>

    @elseif ($selectedParticipant)

        <section class="participant-hero">

            <div class="participant-hero-content">

                <div class="participant-profile">

                    <div class="participant-avatar">
                        {{ $participantInitials ?: 'PS' }}
                    </div>

                    <div>

                        <div class="participant-name">
                            {{ $selectedParticipant->full_name }}
                        </div>

                        <div class="participant-meta">
                            Laporan perkembangan kemampuan renang
                            berdasarkan penilaian pelatih.
                        </div>

                    </div>

                </div>


                <div class="hero-level">

                    <div class="hero-level-label">
                        Level Saat Ini
                    </div>

                    <div class="hero-level-value">
                        {{ $statistics['latest_level'] }}
                    </div>

                </div>

            </div>

        </section>


        <div class="summary-grid">

            <div class="card summary-card">

                <div class="summary-card-top">

                    <div class="summary-label">
                        Rata-rata Kemampuan
                    </div>

                    <div class="summary-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4 17L9 12L13 15L20 7"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M15 7H20V12"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </div>

                </div>

                <div class="summary-value">
                    {{ number_format($statistics['latest_average'], 2, ',', '.') }}
                    <small>/ 5</small>
                </div>

                <div class="summary-note">
                    Berdasarkan penilaian terbaru.
                </div>

            </div>


            <div class="card summary-card">

                <div class="summary-card-top">

                    <div class="summary-label">
                        Total Penilaian
                    </div>

                    <div class="summary-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="5"
                                y="4"
                                width="14"
                                height="16"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M9 9H15M9 13H15M9 17H13"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                </div>

                <div class="summary-value">
                    {{ $statistics['total_assessments'] }}
                    <small>penilaian</small>
                </div>

                <div class="summary-note">
                    Seluruh evaluasi yang telah diberikan pelatih.
                </div>

            </div>


            <div class="card summary-card">

                <div class="summary-card-top">

                    <div class="summary-label">
                        Level Terbaru
                    </div>

                    <div class="summary-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M5 19V13M12 19V9M19 19V5"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <path
                                d="M3 20H21"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                </div>

                <div
                    class="summary-value"
                    style="font-size: 21px;"
                >
                    {{ $statistics['latest_level'] }}
                </div>

                <div class="summary-note">
                    Level berdasarkan evaluasi terakhir.
                </div>

            </div>

        </div>


        @if ($latestProgress)

            <div class="section-heading">

                <div>

                    <div class="section-title">
                        Penilaian Terbaru
                    </div>

                    <div class="section-description">
                        Detail kemampuan berdasarkan evaluasi
                        terakhir dari pelatih.
                    </div>

                </div>

            </div>


            <div class="latest-grid">

                <div class="card skills-card">

                    <div class="skills-card-header">

                        <div>

                            <div class="assessment-title">
                                {{ $latestSchedule?->title ?? 'Evaluasi Kemampuan Renang' }}
                            </div>

                            <div class="assessment-meta">

                                {{ $latestProgress->assessment_date
                                    ? $latestProgress->assessment_date->translatedFormat('d F Y')
                                    : '-' }}

                                @if ($latestProgram)
                                    · {{ $latestProgram->name }}
                                @endif

                            </div>

                        </div>


                        <div class="assessment-score">

                            <div class="assessment-score-value">
                                {{ number_format(
                                    $latestProgress->average_score,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </div>

                            <div class="assessment-score-label">
                                Rata-rata / 5
                            </div>

                        </div>

                    </div>


                    <div class="skills-list">

                        @foreach ($skills as $skill)

                            @php
                                $score = $skill['score'];

                                $percentage = $score
                                    ? min(100, max(0, ($score / 5) * 100))
                                    : 0;
                            @endphp

                            <div class="skill-row">

                                <div class="skill-name">
                                    {{ $skill['label'] }}
                                </div>

                                <div class="skill-track">

                                    <div
                                        class="skill-fill"
                                        style="width: {{ $percentage }}%;"
                                    ></div>

                                </div>

                                <div
                                    class="skill-score {{ $score === null ? 'skill-empty' : '' }}"
                                >
                                    {{ $score !== null ? $score . '/5' : '-' }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                <aside class="card coach-card">

                    <div class="coach-card-header">

                        <div class="coach-card-title">
                            Catatan Pelatih
                        </div>

                    </div>

                    <div class="coach-card-body">

                        <div class="coach-profile">

                            <div class="coach-avatar">
                                {{ $latestCoachInitials ?: 'PL' }}
                            </div>

                            <div>

                                <div class="coach-name">
                                    {{ $latestCoachName }}
                                </div>

                                <div class="coach-role">
                                    Pelatih Ragen Swim Course
                                </div>

                            </div>

                        </div>


                        <div class="coach-notes">

                            <div class="coach-notes-title">
                                Evaluasi
                            </div>

                            {{ $latestProgress->coach_notes
                                ?: 'Belum ada catatan tambahan dari pelatih.' }}

                        </div>


                        <div class="class-info">

                            <div class="class-info-row">

                                <div class="class-info-label">
                                    Level
                                </div>

                                <div class="class-info-value">
                                    {{ $latestProgress->level_label }}
                                </div>

                            </div>


                            <div class="class-info-row">

                                <div class="class-info-label">
                                    Pelatih
                                </div>

                                <div class="class-info-value">
                                    {{ $latestCoachName }}
                                </div>

                            </div>


                            <div class="class-info-row">

                                <div class="class-info-label">
                                    Program
                                </div>

                                <div class="class-info-value">
                                    {{ $latestProgram?->name ?? '-' }}
                                </div>

                            </div>


                            <div class="class-info-row">

                                <div class="class-info-label">
                                    Lokasi
                                </div>

                                <div class="class-info-value">
                                    {{ $latestLocation?->name ?? '-' }}
                                </div>

                            </div>


                            <div class="class-info-row">

                                <div class="class-info-label">
                                    Tanggal
                                </div>

                                <div class="class-info-value">

                                    {{ $latestProgress->assessment_date
                                        ? $latestProgress->assessment_date->translatedFormat('d F Y')
                                        : '-' }}

                                </div>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>


            <div class="section-heading">

                <div>

                    <div class="section-title">
                        Riwayat Perkembangan
                    </div>

                    <div class="section-description">
                        Riwayat evaluasi peserta dari setiap
                        sesi yang telah dinilai oleh pelatih.
                    </div>

                </div>

            </div>


            <div class="history-list">

                @foreach ($progressRecords as $progress)

                    @php
                        $historySchedule =
                            $progress->booking?->classSchedule;

                        $historyCoach =
                            $progress->coach?->user?->name
                            ?? 'Pelatih';
                    @endphp

                    <div class="card history-card">

                        <div class="history-main">

                            <div class="history-date">

                                <div class="history-date-day">

                                    {{ $progress->assessment_date
                                        ? $progress->assessment_date->translatedFormat('d M Y')
                                        : '-' }}

                                </div>

                                <div class="history-date-time">
                                    Penilaian sesi latihan
                                </div>

                            </div>


                            <div class="history-content">

                                <div class="history-title">
                                    {{ $historySchedule?->title
                                        ?? 'Evaluasi Kemampuan Renang' }}
                                </div>

                                <div class="history-meta">

                                    Pelatih:
                                    {{ $historyCoach }}

                                    @if ($historySchedule?->location)
                                        · {{ $historySchedule->location->name }}
                                    @endif

                                </div>

                            </div>


                            <div class="history-badges">

                                <span class="level-badge">
                                    {{ $progress->level_label }}
                                </span>

                                <span class="score-badge">
                                    {{ number_format(
                                        $progress->average_score,
                                        2,
                                        ',',
                                        '.'
                                    ) }}/5
                                </span>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="card empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M5 19V13M12 19V9M19 19V5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />

                        <path
                            d="M3 20H21"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

                <div class="empty-title">
                    Belum Ada Penilaian
                </div>

                <div class="empty-description">
                    {{ $selectedParticipant->full_name }}
                    belum memiliki laporan perkembangan.
                    Penilaian akan tampil setelah peserta
                    mengikuti sesi latihan dan pelatih
                    mencatat hasil perkembangannya.
                </div>

            </div>

        @endif

    @endif

</div>

@endsection
