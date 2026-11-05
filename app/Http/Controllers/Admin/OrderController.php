<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentFinalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    /**
     * Menampilkan seluruh order customer.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $status = $request->input('status');

        $date = $request->input('date');

        $query = Order::query()
            ->with([
                'user',
                'promoCode',
                'items.participant',
                'items.package.program',
                'payments',
                'invoice',
            ])
            ->withCount('items');

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(function ($orderQuery) use ($search) {
                $orderQuery
                    ->where(
                        'order_code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'user',
                        function ($userQuery) use ($search) {
                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'items.participant',
                        function ($participantQuery) use ($search) {
                            $participantQuery->where(
                                'full_name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'items.package',
                        function ($packageQuery) use ($search) {
                            $packageQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'payments',
                        function ($paymentQuery) use ($search) {
                            $paymentQuery->where(
                                'payment_code',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'invoice',
                        function ($invoiceQuery) use ($search) {
                            $invoiceQuery->where(
                                'invoice_number',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            'pending',
            'paid',
            'cancelled',
            'expired',
            'refunded',
        ];

        if (
            is_string($status)
            && in_array($status, $allowedStatuses, true)
        ) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal Order
        |--------------------------------------------------------------------------
        */

        if (
            is_string($date)
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
        ) {
            $query->whereDate('ordered_at', $date);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $orders = $query
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik Order
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total' => Order::query()->count(),

            'pending' => Order::query()
                ->where('status', 'pending')
                ->count(),

            'paid' => Order::query()
                ->where('status', 'paid')
                ->count(),

            'cancelled' => Order::query()
                ->where('status', 'cancelled')
                ->count(),

            'expired' => Order::query()
                ->where('status', 'expired')
                ->count(),

            'refunded' => Order::query()
                ->where('status', 'refunded')
                ->count(),

            'paid_revenue' => Order::query()
                ->where('status', 'paid')
                ->sum('total_amount'),
        ];

        return view(
            'admin.orders.index',
            compact(
                'orders',
                'statistics',
                'search',
                'status',
                'date'
            )
        );
    }

    /**
     * Menampilkan detail satu order.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'promoCode',
            'items.participant.user',
            'items.package.program',
            'payments.verifier',
            'payments.logs',
            'payments.refunds',
            'invoice',
        ]);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }

    /**
     * Admin menyetujui bukti pembayaran transfer manual.
     */
    public function verifyPayment(
        Request $request,
        Order $order,
        Payment $payment,
        PaymentFinalizationService $paymentFinalizationService
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Pastikan Payment Milik Order
        |--------------------------------------------------------------------------
        */

        if ((int) $payment->order_id !== (int) $order->id) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Awal
        |--------------------------------------------------------------------------
        |
        | Pemeriksaan awal memberikan pesan yang mudah dipahami.
        | Service tetap memeriksa ulang data setelah mengunci
        | transaksi untuk mencegah perubahan status bersamaan.
        |
        */

        if ($order->status === 'paid') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Order ini sudah berstatus lunas.'
                );
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Order ini sudah tidak dapat diverifikasi.'
                );
        }

        if ($payment->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Pembayaran ini sudah tidak berstatus menunggu.'
                );
        }

        if (empty($payment->proof_image)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Bukti pembayaran belum diupload oleh customer.'
                );
        }

        if (!empty($payment->rejection_reason)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Bukti pembayaran ini sudah ditolak. Tunggu customer mengupload bukti pembayaran baru.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Finalisasi Pembayaran
        |--------------------------------------------------------------------------
        */

        try {
            /*
            | Pemeriksaan penolakan di dalam transaksi ini
            | dilakukan setelah order dan payment dikunci.
            |
            | PaymentFinalizationService menggunakan transaksi
            | database sendiri. Laravel mendukung transaksi
            | bertingkat pada koneksi database yang sama.
            */

            DB::transaction(function () use (
                $order,
                $payment,
                $request,
                $paymentFinalizationService
            ): void {
                $lockedOrder = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                $lockedPayment = Payment::query()
                    ->lockForUpdate()
                    ->findOrFail($payment->id);

                if (
                    (int) $lockedPayment->order_id
                    !== (int) $lockedOrder->id
                ) {
                    throw new RuntimeException(
                        'Pembayaran tidak terhubung dengan order ini.'
                    );
                }

                if ($lockedOrder->status !== 'pending') {
                    throw new RuntimeException(
                        'Order ini sudah tidak dapat diverifikasi.'
                    );
                }

                if ($lockedPayment->status !== 'pending') {
                    throw new RuntimeException(
                        'Pembayaran ini sudah tidak berstatus menunggu.'
                    );
                }

                if (empty($lockedPayment->proof_image)) {
                    throw new RuntimeException(
                        'Bukti pembayaran belum tersedia.'
                    );
                }

                if (!empty($lockedPayment->rejection_reason)) {
                    throw new RuntimeException(
                        'Bukti pembayaran sudah ditolak. Tunggu bukti pembayaran baru.'
                    );
                }

                $paymentFinalizationService->finalize(
                    $lockedOrder,
                    $lockedPayment,
                    (int) $request->user()->id
                );
            }, 3);

        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                'success',
                'Pembayaran berhasil diverifikasi. Order telah lunas, invoice tersedia, dan paket peserta sudah diaktifkan.'
            );
    }

    /**
     * Admin menolak bukti pembayaran transfer manual.
     */
    public function rejectPayment(
        Request $request,
        Order $order,
        Payment $payment
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Pastikan Payment Milik Order
        |--------------------------------------------------------------------------
        */

        if ((int) $payment->order_id !== (int) $order->id) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Alasan Penolakan
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'rejection_reason' => [
                    'required',
                    'string',
                    'min:3',
                    'max:1000',
                ],
            ],
            [
                'rejection_reason.required' =>
                    'Alasan penolakan wajib diisi.',

                'rejection_reason.string' =>
                    'Alasan penolakan harus berupa teks.',

                'rejection_reason.min' =>
                    'Alasan penolakan minimal 3 karakter.',

                'rejection_reason.max' =>
                    'Alasan penolakan maksimal 1000 karakter.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Validasi Awal
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'paid') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Order yang sudah lunas tidak dapat ditolak.'
                );
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Order ini sudah tidak dapat diproses.'
                );
        }

        if ($payment->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Pembayaran ini sudah tidak dapat ditolak.'
                );
        }

        if (empty($payment->proof_image)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Belum ada bukti pembayaran yang dapat ditolak.'
                );
        }

        if (!empty($payment->rejection_reason)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'warning',
                    'Bukti pembayaran ini sudah ditolak sebelumnya.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Penolakan
        |--------------------------------------------------------------------------
        */

        try {
            DB::transaction(function () use (
                $order,
                $payment,
                $validated
            ): void {
                /*
                | Urutan penguncian disamakan dengan
                | PaymentFinalizationService:
                | order terlebih dahulu, lalu payment.
                */

                $lockedOrder = Order::query()
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                $lockedPayment = Payment::query()
                    ->lockForUpdate()
                    ->findOrFail($payment->id);

                if (
                    (int) $lockedPayment->order_id
                    !== (int) $lockedOrder->id
                ) {
                    throw new RuntimeException(
                        'Pembayaran tidak terhubung dengan order ini.'
                    );
                }

                if ($lockedOrder->status === 'paid') {
                    throw new RuntimeException(
                        'Order yang sudah lunas tidak dapat ditolak.'
                    );
                }

                if ($lockedOrder->status !== 'pending') {
                    throw new RuntimeException(
                        'Order ini sudah tidak dapat diproses.'
                    );
                }

                if ($lockedPayment->status !== 'pending') {
                    throw new RuntimeException(
                        'Pembayaran sudah tidak dapat ditolak.'
                    );
                }

                if (empty($lockedPayment->proof_image)) {
                    throw new RuntimeException(
                        'Bukti pembayaran tidak ditemukan.'
                    );
                }

                if (!empty($lockedPayment->rejection_reason)) {
                    throw new RuntimeException(
                        'Bukti pembayaran sudah ditolak sebelumnya.'
                    );
                }

                /*
                | Status payment tetap pending agar customer
                | dapat mengunggah bukti pembayaran baru.
                |
                | Bukti lama tidak dihapus di sini.
                | Penolakan ditandai melalui rejection_reason.
                */

                $lockedPayment->update([
                    'verified_at' => null,
                    'verified_by' => null,
                    'rejection_reason' =>
                        $validated['rejection_reason'],
                ]);

            }, 3);

        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                'success',
                'Bukti pembayaran berhasil ditolak. Customer dapat mengupload bukti pembayaran baru.'
            );
    }
}
