
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal invoices sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Tabel orders harus tersedia terlebih dahulu.
     * Perubahan tambahan dibuat melalui migration baru.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
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
            | Relasi Pesanan
            |--------------------------------------------------------------------------
            |
            | Satu pesanan hanya memiliki satu invoice.
            | Pesanan yang memiliki invoice tidak boleh
            | dihapus secara permanen.
            |
            */

            $table->unsignedBigInteger('order_id');

            /*
            |--------------------------------------------------------------------------
            | Nomor Invoice
            |--------------------------------------------------------------------------
            */

            $table->string('invoice_number', 50);

            /*
            |--------------------------------------------------------------------------
            | Waktu Penerbitan dan Jatuh Tempo
            |--------------------------------------------------------------------------
            */

            $table->dateTime('issued_at');

            $table->dateTime('due_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Invoice
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'unpaid',
                'paid',
                'cancelled',
                'refunded',
            ])->default('unpaid');

            /*
            |--------------------------------------------------------------------------
            | Timestamp Otomatis MySQL
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

            $table->unique('order_id', 'order_id');

            $table->unique(
                'invoice_number',
                'invoice_number'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'order_id',
                'fk_invoices_order'
            )
                ->references('id')
                ->on('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
