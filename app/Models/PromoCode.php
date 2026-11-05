<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    /**
     * Nama tabel database.
     */
    protected $table = 'promo_codes';

    /**
     * Kolom yang boleh diisi menggunakan mass assignment.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_purchase' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Relasi Promo Code -> Orders.
     *
     * Satu kode promo dapat digunakan
     * pada beberapa transaksi.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mengecek apakah promo aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Mengecek apakah promo sudah dimulai.
     */
    public function hasStarted(): bool
    {
        if ($this->start_date === null) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->start_date);
    }

    /**
     * Mengecek apakah promo sudah berakhir.
     */
    public function hasEnded(): bool
    {
        if ($this->end_date === null) {
            return false;
        }

        return now()->greaterThan($this->end_date);
    }

    /**
     * Mengecek apakah batas penggunaan
     * promo sudah tercapai.
     */
    public function hasReachedUsageLimit(): bool
    {
        if ($this->usage_limit === null) {
            return false;
        }

        return $this->used_count >= $this->usage_limit;
    }

    /**
     * Mengecek apakah promo masih dapat digunakan.
     */
    public function canBeUsed(): bool
    {
        return $this->isActive()
            && $this->hasStarted()
            && !$this->hasEnded()
            && !$this->hasReachedUsageLimit();
    }

    /**
     * Mengecek apakah nominal transaksi
     * memenuhi minimum pembelian.
     */
    public function meetsMinimumPurchase(float $subtotal): bool
    {
        if ($this->minimum_purchase === null) {
            return true;
        }

        return $subtotal >= (float) $this->minimum_purchase;
    }

    /**
     * Menghitung jumlah diskon berdasarkan subtotal.
     *
     * Mendukung:
     * - percentage = diskon persen
     * - fixed = potongan nominal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->canBeUsed()) {
            return 0;
        }

        if (!$this->meetsMinimumPurchase($subtotal)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $subtotal
                * ((float) $this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discount = (float) $this->discount_value;
        } else {
            return 0;
        }

        /**
         * Terapkan batas maksimal diskon
         * jika maximum_discount tersedia.
         */
        if (
            $this->maximum_discount !== null
            && $discount > (float) $this->maximum_discount
        ) {
            $discount = (float) $this->maximum_discount;
        }

        /**
         * Diskon tidak boleh lebih besar
         * daripada subtotal transaksi.
         */
        return round(
            min($discount, $subtotal),
            2
        );
    }

    /**
     * Format minimum pembelian menjadi Rupiah.
     */
    public function getFormattedMinimumPurchaseAttribute(): string
    {
        if ($this->minimum_purchase === null) {
            return 'Tanpa Minimum Pembelian';
        }

        return 'Rp ' . number_format(
            (float) $this->minimum_purchase,
            0,
            ',',
            '.'
        );
    }

    /**
     * Format maksimal diskon menjadi Rupiah.
     */
    public function getFormattedMaximumDiscountAttribute(): string
    {
        if ($this->maximum_discount === null) {
            return 'Tanpa Batas Maksimal';
        }

        return 'Rp ' . number_format(
            (float) $this->maximum_discount,
            0,
            ',',
            '.'
        );
    }

    /**
     * Mendapatkan tampilan nilai promo.
     *
     * Contoh:
     * 10%
     * atau
     * Rp 50.000
     */
    public function getDiscountDisplayAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return number_format(
                (float) $this->discount_value,
                0,
                ',',
                '.'
            ) . '%';
        }

        if ($this->discount_type === 'fixed') {
            return 'Rp ' . number_format(
                (float) $this->discount_value,
                0,
                ',',
                '.'
            );
        }

        return '-';
    }
}
