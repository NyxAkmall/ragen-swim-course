<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_tables_exist(): void
    {
        $this->assertSame('mysql', DB::getDriverName());

        foreach ([
            'users',
            'participants',
            'programs',
            'packages',
            'participant_packages',
            'class_schedules',
            'bookings',
            'waiting_lists',
            'attendance',
        ] as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Tabel {$table} tidak ditemukan."
            );
        }
    }

    public function test_booking_unique_code_index_exists(): void
    {
        $indexes = DB::select(
            "SELECT
                INDEX_NAME AS index_name,
                NON_UNIQUE AS non_unique,
                COLUMN_NAME AS column_name,
                SEQ_IN_INDEX AS seq_in_index
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND NON_UNIQUE = 0
            ORDER BY INDEX_NAME, SEQ_IN_INDEX",
            ['bookings']
        );

        $uniqueIndexes = [];

        foreach ($indexes as $index) {
            $uniqueIndexes[$index->index_name][] =
                $index->column_name;
        }

        $found = in_array(
            ['booking_code'],
            array_values($uniqueIndexes),
            true
        );

        $this->assertTrue(
            $found,
            'Unique index booking_code tidak ditemukan.'
        );
    }
}
