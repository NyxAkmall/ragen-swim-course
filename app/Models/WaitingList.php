<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_lists';

    protected $fillable = [
        'participant_id',
        'class_schedule_id',
        'status',
        'queue_number',
        'offered_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'queue_number' => 'integer',
            'offered_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Peserta yang masuk daftar tunggu.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Jadwal kelas yang sedang ditunggu.
     */
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    /**
     * Peserta masih berada dalam antrean.
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Slot sudah ditawarkan kepada peserta.
     *
     * Nama metode lama dipertahankan.
     */
    public function isNotified(): bool
    {
        return $this->status === 'offered';
    }

    /**
     * Peserta telah menerima penawaran slot.
     *
     * Nama metode lama dipertahankan.
     */
    public function isConverted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Peserta membatalkan daftar tunggu.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Penawaran slot telah kedaluwarsa.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return now()->greaterThanOrEqualTo(
            $this->expires_at
        );
    }

    /**
     * Daftar tunggu masih aktif apabila
     * menunggu atau sedang mendapat penawaran
     * dan belum kedaluwarsa.
     */
    public function isActive(): bool
    {
        return in_array(
            $this->status,
            ['waiting', 'offered'],
            true
        ) && !$this->isExpired();
    }

    /**
     * Peserta dapat menerima penawaran slot
     * jika statusnya offered dan belum kedaluwarsa.
     *
     * Pemeriksaan kapasitas kelas dan paket
     * tetap dilakukan di service/controller.
     */
    public function canClaimSlot(): bool
    {
        return $this->isNotified()
            && !$this->isExpired();
    }

    /**
     * Peserta dapat membatalkan antrean
     * selama statusnya masih aktif.
     */
    public function canBeCancelled(): bool
    {
        return $this->isActive();
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        // Status final harus tetap terbaca meskipun tenggat tawaran berlalu.
        if ($this->status === 'accepted') {
            return 'Penawaran Diterima';
        }

        if ($this->status === 'cancelled') {
            return 'Dibatalkan';
        }

        if ($this->isExpired()) {
            return 'Kedaluwarsa';
        }

        return match ($this->status) {
            'waiting' => 'Dalam Antrean',
            'offered' => 'Slot Ditawarkan',
            'accepted' => 'Penawaran Diterima',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }
}
