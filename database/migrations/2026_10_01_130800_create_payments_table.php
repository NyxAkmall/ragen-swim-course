
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal payments sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan tambahan dibuat melalui
     * migration terpisah.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            /*
            |--------------------------------------------------------------------------
            | Primary Key dan Relasi Pesanan
            |--------------------------------------------------------------------------
            */

            $table->id();

            $table->unsignedBigInteger('order_id');

            /*
            |--------------------------------------------------------------------------
            | Identitas dan Metode Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->string('payment_code', 50);

            $table->string('payment_method', 50)
                ->nullable();

            $table->string('payment_channel', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Nominal dan Status Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'refunded',
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Referensi Gateway
            |--------------------------------------------------------------------------
            */

            $table->string('gateway_reference', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Bukti Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->string('proof_image', 255)
                ->nullable();

            $table->dateTime('proof_uploaded_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Verifikasi Admin
            |--------------------------------------------------------------------------
            */

            $table->dateTime('verified_at')
                ->nullable();

            $table->unsignedBigInteger('verified_by')
                ->nullable();

            $table->text('rejection_reason')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Waktu Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->dateTime('paid_at')
                ->nullable();

            $table->dateTime('expired_at')
                ->nullable();

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
            | Unique Index dan Index
            |--------------------------------------------------------------------------
            */

            $table->unique('payment_code', 'payment_code');

            $table->index('order_id', 'idx_payments_order');

            $table->index('status', 'idx_payments_status');

            $table->index(
                'verified_by',
                'fk_payments_verified_by'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign('order_id', 'fk_payments_order')
                ->references('id')
                ->on('orders')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'verified_by',
                'fk_payments_verified_by'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
