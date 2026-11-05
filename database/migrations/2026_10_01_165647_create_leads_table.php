
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel leads sesuai database
     * MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Identitas calon pelanggan
            $table->string('name', 100);
            $table->string('phone', 20);

            $table->string('email', 150)
                ->nullable();

            // Program yang diminati (opsional)
            $table->unsignedBigInteger('program_id')
                ->nullable();

            // Sumber calon pelanggan
            $table->enum('source', [
                'website',
                'whatsapp',
                'instagram',
                'tiktok',
                'referral',
                'other',
            ])->default('website');

            // Status tindak lanjut
            $table->enum('status', [
                'new',
                'contacted',
                'follow_up',
                'converted',
                'not_interested',
            ])->default('new');

            // Catatan dan jadwal tindak lanjut
            $table->text('notes')
                ->nullable();

            $table->dateTime('follow_up_at')
                ->nullable();

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Index sesuai database asli
            $table->index(
                'program_id',
                'fk_leads_program'
            );

            // Foreign key ke tabel programs
            $table->foreign(
                'program_id',
                'fk_leads_program'
            )
                ->references('id')
                ->on('programs')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
