
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel FAQ sesuai database
     * MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Pertanyaan dan jawaban
            $table->string('question', 255);
            $table->text('answer');

            // Kategori FAQ (opsional)
            $table->string('category', 100)
                ->nullable();

            // Urutan tampilan FAQ
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
        Schema::dropIfExists('faqs');
    }
};
