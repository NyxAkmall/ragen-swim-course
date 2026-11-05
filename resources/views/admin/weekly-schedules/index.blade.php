@extends('layouts.admin')

@section('content')
@php
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
@endphp

<div style="max-width:1300px;margin:auto;padding:24px 12px">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
        <div>
            <h1 style="font-size:26px;font-weight:700;margin:0">Jadwal Mingguan</h1>
            <p style="color:#64748b;margin-top:6px">Kelola template kelas yang berulang setiap minggu.</p>
        </div>
        <a href="{{ route('admin.weekly-schedules.create') }}" style="background:#2563eb;color:white;padding:12px 18px;border-radius:10px;text-decoration:none">
            + Tambah Jadwal Mingguan
        </a>
    </div>

    @if(session('success'))
        <div style="padding:14px;background:#dcfce7;color:#166534;border-radius:10px;margin-bottom:18px">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px">
        <div style="padding:16px;background:#eff6ff;border-radius:12px;min-width:140px">Total Template<br><strong>{{ $statistics['total'] }}</strong></div>
        <div style="padding:16px;background:#f0fdf4;border-radius:12px;min-width:140px">Aktif<br><strong>{{ $statistics['active'] }}</strong></div>
        <div style="padding:16px;background:#f8fafc;border-radius:12px;min-width:140px">Nonaktif<br><strong>{{ $statistics['inactive'] }}</strong></div>
    </div>

    <form method="GET" action="{{ route('admin.weekly-schedules.index') }}" style="display:flex;gap:10px;margin-bottom:18px">
        <input name="search" value="{{ $search }}" placeholder="Cari program, coach, lokasi, judul..." style="padding:11px;border:1px solid #cbd5e1;border-radius:8px;flex:1;min-width:0">
        <button type="submit" style="padding:11px 18px;background:#334155;color:white;border:0;border-radius:8px">Cari</button>
    </form>

    <div style="overflow-x:auto;background:white;border:1px solid #e2e8f0;border-radius:12px">
        <table style="width:100%;border-collapse:collapse;min-width:1000px">
            <thead style="background:#f8fafc">
                <tr>
                    @foreach(['Hari / Jam','Kelas','Program','Coach','Lokasi','Kapasitas','Kelas Terbuat','Status','Aksi'] as $heading)
                        <th style="text-align:left;padding:14px;border-bottom:1px solid #e2e8f0">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">
                            <strong>{{ $days[$schedule->day_of_week] ?? '-' }}</strong><br>
                            {{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }}
                        </td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->title }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->program?->name ?? '-' }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->coach?->user?->name ?? '-' }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->location?->name ?? '-' }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->capacity }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->class_schedules_count }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">{{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td style="padding:14px;border-bottom:1px solid #e2e8f0">
                            <a href="{{ route('admin.weekly-schedules.edit', $schedule) }}">Edit</a>
                            <form action="{{ route('admin.weekly-schedules.status.update', $schedule) }}" method="POST" style="margin-top:8px">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('Ubah status template ini?')" style="cursor:pointer">
                                    {{ $schedule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form
                                action="{{ route('admin.weekly-schedules.destroy', $schedule) }}"
                                method="POST"
                                style="margin-top:8px"
                                onsubmit="return confirm('Yakin ingin menghapus template mingguan ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    style="color:#dc2626;border:1px solid #fecaca;background:#fff;padding:7px 12px;border-radius:7px;cursor:pointer"
                                >
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:28px;text-align:center;color:#64748b">Belum ada template jadwal mingguan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">{{ $schedules->links() }}</div>
</div>
@endsection
