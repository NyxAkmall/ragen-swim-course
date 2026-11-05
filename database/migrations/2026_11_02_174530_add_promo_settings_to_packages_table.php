<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('promo_enabled')->default(false);
            $table->string('promo_name', 150)->nullable();
            $table->decimal('promo_price', 12, 2)->nullable();
            $table->date('promo_start_date')->nullable();
            $table->date('promo_end_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
'promo_enabled', 'promo_name', 'promo_price',
'promo_start_date', 'promo_end_date'
            ]);
        });
    }
};
