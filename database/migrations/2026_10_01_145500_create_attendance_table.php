
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal attendance sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Satu booking hanya boleh mempunyai
     * satu catatan kehadiran.
     *
     * Perubahan struktur berikutnya dibuat
     * melalui migration tambahan.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
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
            | Relasi Booking
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('booking_id');

            /*
            |--------------------------------------------------------------------------
            | Status Kehadiran
            |--------------------------------------------------------------------------
            |
            | present    = Hadir
            | permission = Izin
            | sick       = Sakit
            | absent     = Tidak hadir
            |
            */

            $table->enum('status', [
                'present',
                'permission',
                'sick',
                'absent',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Catatan Coach
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Waktu Pencatatan
            |--------------------------------------------------------------------------
            */

            $table->dateTime('checked_at')
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
            | Unique Index
            |--------------------------------------------------------------------------
            |
            | Mencegah pencatatan kehadiran
            | lebih dari satu kali untuk booking
            | yang sama.
            |
            */

            $table->unique('booking_id', 'booking_id');

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            |
            | Booking yang memiliki catatan
            | kehadiran tidak boleh dihapus.
            |
            */

            $table->foreign(
                'booking_id',
                'fk_attendance_booking'
            )
                ->references('id')
                ->on('bookings')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
