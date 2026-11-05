<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal users sesuai backup database kelas_renang.
     *
     * Perubahan setelah baseline ini dibuat melalui
     * migration tambahan.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identitas Akun
            |--------------------------------------------------------------------------
            */

            $table->string('name', 100);

            $table->string('email', 150);

            $table->string('phone', 20)
                ->nullable();

            $table->string('password', 255);

            /*
            |--------------------------------------------------------------------------
            | Role dan Status
            |--------------------------------------------------------------------------
            */

            $table->enum('role', [
                'admin',
                'coach',
                'customer',
            ])->default('customer');

            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
            ])->default('active');

            /*
            |--------------------------------------------------------------------------
            | Verifikasi Email
            |--------------------------------------------------------------------------
            */

            $table->dateTime('email_verified_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            |
            | Mengikuti pengaturan waktu otomatis database aktif.
            |
            */

            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->unique('email', 'email');
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
