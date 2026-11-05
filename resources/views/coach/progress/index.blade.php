@extends('layouts.coach')

@section('title', 'Perkembangan Peserta | Ragen Swim Course')

@section('topbar-title', 'Perkembangan Peserta')

@section(
    'topbar-subtitle',
    'Lihat riwayat perkembangan peserta yang Anda latih.'
)

@section('content')

<style>

    .progress-index {
        max-width: 1250px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-header h1 {
        margin: 0 0 7px;
        color: #07355f;
        font-size: 28px;
        font-weight: 800;
        line-height: 1.2;
    }

    .page-header p {
        margin: 0;
        color: #70859a;
        font-size: 13px;
        line-height: 1.6;
    }

    .coach-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border: 1px solid #d8eaf2;
        border-radius: 12px;
        background: #ffffff;
        color: #315875;
        font-size: 12px;
        font-weight: 700;
    }

    .coach-badge-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #e9f8fb;
        color: #0785cc;
        font-size: 12px;
        font-weight: 800;
    }

    .statistics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 24px;
    }

    .stat-card {
        padding: 20px;
        border: 1px solid #dce9f1;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(8, 60, 96, .04);
    }

    .stat-label {
        margin-bottom: 9px;
        color: #8197a8;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .stat-value {
        color: #07355f;
        font-size: 27px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-description {
        margin-top: 8px;
        color: #8ca0af;
        font-size: 11px;
    }

    .content-card {
        overflow: hidden;
        border: 1px solid #dce9f1;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(8, 60, 96, .04);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 21px 24px;
        border-bottom: 1px solid #edf3f7;
    }

    .card-header h2 {
        margin: 0 0 4px;
        color: #07355f;
        font-size: 18px;
        font-weight: 800;
    }

    .card-header p {
        margin: 0;
        color: #7b91a5;
        font-size: 12px;
    }

    .record-count {
        padding: 8px 11px;
        border-radius: 999px;
        background: #eef8fc;
        color: #087fc1;
        font-size: 11px;
        font-weight: 800;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .progress-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .progress-table th {
        padding: 13px 18px;
        border-bottom: 1px solid #edf3f7;
        background: #fbfdfe;
        color: #8a9eae;
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    .progress-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #edf3f7;
        color: #315875;
        font-size: 12px;
        vertical-align: middle;
    }

    .progress-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .progress-table tbody tr:hover {
        background: #fbfdfe;
    }

    .participant-cell {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .participant-avatar {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #e9f8fb;
        color: #0785cc;
        font-size: 12px;
        font-weight: 800;
    }

    .participant-name {
        color: #123e61;
        font-size: 13px;
        font-weight: 800;
    }

    .participant-code {
        margin-top: 3px;
        color: #91a3b1;
        font-size: 10px;
    }

    .level-badge {
        display: inline-flex;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .level-beginner {
        color: #8a5a00;
        background: #fff5d9;
    }

    .level-basic {
        color: #0b7094;
        background: #e7f8fd;
    }

    .level-intermediate {
        color: #116d4b;
        background: #e8f8f0;
    }

    .level-advanced {
        color: #5e4a9c;
        background: #f0ecfb;
    }

    .score {
        color: #0785cc;
        font-size: 14px;
        font-weight: 800;
    }

    .score small {
        color: #9aabb8;
        font-size: 10px;
        font-weight: 600;
    }

    .date-text {
        color: #527089;
        white-space: nowrap;
    }

    .class-text strong {
        display: block;
        margin-bottom: 3px;
        color: #174664;
        font-size: 12px;
    }

    .class-text span {
        color: #91a3b1;
        font-size: 10px;
    }

    .notes-cell {
        max-width: 230px;
        color: #70859a;
        font-size: 11px;
        line-height: 1.5;
    }

    .empty-state {
        padding: 65px 25px;
        text-align: center;
    }

    .empty-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #eef8fc;
        color: #0785cc;
    }

    .empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .empty-state h3 {
        margin: 0 0 7px;
        color: #07355f;
        font-size: 17px;
    }

    .empty-state p {
        max-width: 480px;
        margin: 0 auto;
        color: #8197a8;
        font-size: 12px;
        line-height: 1.7;
    }

    .empty-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 18px;
        min-height: 40px;
        padding: 0 15px;
        border-radius: 10px;
        color: white;
        background: #0785cc;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .empty-action:hover {
        background: #056da8;
    }

    @media (max-width: 1050px) {

        .statistics {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media (max-width: 700px) {

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .page-header h1 {
            font-size: 23px;
        }

        .statistics {
            grid-template-columns: 1fr;
        }

        .card-header {
            align-items: flex-start;
            flex-direction: column;
        }

    }

</style>


@php

    $initials = collect(
        preg_split(
            '/\s+/',
            trim($coach->user?->name ?? 'Coach')
        )
    )
        ->filter()
        ->map(
            fn ($part) => strtoupper(
                substr($part, 0, 1)
            )
        )
        ->take(2)
        ->implode('');

@endphp


<div class="progress-index">


    {{-- =====================================================
         HEADER
         ===================================================== --}}

    <div class="page-header">

        <div>

            <h1>
                Perkembangan Peserta
            </h1>

            <p>
                Pantau hasil penilaian dan perkembangan
                peserta yang Anda latih.
            </p>

        </div>


        <div class="coach-badge">

            <div class="coach-badge-icon">
                {{ $initials ?: 'C' }}
            </div>

            <span>
                {{ $coach->user?->name ?? 'Coach' }}
            </span>

        </div>

    </div>


    {{-- =====================================================
         STATISTICS
         ===================================================== --}}

    <div class="statistics">


        <div class="stat-card">

            <div class="stat-label">
                Total Penilaian
            </div>

            <div class="stat-value">
                {{ $statistics['total_assessments'] }}
            </div>

            <div class="stat-description">
                Seluruh penilaian yang telah dicatat
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Peserta Dinilai
            </div>

            <div class="stat-value">
                {{ $statistics['total_participants'] }}
            </div>

            <div class="stat-description">
                Peserta unik yang telah dinilai
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Rata-rata Nilai
            </div>

            <div class="stat-value">
                {{ number_format($statistics['average_score'], 2) }}
            </div>

            <div class="stat-description">
                Dari skala kemampuan 1–5
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Level Lanjutan
            </div>

            <div class="stat-value">
                {{ $statistics['advanced_count'] }}
            </div>

            <div class="stat-description">
                Penilaian dengan level lanjutan
            </div>

        </div>


    </div>


    {{-- =====================================================
         DATA PERKEMBANGAN
         ===================================================== --}}

    <section class="content-card">


        <div class="card-header">

            <div>

                <h2>
                    Riwayat Perkembangan
                </h2>

                <p>
                    Daftar penilaian perkembangan peserta
                    berdasarkan sesi latihan.
                </p>

            </div>


            <div class="record-count">

                {{ $progressRecords->count() }}
                Penilaian

            </div>

        </div>


        @if ($progressRecords->isEmpty())

            <div class="empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            d="M4 19V9"
                        />

                        <path
                            d="M10 19V5"
                        />

                        <path
                            d="M16 19v-7"
                        />

                        <path
                            d="M22 19V3"
                        />

                    </svg>

                </div>


                <h3>
                    Belum Ada Penilaian
                </h3>


                <p>
                    Belum ada data perkembangan peserta
                    yang tercatat untuk Anda. Setelah peserta
                    hadir dan penilaian disimpan melalui detail
                    kelas, datanya akan muncul di halaman ini.
                </p>


                <a
                    href="{{ route('coach.schedules.index') }}"
                    class="empty-action"
                >
                    Buka Jadwal Saya
                </a>

            </div>

        @else

            <div class="table-wrapper">

                <table class="progress-table">

                    <thead>

                        <tr>

                            <th>
                                Peserta
                            </th>

                            <th>
                                Level
                            </th>

                            <th>
                                Nilai
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Kelas
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th>
                                Catatan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($progressRecords as $record)

                            @php

                                $participantName =
                                    $record->participant?->full_name
                                    ?? 'Peserta';

                                $participantInitials =
                                    collect(
                                        preg_split(
                                            '/\s+/',
                                            trim($participantName)
                                        )
                                    )
                                        ->filter()
                                        ->map(
                                            fn ($part) =>
                                                strtoupper(
                                                    substr(
                                                        $part,
                                                        0,
                                                        1
                                                    )
                                                )
                                        )
                                        ->take(2)
                                        ->implode('');

                                $levelClass =
                                    match ($record->level) {
                                        'beginner' =>
                                            'level-beginner',

                                        'basic' =>
                                            'level-basic',

                                        'intermediate' =>
                                            'level-intermediate',

                                        'advanced' =>
                                            'level-advanced',

                                        default => '',
                                    };

                                $notes =
                                    trim(
                                        (string)
                                        $record->coach_notes
                                    );

                            @endphp


                            <tr>


                                {{-- PESERTA --}}

                                <td>

                                    <div class="participant-cell">

                                        <div class="participant-avatar">

                                            {{ $participantInitials ?: 'P' }}

                                        </div>


                                        <div>

                                            <div class="participant-name">

                                                {{ $participantName }}

                                            </div>


                                            <div class="participant-code">

                                                {{ $record->booking?->booking_code ?? '-' }}

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- LEVEL --}}

                                <td>

                                    <span
                                        class="level-badge {{ $levelClass }}"
                                    >

                                        {{ $record->level_label }}

                                    </span>

                                </td>


                                {{-- NILAI --}}

                                <td>

                                    <span class="score">

                                        {{ number_format(
                                            $record->average_score,
                                            1
                                        ) }}

                                        <small>
                                            / 5
                                        </small>

                                    </span>

                                </td>


                                {{-- TANGGAL --}}

                                <td>

                                    <span class="date-text">

                                        {{ $record->assessment_date
                                            ? $record->assessment_date->translatedFormat('d F Y')
                                            : '-'
                                        }}

                                    </span>

                                </td>


                                {{-- KELAS --}}

                                <td>

                                    <div class="class-text">

                                        <strong>

                                            {{
                                                $record
                                                    ->booking
                                                    ?->classSchedule
                                                    ?->title
                                                ?? 'Kelas Renang'
                                            }}

                                        </strong>


                                        <span>

                                            {{
                                                $record
                                                    ->booking
                                                    ?->classSchedule
                                                    ?->program
                                                    ?->name
                                                ?? '-'
                                            }}

                                        </span>

                                    </div>

                                </td>


                                {{-- LOKASI --}}

                                <td>

                                    <span class="date-text">

                                        {{
                                            $record
                                                ->booking
                                                ?->classSchedule
                                                ?->location
                                                ?->name
                                            ?? '-'
                                        }}

                                    </span>

                                </td>


                                {{-- CATATAN --}}

                                <td>

                                    <div class="notes-cell">

                                        @if ($notes !== '')

                                            {{ $notes }}

                                        @else

                                            <span>
                                                Tidak ada catatan.
                                            </span>

                                        @endif

                                    </div>

                                </td>


                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif


    </section>


</div>

@endsection
