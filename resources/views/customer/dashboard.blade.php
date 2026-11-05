@extends('layouts.customer')

@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard Customer')

@php
    $latestPayment = $latestOrder?->payments
        ?->sortByDesc('id')
        ?->first();

    $latestOrderItem = $latestOrder?->items?->first();

    $progressParticipantName = $latestProgress?->participant?->full_name ?? '-';
    $progressCoachName = $latestProgress?->coach?->user?->name ?? '-';

    $progressSkills = $latestProgress ? [
        'Adaptasi Air' => $latestProgress->water_adaptation,
        'Mengapung' => $latestProgress->floating,
        'Pernapasan' => $latestProgress->breathing,
        'Gerakan Kaki' => $latestProgress->kicking,
        'Gaya Bebas' => $latestProgress->freestyle,
        'Gaya Dada' => $latestProgress->breaststroke,
    ] : [];

    $averageScore = $latestProgress?->average_score;

    $orderStatusClass = match ($latestOrder?->status) {
        'paid' => 'status-success',
        'pending' => 'status-warning',
        'cancelled', 'expired', 'refunded' => 'status-danger',
        default => 'status-neutral',
    };

    $paymentStatusClass = match ($latestPayment?->status) {
        'paid' => 'status-success',
        'pending' => 'status-warning',
        'failed', 'expired', 'refunded' => 'status-danger',
        default => 'status-neutral',
    };

    $invoiceStatusClass = match ($latestInvoice?->status) {
        'paid' => 'status-success',
        'unpaid' => 'status-warning',
        'cancelled', 'refunded' => 'status-danger',
        default => 'status-neutral',
    };

    $formatTime = function ($time) {
        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }

        return $time ? substr((string) $time, 0, 5) : '-';
    };
@endphp

@section('content')

<div class="customer-dashboard">

    {{-- WELCOME --}}
    <section class="customer-welcome">
        <div class="customer-welcome-copy">
            <span class="customer-eyebrow">RAGEN SWIM COURSE · CUSTOMER PORTAL</span>

            <h1>Halo, {{ $user->name }}!</h1>

            <p>
                Kelola peserta, lihat jadwal berenang, pantau sisa pertemuan,
                dan ikuti perkembangan latihan melalui satu dashboard.
            </p>
        </div>

        <div class="customer-welcome-actions">
            <a href="{{ route('customer.bookings.index') }}" class="customer-btn customer-btn-glass">
                Jadwal Saya
            </a>

            <a href="{{ route('customer.packages.browse') }}" class="customer-btn customer-btn-aqua">
                Beli Paket
            </a>
        </div>
    </section>

    {{-- AKSES CEPAT CUSTOMER --}}
    <nav class="customer-quick-actions" aria-label="Akses cepat customer">
        <a class="customer-quick-action" href="{{ route('customer.participants.index') }}">
            <span class="customer-quick-action-symbol" aria-hidden="true">01</span>
            <span><strong>Peserta Saya</strong><small>Kelola profil anggota keluarga</small></span>
        </a>
        <a class="customer-quick-action" href="{{ route('customer.bookings.index') }}">
            <span class="customer-quick-action-symbol" aria-hidden="true">02</span>
            <span><strong>Jadwal Renang</strong><small>Periksa kelas dan reservasi</small></span>
        </a>
        <a class="customer-quick-action" href="{{ route('customer.packages.index') }}">
            <span class="customer-quick-action-symbol" aria-hidden="true">03</span>
            <span><strong>Paket Aktif</strong><small>Pantau sisa sesi dan masa berlaku</small></span>
        </a>
        <a class="customer-quick-action" href="{{ route('customer.progress.index') }}">
            <span class="customer-quick-action-symbol" aria-hidden="true">04</span>
            <span><strong>Progres Latihan</strong><small>Lihat perkembangan kemampuan</small></span>
        </a>
    </nav>

    {{-- PENGINGAT MASA BERLAKU PAKET --}}
    @if ($expiringPackages->isNotEmpty())
        <section class="customer-expiry-notice" aria-label="Pengingat masa berlaku paket">
            <div>
                <span class="customer-expiry-kicker">PENGINGAT PAKET</span>
                <h2>Paket segera berakhir</h2>
                <p>Periksa masa berlaku paket agar sisa sesi latihan dapat direncanakan.</p>
                <ul>
                    @foreach ($expiringPackages as $expiringPackage)
                        <li>
                            <strong>{{ $expiringPackage->participant?->full_name ?? 'Peserta' }}</strong>
                            — {{ $expiringPackage->package?->name ?? 'Paket renang' }}
                            · berakhir {{ \Illuminate\Support\Carbon::parse($expiringPackage->expiry_date)->translatedFormat('d M Y') }}
                            · {{ (int) $expiringPackage->remaining_sessions }} sesi tersisa
                        </li>
                    @endforeach
                </ul>
            </div>
            <a class="customer-btn customer-btn-outline" href="{{ route('customer.packages.browse') }}">Lihat Pilihan Paket</a>
        </section>
    @endif

    {{-- STATISTIK --}}
    <section class="customer-stats">

        <a href="{{ route('customer.participants.index') }}" class="customer-stat">
            <div class="customer-stat-icon icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="3.5"/>
                    <path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"/>
                </svg>
            </div>
            <div class="customer-stat-content">
                <span>Peserta Aktif</span>
                <strong>{{ number_format($statistics['participants'] ?? 0) }}</strong>
                <small>Profil peserta terdaftar</small>
            </div>
        </a>

        <a href="{{ route('customer.packages.index') }}" class="customer-stat">
            <div class="customer-stat-icon icon-cyan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7.5 12 4l8 3.5-8 3.5-8-3.5Z"/>
                    <path d="m4 12 8 3.5 8-3.5M4 16.5l8 3.5 8-3.5"/>
                </svg>
            </div>
            <div class="customer-stat-content">
                <span>Paket Aktif</span>
                <strong>{{ number_format($statistics['active_packages'] ?? 0) }}</strong>
                <small>Paket yang sedang berjalan</small>
            </div>
        </a>

        <a href="{{ route('customer.packages.index') }}" class="customer-stat">
            <div class="customer-stat-icon icon-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>
            <div class="customer-stat-content">
                <span>Sisa Sesi</span>
                <strong>{{ number_format($statistics['remaining_sessions'] ?? 0) }}</strong>
                <small>Total seluruh paket aktif</small>
            </div>
        </a>

        <a href="{{ route('customer.bookings.index') }}" class="customer-stat">
            <div class="customer-stat-icon icon-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M8 3v4M16 3v4M3 10h18"/>
                </svg>
            </div>
            <div class="customer-stat-content">
                <span>Booking Mendatang</span>
                <strong>{{ number_format($statistics['upcoming_bookings'] ?? 0) }}</strong>
                <small>Jadwal hari ini dan berikutnya</small>
            </div>
        </a>

    </section>

    {{-- JADWAL DAN PERKEMBANGAN --}}
    <div class="customer-grid">

        <section class="customer-panel">
            <div class="customer-panel-header">
                <div>
                    <span class="customer-section-label">JADWAL LATIHAN</span>
                    <h2>Kelas Berikutnya</h2>
                    <p>Informasi booking terdekat yang akan diikuti.</p>
                </div>

                <a href="{{ route('customer.bookings.index') }}" class="customer-text-link">
                    Semua Jadwal →
                </a>
            </div>

            @if ($nextBooking && $nextBooking->classSchedule)
                @php
                    $nextSchedule = $nextBooking->classSchedule;
                @endphp

                <div class="customer-next-class">
                    <div class="customer-date-box">
                        <strong>{{ $nextSchedule->class_date->format('d') }}</strong>
                        <span>{{ $nextSchedule->class_date->translatedFormat('M') }}</span>
                    </div>

                    <div class="customer-next-details">
                        <div class="customer-next-heading">
                            <div>
                                <h3>
                                    {{ $nextSchedule->title ?: ($nextSchedule->program?->name ?? 'Kelas Renang') }}
                                </h3>
                                <p>{{ $nextBooking->participant?->full_name ?? 'Peserta' }}</p>
                            </div>

                            <span class="customer-status {{ $nextBooking->status === 'confirmed' ? 'status-success' : 'status-warning' }}">
                                {{ $nextBooking->status === 'confirmed' ? 'Terkonfirmasi' : 'Menunggu Konfirmasi' }}
                            </span>
                        </div>

                        <div class="customer-next-meta">
                            <span>
                                {{ $nextSchedule->class_date->translatedFormat('l, d F Y') }}
                            </span>
                            <span>
                                {{ $formatTime($nextSchedule->start_time) }} –
                                {{ $formatTime($nextSchedule->end_time) }}
                            </span>
                            <span>
                                {{ $nextSchedule->location?->name ?? 'Lokasi belum ditentukan' }}
                            </span>
                            <span>
                                Coach: {{ $nextSchedule->coach?->user?->name ?? 'Belum ditentukan' }}
                            </span>
                        </div>

                        <a href="{{ route('customer.bookings.show', $nextBooking->id) }}"
                           class="customer-btn customer-btn-primary">
                            Detail Booking
                        </a>
                    </div>
                </div>
            @else
                <div class="customer-empty">
                    <div class="customer-empty-symbol">📅</div>
                    <strong>Belum Ada Jadwal Mendatang</strong>
                    <p>Jadwal latihan berikutnya akan muncul setelah kamu melakukan booking.</p>
                    <a href="{{ route('customer.bookings.index') }}" class="customer-btn customer-btn-primary">
                        Lihat Jadwal Saya
                    </a>
                </div>
            @endif
        </section>

        <section class="customer-panel">
            <div class="customer-panel-header">
                <div>
                    <span class="customer-section-label">PERKEMBANGAN PESERTA</span>
                    <h2>Evaluasi Terbaru</h2>
                    <p>Hasil penilaian terbaru dari coach.</p>
                </div>

                <a href="{{ route('customer.progress.index') }}" class="customer-text-link">
                    Detail →
                </a>
            </div>

            @if ($latestProgress)
                <div class="customer-progress-top">
                    <div class="customer-person">
                        <div class="customer-avatar">
                            {{ mb_strtoupper(mb_substr($progressParticipantName, 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ $progressParticipantName }}</strong>
                            <span>
                                {{ $latestProgress->assessment_date?->translatedFormat('d F Y') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="customer-score">
                        <strong>
                            {{ $averageScore !== null ? number_format((float) $averageScore, 2, ',', '.') : '-' }}
                        </strong>
                        <span>dari 5</span>
                    </div>
                </div>

                <div class="customer-progress-level">
                    <div>
                        <small>Level Kemampuan</small>
                        <strong>{{ $latestProgress->level_label ?? '-' }}</strong>
                    </div>
                </div>

                <div class="customer-skills">
                    @foreach ($progressSkills as $skillName => $skillScore)
                        @php
                            $validScore = is_numeric($skillScore)
                                ? max(0, min(5, (float) $skillScore))
                                : null;

                            $percentage = $validScore !== null
                                ? ($validScore / 5) * 100
                                : 0;
                        @endphp

                        <div class="customer-skill">
                            <div class="customer-skill-heading">
                                <span>{{ $skillName }}</span>
                                <strong>
                                    {{ $validScore !== null ? number_format($validScore, 1, ',', '.') . '/5' : '-' }}
                                </strong>
                            </div>
                            <div class="customer-skill-track">
                                <div class="customer-skill-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="customer-progress-footer">
                    <span>Coach: <strong>{{ $progressCoachName }}</strong></span>
                    <a href="{{ route('customer.progress.index', ['participant' => $latestProgress->participant_id]) }}"
                       class="customer-text-link">
                        Lihat Perkembangan →
                    </a>
                </div>
            @else
                <div class="customer-empty">
                    <div class="customer-empty-symbol">📈</div>
                    <strong>Belum Ada Evaluasi</strong>
                    <p>Perkembangan kemampuan akan ditampilkan setelah coach memberikan penilaian.</p>
                </div>
            @endif
        </section>

    </div>

    {{-- PESERTA DAN TRANSAKSI --}}
    <div class="customer-grid">

        <section class="customer-panel">
            <div class="customer-panel-header">
                <div>
                    <span class="customer-section-label">PESERTA</span>
                    <h2>Peserta Saya</h2>
                    <p>Peserta aktif yang terhubung dengan akun.</p>
                </div>

                <a href="{{ route('customer.participants.create') }}"
                   class="customer-btn customer-btn-outline">
                    + Tambah Peserta
                </a>
            </div>

            @forelse ($participants as $participant)
                @php
                    $participantPackages = $activePackages->where('participant_id', $participant->id);
                    $participantSessions = (int) $participantPackages->sum('remaining_sessions');
                @endphp

                <div class="customer-participant">
                    <div class="customer-person">
                        <div class="customer-avatar">
                            {{ mb_strtoupper(mb_substr($participant->full_name, 0, 1)) }}
                        </div>

                        <div>
                            <strong>{{ $participant->full_name }}</strong>
                            <span>
                                {{ match($participant->swimming_level) {
                                    'beginner' => 'Pemula',
                                    'basic' => 'Dasar',
                                    'intermediate' => 'Menengah',
                                    'advanced' => 'Lanjutan',
                                    default => 'Belum ditentukan'
                                } }}
                            </span>
                        </div>
                    </div>

                    <div class="customer-participant-actions">
                        @if ($participantPackages->isNotEmpty())
                            <div class="customer-session-count">
                                <strong>{{ $participantSessions }}</strong>
                                <span>sesi tersisa</span>
                            </div>
                        @else
                            <span class="customer-status status-neutral">Belum Ada Paket</span>
                        @endif

                        <a href="{{ route('customer.participants.edit', $participant->id) }}"
                           class="customer-edit-link">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="customer-empty">
                    <div class="customer-empty-symbol">👤</div>
                    <strong>Belum Ada Peserta</strong>
                    <p>Tambahkan diri sendiri atau anak yang akan mengikuti kelas renang.</p>
                    <a href="{{ route('customer.participants.create') }}" class="customer-btn customer-btn-primary">
                        Tambah Peserta
                    </a>
                </div>
            @endforelse

            @if ($participants->isNotEmpty())
                <div class="customer-panel-footer">
                    <a href="{{ route('customer.participants.index') }}" class="customer-text-link">
                        Kelola Semua Peserta →
                    </a>
                </div>
            @endif
        </section>

        <section class="customer-panel">
            <div class="customer-panel-header">
                <div>
                    <span class="customer-section-label">PEMBAYARAN</span>
                    <h2>Transaksi Terakhir</h2>
                    <p>Ringkasan pembelian paket terbaru.</p>
                </div>

                <a href="{{ route('customer.orders.index') }}" class="customer-text-link">
                    Riwayat →
                </a>
            </div>

            @if ($latestOrder)
                <div class="customer-transaction">
                    <div class="customer-transaction-header">
                        <div>
                            <small>Kode Order</small>
                            <strong>{{ $latestOrder->order_code }}</strong>
                        </div>
                        <span class="customer-status {{ $orderStatusClass }}">
                            {{ $latestOrder->status_label }}
                        </span>
                    </div>

                    <div class="customer-transaction-product">
                        <strong>{{ $latestOrderItem?->package_name ?? 'Pembelian Paket' }}</strong>
                        <span>{{ $latestOrderItem?->participant?->full_name ?? '-' }}</span>
                    </div>

                    <div class="customer-transaction-row">
                        <span>Tanggal</span>
                        <strong>
                            {{ $latestOrder->ordered_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </strong>
                    </div>

                    <div class="customer-transaction-row">
                        <span>Pembayaran</span>
                        <strong>
                            @if ($latestPayment)
                                <span class="customer-status {{ $paymentStatusClass }}">
                                    {{ match($latestPayment->status) {
                                        'paid' => 'Lunas',
                                        'pending' => 'Menunggu',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kedaluwarsa',
                                        'refunded' => 'Refund',
                                        default => ucfirst($latestPayment->status)
                                    } }}
                                </span>
                            @else
                                <span class="customer-status status-neutral">Belum Tersedia</span>
                            @endif
                        </strong>
                    </div>

                    <div class="customer-transaction-row customer-transaction-total">
                        <span>Total Pembayaran</span>
                        <strong>{{ $latestOrder->formatted_total }}</strong>
                    </div>

                    <a href="{{ route('customer.orders.show', $latestOrder->id) }}"
                       class="customer-btn customer-btn-primary customer-full-btn">
                        Detail Transaksi
                    </a>
                </div>
            @else
                <div class="customer-empty">
                    <div class="customer-empty-symbol">💳</div>
                    <strong>Belum Ada Transaksi</strong>
                    <p>Pembelian paket renang akan ditampilkan di sini.</p>
                    <a href="{{ route('customer.packages.browse') }}" class="customer-btn customer-btn-primary">
                        Lihat Paket Renang
                    </a>
                </div>
            @endif
        </section>

    </div>

    {{-- INVOICE --}}
    <section class="customer-panel">
        <div class="customer-panel-header">
            <div>
                <span class="customer-section-label">INVOICE</span>
                <h2>Invoice Terbaru</h2>
                <p>Dokumen tagihan dari pembelian paket.</p>
            </div>

            <a href="{{ route('customer.invoices.index') }}" class="customer-text-link">
                Semua Invoice →
            </a>
        </div>

        @if ($latestInvoice)
            <div class="customer-invoice">
                <div class="customer-invoice-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M7 3h8l4 4v14H5V5a2 2 0 0 1 2-2Z"/>
                        <path d="M15 3v5h4M9 12h6M9 16h6"/>
                    </svg>
                </div>

                <div class="customer-invoice-details">
                    <div class="customer-invoice-heading">
                        <div>
                            <small>Nomor Invoice</small>
                            <strong>{{ $latestInvoice->invoice_number }}</strong>
                        </div>

                        <span class="customer-status {{ $invoiceStatusClass }}">
                            {{ $latestInvoice->status_label }}
                        </span>
                    </div>

                    <div class="customer-invoice-meta">
                        <div>
                            <span>Tanggal Terbit</span>
                            <strong>{{ $latestInvoice->formatted_issued_at }}</strong>
                        </div>
                        <div>
                            <span>Kode Order</span>
                            <strong>{{ $latestInvoice->order?->order_code ?? '-' }}</strong>
                        </div>
                        <div>
                            <span>Total Pembayaran</span>
                            <strong>{{ $latestInvoice->formatted_total }}</strong>
                        </div>
                    </div>

                    <div class="customer-invoice-actions">
                        <a href="{{ route('customer.invoices.show', $latestInvoice) }}"
                           target="_blank"
                           rel="noopener"
                           class="customer-btn customer-btn-primary">
                            Lihat / Cetak Invoice
                        </a>

                        <a href="{{ route('customer.invoices.index') }}"
                           class="customer-btn customer-btn-outline">
                            Riwayat Invoice
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="customer-empty">
                <div class="customer-empty-symbol">📄</div>
                <strong>Belum Ada Invoice</strong>
                <p>Invoice akan muncul setelah transaksi pembelian paket diproses.</p>
                <a href="{{ route('customer.orders.index') }}" class="customer-btn customer-btn-primary">
                    Lihat Transaksi
                </a>
            </div>
        @endif
    </section>

    {{-- AKUN --}}
    <section class="customer-panel">
        <div class="customer-panel-header">
            <div>
                <span class="customer-section-label">AKUN</span>
                <h2>Informasi Akun</h2>
                <p>Data akun customer yang sedang digunakan.</p>
            </div>
        </div>

        <div class="customer-account-grid">
            <div>
                <span>Nama</span>
                <strong>{{ $user->name }}</strong>
            </div>
            <div>
                <span>Email</span>
                <strong>{{ $user->email }}</strong>
            </div>
            <div>
                <span>WhatsApp</span>
                <strong>{{ $user->phone ?: '-' }}</strong>
            </div>
            <div>
                <span>Status Akun</span>
                <strong>
                    <span class="customer-status status-success">Aktif</span>
                </strong>
            </div>
        </div>
    </section>

</div>

@endsection

@push('styles')
<style>
    .customer-dashboard {
        display: flex;
        flex-direction: column;
        gap: 21px;
        min-width: 0;
    }

    /* Akses cepat: ringkas, konsisten, dan dapat dioperasikan dengan keyboard. */
    .customer-quick-actions { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin:18px 0 22px; }
    .customer-quick-action { display:flex; align-items:center; gap:12px; min-width:0; padding:16px; border:1px solid #dce8f2; border-radius:16px; background:#fff; color:#143b5d; text-decoration:none; transition:box-shadow .2s,border-color .2s,transform .2s; }
    .customer-quick-action:hover { border-color:#1184ba; box-shadow:0 8px 24px rgba(12,61,101,.09); transform:translateY(-2px); }
    .customer-quick-action:focus-visible { outline:3px solid #38bdf8; outline-offset:3px; }
    .customer-quick-action-symbol { display:grid; place-items:center; flex:0 0 38px; width:38px; height:38px; border-radius:12px; background:#eaf6fc; color:#08658e; font-size:13px; font-weight:800; }
    .customer-quick-action strong,.customer-quick-action small { display:block; }
    .customer-quick-action strong { font-size:13px; line-height:1.5; }
    .customer-quick-action small { margin-top:3px; color:#61778a; font-size:11px; line-height:1.5; }
    @media(max-width:1100px) { .customer-quick-actions { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:560px) { .customer-quick-actions { grid-template-columns:1fr; } }
    @media(prefers-reduced-motion:reduce) { .customer-quick-action { transition:none; } .customer-quick-action:hover { transform:none; } }

    .customer-expiry-notice {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
        margin: 20px 0;
        padding: 20px 24px;
        background: #fff9ed;
        border: 1px solid #f4dbac;
        border-radius: 18px;
        color: #62451b;
    }
    .customer-expiry-notice h2 { font-size: 18px; margin: 6px 0; color: #55390f; }
    .customer-expiry-notice p { font-size: 13px; margin: 0 0 10px; }
    .customer-expiry-notice ul { margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.9; }
    .customer-expiry-kicker { font-size: 11px; font-weight: 800; letter-spacing: .09em; }

    .customer-welcome {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        padding: 30px;
        border-radius: 20px;
        background: linear-gradient(135deg, #082f49, #075985 60%, #0284c7);
        color: #ffffff;
    }

    .customer-welcome-copy {
        min-width: 0;
        max-width: 680px;
    }

    .customer-eyebrow {
        color: #a5f3fc;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .customer-welcome h1 {
        margin: 10px 0;
        font-size: clamp(23px, 3vw, 29px);
        line-height: 1.3;
    }

    .customer-welcome p {
        color: #dbeafe;
        font-size: 12px;
        line-height: 1.8;
    }

    .customer-welcome-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        flex-shrink: 0;
    }

    .customer-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 41px;
        padding: 10px 15px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease;
    }

    .customer-btn:hover {
        transform: translateY(-1px);
    }

    .customer-btn-glass {
        background: rgba(255,255,255,.13);
        border-color: rgba(255,255,255,.25);
        color: #ffffff;
    }

    .customer-btn-aqua {
        background: #67e8f9;
        color: #083344;
    }

    .customer-btn-primary {
        background: #0284c7;
        color: #ffffff;
    }

    .customer-btn-primary:hover {
        background: #0369a1;
    }

    .customer-btn-outline {
        background: #ffffff;
        color: #0369a1;
        border-color: #cbd5e1;
    }

    .customer-full-btn {
        width: 100%;
        margin-top: 16px;
    }

    .customer-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .customer-stat {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
        padding: 19px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        text-decoration: none;
        transition: border-color .2s ease, transform .2s ease;
    }

    .customer-stat:hover {
        transform: translateY(-2px);
        border-color: #7dd3fc;
    }

    .customer-stat-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
    }

    .customer-stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-cyan { background: #cffafe; color: #0891b2; }
    .icon-purple { background: #f3e8ff; color: #9333ea; }
    .icon-orange { background: #ffedd5; color: #ea580c; }

    .customer-stat-content {
        min-width: 0;
    }

    .customer-stat-content span,
    .customer-stat-content small {
        display: block;
        color: #64748b;
        font-size: 10px;
        line-height: 1.5;
    }

    .customer-stat-content strong {
        display: block;
        margin: 4px 0;
        color: #082f49;
        font-size: 25px;
        font-weight: 800;
    }

    .customer-stat-content small {
        font-size: 9px;
    }

    .customer-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
        gap: 20px;
    }

    .customer-panel {
        min-width: 0;
        padding: 23px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
    }

    .customer-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .customer-section-label {
        color: #0284c7;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px;
    }

    .customer-panel-header h2 {
        margin: 5px 0;
        color: #082f49;
        font-size: 17px;
        font-weight: 800;
    }

    .customer-panel-header p {
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .customer-text-link {
        color: #0284c7;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
    }

    .customer-next-class {
        display: flex;
        gap: 17px;
        padding: 17px;
        border: 1px solid #bae6fd;
        border-radius: 14px;
        background: #f0f9ff;
    }

    .customer-date-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 75px;
        flex-shrink: 0;
        border-radius: 13px;
        background: #0284c7;
        color: #ffffff;
    }

    .customer-date-box strong {
        font-size: 24px;
    }

    .customer-date-box span {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .customer-next-details {
        flex: 1;
        min-width: 0;
    }

    .customer-next-heading,
    .customer-transaction-header,
    .customer-invoice-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .customer-next-heading h3 {
        color: #082f49;
        font-size: 14px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .customer-next-heading p {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
    }

    .customer-next-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 9px 15px;
        margin: 15px 0;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    .customer-next-meta span {
        overflow-wrap: anywhere;
    }

    .customer-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-success { background: #dcfce7; color: #15803d; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-danger { background: #fee2e2; color: #b91c1c; }
    .status-neutral { background: #f1f5f9; color: #64748b; }

    .customer-progress-top,
    .customer-person,
    .customer-participant,
    .customer-participant-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .customer-progress-top {
        justify-content: space-between;
        padding-bottom: 17px;
        border-bottom: 1px solid #e2e8f0;
    }

    .customer-person {
        min-width: 0;
    }

    .customer-person > div:last-child {
        min-width: 0;
    }

    .customer-person strong,
    .customer-person span {
        display: block;
        overflow-wrap: anywhere;
    }

    .customer-person strong {
        color: #082f49;
        font-size: 12px;
    }

    .customer-person span {
        margin-top: 4px;
        color: #64748b;
        font-size: 10px;
    }

    .customer-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        flex-shrink: 0;
        border-radius: 13px;
        background: #dbeafe;
        color: #0369a1;
        font-size: 15px;
        font-weight: 800;
    }

    .customer-score {
        flex-shrink: 0;
        text-align: right;
    }

    .customer-score strong {
        display: block;
        color: #0284c7;
        font-size: 23px;
    }

    .customer-score span {
        color: #64748b;
        font-size: 10px;
    }

    .customer-progress-level {
        padding: 15px 0;
    }

    .customer-progress-level small,
    .customer-progress-level strong {
        display: block;
    }

    .customer-progress-level small {
        color: #64748b;
        font-size: 10px;
    }

    .customer-progress-level strong {
        margin-top: 5px;
        color: #082f49;
        font-size: 13px;
    }

    .customer-skills {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .customer-skill-heading {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 7px;
        font-size: 10px;
    }

    .customer-skill-heading span { color: #64748b; }
    .customer-skill-heading strong { color: #082f49; }

    .customer-skill-track {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: #e2e8f0;
    }

    .customer-skill-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #0284c7, #22d3ee);
    }

    .customer-progress-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 19px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 10px;
    }

    .customer-progress-footer strong {
        color: #334155;
    }

    .customer-participant {
        justify-content: space-between;
        padding: 13px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fbfdff;
    }

    .customer-participant-actions {
        flex-shrink: 0;
    }

    .customer-session-count {
        text-align: right;
    }

    .customer-session-count strong,
    .customer-session-count span {
        display: block;
    }

    .customer-session-count strong {
        color: #082f49;
        font-size: 15px;
    }

    .customer-session-count span {
        color: #64748b;
        font-size: 9px;
    }

    .customer-edit-link {
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #0369a1;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
    }

    .customer-panel-footer {
        margin-top: 17px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }

    .customer-transaction-header small,
    .customer-invoice-heading small {
        display: block;
        color: #64748b;
        font-size: 10px;
    }

    .customer-transaction-header strong,
    .customer-invoice-heading strong {
        display: block;
        margin-top: 5px;
        color: #082f49;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .customer-transaction-product {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin: 17px 0;
        padding: 14px;
        border-radius: 11px;
        background: #f8fafc;
    }

    .customer-transaction-product strong {
        color: #082f49;
        font-size: 12px;
    }

    .customer-transaction-product span {
        color: #64748b;
        font-size: 10px;
    }

    .customer-transaction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-size: 11px;
    }

    .customer-transaction-row strong {
        color: #334155;
        text-align: right;
    }

    .customer-transaction-total {
        border-bottom: 0;
    }

    .customer-transaction-total strong {
        color: #082f49;
        font-size: 15px;
    }

    .customer-invoice {
        display: flex;
        gap: 18px;
        padding: 20px;
        border: 1px solid #bae6fd;
        border-radius: 14px;
        background: #f0f9ff;
    }

    .customer-invoice-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 13px;
        background: #dbeafe;
        color: #0284c7;
    }

    .customer-invoice-icon svg {
        width: 26px;
        height: 26px;
    }

    .customer-invoice-details {
        flex: 1;
        min-width: 0;
    }

    .customer-invoice-meta {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #dbeafe;
    }

    .customer-invoice-meta span,
    .customer-invoice-meta strong {
        display: block;
        overflow-wrap: anywhere;
    }

    .customer-invoice-meta span {
        color: #64748b;
        font-size: 10px;
    }

    .customer-invoice-meta strong {
        margin-top: 6px;
        color: #082f49;
        font-size: 12px;
    }

    .customer-invoice-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .customer-account-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .customer-account-grid > div {
        min-width: 0;
        padding: 13px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #fbfdff;
    }

    .customer-account-grid span,
    .customer-account-grid strong {
        display: block;
    }

    .customer-account-grid > div > span {
        color: #64748b;
        font-size: 10px;
    }

    .customer-account-grid strong {
        margin-top: 6px;
        color: #082f49;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .customer-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        padding: 25px 15px;
        text-align: center;
    }

    .customer-empty-symbol {
        margin-bottom: 12px;
        font-size: 29px;
    }

    .customer-empty strong {
        color: #082f49;
        font-size: 13px;
    }

    .customer-empty p {
        max-width: 330px;
        margin: 8px 0 16px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.7;
    }

    @media (max-width: 1200px) {
        .customer-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .customer-grid {
            grid-template-columns: 1fr;
        }

        .customer-account-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .customer-welcome {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
        }

        .customer-welcome-actions {
            width: 100%;
        }

        .customer-account-grid {
            grid-template-columns: 1fr;
        }

        .customer-invoice-meta {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .customer-stats {
            gap: 10px;
        }

        .customer-stat {
            flex-direction: column;
            align-items: flex-start;
            padding: 14px;
        }

        .customer-stat-icon {
            width: 40px;
            height: 40px;
        }

        .customer-stat-content strong {
            font-size: 22px;
        }

        .customer-panel {
            padding: 17px;
        }

        .customer-welcome-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .customer-next-class,
        .customer-invoice {
            flex-direction: column;
        }

        .customer-skills {
            grid-template-columns: 1fr;
        }

        .customer-participant {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .customer-participant-actions {
            width: 100%;
            justify-content: space-between;
        }

        .customer-invoice-actions {
            flex-direction: column;
        }

        .customer-invoice-actions .customer-btn {
            width: 100%;
        }
    }

    @media (max-width: 370px) {
        .customer-stats {
            grid-template-columns: 1fr;
        }

        .customer-stat {
            flex-direction: row;
            align-items: center;
        }

        .customer-welcome-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
