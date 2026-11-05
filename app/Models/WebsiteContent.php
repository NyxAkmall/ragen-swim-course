<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteContent extends Model
{
    use HasFactory;

    protected $table = 'website_contents';

    protected $fillable = [
        'page',
        'section',
        'title',
        'subtitle',
        'content',
        'image',
        'button_text',
        'button_url',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Memeriksa apakah konten dipublikasikan.
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
     * Memeriksa apakah konten memiliki gambar.
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    /**
     * Memeriksa apakah konten memiliki tombol.
     */
    public function hasButton(): bool
    {
        return !empty($this->button_text)
            && !empty($this->button_url);
    }

    /**
     * Mengambil isi konten berdasarkan
     * kombinasi halaman dan bagian.
     *
     * Contoh:
     * WebsiteContent::getValue('home.hero');
     *
     * Key tersebut dibaca sebagai:
     * page = home
     * section = hero
     */
    public static function getValue(
        string $key,
        mixed $default = null
    ): mixed {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return $default;
        }

        [$page, $section] = $parts;

        $record = static::query()
            ->where('page', $page)
            ->where('section', $section)
            ->first();

        return $record?->content ?? $default;
    }

    /**
     * Menyimpan isi konten berdasarkan
     * kombinasi halaman dan bagian.
     *
     * Metode ini hanya memperbarui kolom
     * content, bukan title atau image.
     */
    public static function setValue(
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $description = null
    ): self {
        $parts = explode('.', $key, 2);

        if (
            count($parts) !== 2
            || empty($parts[0])
            || empty($parts[1])
        ) {
            throw new \InvalidArgumentException(
                'Key harus menggunakan format page.section.'
            );
        }

        [$page, $section] = $parts;

        $storedValue = static::prepareValueForStorage(
            $value,
            $type
        );

        return static::updateOrCreate(
            [
                'page' => $page,
                'section' => $section,
            ],
            [
                'content' => $storedValue,
            ]
        );
    }

    /**
     * Menyiapkan nilai sebelum disimpan.
     */
    protected static function prepareValueForStorage(
        mixed $value,
        string $type
    ): ?string {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',

            'json' => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            ),

            default => (string) $value,
        };
    }

    /**
     * Mengambil konten yang dipublikasikan.
     */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'published'
        );
    }

    /**
     * Memfilter konten berdasarkan halaman.
     */
    public function scopePage(
        Builder $query,
        string $page
    ): Builder {
        return $query->where(
            'page',
            $page
        );
    }

    /**
     * Memfilter konten berdasarkan bagian.
     */
    public function scopeSection(
        Builder $query,
        string $section
    ): Builder {
        return $query->where(
            'section',
            $section
        );
    }

    /**
     * Mengurutkan konten berdasarkan
     * sort_order, kemudian id.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Memfilter konten berdasarkan
     * format page.section.
     */
    public function scopeKey(
        Builder $query,
        string $key
    ): Builder {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('page', $parts[0])
            ->where('section', $parts[1]);
    }
}
