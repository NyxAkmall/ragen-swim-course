<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->foreignId('weekly_schedule_id')
                ->nullable()
                ->after('id')
                ->constrained('weekly_schedules')
                ->restrictOnDelete();

            $table->unique(
                ['weekly_schedule_id', 'class_date'],
                'unique_weekly_schedule_class_date'
            );
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropUnique('unique_weekly_schedule_class_date');
            $table->dropConstrainedForeignId('weekly_schedule_id');
        });
    }
};
