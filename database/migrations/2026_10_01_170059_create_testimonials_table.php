
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel testimonials sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Identitas pemberi testimoni
            $table->string('name', 100);

            $table->string('participant_name', 100)
                ->nullable();

            // Penilaian 1 sampai 5
            $table->unsignedTinyInteger('rating')
                ->default(5);

            // Isi testimoni
            $table->text('content');

            // Foto pemberi testimoni (opsional)
            $table->string('photo', 255)
                ->nullable();

            // Penanda testimoni unggulan
            $table->boolean('is_featured')
                ->default(false);

            // Status publikasi
            $table->enum('status', [
                'published',
                'draft',
            ])->default('draft');

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();
        });

        /*
        |--------------------------------------------------------------------------
        | CHECK Constraint Rating
        |--------------------------------------------------------------------------
        |
        | Rating wajib berada dalam rentang 1–5.
        | Nama constraint mengikuti database asli.
        |
        */

        DB::statement(
            'ALTER TABLE `testimonials`
             ADD CONSTRAINT `chk_testimonial_rating`
             CHECK (`rating` BETWEEN 1 AND 5)'
        );
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
