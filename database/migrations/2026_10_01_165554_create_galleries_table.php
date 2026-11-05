
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel galleries sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Informasi galeri
            $table->string('title', 150)
                ->nullable();

            $table->text('description')
                ->nullable();

            // Lokasi file gambar (wajib)
            $table->string('image', 255);

            // Kategori galeri
            $table->string('category', 100)
                ->nullable();

            // Status publikasi
            $table->enum('status', [
                'published',
                'draft',
            ])->default('published');

            // Urutan tampilan
            $table->integer('sort_order')
                ->default(0);

            // Timestamp otomatis MySQL
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
        Schema::dropIfExists('galleries');
    }
};
