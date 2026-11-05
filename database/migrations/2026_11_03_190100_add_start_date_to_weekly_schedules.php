<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_schedules', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('day_of_week');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_schedules', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
};
