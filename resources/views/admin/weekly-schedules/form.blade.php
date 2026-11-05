@php
    $editing = isset($weeklySchedule);
    $selected = fn($field, $default = '') => old($field, $editing ? $weeklySchedule->{$field} : $default);
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    $levels = ['all' => 'Semua Level', 'beginner' => 'Beginner', 'basic' => 'Basic', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'];
@endphp

<form method="POST" action="{{ $editing ? route('admin.weekly-schedules.update', $weeklySchedule) : route('admin.weekly-schedules.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    @if($errors->any())
        <div style="background:#fef2f2;color:#991b1b;padding:15px;border-radius:10px;margin-bottom:20px">
            <strong>Periksa kembali data:</strong>
            <ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="weekly-form-grid">
        <label>Judul Kelas
            <input name="title" required maxlength="150" value="{{ $selected('title') }}">
        </label>

        <label>Program
            <select name="program_id" required>
                <option value="">Pilih program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" @selected((string)$selected('program_id') === (string)$program->id)>{{ $program->name }}</option>
                @endforeach
            </select>
        </label>

        <label>Coach
            <select name="coach_id" required>
                <option value="">Pilih coach</option>
                @foreach($coaches as $coach)
                    <option value="{{ $coach->id }}" @selected((string)$selected('coach_id') === (string)$coach->id)>{{ $coach->user?->name ?? 'Coach #'.$coach->id }}</option>
                @endforeach
            </select>
        </label>

        <label>Lokasi
            <select name="location_id" required>
                <option value="">Pilih lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected((string)$selected('location_id') === (string)$location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </label>

        <label>Hari
            <select name="day_of_week" required>
                <option value="">Pilih hari</option>
                @foreach($days as $number => $day)
                    <option value="{{ $number }}" @selected((string)$selected('day_of_week') === (string)$number)>{{ $day }}</option>
                @endforeach
            </select>
        </label>

        <label>Level
            <select name="swimming_level" required>
                @foreach($levels as $value => $label)
                    <option value="{{ $value }}" @selected($selected('swimming_level','all') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>Jam Mulai
            <input type="time" name="start_time" required value="{{ substr((string)$selected('start_time'),0,5) }}">
        </label>

        <label>Jam Selesai
            <input type="time" name="end_time" required value="{{ substr((string)$selected('end_time'),0,5) }}">
        </label>

        <label>Kapasitas Peserta
            <input type="number" name="capacity" min="1" max="1000" required value="{{ $selected('capacity', 5) }}">
        </label>

        <label class="weekly-full">Catatan (Opsional)
            <textarea name="notes" rows="4" maxlength="5000">{{ $selected('notes') }}</textarea>
        </label>
    </div>

    <p style="color:#64748b;font-size:14px;margin-top:20px">
        Template digunakan untuk menghasilkan jadwal kelas berdasarkan tanggal.
        Mengubah template tidak otomatis mengubah kelas yang sudah dibuat.
    </p>

    <div style="display:flex;gap:12px;margin-top:20px">
        <button type="submit" style="background:#2563eb;color:white;padding:12px 22px;border:0;border-radius:9px;cursor:pointer">
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Jadwal Mingguan' }}
        </button>
        <a href="{{ route('admin.weekly-schedules.index') }}" style="padding:12px 18px;color:#334155">Batal</a>
    </div>
</form>

<style>
.weekly-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.weekly-form-grid label{display:flex;flex-direction:column;gap:7px;font-weight:600;color:#334155}
.weekly-form-grid input,.weekly-form-grid select,.weekly-form-grid textarea{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:9px;background:white;font:inherit;box-sizing:border-box}
.weekly-full{grid-column:1/-1}
@media(max-width:700px){.weekly-form-grid{grid-template-columns:1fr}}
</style>
