@extends('layouts.admin')

@section('title', 'Detail Paket')
@section('topbar-title', 'Detail Paket')

@push('styles')
<style>
    .detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .detail-header-main {
        min-width: 0;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 8px;
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 650;
    }

    .breadcrumb a {
        color: var(--blue);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .detail-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .detail-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 850;
        letter-spacing: -0.7px;
    }

    .detail-description {
        max-width: 760px;
        margin-top: 7px;
        color: var(--text-soft);
        font-size: 14px;
        line-height: 1.65;
    }

    .detail-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-shrink: 0;
    }

    .button-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-active {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-active::before {
        background: #22c55e;
    }

    .status-inactive {
        background: #f8fafc;
        color: #64748b;
    }

    .status-inactive::before {
        background: #94a3b8;
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .stat-card {
        min-height: 122px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 19px;
    }

    .stat-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 700;
    }

    .stat-value {
        margin-top: 7px;
        color: var(--navy);
        font-size: 25px;
        font-weight: 850;
        letter-spacing: -0.5px;
    }

    .stat-value.price {
        font-size: 21px;
    }

    .stat-description {
        margin-top: 4px;
        color: var(--text-light);
        font-size: 10px;
        line-height: 1.5;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .stat-icon.aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .stat-icon.green {
        background: #f0fdf4;
        color: #15803d;
    }

    .stat-icon.slate {
        background: #f8fafc;
        color: #64748b;
    }

    .stat-icon svg {
        width: 21px;
        height: 21px;
    }

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 20px;
        align-items: start;
    }

    .main-column,
    .side-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .section-card {
        overflow: hidden;
    }

    .section-header {
        padding: 19px 20px;
        border-bottom: 1px solid var(--border);
    }

    .section-title {
        color: var(--navy);
        font-size: 15px;
        font-weight: 800;
    }

    .section-subtitle {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .section-body {
        padding: 20px;
    }

    /*
    |--------------------------------------------------------------------------
    | Package Information
    |--------------------------------------------------------------------------
    */

    .information-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .information-item.full {
        grid-column: 1 / -1;
    }

    .information-label {
        margin-bottom: 6px;
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.55px;
    }

    .information-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 750;
        line-height: 1.6;
    }

    .information-value.regular {
        color: #475569;
        font-weight: 450;
        white-space: pre-line;
    }

    .program-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fafc;
    }

    .program-name {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .program-description {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.55;
    }

    .program-link {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 7px 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--white);
        color: var(--navy);
        text-decoration: none;
        font-size: 10px;
        font-weight: 750;
    }

    .program-link:hover {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: var(--blue-dark);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Card
    |--------------------------------------------------------------------------
    */

    .status-card {
        padding: 20px;
    }

    .status-card-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .status-card-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .status-current {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 17px;
        padding: 13px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #f8fafc;
    }

    .status-current-label {
        color: var(--text-soft);
        font-size: 10px;
        font-weight: 700;
    }

    .status-form {
        margin-top: 14px;
    }

    .status-button {
        width: 100%;
        min-height: 41px;
        padding: 9px 13px;
        border-radius: 9px;
        font-size: 11px;
        font-weight: 800;
    }

    .status-button.deactivate {
        border: 1px solid #fecaca;
        background: var(--white);
        color: #dc2626;
    }

    .status-button.deactivate:hover {
        background: #fef2f2;
    }

    .status-button.activate {
        border: 1px solid #bbf7d0;
        background: var(--white);
        color: #15803d;
    }

    .status-button.activate:hover {
        background: #f0fdf4;
    }

    /*
    |--------------------------------------------------------------------------
    | Quick Action
    |--------------------------------------------------------------------------
    */

    .quick-card {
        padding: 20px;
    }

    .quick-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .quick-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.6;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 9px;
        margin-top: 16px;
    }

    .quick-actions .btn-primary,
    .quick-actions .btn-secondary {
        width: 100%;
    }

    /*
    |--------------------------------------------------------------------------
    | Note
    |--------------------------------------------------------------------------
    */

    .note-card {
        padding: 16px;
        border: 1px solid #bae6fd;
        border-radius: 13px;
        background: #f0f9ff;
    }

    .note-title {
        color: var(--blue-dark);
        font-size: 12px;
        font-weight: 800;
    }

    .note-text {
        margin-top: 5px;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1150px) {
        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .detail-header {
            flex-direction: column;
        }

        .detail-actions {
            width: 100%;
            flex-direction: column;
        }

        .detail-actions .btn-primary,
        .detail-actions .btn-secondary {
            width: 100%;
        }

        .detail-title {
            font-size: 24px;
        }

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .information-list {
            grid-template-columns: 1fr;
        }

        .information-item.full {
            grid-column: auto;
        }

        .program-box {
            align-items: flex-start;
            flex-direction: column;
        }

        .program-link {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="detail-page">

    {{-- HEADER --}}
    <div class="detail-header">

        <div class="detail-header-main">

            <div class="breadcrumb">

                <a href="{{ route('admin.programs.index') }}">
                    Program & Paket
                </a>

                <span>/</span>

                <a href="{{ route('admin.packages.index') }}">
                    Paket
                </a>

                <span>/</span>

                <span>Detail</span>

            </div>


            <div class="detail-title-row">

                <h1 class="detail-title">
                    {{ $package->name }}
                </h1>

                @if ($package->status === 'active')

                    <span class="status-badge status-active">
                        Aktif
                    </span>

                @else

                    <span class="status-badge status-inactive">
                        Nonaktif
                    </span>

                @endif

            </div>


            <p class="detail-description">
                Paket
                <strong>{{ $package->program?->name ?? '-' }}</strong>
                dengan {{ $package->total_sessions }} sesi pembelajaran.
            </p>

        </div>


        <div class="detail-actions">

            <a
                href="{{ route('admin.packages.index') }}"
                class="btn-secondary"
            >
                <svg
                    class="button-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                >
                    <path
                        d="M15 18L9 12L15 6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                Kembali
            </a>


            <a
                href="{{ route('admin.packages.edit', $package) }}"
                class="btn-primary"
            >
                Edit Paket
            </a>

        </div>

    </div>


    {{-- STATISTICS --}}
    <div class="statistics-grid">

        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Harga Paket
                </div>

                <div class="stat-value price">
                    {{ $package->formatted_price }}
                </div>

                <div class="stat-description">
                    Harga pembelian paket
                </div>
            </div>

            <div class="stat-icon green">

                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="4"
                        y="6"
                        width="16"
                        height="12"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M8 12H16"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

            </div>

        </div>


        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Jumlah Sesi
                </div>

                <div class="stat-value">
                    {{ $package->total_sessions }}
                </div>

                <div class="stat-description">
                    Pertemuan per paket
                </div>
            </div>

            <div class="stat-icon">

                <svg viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="8"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M12 8V12L15 14"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

            </div>

        </div>


        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Masa Aktif
                </div>

                <div class="stat-value">
                    {{ $package->validity_days ?: '-' }}
                </div>

                <div class="stat-description">
                    {{ $package->validity_days ? 'Hari sejak paket diaktifkan' : 'Tidak ditentukan' }}
                </div>
            </div>

            <div class="stat-icon aqua">

                <svg viewBox="0 0 24 24" fill="none">
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

        </div>


        <div class="card stat-card">

            <div>
                <div class="stat-label">
                    Status
                </div>

                <div class="stat-value" style="font-size: 20px;">
                    {{ $package->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </div>

                <div class="stat-description">
                    Status penawaran paket
                </div>
            </div>

            <div class="stat-icon slate">

                <svg viewBox="0 0 24 24" fill="none">
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

            </div>

        </div>

    </div>


    <div class="detail-grid">

        {{-- MAIN --}}
        <div class="main-column">

            {{-- PACKAGE INFORMATION --}}
            <div class="card section-card">

                <div class="section-header">

                    <div class="section-title">
                        Informasi Paket
                    </div>

                    <div class="section-subtitle">
                        Informasi utama paket pembelajaran.
                    </div>

                </div>


                <div class="section-body">

                    <div class="information-list">

                        <div class="information-item">

                            <div class="information-label">
                                Nama Paket
                            </div>

                            <div class="information-value">
                                {{ $package->name }}
                            </div>

                        </div>


                        <div class="information-item">

                            <div class="information-label">
                                Harga
                            </div>

                            <div class="information-value">
                                {{ $package->formatted_price }}
                            </div>

                        </div>


                        <div class="information-item">

                            <div class="information-label">
                                Jumlah Sesi
                            </div>

                            <div class="information-value">
                                {{ $package->total_sessions }} sesi
                            </div>

                        </div>


                        <div class="information-item">

                            <div class="information-label">
                                Masa Aktif
                            </div>

                            <div class="information-value">

                                @if ($package->validity_days)

                                    {{ $package->validity_days }} hari

                                @else

                                    Tidak ditentukan

                                @endif

                            </div>

                        </div>


                        <div class="information-item">

                            <div class="information-label">
                                Status
                            </div>

                            <div class="information-value">
                                {{ $package->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </div>

                        </div>


                        <div class="information-item">

                            <div class="information-label">
                                ID Paket
                            </div>

                            <div class="information-value">
                                #{{ $package->id }}
                            </div>

                        </div>


                        <div class="information-item full">

                            <div class="information-label">
                                Deskripsi
                            </div>

                            <div class="information-value regular">
                                {{ $package->description ?: 'Belum ada deskripsi untuk paket ini.' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PROGRAM --}}
            <div class="card section-card">

                <div class="section-header">

                    <div class="section-title">
                        Program Terhubung
                    </div>

                    <div class="section-subtitle">
                        Program pembelajaran yang menggunakan paket ini.
                    </div>

                </div>


                <div class="section-body">

                    @if ($package->program)

                        <div class="program-box">

                            <div>

                                <div class="program-name">
                                    {{ $package->program->name }}
                                </div>

                                <div class="program-description">
                                    {{ $package->program->short_description ?: 'Belum ada deskripsi singkat program.' }}
                                </div>

                            </div>


                            <a
                                href="{{ route('admin.programs.show', $package->program) }}"
                                class="program-link"
                            >
                                Lihat Program
                            </a>

                        </div>

                    @else

                        <div class="program-box">

                            <div>

                                <div class="program-name">
                                    Program tidak ditemukan
                                </div>

                                <div class="program-description">
                                    Paket ini tidak memiliki program
                                    yang dapat ditampilkan.
                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- SIDEBAR --}}
        <div class="side-column">

            {{-- QUICK ACTION --}}
            <div class="card quick-card">

                <div class="quick-title">
                    Aksi Cepat
                </div>

                <div class="quick-description">
                    Kelola data paket atau kembali ke program
                    yang terhubung.
                </div>


                <div class="quick-actions">

                    <a
                        href="{{ route('admin.packages.edit', $package) }}"
                        class="btn-primary"
                    >
                        Edit Paket
                    </a>


                    @if ($package->program)

                        <a
                            href="{{ route('admin.programs.show', $package->program) }}"
                            class="btn-secondary"
                        >
                            Lihat Program
                        </a>

                    @endif


                    <a
                        href="{{ route('admin.packages.index') }}"
                        class="btn-secondary"
                    >
                        Semua Paket
                    </a>

                </div>

            </div>


            {{-- STATUS --}}
            <div class="card status-card">

                <div class="status-card-title">
                    Status Paket
                </div>

                <div class="status-card-description">
                    Atur apakah paket dapat digunakan dan
                    ditawarkan kepada customer.
                </div>


                <div class="status-current">

                    <div class="status-current-label">
                        Status saat ini
                    </div>


                    @if ($package->status === 'active')

                        <span class="status-badge status-active">
                            Aktif
                        </span>

                    @else

                        <span class="status-badge status-inactive">
                            Nonaktif
                        </span>

                    @endif

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.packages.status.update', $package) }}"
                    class="status-form"
                    onsubmit="return confirm('Yakin ingin mengubah status paket ini?')"
                >
                    @csrf
                    @method('PATCH')


                    @if ($package->status === 'active')

                        <input
                            type="hidden"
                            name="status"
                            value="inactive"
                        >

                        <button
                            type="submit"
                            class="status-button deactivate"
                        >
                            Nonaktifkan Paket
                        </button>

                    @else

                        <input
                            type="hidden"
                            name="status"
                            value="active"
                        >

                        <button
                            type="submit"
                            class="status-button activate"
                        >
                            Aktifkan Paket
                        </button>

                    @endif

                </form>

            </div>


            {{-- NOTE --}}
            <div class="note-card">

                <div class="note-title">
                    Tentang perubahan paket
                </div>

                <div class="note-text">
                    Mengubah data paket di sini tidak mengubah
                    informasi transaksi lama yang sudah tersimpan
                    pada order item.
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
