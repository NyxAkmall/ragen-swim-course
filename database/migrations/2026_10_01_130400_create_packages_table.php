
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal packages sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan struktur berikutnya dibuat
     * melalui migration tambahan.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
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
            | Relasi Program
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('program_id');

            /*
            |--------------------------------------------------------------------------
            | Informasi Paket
            |--------------------------------------------------------------------------
            */

            $table->string('name', 100);

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jumlah Sesi
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('total_sessions');

            /*
            |--------------------------------------------------------------------------
            | Harga Paket
            |--------------------------------------------------------------------------
            */

            $table->decimal('price', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Masa Berlaku dalam Hari
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('validity_days')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Paket
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
                'program_id',
                'fk_packages_program'
            );

            $table->foreign(
                'program_id',
                'fk_packages_program'
            )
                ->references('id')
                ->on('programs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
