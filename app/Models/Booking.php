<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'bookings';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'participant_id',
        'participant_package_id',
        'class_schedule_id',
        'booking_code',
        'status',
        'session_deducted',
        'booked_at',
        'cancelled_at',
        'cancellation_reason',
        'previous_booking_id',
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'session_deducted' => 'boolean',
            'booked_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Peserta yang melakukan booking.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Paket peserta yang digunakan untuk booking.
     */
    public function participantPackage(): BelongsTo
    {
        return $this->belongsTo(ParticipantPackage::class);
    }

    /**
     * Jadwal kelas yang dipesan.
     */
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    /**
     * Data kehadiran untuk booking ini.
     */
    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * Laporan perkembangan peserta untuk booking ini.
     */
    public function progressRecord(): HasOne
    {
        return $this->hasOne(ProgressRecord::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Reschedule
    |--------------------------------------------------------------------------
    */

    /**
     * Booking sebelumnya jika booking ini merupakan
     * hasil pemindahan jadwal.
     */
    public function previousBooking(): BelongsTo
    {
        return $this->belongsTo(
            Booking::class,
            'previous_booking_id'
        );
    }

    /**
     * Booking baru yang menggantikan booking ini
     * setelah proses reschedule.
     */
    public function rescheduledBooking(): HasOne
    {
        return $this->hasOne(
            Booking::class,
            'previous_booking_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Booking
    |--------------------------------------------------------------------------
    */

    /**
     * Booking masih menunggu konfirmasi.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Booking sudah dikonfirmasi.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Kelas pada booking sudah selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Booking sudah dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Booking sudah dipindahkan ke jadwal lain.
     */
    public function isRescheduled(): bool
    {
        return $this->status === 'rescheduled';
    }

    /*
    |--------------------------------------------------------------------------
    | Kapasitas dan Penggunaan Sesi
    |--------------------------------------------------------------------------
    */

    /**
     * Menentukan apakah booking masih menggunakan
     * kapasitas kelas.
     *
     * Booking pending dan confirmed memegang slot.
     */
    public function holdsClassSlot(): bool
    {
        return in_array(
            $this->status,
            ['pending', 'confirmed'],
            true
        );
    }

    /**
     * Mengecek apakah sesi paket sudah pernah
     * dikurangi untuk booking ini.
     */
    public function hasDeductedSession(): bool
    {
        return $this->session_deducted === true;
    }

    /*
    |--------------------------------------------------------------------------
    | Pembatalan dan Reschedule
    |--------------------------------------------------------------------------
    */

    /**
     * Menentukan apakah booking masih dapat
     * dibatalkan atau dipindahkan.
     *
     * Batas waktu pembatalan dan reschedule
     * akan diperiksa oleh service/controller.
     */
    public function canBeModified(): bool
    {
        return in_array(
            $this->status,
            ['pending', 'confirmed'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Label status untuk halaman Customer,
     * Coach, dan Admin.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'rescheduled' => 'Dijadwalkan Ulang',
            default => ucfirst(
                str_replace('_', ' ', (string) $this->status)
            ),
        };
    }
}
