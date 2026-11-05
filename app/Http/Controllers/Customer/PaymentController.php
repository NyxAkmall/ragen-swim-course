<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Menyiapkan pembayaran transfer bank manual.
     */
    public function prepareManualTransfer(
        Request $request,
        Order $order
    ): RedirectResponse {
        $this->ensureOrderOwnership(
            $request,
            $order
        );

        /*
        |--------------------------------------------------------------------------
        | Pastikan Rekening Pembayaran Sudah Dikonfigurasi
        |--------------------------------------------------------------------------
        |
        | Validasi ini dilakukan di backend sehingga endpoint tidak dapat
        | digunakan untuk membuat pembayaran transfer manual sebelum
        | rekening resmi Ragen Swim Course tersedia.
        |
        */

        if (!Setting::hasPaymentBankAccount()) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Pembayaran transfer bank belum tersedia karena rekening pembayaran belum dikonfigurasi.'
                );
        }

        if ($order->status === 'paid') {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Pesanan ini sudah dibayar.'
                );
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Pesanan ini sudah tidak dapat dibayar.'
                );
        }

        try {
            DB::transaction(function () use ($order): void {
                $lockedOrder = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if ($lockedOrder->status !== 'pending') {
                    throw new RuntimeException(
                        'Pesanan ini sudah tidak dapat dibayar.'
                    );
                }

                // Reservasi promo harus masih berlaku sebelum pembayaran baru disiapkan.
                // Bukti yang sudah diunggah tepat waktu tetap boleh menunggu admin.
                $this->ensurePromoReservationForPreparation($lockedOrder);

                /*
                |--------------------------------------------------------------------------
                | Periksa Kembali Konfigurasi Rekening
                |--------------------------------------------------------------------------
                |
                | Dilakukan kembali setelah proses masuk ke transaction agar
                | pembuatan pembayaran tetap mengikuti konfigurasi aplikasi
                | yang berlaku.
                |
                */

                if (!Setting::hasPaymentBankAccount()) {
                    throw new RuntimeException(
                        'Pembayaran transfer bank belum tersedia karena rekening pembayaran belum dikonfigurasi.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Gunakan Kembali Pembayaran Transfer Manual
                |--------------------------------------------------------------------------
                */

                $existingPayment = Payment::query()
                    ->where(
                        'order_id',
                        $lockedOrder->id
                    )
                    ->where(
                        'status',
                        'pending'
                    )
                    ->where(
                        'payment_method',
                        'bank_transfer'
                    )
                    ->where(
                        'payment_channel',
                        'manual_transfer'
                    )
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->first();

                if ($existingPayment !== null) {
                    // Bukti yang sudah dikirim tetap menunggu pemeriksaan admin.
                    // Jangan memperpanjang batas waktu saat bukti sedang ditinjau.
                    if (!empty($existingPayment->proof_image)) {
                        return;
                    }

                    // Jika belum ada bukti, customer boleh menyiapkan ulang
                    // pembayaran yang telah melewati batas waktu.
                    if (
                        $existingPayment->expired_at !== null
                        && $existingPayment->expired_at->isPast()
                    ) {
                        $existingPayment->update([
                            'expired_at' => $this->paymentDeadline($lockedOrder),
                        ]);
                    }

                    return;
                }

                Payment::create([
                    'order_id' =>
                        $lockedOrder->id,

                    'payment_code' =>
                        $this->generatePaymentCode(),

                    'payment_method' =>
                        'bank_transfer',

                    'payment_channel' =>
                        'manual_transfer',

                    'amount' =>
                        $lockedOrder->total_amount,

                    'status' =>
                        'pending',

                    'gateway_reference' =>
                        null,

                    'proof_image' =>
                        null,

                    'proof_uploaded_at' =>
                        null,

                    'verified_at' =>
                        null,

                    'verified_by' =>
                        null,

                    'rejection_reason' =>
                        null,

                    'paid_at' =>
                        null,

                    'expired_at' =>
                        $this->paymentDeadline($lockedOrder),
                ]);
            }, 3);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    $exception->getMessage()
                );
        }

        return redirect()
            ->route(
                'customer.orders.show',
                $order
            )
            ->with(
                'success',
                'Pembayaran transfer manual berhasil disiapkan. Silakan transfer dan upload bukti pembayaran.'
            );
    }

    /**
     * Mengunggah bukti transfer.
     *
     * Customer dapat mengirim bukti pertama kali atau
     * mengganti bukti yang sebelumnya ditolak Admin.
     */
    public function uploadProof(
        Request $request,
        Order $order
    ): RedirectResponse {
        $this->ensureOrderOwnership(
            $request,
            $order
        );

        if ($order->status === 'paid') {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Pesanan ini sudah dibayar.'
                );
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Bukti pembayaran tidak dapat dikirim karena pesanan sudah tidak aktif.'
                );
        }

        if (
            $order->promo_code_id !== null
            && (
                $order->promo_reserved_until === null
                || !$order->promo_reserved_until->isFuture()
            )
        ) {
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('warning', 'Batas waktu reservasi promo telah berakhir. Bukti baru tidak dapat dikirim.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi File
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'proof_image' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'proof_image.required' =>
                    'Bukti pembayaran wajib dipilih.',

                'proof_image.file' =>
                    'Bukti pembayaran harus berupa file.',

                'proof_image.image' =>
                    'Bukti pembayaran harus berupa gambar.',

                'proof_image.mimes' =>
                    'Format bukti pembayaran harus JPG, JPEG, PNG, atau WEBP.',

                'proof_image.max' =>
                    'Ukuran bukti pembayaran maksimal 5 MB.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Cari Pembayaran Transfer Manual
        |--------------------------------------------------------------------------
        */

        $payment = Payment::query()
            ->where(
                'order_id',
                $order->id
            )
            ->where(
                'status',
                'pending'
            )
            ->where(
                'payment_method',
                'bank_transfer'
            )
            ->where(
                'payment_channel',
                'manual_transfer'
            )
            ->orderByDesc('id')
            ->first();

        if ($payment === null) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Data pembayaran transfer manual belum tersedia. Silakan pilih metode transfer manual terlebih dahulu.'
                );
        }

        if (
            $payment->expired_at !== null
            && $payment->expired_at->isPast()
            && empty($payment->proof_image)
        ) {
            return redirect()
                ->route('customer.orders.show', $order)
                ->with(
                    'warning',
                    'Batas waktu pembayaran telah lewat. Silakan siapkan kembali pembayaran transfer manual.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cegah Penggantian Bukti yang Sedang Diverifikasi
        |--------------------------------------------------------------------------
        */

        if (
            !empty($payment->proof_image)
            && empty($payment->rejection_reason)
        ) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    'Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi Admin.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Bukti Baru
        |--------------------------------------------------------------------------
        */

        $newProofPath = $validated['proof_image']
            ->store(
                'payment-proofs',
                'local'
            );

        if ($newProofPath === false) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Bukti pembayaran gagal disimpan. Silakan coba kembali.'
                );
        }

        try {
            DB::transaction(function () use (
                $order,
                $payment,
                $newProofPath
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

                if ($lockedOrder->status !== 'pending') {
                    throw new RuntimeException(
                        'Pesanan sudah tidak dapat menerima bukti pembayaran.'
                    );
                }

                if (
                    (int) $lockedPayment->order_id
                    !== (int) $lockedOrder->id
                ) {
                    throw new RuntimeException(
                        'Pembayaran tidak terhubung dengan pesanan ini.'
                    );
                }

                if (
                    $lockedPayment->status !== 'pending'
                    || $lockedPayment->verified_at !== null
                ) {
                    throw new RuntimeException(
                        'Pembayaran sudah tidak dapat menerima bukti baru.'
                    );
                }

                if (
                    $lockedPayment->payment_method
                        !== 'bank_transfer'
                    || $lockedPayment->payment_channel
                        !== 'manual_transfer'
                ) {
                    throw new RuntimeException(
                        'Metode pembayaran tidak sesuai untuk upload bukti transfer.'
                    );
                }

                // Jangan terima bukti baru sesudah reservasi promo habis.
                // Termasuk penggantian bukti yang sebelumnya ditolak admin.
                $this->ensurePromoReservationForUpload($lockedOrder);

                if (
                    $lockedPayment->expired_at !== null
                    && $lockedPayment->expired_at->isPast()
                    && empty($lockedPayment->proof_image)
                ) {
                    throw new RuntimeException(
                        'Batas waktu pembayaran telah lewat. Silakan siapkan kembali pembayaran transfer manual.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Bukti Sebelumnya
                |--------------------------------------------------------------------------
                |
                | Bukti hanya boleh diganti apabila sebelumnya telah
                | ditolak oleh Admin.
                |
                */

                if (
                    !empty($lockedPayment->proof_image)
                    && empty(
                        $lockedPayment->rejection_reason
                    )
                ) {
                    throw new RuntimeException(
                        'Bukti pembayaran sedang menunggu verifikasi Admin.'
                    );
                }

                $oldProofPath =
                    $lockedPayment->proof_image;

                $lockedPayment->update([
                    'proof_image' =>
                        $newProofPath,

                    'proof_uploaded_at' =>
                        now(),

                    'verified_at' =>
                        null,

                    'verified_by' =>
                        null,

                    'rejection_reason' =>
                        null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Hapus Bukti Lama Setelah Commit
                |--------------------------------------------------------------------------
                */

                if (
                    !empty($oldProofPath)
                    && $oldProofPath
                        !== $newProofPath
                ) {
                    DB::afterCommit(
                        static function () use (
                            $oldProofPath
                        ): void {
                            // Hapus dari kedua disk selama masa transisi.
                            // Path bukti lama dapat berada di public
                            // atau local.
                            foreach (['local', 'public'] as $diskName) {
                                $disk = Storage::disk($diskName);

                                if ($disk->exists($oldProofPath)) {
                                    $disk->delete($oldProofPath);
                                }
                            }
                        }
                    );
                }
            }, 3);
        } catch (RuntimeException $exception) {
            Storage::disk('local')
                ->delete($newProofPath);

            return redirect()
                ->route(
                    'customer.orders.show',
                    $order
                )
                ->with(
                    'warning',
                    $exception->getMessage()
                );
        } catch (Throwable $exception) {
            Storage::disk('local')
                ->delete($newProofPath);

            throw $exception;
        }

        return redirect()
            ->route(
                'customer.orders.show',
                $order
            )
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi Admin.'
            );
    }

    /**
     * Tenggat pembayaran tidak boleh melebihi reservasi promo.
     */
    private function paymentDeadline(Order $order): \Carbon\CarbonInterface
    {
        $deadline = now()->addDay();

        if (
            $order->promo_code_id !== null
            && $order->promo_reserved_until !== null
            && $order->promo_reserved_until->lessThan($deadline)
        ) {
            return $order->promo_reserved_until;
        }

        return $deadline;
    }

    /**
     * Untuk pembayaran baru, reservasi harus berlaku.
     * Pembayaran dengan bukti yang sedang diverifikasi tidak diubah.
     */
    private function ensurePromoReservationForPreparation(Order $order): void
    {
        if ($order->promo_code_id === null) {
            return;
        }

        if ($order->promo_reserved_until === null) {
            throw new RuntimeException(
                'Pesanan promo ini belum memiliki batas reservasi. Hubungi admin.'
            );
        }

        if ($order->promo_reserved_until->isFuture()) {
            return;
        }

        // whereColumn membutuhkan tabel orders di query.
        $hasTimelyProof = Payment::query()
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('payments.order_id', $order->id)
            ->where('payments.status', 'pending')
            ->whereNotNull('payments.proof_image')
            ->whereNotNull('payments.proof_uploaded_at')
            ->whereNull('payments.rejection_reason')
            ->whereColumn(
                'payments.proof_uploaded_at',
                '<=',
                'orders.promo_reserved_until'
            )
            ->exists();

        if (!$hasTimelyProof) {
            throw new RuntimeException(
                'Reservasi promo sudah berakhir. Silakan buat pesanan baru.'
            );
        }
    }

    /**
     * Bukti baru atau pengganti wajib diunggah sebelum batas reservasi.
     */
    private function ensurePromoReservationForUpload(Order $order): void
    {
        if ($order->promo_code_id === null) {
            return;
        }

        if (
            $order->promo_reserved_until === null
            || !$order->promo_reserved_until->isFuture()
        ) {
            throw new RuntimeException(
                'Batas waktu reservasi promo telah berakhir. Bukti baru tidak dapat dikirim.'
            );
        }
    }

    /**
     * Memastikan order milik customer yang sedang login.
     */
    private function ensureOrderOwnership(
        Request $request,
        Order $order
    ): void {
        abort_unless(
            (int) $order->user_id
                === (int) $request->user()->id,
            403
        );
    }

    /**
     * Membuat kode pembayaran unik.
     */
    private function generatePaymentCode(): string
    {
        do {
            $paymentCode =
                'PAY-'
                . now()->format('Y')
                . '-'
                . strtoupper(
                    Str::random(8)
                );
        } while (
            Payment::query()
                ->where(
                    'payment_code',
                    $paymentCode
                )
                ->exists()
        );

        return $paymentCode;
    }
}
