
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel notifications sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Pengguna penerima notifikasi
            $table->unsignedBigInteger('user_id');

            // Informasi notifikasi
            $table->string('type', 50);
            $table->string('title', 150);
            $table->text('message');

            // Referensi terkait notifikasi (opsional)
            $table->string('reference_type', 50)
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            // Status sudah dibaca atau belum
            $table->boolean('is_read')
                ->default(false);

            $table->dateTime('read_at')
                ->nullable();

            // Hanya created_at, tanpa updated_at
            $table->timestamp('created_at')
                ->useCurrent();

            // Index gabungan sesuai database asli
            $table->index(
                ['user_id', 'is_read'],
                'idx_notifications_user_read'
            );

            // Foreign key ke pengguna
            $table->foreign(
                'user_id',
                'fk_notifications_user'
            )
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
