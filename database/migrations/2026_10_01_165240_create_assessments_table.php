
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal assessments sesuai
     * database MySQL kelas_renang.
     *
     * Tabel participants dan coaches harus
     * tersedia sebelum migration ini.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            /*
            |--------------------------------------------------------------------------
            | Primary Key dan Relasi
            |--------------------------------------------------------------------------
            */

            $table->id();

            $table->unsignedBigInteger('participant_id');

            $table->unsignedBigInteger('coach_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tanggal Penilaian
            |--------------------------------------------------------------------------
            */

            $table->date('assessment_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indikator Kemampuan Renang
            |--------------------------------------------------------------------------
            |
            | Setiap indikator menerima nilai 1–5
            | atau NULL jika belum dinilai.
            |
            */

            $table->unsignedTinyInteger('water_confidence')
                ->nullable();

            $table->unsignedTinyInteger('floating')
                ->nullable();

            $table->unsignedTinyInteger('breathing')
                ->nullable();

            $table->unsignedTinyInteger('kicking')
                ->nullable();

            $table->unsignedTinyInteger('freestyle')
                ->nullable();

            $table->unsignedTinyInteger('breaststroke')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Rekomendasi Level dan Catatan
            |--------------------------------------------------------------------------
            */

            $table->enum('recommended_level', [
                'beginner',
                'basic',
                'intermediate',
                'advanced',
            ])->nullable();

            $table->text('coach_notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Penilaian
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
            ])->default('scheduled');

            /*
            |--------------------------------------------------------------------------
            | Timestamp Otomatis MySQL
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'participant_id',
                'fk_assessment_participant'
            );

            $table->index(
                'coach_id',
                'fk_assessment_coach'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'participant_id',
                'fk_assessment_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'coach_id',
                'fk_assessment_coach'
            )
                ->references('id')
                ->on('coaches')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        /*
        |--------------------------------------------------------------------------
        | CHECK Constraints
        |--------------------------------------------------------------------------
        |
        | Nama constraint mengikuti database asli.
        | Nilai NULL tetap diperbolehkan.
        |
        */

        $constraints = [
            'chk_assessment_water' => 'water_confidence',
            'chk_assessment_floating' => 'floating',
            'chk_assessment_breathing' => 'breathing',
            'chk_assessment_kicking' => 'kicking',
            'chk_assessment_freestyle' => 'freestyle',
            'chk_assessment_breaststroke' => 'breaststroke',
        ];

        foreach ($constraints as $name => $column) {
            DB::statement(
                "ALTER TABLE `assessments`
                 ADD CONSTRAINT `{$name}`
                 CHECK (`{$column}` BETWEEN 1 AND 5)"
            );
        }
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
