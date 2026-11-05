<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'promo_code_id',
        'promo_reserved_until',
        'order_code',
        'subtotal',
        'discount_amount',
        'registration_fee',
        'total_amount',
        'status',
        'notes',
        'ordered_at',
        'paid_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
            'paid_at' => 'datetime',
            'promo_reserved_until' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Customer yang membuat pesanan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kode promo yang digunakan pada pesanan.
     */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    /**
     * Seluruh item pembelian dalam pesanan.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Seluruh catatan pembayaran pesanan.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Invoice yang terhubung dengan pesanan.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Reservasi Promo
    |--------------------------------------------------------------------------
    */

    /**
     * Memeriksa apakah pesanan memiliki
     * batas waktu reservasi promo.
     */
    public function hasPromoReservation(): bool
    {
        return $this->promo_code_id !== null
            && $this->promo_reserved_until !== null;
    }

    /**
     * Memeriksa apakah reservasi promo
     * masih aktif dan belum kedaluwarsa.
     *
     * Hanya order pending yang dianggap
     * menahan kuota promo sementara.
     */
    public function hasActivePromoReservation(): bool
    {
        return $this->isPending()
            && $this->hasPromoReservation()
            && $this->promo_reserved_until->isFuture();
    }

    /**
     * Memeriksa apakah batas waktu
     * reservasi promo sudah terlewati.
     */
    public function isPromoReservationExpired(): bool
    {
        return $this->hasPromoReservation()
            && $this->promo_reserved_until->isPast();
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Data Terkait
    |--------------------------------------------------------------------------
    */

    public function hasPayments(): bool
    {
        if ($this->relationLoaded('payments')) {
            return $this->payments->isNotEmpty();
        }

        return $this->payments()->exists();
    }

    public function hasInvoice(): bool
    {
        if ($this->relationLoaded('invoice')) {
            return $this->invoice !== null;
        }

        return $this->invoice()->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Kompatibilitas Atribut Total
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute(): string
    {
        return (string) ($this->total_amount ?? '0.00');
    }

    /*
    |--------------------------------------------------------------------------
    | Format Rupiah
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->subtotal,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->discount_amount,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedRegistrationFeeAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->registration_fee,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->total_amount,
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            'refunded' => 'Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }
}
