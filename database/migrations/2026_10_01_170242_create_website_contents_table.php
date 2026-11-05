
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel website_contents sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('website_contents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Identitas halaman dan bagian konten
            $table->string('page', 50);
            $table->string('section', 100);

            // Judul dan subjudul (opsional)
            $table->string('title', 255)
                ->nullable();

            $table->string('subtitle', 255)
                ->nullable();

            // Isi konten dan gambar
            $table->text('content')
                ->nullable();

            $table->string('image', 255)
                ->nullable();

            // Tombol dan tautan (opsional)
            $table->string('button_text', 100)
                ->nullable();

            $table->string('button_url', 255)
                ->nullable();

            // Urutan tampilan
            $table->integer('sort_order')
                ->default(0);

            // Status publikasi
            $table->enum('status', [
                'published',
                'draft',
            ])->default('published');

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
        Schema::dropIfExists('website_contents');
    }
};
