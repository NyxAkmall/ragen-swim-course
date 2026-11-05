
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel waiting_lists sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Relasi peserta dan jadwal kelas
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('class_schedule_id');

            // Status daftar tunggu
            $table->enum('status', [
                'waiting',
                'offered',
                'accepted',
                'expired',
                'cancelled',
            ])->default('waiting');

            // Nomor antrean (opsional)
            $table->unsignedInteger('queue_number')
                ->nullable();

            // Waktu penawaran dan batas penerimaan
            $table->dateTime('offered_at')
                ->nullable();

            $table->dateTime('expires_at')
                ->nullable();

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            /*
             * Satu peserta hanya boleh memiliki
             * satu entri per jadwal kelas.
             */
            $table->unique(
                ['participant_id', 'class_schedule_id'],
                'unique_waiting_participant_schedule'
            );

            // Index sesuai database asli
            $table->index(
                'class_schedule_id',
                'fk_waiting_schedule'
            );

            // Foreign key peserta
            $table->foreign(
                'participant_id',
                'fk_waiting_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Foreign key jadwal kelas
            $table->foreign(
                'class_schedule_id',
                'fk_waiting_schedule'
            )
                ->references('id')
                ->on('class_schedules')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
