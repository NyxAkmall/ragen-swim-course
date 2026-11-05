@extends('layouts.admin')

@section('title', 'Edit Coach')
@section('topbar-title', 'Edit Coach')

@section('content')

@php
    $coachName = $coach->user?->name ?? 'Coach';

    $coachInitials = collect(
        preg_split('/\s+/', trim($coachName))
    )
        ->filter()
        ->take(2)
        ->map(function ($word) {
            return strtoupper(
                mb_substr($word, 0, 1)
            );
        })
        ->implode('');
@endphp


<div class="page-header">

    <div>

        <a
            href="{{ route('admin.coaches.show', $coach) }}"
            class="back-link"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M15 18L9 12L15 6"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

            Kembali ke Detail Coach
        </a>

        <h1 class="page-title">
            Edit Coach
        </h1>

        <p class="page-description">
            Perbarui informasi akun dan profil profesional coach
            Ragen Swim Course.
        </p>

    </div>

</div>


<form
    method="POST"
    action="{{ route('admin.coaches.update', $coach) }}"
    id="coachEditForm"
>
    @csrf
    @method('PUT')


    <div class="edit-layout">

        {{-- =====================================================
            MAIN CONTENT
        ====================================================== --}}

        <div class="edit-main">

            {{-- PROFILE SUMMARY --}}
            <div class="card profile-summary">

                <div class="profile-avatar-large">

                    @if ($coach->photo)

                        <img
                            src="{{ asset('storage/' . $coach->photo) }}"
                            alt="{{ $coachName }}"
                        >

                    @else

                        {{ $coachInitials ?: 'C' }}

                    @endif

                </div>


                <div class="profile-summary-content">

                    <div class="profile-name-row">

                        <h2>
                            {{ $coachName }}
                        </h2>


                        @if ($coach->status === 'active')

                            <span class="status-badge status-active">
                                <span class="status-dot"></span>
                                Aktif
                            </span>

                        @else

                            <span class="status-badge status-inactive">
                                <span class="status-dot"></span>
                                Tidak Aktif
                            </span>

                        @endif

                    </div>

                    <div class="profile-code">
                        Coach #{{ $coach->id }}
                    </div>

                    <p>
                        Perubahan yang disimpan akan langsung
                        memperbarui profil coach.
                    </p>

                </div>

            </div>


            {{-- ACCOUNT INFORMATION --}}
            <div class="card form-card">

                <div class="form-card-heading">

                    <div class="heading-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="12"
                                cy="7"
                                r="3.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M5 20C5.5 15.8 8 14 12 14C16 14 18.5 15.8 19 20"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2>
                            Informasi Akun
                        </h2>

                        <p>
                            Informasi utama yang terhubung dengan akun coach.
                        </p>

                    </div>

                </div>


                <div class="form-card-body">

                    <div class="form-group">

                        <label for="name">
                            Nama Lengkap
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $coach->user?->name) }}"
                            maxlength="150"
                            placeholder="Nama lengkap coach"
                            required
                            autofocus
                            class="@error('name') input-error @enderror"
                        >

                        @error('name')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="form-grid">

                        <div class="form-group">

                            <label for="email">
                                Email
                                <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $coach->user?->email) }}"
                                maxlength="150"
                                placeholder="coach@example.com"
                                required
                                class="@error('email') input-error @enderror"
                            >

                            <div class="field-help">
                                Email ini digunakan coach untuk login.
                            </div>

                            @error('email')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="form-group">

                            <label for="phone">
                                Nomor Telepon
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $coach->user?->phone) }}"
                                maxlength="30"
                                placeholder="Contoh: 081234567890"
                                required
                                class="@error('phone') input-error @enderror"
                            >

                            <div class="field-help">
                                Gunakan nomor telepon aktif coach.
                            </div>

                            @error('phone')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>


            {{-- PROFESSIONAL PROFILE --}}
            <div class="card form-card">

                <div class="form-card-heading">

                    <div class="heading-icon heading-icon-aqua">

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
                                d="M7 10V15C7 17 9.2 18.5 12 18.5C14.8 18.5 17 17 17 15V10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2>
                            Profil Profesional
                        </h2>

                        <p>
                            Perbarui spesialisasi, pengalaman, dan bio coach.
                        </p>

                    </div>

                </div>


                <div class="form-card-body">

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="specialization">
                                Spesialisasi
                            </label>

                            <input
                                type="text"
                                id="specialization"
                                name="specialization"
                                value="{{ old('specialization', $coach->specialization) }}"
                                maxlength="255"
                                placeholder="Contoh: Kids & Beginner Swimming"
                                class="@error('specialization') input-error @enderror"
                            >

                            <div class="field-help">
                                Contoh: Kids Swimming, Beginner, Freestyle,
                                atau Competitive.
                            </div>

                            @error('specialization')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="form-group">

                            <label for="experience_years">
                                Pengalaman
                            </label>

                            <div class="input-suffix-wrapper">

                                <input
                                    type="number"
                                    id="experience_years"
                                    name="experience_years"
                                    value="{{ old('experience_years', $coach->experience_years ?? 0) }}"
                                    min="0"
                                    max="80"
                                    step="1"
                                    class="@error('experience_years') input-error @enderror"
                                >

                                <span class="input-suffix">
                                    Tahun
                                </span>

                            </div>

                            @error('experience_years')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="bio">
                            Bio Coach
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            rows="7"
                            maxlength="3000"
                            placeholder="Tuliskan profil singkat, pengalaman, pendekatan mengajar, atau informasi profesional coach..."
                            class="@error('bio') input-error @enderror"
                        >{{ old('bio', $coach->bio) }}</textarea>


                        <div class="textarea-footer">

                            <div class="field-help">
                                Maksimal 3.000 karakter.
                            </div>

                            <div
                                class="character-count"
                                id="bioCharacterCount"
                            >
                                0 / 3000
                            </div>

                        </div>

                        @error('bio')
                            <div class="field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            SIDEBAR
        ====================================================== --}}

        <div class="edit-sidebar">

            {{-- ACCOUNT INFO --}}
            <div class="card side-card">

                <div class="side-card-heading">
                    Informasi Coach
                </div>


                <div class="account-row">

                    <span>
                        ID Coach
                    </span>

                    <strong>
                        #{{ $coach->id }}
                    </strong>

                </div>


                <div class="account-row">

                    <span>
                        Role
                    </span>

                    <strong>
                        Coach
                    </strong>

                </div>


                <div class="account-row">

                    <span>
                        Status
                    </span>

                    <strong class="{{ $coach->status === 'active' ? 'text-success' : 'text-danger' }}">
                        {{ $coach->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                    </strong>

                </div>


                <div class="account-row">

                    <span>
                        Terdaftar
                    </span>

                    <strong>
                        {{ $coach->created_at?->format('d M Y') ?? '-' }}
                    </strong>

                </div>

            </div>


            {{-- STATUS INFORMATION --}}
            <div class="card side-card">

                <div class="side-card-heading">
                    Status Akun
                </div>


                <div class="status-info">

                    <div class="status-info-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />

                            <path
                                d="M12 11V16"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />

                            <circle
                                cx="12"
                                cy="8"
                                r="1"
                                fill="currentColor"
                            />
                        </svg>

                    </div>


                    <div>

                        <strong>
                            Status tidak diubah di sini
                        </strong>

                        <p>
                            Pengaktifan atau penonaktifan coach dilakukan
                            melalui halaman Detail Coach agar pengelolaan
                            akun lebih aman dan terpisah dari perubahan profil.
                        </p>

                    </div>

                </div>

            </div>


            {{-- SAVE ACTION --}}
            <div class="card action-card">

                <button
                    type="submit"
                    class="save-button"
                    id="saveCoachButton"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M5 4H16L20 8V20H5V4Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M8 4V10H16V4"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M8 20V14H17V20"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Simpan Perubahan
                </button>


                <a
                    href="{{ route('admin.coaches.show', $coach) }}"
                    class="cancel-button"
                >
                    Batal
                </a>

            </div>

        </div>

    </div>

</form>

@endsection


@push('styles')

<style>
    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    .back-link {
        display: inline-flex;
        align-items: center;

        gap: 5px;

        margin-bottom: 10px;

        color: #0284c7;

        text-decoration: none;

        font-size: 12px;
        font-weight: 750;
    }

    .back-link:hover {
        color: #0369a1;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }


    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    .edit-layout {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            320px;

        align-items: start;

        gap: 20px;
    }

    .edit-main,
    .edit-sidebar {
        display: flex;
        flex-direction: column;

        gap: 20px;
    }


    /*
    |--------------------------------------------------------------------------
    | Profile Summary
    |--------------------------------------------------------------------------
    */

    .profile-summary {
        display: flex;
        align-items: center;

        gap: 17px;

        padding: 21px;
    }

    .profile-avatar-large {
        width: 68px;
        height: 68px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        overflow: hidden;

        border-radius: 18px;

        background:
            linear-gradient(
                135deg,
                #0284c7,
                #06b6d4
            );

        color: #ffffff;

        font-size: 21px;
        font-weight: 850;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }

    .profile-summary-content {
        min-width: 0;
    }

    .profile-name-row {
        display: flex;
        align-items: center;

        flex-wrap: wrap;

        gap: 9px;
    }

    .profile-name-row h2 {
        color: var(--navy);

        font-size: 19px;
        font-weight: 850;

        letter-spacing: -0.3px;
    }

    .profile-code {
        margin-top: 3px;

        color: var(--text-light);

        font-size: 10px;
        font-weight: 700;
    }

    .profile-summary-content p {
        margin-top: 7px;

        color: var(--text-soft);

        font-size: 11px;
        line-height: 1.55;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;

        gap: 6px;

        padding: 5px 8px;

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

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }


    /*
    |--------------------------------------------------------------------------
    | Form Card
    |--------------------------------------------------------------------------
    */

    .form-card {
        overflow: hidden;
    }

    .form-card-heading {
        display: flex;
        align-items: center;

        gap: 13px;

        padding: 19px 21px;

        border-bottom: 1px solid var(--border);
    }

    .heading-icon {
        width: 40px;
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 11px;

        background: #eff6ff;
        color: #2563eb;
    }

    .heading-icon-aqua {
        background: #ecfeff;
        color: #0891b2;
    }

    .heading-icon svg {
        width: 20px;
        height: 20px;
    }

    .form-card-heading h2 {
        color: var(--navy);

        font-size: 15px;
        font-weight: 800;
    }

    .form-card-heading p {
        margin-top: 3px;

        color: var(--text-soft);

        font-size: 11px;
        line-height: 1.5;
    }

    .form-card-body {
        padding: 22px;
    }


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    .form-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 17px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-grid .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;

        margin-bottom: 7px;

        color: var(--text);

        font-size: 12px;
        font-weight: 750;
    }

    .required {
        color: #dc2626;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;

        border: 1px solid var(--border);
        border-radius: 10px;

        background: var(--white);
        color: var(--text);

        outline: none;

        font-size: 13px;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .form-group input {
        height: 44px;

        padding: 0 13px;
    }

    .form-group textarea {
        min-height: 145px;

        padding: 12px 13px;

        resize: vertical;

        line-height: 1.6;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #94a3b8;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #38bdf8;

        box-shadow:
            0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .form-group input.input-error,
    .form-group textarea.input-error {
        border-color: #fca5a5;
    }

    .field-help {
        margin-top: 6px;

        color: var(--text-light);

        font-size: 10px;
        line-height: 1.5;
    }

    .field-error {
        margin-top: 6px;

        color: #dc2626;

        font-size: 10px;
        font-weight: 650;

        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | Input Suffix
    |--------------------------------------------------------------------------
    */

    .input-suffix-wrapper {
        position: relative;
    }

    .input-suffix-wrapper input {
        padding-right: 65px;
    }

    .input-suffix {
        position: absolute;
        top: 50%;
        right: 13px;

        transform: translateY(-50%);

        color: var(--text-soft);

        font-size: 10px;
        font-weight: 700;

        pointer-events: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Textarea
    |--------------------------------------------------------------------------
    */

    .textarea-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;
    }

    .character-count {
        margin-top: 6px;

        color: var(--text-light);

        font-size: 9px;
        font-weight: 650;
    }


    /*
    |--------------------------------------------------------------------------
    | Sidebar Cards
    |--------------------------------------------------------------------------
    */

    .side-card {
        padding: 20px;
    }

    .side-card-heading {
        margin-bottom: 14px;

        color: var(--navy);

        font-size: 14px;
        font-weight: 800;
    }

    .account-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 15px;

        padding: 11px 0;

        border-bottom: 1px solid #f1f5f9;
    }

    .account-row:last-child {
        border-bottom: 0;
    }

    .account-row span {
        color: var(--text-soft);

        font-size: 10px;
    }

    .account-row strong {
        color: var(--text);

        text-align: right;

        font-size: 10px;
        font-weight: 750;
    }

    .account-row .text-success {
        color: #15803d;
    }

    .account-row .text-danger {
        color: #dc2626;
    }


    /*
    |--------------------------------------------------------------------------
    | Status Information
    |--------------------------------------------------------------------------
    */

    .status-info {
        display: flex;
        align-items: flex-start;

        gap: 9px;

        padding: 12px;

        border: 1px solid #bae6fd;
        border-radius: 10px;

        background: #f0f9ff;
    }

    .status-info-icon {
        width: 20px;
        height: 20px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        color: #0284c7;
    }

    .status-info-icon svg {
        width: 18px;
        height: 18px;
    }

    .status-info strong {
        display: block;

        color: #075985;

        font-size: 10px;
        font-weight: 800;
    }

    .status-info p {
        margin-top: 4px;

        color: #0369a1;

        font-size: 9px;
        line-height: 1.55;
    }


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .action-card {
        padding: 16px;
    }

    .save-button {
        width: 100%;
        min-height: 44px;

        display: flex;
        align-items: center;
        justify-content: center;

        gap: 7px;

        border: 0;
        border-radius: 10px;

        background: #0284c7;
        color: #ffffff;

        font-size: 12px;
        font-weight: 800;

        transition: background 0.2s ease;
    }

    .save-button:hover {
        background: #0369a1;
    }

    .save-button:disabled {
        cursor: not-allowed;

        opacity: 0.65;
    }

    .save-button svg {
        width: 17px;
        height: 17px;
    }

    .cancel-button {
        width: 100%;
        min-height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-top: 8px;

        border: 1px solid var(--border);
        border-radius: 10px;

        background: #ffffff;
        color: var(--text-soft);

        text-decoration: none;

        font-size: 11px;
        font-weight: 750;
    }

    .cancel-button:hover {
        background: #f8fafc;
        color: var(--navy);
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1000px) {

        .edit-layout {
            grid-template-columns: 1fr;
        }

        .edit-sidebar {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .action-card {
            grid-column: 1 / -1;
        }
    }


    @media (max-width: 700px) {

        .form-grid {
            grid-template-columns: 1fr;

            gap: 0;
        }

        .edit-sidebar {
            display: flex;
        }

        .profile-summary {
            align-items: flex-start;
        }

        .profile-avatar-large {
            width: 58px;
            height: 58px;

            border-radius: 15px;

            font-size: 18px;
        }

        .form-card-body {
            padding: 18px;
        }

        .form-card-heading {
            padding: 17px 18px;
        }
    }


    @media (max-width: 500px) {

        .profile-summary {
            flex-direction: column;
        }
    }
</style>

@endpush


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Bio Character Counter
        |--------------------------------------------------------------------------
        */

        const bio =
            document.getElementById('bio');

        const bioCharacterCount =
            document.getElementById(
                'bioCharacterCount'
            );


        function updateBioCount() {

            if (
                !bio
                ||
                !bioCharacterCount
            ) {
                return;
            }

            bioCharacterCount.textContent =
                bio.value.length
                + ' / 3000';

        }


        if (bio) {

            bio.addEventListener(
                'input',
                updateBioCount
            );

            updateBioCount();

        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Double Submit
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'coachEditForm'
            );

        const saveButton =
            document.getElementById(
                'saveCoachButton'
            );


        if (
            form
            &&
            saveButton
        ) {

            form.addEventListener(
                'submit',
                function () {

                    if (!form.checkValidity()) {
                        return;
                    }

                    saveButton.disabled = true;

                    saveButton.innerHTML =
                        'Menyimpan...';

                }
            );

        }

    });
</script>

@endpush
