<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Nama tabel database.
     */
    protected $table = 'invoices';


    /**
     * Kolom yang boleh diisi menggunakan mass assignment.
     */
    protected $fillable = [
        'order_id',
        'invoice_number',
        'issued_at',
        'due_at',
        'status',
    ];


    /**
     * Konversi tipe data otomatis.
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }


    /**
     * Relasi Invoice -> Order.
     *
     * Data nominal invoice seperti subtotal, diskon,
     * dan total pembayaran berasal dari order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


    /**
     * Mengecek apakah invoice masih belum dibayar.
     */
    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }


    /**
     * Mengecek apakah invoice sudah dibayar.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }


    /**
     * Mengecek apakah invoice dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }


    /**
     * Mengecek apakah invoice sudah direfund.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }


    /**
     * Mengecek apakah invoice melewati batas pembayaran.
     */
    public function isPastDue(): bool
    {
        if ($this->due_at === null) {
            return false;
        }

        return $this->due_at->isPast()
            && !$this->isPaid();
    }


    /**
     * Label status invoice dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }


    /**
     * Format subtotal.
     *
     * Nilai diambil dari order karena tabel invoices
     * tidak menyimpan subtotal secara terpisah.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) ($this->order?->subtotal ?? 0),
            0,
            ',',
            '.'
        );
    }


    /**
     * Format diskon.
     *
     * Nilai diambil dari order.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) ($this->order?->discount_amount ?? 0),
            0,
            ',',
            '.'
        );
    }


    /**
     * Format total pembayaran.
     *
     * Nilai diambil dari order.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) ($this->order?->total_amount ?? 0),
            0,
            ',',
            '.'
        );
    }


    /**
     * Format waktu invoice diterbitkan.
     */
    public function getFormattedIssuedAtAttribute(): string
    {
        if ($this->issued_at === null) {
            return '-';
        }

        return $this->issued_at->format('d-m-Y H:i');
    }


    /**
     * Format batas waktu pembayaran.
     */
    public function getFormattedDueAtAttribute(): string
    {
        if ($this->due_at === null) {
            return '-';
        }

        return $this->due_at->format('d-m-Y H:i');
    }
}
