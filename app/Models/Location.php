<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'address',
        'city',
        'google_maps_url',
        'description',
        'facilities',
        'image',
        'status',
        'customer_entry_fee',
        'coach_entry_fee',
        'customer_ticket_status',
        'customer_ticket_payer',
        'customer_ticket_payment_method',
        'coach_ticket_status',
        'coach_ticket_payer',
        'full_include_enabled',
        'full_include_name',
        'full_include_price',
        'full_include_start_date',
        'full_include_end_date',
    ];

    protected function casts(): array
    {
        return [
            'customer_entry_fee' => 'decimal:2',
            'coach_entry_fee' => 'decimal:2',
            'full_include_enabled' => 'boolean',
            'full_include_price' => 'decimal:2',
            'full_include_start_date' => 'date',
            'full_include_end_date' => 'date',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasGoogleMaps(): bool
    {
        return !empty($this->google_maps_url);
    }

    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => 'Tidak Diketahui',
        };
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            trim((string) $this->address),
            trim((string) $this->city),
        ]);

        return $parts
            ? implode(', ', $parts)
            : 'Alamat Belum Tersedia';
    }

    public function getFacilitiesListAttribute(): array
    {
        $facilities = $this->facilities;

        if (empty($facilities)) {
            return [];
        }

        if (is_array($facilities)) {
            return array_values(array_filter(
                $facilities,
                fn ($item) => is_string($item)
                    && trim($item) !== ''
            ));
        }

        $decoded = json_decode(
            (string) $facilities,
            true
        );

        if (
            json_last_error() === JSON_ERROR_NONE
            && is_array($decoded)
        ) {
            return array_values(array_filter(
                $decoded,
                fn ($item) => is_string($item)
                    && trim($item) !== ''
            ));
        }

        $items = preg_split(
            '/[\r\n,]+/',
            (string) $facilities
        );

        return array_values(array_filter(
            array_map('trim', $items),
            fn ($item) => $item !== ''
        ));
    }

    public function getCustomerEntryFeeFormattedAttribute(): string
    {
        return 'Rp' . number_format(
            (float) $this->customer_entry_fee,
            0,
            ',',
            '.'
        );
    }

    public function getCoachEntryFeeFormattedAttribute(): string
    {
        return 'Rp' . number_format(
            (float) $this->coach_entry_fee,
            0,
            ',',
            '.'
        );
    }
}
