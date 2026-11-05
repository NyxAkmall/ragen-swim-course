<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = [
        'payment_id',
        'refund_code',
        'amount',
        'reason',
        'status',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke transaksi pembayaran.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Refund masih menunggu proses.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Dipertahankan untuk kompatibilitas kode lama.
     * Database belum memiliki status processing.
     */
    public function isProcessing(): bool
    {
        return false;
    }

    /**
     * Refund berhasil diproses.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'processed';
    }

    /**
     * Refund gagal.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Dipertahankan untuk kompatibilitas kode lama.
     * Database belum memiliki status rejected.
     */
    public function isRejected(): bool
    {
        return false;
    }

    /**
     * Refund sudah memiliki hasil akhir.
     */
    public function isFinished(): bool
    {
        return in_array(
            $this->status,
            ['processed', 'failed'],
            true
        );
    }

    /**
     * Format nominal refund menjadi Rupiah.
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

    /**
     * Label status refund dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Proses',
            'processed' => 'Refund Berhasil',
            'failed' => 'Refund Gagal',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Dipertahankan untuk kompatibilitas kode lama.
     * Tabel refunds tidak memiliki gateway_reference.
     */
    public function hasGatewayReference(): bool
    {
        return false;
    }
}
