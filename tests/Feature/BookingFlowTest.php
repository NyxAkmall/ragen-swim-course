<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingFlowTest extends TestCase
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
            'name' => 'Test Customer',
            'email' => 'customer-test@example.test',
            'password' => 'password123',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $coachUser = User::create([
            'name' => 'Test Coach',
            'email' => 'coach-test@example.test',
            'password' => 'password123',
            'role' => 'coach',
            'status' => 'active',
        ]);

        $participant = DB::table('participants')->insertGetId([
            'user_id' => $customer->id,
            'full_name' => 'Peserta Test',
            'status' => 'active',
        ]);

        $program = DB::table('programs')->insertGetId([
            'name' => 'Program Test',
            'slug' => 'program-test',
            'status' => 'active',
        ]);

        $package = DB::table('packages')->insertGetId([
            'program_id' => $program,
            'name' => 'Paket Test',
            'total_sessions' => 4,
            'price' => 500000,
            'status' => 'active',
        ]);

        $participantPackage = DB::table('participant_packages')
            ->insertGetId([
                'participant_id' => $participant,
                'package_id' => $package,
                'total_sessions' => 4,
                'used_sessions' => 0,
                'remaining_sessions' => 4,
                'status' => 'active',
            ]);

        $coach = DB::table('coaches')->insertGetId([
            'user_id' => $coachUser->id,
            'status' => 'active',
        ]);

        $location = DB::table('locations')->insertGetId([
            'name' => 'Kolam Test',
            'address' => 'Alamat khusus pengujian',
            'status' => 'active',
        ]);

        $schedule = DB::table('class_schedules')->insertGetId([
            'program_id' => $program,
            'coach_id' => $coach,
            'location_id' => $location,
            'class_date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 2,
            'status' => 'scheduled',
        ]);

        return compact(
            'customer',
            'participant',
            'participantPackage',
            'schedule'
        );
    }

    private function book(array $data)
    {
        return $this->actingAs($data['customer'])
            ->post(route('customer.bookings.store'), [
                'participant_package_id' =>
                    $data['participantPackage'],
                'class_schedule_id' => $data['schedule'],
            ]);
    }

    public function test_customer_can_create_booking(): void
    {
        $data = $this->fixture();

        $response = $this->book($data);

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'participant_id' => $data['participant'],
            'class_schedule_id' => $data['schedule'],
            'status' => 'pending',
            'session_deducted' => 0,
        ]);
    }

    public function test_duplicate_booking_is_rejected(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $this->book($data)->assertStatus(422);

        $this->assertSame(
            1,
            DB::table('bookings')->count()
        );
    }

    public function test_inactive_package_is_rejected(): void
    {
        $data = $this->fixture();

        DB::table('participant_packages')
            ->where('id', $data['participantPackage'])
            ->update(['status' => 'expired']);

        $this->book($data)->assertStatus(422);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_exhausted_sessions_are_rejected(): void
    {
        $data = $this->fixture();

        DB::table('participant_packages')
            ->where('id', $data['participantPackage'])
            ->update(['remaining_sessions' => 0]);

        $this->book($data)->assertStatus(422);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_full_class_is_rejected(): void
    {
        $data = $this->fixture();

        DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->update(['capacity' => 1]);

        $otherCustomer = User::create([
            'name' => 'Customer Lain',
            'email' => 'other-customer@example.test',
            'password' => 'password123',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $otherParticipant = DB::table('participants')
            ->insertGetId([
                'user_id' => $otherCustomer->id,
                'full_name' => 'Peserta Lain',
                'status' => 'active',
            ]);

        DB::table('bookings')->insert([
            'participant_id' => $otherParticipant,
            'participant_package_id' => $data['participantPackage'],
            'class_schedule_id' => $data['schedule'],
            'booking_code' => 'BKG-TEST-FULL',
            'status' => 'confirmed',
            'session_deducted' => false,
        ]);

        $this->book($data)->assertStatus(422);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_reserved_session_cannot_be_reused(): void
    {
        $data = $this->fixture();

        DB::table('participant_packages')
            ->where('id', $data['participantPackage'])
            ->update([
                'total_sessions' => 1,
                'remaining_sessions' => 1,
            ]);

        $this->book($data)->assertRedirect();

        $schedule = (array) DB::table('class_schedules')
            ->where('id', $data['schedule'])
            ->first();

        unset($schedule['id']);

        $schedule['class_date'] =
            now()->addDays(8)->toDateString();

        $schedule['start_time'] = '11:00:00';
        $schedule['end_time'] = '12:00:00';

        $data['schedule'] = DB::table('class_schedules')
            ->insertGetId($schedule);

        $this->book($data)->assertStatus(422);

        $this->assertDatabaseCount('bookings', 1);
    }

    private function createAlternativeSchedule(
        int $scheduleId,
        int $capacity = 2
    ): int {
        $schedule = (array) DB::table('class_schedules')
            ->where('id', $scheduleId)
            ->first();

        unset($schedule['id']);

        $schedule['class_date'] =
            now()->addDays(9)->toDateString();

        $schedule['start_time'] = '13:00:00';
        $schedule['end_time'] = '14:00:00';
        $schedule['capacity'] = $capacity;

        return DB::table('class_schedules')
            ->insertGetId($schedule);
    }

    public function test_reschedule_creates_new_booking(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $oldBooking = DB::table('bookings')->first();

        $newScheduleId = $this->createAlternativeSchedule(
            $data['schedule']
        );

        $response = $this->actingAs($data['customer'])
            ->post(
                route(
                    'customer.bookings.reschedule.process',
                    $oldBooking->id
                ),
                ['class_schedule_id' => $newScheduleId]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $oldBooking->id,
            'status' => 'rescheduled',
        ]);

        $this->assertDatabaseHas('bookings', [
            'previous_booking_id' => $oldBooking->id,
            'class_schedule_id' => $newScheduleId,
            'status' => 'pending',
            'session_deducted' => 0,
        ]);

        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_reschedule_to_same_schedule_is_rejected(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $booking = DB::table('bookings')->first();

        $this->actingAs($data['customer'])
            ->post(
                route(
                    'customer.bookings.reschedule.process',
                    $booking->id
                ),
                ['class_schedule_id' => $data['schedule']]
            )
            ->assertStatus(422);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_reschedule_to_full_schedule_is_rejected(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $booking = DB::table('bookings')->first();

        $newScheduleId = $this->createAlternativeSchedule(
            $data['schedule'],
            0
        );

        $this->actingAs($data['customer'])
            ->post(
                route(
                    'customer.bookings.reschedule.process',
                    $booking->id
                ),
                ['class_schedule_id' => $newScheduleId]
            )
            ->assertStatus(422);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_reschedule_does_not_deduct_sessions(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $booking = DB::table('bookings')->first();

        $newScheduleId = $this->createAlternativeSchedule(
            $data['schedule']
        );

        $this->actingAs($data['customer'])
            ->post(
                route(
                    'customer.bookings.reschedule.process',
                    $booking->id
                ),
                ['class_schedule_id' => $newScheduleId]
            )
            ->assertRedirect();

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'used_sessions' => 0,
            'remaining_sessions' => 4,
        ]);

        $this->assertSame(
            0,
            DB::table('bookings')
                ->where('session_deducted', true)
                ->count()
        );
    }

    public function test_cancel_booking(): void
    {
        $data = $this->fixture();

        $this->book($data)->assertRedirect();

        $booking = DB::table('bookings')->first();

        $response = $this->actingAs($data['customer'])
            ->patch(
                route('customer.bookings.cancel', $booking->id),
                ['cancellation_reason' => 'Perubahan jadwal']
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'session_deducted' => 0,
        ]);
    }
}
