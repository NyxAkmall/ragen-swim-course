
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur awal bookings sesuai database
     * MySQL kelas_renang yang sudah berjalan.
     *
     * Perubahan berikutnya dibuat melalui
     * migration tambahan.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            /*
            |--------------------------------------------------------------------------
            | Primary Key dan Relasi
            |--------------------------------------------------------------------------
            */

            $table->id();

            $table->unsignedBigInteger('participant_id');

            $table->unsignedBigInteger('participant_package_id');

            $table->unsignedBigInteger('class_schedule_id');

            /*
            |--------------------------------------------------------------------------
            | Identitas Booking
            |--------------------------------------------------------------------------
            */

            $table->string('booking_code', 30);

            /*
            |--------------------------------------------------------------------------
            | Status dan Pemotongan Sesi
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
                'rescheduled',
            ])->default('pending');

            $table->boolean('session_deducted')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Waktu Booking dan Pembatalan
            |--------------------------------------------------------------------------
            */

            $table->timestamp('booked_at')
                ->useCurrent();

            $table->dateTime('cancelled_at')
                ->nullable();

            $table->text('cancellation_reason')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Riwayat Penjadwalan Ulang
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('previous_booking_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Catatan Tambahan
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
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

            $table->unique('booking_code', 'booking_code');

            $table->index(
                'previous_booking_id',
                'fk_booking_previous'
            );

            $table->index(
                'participant_id',
                'idx_booking_participant'
            );

            $table->index(
                'participant_package_id',
                'idx_booking_package'
            );

            $table->index(
                'class_schedule_id',
                'idx_booking_schedule'
            );

            $table->index(
                'status',
                'idx_booking_status'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'participant_id',
                'fk_booking_participant'
            )
                ->references('id')
                ->on('participants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'participant_package_id',
                'fk_booking_participant_package'
            )
                ->references('id')
                ->on('participant_packages')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'class_schedule_id',
                'fk_booking_schedule'
            )
                ->references('id')
                ->on('class_schedules')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign(
                'previous_booking_id',
                'fk_booking_previous'
            )
                ->references('id')
                ->on('bookings')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel ketika migration dibatalkan.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
