<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'program_id',
        'name',
        'phone',
        'email',
        'source',
        'status',
        'follow_up_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
        ];
    }

    /**
     * Program yang diminati calon pelanggan.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Pemeriksaan status calon pelanggan.
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isContacted(): bool
    {
        return $this->status === 'contacted';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function isLost(): bool
    {
        return $this->status === 'not_interested';
    }

    public function isNotInterested(): bool
    {
        return $this->status === 'not_interested';
    }

    /**
     * Calon pelanggan masih membutuhkan tindak lanjut.
     */
    public function needsFollowUp(): bool
    {
        return in_array(
            $this->status,
            ['new', 'contacted', 'follow_up'],
            true
        );
    }

    /**
     * Jadwal tindak lanjut sudah tiba.
     */
    public function isFollowUpDue(): bool
    {
        if (
            $this->follow_up_at === null
            || !$this->needsFollowUp()
        ) {
            return false;
        }

        return $this->follow_up_at->lessThanOrEqualTo(now());
    }

    /**
     * Pemeriksaan kelengkapan kontak.
     */
    public function hasPhone(): bool
    {
        return !empty($this->phone);
    }

    public function hasEmail(): bool
    {
        return !empty($this->email);
    }

    /**
     * Label status calon pelanggan.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Lead Baru',
            'contacted' => 'Sudah Dihubungi',
            'follow_up' => 'Perlu Tindak Lanjut',
            'converted' => 'Menjadi Pelanggan',
            'not_interested' => 'Tidak Berminat',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Label sumber calon pelanggan.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'website' => 'Website',
            'whatsapp' => 'WhatsApp',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'referral' => 'Referensi',
            'other' => 'Lainnya',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Nama program yang diminati.
     */
    public function getProgramNameAttribute(): string
    {
        return $this->program?->name
            ?? 'Belum Memilih Program';
    }

    /**
     * Jadwal tindak lanjut dalam format Indonesia.
     */
    public function getFollowUpLabelAttribute(): string
    {
        if ($this->follow_up_at === null) {
            return 'Belum Dijadwalkan';
        }

        return $this->follow_up_at->format('d/m/Y H:i');
    }
}
