
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal locations sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan berikutnya dibuat melalui
     * migration tambahan.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
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
            | Informasi Lokasi
            |--------------------------------------------------------------------------
            */

            $table->string('name', 150);

            $table->text('address');

            $table->string('city', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Google Maps
            |--------------------------------------------------------------------------
            */

            $table->string('google_maps_url', 500)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Deskripsi dan Fasilitas
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            $table->text('facilities')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Gambar Lokasi
            |--------------------------------------------------------------------------
            */

            $table->string('image', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Lokasi
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
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
