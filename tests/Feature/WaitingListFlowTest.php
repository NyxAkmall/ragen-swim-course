<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WaitingListService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WaitingListFlowTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();

        if (
            app()->environment() !== 'testing' ||
            config('database.default') !== 'mysql' ||
            config('database.connections.mysql.database') !== 'ragenswi_ragen_swim_test' ||
            $connection->getDriverName() !== 'mysql' ||
            $connection->getDatabaseName() !== 'ragenswi_ragen_swim_test'
        ) {
            throw new \RuntimeException(
                'TEST DIBATALKAN: database bukan MySQL testing.'
            );
        }
    }

    private function fixture(): array
    {
        $customer = User::create([
            'name' => 'Customer Waiting',
            'email' => 'waiting@example.test',
            'password' => 'password123',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $participantId = DB::table('participants')->insertGetId([
            'user_id' => $customer->id,
            'full_name' => 'Peserta Waiting',
            'status' => 'active',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'Program Waiting Test',
            'slug' => 'program-waiting-test',
            'duration_minutes' => 60,
            'status' => 'active',
        ]);

        $coachUser = User::create([
            'name' => 'Coach Waiting',
            'email' => 'coach-waiting@example.test',
            'password' => 'password123',
            'role' => 'coach',
            'status' => 'active',
        ]);

        $coachId = DB::table('coaches')->insertGetId([
            'user_id' => $coachUser->id,
            'status' => 'active',
        ]);

        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Kolam Waiting Test',
            'address' => 'Lokasi Pengujian Ragen Swim Course',
            'status' => 'active',
        ]);

        $scheduleId = DB::table('class_schedules')->insertGetId([
            'program_id' => $programId,
            'coach_id' => $coachId,
            'location_id' => $locationId,
            'class_date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 1,
            'status' => 'scheduled',
        ]);

        return [
            'participant' => $participantId,
            'schedule' => $scheduleId,
        ];
    }

    private function fillSchedule(array $data): void
    {
        $programId = DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->value('program_id');

        $packageId = DB::table('packages')->insertGetId([
            'program_id' => $programId,
            'name' => 'Paket Waiting Test',
            'total_sessions' => 4,
            'price' => 100000,
            'validity_days' => 30,
            'status' => 'active',
        ]);

        $participantPackageId = DB::table('participant_packages')
            ->insertGetId([
                'participant_id' => $data['participant'],
                'package_id' => $packageId,
                'total_sessions' => 4,
                'used_sessions' => 0,
                'remaining_sessions' => 4,
                'status' => 'active',
            ]);

        DB::table('bookings')->insert([
            'participant_id' => $data['participant'],
            'participant_package_id' => $participantPackageId,
            'class_schedule_id' => $data['schedule'],
            'booking_code' => 'BKG-WAITING-FULL',
            'status' => 'confirmed',
            'session_deducted' => false,
        ]);
    }

    public function test_join_rejected_when_slot_available(): void
    {
        $data = $this->fixture();

        $this->expectException(ValidationException::class);

        app(WaitingListService::class)->join(
            $data['participant'],
            $data['schedule']
        );
    }

    public function test_join_succeeds_when_class_full(): void
    {
        $data = $this->fixture();

        DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->update(['capacity' => 0]);

        $entry = app(WaitingListService::class)->join(
            $data['participant'],
            $data['schedule']
        );

        $this->assertSame('waiting', $entry->status);

        $this->assertDatabaseHas('waiting_lists', [
            'id' => $entry->id,
            'status' => 'waiting',
            'queue_number' => 1,
        ]);
    }

    public function test_admin_cannot_offer_without_available_slot(): void
    {
        $data = $this->fixture();

        DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->update(['capacity' => 0]);

        $entry = app(WaitingListService::class)->join(
            $data['participant'],
            $data['schedule']
        );

        $this->expectException(ValidationException::class);

        app(WaitingListService::class)
            ->offerSelectedByAdmin($entry->id);
    }

    public function test_accept_offer_creates_booking(): void
    {
        $data = $this->fixture();

        $programId = DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->value('program_id');

        $packageId = DB::table('packages')->insertGetId([
            'program_id' => $programId,
            'name' => 'Paket Accept Offer',
            'total_sessions' => 4,
            'price' => 100000,
            'validity_days' => 30,
            'status' => 'active',
        ]);

        $participantPackageId = DB::table('participant_packages')
            ->insertGetId([
                'participant_id' => $data['participant'],
                'package_id' => $packageId,
                'total_sessions' => 4,
                'used_sessions' => 0,
                'remaining_sessions' => 4,
                'status' => 'active',
            ]);

        DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->update(['capacity' => 0]);

        $service = app(WaitingListService::class);

        $entry = $service->join(
            $data['participant'],
            $data['schedule']
        );

        DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->update(['capacity' => 1]);

        $offered = $service->offerSelectedByAdmin($entry->id);

        $this->assertSame('offered', $offered->status);

        $customerId = DB::table('participants')
            ->where('id', $data['participant'])
            ->value('user_id');

        $booking = $service->acceptOffer(
            $entry->id,
            $customerId,
            $participantPackageId
        );

        $this->assertSame('pending', $booking->status);
        $this->assertFalse((bool) $booking->session_deducted);

        $this->assertDatabaseHas('waiting_lists', [
            'id' => $entry->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'class_schedule_id' => $data['schedule'],
            'participant_package_id' => $participantPackageId,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('participant_packages', [
            'id' => $participantPackageId,
            'used_sessions' => 0,
            'remaining_sessions' => 4,
        ]);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_expired_offer_releases_reservation(): void
    {
        $data = $this->fixture();

        $entryId = DB::table('waiting_lists')->insertGetId([
            'participant_id' => $data['participant'],
            'class_schedule_id' => $data['schedule'],
            'status' => 'offered',
            'queue_number' => 1,
            'offered_at' => now()->subHours(3),
            'expires_at' => now()->subMinute(),
        ]);

        $service = app(WaitingListService::class);

        $expired = $service->refreshQueue($data['schedule']);

        $this->assertSame(1, $expired);

        $this->assertDatabaseHas('waiting_lists', [
            'id' => $entryId,
            'status' => 'expired',
        ]);

        $this->assertSame(
            0,
            $service->getReservedOfferCount($data['schedule'])
        );
    }
}
