<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_type',
        'reference_id',
        'is_read',
        'read_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'reference_id' => 'integer',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->is_read === true;
    }

    public function isUnread(): bool
    {
        return !$this->isRead();
    }

    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return true;
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function hasReference(): bool
    {
        return !empty($this->reference_type)
            && $this->reference_id !== null
            && $this->reference_id > 0;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'booking' => 'Booking',
            'payment' => 'Pembayaran',
            'schedule' => 'Jadwal',
            'progress' => 'Perkembangan',
            'promo' => 'Promo',
            'system' => 'Sistem',
            default => 'Informasi',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->isRead()
            ? 'Sudah Dibaca'
            : 'Belum Dibaca';
    }

    public function getReferenceLabelAttribute(): string
    {
        if (!$this->hasReference()) {
            return 'Tidak Ada Referensi';
        }

        return match ($this->reference_type) {
            'booking' => 'Booking',
            'order' => 'Pesanan',
            'payment' => 'Pembayaran',
            'invoice' => 'Invoice',
            'class_schedule' => 'Jadwal Kelas',
            'participant' => 'Peserta',
            'progress_record' => 'Laporan Perkembangan',
            'assessment' => 'Assessment',
            default => 'Referensi Lainnya',
        };
    }
}
