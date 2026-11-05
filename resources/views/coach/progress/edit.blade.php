@extends('layouts.coach')

@section('title', 'Penilaian Perkembangan | Ragen Swim Course')
@section('topbar-title', 'Penilaian Perkembangan')
@section('topbar-subtitle', 'Catat perkembangan kemampuan renang peserta.')

@section('content')

<style>
    .progress-page {
        max-width: 1180px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        color: #087fc1;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 20px;
        font-size: 14px;
        line-height: 1.6;
    }

    .alert-danger {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .alert-danger ul {
        margin: 6px 0 0 18px;
        padding: 0;
    }

    .participant-hero {
        position: relative;
        overflow: hidden;
        padding: 28px;
        margin-bottom: 22px;
        border-radius: 24px;
        color: white;
        background:
            linear-gradient(
                120deg,
                #073b67 0%,
                #086da6 55%,
                #17a8cf 100%
            );
    }

    .participant-hero::before {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        right: -80px;
        top: -140px;
    }

    .participant-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 50%;
        right: 40px;
        bottom: -180px;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-label {
        display: inline-flex;
        padding: 7px 12px;
        margin-bottom: 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.15);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .participant-hero h2 {
        margin: 0 0 8px;
        font-size: 27px;
        line-height: 1.25;
    }

    .participant-hero p {
        margin: 0;
        color: rgba(255,255,255,.82);
        font-size: 14px;
    }

    .hero-info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 24px;
    }

    .hero-info {
        padding: 14px 16px;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 14px;
        background: rgba(255,255,255,.10);
    }

    .hero-info span {
        display: block;
        margin-bottom: 5px;
        color: rgba(255,255,255,.65);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .hero-info strong {
        display: block;
        font-size: 13px;
        line-height: 1.4;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        gap: 22px;
        align-items: start;
    }

    .card {
        border: 1px solid #dce9f1;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(8, 60, 96, .04);
    }

    .card-header {
        padding: 22px 24px 17px;
        border-bottom: 1px solid #edf3f7;
    }

    .card-header h3 {
        margin: 0 0 5px;
        color: #07345b;
        font-size: 18px;
    }

    .card-header p {
        margin: 0;
        color: #7b91a5;
        font-size: 13px;
        line-height: 1.6;
    }

    .card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 9px;
        color: #123e61;
        font-size: 13px;
        font-weight: 800;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        box-sizing: border-box;
        padding: 13px 14px;
        border: 1px solid #d5e3ec;
        border-radius: 12px;
        outline: none;
        background: #fff;
        color: #123e61;
        font: inherit;
        font-size: 14px;
        transition: .2s ease;
    }

    .form-control:focus {
        border-color: #159bd1;
        box-shadow: 0 0 0 3px rgba(21,155,209,.10);
    }

    textarea.form-control {
        min-height: 130px;
        resize: vertical;
        line-height: 1.6;
    }

    .skill-title {
        margin-bottom: 5px;
        color: #07345b;
        font-size: 18px;
    }

    .skill-description {
        margin-bottom: 20px;
        color: #7b91a5;
        font-size: 13px;
        line-height: 1.6;
    }

    .skills-wrapper {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .skill-row {
        display: grid;
        grid-template-columns: minmax(170px, 1fr) minmax(300px, 2fr);
        gap: 20px;
        align-items: center;
        padding: 17px;
        border: 1px solid #e2edf3;
        border-radius: 15px;
        background: #fbfdfe;
    }

    .skill-name strong {
        display: block;
        margin-bottom: 4px;
        color: #103d60;
        font-size: 14px;
    }

    .skill-name span {
        color: #8aa0b2;
        font-size: 11px;
        line-height: 1.4;
    }

    .score-options {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 7px;
    }

    .score-option {
        position: relative;
    }

    .score-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .score-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border: 1px solid #d8e6ee;
        border-radius: 10px;
        background: #ffffff;
        color: #668196;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: .18s ease;
    }

    .score-option label:hover {
        border-color: #159bd1;
        color: #087fc1;
        background: #f0fbff;
    }

    .score-option input:checked + label {
        border-color: #0788c8;
        color: white;
        background: #0788c8;
        box-shadow: 0 5px 12px rgba(7,136,200,.18);
    }

    .score-legend {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 9px;
        color: #9aabb8;
        font-size: 10px;
    }

    .average-box {
        margin-top: 18px;
        padding: 18px;
        border-radius: 15px;
        background: #eefaff;
        border: 1px solid #d4f0fb;
    }

    .average-box span {
        display: block;
        margin-bottom: 5px;
        color: #6e8da1;
        font-size: 11px;
        font-weight: 700;
    }

    .average-value {
        display: flex;
        align-items: baseline;
        gap: 5px;
    }

    .average-value strong {
        color: #087fc1;
        font-size: 28px;
    }

    .average-value small {
        color: #8ca0af;
        font-size: 12px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 22px;
        margin-top: 24px;
        border-top: 1px solid #edf3f7;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 18px;
        border: 0;
        border-radius: 11px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-secondary {
        border: 1px solid #d7e4ec;
        color: #315875;
        background: #ffffff;
    }

    .btn-primary {
        color: #ffffff;
        background: linear-gradient(135deg, #086aa5, #0b9bd0);
        box-shadow: 0 8px 18px rgba(8,106,165,.18);
    }

    .side-card {
        padding: 22px;
    }

    .side-title {
        margin: 0 0 16px;
        color: #07345b;
        font-size: 16px;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .info-item {
        padding-bottom: 14px;
        border-bottom: 1px solid #edf3f7;
    }

    .info-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .info-item span {
        display: block;
        margin-bottom: 4px;
        color: #91a3b1;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .info-item strong {
        display: block;
        color: #174664;
        font-size: 13px;
        line-height: 1.5;
    }

    .guide-card {
        margin-top: 18px;
        padding: 20px;
        border: 1px solid #d9eef7;
        border-radius: 18px;
        background: #f3fbfe;
    }

    .guide-card h4 {
        margin: 0 0 13px;
        color: #0878ad;
        font-size: 14px;
    }

    .guide-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .guide-item {
        display: flex;
        gap: 10px;
        color: #678497;
        font-size: 11px;
        line-height: 1.5;
    }

    .guide-number {
        flex: 0 0 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 7px;
        color: #087fc1;
        background: #dff5fd;
        font-size: 10px;
        font-weight: 900;
    }

    @media (max-width: 1000px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .hero-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 720px) {
        .participant-hero {
            padding: 22px;
        }

        .participant-hero h2 {
            font-size: 22px;
        }

        .hero-info-grid {
            grid-template-columns: 1fr;
        }

        .skill-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .card-body,
        .card-header {
            padding-left: 18px;
            padding-right: 18px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }
    }
</style>

@php
    $progress = $booking->progressRecord;

    $participantName = $booking->participant?->full_name
        ?? $booking->participant?->name
        ?? 'Peserta';

    $schedule = $booking->classSchedule;

    $skills = [
        'water_adaptation' => [
            'name' => 'Adaptasi Air',
            'description' => 'Kenyamanan dan keberanian peserta di dalam air.',
        ],
        'floating' => [
            'name' => 'Mengapung',
            'description' => 'Kemampuan menjaga posisi tubuh tetap mengapung.',
        ],
        'breathing' => [
            'name' => 'Pernapasan',
            'description' => 'Kontrol dan teknik mengambil serta membuang napas.',
        ],
        'kicking' => [
            'name' => 'Gerakan Kaki',
            'description' => 'Teknik dan konsistensi gerakan kaki saat berenang.',
        ],
        'freestyle' => [
            'name' => 'Gaya Bebas',
            'description' => 'Koordinasi dasar teknik renang gaya bebas.',
        ],
        'breaststroke' => [
            'name' => 'Gaya Dada',
            'description' => 'Koordinasi dasar teknik renang gaya dada.',
        ],
    ];
@endphp

<div class="progress-page">

    <a
        href="{{ route('coach.schedules.show', $schedule->id) }}"
        class="back-link"
    >
        ← Kembali ke Detail Kelas
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum dapat disimpan.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <section class="participant-hero">
        <div class="hero-content">

            <div class="hero-label">
                Penilaian Peserta
            </div>

            <h2>{{ $participantName }}</h2>

            <p>
                Catat perkembangan kemampuan peserta berdasarkan
                hasil latihan pada pertemuan ini.
            </p>

            <div class="hero-info-grid">

                <div class="hero-info">
                    <span>Kelas</span>
                    <strong>
                        {{ $schedule?->title ?? 'Kelas Renang' }}
                    </strong>
                </div>

                <div class="hero-info">
                    <span>Program</span>
                    <strong>
                        {{ $schedule?->program?->name ?? '-' }}
                    </strong>
                </div>

                <div class="hero-info">
                    <span>Tanggal Latihan</span>
                    <strong>
                        {{ $schedule?->class_date?->translatedFormat('d F Y') ?? '-' }}
                    </strong>
                </div>

                <div class="hero-info">
                    <span>Status Kehadiran</span>
                    <strong>
                        {{ $booking->attendance?->status_label ?? 'Belum Dicatat' }}
                    </strong>
                </div>

            </div>

        </div>
    </section>


    <div class="content-grid">

        <div>

            <form
                action="{{ route('coach.progress.update', $booking->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')


                <section class="card">
                    <div class="card-header">
                        <h3>Informasi Penilaian</h3>

                        <p>
                            Tentukan level peserta setelah melihat kemampuan
                            peserta pada sesi latihan ini.
                        </p>
                    </div>

                    <div class="card-body">

                        <div class="form-group">

                            <label
                                for="level"
                                class="form-label"
                            >
                                Level Peserta
                                <span class="required">*</span>
                            </label>

                            <select
                                name="level"
                                id="level"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    Pilih level peserta
                                </option>

                                <option
                                    value="beginner"
                                    @selected(old('level', $progress?->level) === 'beginner')
                                >
                                    Pemula
                                </option>

                                <option
                                    value="basic"
                                    @selected(old('level', $progress?->level) === 'basic')
                                >
                                    Dasar
                                </option>

                                <option
                                    value="intermediate"
                                    @selected(old('level', $progress?->level) === 'intermediate')
                                >
                                    Menengah
                                </option>

                                <option
                                    value="advanced"
                                    @selected(old('level', $progress?->level) === 'advanced')
                                >
                                    Lanjutan
                                </option>
                            </select>

                        </div>


                        <h3 class="skill-title">
                            Penilaian Kemampuan
                        </h3>

                        <div class="skill-description">
                            Berikan nilai 1–5 pada setiap kemampuan.
                            Nilai dapat disesuaikan berdasarkan hasil pengamatan
                            pelatih selama latihan.
                        </div>


                        <div class="skills-wrapper">

                            @foreach ($skills as $field => $skill)

                                @php
                                    $selectedScore = old(
                                        $field,
                                        $progress?->{$field}
                                    );
                                @endphp

                                <div class="skill-row">

                                    <div class="skill-name">
                                        <strong>
                                            {{ $skill['name'] }}
                                        </strong>

                                        <span>
                                            {{ $skill['description'] }}
                                        </span>
                                    </div>


                                    <div>

                                        <div class="score-options">

                                            @for ($score = 1; $score <= 5; $score++)

                                                <div class="score-option">

                                                    <input
                                                        type="radio"
                                                        name="{{ $field }}"
                                                        id="{{ $field }}_{{ $score }}"
                                                        value="{{ $score }}"
                                                        class="skill-score"
                                                        @checked((string) $selectedScore === (string) $score)
                                                    >

                                                    <label
                                                        for="{{ $field }}_{{ $score }}"
                                                    >
                                                        {{ $score }}
                                                    </label>

                                                </div>

                                            @endfor

                                        </div>

                                        <div class="score-legend">
                                            <span>Perlu latihan</span>
                                            <span>Sangat baik</span>
                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        <div class="average-box">

                            <span>
                                Rata-rata nilai kemampuan
                            </span>

                            <div class="average-value">
                                <strong id="averageScore">
                                    {{ $progress ? number_format($progress->average_score, 1) : '0.0' }}
                                </strong>

                                <small>/ 5.0</small>
                            </div>

                        </div>


                        <div
                            class="form-group"
                            style="margin-top: 25px;"
                        >

                            <label
                                for="coach_notes"
                                class="form-label"
                            >
                                Catatan Pelatih
                            </label>

                            <textarea
                                name="coach_notes"
                                id="coach_notes"
                                class="form-control"
                                placeholder="Contoh: Ahmad sudah lebih percaya diri di air dan mulai mampu mengatur pernapasan dengan baik. Gerakan kaki masih perlu dilatih agar lebih konsisten."
                            >{{ old('coach_notes', $progress?->coach_notes) }}</textarea>

                        </div>


                        <div class="form-actions">

                            <a
                                href="{{ route('coach.schedules.show', $schedule->id) }}"
                                class="btn btn-secondary"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                {{ $progress ? 'Perbarui Penilaian' : 'Simpan Penilaian' }}
                            </button>

                        </div>

                    </div>
                </section>

            </form>

        </div>


        <aside>

            <section class="card side-card">

                <h3 class="side-title">
                    Informasi Pertemuan
                </h3>

                <div class="info-list">

                    <div class="info-item">
                        <span>Kode Booking</span>

                        <strong>
                            {{ $booking->booking_code }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Peserta</span>

                        <strong>
                            {{ $participantName }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Pelatih</span>

                        <strong>
                            {{ $schedule?->coach?->user?->name ?? auth()->user()->name }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Lokasi</span>

                        <strong>
                            {{ $schedule?->location?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Waktu</span>

                        <strong>
                            {{ $schedule?->start_time ? substr($schedule->start_time, 0, 5) : '-' }}
                            -
                            {{ $schedule?->end_time ? substr($schedule->end_time, 0, 5) : '-' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Kehadiran</span>

                        <strong>
                            {{ $booking->attendance?->status_label ?? 'Belum Dicatat' }}
                        </strong>
                    </div>

                </div>

            </section>


            <section class="guide-card">

                <h4>Panduan Nilai</h4>

                <div class="guide-list">

                    <div class="guide-item">
                        <div class="guide-number">1</div>
                        <div>Perlu Banyak Latihan</div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-number">2</div>
                        <div>Mulai Berkembang</div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-number">3</div>
                        <div>Cukup Baik</div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-number">4</div>
                        <div>Baik</div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-number">5</div>
                        <div>Sangat Baik</div>
                    </div>

                </div>

            </section>

        </aside>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const scoreInputs = document.querySelectorAll('.skill-score');
        const averageElement = document.getElementById('averageScore');

        function calculateAverage() {

            const skillNames = [
                'water_adaptation',
                'floating',
                'breathing',
                'kicking',
                'freestyle',
                'breaststroke'
            ];

            let total = 0;
            let count = 0;

            skillNames.forEach(function (skillName) {

                const selected = document.querySelector(
                    'input[name="' + skillName + '"]:checked'
                );

                if (selected) {
                    total += parseInt(selected.value);
                    count++;
                }

            });

            const average = count > 0
                ? (total / count).toFixed(1)
                : '0.0';

            averageElement.textContent = average;
        }

        scoreInputs.forEach(function (input) {
            input.addEventListener('change', calculateAverage);
        });

        calculateAverage();

    });
</script>

@endsection
