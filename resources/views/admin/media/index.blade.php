@extends('layouts.admin')

@section('content')
<style>
.media-wrap {
    max-width: 1150px;
    margin: auto;
    color: #17375d;
}

.media-wrap h1 {
    font-size: 26px;
    margin: 0 0 8px;
}

.media-card {
    background: #fff;
    border: 1px solid #dbe5ef;
    border-radius: 15px;
    padding: 22px;
    margin: 18px 0;
    box-shadow: 0 3px 14px #0b2c5010;
}

.media-wrap input,
.media-wrap select,
.media-wrap textarea {
    border: 1px solid #ccd9e5;
    border-radius: 8px;
    padding: 10px;
    width: 100%;
    max-width: 100%;
    background: white;
}

.media-wrap label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 12px;
    margin: 14px 0;
}

.media-wrap button {
    border: 0;
    background: #174d89;
    color: white;
    border-radius: 8px;
    padding: 10px 16px;
    cursor: pointer;
}

.media-wrap button.danger {
    background: #b72e3b;
}

.media-item {
    border-top: 1px solid #e3eaf2;
    padding: 16px 0;
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 18px;
}

.media-item img {
    width: 120px;
    height: 100px;
    object-fit: contain;
    background: #f1f6fb;
    border-radius: 9px;
}

.media-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.media-note {
    color: #61758c;
    font-size: 13px;
}

.media-wrap details summary {
    cursor: pointer;
    color: #174d89;
    font-weight: 700;
    margin: 10px 0;
}

.media-preview {
    display: flex;
    align-items: center;
    gap: 18px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.media-preview img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    padding: 12px;
    background: #f1f6fb;
    border: 1px solid #dbe5ef;
    border-radius: 12px;
}

@media (max-width: 600px) {
    .media-item {
        grid-template-columns: 1fr;
    }

    .media-item img {
        width: 100%;
    }
}
</style>

<div class="media-wrap">

    <h1>Kelola Website</h1>

    <p>
        Upload, edit, urutkan, tampilkan/sembunyikan,
        atau hapus konten langsung dari dashboard.
    </p>

    @if(session('success'))
        <div class="media-card" style="color:green">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="media-card" style="color:#b72e3b">
            {{ implode(' | ', $errors->all()) }}
        </div>
    @endif

    {{-- LOGO WEBSITE --}}

    <div class="media-card">
        <h2>Logo Website</h2>

        <div class="media-preview">
            <img
                src="{{ $logo === 'branding/ragen-logo.png' ? asset('branding/ragen-logo.png') : asset('storage/'.$logo) }}"
                alt="Logo Ragen Swim Course"
            >

            <p class="media-note">
                Logo utama yang digunakan pada website Ragen Swim Course.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.media.logo') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="media-grid">
                <div>
                    <label for="website-logo">
                        Ganti Logo (PNG/JPG/WebP)
                    </label>

                    <input
                        id="website-logo"
                        type="file"
                        name="logo"
                        accept="image/png,image/jpeg,image/webp"
                        required
                    >
                </div>
            </div>

            <button type="submit">Simpan Logo</button>
        </form>
    </div>

    {{-- FAVICON WEBSITE --}}

    <div class="media-card">
        <h2>Favicon Website</h2>

        <p class="media-note">
            Favicon adalah ikon kecil yang muncul pada tab browser.
            Gunakan gambar berbentuk persegi, disarankan ukuran
            256 × 256 piksel atau 512 × 512 piksel.
            Ukuran file maksimal 2 MB.
        </p>

        <div class="media-preview">
            <img
                src="{{ ($favicon ?? 'favicon.ico') === 'favicon.ico' ? asset('favicon.ico') : asset('storage/'.$favicon) }}"
                alt="Favicon Ragen Swim Course"
            >

            <div>
                <strong>Favicon Saat Ini</strong>

                <p class="media-note">
                    {{ ($favicon ?? 'favicon.ico') === 'favicon.ico' ? 'Menggunakan favicon bawaan website.' : 'Menggunakan favicon yang diunggah melalui dashboard Admin.' }}
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('admin.media.favicon') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="media-grid">
                <div>
                    <label for="website-favicon">
                        Ganti Favicon (ICO/PNG/JPG/WebP)
                    </label>

                    <input
                        id="website-favicon"
                        type="file"
                        name="favicon"
                        accept=".ico,image/x-icon,image/vnd.microsoft.icon,image/png,image/jpeg,image/webp"
                        required
                    >
                </div>
            </div>

            <button type="submit">Simpan Favicon</button>
        </form>
    </div>

    {{-- GALLERY KEGIATAN --}}

    <div class="media-card">
        <h2>Gallery Kegiatan</h2>

        <p class="media-note">
            Foto yang dipublikasikan akan muncul di beranda.
            Upload maksimal 5 MB.
        </p>

        <details>
            <summary>+ Tambah Foto</summary>

            <form
                method="POST"
                action="{{ route('admin.media.gallery.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="media-grid">
                    <div>
                        <label>Foto</label>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" required>
                    </div>

                    <div>
                        <label>Judul</label>
                        <input name="title" maxlength="150">
                    </div>

                    <div>
                        <label>Kategori</label>
                        <select name="category">
                            <option value="regular">Kelas Reguler</option>
                            <option value="private">Kelas Private</option>
                            <option value="special_needs">Kelas ABK</option>
                            <option value="trial">Trial</option>
                            <option value="event">Event</option>
                            <option value="facility">Fasilitas</option>
                            <option value="coach">Pelatih</option>
                        </select>
                    </div>

                    <div>
                        <label>Urutan</label>
                        <input
                            name="sort_order"
                            type="number"
                            min="0"
                            value="0"
                            required
                        >
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="published">Tampilkan</option>
                            <option value="draft">Sembunyikan</option>
                        </select>
                    </div>
                </div>

                <label>Deskripsi</label>
                <textarea name="description" rows="2"></textarea>

                <p>
                    <button type="submit">Tambah Foto</button>
                </p>
            </form>
        </details>

        @forelse($galleries as $gallery)
            <div class="media-item">
                <img
                    src="{{ asset('storage/'.$gallery->image) }}"
                    alt="{{ $gallery->title }}"
                >

                <div>
                    <strong>{{ $gallery->title ?: 'Tanpa judul' }}</strong>

                    <p class="media-note">
                        {{ $gallery->category_label }}
                        ·
                        {{ $gallery->status_label }}
                    </p>

                    <details>
                        <summary>Edit Foto</summary>

                        <form
                            method="POST"
                            action="{{ route('admin.media.gallery.update', $gallery) }}"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @method('PUT')

                            <div class="media-grid">
                                <div>
                                    <label>Ganti foto (opsional)</label>
                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/png,image/jpeg,image/webp"
                                    >
                                </div>

                                <div>
                                    <label>Judul</label>
                                    <input
                                        name="title"
                                        value="{{ $gallery->title }}"
                                    >
                                </div>

                                <div>
                                    <label>Kategori</label>
                                    <select name="category">
                                        @foreach([
                                            'regular' => 'Kelas Reguler',
                                            'private' => 'Kelas Private',
                                            'special_needs' => 'Kelas ABK',
                                            'trial' => 'Trial',
                                            'event' => 'Event',
                                            'facility' => 'Fasilitas',
                                            'coach' => 'Pelatih',
                                        ] as $value => $label)
                                            <option
                                                value="{{ $value }}"
                                                @selected($gallery->category === $value)
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label>Urutan</label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="sort_order"
                                        value="{{ $gallery->sort_order }}"
                                        required
                                    >
                                </div>

                                <div>
                                    <label>Status</label>

                                    <select name="status">
                                        <option
                                            value="published"
                                            @selected($gallery->status === 'published')
                                        >
                                            Tampilkan
                                        </option>

                                        <option
                                            value="draft"
                                            @selected($gallery->status === 'draft')
                                        >
                                            Sembunyikan
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <label>Deskripsi</label>

                            <textarea name="description">{{ $gallery->description }}</textarea>

                            <p>
                                <button type="submit">Simpan Perubahan</button>
                            </p>
                        </form>
                    </details>

                    <form
                        method="POST"
                        action="{{ route('admin.media.gallery.destroy', $gallery) }}"
                        onsubmit="return confirm('Hapus foto ini?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="danger">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p>Belum ada foto. Tambahkan foto pertama di atas.</p>
        @endforelse
    </div>

    {{-- SPONSOR DAN PARTNER --}}

    <div class="media-card">
        <h2>Sponsor &amp; Partner</h2>

        <p class="media-note">
            Hanya masukkan sponsor yang benar-benar bekerja sama
            dengan Ragen Swim Course.
        </p>

        <details>
            <summary>+ Tambah Sponsor</summary>

            <form
                method="POST"
                action="{{ route('admin.media.sponsor.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="media-grid">
                    <div>
                        <label>Nama</label>
                        <input name="name" required>
                    </div>

                    <div>
                        <label>Logo</label>
                        <input
                            type="file"
                            name="logo"
                            accept="image/png,image/jpeg,image/webp"
                            required
                        >
                    </div>

                    <div>
                        <label>Website (opsional)</label>
                        <input
                            name="website_url"
                            type="url"
                            placeholder="https://"
                        >
                    </div>

                    <div>
                        <label>Urutan</label>
                        <input
                            name="sort_order"
                            type="number"
                            min="0"
                            value="0"
                            required
                        >
                    </div>

                    <div>
                        <label>Status</label>

                        <select name="is_active">
                            <option value="1">Tampilkan</option>
                            <option value="0">Sembunyikan</option>
                        </select>
                    </div>
                </div>

                <button type="submit">Tambah Sponsor</button>
            </form>
        </details>

        @forelse($sponsors as $sponsor)
            <div class="media-item">
                <img
                    src="{{ asset('storage/'.$sponsor->logo) }}"
                    alt="{{ $sponsor->name }}"
                >

                <div>
                    <strong>{{ $sponsor->name }}</strong>

                    <p class="media-note">
                        {{ $sponsor->is_active ? 'Tampil' : 'Disembunyikan' }}
                    </p>

                    <details>
                        <summary>Edit Sponsor</summary>

                        <form
                            method="POST"
                            action="{{ route('admin.media.sponsor.update', $sponsor) }}"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @method('PUT')

                            <div class="media-grid">
                                <div>
                                    <label>Nama</label>
                                    <input
                                        name="name"
                                        value="{{ $sponsor->name }}"
                                        required
                                    >
                                </div>

                                <div>
                                    <label>Ganti logo (opsional)</label>
                                    <input
                                        type="file"
                                        name="logo"
                                        accept="image/png,image/jpeg,image/webp"
                                    >
                                </div>

                                <div>
                                    <label>Website</label>
                                    <input
                                        type="url"
                                        name="website_url"
                                        value="{{ $sponsor->website_url }}"
                                    >
                                </div>

                                <div>
                                    <label>Urutan</label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="sort_order"
                                        value="{{ $sponsor->sort_order }}"
                                        required
                                    >
                                </div>

                                <div>
                                    <label>Status</label>

                                    <select name="is_active">
                                        <option
                                            value="1"
                                            @selected($sponsor->is_active)
                                        >
                                            Tampilkan
                                        </option>

                                        <option
                                            value="0"
                                            @selected(!$sponsor->is_active)
                                        >
                                            Sembunyikan
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit">Simpan Perubahan</button>
                        </form>
                    </details>

                    <form
                        method="POST"
                        action="{{ route('admin.media.sponsor.destroy', $sponsor) }}"
                        onsubmit="return confirm('Hapus sponsor ini?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="danger">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p>Belum ada sponsor terdaftar.</p>
        @endforelse
    </div>

</div>
@endsection
