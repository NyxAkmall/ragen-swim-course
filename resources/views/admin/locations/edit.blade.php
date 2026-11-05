
@extends('layouts.admin')

@section('title', 'Edit Lokasi')
@section('topbar-title', 'Edit Lokasi')

@push('styles')
<style>
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 12px;
        color: var(--text-soft);
        font-size: 12px;
    }

    .breadcrumb a {
        color: var(--blue);
        text-decoration: none;
        font-weight: 700;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-light);
    }

    .form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        overflow: hidden;
    }

    .form-card-header {
        padding: 21px 23px;
        border-bottom: 1px solid var(--border);
    }

    .form-card-title {
        color: var(--navy);
        font-size: 16px;
        font-weight: 800;
    }

    .form-card-description {
        margin-top: 5px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .form-card-body {
        padding: 23px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 19px;
    }

    .form-group {
        min-width: 0;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: var(--text);
        font-size: 12px;
        font-weight: 750;
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        box-sizing: border-box;
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
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

    .form-control:focus {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.09);
    }

    .form-control.is-invalid {
        border-color: #fca5a5;
    }

    textarea.form-control {
        min-height: 115px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-help {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .form-error {
        margin-top: 6px;
        color: var(--danger);
        font-size: 11px;
        font-weight: 650;
    }

    .fee-section {
        grid-column: 1 / -1;
        margin-top: 4px;
        padding: 20px;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: #f8fbff;
    }

    .fee-section-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .fee-section-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .fee-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-top: 19px;
    }

    .fee-input-wrap {
        display: flex;
        align-items: stretch;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: var(--white);
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .fee-input-wrap:focus-within {
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.09);
    }

    .fee-input-wrap.is-invalid {
        border-color: #fca5a5;
    }

    .fee-prefix {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        padding: 0 12px;
        border-right: 1px solid var(--border);
        background: #f1f5f9;
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .fee-input {
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        min-height: 44px;
        padding: 10px 12px;
        border: 0;
        outline: none;
        background: transparent;
        color: var(--text);
        font-family: inherit;
        font-size: 13px;
    }

    .fee-note {
        margin-top: 17px;
        padding: 12px 14px;
        border-radius: 10px;
        background: #eaf5ff;
        color: #1e40af;
        font-size: 11px;
        line-height: 1.7;
    }

    .form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 18px 23px;
        border-top: 1px solid var(--border);
        background: #fbfdff;
    }

    .side-card {
        padding: 21px;
    }

    .side-card + .side-card {
        margin-top: 17px;
    }

    .side-title {
        color: var(--navy);
        font-size: 14px;
        font-weight: 800;
    }

    .side-description {
        margin-top: 6px;
        color: var(--text-soft);
        font-size: 12px;
        line-height: 1.6;
    }

    .location-summary {
        margin-top: 17px;
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #f8fafc;
    }

    .location-summary-name {
        color: var(--navy);
        font-size: 13px;
        font-weight: 800;
    }

    .location-summary-city {
        margin-top: 4px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-badge::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        content: "";
    }

    .status-active {
        background: var(--success-bg);
        color: var(--success);
    }

    .status-active::before {
        background: #22c55e;
    }

    .status-inactive {
        background: #f1f5f9;
        color: var(--text-soft);
    }

    .status-inactive::before {
        background: #94a3b8;
    }

    .summary-fee {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 12px;
        color: var(--text-soft);
        font-size: 11px;
    }

    .summary-fee strong {
        color: var(--navy);
        font-weight: 800;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 17px;
    }

    .info-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-icon {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #f0f9ff;
        color: var(--blue);
    }

    .info-icon svg {
        width: 16px;
        height: 16px;
    }

    .info-content {
        min-width: 0;
    }

    .info-title {
        color: var(--text);
        font-size: 11px;
        font-weight: 800;
    }

    .info-text {
        margin-top: 3px;
        color: var(--text-soft);
        font-size: 11px;
        line-height: 1.5;
    }

    .side-action {
        margin-top: 16px;
    }

    .side-action .btn-secondary {
        width: 100%;
    }

    @media (max-width: 1000px) {
        .form-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .form-grid,
        .fee-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full,
        .fee-section {
            grid-column: auto;
        }

        .form-card-header,
        .form-card-body {
            padding: 18px;
        }

        .fee-section {
            padding: 16px;
        }

        .form-footer {
            flex-direction: column-reverse;
            padding: 17px 18px;
        }

        .form-footer .btn-primary,
        .form-footer .btn-secondary {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('admin.locations.index') }}">
        Lokasi
    </a>

    <span class="breadcrumb-separator">/</span>

    <a href="{{ route('admin.locations.show', $location) }}">
        {{ $location->name }}
    </a>

    <span class="breadcrumb-separator">/</span>

    <span>Edit</span>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Lokasi</h1>

        <p class="page-description">
            Perbarui informasi lokasi dan tarif dasar
            masuk kolam per sesi untuk customer dan coach.
        </p>
    </div>
</div>

<div class="form-layout">

    <div class="card form-card">

        <div class="form-card-header">
            <div class="form-card-title">
                Informasi Lokasi
            </div>

            <div class="form-card-description">
                Ubah informasi yang diperlukan kemudian simpan perubahan.
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('admin.locations.update', $location) }}"
        >
            @csrf
            @method('PUT')

            <div class="form-card-body">

                <div class="form-grid">

                    {{-- NAMA --}}
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Nama Lokasi
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $location->name) }}"
                            maxlength="150"
                            required
                            autofocus
                        >

                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KOTA --}}
                    <div class="form-group">
                        <label for="city" class="form-label">
                            Kota / Kabupaten
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}"
                            value="{{ old('city', $location->city) }}"
                            maxlength="100"
                            required
                        >

                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ALAMAT --}}
                    <div class="form-group full">
                        <label for="address" class="form-label">
                            Alamat Lengkap
                            <span class="required">*</span>
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                            maxlength="1000"
                            required
                        >{{ old('address', $location->address) }}</textarea>

                        <div class="form-help">
                            Gunakan alamat yang jelas agar mudah
                            ditemukan oleh peserta dan pelatih.
                        </div>

                        @error('address')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- GOOGLE MAPS --}}
                    <div class="form-group full">
                        <label for="google_maps_url" class="form-label">
                            Link Google Maps
                        </label>

                        <input
                            type="url"
                            id="google_maps_url"
                            name="google_maps_url"
                            class="form-control {{ $errors->has('google_maps_url') ? 'is-invalid' : '' }}"
                            value="{{ old('google_maps_url', $location->google_maps_url) }}"
                            placeholder="https://maps.google.com/..."
                            maxlength="1000"
                        >

                        <div class="form-help">
                            Opsional. Kosongkan jika lokasi belum
                            memiliki link Google Maps.
                        </div>

                        @error('google_maps_url')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="form-group full">
                        <label for="description" class="form-label">
                            Deskripsi Lokasi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                            maxlength="5000"
                            placeholder="Jelaskan secara singkat mengenai lokasi..."
                        >{{ old('description', $location->description) }}</textarea>

                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- FASILITAS --}}
                    <div class="form-group full">
                        <label for="facilities" class="form-label">
                            Fasilitas
                        </label>

                        <textarea
                            id="facilities"
                            name="facilities"
                            class="form-control {{ $errors->has('facilities') ? 'is-invalid' : '' }}"
                            maxlength="5000"
                            placeholder="Contoh: Kolam anak, ruang ganti, shower, area tunggu"
                        >{{ old('facilities', $location->facilities) }}</textarea>

                        <div class="form-help">
                            Fasilitas dapat dipisahkan menggunakan tanda koma.
                        </div>

                        @error('facilities')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- BIAYA MASUK KOLAM --}}
                    <div class="fee-section">

                        <div class="fee-section-title">
                            Biaya Masuk Kolam per Sesi
                        </div>

                        <div class="fee-section-description">
                            Perbarui tarif dasar untuk satu customer dan
                            satu coach dalam satu sesi latihan.
                        </div>

                        <div class="fee-grid">

                            <div class="form-group">
                                <label
                                    for="customer_entry_fee"
                                    class="form-label"
                                >
                                    Biaya Masuk Customer
                                    <span class="required">*</span>
                                </label>

                                <div class="fee-input-wrap {{ $errors->has('customer_entry_fee') ? 'is-invalid' : '' }}">
                                    <span class="fee-prefix">Rp</span>

                                    <input
                                        type="number"
                                        id="customer_entry_fee"
                                        name="customer_entry_fee"
                                        class="fee-input"
                                        value="{{ old('customer_entry_fee', $location->customer_entry_fee) }}"
                                        min="0"
                                        max="9999999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        required
                                    >
                                </div>

                                <div class="form-help">
                                    Tarif per customer per sesi.
                                    Isi 0 jika gratis.
                                </div>

                                @error('customer_entry_fee')
                                    <div class="form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label
                                    for="coach_entry_fee"
                                    class="form-label"
                                >
                                    Biaya Masuk Coach
                                    <span class="required">*</span>
                                </label>

                                <div class="fee-input-wrap {{ $errors->has('coach_entry_fee') ? 'is-invalid' : '' }}">
                                    <span class="fee-prefix">Rp</span>

                                    <input
                                        type="number"
                                        id="coach_entry_fee"
                                        name="coach_entry_fee"
                                        class="fee-input"
                                        value="{{ old('coach_entry_fee', $location->coach_entry_fee) }}"
                                        min="0"
                                        max="9999999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        required
                                    >
                                </div>

                                <div class="form-help">
                                    Tarif per coach per sesi.
                                    Isi 0 jika gratis.
                                </div>

                                @error('coach_entry_fee')
                                    <div class="form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                        <div class="fee-note">
                            <strong>Catatan:</strong>
                            Perubahan tarif ini hanya memperbarui
                            tarif dasar lokasi. Harga paket kursus
                            tidak berubah. Tarif aktual pada setiap
                            sesi akan dikelola melalui modul jadwal
                            pada tahap pengembangan berikutnya.
                        </div>

                    </div>

                </div>

            </div>


            {{-- PENGATURAN TIKET DAN PROMO --}}
            <div class="fee-section" style="margin-top:24px;">
                <div class="fee-section-title">Pengaturan Tiket Masuk Kolam</div>
                <div class="fee-section-description">Atur kebijakan tiket Customer dan Coach untuk lokasi ini. Harga tiket tetap diisi pada bagian Biaya Masuk Kolam di atas.</div>
                <div class="fee-grid">
                    <div class="form-group"><label class="form-label" for="customer_ticket_status">Status Tiket Customer</label><select id="customer_ticket_status" name="customer_ticket_status" class="form-control"><option value="unconfigured" {{ old('customer_ticket_status', $location->customer_ticket_status) === 'unconfigured' ? 'selected' : '' }}>Belum ditentukan</option><option value="free" {{ old('customer_ticket_status', $location->customer_ticket_status) === 'free' ? 'selected' : '' }}>Gratis</option><option value="paid" {{ old('customer_ticket_status', $location->customer_ticket_status) === 'paid' ? 'selected' : '' }}>Berbayar</option></select>@error('customer_ticket_status')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="customer_ticket_payer">Penanggung Biaya Customer</label><select id="customer_ticket_payer" name="customer_ticket_payer" class="form-control"><option value="customer" {{ old('customer_ticket_payer', $location->customer_ticket_payer) === 'customer' ? 'selected' : '' }}>Customer</option><option value="ragen" {{ old('customer_ticket_payer', $location->customer_ticket_payer) === 'ragen' ? 'selected' : '' }}>Ragen Swim Course</option><option value="pool" {{ old('customer_ticket_payer', $location->customer_ticket_payer) === 'pool' ? 'selected' : '' }}>Pihak Kolam</option></select>@error('customer_ticket_payer')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="customer_ticket_payment_method">Metode Pembayaran Customer</label><select id="customer_ticket_payment_method" name="customer_ticket_payment_method" class="form-control"><option value="direct" {{ old('customer_ticket_payment_method', $location->customer_ticket_payment_method) === 'direct' ? 'selected' : '' }}>Langsung ke Kolam</option><option value="through_ragen" {{ old('customer_ticket_payment_method', $location->customer_ticket_payment_method) === 'through_ragen' ? 'selected' : '' }}>Melalui Ragen</option></select>@error('customer_ticket_payment_method')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="coach_ticket_status">Status Tiket Coach</label><select id="coach_ticket_status" name="coach_ticket_status" class="form-control"><option value="unconfigured" {{ old('coach_ticket_status', $location->coach_ticket_status) === 'unconfigured' ? 'selected' : '' }}>Belum ditentukan</option><option value="free" {{ old('coach_ticket_status', $location->coach_ticket_status) === 'free' ? 'selected' : '' }}>Gratis</option><option value="paid" {{ old('coach_ticket_status', $location->coach_ticket_status) === 'paid' ? 'selected' : '' }}>Berbayar</option></select>@error('coach_ticket_status')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="coach_ticket_payer">Penanggung Biaya Coach</label><select id="coach_ticket_payer" name="coach_ticket_payer" class="form-control"><option value="ragen" {{ old('coach_ticket_payer', $location->coach_ticket_payer) === 'ragen' ? 'selected' : '' }}>Ragen Swim Course</option><option value="coach" {{ old('coach_ticket_payer', $location->coach_ticket_payer) === 'coach' ? 'selected' : '' }}>Coach</option><option value="customer" {{ old('coach_ticket_payer', $location->coach_ticket_payer) === 'customer' ? 'selected' : '' }}>Customer</option><option value="pool" {{ old('coach_ticket_payer', $location->coach_ticket_payer) === 'pool' ? 'selected' : '' }}>Pihak Kolam</option></select>@error('coach_ticket_payer')<div class="form-error">{{ $message }}</div>@enderror</div>
                </div>
                <div class="fee-note"><strong>Catatan:</strong> Pilih Belum ditentukan apabila harga tiket masih menunggu konfirmasi owner. Pengaturan ini belum mengubah checkout.</div>
            </div>
            <div class="fee-section" style="margin-top:24px;">
                <div class="fee-section-title">Promo Full Include</div>
                <div class="fee-section-description">Konfigurasi promo lokasi. Promo belum otomatis diterapkan pada paket maupun checkout.</div>
                <div class="fee-grid">
                    <div class="form-group"><label class="form-label" for="full_include_enabled">Status Promo</label><select id="full_include_enabled" name="full_include_enabled" class="form-control"><option value="0" {{ !old('full_include_enabled', $location->full_include_enabled) ? 'selected' : '' }}>Nonaktif</option><option value="1" {{ old('full_include_enabled', $location->full_include_enabled) ? 'selected' : '' }}>Aktif (konfigurasi saja)</option></select></div>
                    <div class="form-group"><label class="form-label" for="full_include_name">Nama Promo</label><input class="form-control" type="text" id="full_include_name" name="full_include_name" maxlength="255" value="{{ old('full_include_name', $location->full_include_name) }}">@error('full_include_name')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="full_include_price">Harga Promo (Rp)</label><input class="form-control" type="number" id="full_include_price" name="full_include_price" min="0" max="9999999999.99" step="0.01" value="{{ old('full_include_price', $location->full_include_price) }}">@error('full_include_price')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="full_include_start_date">Tanggal Mulai</label><input class="form-control" type="date" id="full_include_start_date" name="full_include_start_date" value="{{ old('full_include_start_date', $location->full_include_start_date?->format('Y-m-d')) }}">@error('full_include_start_date')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label class="form-label" for="full_include_end_date">Tanggal Berakhir</label><input class="form-control" type="date" id="full_include_end_date" name="full_include_end_date" value="{{ old('full_include_end_date', $location->full_include_end_date?->format('Y-m-d')) }}">@error('full_include_end_date')<div class="form-error">{{ $message }}</div>@enderror</div>
                </div>
                <div class="fee-note"><strong>Penting:</strong> Promo Full Include belum terhubung dengan paket tertentu. Jangan gunakan pengaturan ini sebagai harga final sebelum owner menyetujui ketentuannya.</div>
            </div>

            <div class="form-footer">
                <a
                    href="{{ route('admin.locations.show', $location) }}"
                    class="btn-secondary"
                >
                    Batal
                </a>

                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

    {{-- SIDE INFORMATION --}}
    <div>

        <div class="card side-card">

            <div class="side-title">
                Lokasi Saat Ini
            </div>

            <div class="side-description">
                Informasi singkat lokasi yang sedang diperbarui.
            </div>

            <div class="location-summary">

                <div class="location-summary-name">
                    {{ $location->name }}
                </div>

                <div class="location-summary-city">
                    {{ $location->city }}
                </div>

                @if ($location->status === 'active')

                    <span class="status-badge status-active">
                        Aktif
                    </span>

                @else

                    <span class="status-badge status-inactive">
                        Nonaktif
                    </span>

                @endif

                <div class="summary-fee">
                    <span>Customer / sesi</span>
                    <strong>
                        {{ $location->customer_entry_fee_formatted }}
                    </strong>
                </div>

                <div class="summary-fee">
                    <span>Coach / sesi</span>
                    <strong>
                        {{ $location->coach_entry_fee_formatted }}
                    </strong>
                </div>

            </div>

            <div class="side-action">
                <a
                    href="{{ route('admin.locations.show', $location) }}"
                    class="btn-secondary"
                >
                    Lihat Detail Lokasi
                </a>
            </div>

        </div>

        <div class="card side-card">

            <div class="side-title">
                Catatan
            </div>

            <div class="side-description">
                Beberapa hal yang perlu diperhatikan ketika
                memperbarui lokasi.
            </div>

            <div class="info-list">

                <div class="info-item">
                    <div class="info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12 21L12 3M5 10L12 3L19 10"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <div class="info-content">
                        <div class="info-title">
                            Jadwal tidak dihapus
                        </div>

                        <div class="info-text">
                            Mengedit lokasi tidak menghapus
                            jadwal kelas yang sudah terhubung.
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M5 12L10 17L19 7"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <div class="info-content">
                        <div class="info-title">
                            Status dikelola terpisah
                        </div>

                        <div class="info-text">
                            Aktivasi atau nonaktivasi lokasi
                            dilakukan melalui halaman Detail Lokasi.
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4 7H20M4 12H20M4 17H20"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <div class="info-content">
                        <div class="info-title">
                            Tarif terpisah dari paket
                        </div>

                        <div class="info-text">
                            Biaya kolam dicatat sebagai tarif
                            dasar per sesi, bukan bagian dari
                            harga paket kursus.
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
