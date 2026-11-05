<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Participant;
use App\Models\ParticipantPackage;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentFinalizationService
{
    /**
     * Menyelesaikan pembayaran sebuah order.
     *
     * Seluruh proses dilakukan dalam satu transaksi:
     * - mengunci order dan payment
     * - memvalidasi pembayaran
     * - mengubah payment menjadi paid
     * - mencatat payment log
     * - menutup payment pending lainnya
     * - mengubah order menjadi paid
     * - membuat atau memperbarui invoice
     * - mengaktifkan paket peserta
     * - mencatat pelunasan pendaftaran peserta
     *
     * Jika terjadi kesalahan, transaksi dibatalkan.
     */
    public function finalize(
        Order $order,
        Payment $payment,
        ?int $verifiedBy = null
    ): void {
        DB::transaction(function () use (
            $order,
            $payment,
            $verifiedBy
        ): void {

            /*
            |--------------------------------------------------------------------------
            | Lock Order
            |--------------------------------------------------------------------------
            */

            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            /*
            |--------------------------------------------------------------------------
            | Lock Payment
            |--------------------------------------------------------------------------
            */

            $lockedPayment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            /*
            |--------------------------------------------------------------------------
            | Validasi Relasi Payment dan Order
            |--------------------------------------------------------------------------
            */

            if (
                (int) $lockedPayment->order_id
                !== (int) $lockedOrder->id
            ) {
                throw new RuntimeException(
                    'Pembayaran tidak terhubung dengan order ini.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Idempotency
            |--------------------------------------------------------------------------
            |
            | Jika order dan payment yang sama sudah paid,
            | jangan mengubah tanggal pembayaran.
            |
            | Invoice, paket peserta, dan status pendaftaran
            | dipastikan tetap tersedia tanpa membuat duplikasi.
            */

            if ($lockedOrder->status === 'paid') {

                if ($lockedPayment->status !== 'paid') {
                    throw new RuntimeException(
                        'Order sudah lunas dengan pembayaran lain.'
                    );
                }

                $paidAt = $lockedOrder->paid_at
                    ?? $lockedPayment->paid_at
                    ?? now();

                $this->expireOtherPendingPayments(
                    $lockedOrder,
                    $lockedPayment,
                    $paidAt
                );

                $this->ensureInvoice(
                    $lockedOrder,
                    $paidAt
                );

                $this->ensureParticipantPackages(
                    $lockedOrder,
                    $paidAt
                );

                $this->ensureRegistrationPaid(
                    $lockedOrder,
                    $paidAt
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Status Order
            |--------------------------------------------------------------------------
            */

            if ($lockedOrder->status !== 'pending') {
                throw new RuntimeException(
                    'Order sudah tidak dapat diproses sebagai pembayaran berhasil.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Status Payment
            |--------------------------------------------------------------------------
            */

            if ($lockedPayment->status !== 'pending') {
                throw new RuntimeException(
                    'Pembayaran sudah tidak berstatus menunggu.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Verifikasi Manual
            |--------------------------------------------------------------------------
            |
            | Jika verifiedBy terisi, pembayaran sedang
            | diverifikasi oleh Admin.
            |
            | Bukti pembayaran wajib tersedia.
            */

            if (
                $verifiedBy !== null
                && empty($lockedPayment->proof_image)
            ) {
                throw new RuntimeException(
                    'Bukti pembayaran belum tersedia.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Tolak Bukti Pembayaran yang Sudah Ditolak Admin
            |--------------------------------------------------------------------------
            |
            | Pemeriksaan ini dilakukan pada data yang telah dikunci.
            | Dengan demikian, pemanggilan service dari jalur lain
            | tidak dapat melunasi pembayaran dengan bukti ditolak.
            |
            */

            if (
                $verifiedBy !== null
                && !empty($lockedPayment->rejection_reason)
            ) {
                throw new RuntimeException(
                    'Bukti pembayaran sudah ditolak. Tunggu customer mengunggah bukti pembayaran baru.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Cegah Pembayaran Ganda
            |--------------------------------------------------------------------------
            */

            $hasOtherPaidPayment = Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->where('id', '!=', $lockedPayment->id)
                ->where('status', 'paid')
                ->exists();

            if ($hasOtherPaidPayment) {
                throw new RuntimeException(
                    'Order sudah memiliki pembayaran berhasil lainnya.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Konfirmasi Promo (sekali untuk setiap order)
            |--------------------------------------------------------------------------
            |
            | Lock baris promo sebelum menambah used_count. Checkout juga mengunci
            | baris ini, sehingga pemakaian kuota diserialisasi.
            |
            | Untuk order yang memiliki reservasi, bukti transfer harus diunggah
            | sebelum batas reservasi. Waktu verifikasi admin boleh sesudahnya.
            | Order lama tanpa batas reservasi tetap dapat diselesaikan.
            |
            */
            if ($lockedOrder->promo_code_id !== null) {
                $lockedPromo = PromoCode::query()
                    ->whereKey($lockedOrder->promo_code_id)
                    ->lockForUpdate()
                    ->first();

                if ($lockedPromo === null) {
                    throw new RuntimeException('Kode promo pada pesanan tidak ditemukan.');
                }

                if ($lockedOrder->promo_reserved_until !== null) {
                    $proofUploadedAt = $lockedPayment->proof_uploaded_at;

                    if (
                        $proofUploadedAt === null
                        || $proofUploadedAt->greaterThan($lockedOrder->promo_reserved_until)
                    ) {
                        throw new RuntimeException(
                            'Reservasi promo sudah berakhir sebelum bukti pembayaran diunggah.'
                        );
                    }
                }

                $lockedPromo->increment('used_count');
            }

            $now = now();

            $oldPaymentStatus = $lockedPayment->status;

            /*
            |--------------------------------------------------------------------------
            | Update Payment Menjadi Paid
            |--------------------------------------------------------------------------
            */

            $lockedPayment->update([
                'amount' => $lockedOrder->total_amount,

                'status' => 'paid',

                'verified_at' => $verifiedBy !== null
                    ? $now
                    : null,

                'verified_by' => $verifiedBy,

                'rejection_reason' => null,

                'paid_at' => $now,

                'expired_at' => null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Payment Log
            |--------------------------------------------------------------------------
            */

            PaymentLog::create([
                'payment_id' => $lockedPayment->id,

                'old_status' => $oldPaymentStatus,

                'new_status' => 'paid',

                'gateway_response' => $verifiedBy !== null
                    ? 'Pembayaran diverifikasi oleh admin.'
                    : 'Pembayaran berhasil diproses oleh sistem.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tutup Payment Pending Lain
            |--------------------------------------------------------------------------
            */

            $this->expireOtherPendingPayments(
                $lockedOrder,
                $lockedPayment,
                $now
            );

            /*
            |--------------------------------------------------------------------------
            | Update Order
            |--------------------------------------------------------------------------
            */

            $lockedOrder->update([
                'status' => 'paid',
                'paid_at' => $now,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Invoice
            |--------------------------------------------------------------------------
            */

            $this->ensureInvoice(
                $lockedOrder,
                $now
            );

            /*
            |--------------------------------------------------------------------------
            | Aktivasi Paket Peserta
            |--------------------------------------------------------------------------
            */

            $this->ensureParticipantPackages(
                $lockedOrder,
                $now
            );

            /*
            |--------------------------------------------------------------------------
            | Pelunasan Biaya Pendaftaran
            |--------------------------------------------------------------------------
            |
            | Pendaftaran hanya dicatat lunas setelah
            | pembayaran order berhasil.
            |
            | Proses ini tidak berjalan untuk pembayaran
            | pending, ditolak, atau gagal.
            */

            $this->ensureRegistrationPaid(
                $lockedOrder,
                $now
            );

        }, 3);
    }

    /**
     * Menutup payment pending lainnya setelah
     * satu payment berhasil melunasi order.
     */
    private function expireOtherPendingPayments(
        Order $order,
        Payment $paidPayment,
        mixed $expiredAt
    ): void {
        $pendingPayments = Payment::query()
            ->where('order_id', $order->id)
            ->where('id', '!=', $paidPayment->id)
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        foreach ($pendingPayments as $pendingPayment) {

            $oldStatus = $pendingPayment->status;

            $pendingPayment->update([
                'status' => 'expired',
                'expired_at' => $expiredAt,
                'verified_at' => null,
                'verified_by' => null,
            ]);

            PaymentLog::create([
                'payment_id' => $pendingPayment->id,

                'old_status' => $oldStatus,

                'new_status' => 'expired',

                'gateway_response' =>
                    'Pembayaran ditutup karena order telah lunas melalui pembayaran lain.',
            ]);
        }
    }

    /**
     * Membuat invoice jika belum tersedia.
     *
     * Nomor invoice lama tetap dipertahankan
     * apabila invoice sudah ada.
     */
    private function ensureInvoice(
        Order $order,
        mixed $paidAt
    ): void {
        $invoice = Invoice::query()
            ->where('order_id', $order->id)
            ->lockForUpdate()
            ->first();

        if ($invoice === null) {

            Invoice::create([
                'order_id' => $order->id,

                'invoice_number' =>
                    $this->generateInvoiceNumber(),

                'issued_at' => $paidAt,

                'due_at' => null,

                'status' => 'paid',
            ]);

            return;
        }

        $invoice->update([
            'status' => 'paid',

            'issued_at' => $invoice->issued_at
                ?? $paidAt,
        ]);
    }

    /**
     * Membuat paket peserta berdasarkan order items.
     *
     * Paket tidak dibuat ulang jika order item
     * sudah memiliki participant package.
     */
    private function ensureParticipantPackages(
        Order $order,
        mixed $paidAt
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Reload Order Items
        |--------------------------------------------------------------------------
        */

        $order->load([
            'items.participant',
            'items.package',
        ]);

        $startDate = Carbon::parse(
            $paidAt
        )->startOfDay();

        foreach ($order->items as $item) {

            /*
            |--------------------------------------------------------------------------
            | Cegah Participant Package Ganda
            |--------------------------------------------------------------------------
            */

            $existingParticipantPackage =
                ParticipantPackage::query()
                    ->where(
                        'order_item_id',
                        $item->id
                    )
                    ->lockForUpdate()
                    ->first();

            if ($existingParticipantPackage !== null) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Package
            |--------------------------------------------------------------------------
            */

            $package = $item->package;

            if ($package === null) {
                throw new RuntimeException(
                    'Data paket untuk order item tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Participant
            |--------------------------------------------------------------------------
            */

            if ($item->participant === null) {
                throw new RuntimeException(
                    'Data peserta untuk order item tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Jumlah Paket dan Sesi
            |--------------------------------------------------------------------------
            */

            $quantity = (int) $item->quantity;

            $sessionsPerPackage =
                (int) $package->total_sessions;

            if (
                $quantity < 1
                || $sessionsPerPackage < 1
            ) {
                throw new RuntimeException(
                    'Jumlah paket atau sesi tidak valid.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung Total Sesi
            |--------------------------------------------------------------------------
            */

            $totalSessions =
                $sessionsPerPackage * $quantity;

            /*
            |--------------------------------------------------------------------------
            | Hitung Masa Berlaku
            |--------------------------------------------------------------------------
            */

            $expiryDate =
                $package->validity_days !== null
                    ? $startDate
                        ->copy()
                        ->addDays(
                            max(1, (int) $package->validity_days) - 1
                        )
                    : null;

            /*
            |--------------------------------------------------------------------------
            | Buat Participant Package
            |--------------------------------------------------------------------------
            */

            ParticipantPackage::create([
                'participant_id' =>
                    $item->participant_id,

                'package_id' =>
                    $item->package_id,

                'order_item_id' =>
                    $item->id,

                'total_sessions' =>
                    $totalSessions,

                'used_sessions' =>
                    0,

                'remaining_sessions' =>
                    $totalSessions,

                'start_date' =>
                    $startDate->toDateString(),

                'expiry_date' =>
                    $expiryDate?->toDateString(),

                'status' =>
                    'active',
            ]);
        }
    }

    /**
     * Mencatat pelunasan biaya pendaftaran peserta.
     *
     * Ketentuan:
     *
     * 1. Order harus sudah berstatus paid.
     * 2. Biaya pendaftaran pada order harus > 0.
     * 3. Peserta yang sudah terdaftar tidak diubah lagi.
     * 4. Peserta yang dibebaskan dari biaya tidak diubah.
     * 5. Setiap peserta hanya dicatat satu kali.
     *
     * Semua perubahan berlangsung dalam transaksi
     * yang sama dengan finalisasi pembayaran.
     */
    private function ensureRegistrationPaid(
        Order $order,
        mixed $paidAt
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Validasi Status Order
        |--------------------------------------------------------------------------
        */

        if ($order->status !== 'paid') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Biaya Pendaftaran
        |--------------------------------------------------------------------------
        |
        | Order lama yang tidak mempunyai biaya
        | pendaftaran tidak otomatis dianggap
        | melunasi biaya pendaftaran.
        */

        if ((float) $order->registration_fee <= 0) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Participant ID
        |--------------------------------------------------------------------------
        */

        $participantIds = $order->items()
            ->whereNotNull('participant_id')
            ->distinct()
            ->pluck('participant_id')
            ->sort()
            ->values();

        if ($participantIds->isEmpty()) {
            throw new RuntimeException(
                'Peserta untuk pencatatan biaya pendaftaran tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lock Peserta
        |--------------------------------------------------------------------------
        |
        | Mengunci baris peserta agar pencatatan
        | pelunasan tidak terjadi secara bersamaan.
        */

        $participants = Participant::query()
            ->whereIn('id', $participantIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($participants->count() !== $participantIds->count()) {
            throw new RuntimeException(
                'Sebagian data peserta untuk pelunasan pendaftaran tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Catat Pelunasan
        |--------------------------------------------------------------------------
        */

        foreach ($participants as $participant) {

            if ($participant->registration_paid_at !== null) {
                continue;
            }

            if ($participant->registration_fee_exempt) {
                continue;
            }

            $participant->update([
                'registration_paid_at' => $paidAt,
            ]);
        }
    }

    /**
     * Membuat nomor invoice unik.
     */
    private function generateInvoiceNumber(): string
    {
        do {

            $invoiceNumber =
                'INV-'
                . now()->format('Y')
                . '-'
                . strtoupper(
                    Str::random(8)
                );

        } while (
            Invoice::query()
                ->where(
                    'invoice_number',
                    $invoiceNumber
                )
                ->exists()
        );

        return $invoiceNumber;
    }
}
