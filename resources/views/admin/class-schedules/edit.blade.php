@extends('layouts.admin')

@section('title', 'Edit Jadwal Kelas')
@section('topbar-title', 'Edit Jadwal Kelas')

@push('styles')
<style>
    .schedule-form-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .schedule-form-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .schedule-form-description {
        max-width: 720px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .schedule-header-actions {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }

    .schedule-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: start;
    }

    .schedule-form-card {
        padding: 24px;
    }

    .schedule-card-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .schedule-card-description {
        margin-top: 5px;
        margin-bottom: 22px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .schedule-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 19px;
    }

    .schedule-form-group {
        min-width: 0;
    }

    .schedule-form-group.full {
        grid-column: 1 / -1;
    }

    .schedule-label {
        display: block;
        margin-bottom: 7px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .schedule-required {
        color: var(--danger);
    }

    .schedule-input,
    .schedule-select,
    .schedule-textarea {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        background: var(--white);
        color: var(--text);
        font-size: 13px;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .schedule-input,
    .schedule-select {
        min-height: 44px;
        padding: 9px 12px;
    }

    .schedule-textarea {
        min-height: 125px;
        padding: 12px;
        resize: vertical;
        line-height: 1.6;
    }

    .schedule-input:focus,
    .schedule-select:focus,
    .schedule-textarea:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .schedule-help {
        margin-top: 6px;
        color: var(--text-light);
        font-size: 11px;
        line-height: 1.5;
    }

    .schedule-field-error {
        margin-top: 6px;
        color: var(--danger);
        font-size: 11px;
        font-weight: 650;
        line-height: 1.5;
    }

    .schedule-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .schedule-side-card {
        padding: 21px;
    }

    .schedule-side-card + .schedule-side-card {
        margin-top: 16px;
    }

    .schedule-side-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .schedule-summary {
        margin-top: 15px;
    }

    .schedule-summary-item {
        padding: 11px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .schedule-summary-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .schedule-summary-label {
        color: var(--text-light);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .schedule-summary-value {
        margin-top: 4px;
        color: var(--text);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
    }

    .schedule-warning {
        margin-top: 14px;
        padding: 13px;
        border: 1px solid #fde68a;
        border-radius: 10px;
        background: var(--warning-bg);
        color: #92400e;
        font-size: 11px;
        line-height: 1.65;
    }

    .schedule-rule-list {
        margin-top: 14px;
        padding-left: 18px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.7;
    }

    .schedule-rule-list li + li {
        margin-top: 6px;
    }

    @media (max-width: 1050px) {
        .schedule-form-layout {
            grid-template-columns: 1fr;
        }

        .schedule-side {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .schedule-side-card + .schedule-side-card {
            margin-top: 0;
        }
    }

    @media (max-width: 700px) {
        .schedule-form-header {
            display: block;
        }

        .schedule-form-title {
            font-size: 24px;
        }

        .schedule-header-actions {
            margin-top: 16px;
        }

        .schedule-header-actions .btn-secondary {
            flex: 1;
        }

        .schedule-form-card {
            padding: 19px;
        }

        .schedule-form-grid {
            grid-template-columns: 1fr;
        }

        .schedule-form-group.full {
            grid-column: auto;
        }

        .schedule-side {
            grid-template-columns: 1fr;
        }

        .schedule-form-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
@endpush


@section('content')

<div class="schedule-form-header">

    <div>

        <h1 class="schedule-form-title">
            Edit Jadwal Kelas
        </h1>

        <p class="schedule-form-description">
            Perbarui informasi jadwal kelas. Sistem akan tetap
            memeriksa bentrok waktu coach, bentrok lokasi, serta
            kapasitas kelas sebelum perubahan disimpan.
        </p>

    </div>


    <div class="schedule-header-actions">

        <a
            href="{{ route('admin.class-schedules.show', $classSchedule) }}"
            class="btn-secondary"
        >
            Detail Jadwal
        </a>

        <a
            href="{{ route('admin.class-schedules.index') }}"
            class="btn-secondary"
        >
            Daftar Jadwal
        </a>

    </div>

</div>


<div class="schedule-form-layout">

    {{-- =========================================================
        FORM
    ========================================================== --}}

    <div class="card schedule-form-card">

        <div class="schedule-card-title">
            Informasi Jadwal
        </div>

        <div class="schedule-card-description">
            Perubahan hanya dapat dilakukan selama jadwal masih
            berstatus terjadwal.
        </div>


        <form
            method="POST"
            action="{{ route('admin.class-schedules.update', $classSchedule) }}"
            id="scheduleEditForm"
        >
            @csrf
            @method('PUT')


            <div class="schedule-form-grid">

                {{-- PROGRAM --}}
                <div class="schedule-form-group">

                    <label
                        for="program_id"
                        class="schedule-label"
                    >
                        Program
                        <span class="schedule-required">*</span>
                    </label>

                    <select
                        name="program_id"
                        id="program_id"
                        class="schedule-select"
                        required
                    >

                        <option value="">
                            Pilih Program
                        </option>

                        @foreach ($programs as $program)

                            <option
                                value="{{ $program->id }}"
                                data-name="{{ $program->name }}"
                                data-duration="{{ $program->duration_minutes }}"
                                {{
                                    (string) old(
                                        'program_id',
                                        $classSchedule->program_id
                                    ) === (string) $program->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $program->name }}
                                — {{ $program->duration_minutes }} menit
                            </option>

                        @endforeach

                    </select>

                    @error('program_id')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- COACH --}}
                <div class="schedule-form-group">

                    <label
                        for="coach_id"
                        class="schedule-label"
                    >
                        Coach
                        <span class="schedule-required">*</span>
                    </label>

                    <select
                        name="coach_id"
                        id="coach_id"
                        class="schedule-select"
                        required
                    >

                        <option value="">
                            Pilih Coach
                        </option>

                        @foreach ($coaches as $coach)

                            <option
                                value="{{ $coach->id }}"
                                data-name="{{ $coach->user?->name ?? '-' }}"
                                {{
                                    (string) old(
                                        'coach_id',
                                        $classSchedule->coach_id
                                    ) === (string) $coach->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $coach->user?->name ?? 'Coach' }}

                                @if ($coach->specialization)
                                    — {{ $coach->specialization }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @error('coach_id')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- LOCATION --}}
                <div class="schedule-form-group">

                    <label
                        for="location_id"
                        class="schedule-label"
                    >
                        Lokasi
                        <span class="schedule-required">*</span>
                    </label>

                    <select
                        name="location_id"
                        id="location_id"
                        class="schedule-select"
                        required
                    >

                        <option value="">
                            Pilih Lokasi
                        </option>

                        @foreach ($locations as $location)

                            <option
                                value="{{ $location->id }}"
                                data-name="{{ $location->name }}"
                                {{
                                    (string) old(
                                        'location_id',
                                        $classSchedule->location_id
                                    ) === (string) $location->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $location->name }}

                                @if ($location->city)
                                    — {{ $location->city }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @error('location_id')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- LEVEL --}}
                <div class="schedule-form-group">

                    <label
                        for="swimming_level"
                        class="schedule-label"
                    >
                        Level Kelas
                        <span class="schedule-required">*</span>
                    </label>

                    <select
                        name="swimming_level"
                        id="swimming_level"
                        class="schedule-select"
                        required
                    >

                        <option value="">
                            Pilih Level
                        </option>

                        <option
                            value="all"
                            {{
                                old(
                                    'swimming_level',
                                    $classSchedule->swimming_level
                                ) === 'all'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Semua Level
                        </option>

                        <option
                            value="beginner"
                            {{
                                old(
                                    'swimming_level',
                                    $classSchedule->swimming_level
                                ) === 'beginner'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Pemula
                        </option>

                        <option
                            value="basic"
                            {{
                                old(
                                    'swimming_level',
                                    $classSchedule->swimming_level
                                ) === 'basic'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Dasar
                        </option>

                        <option
                            value="intermediate"
                            {{
                                old(
                                    'swimming_level',
                                    $classSchedule->swimming_level
                                ) === 'intermediate'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Menengah
                        </option>

                        <option
                            value="advanced"
                            {{
                                old(
                                    'swimming_level',
                                    $classSchedule->swimming_level
                                ) === 'advanced'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Lanjutan
                        </option>

                    </select>

                    @error('swimming_level')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- TITLE --}}
                <div class="schedule-form-group full">

                    <label
                        for="title"
                        class="schedule-label"
                    >
                        Judul Kelas
                        <span class="schedule-required">*</span>
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="schedule-input"
                        value="{{ old('title', $classSchedule->title) }}"
                        maxlength="150"
                        required
                    >

                    @error('title')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- DATE --}}
                <div class="schedule-form-group">

                    <label
                        for="class_date"
                        class="schedule-label"
                    >
                        Tanggal Kelas
                        <span class="schedule-required">*</span>
                    </label>

                    <input
                        type="date"
                        name="class_date"
                        id="class_date"
                        class="schedule-input"
                        value="{{
                            old(
                                'class_date',
                                $classSchedule->class_date?->format('Y-m-d')
                            )
                        }}"
                        required
                    >

                    @error('class_date')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- CAPACITY --}}
                <div class="schedule-form-group">

                    <label
                        for="capacity"
                        class="schedule-label"
                    >
                        Kapasitas
                        <span class="schedule-required">*</span>
                    </label>

                    <input
                        type="number"
                        name="capacity"
                        id="capacity"
                        class="schedule-input"
                        value="{{ old('capacity', $classSchedule->capacity) }}"
                        min="{{ max(1, $activeBookingsCount + $activeOffersCount) }}"
                        max="1000"
                        required
                    >

                    <div class="schedule-help">
                        Booking aktif: <strong>{{ $activeBookingsCount }}</strong>.
                        Penawaran Waiting List aktif:
                        <strong>{{ $activeOffersCount }}</strong>.
                        Kapasitas minimal:
                        <strong>{{ max(1, $activeBookingsCount + $activeOffersCount) }}</strong>.
                    </div>

                    @error('capacity')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- START --}}
                <div class="schedule-form-group">

                    <label
                        for="start_time"
                        class="schedule-label"
                    >
                        Jam Mulai
                        <span class="schedule-required">*</span>
                    </label>

                    <input
                        type="time"
                        name="start_time"
                        id="start_time"
                        class="schedule-input"
                        value="{{
                            old(
                                'start_time',
                                \Illuminate\Support\Str::substr(
                                    $classSchedule->start_time,
                                    0,
                                    5
                                )
                            )
                        }}"
                        required
                    >

                    @error('start_time')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- END --}}
                <div class="schedule-form-group">

                    <label
                        for="end_time"
                        class="schedule-label"
                    >
                        Jam Selesai
                        <span class="schedule-required">*</span>
                    </label>

                    <input
                        type="time"
                        name="end_time"
                        id="end_time"
                        class="schedule-input"
                        value="{{
                            old(
                                'end_time',
                                \Illuminate\Support\Str::substr(
                                    $classSchedule->end_time,
                                    0,
                                    5
                                )
                            )
                        }}"
                        required
                    >

                    <div class="schedule-help">
                        Jam selesai harus lebih besar dari jam mulai.
                    </div>

                    @error('end_time')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- NOTES --}}
                <div class="schedule-form-group full">

                    <label
                        for="notes"
                        class="schedule-label"
                    >
                        Catatan
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        class="schedule-textarea"
                        maxlength="5000"
                        placeholder="Catatan tambahan mengenai pelaksanaan kelas..."
                    >{{ old('notes', $classSchedule->notes) }}</textarea>

                    <div class="schedule-help">
                        Opsional. Maksimal 5.000 karakter.
                    </div>

                    @error('notes')
                        <div class="schedule-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            <div class="schedule-form-actions">

                <a
                    href="{{ route('admin.class-schedules.show', $classSchedule) }}"
                    class="btn-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
        SIDE
    ========================================================== --}}

    <div class="schedule-side">

        <div class="card schedule-side-card">

            <div class="schedule-side-title">
                Ringkasan Jadwal
            </div>


            <div class="schedule-summary">

                <div class="schedule-summary-item">

                    <div class="schedule-summary-label">
                        Program
                    </div>

                    <div
                        class="schedule-summary-value"
                        id="summaryProgram"
                    >
                        -
                    </div>

                </div>


                <div class="schedule-summary-item">

                    <div class="schedule-summary-label">
                        Coach
                    </div>

                    <div
                        class="schedule-summary-value"
                        id="summaryCoach"
                    >
                        -
                    </div>

                </div>


                <div class="schedule-summary-item">

                    <div class="schedule-summary-label">
                        Lokasi
                    </div>

                    <div
                        class="schedule-summary-value"
                        id="summaryLocation"
                    >
                        -
                    </div>

                </div>


                <div class="schedule-summary-item">

                    <div class="schedule-summary-label">
                        Waktu
                    </div>

                    <div
                        class="schedule-summary-value"
                        id="summaryTime"
                    >
                        -
                    </div>

                </div>


                <div class="schedule-summary-item">

                    <div class="schedule-summary-label">
                        Booking Aktif
                    </div>

                    <div class="schedule-summary-value">
                        {{ $activeBookingsCount }} peserta
                    </div>

                </div>

            </div>

        </div>


        <div class="card schedule-side-card">

            <div class="schedule-side-title">
                Aturan Perubahan
            </div>

            <ul class="schedule-rule-list">

                <li>
                    Jadwal yang sudah selesai atau dibatalkan
                    tidak dapat diedit.
                </li>

                <li>
                    Coach tidak boleh memiliki kelas lain yang
                    waktunya bertabrakan.
                </li>

                <li>
                    Lokasi tidak boleh digunakan kelas lain pada
                    waktu yang bertabrakan.
                </li>

                <li>
                    Kapasitas tidak boleh lebih kecil dari jumlah
                    booking aktif dan penawaran Waiting List aktif.
                </li>

            </ul>


            @if ($activeBookingsCount + $activeOffersCount > 0)

                <div class="schedule-warning">
                    Jadwal ini memiliki
                    <strong>{{ $activeBookingsCount }}</strong>
                    booking aktif dan
                    <strong>{{ $activeOffersCount }}</strong>
                    penawaran Waiting List aktif. Perhatikan perubahan tanggal,
                    waktu, coach, lokasi, dan kapasitas karena
                    perubahan tersebut memengaruhi kelas peserta.
                </div>

            @endif

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const programSelect =
            document.getElementById('program_id');

        const coachSelect =
            document.getElementById('coach_id');

        const locationSelect =
            document.getElementById('location_id');

        const dateInput =
            document.getElementById('class_date');

        const startInput =
            document.getElementById('start_time');

        const endInput =
            document.getElementById('end_time');

        const summaryProgram =
            document.getElementById('summaryProgram');

        const summaryCoach =
            document.getElementById('summaryCoach');

        const summaryLocation =
            document.getElementById('summaryLocation');

        const summaryTime =
            document.getElementById('summaryTime');


        function selectedData(select, key) {

            if (!select || !select.value) {
                return null;
            }

            const option =
                select.options[select.selectedIndex];

            return option.dataset[key] || null;
        }


        function updateProgramSummary() {

            const name =
                selectedData(programSelect, 'name');

            const duration =
                selectedData(programSelect, 'duration');

            summaryProgram.textContent =
                name
                    ? (
                        duration
                            ? name + ' · ' + duration + ' menit'
                            : name
                    )
                    : 'Belum dipilih';
        }


        function updateCoachSummary() {

            const name =
                selectedData(coachSelect, 'name');

            summaryCoach.textContent =
                name || 'Belum dipilih';
        }


        function updateLocationSummary() {

            const name =
                selectedData(locationSelect, 'name');

            summaryLocation.textContent =
                name || 'Belum dipilih';
        }


        function updateTimeSummary() {

            const date =
                dateInput.value;

            const start =
                startInput.value;

            const end =
                endInput.value;


            let result = '';


            if (date) {

                const parts =
                    date.split('-');

                if (parts.length === 3) {

                    result =
                        parts[2]
                        + '/'
                        + parts[1]
                        + '/'
                        + parts[0];

                }

            }


            if (start || end) {

                if (result) {
                    result += ' · ';
                }

                result +=
                    (start || '--:--')
                    + ' - '
                    + (end || '--:--')
                    + ' WIB';

            }


            summaryTime.textContent =
                result || 'Belum ditentukan';
        }


        programSelect.addEventListener(
            'change',
            updateProgramSummary
        );

        coachSelect.addEventListener(
            'change',
            updateCoachSummary
        );

        locationSelect.addEventListener(
            'change',
            updateLocationSummary
        );

        dateInput.addEventListener(
            'change',
            updateTimeSummary
        );

        startInput.addEventListener(
            'change',
            updateTimeSummary
        );

        endInput.addEventListener(
            'change',
            updateTimeSummary
        );


        updateProgramSummary();
        updateCoachSummary();
        updateLocationSummary();
        updateTimeSummary();

    });
</script>
@endpush
