
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal class_schedules sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
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
            | Relasi Program, Coach, dan Lokasi
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('program_id');

            $table->unsignedBigInteger('coach_id');

            $table->unsignedBigInteger('location_id');

            /*
            |--------------------------------------------------------------------------
            | Informasi Kelas
            |--------------------------------------------------------------------------
            */

            $table->string('title', 150)
                ->nullable();

            $table->enum('swimming_level', [
                'all',
                'beginner',
                'basic',
                'intermediate',
                'advanced',
            ])->default('all');

            /*
            |--------------------------------------------------------------------------
            | Tanggal dan Waktu
            |--------------------------------------------------------------------------
            */

            $table->date('class_date');

            $table->time('start_time');

            $table->time('end_time');

            /*
            |--------------------------------------------------------------------------
            | Kapasitas Kelas
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('capacity')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Status dan Catatan
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->text('notes')
                ->nullable();

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
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'class_date',
                'idx_schedule_date'
            );

            $table->index(
                'coach_id',
                'idx_schedule_coach'
            );

            $table->index(
                'program_id',
                'idx_schedule_program'
            );

            $table->index(
                'location_id',
                'idx_schedule_location'
            );

            $table->index([
                'coach_id',
                'class_date',
                'start_time',
                'end_time',
            ], 'idx_schedule_coach_datetime');

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'coach_id',
                'fk_schedule_coach'
            )
                ->references('id')
                ->on('coaches')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'location_id',
                'fk_schedule_location'
            )
                ->references('id')
                ->on('locations')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'program_id',
                'fk_schedule_program'
            )
                ->references('id')
                ->on('programs')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
