
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal participant_packages sesuai
     * database MySQL kelas_renang.
     *
     * Tabel participants, packages, dan order_items
     * harus tersedia terlebih dahulu.
     */
    public function up(): void
    {
        Schema::create('participant_packages', function (Blueprint $table) {
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
            | Relasi Peserta, Paket, dan Pembelian
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('participant_id');

            $table->unsignedBigInteger('package_id');

            $table->unsignedBigInteger('order_item_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jumlah Sesi
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('total_sessions');

            $table->unsignedInteger('used_sessions')
                ->default(0);

            $table->unsignedInteger('remaining_sessions');

            /*
            |--------------------------------------------------------------------------
            | Masa Berlaku
            |--------------------------------------------------------------------------
            */

            $table->date('start_date')
                ->nullable();

            $table->date('expiry_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Paket Peserta
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'active',
                'completed',
                'expired',
                'frozen',
                'cancelled',
            ])->default('pending');

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
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'participant_id',
                'fk_participant_package_participant'
            );

            $table->index(
                'package_id',
                'fk_participant_package_package'
            );

            $table->index(
                'order_item_id',
                'fk_participant_package_order_item'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'participant_id',
                'fk_participant_package_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'package_id',
                'fk_participant_package_package'
            )
                ->references('id')
                ->on('packages')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'order_item_id',
                'fk_participant_package_order_item'
            )
                ->references('id')
                ->on('order_items')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_packages');
    }
};
