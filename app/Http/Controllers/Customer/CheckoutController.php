<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Models\Participant;
use App\Models\Program;
use App\Models\PromoCode;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Durasi reservasi promo dalam jam.
     */
    private const PROMO_RESERVATION_HOURS = 24;

    /**
     * Menampilkan halaman checkout.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $validated = $request->validate(
            $this->checkoutRules(false),
            $this->validationMessages()
        );

        $participant = $this->findParticipant(
            (int) $validated['participant_id'],
            (int) $request->user()->id
        );

        if ($participant === null) {
            return redirect()
                ->route('customer.packages.browse')
                ->withErrors([
                    'participant' =>
                        'Peserta tidak ditemukan atau tidak dapat digunakan.',
                ]);
        }

        $package = $this->findPackage(
            (int) $validated['package_id']
        );

        if ($package === null) {
            return redirect()
                ->route('customer.packages.browse')
                ->withErrors([
                    'package' =>
                        'Paket tersebut sedang tidak tersedia.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cegah Tagihan Pendaftaran Ganda
        |--------------------------------------------------------------------------
        */

        if ($participant->needsRegistrationFee()) {
            $existingOrder = $this->findPendingRegistrationOrder(
                $participant->id
            );

            if ($existingOrder !== null) {
                return redirect()
                    ->route('customer.orders.show', $existingOrder)
                    ->with(
                        'warning',
                        'Peserta masih memiliki pesanan yang memuat biaya '
                        . 'pendaftaran. Selesaikan pesanan tersebut terlebih dahulu.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Estimasi Harga dan Promo
        |--------------------------------------------------------------------------
        */

        $subtotal = $package->getEffectivePrice();

        if ($package->hasActivePromo() && trim((string) ($validated['promo_code'] ?? "")) !== "") {
            return back()->withInput()->withErrors(['promo_code' => 'Paket ini sedang mendapatkan harga promo. Kode voucher tidak dapat digunakan bersamaan.']);
        }

        $promoResult = $this->resolvePromo(
            $validated['promo_code'] ?? null,
            $subtotal
        );

        if ($promoResult['error'] !== null) {
            return back()
                ->withInput()
                ->withErrors([
                    'promo_code' => $promoResult['error'],
                ]);
        }

        $promoCode = $promoResult['promo'];
        $discountAmount = $promoResult['discount'];

        /*
        |--------------------------------------------------------------------------
        | Biaya Pendaftaran
        |--------------------------------------------------------------------------
        */

        $registrationFee = $participant->needsRegistrationFee()
            ? Setting::registrationFee()
            : 0;

        $totalAmount = max(
            0,
            $subtotal - $discountAmount
        ) + $registrationFee;

        return view(
            'customer.checkout.show',
            compact(
                'participant',
                'package',
                'subtotal',
                'discountAmount',
                'registrationFee',
                'totalAmount',
                'promoCode'
            )
        );
    }

    /**
     * Membuat pesanan dan mereservasi kuota promo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->checkoutRules(true),
            $this->validationMessages()
        );

        /*
        |--------------------------------------------------------------------------
        | Validasi Awal
        |--------------------------------------------------------------------------
        */

        $participant = $this->findParticipant(
            (int) $validated['participant_id'],
            (int) $request->user()->id
        );

        if ($participant === null) {
            return redirect()
                ->route('customer.packages.browse')
                ->withErrors([
                    'participant' =>
                        'Peserta tidak ditemukan atau tidak dapat digunakan.',
                ]);
        }

        $package = $this->findPackage(
            (int) $validated['package_id']
        );

        if ($package === null) {
            return redirect()
                ->route('customer.packages.browse')
                ->withErrors([
                    'package' =>
                        'Paket tersebut sedang tidak tersedia.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Transaksi Database
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use (
            $request,
            $validated,
            $participant,
            $package
        ): array {
            /*
            |--------------------------------------------------------------------------
            | Kunci Participant
            |--------------------------------------------------------------------------
            */

            $lockedParticipant = Participant::query()
                ->where('id', $participant->id)
                ->where('user_id', $request->user()->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($lockedParticipant === null) {
                return [
                    'order' => null,
                    'existing_order' => null,
                    'error' =>
                        'Peserta tidak ditemukan atau tidak dapat digunakan.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Kunci dan Validasi Paket
            |--------------------------------------------------------------------------
            */

            $lockedPackage = Package::query()
                ->where('id', $package->id)
                ->lockForUpdate()
                ->first();

            if (
                $lockedPackage === null
                || !$lockedPackage->isActive()
                || !Program::query()
                    ->where('id', $lockedPackage->program_id)
                    ->where('status', 'active')
                    ->exists()
            ) {
                return [
                    'order' => null,
                    'existing_order' => null,
                    'error' =>
                        'Paket tersebut sedang tidak tersedia.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Cegah Tagihan Pendaftaran Ganda
            |--------------------------------------------------------------------------
            */

            if ($lockedParticipant->needsRegistrationFee()) {
                $existingOrder = $this->findPendingRegistrationOrder(
                    $lockedParticipant->id
                );

                if ($existingOrder !== null) {
                    return [
                        'order' => null,
                        'existing_order' => $existingOrder,
                        'error' => null,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Harga Terbaru
            |--------------------------------------------------------------------------
            */

            $quantity = 1;
            $unitPrice = $lockedPackage->getEffectivePrice();
            $subtotal = $unitPrice * $quantity;

            /*
            |--------------------------------------------------------------------------
            | Validasi Promo dan Kuota
            |--------------------------------------------------------------------------
            */

            if ($lockedPackage->hasActivePromo() && trim((string) ($validated['promo_code'] ?? "")) !== "") {
                return ['order' => null, 'existing_order' => null, 'error' => 'Paket ini sedang mendapatkan harga promo. Kode voucher tidak dapat digunakan bersamaan.'];
            }

            $promoResult = $this->resolvePromo(
                $validated['promo_code'] ?? null,
                $subtotal,
                true
            );

            if ($promoResult['error'] !== null) {
                return [
                    'order' => null,
                    'existing_order' => null,
                    'error' => $promoResult['error'],
                ];
            }

            $promoCode = $promoResult['promo'];
            $discountAmount = $promoResult['discount'];

            /*
            |--------------------------------------------------------------------------
            | Biaya Pendaftaran dan Total
            |--------------------------------------------------------------------------
            */

            $registrationFee = $lockedParticipant->needsRegistrationFee()
                ? Setting::registrationFee()
                : 0;

            $totalAmount = max(
                0,
                $subtotal - $discountAmount
            ) + $registrationFee;

            /*
            |--------------------------------------------------------------------------
            | Masa Reservasi Promo
            |--------------------------------------------------------------------------
            */

            $reservationExpiresAt = $promoCode !== null
                ? now()->addHours(
                    self::PROMO_RESERVATION_HOURS
                )
                : null;

            /*
            |--------------------------------------------------------------------------
            | Buat Order
            |--------------------------------------------------------------------------
            */

            $order = Order::create([
                'user_id' => $request->user()->id,
                'promo_code_id' => $promoCode?->id,
                'promo_reserved_until' => $reservationExpiresAt,
                'order_code' => $this->generateOrderCode(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'registration_fee' => $registrationFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'ordered_at' => now(),
                'paid_at' => null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Buat Order Item
            |--------------------------------------------------------------------------
            */

            $order->items()->create([
                'participant_id' => $lockedParticipant->id,
                'package_id' => $lockedPackage->id,
                'package_name' => $lockedPackage->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'created_at' => now(),
            ]);

            return [
                'order' => $order,
                'existing_order' => null,
                'error' => null,
            ];
        }, 3);

        /*
        |--------------------------------------------------------------------------
        | Hasil Transaksi
        |--------------------------------------------------------------------------
        */

        if ($result['error'] !== null) {
            return redirect()
                ->route('customer.packages.browse')
                ->withErrors([
                    'checkout' => $result['error'],
                ]);
        }

        if ($result['existing_order'] !== null) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $result['existing_order']
                )
                ->with(
                    'warning',
                    'Peserta masih memiliki pesanan yang memuat biaya '
                    . 'pendaftaran. Selesaikan pesanan tersebut terlebih dahulu.'
                );
        }

        return redirect()
            ->route('customer.orders.show', $result['order'])
            ->with(
                'success',
                'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.'
            );
    }

    /**
     * Aturan validasi checkout.
     */
    private function checkoutRules(bool $includeNotes): array
    {
        $rules = [
            'participant_id' => [
                'required',
                'integer',
                Rule::exists('participants', 'id'),
            ],
            'package_id' => [
                'required',
                'integer',
                Rule::exists('packages', 'id'),
            ],
            'promo_code' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];

        if ($includeNotes) {
            $rules['notes'] = [
                'nullable',
                'string',
                'max:2000',
            ];
        }

        return $rules;
    }

    /**
     * Pesan validasi.
     */
    private function validationMessages(): array
    {
        return [
            'participant_id.required' => 'Peserta wajib dipilih.',
            'participant_id.integer' => 'Data peserta tidak valid.',
            'participant_id.exists' => 'Peserta tidak ditemukan.',

            'package_id.required' => 'Paket wajib dipilih.',
            'package_id.integer' => 'Data paket tidak valid.',
            'package_id.exists' => 'Paket tidak ditemukan.',

            'promo_code.string' => 'Kode promo tidak valid.',
            'promo_code.max' => 'Kode promo maksimal 50 karakter.',

            'notes.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    /**
     * Mengambil peserta aktif milik customer.
     */
    private function findParticipant(
        int $participantId,
        int $userId
    ): ?Participant {
        return Participant::query()
            ->where('id', $participantId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Mengambil paket aktif dari program aktif.
     */
    private function findPackage(int $packageId): ?Package
    {
        return Package::query()
            ->with('program')
            ->where('id', $packageId)
            ->where('status', 'active')
            ->whereHas('program', function ($query) {
                $query->where('status', 'active');
            })
            ->first();
    }

    /**
     * Mencari pesanan pending yang sudah
     * memuat biaya pendaftaran peserta.
     */
    private function findPendingRegistrationOrder(
        int $participantId
    ): ?Order {
        return Order::query()
            ->where('status', 'pending')
            ->where('registration_fee', '>', 0)
            ->whereHas('items', function ($query) use ($participantId) {
                $query->where('participant_id', $participantId);
            })
            ->orderBy('id')
            ->first();
    }

    /**
     * Validasi dan perhitungan promo.
     *
     * Saat store(), baris promo dikunci
     * sebelum menghitung kuota.
     */
    private function resolvePromo(
        ?string $promoInput,
        float $subtotal,
        bool $lockForUpdate = false
    ): array {
        $promoCodeInput = trim((string) $promoInput);

        $result = [
            'promo' => null,
            'discount' => 0.0,
            'error' => null,
        ];

        if ($promoCodeInput === '') {
            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Kode Promo
        |--------------------------------------------------------------------------
        */

        $promoQuery = PromoCode::query()
            ->whereRaw(
                'UPPER(code) = ?',
                [strtoupper($promoCodeInput)]
            );

        if ($lockForUpdate) {
            $promoQuery->lockForUpdate();
        }

        $promoCode = $promoQuery->first();

        if ($promoCode === null) {
            $result['error'] = 'Kode promo tidak ditemukan.';
            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | Status dan Masa Berlaku
        |--------------------------------------------------------------------------
        */

        if (!$promoCode->isActive()) {
            $result['error'] = 'Kode promo sedang tidak aktif.';
            return $result;
        }

        if (!$promoCode->hasStarted()) {
            $result['error'] = 'Kode promo belum mulai berlaku.';
            return $result;
        }

        if ($promoCode->hasEnded()) {
            $result['error'] = 'Kode promo sudah berakhir.';
            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | Periksa Kuota Promo
        |--------------------------------------------------------------------------
        */

        if ($promoCode->hasReachedUsageLimit()) {
            $result['error'] =
                'Batas penggunaan kode promo sudah tercapai.';

            return $result;
        }

        if ($promoCode->usage_limit !== null) {
            $activeReservations = $this
                ->countActivePromoReservations(
                    (int) $promoCode->id
                );

            $remainingQuota =
                (int) $promoCode->usage_limit
                - (int) $promoCode->used_count
                - $activeReservations;

            if ($remainingQuota <= 0) {
                $result['error'] =
                    'Kuota kode promo sedang habis atau telah '
                    . 'direservasi oleh customer lain. '
                    . 'Silakan coba lagi nanti.';

                return $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Pembelian
        |--------------------------------------------------------------------------
        */

        if (!$promoCode->meetsMinimumPurchase($subtotal)) {
            $result['error'] =
                'Minimum pembelian untuk kode promo ini adalah '
                . $promoCode->formatted_minimum_purchase
                . '.';

            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | Hitung Diskon
        |--------------------------------------------------------------------------
        */

        $discountAmount = (float) $promoCode
            ->calculateDiscount($subtotal);

        if ($discountAmount <= 0) {
            $result['error'] =
                'Kode promo tidak dapat digunakan untuk transaksi ini.';

            return $result;
        }

        $result['promo'] = $promoCode;
        $result['discount'] = min(
            $subtotal,
            $discountAmount
        );

        return $result;
    }

    /**
     * Menghitung reservasi promo yang masih berlaku.
     *
     * Setiap order dihitung maksimal satu kali.
     *
     * Reservasi tetap aktif apabila:
     *
     * - Order pending dan batas reservasi
     *   belum lewat; atau
     *
     * - Order pending dan ada pembayaran pending
     *   dengan bukti yang diunggah tepat waktu
     *   serta belum ditolak admin.
     *
     * Pembayaran yang memiliki rejection_reason
     * tidak lagi mempertahankan reservasi setelah
     * batas reservasi berakhir.
     */
    private function countActivePromoReservations(
        int $promoCodeId
    ): int {
        $currentTime = now();

        return Order::query()
            ->where('promo_code_id', $promoCodeId)
            ->where('status', 'pending')
            ->whereNotNull('promo_reserved_until')
            ->where(function ($query) use ($currentTime) {
                /*
                |--------------------------------------------------------------------------
                | Reservasi Belum Kedaluwarsa
                |--------------------------------------------------------------------------
                */

                $query->where(
                    'promo_reserved_until',
                    '>',
                    $currentTime
                );

                /*
                |--------------------------------------------------------------------------
                | Bukti Pembayaran Menunggu Verifikasi
                |--------------------------------------------------------------------------
                |
                | Bukti harus:
                | - Berstatus pending
                | - Memiliki file bukti
                | - Diunggah tepat waktu
                | - Belum memiliki alasan penolakan
                |
                */

                $query->orWhereHas(
                    'payments',
                    function ($paymentQuery) {
                        $paymentQuery
                            ->where('status', 'pending')
                            ->whereNotNull('proof_image')
                            ->where('proof_image', '<>', '')
                            ->whereNotNull('proof_uploaded_at')
                            ->whereNull('rejection_reason')
                            ->whereColumn(
                                'payments.proof_uploaded_at',
                                '<=',
                                'orders.promo_reserved_until'
                            );
                    }
                );
            })
            ->count();
    }

    /**
     * Membuat kode order unik.
     */
    private function generateOrderCode(): string
    {
        do {
            $orderCode =
                'ORD-'
                . now()->format('Y')
                . '-'
                . strtoupper(Str::random(8));

        } while (
            Order::query()
                ->where('order_code', $orderCode)
                ->exists()
        );

        return $orderCode;
    }
}
