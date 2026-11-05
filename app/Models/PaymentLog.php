<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use HasFactory;

    protected $table = 'payment_logs';

    // Database memiliki created_at, tetapi tidak memiliki updated_at.
    const UPDATED_AT = null;

    protected $fillable = [
        'payment_id',
        'old_status',
        'new_status',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Setiap riwayat perubahan status dimiliki oleh satu pembayaran.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Pemeriksaan status pembayaran terbaru.
     */
    public function isPaid(): bool
    {
        return $this->new_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->new_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->new_status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->new_status === 'expired';
    }

    public function isRefunded(): bool
    {
        return $this->new_status === 'refunded';
    }

    /**
     * Label status pembayaran dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->new_status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Pembayaran Berhasil',
            'failed' => 'Pembayaran Gagal',
            'expired' => 'Pembayaran Kedaluwarsa',
            'refunded' => 'Dana Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Memeriksa apakah respons payment gateway tersedia.
     */
    public function hasGatewayResponse(): bool
    {
        return !empty($this->gateway_response);
    }

    /**
     * Mempertahankan nama metode lama untuk kompatibilitas.
     */
    public function hasPayload(): bool
    {
        return $this->hasGatewayResponse();
    }
}
