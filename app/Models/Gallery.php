<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    protected $fillable = [
        'title',
        'description',
        'image',
        'category',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'regular' => 'Kelas Reguler',
            'private' => 'Kelas Private',
            'special_needs' => 'Anak Berkebutuhan Khusus',
            'trial' => 'Trial Class',
            'event' => 'Event',
            'facility' => 'Fasilitas',
            'coach' => 'Pelatih',
            default => $this->category
                ? ucfirst($this->category)
                : 'Umum',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Dipublikasikan',
            'draft' => 'Draf',
            default => 'Tidak Diketahui',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeCategory(
        Builder $query,
        string $category
    ): Builder {
        return $query->where('category', $category);
    }
}
