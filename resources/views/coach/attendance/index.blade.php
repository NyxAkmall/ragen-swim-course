@extends('layouts.coach')

@section('title', 'Kehadiran Peserta | Ragen Swim Course')
@section('topbar-title', 'Kehadiran Peserta')
@section('topbar-subtitle', 'Lihat riwayat kehadiran peserta dari kelas yang Anda tangani.')

@section('content')

<style>
    .attendance-page {
        max-width: 1250px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 22px;
    }

    .page-title {
        color: var(--navy);
        font-size: 23px;
        font-weight: 800;
        line-height: 1.3;
    }

    .page-description {
        color: var(--muted);
        font-size: 11px;
        line-height: 1.6;
        margin-top: 5px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: white;
        color: var(--ocean);
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .back-link:hover {
        background: var(--blue-soft);
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 13px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 17px;
        padding: 17px;
    }

    .summary-label {
        color: var(--muted);
        font-size: 9px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .summary-value {
        color: var(--navy);
        font-size: 22px;
        font-weight: 800;
    }

    .summary-description {
        color: var(--muted);
        font-size: 8px;
        margin-top: 4px;
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE PANEL
    |--------------------------------------------------------------------------
    */

    .table-panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 21px;
        overflow: hidden;
    }

    .table-header {
        padding: 21px 22px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
    }

    .table-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .table-description {
        color: var(--muted);
        font-size: 9px;
        line-height: 1.6;
        margin-top: 4px;
    }

    .total-badge {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border-radius: 999px;
        background: var(--aqua-soft);
        color: var(--ocean);
        font-size: 8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .attendance-table th {
        padding: 13px 16px;
        background: #f8fbfd;
        border-bottom: 1px solid var(--border);
        color: var(--muted);
        text-align: left;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    .attendance-table td {
        padding: 15px 16px;
        border-bottom: 1px solid #edf3f6;
        color: var(--text);
        font-size: 9px;
        vertical-align: middle;
    }

    .attendance-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .attendance-table tbody tr:hover {
        background: #fbfdfe;
    }

    /*
    |--------------------------------------------------------------------------
    | PARTICIPANT
    |--------------------------------------------------------------------------
    */

    .participant-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .participant-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--blue-soft);
        color: var(--ocean);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 10px;
        font-weight: 800;
    }

    .participant-name {
        color: var(--navy);
        font-size: 10px;
        font-weight: 800;
        line-height: 1.4;
    }

    .booking-code {
        color: var(--muted);
        font-size: 8px;
        margin-top: 3px;
    }

    /*
    |--------------------------------------------------------------------------
    | CLASS
    |--------------------------------------------------------------------------
    */

    .class-name {
        color: var(--navy);
        font-size: 9px;
        font-weight: 700;
        line-height: 1.5;
    }

    .program-name {
        color: var(--muted);
        font-size: 8px;
        margin-top: 3px;
    }

    /*
    |--------------------------------------------------------------------------
    | DATE
    |--------------------------------------------------------------------------
    */

    .date-value {
        color: var(--text);
        font-size: 9px;
        font-weight: 600;
        white-space: nowrap;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-present {
        background: #eaf8f0;
        color: #18834b;
    }

    .status-permission {
        background: #fff7e5;
        color: #a66a00;
    }

    .status-sick {
        background: #eef6ff;
        color: #2865a5;
    }

    .status-absent {
        background: #fff0f0;
        color: #c13b3b;
    }

    .status-default {
        background: #f2f6f8;
        color: var(--muted);
    }

    /*
    |--------------------------------------------------------------------------
    | NOTES
    |--------------------------------------------------------------------------
    */

    .notes {
        max-width: 220px;
        color: var(--muted);
        font-size: 8px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKED AT
    |--------------------------------------------------------------------------
    */

    .checked-at {
        color: var(--muted);
        font-size: 8px;
        line-height: 1.5;
        white-space: nowrap;
    }

    /*
    |--------------------------------------------------------------------------
    | EMPTY
    |--------------------------------------------------------------------------
    */

    .empty-state {
        padding: 55px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 14px;
        border-radius: 15px;
        background: var(--blue-soft);
        color: var(--ocean);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-icon svg {
        width: 24px;
        height: 24px;
    }

    .empty-title {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .empty-description {
        max-width: 400px;
        margin: 5px auto 0;
        color: var(--muted);
        font-size: 9px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    .pagination-wrapper {
        padding: 18px 22px;
        border-top: 1px solid var(--border);
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1100px) {

        .summary-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 750px) {

        .page-header {
            flex-direction: column;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-header {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {

        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


@php
    /*
     * Membuat inisial peserta untuk avatar.
     */
    $makeInitials = function ($name) {

        $words = collect(
            preg_split('/\s+/', trim($name))
        )
        ->filter()
        ->take(2);

        return $words
            ->map(function ($word) {
                return strtoupper(
                    substr($word, 0, 1)
                );
            })
            ->implode('');
    };
@endphp


<div class="attendance-page">

    {{-- ========================================================
         HEADER
         ======================================================== --}}

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Riwayat Kehadiran
            </h1>

            <p class="page-description">
                Daftar kehadiran peserta dari seluruh kelas
                yang ditangani oleh Anda.
            </p>

        </div>


        <a
            href="{{ route('coach.schedules.index') }}"
            class="back-link"
        >

            ← Kembali ke Jadwal

        </a>

    </div>


    {{-- ========================================================
         SUMMARY
         ======================================================== --}}

    <div class="summary-grid">

        {{-- TOTAL --}}

        <div class="summary-card">

            <div class="summary-label">
                Total Kehadiran
            </div>

            <div class="summary-value">
                {{ $totalAttendance }}
            </div>

            <div class="summary-description">
                seluruh catatan kehadiran
            </div>

        </div>


        {{-- HADIR --}}

        <div class="summary-card">

            <div class="summary-label">
                Hadir
            </div>

            <div class="summary-value">
                {{ $presentCount }}
            </div>

            <div class="summary-description">
                mengikuti latihan
            </div>

        </div>


        {{-- IZIN --}}

        <div class="summary-card">

            <div class="summary-label">
                Izin
            </div>

            <div class="summary-value">
                {{ $permissionCount }}
            </div>

            <div class="summary-description">
                tidak memotong sesi
            </div>

        </div>


        {{-- SAKIT --}}

        <div class="summary-card">

            <div class="summary-label">
                Sakit
            </div>

            <div class="summary-value">
                {{ $sickCount }}
            </div>

            <div class="summary-description">
                tidak memotong sesi
            </div>

        </div>


        {{-- TIDAK HADIR --}}

        <div class="summary-card">

            <div class="summary-label">
                Tidak Hadir
            </div>

            <div class="summary-value">
                {{ $absentCount }}
            </div>

            <div class="summary-description">
                menggunakan 1 sesi
            </div>

        </div>

    </div>


    {{-- ========================================================
         TABLE
         ======================================================== --}}

    <section class="table-panel">

        <div class="table-header">

            <div>

                <h2 class="table-title">
                    Riwayat Kehadiran Peserta
                </h2>

                <p class="table-description">
                    Data di bawah berasal dari kelas yang menjadi
                    tanggung jawab Coach yang sedang login.
                </p>

            </div>


            <span class="total-badge">

                {{ $attendances->total() }}

                catatan

            </span>

        </div>


        @if($attendances->isEmpty())

            {{-- =================================================
                 EMPTY STATE
                 ================================================= --}}

            <div class="empty-state">

                <div class="empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M9 11l3 3L22 4"/>

                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>

                </div>


                <div class="empty-title">

                    Belum ada data kehadiran

                </div>


                <p class="empty-description">

                    Data kehadiran akan muncul setelah Coach
                    melakukan pencatatan melalui Detail Kelas.

                </p>

            </div>

        @else

            {{-- =================================================
                 TABLE DATA
                 ================================================= --}}

            <div class="table-wrapper">

                <table class="attendance-table">

                    <thead>

                        <tr>

                            <th>
                                Peserta
                            </th>

                            <th>
                                Kelas
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Catatan
                            </th>

                            <th>
                                Dicatat
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($attendances as $attendance)

                            @php

                                $booking =
                                    $attendance->booking;

                                $participant =
                                    $booking?->participant;

                                $participantName =
                                    $participant?->full_name
                                    ?? $participant?->name
                                    ?? 'Peserta';

                                $initials =
                                    $makeInitials(
                                        $participantName
                                    );

                                $statusClass = match (
                                    $attendance->status
                                ) {

                                    'present' =>
                                        'status-present',

                                    'permission' =>
                                        'status-permission',

                                    'sick' =>
                                        'status-sick',

                                    'absent' =>
                                        'status-absent',

                                    default =>
                                        'status-default',
                                };

                            @endphp


                            <tr>

                                {{-- =================================
                                     PESERTA
                                     ================================= --}}

                                <td>

                                    <div class="participant-wrapper">

                                        <div class="participant-avatar">

                                            {{ $initials ?: 'P' }}

                                        </div>


                                        <div>

                                            <div class="participant-name">

                                                {{ $participantName }}

                                            </div>


                                            <div class="booking-code">

                                                {{ $booking?->booking_code ?? '-' }}

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- =================================
                                     KELAS
                                     ================================= --}}

                                <td>

                                    <div class="class-name">

                                        {{ $booking?->classSchedule?->title
                                            ?? 'Kelas Renang' }}

                                    </div>


                                    @if(
                                        $booking?->classSchedule?->program
                                    )

                                        <div class="program-name">

                                            {{ $booking->classSchedule->program->name }}

                                        </div>

                                    @endif

                                </td>


                                {{-- =================================
                                     TANGGAL
                                     ================================= --}}

                                <td>

                                    <div class="date-value">

                                        @if(
                                            $booking?->classSchedule?->class_date
                                        )

                                            {{ $booking->classSchedule->class_date->translatedFormat('d F Y') }}

                                        @else

                                            -

                                        @endif

                                    </div>

                                </td>


                                {{-- =================================
                                     STATUS
                                     ================================= --}}

                                <td>

                                    <span
                                        class="status-badge {{ $statusClass }}"
                                    >

                                        {{ $attendance->status_label }}

                                    </span>

                                </td>


                                {{-- =================================
                                     CATATAN
                                     ================================= --}}

                                <td>

                                    <div class="notes">

                                        {{ $attendance->notes ?: '-' }}

                                    </div>

                                </td>


                                {{-- =================================
                                     WAKTU
                                     ================================= --}}

                                <td>

                                    <div class="checked-at">

                                        @if($attendance->checked_at)

                                            {{ $attendance->checked_at->format('d-m-Y') }}

                                            <br>

                                            {{ $attendance->checked_at->format('H:i') }}

                                        @else

                                            -

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 PAGINATION
                 ================================================= --}}

            @if($attendances->hasPages())

                <div class="pagination-wrapper">

                    {{ $attendances->links() }}

                </div>

            @endif

        @endif

    </section>

</div>

@endsection
