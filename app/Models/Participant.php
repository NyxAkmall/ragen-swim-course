<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Tabel
    |--------------------------------------------------------------------------
    */

    protected $table = 'participants';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'birth_date',
        'swimming_level',
        'participant_type',
        'notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'registration_paid_at',
        'registration_fee_exempt',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registration_paid_at' => 'datetime',
            'registration_fee_exempt' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Akun customer atau orang tua
     * yang memiliki profil peserta.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Seluruh paket yang dimiliki peserta.
     */
    public function participantPackages(): HasMany
    {
        return $this->hasMany(ParticipantPackage::class);
    }

    /**
     * Riwayat pembelian paket peserta.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Seluruh booking kelas peserta.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Riwayat perkembangan kemampuan renang.
     */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    /**
     * Riwayat assessment peserta.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Riwayat daftar tunggu kelas.
     */
    public function waitingLists(): HasMany
    {
        return $this->hasMany(WaitingList::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Peserta
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah peserta masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Biaya Pendaftaran
    |--------------------------------------------------------------------------
    */

    /**
     * Peserta sudah melunasi biaya pendaftaran.
     */
    public function hasPaidRegistration(): bool
    {
        return $this->registration_paid_at !== null;
    }

    /**
     * Peserta dibebaskan dari biaya pendaftaran.
     */
    public function isRegistrationFeeExempt(): bool
    {
        return (bool) $this->registration_fee_exempt;
    }

    /**
     * Menentukan apakah peserta masih wajib
     * membayar biaya pendaftaran.
     */
    public function needsRegistrationFee(): bool
    {
        return !$this->hasPaidRegistration()
            && !$this->isRegistrationFeeExempt();
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Jenis Peserta
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah profil peserta merupakan anak.
     */
    public function isChild(): bool
    {
        return $this->participant_type === 'child';
    }

    /**
     * Mengecek apakah peserta mendaftarkan dirinya sendiri.
     */
    public function isSelf(): bool
    {
        return $this->participant_type === 'self';
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Usia
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung usia peserta berdasarkan tanggal lahir.
     *
     * Jika tanggal lahir belum tersedia atau berada
     * di masa depan, usia tidak ditampilkan.
     */
    public function getAgeAttribute(): ?int
    {
        if ($this->birth_date === null) {
            return null;
        }

        if ($this->birth_date->isFuture()) {
            return null;
        }

        return $this->birth_date->age;
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Label status peserta.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Label jenis peserta.
     */
    public function getParticipantTypeLabelAttribute(): string
    {
        return match ($this->participant_type) {
            'child' => 'Anak',
            'self' => 'Diri Sendiri',
            default => 'Tidak Ditentukan',
        };
    }

    /**
     * Label tingkat kemampuan renang.
     */
    public function getSwimmingLevelLabelAttribute(): string
    {
        return match ($this->swimming_level) {
            'beginner' => 'Pemula',
            'basic' => 'Dasar',
            'intermediate' => 'Menengah',
            'advanced' => 'Lanjutan',
            default => 'Belum Ditentukan',
        };
    }

    /**
     * Label jenis kelamin.
     */
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            default => 'Belum Ditentukan',
        };
    }
}
