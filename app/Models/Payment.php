<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_code',
        'payment_method',
        'payment_channel',
        'amount',
        'status',
        'gateway_reference',
        'proof_image',
        'proof_uploaded_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'proof_uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Method
    |--------------------------------------------------------------------------
    */

    public function isManualTransfer(): bool
    {
        return $this->payment_method === 'bank_transfer'
            && $this->payment_channel === 'manual_transfer';
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Status
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

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Kompatibilitas dengan kode lama.
     *
     * Database tidak mempunyai status "processing",
     * sehingga method ini selalu false.
     */
    public function isProcessing(): bool
    {
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Proof Payment
    |--------------------------------------------------------------------------
    */

    public function hasProof(): bool
    {
        return !empty($this->proof_image);
    }

    public function hasRejection(): bool
    {
        return !empty($this->rejection_reason);
    }

    /**
     * Bukti transfer telah ditolak Admin,
     * tetapi payment tetap pending agar customer
     * dapat mengunggah ulang bukti.
     */
    public function isRejected(): bool
    {
        return $this->isManualTransfer()
            && $this->isPending()
            && $this->hasRejection();
    }

    /**
     * Bukti transfer sudah dikirim dan
     * sedang menunggu pemeriksaan Admin.
     */
    public function isWaitingVerification(): bool
    {
        return $this->isManualTransfer()
            && $this->isPending()
            && $this->hasProof()
            && $this->verified_at === null
            && !$this->hasRejection();
    }

    /**
     * Transfer manual sudah disiapkan,
     * tetapi customer belum mengunggah bukti.
     */
    public function isWaitingForProof(): bool
    {
        return $this->isManualTransfer()
            && $this->isPending()
            && !$this->hasProof()
            && !$this->hasRejection();
    }

    /**
     * Pembayaran manual telah diverifikasi Admin.
     */
    public function isVerified(): bool
    {
        return $this->isPaid()
            && $this->verified_at !== null
            && $this->verified_by !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Formatted Attributes
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->amount,
            0,
            ',',
            '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Display Information
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        if ($this->isRejected()) {
            return 'Bukti Ditolak - Upload Ulang';
        }

        if ($this->isWaitingVerification()) {
            return 'Menunggu Verifikasi Admin';
        }

        if ($this->isWaitingForProof()) {
            return 'Menunggu Bukti Pembayaran';
        }

        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Berhasil Dibayar',
            'failed' => 'Pembayaran Gagal',
            'expired' => 'Pembayaran Kedaluwarsa',
            'refunded' => 'Dana Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }

    public function getPaymentDisplayAttribute(): string
    {
        if ($this->isManualTransfer()) {
            return 'Transfer Bank Manual';
        }

        return match ($this->payment_method) {
            'bank_transfer' => 'Transfer Bank',

            default => ucfirst(
                $this->payment_method
                    ?? 'Belum Dipilih'
            ),
        };
    }
}
