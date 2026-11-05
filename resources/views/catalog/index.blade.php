@php
    $catalogLogoPath = \App\Models\Setting::getValue('website_logo', 'branding/ragen-logo.png');
    $catalogLogoUrl = $catalogLogoPath && $catalogLogoPath !== 'branding/ragen-logo.png'
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($catalogLogoPath)
        : asset('branding/ragen-logo.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Katalog paket latihan Ragen Swim Course. Cari dan pilih paket sesuai program renang.">
    <title>Katalog Paket Renang | Ragen Swim Course</title>
    <style>
        :root{color-scheme:light;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#102b4e;background:#f5f8fc}
        *{box-sizing:border-box}body{margin:0}a{color:inherit;text-decoration:none}button,input,select{font:inherit}
        .wrap{max-width:1150px;margin:auto;padding:0 24px}.header{background:#fff;border-bottom:1px solid #e2e9f2}
        .header-inner{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:17px 0}.brand{display:flex;align-items:center;gap:12px;font-weight:800}.brand img{width:48px;height:48px;object-fit:contain}.home-link{color:#195ca5;font-weight:700}
        .hero{padding:58px 0 34px}.eyebrow{color:#2474bd;font-weight:800;font-size:.8rem;letter-spacing:.12em;text-transform:uppercase}h1{font-size:clamp(2rem,4vw,3rem);line-height:1.14;margin:12px 0}p{line-height:1.65;color:#5b6f85}.hero p{max-width:700px}
        .filters{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;background:#fff;padding:20px;border:1px solid #e1e9f2;border-radius:18px;box-shadow:0 10px 30px rgba(19,50,84,.04)}
        .field{display:grid;gap:8px;font-weight:700;font-size:.88rem}.field input,.field select{width:100%;min-width:0;border:1px solid #cbd9e8;border-radius:10px;padding:12px;background:#fff;color:#153658}.btn{display:inline-flex;justify-content:center;align-items:center;border:0;border-radius:10px;background:#145da0;color:#fff;padding:12px 18px;font-weight:800;cursor:pointer}.btn:hover{background:#0c4379}.btn:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid #64a8ee;outline-offset:3px}
        .results{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:30px 0 18px}.results h2{margin:0;font-size:1.25rem}.muted{color:#62768c;font-size:.9rem}.cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.card{background:#fff;border:1px solid #e1e9f2;border-radius:18px;padding:24px;display:flex;flex-direction:column;gap:12px;box-shadow:0 12px 28px rgba(18,47,78,.045)}.card:hover{border-color:#8ab7e3}.tag{font-size:.8rem;color:#1665a9;font-weight:800}.card h3{margin:0;font-size:1.3rem}.card p{margin:0;flex:1}.price{font-size:1.65rem;font-weight:850;color:#0b477f}.meta{display:flex;justify-content:space-between;gap:10px;padding-top:14px;border-top:1px solid #e7edf5;color:#5b6f85;font-size:.9rem}.meta strong{color:#173f68}.empty{padding:45px 24px;background:white;border:1px solid #e1e9f2;border-radius:18px;text-align:center}
        .pager{display:flex;align-items:center;justify-content:center;gap:16px;margin:36px 0 60px}.page-link{padding:10px 14px;border:1px solid #cbd9e8;background:white;border-radius:10px;color:#155a99;font-weight:700}.page-disabled{opacity:.45}.footer{background:#0b2948;color:#fff;padding:25px 0}.footer p{color:#c9d9e9;margin:0}
        @media(max-width:800px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.filters{grid-template-columns:1fr 1fr}.filters .btn{grid-column:1/-1}}
        @media(max-width:560px){.wrap{padding:0 16px}.cards,.filters{grid-template-columns:1fr}.filters .btn{grid-column:auto}.hero{padding-top:38px}.results{align-items:flex-start;flex-direction:column}.brand span{font-size:.9rem}.home-link{font-size:.86rem}.card{padding:20px}}
    </style>
</head>
<body>
    <header class="header"><div class="wrap header-inner">
        <a class="brand" href="{{ route('home') }}"><img src="{{ $catalogLogoUrl }}" alt="Logo Ragen Swim Course"><span>Ragen Swim Course</span></a>
        <a class="home-link" href="{{ route('home') }}">← Kembali ke Beranda</a>
    </div></header>
    <main class="wrap">
        <section class="hero"><span class="eyebrow">Katalog Program</span><h1>Temukan Paket Renang yang Tepat</h1><p>Bandingkan harga, jumlah sesi, dan masa berlaku paket. Semua informasi berasal dari paket aktif Ragen Swim Course.</p></section>
        <form class="filters" method="GET" action="{{ route('catalog.index') }}" aria-label="Filter paket renang">
            <label class="field">Program Renang
                <select name="program"><option value="">Semua Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" @selected($selectedProgram?->id === $program->id)>{{ $program->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">Cari Paket<input name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Nama atau deskripsi paket"></label>
            <button class="btn" type="submit">Cari Paket</button>
        </form>
        <div class="results"><h2>{{ $selectedProgram ? 'Paket ' . $selectedProgram->name : ($hasProgramFilter ? 'Program Tidak Ditemukan' : 'Semua Paket Tersedia') }}</h2><span class="muted">{{ $packages->total() }} paket ditemukan</span></div>
        @if($packages->isEmpty())
            <div class="empty"><h3>Belum ada paket yang sesuai</h3><p>Coba pilih program lain atau hapus kata pencarian.</p><a class="home-link" href="{{ route('catalog.index') }}">Tampilkan semua paket →</a></div>
        @else
            <div class="cards">
                @foreach($packages as $package)
                    <article class="card">
                        <span class="tag">{{ $package->program?->name }}</span><h3>{{ $package->name }}</h3>
                        <p>{{ $package->description ?: 'Program latihan terarah bersama Ragen Swim Course.' }}</p>
                        <div class="price">Rp{{ number_format((float) $package->price, 0, ',', '.') }}</div>
                        <div class="meta"><span>Pertemuan <strong>{{ $package->total_sessions }} sesi</strong></span><span>Masa aktif <strong>{{ $package->validity_days ? $package->validity_days . ' hari' : 'Sesuai ketentuan' }}</strong></span></div>
                        <a class="btn" href="{{ route('customer.packages.browse', ['package' => $package->id]) . '#paket-' . $package->id }}">Pilih Paket</a>
                    </article>
                @endforeach
            </div>
            @if($packages->hasPages())
                <nav class="pager" aria-label="Navigasi halaman paket">
                    @if($packages->onFirstPage())<span class="page-link page-disabled">← Sebelumnya</span>@else<a class="page-link" href="{{ $packages->previousPageUrl() }}">← Sebelumnya</a>@endif
                    <span class="muted">Halaman {{ $packages->currentPage() }} dari {{ $packages->lastPage() }}</span>
                    @if($packages->hasMorePages())<a class="page-link" href="{{ $packages->nextPageUrl() }}">Berikutnya →</a>@else<span class="page-link page-disabled">Berikutnya →</span>@endif
                </nav>
            @endif
        @endif
    </main>
    <footer class="footer"><div class="wrap"><p>© {{ date('Y') }} Ragen Swim Course. Belajar renang dengan nyaman dan terarah.</p></div></footer>
</body>
</html>
