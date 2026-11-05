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
        $this->assertSame('sqlite', DB::getDriverName());

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
            "PRAGMA index_list('bookings')"
        );

        $uniqueIndexes = array_filter(
            $indexes,
            fn ($index) => (int) $index->unique === 1
        );

        $found = false;

        foreach ($uniqueIndexes as $index) {
            $columns = DB::select(
                "PRAGMA index_info('"
                . str_replace("'", "''", $index->name)
                . "')"
            );

            $names = array_map(
                fn ($column) => $column->name,
                $columns
            );

            if ($names === ['booking_code']) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            'Unique index booking_code tidak ditemukan.'
        );
    }
}
