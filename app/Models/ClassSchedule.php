<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'class_schedules';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        "weekly_schedule_id",
        'program_id',
        'coach_id',
        'location_id',
        'title',
        'swimming_level',
        'class_date',
        'start_time',
        'end_time',
        'capacity',
        'status',
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'class_date' => 'date',
            'capacity' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Program yang memiliki jadwal kelas.
     */
    public function weeklySchedule(): BelongsTo
    {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Pelatih yang bertanggung jawab atas kelas.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /**
     * Lokasi pelaksanaan kelas.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Seluruh booking pada jadwal kelas.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Daftar tunggu ketika kelas penuh.
     */
    public function waitingLists(): HasMany
    {
        return $this->hasMany(WaitingList::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Kapasitas Kelas
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung booking yang masih memegang slot.
     *
     * Hanya status pending dan confirmed yang dihitung.
     */
    public function getBookedCountAttribute(): int
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    /**
     * Menghitung jumlah slot yang masih tersedia.
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(
            0,
            (int) $this->capacity - $this->booked_count
        );
    }

    /**
     * Mengecek apakah seluruh slot sudah terisi.
     */
    public function isFull(): bool
    {
        return $this->remaining_slots <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Jadwal
    |--------------------------------------------------------------------------
    */

    /**
     * Jadwal masih berstatus scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Kelas sudah selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Kelas dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Waktu Kelas
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah waktu mulai kelas sudah lewat.
     *
     * Tanggal dan jam mengikuti timezone aplikasi Laravel.
     */
    public function hasStarted(): bool
    {
        if (
            $this->class_date === null
            || $this->start_time === null
        ) {
            return false;
        }

        $startTime = Carbon::parse(
            $this->class_date->format('Y-m-d')
            . ' '
            . $this->start_time,
            config('app.timezone')
        );

        return $startTime->lessThanOrEqualTo(now());
    }

    /*
    |--------------------------------------------------------------------------
    | Ketersediaan Booking
    |--------------------------------------------------------------------------
    */

    /**
     * Jadwal dapat dipesan jika:
     * - Status masih scheduled.
     * - Tanggal dan jam mulai tersedia.
     * - Kelas belum dimulai.
     * - Masih memiliki slot.
     *
     * Pemeriksaan bentrok jadwal peserta,
     * kesesuaian paket, dan transaksi booking
     * tetap dilakukan pada service/controller.
     */
    public function canBeBooked(): bool
    {
        return $this->isScheduled()
            && $this->class_date !== null
            && $this->start_time !== null
            && !$this->hasStarted()
            && !$this->isFull();
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Label status untuk Customer, Coach, dan Admin.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Terjadwal',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(
                str_replace('_', ' ', (string) $this->status)
            ),
        };
    }
}
