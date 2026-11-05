<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'email_verified_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Seluruh peserta yang terdaftar
     * menggunakan akun customer ini.
     *
     * Satu akun dapat mendaftarkan
     * beberapa peserta, misalnya beberapa anak.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Profil pelatih yang terhubung
     * dengan akun user.
     */
    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    /**
     * Seluruh transaksi pembelian paket
     * yang dilakukan oleh user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Seluruh notifikasi internal
     * Ragen Swim Course milik user.
     *
     * Nama relasi dibedakan dari notifications()
     * milik trait Notifiable Laravel.
     */
    public function systemNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Notifikasi internal yang belum dibaca.
     */
    public function unreadSystemNotifications(): HasMany
    {
        return $this->systemNotifications()
            ->where('is_read', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Role
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah user merupakan Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Mengecek apakah user merupakan Coach.
     */
    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    /**
     * Mengecek apakah user merupakan Customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /*
    |--------------------------------------------------------------------------
    | Pemeriksaan Status Akun
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah akun masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Mengecek apakah akun sedang ditangguhkan.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Mengecek apakah alamat email
     * sudah terverifikasi.
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi Tampilan
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan nama role
     * dalam format yang mudah dibaca.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'coach' => 'Pelatih',
            'customer' => 'Customer',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Menampilkan status akun
     * dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'suspended' => 'Ditangguhkan',
            default => 'Tidak Diketahui',
        };
    }
}
