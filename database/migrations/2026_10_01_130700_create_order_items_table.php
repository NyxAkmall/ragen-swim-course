
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal order_items sesuai
     * database MySQL kelas_renang.
     *
     * Tabel orders, participants, dan packages
     * harus tersedia sebelum migration ini.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
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
            | Relasi Pesanan, Peserta, dan Paket
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('order_id');

            $table->unsignedBigInteger('participant_id');

            $table->unsignedBigInteger('package_id');

            /*
            |--------------------------------------------------------------------------
            | Data Paket Saat Pembelian
            |--------------------------------------------------------------------------
            |
            | Nama dan harga disimpan sebagai snapshot.
            | Perubahan katalog tidak mengubah
            | rincian pembelian yang sudah tercatat.
            |
            */

            $table->string('package_name', 100);

            $table->unsignedInteger('quantity')
                ->default(1);

            $table->decimal('unit_price', 12, 2);

            $table->decimal('subtotal', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            |
            | Tabel ini hanya memiliki created_at.
            | Tidak ada kolom updated_at.
            |
            */

            $table->timestamp('created_at')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'participant_id',
                'fk_order_items_participant'
            );

            $table->index(
                'package_id',
                'fk_order_items_package'
            );

            $table->index(
                'order_id',
                'idx_order_items_order'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'order_id',
                'fk_order_items_order'
            )
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'package_id',
                'fk_order_items_package'
            )
                ->references('id')
                ->on('packages')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'participant_id',
                'fk_order_items_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
