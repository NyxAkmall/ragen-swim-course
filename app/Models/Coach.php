<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    use HasFactory;

    protected $table = 'coaches';

    protected $fillable = [
        'user_id',
        'bio',
        'specialization',
        'experience_years',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name
            ?? 'Nama Pelatih Belum Tersedia';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => 'Tidak Diketahui',
        };
    }

    public function getExperienceLabelAttribute(): string
    {
        if ($this->experience_years === null) {
            return 'Belum Dicantumkan';
        }

        $years = max(
            0,
            (int) $this->experience_years
        );

        return $years . ' Tahun Pengalaman';
    }
}
