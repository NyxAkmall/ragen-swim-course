
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal participants sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan struktur berikutnya harus dibuat
     * melalui migration tambahan.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi Akun Customer
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Identitas Peserta
            |--------------------------------------------------------------------------
            */

            $table->string('full_name', 100);

            $table->enum('gender', [
                'male',
                'female',
            ])->nullable();

            $table->date('birth_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Kemampuan Renang
            |--------------------------------------------------------------------------
            */

            $table->enum('swimming_level', [
                'unknown',
                'beginner',
                'basic',
                'intermediate',
                'advanced',
            ])->default('unknown');

            /*
            |--------------------------------------------------------------------------
            | Jenis Peserta
            |--------------------------------------------------------------------------
            */

            $table->enum('participant_type', [
                'self',
                'child',
            ])->default('child');

            /*
            |--------------------------------------------------------------------------
            | Catatan dan Kontak Darurat
            |--------------------------------------------------------------------------
            */

            $table->text('notes')->nullable();

            $table->string('emergency_contact_name', 100)
                ->nullable();

            $table->string('emergency_contact_phone', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Peserta
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
            */

            $table->index(
                'user_id',
                'fk_participants_user'
            );

            $table->foreign(
                'user_id',
                'fk_participants_user'
            )
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
        Schema::dropIfExists('participants');
    }
};
