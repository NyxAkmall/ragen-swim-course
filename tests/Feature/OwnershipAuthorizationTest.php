<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class OwnershipAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (
            app()->environment() !== 'testing' ||
            config('database.default') !== 'mysql' ||
            config('database.connections.mysql.database') !== 'ragenswi_ragen_swim_test' ||
            DB::connection()->getDriverName() !== 'mysql' ||
            DB::connection()->getDatabaseName() !== 'ragenswi_ragen_swim_test'
        ) {
            throw new \RuntimeException(
                'TEST DIBATALKAN: database bukan MySQL testing.'
            );
        }
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Security Test ' . ucfirst($role),
            'email' => Str::uuid() . '@example.test',
            'password' => 'password123',
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function fixture(): array
    {
        $owner = $this->user('customer');
        $attacker = $this->user('customer');
        $coachOwner = $this->user('coach');
        $coachAttacker = $this->user('coach');

        $participant = DB::table('participants')->insertGetId([
            'user_id' => $owner->id,
            'full_name' => 'Peserta Security Test',
            'status' => 'active',
        ]);

        $program = DB::table('programs')->insertGetId([
            'name' => 'Security Program',
            'slug' => 'security-' . Str::random(10),
            'status' => 'active',
        ]);

        $package = DB::table('packages')->insertGetId([
            'program_id' => $program,
            'name' => 'Security Package',
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

        $coachId = DB::table('coaches')->insertGetId([
            'user_id' => $coachOwner->id,
            'status' => 'active',
        ]);

        DB::table('coaches')->insert([
            'user_id' => $coachAttacker->id,
            'status' => 'active',
        ]);

        $location = DB::table('locations')->insertGetId([
            'name' => 'Security Pool',
            'address' => 'Test Location',
            'status' => 'active',
        ]);

        $schedule = DB::table('class_schedules')->insertGetId([
            'program_id' => $program,
            'coach_id' => $coachId,
            'location_id' => $location,
            'class_date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 2,
            'status' => 'scheduled',
        ]);

        $otherSchedule = DB::table('class_schedules')->insertGetId([
            'program_id' => $program,
            'coach_id' => $coachId,
            'location_id' => $location,
            'class_date' => now()->addDays(8)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 2,
            'status' => 'scheduled',
        ]);

        $booking = DB::table('bookings')->insertGetId([
            'participant_id' => $participant,
            'participant_package_id' => $participantPackage,
            'class_schedule_id' => $schedule,
            'booking_code' => 'SEC-' . Str::upper(Str::random(12)),
            'status' => 'confirmed',
            'session_deducted' => false,
            'booked_at' => now(),
        ]);

        return compact(
            'owner',
            'attacker',
            'coachOwner',
            'coachAttacker',
            'participantPackage',
            'booking',
            'otherSchedule'
        );
    }

    private function assertBookingUnchanged(array $data): void
    {
        $this->assertDatabaseHas('bookings', [
            'id' => $data['booking'],
            'status' => 'confirmed',
            'session_deducted' => 0,
        ]);

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'used_sessions' => 0,
            'remaining_sessions' => 4,
        ]);

        $this->assertDatabaseMissing('attendance', [
            'booking_id' => $data['booking'],
        ]);
    }

    public function test_customer_cannot_view_another_customers_booking(): void
    {
        $data = $this->fixture();

        $this->actingAs($data['attacker'])
            ->get(route('customer.bookings.show', $data['booking']))
            ->assertNotFound();

        $this->assertBookingUnchanged($data);
    }

    public function test_customer_cannot_open_another_customers_reschedule(): void
    {
        $data = $this->fixture();

        $this->actingAs($data['attacker'])
            ->get(route('customer.bookings.reschedule', $data['booking']))
            ->assertNotFound();

        $this->assertBookingUnchanged($data);
    }

    public function test_customer_cannot_cancel_another_customers_booking(): void
    {
        $data = $this->fixture();

        $this->actingAs($data['attacker'])
            ->patch(route('customer.bookings.cancel', $data['booking']), [
                'cancellation_reason' => 'Unauthorized cancellation',
            ])
            ->assertForbidden();

        $this->assertBookingUnchanged($data);
    }

    public function test_customer_cannot_reschedule_another_customers_booking(): void
    {
        $data = $this->fixture();

        $this->actingAs($data['attacker'])
            ->post(
                route('customer.bookings.reschedule.process', $data['booking']),
                ['class_schedule_id' => $data['otherSchedule']]
            )
            ->assertForbidden();

        $this->assertBookingUnchanged($data);

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_coach_cannot_attend_another_coachs_booking(): void
    {
        $data = $this->fixture();

        $this->actingAs($data['coachAttacker'])
            ->post(route('coach.attendance.store', $data['booking']), [
                'status' => 'present',
            ])
            ->assertForbidden();

        $this->assertBookingUnchanged($data);
    }
}
