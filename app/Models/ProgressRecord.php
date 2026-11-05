<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressRecord extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'progress_records';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'participant_id',
        'coach_id',
        'booking_id',
        'level',
        'water_adaptation',
        'floating',
        'breathing',
        'kicking',
        'freestyle',
        'breaststroke',
        'coach_notes',
        'assessment_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'water_adaptation' => 'integer',
            'floating' => 'integer',
            'breathing' => 'integer',
            'kicking' => 'integer',
            'freestyle' => 'integer',
            'breaststroke' => 'integer',
            'assessment_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Peserta yang mendapatkan penilaian.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Pelatih yang memberikan penilaian.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /**
     * Pertemuan atau booking yang dinilai.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Data Penilaian
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil seluruh nilai kemampuan renang.
     *
     * Nilai yang belum diisi tetap berupa null.
     */
    public function getSkillScores(): array
    {
        return [
            'water_adaptation' => $this->water_adaptation,
            'floating' => $this->floating,
            'breathing' => $this->breathing,
            'kicking' => $this->kicking,
            'freestyle' => $this->freestyle,
            'breaststroke' => $this->breaststroke,
        ];
    }

    /**
     * Menghitung jumlah kemampuan yang sudah dinilai.
     *
     * Nilai kosong tidak dihitung.
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
     * Menghitung rata-rata nilai kemampuan peserta.
     *
     * Sistem menggunakan skala 1–5.
     * Kemampuan yang belum dinilai tidak ikut dihitung.
     *
     * Jika seluruh nilai kosong, hasilnya 0.
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

    /*
    |--------------------------------------------------------------------------
    | Informasi Level
    |--------------------------------------------------------------------------
    */

    /**
     * Mengubah kode level menjadi label
     * dalam Bahasa Indonesia.
     */
    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'beginner' => 'Pemula',
            'basic' => 'Dasar',
            'intermediate' => 'Menengah',
            'advanced' => 'Lanjutan',
            default => 'Belum Ditentukan',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Nilai
    |--------------------------------------------------------------------------
    */

    /**
     * Mengubah nilai angka menjadi
     * keterangan perkembangan peserta.
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
}
