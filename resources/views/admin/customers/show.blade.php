@extends('layouts.admin')

@section('title', 'Detail Customer')
@section('topbar-title', 'Detail Customer')

@section('content')

@php
    $statusLabels = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'suspended' => 'Ditangguhkan',
    ];

    $participantStatusLabels = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
    ];

    $levelLabels = [
        'beginner' => 'Pemula',
        'basic' => 'Dasar',
        'intermediate' => 'Menengah',
        'advanced' => 'Lanjutan',
    ];

    $participantTypeLabels = [
        'child' => 'Anak',
        'self' => 'Diri Sendiri',
    ];

    $packageStatusLabels = [
        'pending' => 'Menunggu',
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'expired' => 'Kedaluwarsa',
        'frozen' => 'Dibekukan',
        'cancelled' => 'Dibatalkan',
    ];

    $customerStatus =
        $statusLabels[$customer->status]
        ?? ucfirst($customer->status);

    $initials = collect(
        preg_split('/\s+/', trim($customer->name))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return strtoupper(
                mb_substr($word, 0, 1)
            );
        })
        ->implode('');

    $totalBookings = $customer->participants
        ->sum('bookings_count');

    $totalProgress = $customer->participants
        ->sum('progress_records_count');

    $activePackages = $customer->participants
        ->flatMap(function ($participant) {
            return $participant->participantPackages;
        })
        ->where('status', 'active')
        ->count();

    $remainingSessions = $customer->participants
        ->flatMap(function ($participant) {
            return $participant->participantPackages;
        })
        ->where('status', 'active')
        ->sum('remaining_sessions');
@endphp


{{-- =========================================================
    BACK
========================================================== --}}

<div class="detail-navigation">

    <a
        href="{{ route('admin.customers.index') }}"
        class="back-link"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M15 6L9 12L15 18"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>

        Kembali ke Daftar Customer
    </a>

</div>


{{-- =========================================================
    CUSTOMER PROFILE
========================================================== --}}

<section class="customer-profile-card">

    <div class="customer-profile-main">

        <div class="customer-large-avatar">
            {{ $initials ?: 'C' }}
        </div>


        <div class="customer-main-information">

            <div class="customer-name-row">

                <h1>
                    {{ $customer->name }}
                </h1>


                @if ($customer->status === 'active')

                    <span class="status-badge status-active">
                        <span class="status-dot"></span>
                        {{ $customerStatus }}
                    </span>

                @elseif ($customer->status === 'suspended')

                    <span class="status-badge status-suspended">
                        <span class="status-dot"></span>
                        {{ $customerStatus }}
                    </span>

                @else

                    <span class="status-badge status-inactive">
                        <span class="status-dot"></span>
                        {{ $customerStatus }}
                    </span>

                @endif

            </div>


            <p class="customer-id">
                Customer #{{ $customer->id }}
            </p>


            <div class="customer-contact-grid">

                <div class="customer-contact-item">

                    <span class="contact-label">
                        Email
                    </span>

                    <span class="contact-value">
                        {{ $customer->email }}
                    </span>

                </div>


                <div class="customer-contact-item">

                    <span class="contact-label">
                        Nomor Telepon
                    </span>

                    <span class="contact-value">
                        {{ $customer->phone ?: 'Belum tersedia' }}
                    </span>

                </div>


                <div class="customer-contact-item">

                    <span class="contact-label">
                        Terdaftar
                    </span>

                    <span class="contact-value">
                        {{
                            $customer->created_at
                                ? $customer->created_at->translatedFormat('d F Y')
                                : '-'
                        }}
                    </span>

                </div>


                <div class="customer-contact-item">

                    <span class="contact-label">
                        Role
                    </span>

                    <span class="contact-value">
                        Customer
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    ACCOUNT MANAGEMENT
========================================================== --}}

<section
    class="account-management-card
    {{ $customer->status === 'suspended' ? 'account-suspended' : '' }}"
>

    <div class="account-management-content">

        <div
            class="account-management-icon
            {{ $customer->status === 'suspended' ? 'account-icon-success' : 'account-icon-warning' }}"
        >

            @if ($customer->status === 'suspended')

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M12 3L19 6V11C19 15.5 16.3 19.2 12 21C7.7 19.2 5 15.5 5 11V6L12 3Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M9 12L11 14L15.5 9.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            @else

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M12 3L19 6V11C19 15.5 16.3 19.2 12 21C7.7 19.2 5 15.5 5 11V6L12 3Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M12 8V12"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />

                    <circle
                        cx="12"
                        cy="15.5"
                        r="1"
                        fill="currentColor"
                    />
                </svg>

            @endif

        </div>


        <div class="account-management-text">

            <span class="account-management-label">
                Manajemen Status Akun
            </span>


            @if ($customer->status === 'active')

                <h2>
                    Akun customer sedang aktif
                </h2>

                <p>
                    Admin dapat menangguhkan akun
                    <strong>{{ $customer->name }}</strong>.
                    Data customer, peserta, paket, booking,
                    pembayaran, dan perkembangan tetap tersimpan.
                </p>

            @elseif ($customer->status === 'suspended')

                <h2>
                    Akun customer sedang ditangguhkan
                </h2>

                <p>
                    Admin dapat mengaktifkan kembali akun
                    <strong>{{ $customer->name }}</strong>
                    agar status akun kembali menjadi aktif.
                </p>

            @else

                <h2>
                    Status akun: {{ $customerStatus }}
                </h2>

                <p>
                    Status ini tidak dapat diubah melalui
                    tindakan cepat pada halaman detail customer.
                </p>

            @endif

        </div>

    </div>


    <div class="account-management-action">

        @if ($customer->status === 'active')

            <form
                method="POST"
                action="{{ route('admin.customers.status.update', $customer) }}"
                class="status-action-form"
                onsubmit="return confirm('Yakin ingin menangguhkan akun {{ addslashes($customer->name) }}? Status akun akan diubah menjadi Ditangguhkan.');"
            >
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="status"
                    value="suspended"
                >

                <button
                    type="submit"
                    class="account-action-button suspend-button"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M9.5 9.5L14.5 14.5M14.5 9.5L9.5 14.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                    Tangguhkan Akun
                </button>

            </form>

        @elseif ($customer->status === 'suspended')

            <form
                method="POST"
                action="{{ route('admin.customers.status.update', $customer) }}"
                class="status-action-form"
                onsubmit="return confirm('Yakin ingin mengaktifkan kembali akun {{ addslashes($customer->name) }}?');"
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
                    class="account-action-button activate-button"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="8"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M8.5 12L11 14.5L15.5 9.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Aktifkan Kembali
                </button>

            </form>

        @else

            <span class="account-action-unavailable">
                Tidak ada tindakan tersedia
            </span>

        @endif

    </div>

</section>

@include('admin.customers.password-reset-action')

{{-- =========================================================
    STATISTICS
========================================================== --}}

<section class="detail-statistics">

    <div class="detail-stat-card">

        <div class="stat-icon stat-blue">

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

        <div>
            <span class="stat-label">
                Peserta
            </span>

            <strong class="stat-value">
                {{ number_format($customer->participants_count) }}
            </strong>

            <span class="stat-description">
                Profil peserta
            </span>
        </div>

    </div>


    <div class="detail-stat-card">

        <div class="stat-icon stat-green">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M4 7.5L12 4L20 7.5L12 11L4 7.5Z"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />

                <path
                    d="M4 12L12 15.5L20 12"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />

                <path
                    d="M4 16.5L12 20L20 16.5"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
            </svg>

        </div>

        <div>
            <span class="stat-label">
                Paket Aktif
            </span>

            <strong class="stat-value">
                {{ number_format($activePackages) }}
            </strong>

            <span class="stat-description">
                Sedang digunakan
            </span>
        </div>

    </div>


    <div class="detail-stat-card">

        <div class="stat-icon stat-aqua">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <circle
                    cx="12"
                    cy="12"
                    r="8"
                    stroke="currentColor"
                    stroke-width="1.8"
                />

                <path
                    d="M12 8V12L14.5 14"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

        </div>

        <div>
            <span class="stat-label">
                Sisa Sesi
            </span>

            <strong class="stat-value">
                {{ number_format($remainingSessions) }}
            </strong>

            <span class="stat-description">
                Dari paket aktif
            </span>
        </div>

    </div>


    <div class="detail-stat-card">

        <div class="stat-icon stat-purple">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <rect
                    x="4"
                    y="5"
                    width="16"
                    height="15"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.8"
                />

                <path
                    d="M8 3V7M16 3V7M4 10H20"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <span class="stat-label">
                Booking
            </span>

            <strong class="stat-value">
                {{ number_format($totalBookings) }}
            </strong>

            <span class="stat-description">
                Seluruh riwayat
            </span>
        </div>

    </div>


    <div class="detail-stat-card">

        <div class="stat-icon stat-orange">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M5 19V10M10 19V5M15 19V13M20 19V8"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <span class="stat-label">
                Penilaian
            </span>

            <strong class="stat-value">
                {{ number_format($totalProgress) }}
            </strong>

            <span class="stat-description">
                Catatan perkembangan
            </span>
        </div>

    </div>


    <div class="detail-stat-card">

        <div class="stat-icon stat-navy">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <rect
                    x="4"
                    y="4"
                    width="16"
                    height="16"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.8"
                />

                <path
                    d="M8 9H16M8 13H16M8 17H12"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <div>
            <span class="stat-label">
                Order
            </span>

            <strong class="stat-value">
                {{ number_format($customer->orders_count) }}
            </strong>

            <span class="stat-description">
                Seluruh transaksi
            </span>
        </div>

    </div>

</section>


{{-- =========================================================
    PARTICIPANTS
========================================================== --}}

<section class="participants-section">

    <div class="section-heading">

        <div>

            <h2>
                Peserta Milik Customer
            </h2>

            <p>
                Seluruh profil peserta yang terhubung dengan
                akun {{ $customer->name }}.
            </p>

        </div>


        <div class="section-count">
            {{ $customer->participants->count() }} Peserta
        </div>

    </div>


    @if ($customer->participants->isEmpty())

        <div class="participant-empty">

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

            <strong>
                Belum ada peserta
            </strong>

            <span>
                Customer ini belum mempunyai profil peserta.
            </span>

        </div>

    @else

        <div class="participant-list">

            @foreach ($customer->participants as $participant)

                @php
                    $participantInitials = collect(
                        preg_split(
                            '/\s+/',
                            trim($participant->full_name)
                        )
                    )
                        ->filter()
                        ->take(2)
                        ->map(function ($word) {
                            return strtoupper(
                                mb_substr($word, 0, 1)
                            );
                        })
                        ->implode('');

                    $participantStatus =
                        $participantStatusLabels[$participant->status]
                        ?? ucfirst($participant->status);

                    $participantLevel =
                        $levelLabels[$participant->swimming_level]
                        ?? ucfirst($participant->swimming_level ?? '-');

                    $participantType =
                        $participantTypeLabels[$participant->participant_type]
                        ?? ucfirst($participant->participant_type ?? '-');

                    $participantAge = $participant->birth_date
                        ? $participant->birth_date->age
                        : null;

                    $participantActivePackages =
                        $participant->participantPackages
                            ->where('status', 'active');

                    $participantRemainingSessions =
                        $participantActivePackages
                            ->sum('remaining_sessions');
                @endphp


                <article class="participant-card">

                    {{-- HEADER --}}
                    <div class="participant-header">

                        <div class="participant-identity">

                            <div class="participant-avatar">
                                {{ $participantInitials ?: 'P' }}
                            </div>


                            <div>

                                <div class="participant-name-row">

                                    <h3>
                                        {{ $participant->full_name }}
                                    </h3>


                                    @if ($participant->status === 'active')

                                        <span class="participant-status participant-active">
                                            {{ $participantStatus }}
                                        </span>

                                    @else

                                        <span class="participant-status participant-inactive">
                                            {{ $participantStatus }}
                                        </span>

                                    @endif

                                </div>


                                <p>
                                    Peserta #{{ $participant->id }}
                                    ·
                                    {{ $participantType }}
                                </p>

                            </div>

                        </div>


                        <div class="participant-level">

                            <span>
                                Level Renang
                            </span>

                            <strong>
                                {{ $participantLevel }}
                            </strong>

                        </div>

                    </div>


                    {{-- INFORMATION --}}
                    <div class="participant-information">

                        <div class="information-item">

                            <span class="information-label">
                                Jenis Kelamin
                            </span>

                            <strong>
                                @if ($participant->gender === 'male')
                                    Laki-laki
                                @elseif ($participant->gender === 'female')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </strong>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Tanggal Lahir
                            </span>

                            <strong>
                                {{
                                    $participant->birth_date
                                        ? $participant->birth_date->translatedFormat('d M Y')
                                        : '-'
                                }}
                            </strong>

                            @if ($participantAge !== null)

                                <small>
                                    {{ $participantAge }} tahun
                                </small>

                            @endif

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Kontak Darurat
                            </span>

                            <strong>
                                {{ $participant->emergency_contact_name ?: '-' }}
                            </strong>

                            <small>
                                {{ $participant->emergency_contact_phone ?: 'Nomor belum tersedia' }}
                            </small>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Booking
                            </span>

                            <strong>
                                {{ number_format($participant->bookings_count) }}
                            </strong>

                            <small>
                                total booking
                            </small>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Penilaian
                            </span>

                            <strong>
                                {{ number_format($participant->progress_records_count) }}
                            </strong>

                            <small>
                                catatan perkembangan
                            </small>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Sisa Sesi
                            </span>

                            <strong>
                                {{ number_format($participantRemainingSessions) }}
                            </strong>

                            <small>
                                dari paket aktif
                            </small>

                        </div>

                    </div>


                    {{-- NOTES --}}
                    @if ($participant->notes)

                        <div class="participant-notes">

                            <span>
                                Catatan Peserta
                            </span>

                            <p>
                                {{ $participant->notes }}
                            </p>

                        </div>

                    @endif


                    {{-- PACKAGES --}}
                    <div class="package-area">

                        <div class="package-heading">

                            <div>
                                <h4>
                                    Paket Peserta
                                </h4>

                                <p>
                                    Riwayat paket yang dimiliki peserta.
                                </p>
                            </div>

                            <span>
                                {{ $participant->participantPackages->count() }}
                                Paket
                            </span>

                        </div>


                        @if ($participant->participantPackages->isEmpty())

                            <div class="package-empty">
                                Peserta belum mempunyai paket.
                            </div>

                        @else

                            <div class="package-table-wrapper">

                                <table class="package-table">

                                    <thead>
                                        <tr>
                                            <th>Paket</th>
                                            <th>Program</th>
                                            <th>Sesi</th>
                                            <th>Periode</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>


                                    <tbody>

                                        @foreach ($participant->participantPackages as $participantPackage)

                                            @php
                                                $packageStatus =
                                                    $packageStatusLabels[$participantPackage->status]
                                                    ?? ucfirst($participantPackage->status);

                                                $packageName =
                                                    $participantPackage->package?->name
                                                    ?? 'Paket #' . $participantPackage->package_id;

                                                $programName =
                                                    $participantPackage->package?->program?->name
                                                    ?? '-';
                                            @endphp

                                            <tr>

                                                <td>
                                                    <strong class="package-name">
                                                        {{ $packageName }}
                                                    </strong>
                                                </td>


                                                <td>
                                                    {{ $programName }}
                                                </td>


                                                <td>

                                                    <div class="session-information">

                                                        <strong>
                                                            {{ $participantPackage->remaining_sessions }}
                                                        </strong>

                                                        <span>
                                                            sisa dari
                                                            {{ $participantPackage->total_sessions }}
                                                        </span>

                                                    </div>

                                                </td>


                                                <td>

                                                    <div class="package-period">

                                                        <span>
                                                            {{
                                                                $participantPackage->start_date
                                                                    ? $participantPackage->start_date->translatedFormat('d M Y')
                                                                    : '-'
                                                            }}
                                                        </span>

                                                        <small>
                                                            s/d
                                                        </small>

                                                        <span>
                                                            {{
                                                                $participantPackage->expiry_date
                                                                    ? $participantPackage->expiry_date->translatedFormat('d M Y')
                                                                    : '-'
                                                            }}
                                                        </span>

                                                    </div>

                                                </td>


                                                <td>

                                                    <span class="package-status package-status-{{ $participantPackage->status }}">
                                                        {{ $packageStatus }}
                                                    </span>

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        @endif

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</section>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    .detail-navigation {
        margin-bottom: 18px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        color: #64748b;

        font-size: 11px;
        font-weight: 700;

        text-decoration: none;
    }

    .back-link:hover {
        color: #0284c7;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }


    /*
    |--------------------------------------------------------------------------
    | Customer Profile
    |--------------------------------------------------------------------------
    */

    .customer-profile-card {
        padding: 25px;

        margin-bottom: 18px;

        border: 1px solid #e2e8f0;
        border-radius: 18px;

        background:
            linear-gradient(
                135deg,
                #ffffff 0%,
                #f0f9ff 100%
            );

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .customer-profile-main {
        display: flex;
        align-items: center;

        gap: 20px;
    }

    .customer-large-avatar {
        width: 78px;
        height: 78px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 20px;

        background:
            linear-gradient(
                135deg,
                #0284c7,
                #06b6d4
            );

        color: #ffffff;

        font-size: 23px;
        font-weight: 850;

        box-shadow:
            0 8px 20px rgba(2, 132, 199, 0.18);
    }

    .customer-main-information {
        flex: 1;
        min-width: 0;
    }

    .customer-name-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;

        gap: 10px;
    }

    .customer-name-row h1 {
        color: #082f49;

        font-size: 24px;
        font-weight: 850;

        letter-spacing: -0.5px;
    }

    .customer-id {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 10px;
        font-weight: 650;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;

        gap: 6px;

        padding: 5px 9px;

        border-radius: 999px;

        font-size: 9px;
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

    .status-suspended {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .customer-contact-grid {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));

        gap: 12px;

        margin-top: 19px;
    }

    .customer-contact-item {
        min-width: 0;

        padding-right: 10px;
    }

    .contact-label {
        display: block;

        color: #94a3b8;

        font-size: 9px;
        font-weight: 750;

        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .contact-value {
        display: block;

        margin-top: 5px;

        overflow: hidden;

        color: #334155;

        font-size: 11px;
        font-weight: 650;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Account Management
    |--------------------------------------------------------------------------
    */

    .account-management-card {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 25px;

        margin-bottom: 18px;
        padding: 18px 20px;

        border: 1px solid #fde68a;
        border-radius: 15px;

        background:
            linear-gradient(
                135deg,
                #fffbeb,
                #ffffff
            );

        box-shadow:
            0 3px 12px rgba(15, 23, 42, 0.025);
    }

    .account-management-card.account-suspended {
        border-color: #bae6fd;

        background:
            linear-gradient(
                135deg,
                #f0f9ff,
                #ffffff
            );
    }

    .account-management-content {
        display: flex;
        align-items: flex-start;

        gap: 13px;

        min-width: 0;
    }

    .account-management-icon {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;
    }

    .account-management-icon svg {
        width: 22px;
        height: 22px;
    }

    .account-icon-warning {
        background: #fef3c7;
        color: #b45309;
    }

    .account-icon-success {
        background: #e0f2fe;
        color: #0369a1;
    }

    .account-management-text {
        min-width: 0;
    }

    .account-management-label {
        display: block;

        margin-bottom: 4px;

        color: #94a3b8;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .account-management-text h2 {
        color: #334155;

        font-size: 12px;
        font-weight: 800;
    }

    .account-management-text p {
        max-width: 700px;

        margin-top: 4px;

        color: #64748b;

        font-size: 9px;
        line-height: 1.6;
    }

    .account-management-text p strong {
        color: #475569;
        font-weight: 750;
    }

    .account-management-action {
        flex-shrink: 0;
    }

    .status-action-form {
        margin: 0;
    }

    .account-action-button {
        min-height: 38px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        padding: 9px 13px;

        border-radius: 9px;

        font-size: 9px;
        font-weight: 800;

        transition:
            background 0.2s ease,
            border-color 0.2s ease,
            color 0.2s ease;
    }

    .account-action-button svg {
        width: 16px;
        height: 16px;
    }

    .suspend-button {
        border: 1px solid #fecaca;

        background: #ffffff;
        color: #dc2626;
    }

    .suspend-button:hover {
        border-color: #dc2626;

        background: #fef2f2;
    }

    .activate-button {
        border: 1px solid #bbf7d0;

        background: #ffffff;
        color: #15803d;
    }

    .activate-button:hover {
        border-color: #16a34a;

        background: #f0fdf4;
    }

    .account-action-unavailable {
        display: inline-flex;

        padding: 8px 11px;

        border-radius: 8px;

        background: #f1f5f9;
        color: #94a3b8;

        font-size: 8px;
        font-weight: 700;
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .detail-statistics {
        display: grid;
        grid-template-columns:
            repeat(6, minmax(0, 1fr));

        gap: 12px;

        margin-bottom: 22px;
    }

    .detail-stat-card {
        min-width: 0;

        display: flex;
        align-items: center;

        gap: 11px;

        padding: 16px;

        border: 1px solid #e2e8f0;
        border-radius: 14px;

        background: #ffffff;

        box-shadow:
            0 3px 12px rgba(15, 23, 42, 0.03);
    }

    .stat-icon {
        width: 39px;
        height: 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 11px;
    }

    .stat-icon svg {
        width: 20px;
        height: 20px;
    }

    .stat-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .stat-green {
        background: #f0fdf4;
        color: #16a34a;
    }

    .stat-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-purple {
        background: #faf5ff;
        color: #9333ea;
    }

    .stat-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .stat-navy {
        background: #f1f5f9;
        color: #334155;
    }

    .stat-label {
        display: block;

        color: #94a3b8;

        font-size: 8px;
        font-weight: 750;

        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stat-value {
        display: block;

        margin-top: 2px;

        color: #082f49;

        font-size: 19px;
        font-weight: 850;
    }

    .stat-description {
        display: block;

        margin-top: 1px;

        overflow: hidden;

        color: #94a3b8;

        font-size: 8px;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant Section
    |--------------------------------------------------------------------------
    */

    .participants-section {
        overflow: hidden;

        border: 1px solid #e2e8f0;
        border-radius: 17px;

        background: #ffffff;

        box-shadow:
            0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding: 21px 23px;

        border-bottom: 1px solid #eef2f7;
    }

    .section-heading h2 {
        color: #082f49;

        font-size: 16px;
        font-weight: 800;
    }

    .section-heading p {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 10px;
        line-height: 1.5;
    }

    .section-count {
        flex-shrink: 0;

        padding: 6px 10px;

        border-radius: 999px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 9px;
        font-weight: 800;
    }

    .participant-list {
        padding: 18px;

        background: #f8fafc;
    }

    .participant-card {
        overflow: hidden;

        border: 1px solid #e2e8f0;
        border-radius: 15px;

        background: #ffffff;
    }

    .participant-card + .participant-card {
        margin-top: 15px;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant Header
    |--------------------------------------------------------------------------
    */

    .participant-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        padding: 18px 20px;

        border-bottom: 1px solid #f1f5f9;
    }

    .participant-identity {
        display: flex;
        align-items: center;

        gap: 11px;

        min-width: 0;
    }

    .participant-avatar {
        width: 43px;
        height: 43px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background:
            linear-gradient(
                135deg,
                #e0f2fe,
                #cffafe
            );

        color: #0369a1;

        font-size: 12px;
        font-weight: 850;
    }

    .participant-name-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;

        gap: 7px;
    }

    .participant-name-row h3 {
        color: #0f172a;

        font-size: 13px;
        font-weight: 800;
    }

    .participant-identity p {
        margin-top: 4px;

        color: #94a3b8;

        font-size: 9px;
    }

    .participant-status {
        padding: 4px 7px;

        border-radius: 999px;

        font-size: 8px;
        font-weight: 800;
    }

    .participant-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .participant-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .participant-level {
        flex-shrink: 0;

        text-align: right;
    }

    .participant-level span {
        display: block;

        color: #94a3b8;

        font-size: 8px;
        font-weight: 750;

        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .participant-level strong {
        display: block;

        margin-top: 3px;

        color: #0284c7;

        font-size: 11px;
        font-weight: 800;
    }


    /*
    |--------------------------------------------------------------------------
    | Participant Information
    |--------------------------------------------------------------------------
    */

    .participant-information {
        display: grid;
        grid-template-columns:
            repeat(6, minmax(0, 1fr));

        border-bottom: 1px solid #f1f5f9;
    }

    .information-item {
        min-width: 0;

        padding: 15px 17px;

        border-right: 1px solid #f1f5f9;
    }

    .information-item:last-child {
        border-right: 0;
    }

    .information-label {
        display: block;

        color: #94a3b8;

        font-size: 8px;
        font-weight: 750;

        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .information-item strong {
        display: block;

        margin-top: 5px;

        overflow: hidden;

        color: #334155;

        font-size: 10px;
        font-weight: 700;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .information-item small {
        display: block;

        margin-top: 3px;

        overflow: hidden;

        color: #94a3b8;

        font-size: 8px;

        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

    .participant-notes {
        padding: 13px 18px;

        border-bottom: 1px solid #f1f5f9;

        background: #fffbeb;
    }

    .participant-notes span {
        display: block;

        color: #b45309;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .participant-notes p {
        margin-top: 4px;

        color: #92400e;

        font-size: 9px;
        line-height: 1.6;
    }


    /*
    |--------------------------------------------------------------------------
    | Packages
    |--------------------------------------------------------------------------
    */

    .package-area {
        padding: 17px 19px 19px;
    }

    .package-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        margin-bottom: 12px;
    }

    .package-heading h4 {
        color: #334155;

        font-size: 11px;
        font-weight: 800;
    }

    .package-heading p {
        margin-top: 2px;

        color: #94a3b8;

        font-size: 8px;
    }

    .package-heading > span {
        flex-shrink: 0;

        color: #64748b;

        font-size: 9px;
        font-weight: 700;
    }

    .package-table-wrapper {
        overflow-x: auto;

        border: 1px solid #eef2f7;
        border-radius: 10px;
    }

    .package-table {
        width: 100%;

        border-collapse: collapse;
    }

    .package-table th {
        padding: 9px 12px;

        border-bottom: 1px solid #e2e8f0;

        background: #f8fafc;
        color: #94a3b8;

        font-size: 8px;
        font-weight: 800;

        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.3px;

        white-space: nowrap;
    }

    .package-table td {
        padding: 11px 12px;

        border-bottom: 1px solid #f1f5f9;

        color: #64748b;

        font-size: 9px;

        vertical-align: middle;
    }

    .package-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .package-name {
        color: #334155;

        font-size: 9px;
        font-weight: 750;
    }

    .session-information strong {
        display: block;

        color: #0284c7;

        font-size: 11px;
        font-weight: 850;
    }

    .session-information span {
        display: block;

        margin-top: 2px;

        color: #94a3b8;

        font-size: 8px;
    }

    .package-period span {
        display: block;

        color: #475569;

        font-size: 8px;
    }

    .package-period small {
        display: block;

        margin: 2px 0;

        color: #cbd5e1;

        font-size: 7px;
    }

    .package-status {
        display: inline-flex;

        padding: 4px 7px;

        border-radius: 999px;

        font-size: 8px;
        font-weight: 800;

        white-space: nowrap;
    }

    .package-status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .package-status-pending {
        background: #fffbeb;
        color: #b45309;
    }

    .package-status-completed {
        background: #eff6ff;
        color: #2563eb;
    }

    .package-status-expired {
        background: #f1f5f9;
        color: #64748b;
    }

    .package-status-frozen {
        background: #ecfeff;
        color: #0e7490;
    }

    .package-status-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .package-empty {
        padding: 17px;

        border: 1px dashed #dbe3ed;
        border-radius: 10px;

        background: #fafcff;
        color: #94a3b8;

        font-size: 9px;

        text-align: center;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Participant
    |--------------------------------------------------------------------------
    */

    .participant-empty {
        min-height: 280px;

        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        padding: 35px;

        text-align: center;
    }

    .empty-icon {
        width: 52px;
        height: 52px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 12px;

        border-radius: 14px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .empty-icon svg {
        width: 25px;
        height: 25px;
    }

    .participant-empty strong {
        color: #334155;

        font-size: 12px;
        font-weight: 750;
    }

    .participant-empty span {
        margin-top: 5px;

        color: #94a3b8;

        font-size: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1250px) {

        .detail-statistics {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }

        .participant-information {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }

        .information-item:nth-child(3) {
            border-right: 0;
        }

        .information-item:nth-child(-n+3) {
            border-bottom: 1px solid #f1f5f9;
        }
    }


    @media (max-width: 850px) {

        .account-management-card {
            align-items: flex-start;
            flex-direction: column;
        }

        .account-management-action {
            width: 100%;
        }

        .account-action-button {
            width: 100%;
        }

        .customer-contact-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .participant-information {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .information-item {
            border-bottom: 1px solid #f1f5f9;
        }

        .information-item:nth-child(odd) {
            border-right: 1px solid #f1f5f9;
        }

        .information-item:nth-child(even) {
            border-right: 0;
        }

        .information-item:nth-last-child(-n+2) {
            border-bottom: 0;
        }
    }


    @media (max-width: 650px) {

        .customer-profile-main {
            align-items: flex-start;
            flex-direction: column;
        }

        .customer-large-avatar {
            width: 62px;
            height: 62px;

            border-radius: 17px;

            font-size: 18px;
        }

        .customer-name-row h1 {
            font-size: 20px;
        }

        .detail-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .participant-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .participant-level {
            text-align: left;
        }
    }


    @media (max-width: 480px) {

        .customer-profile-card {
            padding: 19px;
        }

        .customer-contact-grid {
            grid-template-columns: 1fr;
        }

        .detail-statistics {
            grid-template-columns: 1fr;
        }

        .section-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .participant-list {
            padding: 12px;
        }

        .participant-information {
            grid-template-columns: 1fr;
        }

        .information-item,
        .information-item:nth-child(odd),
        .information-item:nth-child(even) {
            border-right: 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .information-item:last-child {
            border-bottom: 0;
        }
    }

</style>

@endpush
