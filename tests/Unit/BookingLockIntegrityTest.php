<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BookingLockIntegrityTest extends TestCase
{
    private function source(string $file): string
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $file;

        $this->assertFileExists($path);

        return file_get_contents($path);
    }

    public function test_customer_booking_has_transaction_and_locks(): void
    {
        $source = $this->source(
            'app/Http/Controllers/Customer/BookingController.php'
        );

        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('ParticipantPackage::query()', $source);
        $this->assertStringContainsString('ClassSchedule::query()', $source);
        $this->assertStringContainsString('Participant::query()', $source);
    }

    public function test_waiting_list_has_transaction_and_locks(): void
    {
        $source = $this->source(
            'app/Services/WaitingListService.php'
        );

        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('ClassSchedule::query()', $source);
        $this->assertStringContainsString('ParticipantPackage::query()', $source);
    }

    public function test_admin_booking_has_transaction_locks(): void
    {
        $source = $this->source(
            'app/Http/Controllers/Admin/BookingController.php'
        );

        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('lockForUpdate()', $source);
    }

    public function test_coach_attendance_protects_session_deduction(): void
    {
        $source = $this->source(
            'app/Http/Controllers/Coach/AttendanceController.php'
        );

        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('ParticipantPackage::query()', $source);
    }

    public function test_checkout_protects_participant_and_package(): void
    {
        $source = $this->source(
            'app/Http/Controllers/Customer/CheckoutController.php'
        );

        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('Participant::query()', $source);
        $this->assertStringContainsString('Package::query()', $source);
    }

    public function test_payment_finalization_protects_payment_records(): void
    {
        $source = $this->source(
            'app/Services/PaymentFinalizationService.php'
        );

        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('lockForUpdate()', $source);
        $this->assertStringContainsString('Order::query()', $source);
        $this->assertStringContainsString('Payment::query()', $source);
    }
}
