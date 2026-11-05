<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'packages';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'program_id',
        'name',
        'description',
        'total_sessions',
        'price',
        'promo_enabled',
        'promo_name',
        'promo_price',
        'promo_start_date',
        'promo_end_date',
        'validity_days',
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
            'price' => 'decimal:2',
            'promo_enabled' => 'boolean',
            'promo_price' => 'decimal:2',
            'promo_start_date' => 'date',
            'promo_end_date' => 'date',
            'validity_days' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Program yang memiliki paket ini.
     *
     * Contoh:
     * Program: Kelas Private
     * Paket: Private 8 Pertemuan
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Seluruh paket yang telah dibeli peserta.
     */
    public function participantPackages(): HasMany
    {
        return $this->hasMany(ParticipantPackage::class);
    }

    /**
     * Riwayat item transaksi pembelian paket.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah paket masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Harga
    |--------------------------------------------------------------------------
    */

    /**
     * Mengubah harga paket menjadi format Rupiah.
     *
     * Contoh:
     * 800000 menjadi Rp 800.000
     */
    /**
     * Memeriksa apakah promo paket sedang berlaku.
     */
    public function hasActivePromo(): bool
    {
        if (!$this->promo_enabled || $this->promo_price === null) {
            return false;
        }

        $normalPrice = (float) $this->price;
        $promoPrice = (float) $this->promo_price;

        if ($promoPrice < 0 || $promoPrice >= $normalPrice) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->promo_start_date !== null
            && $today < $this->promo_start_date->toDateString()) {
            return false;
        }

        if ($this->promo_end_date !== null
            && $today > $this->promo_end_date->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Harga paket yang berlaku saat ini.
     */
    public function getEffectivePrice(): float
    {
        return $this->hasActivePromo()
            ? (float) $this->promo_price
            : (float) $this->price;
    }

        public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->price,
            0,
            ',',
            '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan status paket dalam Bahasa Indonesia.
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
     * Menampilkan jumlah sesi atau pertemuan paket.
     *
     * Contoh:
     * 4 menjadi 4 Pertemuan
     * 8 menjadi 8 Pertemuan
     */
    public function getSessionsLabelAttribute(): string
    {
        if (
            $this->total_sessions === null
            || $this->total_sessions <= 0
        ) {
            return 'Belum Ditentukan';
        }

        return $this->total_sessions . ' Pertemuan';
    }

    /**
     * Menampilkan masa berlaku paket.
     *
     * Contoh:
     * 30 menjadi 30 Hari
     * 60 menjadi 60 Hari
     *
     * Jika masa berlaku belum diisi,
     * tampilkan keterangan pengganti.
     */
    public function getValidityLabelAttribute(): string
    {
        if (
            $this->validity_days === null
            || $this->validity_days <= 0
        ) {
            return 'Belum Ditentukan';
        }

        return $this->validity_days . ' Hari';
    }
}
