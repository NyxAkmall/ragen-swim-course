
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel refunds sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Relasi ke pembayaran
            $table->unsignedBigInteger('payment_id');

            // Kode refund harus unik
            $table->string('refund_code', 50);

            // Nominal pengembalian dana
            $table->decimal('amount', 12, 2);

            // Alasan refund (opsional)
            $table->text('reason')->nullable();

            // Status pengembalian dana
            $table->enum('status', [
                'pending',
                'processed',
                'failed',
            ])->default('pending');

            // Waktu refund diproses
            $table->dateTime('processed_at')
                ->nullable();

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Unique index sesuai database asli
            $table->unique(
                'refund_code',
                'refund_code'
            );

            // Index pembayaran
            $table->index(
                'payment_id',
                'fk_refunds_payment'
            );

            // Foreign key ke tabel payments
            $table->foreign(
                'payment_id',
                'fk_refunds_payment'
            )
                ->references('id')
                ->on('payments')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
