
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur tabel settings sesuai
     * database MySQL kelas_renang.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            // Primary key
            $table->id();

            // Nama pengaturan, wajib unik
            $table->string('setting_key', 100);

            // Nilai pengaturan, boleh kosong
            $table->text('setting_value')
                ->nullable();

            // Kelompok pengaturan
            $table->string('setting_group', 50)
                ->default('general');

            // Timestamp otomatis MySQL
            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Unique index sesuai database asli
            $table->unique(
                'setting_key',
                'setting_key'
            );
        });
    }

    /**
     * Menghapus tabel jika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
