@extends('layouts.customer')

@section('title', 'Jadwal & Booking')
@section('topbar-title', 'Jadwal & Booking')

@section('content')

<div class="booking-page">

    {{-- =========================================================
        VALIDATION ERROR

        Flash success tidak ditampilkan di sini karena sudah
        ditangani oleh layouts.customer agar tidak muncul dua kali.
    ========================================================== --}}

    @if ($errors->any())
        <div class="booking-alert error">
            <div class="alert-icon">!</div>

            <div>
                <strong>Booking tidak dapat diproses</strong>

                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif


    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="booking-header">

        <div>
            <span class="page-eyebrow">
                Kelas Renang
            </span>

            <h1 class="page-title">
                Jadwal & Booking
            </h1>

            <p class="page-description">
                Pilih paket aktif dan jadwal kelas yang sesuai
                untuk peserta Anda.
            </p>
        </div>

        <a
            href="{{ route('customer.packages.index') }}"
            class="package-link"
        >
            Lihat Paket Saya
        </a>

    </div>


    {{-- =========================================================
        QUICK SUMMARY
    ========================================================== --}}

    <div class="booking-summary-grid">

        <div class="summary-card">

            <div class="summary-icon">
                🎫
            </div>

            <div>
                <span>Paket Aktif</span>

                <strong>
                    {{ $participantPackages->count() }}
                </strong>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon">
                📅
            </div>

            <div>
                <span>Jadwal Tersedia</span>

                <strong>
                    {{ $schedules->count() }}
                </strong>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon">
                🏊
            </div>

            <div>
                <span>Booking Aktif</span>

                <strong>
                    {{ $activeBookings->count() }}
                </strong>
            </div>

        </div>

    </div>


    {{-- =========================================================
        ACTIVE BOOKINGS
    ========================================================== --}}

    <section class="section-card">

        <div class="section-heading">

            <div>
                <span class="section-label">
                    Booking Saya
                </span>

                <h2>
                    Jadwal yang Sudah Dibooking
                </h2>

                <p>
                    Booking berstatus menunggu atau terkonfirmasi
                    akan ditampilkan di bagian ini.
                </p>
            </div>

        </div>


        @if ($activeBookings->isEmpty())

            <div class="empty-state">

                <div class="empty-icon">
                    📆
                </div>

                <h3>
                    Belum Ada Booking Aktif
                </h3>

                <p>
                    Setelah melakukan booking kelas,
                    jadwal peserta akan muncul di sini.
                </p>

            </div>

        @else

            <div class="active-booking-grid">

                @foreach ($activeBookings as $booking)

                    <article class="active-booking-card">

                        <div class="active-booking-top">

                            <div>

                                <span class="booking-code">
                                    {{ $booking->booking_code }}
                                </span>

                                <h3>
                                    {{
                                        $booking->classSchedule->title
                                        ?: (
                                            $booking->classSchedule
                                                ->program
                                                ?->name
                                            ?? 'Kelas Renang'
                                        )
                                    }}
                                </h3>

                            </div>


                            @if ($booking->status === 'confirmed')

                                <span class="booking-status confirmed">
                                    Terkonfirmasi
                                </span>

                            @else

                                <span class="booking-status pending">
                                    Menunggu
                                </span>

                            @endif

                        </div>


                        <div class="active-participant">

                            <div class="participant-avatar">
                                {{
                                    strtoupper(
                                        mb_substr(
                                            $booking
                                                ->participant
                                                ->full_name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </div>

                            <div>
                                <span>
                                    Peserta
                                </span>

                                <strong>
                                    {{
                                        $booking
                                            ->participant
                                            ->full_name
                                    }}
                                </strong>
                            </div>

                        </div>


                        <div class="active-booking-details">

                            <div>
                                <span>
                                    Tanggal
                                </span>

                                <strong>
                                    {{
                                        $booking
                                            ->classSchedule
                                            ->class_date
                                            ->translatedFormat(
                                                'd M Y'
                                            )
                                    }}
                                </strong>
                            </div>


                            <div>
                                <span>
                                    Jam
                                </span>

                                <strong>
                                    {{
                                        substr(
                                            $booking
                                                ->classSchedule
                                                ->start_time,
                                            0,
                                            5
                                        )
                                    }}
                                    -
                                    {{
                                        substr(
                                            $booking
                                                ->classSchedule
                                                ->end_time,
                                            0,
                                            5
                                        )
                                    }}
                                </strong>
                            </div>


                            <div>
                                <span>
                                    Pelatih
                                </span>

                                <strong>
                                    {{
                                        $booking
                                            ->classSchedule
                                            ->coach
                                            ?->name
                                        ?? 'Belum ditentukan'
                                    }}
                                </strong>
                            </div>


                            <div>
                                <span>
                                    Lokasi
                                </span>

                                <strong>
                                    {{
                                        $booking
                                            ->classSchedule
                                            ->location
                                            ?->name
                                        ?? 'Belum ditentukan'
                                    }}
                                </strong>
                            </div>

                        </div>


                        <div class="active-package-info">

                            <div>

                                <span>
                                    Menggunakan Paket
                                </span>

                                <strong>
                                    {{
                                        $booking
                                            ->participantPackage
                                            ->package
                                            ->name
                                    }}
                                </strong>

                                <small>
                                    Sisa
                                    {{
                                        $booking
                                            ->participantPackage
                                            ->remaining_sessions
                                    }}
                                    sesi
                                </small>

                            </div>


                            <a
                                href="{{ route(
                                    'customer.bookings.show',
                                    $booking
                                ) }}"
                                class="detail-booking-button"
                            >
                                Lihat Detail
                                <span>→</span>
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </section>


    {{-- =========================================================
        WAITING LIST CUSTOMER
    ========================================================== --}}

    <section class="section-card" id="customerWaitingLists">
        <div class="section-heading">
            <div>
                <span class="section-label">Daftar Tunggu</span>
                <h2>Waiting List Saya</h2>
                <p>Pantau antrean peserta dan penawaran slot dari admin. Penawaran berlaku selama 2 jam.</p>
            </div>
        </div>

        @if ($waitingLists->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">⏳</div>
                <h3>Belum Ada Waiting List</h3>
                <p>Jika kelas yang dipilih penuh, Anda dapat mendaftarkan peserta ke daftar tunggu.</p>
            </div>
        @else
            <div class="waiting-list-grid">
                @foreach ($waitingLists as $waitingList)
                    @php
                        $offerIsActive = $waitingList->status === 'offered'
                            && $waitingList->expires_at !== null
                            && $waitingList->expires_at->isFuture();
                        $effectiveStatus = $waitingList->status === 'offered' && !$offerIsActive
                            ? 'expired'
                            : $waitingList->status;
                        $statusLabels = [
                            'waiting' => 'Dalam Antrean',
                            'offered' => 'Slot Ditawarkan',
                            'accepted' => 'Diterima',
                            'expired' => 'Kedaluwarsa',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp

                    <article class="waiting-list-card">
                        <div class="waiting-list-top">
                            <div>
                                <span class="schedule-program">{{ $waitingList->classSchedule?->program?->name ?? 'Program Renang' }}</span>
                                <h3>{{ $waitingList->classSchedule?->title ?: ($waitingList->classSchedule?->program?->name ?? 'Kelas Renang') }}</h3>
                            </div>
                            <span class="waiting-list-status status-{{ $effectiveStatus }}">
                                {{ $statusLabels[$effectiveStatus] ?? ucfirst($effectiveStatus) }}
                            </span>
                        </div>

                        <div class="waiting-list-details">
                            <div><span>Peserta</span><strong>{{ $waitingList->participant?->full_name ?? '-' }}</strong></div>
                            <div><span>Nomor Antrean</span><strong>#{{ $waitingList->queue_number ?? '-' }}</strong></div>
                            <div><span>Tanggal</span><strong>{{ $waitingList->classSchedule?->class_date?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                            <div><span>Waktu</span><strong>{{ $waitingList->classSchedule ? substr((string) $waitingList->classSchedule->start_time, 0, 5) : '-' }} WIB</strong></div>
                        </div>

                        @if ($offerIsActive)
                            <div class="waiting-list-notice">
                                <strong>Slot tersedia untuk peserta ini.</strong>
                                <p>
                                    Penawaran berlaku sampai
                                    {{ $waitingList->expires_at->translatedFormat('d M Y, H:i') }} WIB.
                                    Silakan terima atau tolak sebelum batas waktu berakhir.
                                </p>
                            </div>

                            @php
                                $eligibleOfferPackages = $participantPackages->filter(
                                    function ($participantPackage) use ($waitingList) {
                                        return (int) $participantPackage->participant_id
                                                === (int) $waitingList->participant_id
                                            && (int) $participantPackage->package->program_id
                                                === (int) $waitingList->classSchedule?->program_id
                                            && $participantPackage->canBeUsed();
                                    }
                                );
                            @endphp

                            @if ($eligibleOfferPackages->isNotEmpty())
                                <form
                                    action="{{ route('customer.waiting-lists.accept', $waitingList) }}"
                                    method="POST"
                                    class="waiting-list-accept-form"
                                >
                                    @csrf

                                    <div class="form-group">
                                        <label for="offer-package-{{ $waitingList->id }}">
                                            Pilih Paket Peserta
                                        </label>

                                        <select
                                            id="offer-package-{{ $waitingList->id }}"
                                            name="participant_package_id"
                                            required
                                            class="waiting-list-package-select"
                                        >
                                            <option value="">-- Pilih Paket --</option>

                                            @foreach ($eligibleOfferPackages as $offerPackage)
                                                <option value="{{ $offerPackage->id }}">
                                                    {{ $offerPackage->package->name }}
                                                    ({{ $offerPackage->remaining_sessions }} sesi tersisa)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button
                                        type="submit"
                                        class="waiting-list-accept-button"
                                        onclick="return confirm('Terima penawaran slot dan buat booking untuk peserta ini?');"
                                    >
                                        Terima Penawaran
                                    </button>
                                </form>
                            @else
                                <p class="waiting-list-help">
                                    Tidak ada paket aktif yang sesuai untuk menerima
                                    penawaran ini. Silakan hubungi admin.
                                </p>
                            @endif

                            <form
                                action="{{ route('customer.waiting-lists.decline', $waitingList) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menolak penawaran slot ini?');"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="waiting-list-decline-button"
                                >
                                    Tolak Penawaran
                                </button>
                            </form>
                        @elseif ($effectiveStatus === 'waiting')
                            <p class="waiting-list-help">Jika ada slot tersedia, Admin dapat memilih peserta yang akan menerima penawaran. Pendaftaran waiting list tidak menjamin penawaran.</p>
                        @elseif ($effectiveStatus === 'expired')
                            <p class="waiting-list-help">Penawaran telah kedaluwarsa dan tidak lagi menahan kapasitas kelas.</p>
                        @endif

                        @if ($effectiveStatus === 'waiting' || $offerIsActive)
                            <form action="{{ route('customer.waiting-lists.destroy', $waitingList) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin membatalkan Waiting List peserta ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cancel-waiting-list-button">Batalkan Waiting List</button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>


    {{-- =========================================================
        BOOKING BARU
    ========================================================== --}}

    <section class="section-card booking-area">

        <div class="section-heading">

            <div>
                <span class="section-label">
                    Booking Baru
                </span>

                <h2>
                    Pilih Paket Peserta
                </h2>

                <p>
                    Pilih paket aktif terlebih dahulu.
                    Jadwal yang sesuai dengan program paket
                    akan tersedia untuk dipilih.
                </p>
            </div>

        </div>


        @if ($participantPackages->isEmpty())

            <div class="no-package-state">

                <div class="empty-icon">
                    🎫
                </div>

                <h3>
                    Belum Ada Paket yang Bisa Digunakan
                </h3>

                <p>
                    Peserta membutuhkan paket aktif dengan
                    sisa sesi sebelum dapat melakukan booking.
                </p>

                <a
                    href="{{ route('customer.packages.browse') }}"
                    class="primary-button"
                >
                    Beli Paket
                </a>

            </div>

        @else

            {{-- =================================================
                PACKAGE SELECTOR
            ================================================== --}}

            <div class="package-selector">

                @foreach ($participantPackages as $participantPackage)

                    <button
                        type="button"
                        class="participant-package-card"
                        data-package-selector
                        data-participant-package-id="{{ $participantPackage->id }}"
                        data-program-id="{{ $participantPackage->package->program_id }}"
                        data-participant-id="{{ $participantPackage->participant_id }}"
                        data-participant-name="{{ $participantPackage->participant->full_name }}"
                        data-package-name="{{ $participantPackage->package->name }}"
                        data-remaining-sessions="{{ $participantPackage->remaining_sessions }}"
                    >

                        <div class="selector-check">
                            ✓
                        </div>


                        <div class="selector-participant">

                            <div class="participant-avatar">
                                {{
                                    strtoupper(
                                        mb_substr(
                                            $participantPackage
                                                ->participant
                                                ->full_name,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </div>


                            <div>
                                <span>
                                    Peserta
                                </span>

                                <strong>
                                    {{
                                        $participantPackage
                                            ->participant
                                            ->full_name
                                    }}
                                </strong>
                            </div>

                        </div>


                        <div class="selector-package">

                            <span>
                                Paket
                            </span>

                            <strong>
                                {{
                                    $participantPackage
                                        ->package
                                        ->name
                                }}
                            </strong>

                            <small>
                                {{
                                    $participantPackage
                                        ->package
                                        ->program
                                        ?->name
                                    ?? 'Program Renang'
                                }}
                            </small>

                        </div>


                        <div class="selector-sessions">

                            <span>
                                Sisa Sesi
                            </span>

                            <strong>
                                {{
                                    $participantPackage
                                        ->remaining_sessions
                                }}
                            </strong>

                        </div>


                        <div class="selector-expiry">

                            <span>
                                Berlaku Sampai
                            </span>

                            <strong>
                                {{
                                    $participantPackage->expiry_date
                                        ? $participantPackage
                                            ->expiry_date
                                            ->translatedFormat(
                                                'd M Y'
                                            )
                                        : 'Tanpa Batas'
                                }}
                            </strong>

                        </div>

                    </button>

                @endforeach

            </div>


            {{-- =================================================
                SELECTED PACKAGE
            ================================================== --}}

            <div
                class="selected-package-info"
                id="selectedPackageInfo"
            >

                <div>
                    <span>
                        Paket Terpilih
                    </span>

                    <strong id="selectedPackageName">
                        Belum dipilih
                    </strong>
                </div>


                <div>
                    <span>
                        Peserta
                    </span>

                    <strong id="selectedParticipantName">
                        -
                    </strong>
                </div>


                <div>
                    <span>
                        Sisa Sesi
                    </span>

                    <strong id="selectedRemainingSessions">
                        -
                    </strong>
                </div>

            </div>


            {{-- =================================================
                SCHEDULE HEADER
            ================================================== --}}

            <div class="schedule-heading">

                <div>
                    <span class="section-label">
                        Jadwal
                    </span>

                    <h2>
                        Pilih Jadwal Kelas
                    </h2>

                    <p id="scheduleInstruction">
                        Pilih paket peserta di atas untuk
                        menampilkan jadwal yang sesuai.
                    </p>
                </div>

            </div>


            @if ($schedules->isEmpty())

                <div class="empty-state">

                    <div class="empty-icon">
                        🗓️
                    </div>

                    <h3>
                        Belum Ada Jadwal Tersedia
                    </h3>

                    <p>
                        Jadwal kelas belum tersedia.
                        Silakan periksa kembali nanti.
                    </p>

                </div>

            @else

                {{-- =================================================
                    SCHEDULE GRID
                ================================================== --}}

                <div
                    class="schedule-grid"
                    id="scheduleGrid"
                >

                    @foreach ($schedules as $schedule)

                        @php
                            $remainingSlots = max(
                                0,
                                (int) $schedule->capacity
                                -
                                (int) $schedule->active_bookings_count
                                -
                                (int) ($schedule->active_offers_count ?? 0)
                            );

                            $isFull = $remainingSlots <= 0;
                        @endphp


                        <article
                            class="schedule-card"
                            data-schedule-card
                            data-program-id="{{ $schedule->program_id }}"
                        >

                            <div class="schedule-card-top">

                                <div>

                                    <span class="schedule-program">
                                        {{
                                            $schedule
                                                ->program
                                                ?->name
                                            ?? 'Program Renang'
                                        }}
                                    </span>

                                    <h3>
                                        {{
                                            $schedule->title
                                            ?: (
                                                $schedule
                                                    ->program
                                                    ?->name
                                                ?? 'Kelas Renang'
                                            )
                                        }}
                                    </h3>

                                </div>


                                @if ($isFull)

                                    <span class="slot-badge full">
                                        Penuh
                                    </span>

                                @else

                                    <span class="slot-badge available">
                                        {{ $remainingSlots }} Slot
                                    </span>

                                @endif

                            </div>


                            <div class="schedule-date">

                                <div class="date-box">

                                    <span>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat('D')
                                        }}
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->format('d')
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat('M')
                                        }}
                                    </small>

                                </div>


                                <div class="schedule-time">

                                    <span>
                                        Tanggal & Waktu
                                    </span>

                                    <strong>
                                        {{
                                            $schedule
                                                ->class_date
                                                ->translatedFormat(
                                                    'd F Y'
                                                )
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            substr(
                                                $schedule->start_time,
                                                0,
                                                5
                                            )
                                        }}
                                        -
                                        {{
                                            substr(
                                                $schedule->end_time,
                                                0,
                                                5
                                            )
                                        }}
                                        WIB
                                    </small>

                                </div>

                            </div>


                            <div class="schedule-details">

                                <div class="schedule-detail">

                                    <span class="detail-icon">
                                        🧑‍🏫
                                    </span>

                                    <div>
                                        <span>
                                            Pelatih
                                        </span>

                                        <strong>
                                            {{
                                                $schedule
                                                    ->coach
                                                    ?->name
                                                ?? 'Belum ditentukan'
                                            }}
                                        </strong>
                                    </div>

                                </div>


                                <div class="schedule-detail">

                                    <span class="detail-icon">
                                        📍
                                    </span>

                                    <div>
                                        <span>
                                            Lokasi
                                        </span>

                                        <strong>
                                            {{
                                                $schedule
                                                    ->location
                                                    ?->name
                                                ?? 'Belum ditentukan'
                                            }}
                                        </strong>

                                        @if ($schedule->location?->city)
                                            <small>
                                                {{ $schedule->location->city }}
                                            </small>
                                        @endif
                                    </div>

                                </div>


                                <div class="schedule-detail">

                                    <span class="detail-icon">
                                        🏊
                                    </span>

                                    <div>
                                        <span>
                                            Level
                                        </span>

                                        <strong>
                                            @switch($schedule->swimming_level)

                                                @case('all')
                                                    Semua Level
                                                    @break

                                                @case('beginner')
                                                    Beginner
                                                    @break

                                                @case('basic')
                                                    Basic
                                                    @break

                                                @case('intermediate')
                                                    Intermediate
                                                    @break

                                                @case('advanced')
                                                    Advanced
                                                    @break

                                                @default
                                                    {{
                                                        ucfirst(
                                                            $schedule
                                                                ->swimming_level
                                                        )
                                                    }}

                                            @endswitch
                                        </strong>
                                    </div>

                                </div>


                                <div class="schedule-detail">

                                    <span class="detail-icon">
                                        👥
                                    </span>

                                    <div>
                                        <span>
                                            Kapasitas
                                        </span>

                                        <strong>
                                            {{
                                                (int) $schedule->active_bookings_count
                                                + (int) ($schedule->active_offers_count ?? 0)
                                            }}
                                            /
                                            {{ $schedule->capacity }}
                                        </strong>

                                        @if (($schedule->active_offers_count ?? 0) > 0)
                                            <small>
                                                Termasuk {{ $schedule->active_offers_count }} slot penawaran aktif
                                            </small>
                                        @else
                                            <small>
                                                peserta
                                            </small>
                                        @endif


                                    </div>

                                </div>

                            </div>


                            @if (!empty($schedule->notes))

                                <div class="schedule-notes">
                                    {{ $schedule->notes }}
                                </div>

                            @endif


                            @if ($isFull)

                                <button
                                    type="button"
                                    class="booking-button waiting-list-button"
                                    data-join-waiting-list
                                    data-schedule-id="{{ $schedule->id }}"
                                    data-schedule-title="{{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}"
                                    data-schedule-date="{{ $schedule->class_date->translatedFormat('d F Y') }}"
                                    data-schedule-time="{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}"
                                >
                                    Masuk Waiting List
                                </button>

                            @else

                                <button
                                    type="button"
                                    class="booking-button"
                                    data-book-schedule
                                    data-schedule-id="{{ $schedule->id }}"
                                    data-schedule-title="{{ $schedule->title ?: ($schedule->program?->name ?? 'Kelas Renang') }}"
                                    data-schedule-date="{{ $schedule->class_date->translatedFormat('d F Y') }}"
                                    data-schedule-time="{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}"
                                    data-location-name="{{ $schedule->location?->name ?? '-' }}"
                                >
                                    Pilih Jadwal
                                </button>

                            @endif

                        </article>

                    @endforeach

                </div>


                <div
                    class="schedule-empty-filter"
                    id="scheduleEmptyFilter"
                    hidden
                >

                    <div class="empty-icon">
                        🗓️
                    </div>

                    <h3>
                        Belum Ada Jadwal yang Sesuai
                    </h3>

                    <p>
                        Saat ini belum tersedia jadwal untuk
                        program paket yang dipilih.
                    </p>

                </div>

            @endif

        @endif

    </section>

</div>


{{-- =============================================================
    BOOKING CONFIRMATION MODAL
============================================================== --}}

<div
    class="booking-modal"
    id="bookingModal"
    aria-hidden="true"
>

    <div
        class="booking-modal-backdrop"
        data-close-booking-modal
    ></div>


    <div
        class="booking-modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="bookingModalTitle"
    >

        <button
            type="button"
            class="modal-close"
            data-close-booking-modal
            aria-label="Tutup"
        >
            ×
        </button>


        <div class="modal-icon">
            🏊
        </div>


        <span class="modal-label">
            Konfirmasi Booking
        </span>

        <h2 id="bookingModalTitle">
            Booking Kelas Renang
        </h2>

        <p class="modal-description">
            Pastikan peserta dan jadwal yang dipilih
            sudah sesuai.
        </p>


        <div class="modal-information">

            <div>
                <span>Peserta</span>

                <strong id="modalParticipant">
                    -
                </strong>
            </div>


            <div>
                <span>Paket</span>

                <strong id="modalPackage">
                    -
                </strong>
            </div>


            <div>
                <span>Kelas</span>

                <strong id="modalSchedule">
                    -
                </strong>
            </div>


            <div>
                <span>Tanggal</span>

                <strong id="modalDate">
                    -
                </strong>
            </div>


            <div>
                <span>Waktu</span>

                <strong id="modalTime">
                    -
                </strong>
            </div>


            <div>
                <span>Lokasi</span>

                <strong id="modalLocation">
                    -
                </strong>
            </div>

        </div>


        <div class="modal-notice">

            <span>ℹ️</span>

            <p>
                Booking tidak langsung mengurangi sisa sesi.
                Pemotongan sesi dilakukan setelah proses
                kehadiran kelas.
            </p>

        </div>


        <form
            action="{{ route('customer.bookings.store') }}"
            method="POST"
            id="bookingForm"
        >

            @csrf


            <input
                type="hidden"
                name="participant_package_id"
                id="bookingParticipantPackageId"
            >


            <input
                type="hidden"
                name="class_schedule_id"
                id="bookingClassScheduleId"
            >


            <div class="form-group">

                <label for="bookingNotes">
                    Catatan
                    <span>(Opsional)</span>
                </label>

                <textarea
                    name="notes"
                    id="bookingNotes"
                    rows="3"
                    maxlength="1000"
                    placeholder="Contoh: peserta baru pertama kali mengikuti kelas."
                >{{ old('notes') }}</textarea>

            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    class="secondary-button"
                    data-close-booking-modal
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="confirm-booking-button"
                    id="confirmBookingButton"
                >
                    Konfirmasi Booking
                </button>

            </div>

        </form>

    </div>

</div>

{{-- =============================================================
    WAITING LIST CONFIRMATION MODAL
============================================================== --}}
<div class="booking-modal" id="waitingListModal" aria-hidden="true">
    <div class="booking-modal-backdrop" data-close-waiting-list-modal></div>
    <div class="booking-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="waitingListModalTitle">
        <button type="button" class="modal-close" data-close-waiting-list-modal aria-label="Tutup">×</button>
        <div class="modal-icon">⏳</div>
        <span class="modal-label">Konfirmasi Daftar Tunggu</span>
        <h2 id="waitingListModalTitle">Masuk Waiting List</h2>
        <p class="modal-description">Peserta akan dimasukkan ke antrean kelas yang penuh. Penawaran slot diberikan secara manual kepada peserta yang dipilih Admin ketika slot tersedia.</p>
        <div class="modal-information">
            <div><span>Peserta</span><strong id="waitingModalParticipant">-</strong></div>
            <div><span>Paket</span><strong id="waitingModalPackage">-</strong></div>
            <div><span>Kelas</span><strong id="waitingModalSchedule">-</strong></div>
            <div><span>Tanggal</span><strong id="waitingModalDate">-</strong></div>
            <div><span>Waktu</span><strong id="waitingModalTime">-</strong></div>
        </div>
        <div class="modal-notice"><span>ℹ️</span><p>Masuk Waiting List bukan berarti booking sudah berhasil. Admin akan menawarkan slot apabila tersedia. Penawaran aktif berlaku selama 2 jam.</p></div>
        <form action="{{ route('customer.waiting-lists.store') }}" method="POST" id="waitingListForm">
            @csrf
            <input type="hidden" name="participant_id" id="waitingParticipantId">
            <input type="hidden" name="class_schedule_id" id="waitingClassScheduleId">
            <div class="modal-actions">
                <button type="button" class="secondary-button" data-close-waiting-list-modal>Batal</button>
                <button type="submit" class="confirm-booking-button" id="confirmWaitingListButton">Konfirmasi Antrean</button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

    .booking-page {
        width: 100%;
    }

    .booking-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .page-eyebrow,
    .section-label {
        display: block;
        margin-bottom: 4px;
        color: #0284c7;
        font-size: 8px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .package-link {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        flex-shrink: 0;
        border: 1px solid #bae6fd;
        border-radius: 9px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 9px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .package-link:hover {
        background: #e0f2fe;
    }


    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    */

    .booking-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 11px;
    }

    .booking-alert.error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .booking-alert .alert-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.75);
        font-size: 11px;
        font-weight: 900;
    }

    .booking-alert strong {
        display: block;
        font-size: 10px;
        font-weight: 850;
    }

    .booking-alert p {
        margin-top: 2px;
        font-size: 9px;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    .booking-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .summary-card {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.025);
    }

    .summary-icon {
        width: 39px;
        height: 39px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0f2fe, #cffafe);
        font-size: 15px;
    }

    .summary-card span {
        display: block;
        color: var(--text-light);
        font-size: 8px;
        font-weight: 700;
    }

    .summary-card strong {
        display: block;
        margin-top: 2px;
        color: var(--navy);
        font-size: 18px;
        font-weight: 900;
    }


    /*
    |--------------------------------------------------------------------------
    | Section
    |--------------------------------------------------------------------------
    */

    .section-card {
        margin-bottom: 18px;
        padding: 20px;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 17px;
    }

    .section-heading h2,
    .schedule-heading h2 {
        color: var(--navy);
        font-size: 15px;
        font-weight: 850;
    }

    .section-heading p,
    .schedule-heading p {
        max-width: 620px;
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty
    |--------------------------------------------------------------------------
    */

    .empty-state,
    .no-package-state,
    .schedule-empty-filter {
        padding: 32px 20px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        text-align: center;
    }

    .empty-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        border-radius: 12px;
        background: #ffffff;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }

    .empty-state h3,
    .no-package-state h3,
    .schedule-empty-filter h3 {
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }

    .empty-state p,
    .no-package-state p,
    .schedule-empty-filter p {
        max-width: 430px;
        margin: 4px auto 0;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.6;
    }

    .primary-button {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 14px;
        padding: 9px 16px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--blue), var(--blue-dark));
        color: #ffffff;
        font-size: 9px;
        font-weight: 850;
        text-decoration: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Active Booking
    |--------------------------------------------------------------------------
    */

    .active-booking-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .active-booking-card {
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        transition: 0.2s ease;
    }

    .active-booking-card:hover {
        border-color: #bae6fd;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .active-booking-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .booking-code {
        display: block;
        margin-bottom: 3px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        letter-spacing: 0.3px;
    }

    .active-booking-top h3 {
        color: var(--navy);
        font-size: 12px;
        font-weight: 850;
    }

    .booking-status {
        padding: 5px 8px;
        flex-shrink: 0;
        border-radius: 999px;
        font-size: 7px;
        font-weight: 850;
    }

    .booking-status.pending {
        background: #fef3c7;
        color: #a16207;
    }

    .booking-status.confirmed {
        background: #dcfce7;
        color: #15803d;
    }

    .active-participant {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        padding: 9px;
        border-radius: 9px;
        background: #f0f9ff;
    }

    .participant-avatar {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #ffffff;
        color: #0369a1;
        font-size: 10px;
        font-weight: 900;
    }

    .active-participant span {
        display: block;
        color: #0284c7;
        font-size: 7px;
    }

    .active-participant strong {
        display: block;
        margin-top: 1px;
        color: var(--navy);
        font-size: 9px;
        font-weight: 800;
    }

    .active-booking-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
        margin-top: 10px;
    }

    .active-booking-details > div {
        padding: 8px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .active-booking-details span,
    .active-package-info span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
    }

    .active-booking-details strong,
    .active-package-info strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .active-package-info {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }

    .active-package-info small {
        display: block;
        margin-top: 3px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 750;
    }

    .detail-booking-button {
        min-height: 33px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 11px;
        flex-shrink: 0;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: 8px;
        font-weight: 850;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .detail-booking-button:hover {
        border-color: #38bdf8;
        background: #e0f2fe;
    }


    /*
    |--------------------------------------------------------------------------
    | Package Selector
    |--------------------------------------------------------------------------
    */

    .package-selector {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .participant-package-card {
        position: relative;
        width: 100%;
        display: grid;
        grid-template-columns:
            minmax(0, 1.25fr)
            minmax(0, 1.25fr)
            75px
            105px;
        align-items: center;
        gap: 10px;
        padding: 13px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #ffffff;
        font-family: inherit;
        text-align: left;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .participant-package-card:hover {
        border-color: #7dd3fc;
        box-shadow: 0 5px 15px rgba(2, 132, 199, 0.06);
    }

    .participant-package-card.selected {
        border-color: #38bdf8;
        background: #f0f9ff;
        box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.08);
    }

    .selector-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #0284c7;
        color: #ffffff;
        font-size: 7px;
        font-weight: 900;
    }

    .participant-package-card.selected .selector-check {
        display: flex;
    }

    .selector-participant {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .selector-participant span,
    .selector-package > span,
    .selector-sessions span,
    .selector-expiry span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
    }

    .selector-participant strong,
    .selector-package strong,
    .selector-expiry strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .selector-package small {
        display: block;
        margin-top: 2px;
        color: #0284c7;
        font-size: 7px;
    }

    .selector-sessions {
        text-align: center;
    }

    .selector-sessions strong {
        display: block;
        margin-top: 2px;
        color: #0369a1;
        font-size: 18px;
        font-weight: 900;
    }


    /*
    |--------------------------------------------------------------------------
    | Selected Package
    |--------------------------------------------------------------------------
    */

    .selected-package-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 13px;
        padding: 11px;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        background: linear-gradient(135deg, #f0f9ff, #ecfeff);
    }

    .selected-package-info span {
        display: block;
        color: #0284c7;
        font-size: 7px;
        font-weight: 700;
    }

    .selected-package-info strong {
        display: block;
        margin-top: 2px;
        color: var(--navy);
        font-size: 9px;
        font-weight: 850;
    }


    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    .schedule-heading {
        margin-top: 25px;
        margin-bottom: 14px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 11px;
    }

    .schedule-card {
        display: none;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        transition: 0.2s ease;
    }

    .schedule-card.visible {
        display: block;
    }

    .schedule-card:hover {
        border-color: #bae6fd;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .schedule-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 9px;
    }

    .schedule-program {
        display: block;
        margin-bottom: 3px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .schedule-card-top h3 {
        color: var(--navy);
        font-size: 11px;
        font-weight: 850;
    }

    .slot-badge {
        padding: 5px 7px;
        flex-shrink: 0;
        border-radius: 999px;
        font-size: 7px;
        font-weight: 850;
    }

    .slot-badge.available {
        background: #dcfce7;
        color: #15803d;
    }

    .slot-badge.full {
        background: #fee2e2;
        color: #b91c1c;
    }

    .schedule-date {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 13px;
        padding: 10px;
        border-radius: 10px;
        background: #f8fafc;
    }

    .date-box {
        width: 45px;
        min-width: 45px;
        padding: 6px 4px;
        border-radius: 8px;
        background: #ffffff;
        text-align: center;
    }

    .date-box span {
        display: block;
        color: #0284c7;
        font-size: 6px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .date-box strong {
        display: block;
        margin: 1px 0;
        color: var(--navy);
        font-size: 17px;
        line-height: 1;
        font-weight: 900;
    }

    .date-box small {
        display: block;
        color: var(--text-soft);
        font-size: 6px;
    }

    .schedule-time span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
    }

    .schedule-time strong {
        display: block;
        margin-top: 2px;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .schedule-time small {
        display: block;
        margin-top: 2px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 750;
    }

    .schedule-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
        margin-top: 10px;
    }

    .schedule-detail {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        min-width: 0;
        padding: 7px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .detail-icon {
        flex-shrink: 0;
        font-size: 10px;
    }

    .schedule-detail div {
        min-width: 0;
    }

    .schedule-detail div > span {
        display: block;
        color: var(--text-light);
        font-size: 6px;
    }

    .schedule-detail strong {
        display: block;
        margin-top: 1px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 7px;
        font-weight: 800;
    }

    .schedule-detail small {
        display: block;
        margin-top: 1px;
        color: var(--text-light);
        font-size: 6px;
    }

    .schedule-notes {
        margin-top: 9px;
        padding: 8px;
        border-radius: 8px;
        background: #fffbeb;
        color: #92400e;
        font-size: 7px;
        line-height: 1.5;
    }

    .booking-button {
        width: 100%;
        min-height: 36px;
        margin-top: 11px;
        padding: 8px 10px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--blue), var(--blue-dark));
        color: #ffffff;
        font-family: inherit;
        font-size: 8px;
        font-weight: 850;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .booking-button:hover {
        transform: translateY(-1px);
    }

    .booking-button.disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }

    .schedule-empty-filter {
        margin-top: 10px;
    }


    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    .booking-modal {
        position: fixed;
        z-index: 9999;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .booking-modal.open {
        display: flex;
    }

    .booking-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
    }

    .booking-modal-dialog {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 520px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 23px;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2);
    }

    .modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        font-size: 15px;
        cursor: pointer;
    }

    .modal-icon {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 11px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e0f2fe, #cffafe);
        font-size: 18px;
    }

    .modal-label {
        display: block;
        margin-bottom: 3px;
        color: #0284c7;
        font-size: 7px;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .booking-modal-dialog h2 {
        color: var(--navy);
        font-size: 16px;
        font-weight: 900;
    }

    .modal-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 9px;
        line-height: 1.55;
    }

    .modal-information {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 16px;
    }

    .modal-information > div {
        padding: 10px;
        border-radius: 8px;
        background: #f8fafc;
    }

    .modal-information span {
        display: block;
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
    }

    .modal-information strong {
        display: block;
        margin-top: 2px;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .modal-notice {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 12px;
        padding: 9px;
        border-radius: 8px;
        background: #f0f9ff;
        color: #075985;
    }

    .modal-notice span {
        flex-shrink: 0;
    }

    .modal-notice p {
        margin: 0;
        font-size: 7px;
        line-height: 1.55;
    }

    .form-group {
        margin-top: 14px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #334155;
        font-size: 8px;
        font-weight: 800;
    }

    .form-group label span {
        color: var(--text-light);
        font-weight: 600;
    }

    .form-group textarea {
        width: 100%;
        padding: 10px 11px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        resize: vertical;
        font-family: inherit;
        font-size: 9px;
        transition: 0.2s ease;
    }

    .form-group textarea:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
    }

    .modal-actions {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 8px;
        margin-top: 16px;
    }

    .secondary-button,
    .confirm-booking-button {
        min-height: 39px;
        padding: 9px 12px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 8px;
        font-weight: 850;
        cursor: pointer;
    }

    .secondary-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    .confirm-booking-button {
        border: 0;
        background: linear-gradient(135deg, var(--blue), var(--blue-dark));
        color: #ffffff;
    }



    .waiting-list-accept-form {
        margin-top: 12px;
    }

    .waiting-list-package-select {
        width: 100%;
        min-height: 38px;
        padding: 9px 11px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-family: inherit;
        font-size: 10px;
    }

    .waiting-list-accept-button {
        width: 100%;
        min-height: 38px;
        margin-top: 10px;
        padding: 9px 12px;
        border: 0;
        border-radius: 8px;
        background: #15803d;
        color: #ffffff;
        font-family: inherit;
        font-size: 10px;
        font-weight: 800;
        cursor: pointer;
    }

    .waiting-list-decline-button {
        width: 100%;
        min-height: 38px;
        margin-top: 9px;
        padding: 9px 12px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fff1f2;
        color: #be123c;
        font-family: inherit;
        font-size: 10px;
        font-weight: 800;
        cursor: pointer;
    }
    /* Waiting List */
    .waiting-list-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .waiting-list-card { padding: 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
    .waiting-list-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .waiting-list-top h3 { color: var(--navy); font-size: 12px; font-weight: 850; }
    .waiting-list-status { padding: 5px 8px; border-radius: 999px; font-size: 8px; font-weight: 800; white-space: nowrap; }
    .status-waiting { background: #fef3c7; color: #92400e; }
    .status-offered { background: #dcfce7; color: #166534; }
    .status-accepted { background: #dbeafe; color: #1d4ed8; }
    .status-expired, .status-cancelled { background: #f1f5f9; color: #64748b; }
    .waiting-list-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 12px; }
    .waiting-list-details > div { padding: 9px; border-radius: 8px; background: #f8fafc; }
    .waiting-list-details span { display: block; color: var(--text-light); font-size: 8px; }
    .waiting-list-details strong { display: block; margin-top: 3px; color: #334155; font-size: 9px; }
    .waiting-list-notice { margin-top: 12px; padding: 11px; border-radius: 9px; background: #f0fdf4; color: #166534; }
    .waiting-list-notice strong { font-size: 10px; }
    .waiting-list-notice p, .waiting-list-help { margin-top: 7px; font-size: 9px; line-height: 1.6; color: #475569; }
    .cancel-waiting-list-button { margin-top: 12px; padding: 9px 12px; border: 1px solid #fecaca; border-radius: 8px; background: #fff1f2; color: #be123c; font: inherit; font-size: 9px; font-weight: 800; cursor: pointer; }
    .waiting-list-button { background: linear-gradient(135deg, #d97706, #b45309); }
    @media (max-width: 850px) { .waiting-list-grid { grid-template-columns: 1fr; } }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {

        .schedule-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .participant-package-card {
            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);
        }

    }

    @media (max-width: 850px) {

        .active-booking-grid,
        .package-selector {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 700px) {

        .booking-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .booking-summary-grid {
            grid-template-columns: 1fr;
        }

        .schedule-grid {
            grid-template-columns: 1fr;
        }

        .selected-package-info {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 500px) {

        .section-card {
            padding: 15px;
        }

        .active-booking-details,
        .schedule-details,
        .modal-information {
            grid-template-columns: 1fr;
        }

        .participant-package-card {
            grid-template-columns: 1fr;
        }

        .selector-sessions {
            text-align: left;
        }

        .active-package-info {
            align-items: stretch;
            flex-direction: column;
        }

        .detail-booking-button {
            width: 100%;
        }

        .modal-actions {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const packageSelectors =
        document.querySelectorAll(
            '[data-package-selector]'
        );

    const scheduleCards =
        document.querySelectorAll(
            '[data-schedule-card]'
        );

    const bookButtons =
        document.querySelectorAll(
            '[data-book-schedule]'
        );

    const modal =
        document.getElementById(
            'bookingModal'
        );

    const closeModalButtons =
        document.querySelectorAll(
            '[data-close-booking-modal]'
        );

    const selectedPackageName =
        document.getElementById(
            'selectedPackageName'
        );

    const selectedParticipantName =
        document.getElementById(
            'selectedParticipantName'
        );

    const selectedRemainingSessions =
        document.getElementById(
            'selectedRemainingSessions'
        );

    const scheduleInstruction =
        document.getElementById(
            'scheduleInstruction'
        );

    const scheduleEmptyFilter =
        document.getElementById(
            'scheduleEmptyFilter'
        );

    const participantPackageInput =
        document.getElementById(
            'bookingParticipantPackageId'
        );

    const classScheduleInput =
        document.getElementById(
            'bookingClassScheduleId'
        );

    const bookingForm =
        document.getElementById(
            'bookingForm'
        );

    const confirmBookingButton =
        document.getElementById(
            'confirmBookingButton'
        );


    let selectedParticipantId = null;
    let selectedParticipantPackageId = null;
    let selectedProgramId = null;
    let selectedParticipant = null;
    let selectedPackage = null;
    let selectedSessions = null;


    /*
    |--------------------------------------------------------------------------
    | Select Package
    |--------------------------------------------------------------------------
    */

    packageSelectors.forEach(function (selector) {

        selector.addEventListener(
            'click',
            function () {

                packageSelectors.forEach(
                    function (item) {

                        item.classList.remove(
                            'selected'
                        );

                    }
                );


                selector.classList.add(
                    'selected'
                );


                selectedParticipantId = selector.dataset.participantId;
                selectedParticipantPackageId =
                    selector.dataset
                        .participantPackageId;

                selectedProgramId =
                    selector.dataset.programId;

                selectedParticipant =
                    selector.dataset.participantName;

                selectedPackage =
                    selector.dataset.packageName;

                selectedSessions =
                    selector.dataset.remainingSessions;


                if (selectedPackageName) {
                    selectedPackageName.textContent =
                        selectedPackage;
                }

                if (selectedParticipantName) {
                    selectedParticipantName.textContent =
                        selectedParticipant;
                }

                if (selectedRemainingSessions) {
                    selectedRemainingSessions.textContent =
                        selectedSessions + ' sesi';
                }

                if (scheduleInstruction) {
                    scheduleInstruction.textContent =
                        'Menampilkan jadwal yang sesuai dengan program paket '
                        + selectedPackage
                        + '.';
                }


                let visibleCount = 0;


                scheduleCards.forEach(
                    function (scheduleCard) {

                        const scheduleProgramId =
                            scheduleCard.dataset
                                .programId;


                        if (
                            String(scheduleProgramId)
                            ===
                            String(selectedProgramId)
                        ) {

                            scheduleCard.classList.add(
                                'visible'
                            );

                            visibleCount++;

                        } else {

                            scheduleCard.classList.remove(
                                'visible'
                            );

                        }

                    }
                );


                if (scheduleEmptyFilter) {

                    scheduleEmptyFilter.hidden =
                        visibleCount !== 0;

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */

    bookButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                if (
                    !selectedParticipantPackageId
                ) {

                    alert(
                        'Silakan pilih paket peserta terlebih dahulu.'
                    );

                    return;
                }


                const scheduleId =
                    button.dataset.scheduleId;

                const scheduleTitle =
                    button.dataset.scheduleTitle;

                const scheduleDate =
                    button.dataset.scheduleDate;

                const scheduleTime =
                    button.dataset.scheduleTime;

                const locationName =
                    button.dataset.locationName;


                if (participantPackageInput) {
                    participantPackageInput.value =
                        selectedParticipantPackageId;
                }

                if (classScheduleInput) {
                    classScheduleInput.value =
                        scheduleId;
                }


                document
                    .getElementById(
                        'modalParticipant'
                    )
                    .textContent =
                        selectedParticipant;

                document
                    .getElementById(
                        'modalPackage'
                    )
                    .textContent =
                        selectedPackage;

                document
                    .getElementById(
                        'modalSchedule'
                    )
                    .textContent =
                        scheduleTitle;

                document
                    .getElementById(
                        'modalDate'
                    )
                    .textContent =
                        scheduleDate;

                document
                    .getElementById(
                        'modalTime'
                    )
                    .textContent =
                        scheduleTime;

                document
                    .getElementById(
                        'modalLocation'
                    )
                    .textContent =
                        locationName;


                if (modal) {

                    modal.classList.add(
                        'open'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.style.overflow =
                        'hidden';

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    function closeBookingModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'open'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow =
            '';

    }


    closeModalButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                closeBookingModal
            );

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
                &&
                modal
                &&
                modal.classList.contains(
                    'open'
                )
            ) {

                closeBookingModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Waiting List Modal
    |--------------------------------------------------------------------------
    */
    const waitingListModal = document.getElementById('waitingListModal');
    const waitingListForm = document.getElementById('waitingListForm');
    const waitingParticipantInput = document.getElementById('waitingParticipantId');
    const waitingScheduleInput = document.getElementById('waitingClassScheduleId');
    const waitingSubmitButton = document.getElementById('confirmWaitingListButton');
    const activeWaitingListKeys = @json($activeWaitingListKeys);

    function closeWaitingListModal() {
        if (!waitingListModal) return;
        waitingListModal.classList.remove('open');
        waitingListModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-close-waiting-list-modal]').forEach(function (button) {
        button.addEventListener('click', closeWaitingListModal);
    });

    document.querySelectorAll('[data-join-waiting-list]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!selectedParticipantId || !selectedParticipantPackageId) {
                alert('Silakan pilih paket peserta terlebih dahulu.');
                return;
            }

            const key = String(selectedParticipantId) + ':' + String(button.dataset.scheduleId);
            if (activeWaitingListKeys.includes(key)) {
                alert('Peserta ini sudah berada dalam Waiting List pada jadwal tersebut. Lihat bagian Waiting List Saya.');
                return;
            }

            waitingParticipantInput.value = selectedParticipantId;
            waitingScheduleInput.value = button.dataset.scheduleId;
            document.getElementById('waitingModalParticipant').textContent = selectedParticipant;
            document.getElementById('waitingModalPackage').textContent = selectedPackage;
            document.getElementById('waitingModalSchedule').textContent = button.dataset.scheduleTitle;
            document.getElementById('waitingModalDate').textContent = button.dataset.scheduleDate;
            document.getElementById('waitingModalTime').textContent = button.dataset.scheduleTime + ' WIB';

            waitingListModal.classList.add('open');
            waitingListModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && waitingListModal && waitingListModal.classList.contains('open')) {
            closeWaitingListModal();
        }
    });

    if (waitingListForm) {
        waitingListForm.addEventListener('submit', function (event) {
            if (!waitingParticipantInput.value || !waitingScheduleInput.value) {
                event.preventDefault();
                alert('Data peserta atau jadwal belum lengkap.');
                return;
            }
            waitingSubmitButton.disabled = true;
            waitingSubmitButton.textContent = 'Memproses Antrean...';
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent Double Submit
    |--------------------------------------------------------------------------
    */

    if (bookingForm) {

        bookingForm.addEventListener(
            'submit',
            function (event) {

                if (
                    !participantPackageInput.value
                    ||
                    !classScheduleInput.value
                ) {

                    event.preventDefault();

                    alert(
                        'Data paket atau jadwal belum lengkap.'
                    );

                    return;
                }


                if (confirmBookingButton) {

                    confirmBookingButton.disabled =
                        true;

                    confirmBookingButton.textContent =
                        'Memproses Booking...';

                }

            }
        );

    }

});

</script>

@endpush
