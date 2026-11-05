<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->restrictOnDelete();

            $table->foreignId('coach_id')
                ->constrained('coaches')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            $table->string('title', 150);

            $table->enum('swimming_level', [
                'all',
                'beginner',
                'basic',
                'intermediate',
                'advanced',
            ])->default('all');

            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('capacity')
                ->default(1);

            $table->boolean('is_active')
                ->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['day_of_week', 'start_time', 'is_active'],
                'idx_weekly_day_time_active'
            );

            $table->index(
                ['coach_id', 'day_of_week'],
                'idx_weekly_coach_day'
            );

            $table->index(
                ['location_id', 'day_of_week'],
                'idx_weekly_location_day'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_schedules');
    }
};
