<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal coaches sesuai database kelas_renang.
     *
     * Tabel users harus tersedia terlebih dahulu.
     */
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi Akun
            |--------------------------------------------------------------------------
            |
            | Satu akun hanya memiliki satu profil coach.
            |
            */

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Informasi Coach
            |--------------------------------------------------------------------------
            */

            $table->text('bio')
                ->nullable();

            $table->string('specialization', 150)
                ->nullable();

            $table->unsignedInteger('experience_years')
                ->nullable()
                ->default(0);

            $table->string('photo', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Index dan Foreign Key
            |--------------------------------------------------------------------------
            |
            | Nama dan aturan mengikuti database aktif.
            |
            */

            $table->unique('user_id', 'user_id');

            $table->foreign('user_id', 'fk_coaches_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
