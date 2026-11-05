<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
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

    private function fixture(
        string $bookingStatus = 'confirmed',
        bool $future = false
    ): array {
        $customer = User::create([
            'name' => 'Customer Attendance',
            'email' => 'customer-attendance@example.test',
            'password' => 'password123',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $coachUser = User::create([
            'name' => 'Coach Attendance',
            'email' => 'coach-attendance@example.test',
            'password' => 'password123',
            'role' => 'coach',
            'status' => 'active',
        ]);

        $participantId = DB::table('participants')->insertGetId([
            'user_id' => $customer->id,
            'full_name' => 'Peserta Attendance',
            'status' => 'active',
        ]);

        $programId = DB::table('programs')->insertGetId([
            'name' => 'Program Attendance',
            'slug' => 'program-attendance',
            'duration_minutes' => 60,
            'status' => 'active',
        ]);

        $packageId = DB::table('packages')->insertGetId([
            'program_id' => $programId,
            'name' => 'Paket Attendance',
            'total_sessions' => 4,
            'price' => 100000,
            'validity_days' => 30,
            'status' => 'active',
        ]);

        $participantPackageId = DB::table('participant_packages')
            ->insertGetId([
                'participant_id' => $participantId,
                'package_id' => $packageId,
                'total_sessions' => 4,
                'used_sessions' => 0,
                'remaining_sessions' => 4,
                'status' => 'active',
            ]);

        $coachId = DB::table('coaches')->insertGetId([
            'user_id' => $coachUser->id,
            'status' => 'active',
        ]);

        $locationId = DB::table('locations')->insertGetId([
            'name' => 'Kolam Attendance',
            'address' => 'Lokasi Pengujian Attendance',
            'status' => 'active',
        ]);

        $scheduleId = DB::table('class_schedules')->insertGetId([
            'program_id' => $programId,
            'coach_id' => $coachId,
            'location_id' => $locationId,
            'class_date' => $future
                ? now()->addDays(7)->toDateString()
                : now()->subDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 2,
            'status' => 'scheduled',
        ]);

        $bookingId = DB::table('bookings')->insertGetId([
            'participant_id' => $participantId,
            'participant_package_id' => $participantPackageId,
            'class_schedule_id' => $scheduleId,
            'booking_code' => 'BKG-ATTENDANCE-TEST',
            'status' => $bookingStatus,
            'session_deducted' => false,
            'booked_at' => now()->subDays(2),
        ]);

        return [
            'coachUser' => $coachUser,
            'booking' => $bookingId,
            'participantPackage' => $participantPackageId,
        ];
    }

    private function submitAttendance(
        array $data,
        string $status
    ) {
        return $this->actingAs($data['coachUser'])
            ->post(
                route('coach.attendance.store', $data['booking']),
                ['status' => $status]
            );
    }

    public function test_present_deducts_one_session(): void
    {
        $data = $this->fixture();

        $this->submitAttendance($data, 'present')
            ->assertRedirect();

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'used_sessions' => 1,
            'remaining_sessions' => 3,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $data['booking'],
            'status' => 'completed',
            'session_deducted' => 1,
        ]);

        $this->assertDatabaseHas('attendance', [
            'booking_id' => $data['booking'],
            'status' => 'present',
        ]);
    }

    public function test_non_present_statuses_do_not_deduct(): void
    {
        foreach (['permission', 'sick', 'absent'] as $status) {
            $data = $this->fixture();

            $this->submitAttendance($data, $status)
                ->assertRedirect();

            $this->assertDatabaseHas('participant_packages', [
                'id' => $data['participantPackage'],
                'used_sessions' => 0,
                'remaining_sessions' => 4,
            ]);

            $this->assertDatabaseHas('bookings', [
                'id' => $data['booking'],
                'status' => 'completed',
                'session_deducted' => 0,
            ]);

            $this->assertDatabaseHas('attendance', [
                'booking_id' => $data['booking'],
                'status' => $status,
            ]);

            DB::table('attendance')->delete();
            DB::table('bookings')->delete();
            DB::table('class_schedules')->delete();
            DB::table('participant_packages')->delete();
            DB::table('packages')->delete();
            DB::table('locations')->delete();
            DB::table('coaches')->delete();
            DB::table('programs')->delete();
            DB::table('participants')->delete();
            DB::table('users')->delete();
        }
    }

    public function test_duplicate_attendance_is_rejected(): void
    {
        $data = $this->fixture();

        $this->submitAttendance($data, 'present')
            ->assertRedirect();

        $this->submitAttendance($data, 'present')
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'used_sessions' => 1,
            'remaining_sessions' => 3,
        ]);

        $this->assertDatabaseCount('attendance', 1);
    }

    public function test_pending_booking_cannot_be_attended(): void
    {
        $data = $this->fixture('pending');

        $this->submitAttendance($data, 'present')
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('attendance', 0);

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'remaining_sessions' => 4,
        ]);
    }

    public function test_attendance_before_class_starts_is_rejected(): void
    {
        $data = $this->fixture('confirmed', true);

        $this->submitAttendance($data, 'present')
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('attendance', 0);

        $this->assertDatabaseHas('participant_packages', [
            'id' => $data['participantPackage'],
            'remaining_sessions' => 4,
        ]);
    }
}
