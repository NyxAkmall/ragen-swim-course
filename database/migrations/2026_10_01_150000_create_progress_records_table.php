
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal progress_records sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('progress_records', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key dan relasi
            $table->id();

            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('coach_id');
            $table->unsignedBigInteger('booking_id')->nullable();

            // Tingkat kemampuan renang
            $table->enum('level', [
                'beginner',
                'basic',
                'intermediate',
                'advanced',
            ]);

            // Enam indikator penilaian (1–5)
            $table->unsignedTinyInteger('water_adaptation')->nullable();
            $table->unsignedTinyInteger('floating')->nullable();
            $table->unsignedTinyInteger('breathing')->nullable();
            $table->unsignedTinyInteger('kicking')->nullable();
            $table->unsignedTinyInteger('freestyle')->nullable();
            $table->unsignedTinyInteger('breaststroke')->nullable();

            // Catatan dan tanggal penilaian
            $table->text('coach_notes')->nullable();
            $table->date('assessment_date');

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Unique index
            $table->unique(
                'booking_id',
                'unique_progress_booking'
            );

            // Index
            $table->index(
                'participant_id',
                'fk_progress_participant'
            );

            $table->index(
                'coach_id',
                'fk_progress_coach'
            );

            // Foreign key
            $table->foreign('booking_id', 'fk_progress_booking')
                ->references('id')
                ->on('bookings')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('coach_id', 'fk_progress_coach')
                ->references('id')
                ->on('coaches')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'participant_id',
                'fk_progress_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        /*
        |--------------------------------------------------------------------------
        | CHECK Constraints
        |--------------------------------------------------------------------------
        |
        | Setiap indikator boleh NULL.
        | Jika diisi, nilainya harus antara 1 dan 5.
        |
        */

        $indicators = [
            'water_adaptation',
            'floating',
            'breathing',
            'kicking',
            'freestyle',
            'breaststroke',
        ];

        foreach ($indicators as $indicator) {
            DB::statement(
                "ALTER TABLE progress_records
                 ADD CONSTRAINT chk_{$indicator}
                 CHECK (`{$indicator}` BETWEEN 1 AND 5)"
            );
        }
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_records');
    }
};
