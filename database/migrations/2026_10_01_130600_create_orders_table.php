
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal orders sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan berikutnya dibuat melalui
     * migration tambahan.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
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
            | Relasi Customer dan Kode Promo
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('promo_code_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Identitas Pesanan
            |--------------------------------------------------------------------------
            */

            $table->string('order_code', 30);

            /*
            |--------------------------------------------------------------------------
            | Nominal Transaksi
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 12, 2)
                ->default(0);

            $table->decimal('discount_amount', 12, 2)
                ->default(0);

            $table->decimal('total_amount', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status Pesanan
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'paid',
                'cancelled',
                'expired',
                'refunded',
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Catatan
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Waktu Transaksi
            |--------------------------------------------------------------------------
            */

            $table->timestamp('ordered_at')
                ->useCurrent();

            $table->dateTime('paid_at')
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
            | Unique Index
            |--------------------------------------------------------------------------
            */

            $table->unique(
                'order_code',
                'order_code'
            );

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'promo_code_id',
                'fk_orders_promo'
            );

            $table->index(
                'user_id',
                'idx_orders_user'
            );

            $table->index(
                'status',
                'idx_orders_status'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'promo_code_id',
                'fk_orders_promo'
            )
                ->references('id')
                ->on('promo_codes')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'user_id',
                'fk_orders_user'
            )
                ->references('id')
                ->on('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
