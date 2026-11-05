@extends('layouts.admin')

@section('content')
<div style="max-width:950px;margin:auto;padding:24px 12px">
    <h1 style="font-size:26px;margin-bottom:8px">Tambah Jadwal Mingguan</h1>
    <p style="color:#64748b;margin-bottom:25px">Buat template kelas yang berlangsung setiap minggu.</p>
    <div style="background:white;padding:25px;border:1px solid #e2e8f0;border-radius:14px">
        @include('admin.weekly-schedules.form')
    </div>
</div>
@endsection
