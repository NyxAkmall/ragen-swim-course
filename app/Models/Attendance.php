<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    /**
     * Nama tabel menggunakan bentuk tunggal,
     * yaitu attendance, bukan attendances.
     */
    protected $table = 'attendance';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'booking_id',
        'status',
        'notes',
        'checked_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Setiap data kehadiran terhubung
     * dengan satu booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Kehadiran
    |--------------------------------------------------------------------------
    */

    /**
     * Peserta hadir mengikuti kelas.
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Peserta tidak hadir karena izin.
     */
    public function isPermission(): bool
    {
        return $this->status === 'permission';
    }

    /**
     * Peserta tidak hadir karena sakit.
     */
    public function isSick(): bool
    {
        return $this->status === 'sick';
    }

    /**
     * Peserta tidak hadir tanpa keterangan.
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Pencatatan Kehadiran
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah kehadiran sudah dicatat.
     *
     * Kehadiran dianggap sudah dicatat jika:
     * - Status kehadiran valid.
     * - Waktu pencatatan tersedia.
     *
     * Fungsi ini tidak mengubah data kehadiran.
     */
    public function isChecked(): bool
    {
        return in_array(
            $this->status,
            ['present', 'permission', 'sick', 'absent'],
            true
        ) && $this->checked_at !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan label status kehadiran
     * dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'Hadir',
            'permission' => 'Izin',
            'sick' => 'Sakit',
            'absent' => 'Tidak Hadir',
            default => 'Tidak Diketahui',
        };
    }
}
