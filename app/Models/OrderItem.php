<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'order_items';

    /*
    | Tabel order_items hanya memiliki created_at,
    | tanpa kolom updated_at.
    */

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'order_id',
        'participant_id',
        'package_id',
        'package_name',
        'quantity',
        'unit_price',
        'subtotal',
        'created_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /*
    | Satu item pesanan dapat menghasilkan satu
    | paket peserta setelah pembayaran berhasil.
    */

    public function participantPackage(): HasOne
    {
        return $this->hasOne(ParticipantPackage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Format Harga
    |--------------------------------------------------------------------------
    */

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->unit_price,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format(
            (float) $this->subtotal,
            0,
            ',',
            '.'
        );
    }
}
