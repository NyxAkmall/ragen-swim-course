
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel payment_logs sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Relasi ke pembayaran
            $table->unsignedBigInteger('payment_id');

            // Status pembayaran sebelumnya (opsional)
            $table->enum('old_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'refunded',
            ])->nullable();

            // Status pembayaran terbaru
            $table->enum('new_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'refunded',
            ]);

            // Respons dari payment gateway
            $table->text('gateway_response')
                ->nullable();

            // Hanya created_at, tanpa updated_at
            $table->timestamp('created_at')
                ->useCurrent();

            // Index sesuai database asli
            $table->index(
                'payment_id',
                'fk_payment_logs_payment'
            );

            // Foreign key ke tabel payments
            $table->foreign(
                'payment_id',
                'fk_payment_logs_payment'
            )
                ->references('id')
                ->on('payments')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
