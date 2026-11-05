@extends('layouts.coach')

@section('title', 'Jadwal Saya')
@section('topbar-title', 'Jadwal Saya')

@push('styles')
<style>
    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 23px;
    }

    .page-heading h1 {
        color: var(--navy);
        font-size: 25px;
        font-weight: 800;
        line-height: 1.25;
    }

    .page-heading p {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.7;
        margin-top: 6px;
    }

    .coach-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--aqua-soft);
        color: var(--ocean);
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 19px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--blue-soft);
        color: var(--ocean);
        flex-shrink: 0;
    }

    .summary-icon svg {
        width: 21px;
        height: 21px;
    }

    .summary-number {
        font-size: 21px;
        line-height: 1;
        color: var(--navy);
        font-weight: 800;
    }

    .summary-label {
        color: var(--muted);
        font-size: 10px;
        margin-top: 6px;
    }

    .section-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 21px;
        padding: 23px;
        margin-bottom: 20px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        align-items: flex-start;
        margin-bottom: 19px;
    }

    .section-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .section-description {
        color: var(--muted);
        font-size: 11px;
        line-height: 1.6;
        margin-top: 4px;
    }

    .section-count {
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--aqua-soft);
        color: var(--ocean);
        font-size: 9px;
        font-weight: 800;
    }

    .schedule-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .schedule-item {
        border: 1px solid var(--border);
        border-radius: 17px;
        padding: 17px;
        display: grid;
        grid-template-columns: 65px minmax(0, 1fr) auto;
        align-items: center;
        gap: 16px;
        transition: .2s ease;
    }

    .schedule-item:hover {
        border-color: #b9ddea;
        box-shadow: 0 10px 28px rgba(16, 81, 120, .06);
    }

    .schedule-item.today {
        border-color: #bce7ef;
        background: #fbfeff;
    }

    .date-box {
        width: 65px;
        min-height: 67px;
        border-radius: 15px;
        background: var(--blue-soft);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .date-day {
        color: var(--ocean);
        font-size: 21px;
        font-weight: 800;
        line-height: 1;
    }

    .date-month {
        color: var(--muted);
        font-size: 9px;
        font-weight: 800;
        margin-top: 5px;
        text-transform: uppercase;
    }

    .schedule-title {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .schedule-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 15px;
        margin-top: 7px;
        color: var(--muted);
        font-size: 10px;
    }

    .schedule-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .schedule-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 800;
    }

    .tag-today {
        background: var(--success-soft);
        color: var(--success);
    }

    .tag-level {
        background: var(--aqua-soft);
        color: var(--ocean);
    }

    .tag-completed {
        background: var(--success-soft);
        color: var(--success);
    }

    .tag-cancelled {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .tag-scheduled {
        background: var(--blue-soft);
        color: var(--ocean);
    }

    .schedule-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .participant-count {
        min-width: 55px;
        text-align: center;
    }

    .participant-count strong {
        display: block;
        color: var(--navy);
        font-size: 17px;
    }

    .participant-count span {
        display: block;
        color: var(--muted);
        font-size: 8px;
        margin-top: 3px;
    }

    .detail-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 11px;
        background: var(--navy);
        color: white;
        font-size: 10px;
        font-weight: 700;
        transition: .2s ease;
        white-space: nowrap;
    }

    .detail-button:hover {
        background: var(--ocean);
    }

    .empty-state {
        padding: 34px 20px;
        border: 1px dashed #c9dfe9;
        background: #fbfdfe;
        border-radius: 16px;
        text-align: center;
    }

    .empty-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        margin: 0 auto 12px;
        background: var(--aqua-soft);
        color: var(--ocean);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-state strong {
        display: block;
        color: var(--navy);
        font-size: 12px;
    }

    .empty-state p {
        color: var(--muted);
        font-size: 10px;
        line-height: 1.6;
        margin-top: 5px;
    }

    .history-item {
        opacity: .82;
    }

    @media (max-width: 850px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .schedule-item {
            grid-template-columns: 58px minmax(0, 1fr);
        }

        .date-box {
            width: 58px;
        }

        .schedule-actions {
            grid-column: 2;
            justify-content: flex-start;
        }
    }

    @media (max-width: 600px) {
        .page-heading {
            flex-direction: column;
        }

        .section-card {
            padding: 17px;
        }

        .schedule-item {
            padding: 14px;
        }

        .schedule-meta {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
@endpush

@section('content')

<div class="page-heading">
    <div>
        <h1>Jadwal Latihan Anda</h1>
        <p>
            Lihat kelas yang ditugaskan kepada Anda dan buka detail kelas
            untuk melihat peserta.
        </p>
    </div>

    <div class="coach-badge">
        Pelatih · {{ $coach->user->name }}
    </div>
</div>

<div class="summary-grid">

    <div class="summary-card">
        <div class="summary-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            </svg>
        </div>

        <div>
            <div class="summary-number">
                {{ $todaySchedules->count() }}
            </div>
            <div class="summary-label">Kelas Hari Ini</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 8v4l3 2"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        </div>

        <div>
            <div class="summary-number">
                {{ $upcomingSchedules->count() }}
            </div>
            <div class="summary-label">Jadwal Mendatang</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>

        <div>
            <div class="summary-number">
                {{ $historySchedules->count() }}
            </div>
            <div class="summary-label">Riwayat Kelas</div>
        </div>
    </div>

</div>

<section class="section-card">

    <div class="section-header">
        <div>
            <h2 class="section-title">Hari Ini</h2>
            <p class="section-description">
                Kelas yang dijadwalkan untuk hari ini.
            </p>
        </div>

        <span class="section-count">
            {{ $todaySchedules->count() }} kelas
        </span>
    </div>

    @if($todaySchedules->isEmpty())

        <div class="empty-state">
            <div class="empty-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M16 3v4M8 3v4M3 10h18"/>
                </svg>
            </div>

            <strong>Tidak ada kelas hari ini</strong>

            <p>
                Jadwal yang ditugaskan kepada Anda akan tampil di sini.
            </p>
        </div>

    @else

        <div class="schedule-list">

            @foreach($todaySchedules as $schedule)

                <article class="schedule-item today">

                    <div class="date-box">
                        <span class="date-day">
                            {{ $schedule->class_date->format('d') }}
                        </span>
                        <span class="date-month">
                            {{ $schedule->class_date->translatedFormat('M') }}
                        </span>
                    </div>

                    <div>
                        <div class="schedule-title">
                            {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                        </div>

                        <div class="schedule-meta">
                            <span>
                                ⏱
                                {{ substr($schedule->start_time, 0, 5) }}
                                -
                                {{ substr($schedule->end_time, 0, 5) }}
                            </span>

                            <span>
                                ◉
                                {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}
                            </span>

                            <span>
                                {{ $schedule->program?->name ?? 'Program' }}
                            </span>
                        </div>

                        <div class="schedule-tags">
                            <span class="tag tag-today">
                                Hari Ini
                            </span>

                            <span class="tag tag-level">
                                Level {{ ucfirst($schedule->swimming_level) }}
                            </span>
                        </div>
                    </div>

                    <div class="schedule-actions">

                        <div class="participant-count">
                            <strong>
                                {{ $schedule->active_bookings_count }}
                            </strong>
                            <span>peserta</span>
                        </div>

                        <a
                            href="{{ route('coach.schedules.show', $schedule) }}"
                            class="detail-button"
                        >
                            Detail Kelas →
                        </a>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</section>

<section class="section-card">

    <div class="section-header">
        <div>
            <h2 class="section-title">Jadwal Mendatang</h2>
            <p class="section-description">
                Kelas yang akan Anda tangani setelah hari ini.
            </p>
        </div>

        <span class="section-count">
            {{ $upcomingSchedules->count() }} kelas
        </span>
    </div>

    @if($upcomingSchedules->isEmpty())

        <div class="empty-state">
            <strong>Belum ada jadwal mendatang</strong>
            <p>Jadwal baru akan muncul otomatis setelah dibuat oleh admin.</p>
        </div>

    @else

        <div class="schedule-list">

            @foreach($upcomingSchedules as $schedule)

                <article class="schedule-item">

                    <div class="date-box">
                        <span class="date-day">
                            {{ $schedule->class_date->format('d') }}
                        </span>
                        <span class="date-month">
                            {{ $schedule->class_date->translatedFormat('M') }}
                        </span>
                    </div>

                    <div>
                        <div class="schedule-title">
                            {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                        </div>

                        <div class="schedule-meta">
                            <span>
                                {{ $schedule->class_date->translatedFormat('l, d F Y') }}
                            </span>

                            <span>
                                ⏱
                                {{ substr($schedule->start_time, 0, 5) }}
                                -
                                {{ substr($schedule->end_time, 0, 5) }}
                            </span>

                            <span>
                                ◉
                                {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}
                            </span>
                        </div>

                        <div class="schedule-tags">
                            <span class="tag tag-level">
                                Level {{ ucfirst($schedule->swimming_level) }}
                            </span>
                        </div>
                    </div>

                    <div class="schedule-actions">

                        <div class="participant-count">
                            <strong>
                                {{ $schedule->active_bookings_count }}
                            </strong>
                            <span>peserta</span>
                        </div>

                        <a
                            href="{{ route('coach.schedules.show', $schedule) }}"
                            class="detail-button"
                        >
                            Detail Kelas →
                        </a>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</section>

@if($historySchedules->isNotEmpty())

<section class="section-card">

    <div class="section-header">
        <div>
            <h2 class="section-title">Riwayat Kelas</h2>
            <p class="section-description">
                Jadwal yang sudah selesai, dibatalkan, atau tanggalnya telah lewat.
            </p>
        </div>

        <span class="section-count">
            {{ $historySchedules->count() }} kelas
        </span>
    </div>

    <div class="schedule-list">

        @foreach($historySchedules as $schedule)

            <article class="schedule-item history-item">

                <div class="date-box">
                    <span class="date-day">
                        {{ $schedule->class_date->format('d') }}
                    </span>
                    <span class="date-month">
                        {{ $schedule->class_date->translatedFormat('M') }}
                    </span>
                </div>

                <div>
                    <div class="schedule-title">
                        {{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}
                    </div>

                    <div class="schedule-meta">
                        <span>
                            {{ $schedule->class_date->translatedFormat('d F Y') }}
                        </span>

                        <span>
                            {{ substr($schedule->start_time, 0, 5) }}
                            -
                            {{ substr($schedule->end_time, 0, 5) }}
                        </span>

                        <span>
                            {{ $schedule->location?->name ?? 'Lokasi belum ditentukan' }}
                        </span>
                    </div>

                    <div class="schedule-tags">

                        @if($schedule->status === 'completed')
                            <span class="tag tag-completed">
                                Selesai
                            </span>
                        @elseif($schedule->status === 'cancelled')
                            <span class="tag tag-cancelled">
                                Dibatalkan
                            </span>
                        @else
                            <span class="tag tag-scheduled">
                                Terjadwal
                            </span>
                        @endif

                    </div>
                </div>

                <div class="schedule-actions">

                    <div class="participant-count">
                        <strong>
                            {{ $schedule->active_bookings_count }}
                        </strong>
                        <span>peserta</span>
                    </div>

                    <a
                        href="{{ route('coach.schedules.show', $schedule) }}"
                        class="detail-button"
                    >
                        Lihat Detail →
                    </a>

                </div>

            </article>

        @endforeach

    </div>

</section>

@endif

@endsection
