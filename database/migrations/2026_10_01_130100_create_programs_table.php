
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal programs sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan struktur berikutnya dibuat
     * melalui migration tambahan.
     */
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
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
            | Informasi Program
            |--------------------------------------------------------------------------
            */

            $table->string('name', 100);

            $table->string('slug', 120);

            /*
            |--------------------------------------------------------------------------
            | Deskripsi Program
            |--------------------------------------------------------------------------
            */

            $table->string('short_description', 255)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Gambar Program
            |--------------------------------------------------------------------------
            */

            $table->string('image', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Durasi Program
            |--------------------------------------------------------------------------
            |
            | Satuan: menit.
            | Default: 60 menit.
            |
            */

            $table->unsignedInteger('duration_minutes')
                ->default(60);

            /*
            |--------------------------------------------------------------------------
            | Status Program
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
            | Unique Index
            |--------------------------------------------------------------------------
            */

            $table->unique('slug', 'slug');
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
