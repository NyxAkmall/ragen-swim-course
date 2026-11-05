<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->timestamp('registration_paid_at')
                ->nullable();

            $table->boolean('registration_fee_exempt')
                ->default(false);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('registration_fee', 12, 2)
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('registration_fee');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'registration_paid_at',
                'registration_fee_exempt',
            ]);
        });
    }
};
