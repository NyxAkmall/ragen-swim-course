<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan batas waktu reservasi promo
     * pada tabel orders.
     *
     * Migration ini tidak menghapus atau
     * mengubah data pesanan yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('promo_reserved_until')
                ->nullable()
                ->after('promo_code_id');

            $table->index(
                ['promo_code_id', 'status', 'promo_reserved_until'],
                'orders_promo_reservation_idx'
            );
        });
    }

    /**
     * Mengembalikan struktur sebelum migration.
     *
     * Method ini hanya digunakan jika migration
     * secara sengaja di-rollback.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_promo_reservation_idx');

            $table->dropColumn('promo_reserved_until');
        });
    }
};
