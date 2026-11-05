<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipantPackage extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'participant_packages';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'participant_id',
        'package_id',
        'order_item_id',
        'total_sessions',
        'used_sessions',
        'remaining_sessions',
        'start_date',
        'expiry_date',
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
            'total_sessions' => 'integer',
            'used_sessions' => 'integer',
            'remaining_sessions' => 'integer',
            'start_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Peserta pemilik paket.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Jenis paket yang dibeli peserta.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Item transaksi yang menghasilkan paket peserta.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Seluruh booking yang menggunakan paket ini.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Paket
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah paket berstatus aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Mengecek apakah paket masih memiliki sisa sesi.
     */
    public function hasRemainingSessions(): bool
    {
        return (int) $this->remaining_sessions > 0;
    }

    /**
     * Mengecek apakah masa berlaku paket sudah berakhir.
     *
     * Paket masih berlaku sepanjang tanggal expiry_date.
     * Contoh: jika expiry_date adalah 10 Oktober,
     * paket baru dianggap kedaluwarsa pada 11 Oktober.
     *
     * Jika expiry_date kosong, paket dianggap tidak
     * memiliki batas tanggal kedaluwarsa.
     */
    public function isExpired(): bool
    {
        if ($this->expiry_date === null) {
            return false;
        }

        return $this->expiry_date->isBefore(today());
    }

    /**
     * Mengecek apakah paket sudah mencapai tanggal mulai.
     *
     * Jika start_date kosong, tidak ada pembatasan
     * berdasarkan tanggal mulai.
     */
    public function hasStarted(): bool
    {
        if ($this->start_date === null) {
            return true;
        }

        return $this->start_date->isSameDay(today())
            || $this->start_date->isBefore(today());
    }

    /**
     * Mengecek apakah paket dapat digunakan untuk booking.
     *
     * Paket harus:
     * - Berstatus aktif.
     * - Sudah mencapai tanggal mulai.
     * - Memiliki sisa sesi.
     * - Belum kedaluwarsa.
     */
    public function canBeUsed(): bool
    {
        return $this->isActive()
            && $this->hasStarted()
            && $this->hasRemainingSessions()
            && !$this->isExpired();
    }

    /**
     * Mengecek apakah seluruh sesi sudah digunakan
     * atau status paket sudah completed.
     */
    public function isCompleted(): bool
    {
        return (int) $this->remaining_sessions <= 0
            || $this->status === 'completed';
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Penggunaan Paket
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung persentase sesi yang sudah digunakan.
     *
     * Nilai dibatasi antara 0 sampai 100 persen.
     */
    public function getUsagePercentageAttribute(): float
    {
        $totalSessions = (int) $this->total_sessions;
        $usedSessions = (int) $this->used_sessions;

        if ($totalSessions <= 0) {
            return 0.0;
        }

        $percentage = round(
            ($usedSessions / $totalSessions) * 100,
            2
        );

        return (float) max(
            0,
            min(100, $percentage)
        );
    }
}
