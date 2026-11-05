<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'programs';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'image',
        'duration_minutes',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Seluruh paket yang tersedia dalam program.
     *
     * Contoh:
     * - Private 4 Pertemuan
     * - Private 8 Pertemuan
     * - Private 12 Pertemuan
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Seluruh jadwal kelas untuk program ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Calon pelanggan yang tertarik
     * dengan program ini.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah program masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan status program
     * dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Menampilkan durasi kelas
     * dalam format yang mudah dibaca.
     *
     * Contoh:
     * 45 menit  -> 45 Menit
     * 60 menit  -> 1 Jam
     * 90 menit  -> 1 Jam 30 Menit
     * 120 menit -> 2 Jam
     */
    public function getDurationLabelAttribute(): string
    {
        if (
            $this->duration_minutes === null
            || $this->duration_minutes <= 0
        ) {
            return 'Belum Ditentukan';
        }

        $minutes = (int) $this->duration_minutes;

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return $minutes . ' Menit';
        }

        if ($remainingMinutes === 0) {
            return $hours . ' Jam';
        }

        return $hours . ' Jam ' . $remainingMinutes . ' Menit';
    }
}
