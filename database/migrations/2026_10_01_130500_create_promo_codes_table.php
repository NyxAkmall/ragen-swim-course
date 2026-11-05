
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal promo_codes sesuai
     * database MySQL kelas_renang.
     *
     * Perubahan berikutnya dibuat melalui
     * migration tambahan.
     */
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identitas Promo
            |--------------------------------------------------------------------------
            */

            $table->string('code', 50);

            $table->string('name', 100);

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jenis dan Nilai Diskon
            |--------------------------------------------------------------------------
            */

            $table->enum('discount_type', [
                'percentage',
                'fixed',
            ]);

            $table->decimal('discount_value', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Ketentuan Pembelian
            |--------------------------------------------------------------------------
            */

            $table->decimal('minimum_purchase', 12, 2)
                ->default(0);

            $table->decimal('maximum_discount', 12, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Batas Penggunaan
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('usage_limit')
                ->nullable();

            $table->unsignedInteger('used_count')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Masa Berlaku
            |--------------------------------------------------------------------------
            */

            $table->dateTime('start_date')
                ->nullable();

            $table->dateTime('end_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Promo
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

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
            | Unique Index
            |--------------------------------------------------------------------------
            */

            $table->unique('code', 'code');
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
