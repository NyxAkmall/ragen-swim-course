<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'participant_name',
        'rating',
        'content',
        'photo',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Memeriksa apakah testimoni boleh
     * ditampilkan kepada pengunjung website.
     */
    public function isActive(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Memeriksa apakah testimoni unggulan.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured === true;
    }

    /**
     * Memeriksa ketersediaan foto.
     */
    public function hasPhoto(): bool
    {
        return !empty($this->photo);
    }

    /**
     * Memeriksa ketersediaan rating.
     */
    public function hasRating(): bool
    {
        return $this->rating !== null;
    }

    /**
     * Menampilkan rating dalam format 5/5.
     */
    public function getRatingLabelAttribute(): string
    {
        if ($this->rating === null) {
            return 'Belum Ada Rating';
        }

        return $this->rating . '/5';
    }

    /**
     * Menampilkan status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Dipublikasikan',
            'draft' => 'Draf',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mengambil testimoni yang sudah dipublikasikan.
     *
     * Pemanggilan lama Testimonial::active()
     * tetap dapat digunakan.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Mengurutkan testimoni terbaru terlebih dahulu.
     *
     * Tabel asli tidak memiliki kolom sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Mengambil testimoni unggulan.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
