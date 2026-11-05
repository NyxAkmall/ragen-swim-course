@extends('layouts.customer')

@section('title', 'Peserta Saya')

@section('topbar-title', 'Peserta Saya')

@section('topbar-subtitle', 'Kelola profil peserta renang Anda.')

@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Peserta Saya
        </h1>

        <p class="page-description">
            Kelola profil diri sendiri atau anak yang mengikuti
            program berenang di Ragen Swim Course.
        </p>
    </div>

    <a
        href="{{ route('customer.participants.create') }}"
        class="btn-primary participant-add-button"
    >
        <span aria-hidden="true">+</span>
        Tambah Peserta
    </a>

</div>

{{-- PESAN SUKSES --}}

@if (session('success'))

    <div
        class="participant-alert"
        role="status"
    >
        <span aria-hidden="true">✓</span>
        <span>{{ session('success') }}</span>
    </div>

@endif

@if ($participants->isNotEmpty())

    {{-- RINGKASAN PESERTA --}}

    <div class="participant-summary">

        <div class="summary-item">
            <div class="summary-number">
                {{ $participants->count() }}
            </div>

            <div class="summary-label">
                Total Peserta
            </div>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-item">
            <div class="summary-number">
                {{ $participants->where('status', 'active')->count() }}
            </div>

            <div class="summary-label">
                Peserta Aktif
            </div>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-item">
            <div class="summary-number">
                {{ $participants->where('participant_type', 'child')->count() }}
            </div>

            <div class="summary-label">
                Anak
            </div>
        </div>

    </div>

    {{-- DAFTAR PESERTA --}}

    <div class="participants-grid">

        @foreach ($participants as $participant)

            <article
                class="participant-card {{ $participant->isActive() ? '' : 'participant-inactive' }}"
            >

                <div class="participant-card-top">

                    <div class="participant-identity">

                        <div
                            class="participant-avatar"
                            aria-hidden="true"
                        >
                            {{ mb_strtoupper(mb_substr($participant->full_name, 0, 1)) }}
                        </div>

                        <div class="participant-heading">

                            <h2
                                class="participant-name"
                                title="{{ $participant->full_name }}"
                            >
                                {{ $participant->full_name }}
                            </h2>

                            <div class="participant-type">
                                {{ $participant->participant_type_label }}
                            </div>

                        </div>

                    </div>

                    <span
                        class="participant-status {{ $participant->isActive() ? 'active' : 'inactive' }}"
                    >
                        {{ $participant->status_label }}
                    </span>

                </div>

                {{-- INFORMASI PESERTA --}}

                <div class="participant-details">

                    <div class="detail-row">

                        <div class="detail-icon" aria-hidden="true">
                            👤
                        </div>

                        <div class="detail-content">

                            <div class="detail-label">
                                Jenis Kelamin
                            </div>

                            <div class="detail-value">
                                {{ $participant->gender_label }}
                            </div>

                        </div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-icon" aria-hidden="true">
                            🎂
                        </div>

                        <div class="detail-content">

                            <div class="detail-label">
                                Usia
                            </div>

                            <div class="detail-value">
                                @if ($participant->age !== null)
                                    {{ $participant->age }} tahun
                                @else
                                    Belum tersedia
                                @endif
                            </div>

                        </div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-icon" aria-hidden="true">
                            🏊
                        </div>

                        <div class="detail-content">

                            <div class="detail-label">
                                Level Berenang
                            </div>

                            <div class="detail-value">
                                {{ $participant->swimming_level_label }}
                            </div>

                        </div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-icon" aria-hidden="true">
                            📞
                        </div>

                        <div class="detail-content">

                            <div class="detail-label">
                                Kontak Darurat
                            </div>

                            <div class="detail-value">
                                {{ $participant->emergency_contact_phone ?: 'Belum diisi' }}
                            </div>

                        </div>

                    </div>

                </div>

                {{-- CATATAN PESERTA --}}

                @if ($participant->notes)

                    <div class="participant-notes">

                        <div class="notes-label">
                            Catatan Peserta
                        </div>

                        <p class="notes-text">
                            {{ $participant->notes }}
                        </p>

                    </div>

                @endif

                {{-- TOMBOL AKSI --}}

                <div class="participant-actions">

                    <a
                        href="{{ route('customer.participants.edit', $participant) }}"
                        class="action-edit"
                    >
                        Edit Data
                    </a>

                    @if ($participant->isActive())

                        <form
                            method="POST"
                            action="{{ route('customer.participants.destroy', $participant) }}"
                            onsubmit="return confirm('Yakin ingin menonaktifkan peserta ini? Data dan riwayat peserta tetap tersimpan.');"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="action-deactivate"
                            >
                                Nonaktifkan
                            </button>

                        </form>

                    @endif

                </div>

                @unless ($participant->isActive())

                    <p class="inactive-information">
                        Peserta ini sudah tidak aktif.
                        Data dan riwayatnya tetap tersimpan.
                    </p>

                @endunless

            </article>

        @endforeach

    </div>

@else

    {{-- BELUM ADA PESERTA --}}

    <section class="card empty-participants">

        <div class="empty-illustration">
            <div class="empty-circle" aria-hidden="true">
                🏊
            </div>
        </div>

        <h2>
            Belum Ada Peserta
        </h2>

        <p>
            Tambahkan profil orang yang akan mengikuti
            kelas berenang. Kamu bisa mendaftarkan
            diri sendiri maupun anak.
        </p>

        <a
            href="{{ route('customer.participants.create') }}"
            class="btn-primary"
        >
            + Tambah Peserta Pertama
        </a>

    </section>

@endif

@endsection

@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Add Button
    |--------------------------------------------------------------------------
    */

    .participant-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        white-space: nowrap;
        text-decoration: none;
    }

    .participant-add-button span {
        font-size: 21px;
        line-height: 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Success Alert
    |--------------------------------------------------------------------------
    */

    .participant-alert {
        display: flex;
        align-items: center;
        gap: 11px;

        margin-bottom: 22px;
        padding: 14px 17px;

        border: 1px solid #bbf7d0;
        border-radius: 12px;

        background: #f0fdf4;
        color: #166534;

        font-size: 13px;
        font-weight: 650;
        line-height: 1.6;
    }

    .participant-alert > span:first-child {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 24px;
        height: 24px;
        flex-shrink: 0;

        border-radius: 50%;
        background: #dcfce7;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    .participant-summary {
        display: flex;
        align-items: center;

        width: fit-content;
        max-width: 100%;

        margin-bottom: 24px;
        padding: 17px 23px;

        border: 1px solid var(--border);
        border-radius: 15px;

        background: var(--white);
    }

    .summary-item {
        min-width: 105px;
        text-align: center;
    }

    .summary-number {
        color: var(--navy);
        font-size: 25px;
        font-weight: 800;
    }

    .summary-label {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 12px;
        font-weight: 600;
    }

    .summary-divider {
        width: 1px;
        height: 40px;
        margin: 0 18px;
        background: var(--border);
    }

    /*
    |--------------------------------------------------------------------------
    | Participant Grid
    |--------------------------------------------------------------------------
    */

    .participants-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .participant-card {
        min-width: 0;
        padding: 23px;

        border: 1px solid var(--border);
        border-radius: 17px;

        background: var(--white);

        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.035);
    }

    .participant-inactive {
        background: #fafbfc;
        border-color: #e2e8f0;
    }

    .participant-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 13px;

        padding-bottom: 19px;
        border-bottom: 1px solid #f1f5f9;
    }

    .participant-identity {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .participant-avatar {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 50px;
        height: 50px;
        flex-shrink: 0;

        border-radius: 50%;

        background: linear-gradient(135deg, #dbeafe, #cffafe);
        color: var(--blue-dark);

        font-size: 19px;
        font-weight: 800;
    }

    .participant-heading {
        min-width: 0;
    }

    .participant-name {
        margin: 0;
        overflow-wrap: anywhere;

        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
        line-height: 1.4;
    }

    .participant-type {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 12px;
    }

    .participant-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;
        padding: 7px 10px;

        border-radius: 999px;

        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .participant-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .participant-status.inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    /*
    |--------------------------------------------------------------------------
    | Participant Details
    |--------------------------------------------------------------------------
    */

    .participant-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 19px 15px;
        padding: 22px 0;
    }

    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
    }

    .detail-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 32px;
        height: 32px;
        flex-shrink: 0;

        border-radius: 9px;
        background: #f0f9ff;

        font-size: 15px;
    }

    .detail-content {
        min-width: 0;
    }

    .detail-label {
        color: var(--text-soft);
        font-size: 11px;
        font-weight: 650;
        line-height: 1.4;
    }

    .detail-value {
        margin-top: 5px;

        color: var(--text);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5;

        overflow-wrap: anywhere;
    }

    /*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

    .participant-notes {
        margin-bottom: 19px;
        padding: 14px 15px;

        border-radius: 11px;
        background: #f8fafc;
    }

    .notes-label {
        margin-bottom: 6px;

        color: var(--text-soft);
        font-size: 11px;
        font-weight: 800;
    }

    .notes-text {
        margin: 0;

        color: #475569;
        font-size: 12px;
        line-height: 1.7;

        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .participant-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;

        padding-top: 17px;
        border-top: 1px solid #f1f5f9;
    }

    .participant-actions form {
        display: inline-flex;
        margin: 0;
    }

    .action-edit,
    .action-deactivate {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 39px;
        padding: 9px 15px;

        border-radius: 9px;

        font: inherit;
        font-size: 12px;
        font-weight: 750;
        line-height: 1.4;

        text-decoration: none;
        cursor: pointer;
    }

    .action-edit {
        border: 1px solid #bae6fd;
        background: #f0f9ff;
        color: #0369a1;
    }

    .action-edit:hover {
        background: #e0f2fe;
    }

    .action-deactivate {
        border: 1px solid #fecaca;
        background: var(--white);
        color: #dc2626;
    }

    .action-deactivate:hover {
        background: #fef2f2;
    }

    .inactive-information {
        margin: 12px 0 0;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .empty-participants {
        padding: 60px 25px;
        text-align: center;
    }

    .empty-illustration {
        display: flex;
        justify-content: center;
        margin-bottom: 19px;
    }

    .empty-circle {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 76px;
        height: 76px;

        border-radius: 50%;
        background: linear-gradient(135deg, #e0f2fe, #cffafe);

        font-size: 32px;
    }

    .empty-participants h2 {
        color: var(--navy);
        font-size: 21px;
        font-weight: 800;
    }

    .empty-participants p {
        max-width: 460px;
        margin: 12px auto 23px;

        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.8;
    }

    .empty-participants .btn-primary {
        display: inline-flex;
        width: auto;
        text-decoration: none;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1050px) {
        .participants-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .participant-add-button {
            width: 100%;
            margin-top: 15px;
        }

        .participant-summary {
            width: 100%;
            justify-content: space-around;
            padding: 15px 12px;
        }

        .summary-item {
            flex: 1;
            min-width: 0;
        }

        .summary-divider {
            margin: 0 10px;
        }
    }

    @media (max-width: 480px) {
        .participant-card {
            padding: 17px;
        }

        .participant-details {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .participant-card-top {
            align-items: center;
        }

        .participant-avatar {
            width: 43px;
            height: 43px;
        }

        .participant-name {
            font-size: 14px;
        }

        .participant-status {
            font-size: 10px;
            padding: 6px 8px;
        }

        .summary-number {
            font-size: 21px;
        }

        .summary-label {
            font-size: 10px;
        }

        .summary-divider {
            margin: 0 7px;
        }

        .participant-actions {
            align-items: stretch;
        }
    }

</style>

@endpush
