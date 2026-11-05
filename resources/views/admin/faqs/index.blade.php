@extends('layouts.admin')

@section('title', 'Kelola FAQ')
@section('topbar-title', 'Kelola FAQ')

@section('content')

<div class="page-header">

    <div>
        <h1 class="page-title">
            Kelola FAQ
        </h1>

        <p class="page-description">
            Kelola pertanyaan dan jawaban yang sering diajukan
            oleh calon peserta dan customer Ragen Swim Course.
        </p>
    </div>

    <a
        href="{{ route('admin.faqs.create') }}"
        class="btn-primary"
    >
        + Tambah FAQ
    </a>

</div>


{{-- =========================================================
    STATISTIK FAQ
========================================================== --}}

<div class="faq-statistics">

    <div class="faq-stat-card">

        <div class="faq-stat-icon faq-icon-blue">
            <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9"
                    stroke="currentColor" stroke-width="1.8"/>
                <path d="M9.5 9A2.5 2.5 0 0 1 12 6.5C13.5 6.5 14.5 7.5 14.5 9C14.5 11 12 11.5 12 13.5"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round"/>
                <circle cx="12" cy="17" r="1" fill="currentColor"/>
            </svg>
        </div>

        <div>
            <span class="faq-stat-label">Total FAQ</span>

            <strong class="faq-stat-value">
                {{ number_format($statistics['total'] ?? 0) }}
            </strong>
        </div>

    </div>


    <div class="faq-stat-card">

        <div class="faq-stat-icon faq-icon-green">
            <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9"
                    stroke="currentColor" stroke-width="1.8"/>
                <path d="M8 12L10.5 14.5L16 9"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>
            </svg>
        </div>

        <div>
            <span class="faq-stat-label">Dipublikasikan</span>

            <strong class="faq-stat-value">
                {{ number_format($statistics['published'] ?? 0) }}
            </strong>
        </div>

    </div>


    <div class="faq-stat-card">

        <div class="faq-stat-icon faq-icon-orange">
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="5" y="4" width="14" height="16" rx="2"
                    stroke="currentColor" stroke-width="1.8"/>
                <path d="M8.5 9H15.5M8.5 13H15.5"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round"/>
            </svg>
        </div>

        <div>
            <span class="faq-stat-label">Draf</span>

            <strong class="faq-stat-value">
                {{ number_format($statistics['draft'] ?? 0) }}
            </strong>
        </div>

    </div>

</div>


{{-- =========================================================
    FILTER DAN PENCARIAN
========================================================== --}}

<section class="card faq-filter-card">

    <div class="faq-section-header">
        <div>
            <h2>Daftar Pertanyaan</h2>

            <p>
                Cari dan kelola seluruh FAQ yang tersimpan.
            </p>
        </div>
    </div>

    <form
        method="GET"
        action="{{ route('admin.faqs.index') }}"
        class="faq-filter-form"
    >

        <div class="faq-field faq-search-field">

            <label for="search">
                Cari FAQ
            </label>

            <input
                type="search"
                id="search"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari pertanyaan atau jawaban..."
            >

        </div>


        <div class="faq-field">

            <label for="category">
                Kategori
            </label>

            <select id="category" name="category">

                <option value="">
                    Semua Kategori
                </option>

                @foreach ($categories as $item)

                    <option
                        value="{{ $item }}"
                        @selected(($category ?? '') === $item)
                    >
                        {{ match($item) {
                            'general' => 'Umum',
                            'registration' => 'Pendaftaran',
                            'program' => 'Program Kelas',
                            'schedule' => 'Jadwal',
                            'booking' => 'Booking',
                            'payment' => 'Pembayaran',
                            'reschedule' => 'Reschedule',
                            'package' => 'Paket Pertemuan',
                            'trial' => 'Trial Class',
                            default => ucfirst($item)
                        } }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="faq-field">

            <label for="status">
                Status
            </label>

            <select id="status" name="status">

                <option value="">
                    Semua Status
                </option>

                <option
                    value="published"
                    @selected(($status ?? '') === 'published')
                >
                    Dipublikasikan
                </option>

                <option
                    value="draft"
                    @selected(($status ?? '') === 'draft')
                >
                    Draf
                </option>

            </select>

        </div>


        <div class="faq-filter-actions">

            <button type="submit" class="btn-primary">
                Filter
            </button>

            <a
                href="{{ route('admin.faqs.index') }}"
                class="btn-secondary"
            >
                Reset
            </a>

        </div>

    </form>

</section>


{{-- =========================================================
    TABEL FAQ
========================================================== --}}

<section class="card faq-table-card">

    <div class="faq-table-header">

        <div>
            <h2>Data FAQ</h2>

            <p>
                Menampilkan {{ $faqs->count() }}
                dari {{ $faqs->total() }} FAQ sesuai filter.
            </p>
        </div>

    </div>


    <div class="faq-table-wrapper">

        <table class="faq-table">

            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Pertanyaan & Jawaban</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($faqs as $faq)

                    <tr>

                        <td>
                            <span class="faq-order">
                                {{ $faq->sort_order }}
                            </span>
                        </td>


                        <td class="faq-content-cell">

                            <strong class="faq-question">
                                {{ $faq->question }}
                            </strong>

                            <p class="faq-answer">
                                {{ \Illuminate\Support\Str::limit($faq->answer, 150) }}
                            </p>

                        </td>


                        <td>
                            <span class="faq-category">
                                {{ $faq->category_label }}
                            </span>
                        </td>


                        <td>

                            @if ($faq->status === 'published')

                                <span class="faq-status faq-status-published">
                                    Dipublikasikan
                                </span>

                            @else

                                <span class="faq-status faq-status-draft">
                                    Draf
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="faq-actions">

                                <a
                                    href="{{ route('admin.faqs.edit', $faq) }}"
                                    class="faq-action faq-action-edit"
                                >
                                    Edit
                                </a>


                                <form
                                    method="POST"
                                    action="{{ route('admin.faqs.status.update', $faq) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="{{ $faq->status === 'published' ? 'draft' : 'published' }}"
                                    >

                                    <button
                                        type="submit"
                                        class="faq-action {{
                                            $faq->status === 'published'
                                                ? 'faq-action-draft'
                                                : 'faq-action-publish'
                                        }}"
                                    >
                                        {{
                                            $faq->status === 'published'
                                                ? 'Jadikan Draf'
                                                : 'Publikasikan'
                                        }}
                                    </button>

                                </form>


                                <form
                                    method="POST"
                                    action="{{ route('admin.faqs.destroy', $faq) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus FAQ ini? Data yang dihapus tidak dapat dikembalikan.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="faq-action faq-action-delete"
                                    >
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">

                            <div class="faq-empty">

                                <div class="faq-empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9"
                                            stroke="currentColor"
                                            stroke-width="1.7"/>
                                        <path d="M9.5 9A2.5 2.5 0 0 1 12 6.5C13.5 6.5 14.5 7.5 14.5 9C14.5 11 12 11.5 12 13.5"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"/>
                                        <circle cx="12" cy="17" r="1"
                                            fill="currentColor"/>
                                    </svg>
                                </div>

                                <strong>
                                    Belum ada FAQ
                                </strong>

                                <p>
                                    Belum ada pertanyaan yang sesuai
                                    dengan pencarian atau filter.
                                </p>

                                <a
                                    href="{{ route('admin.faqs.create') }}"
                                    class="btn-primary"
                                >
                                    + Tambah FAQ
                                </a>

                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if ($faqs->hasPages())

        <div class="faq-pagination">
            {{ $faqs->links() }}
        </div>

    @endif

</section>

@endsection


@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | FAQ Statistics
    |--------------------------------------------------------------------------
    */

    .faq-statistics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .faq-stat-card {
        display: flex;
        align-items: center;
        gap: 17px;

        min-width: 0;
        padding: 22px;

        border: 1px solid #e2e8f0;
        border-radius: 16px;

        background: #ffffff;

        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.035);
    }

    .faq-stat-icon {
        width: 50px;
        height: 50px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;
        border-radius: 13px;
    }

    .faq-stat-icon svg {
        width: 25px;
        height: 25px;
    }

    .faq-icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .faq-icon-green {
        background: #f0fdf4;
        color: #16a34a;
    }

    .faq-icon-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .faq-stat-label {
        display: block;
        margin-bottom: 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 650;
    }

    .faq-stat-value {
        display: block;
        color: #082f49;
        font-size: 27px;
        font-weight: 850;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    .faq-filter-card {
        margin-bottom: 22px;
        padding: 24px;
    }

    .faq-section-header {
        margin-bottom: 22px;
    }

    .faq-section-header h2,
    .faq-table-header h2 {
        color: #082f49;
        font-size: 16px;
        font-weight: 800;
    }

    .faq-section-header p,
    .faq-table-header p {
        margin-top: 5px;
        color: #94a3b8;
        font-size: 12px;
    }

    .faq-filter-form {
        display: grid;
        grid-template-columns:
            minmax(0, 2fr)
            minmax(0, 1fr)
            minmax(0, 1fr)
            auto;

        align-items: end;
        gap: 13px;
    }

    .faq-field {
        min-width: 0;
    }

    .faq-field label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 12px;
        font-weight: 750;
    }

    .faq-field input,
    .faq-field select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;

        border: 1px solid #cbd5e1;
        border-radius: 9px;

        background: #ffffff;
        color: #334155;

        font-size: 12px;
        outline: none;
    }

    .faq-field input:focus,
    .faq-field select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .faq-filter-actions {
        display: flex;
        gap: 8px;
    }

    .faq-filter-actions .btn-primary,
    .faq-filter-actions .btn-secondary {
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .faq-table-card {
        overflow: hidden;
    }

    .faq-table-header {
        padding: 22px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .faq-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .faq-table {
        width: 100%;
        border-collapse: collapse;
    }

    .faq-table th {
        padding: 15px 17px;

        border-bottom: 1px solid #e2e8f0;

        background: #f8fafc;
        color: #64748b;

        font-size: 10px;
        font-weight: 800;

        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;

        white-space: nowrap;
    }

    .faq-table td {
        padding: 18px 17px;

        border-bottom: 1px solid #f1f5f9;

        color: #475569;

        font-size: 12px;
        vertical-align: top;
    }

    .faq-table tbody tr:last-child td {
        border-bottom: none;
    }

    .faq-table tbody tr:hover {
        background: #fafcff;
    }

    .faq-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-width: 32px;
        min-height: 32px;

        padding: 5px 8px;

        border-radius: 9px;

        background: #f1f5f9;
        color: #475569;

        font-size: 12px;
        font-weight: 800;
    }

    .faq-content-cell {
        min-width: 250px;
        max-width: 430px;
    }

    .faq-question {
        display: block;

        color: #0f172a;

        font-size: 12px;
        font-weight: 800;
        line-height: 1.6;

        overflow-wrap: anywhere;
    }

    .faq-answer {
        margin-top: 6px;

        color: #94a3b8;

        font-size: 11px;
        line-height: 1.65;

        overflow-wrap: anywhere;
    }

    .faq-category {
        display: inline-block;

        padding: 6px 10px;

        border-radius: 8px;

        background: #f0f9ff;
        color: #0369a1;

        font-size: 10px;
        font-weight: 750;

        white-space: nowrap;
    }

    .faq-status {
        display: inline-flex;
        align-items: center;

        padding: 6px 10px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 800;

        white-space: nowrap;
    }

    .faq-status-published {
        background: #f0fdf4;
        color: #15803d;
    }

    .faq-status-draft {
        background: #f1f5f9;
        color: #64748b;
    }


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    .faq-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        min-width: 230px;
    }

    .faq-actions form {
        margin: 0;
    }

    .faq-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 32px;
        padding: 7px 10px;

        border: 1px solid transparent;
        border-radius: 8px;

        font-size: 10px;
        font-weight: 800;

        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .faq-action-edit {
        background: #eff6ff;
        color: #2563eb;
    }

    .faq-action-publish {
        background: #f0fdf4;
        color: #15803d;
    }

    .faq-action-draft {
        background: #fffbeb;
        color: #b45309;
    }

    .faq-action-delete {
        background: #fef2f2;
        color: #dc2626;
    }

    .faq-action:hover {
        filter: brightness(0.96);
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    .faq-empty {
        display: flex;
        flex-direction: column;
        align-items: center;

        padding: 38px 20px;
        text-align: center;
    }

    .faq-empty-icon {
        width: 56px;
        height: 56px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 14px;

        border-radius: 16px;

        background: #f0f9ff;
        color: #0284c7;
    }

    .faq-empty-icon svg {
        width: 28px;
        height: 28px;
    }

    .faq-empty strong {
        color: #334155;
        font-size: 14px;
        font-weight: 800;
    }

    .faq-empty p {
        max-width: 340px;
        margin: 7px 0 18px;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.7;
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    .faq-pagination {
        padding: 20px 24px;
        border-top: 1px solid #e2e8f0;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1100px) {

        .faq-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .faq-filter-actions {
            align-self: end;
        }
    }

    @media (max-width: 700px) {

        .faq-statistics {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .faq-stat-card {
            padding: 17px;
        }

        .faq-filter-card {
            padding: 18px;
        }

        .faq-filter-form {
            grid-template-columns: 1fr;
        }

        .faq-filter-actions {
            width: 100%;
        }

        .faq-filter-actions .btn-primary,
        .faq-filter-actions .btn-secondary {
            flex: 1;
        }

        .faq-table-header {
            padding: 18px;
        }

        .page-header .btn-primary {
            margin-top: 16px;
        }
    }

</style>

@endpush
