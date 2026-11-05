<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessments';

    protected $fillable = [
        'participant_id',
        'coach_id',
        'assessment_date',
        'water_confidence',
        'floating',
        'breathing',
        'kicking',
        'freestyle',
        'breaststroke',
        'recommended_level',
        'coach_notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'water_confidence' => 'integer',
            'floating' => 'integer',
            'breathing' => 'integer',
            'kicking' => 'integer',
            'freestyle' => 'integer',
            'breaststroke' => 'integer',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /**
     * Mengambil seluruh nilai kemampuan renang.
     */
    public function getSkillScores(): array
    {
        return [
            'water_confidence' => $this->water_confidence,
            'floating' => $this->floating,
            'breathing' => $this->breathing,
            'kicking' => $this->kicking,
            'freestyle' => $this->freestyle,
            'breaststroke' => $this->breaststroke,
        ];
    }

    /**
     * Menghitung jumlah kemampuan yang sudah dinilai.
     */
    public function getAssessedSkillsCountAttribute(): int
    {
        return count(
            array_filter(
                $this->getSkillScores(),
                fn ($score) => $score !== null
            )
        );
    }

    /**
     * Menghitung rata-rata nilai kemampuan.
     * Nilai yang belum diisi tidak ikut dihitung.
     */
    public function getAverageScoreAttribute(): float
    {
        $scores = array_filter(
            $this->getSkillScores(),
            fn ($score) => $score !== null
        );

        if (count($scores) === 0) {
            return 0.0;
        }

        return round(
            array_sum($scores) / count($scores),
            2
        );
    }

    /**
     * Label tingkat kemampuan yang direkomendasikan.
     */
    public function getRecommendedLevelLabelAttribute(): string
    {
        return match ($this->recommended_level) {
            'beginner' => 'Pemula',
            'basic' => 'Dasar',
            'intermediate' => 'Menengah',
            'advanced' => 'Lanjutan',
            default => 'Belum Ditentukan',
        };
    }

    /**
     * Mengubah nilai menjadi keterangan kemampuan.
     */
    public function getScoreLabel(?int $score): string
    {
        return match ($score) {
            1 => 'Perlu Banyak Latihan',
            2 => 'Mulai Berkembang',
            3 => 'Cukup Baik',
            4 => 'Baik',
            5 => 'Sangat Baik',
            default => 'Belum Dinilai',
        };
    }

    /**
     * Assessment masih dijadwalkan.
     */
    public function isPending(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Assessment sudah selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Assessment dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Menunggu Assessment',
            'completed' => 'Assessment Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }
}
