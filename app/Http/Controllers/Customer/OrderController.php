<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Menampilkan seluruh riwayat transaksi
     * milik customer yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Riwayat Order Customer
        |--------------------------------------------------------------------------
        */

        $orders = Order::query()
            ->with([
                'items.participant',
                'items.package.program',
                'payments',
                'invoice',
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik Pembayaran
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total_orders' => $orders->count(),

            'paid_orders' => $orders
                ->where('status', 'paid')
                ->count(),

            'pending_orders' => $orders
                ->where('status', 'pending')
                ->count(),

            'total_spent' => $orders
                ->where('status', 'paid')
                ->sum(function ($order) {
                    return (float) $order->total_amount;
                }),
        ];

        return view(
            'customer.orders.index',
            compact(
                'orders',
                'statistics'
            )
        );
    }

    /**
     * Menampilkan detail pesanan milik customer.
     *
     * Customer tidak dapat mengakses
     * pesanan milik pengguna lain.
     */
    public function show(
        Request $request,
        Order $order
    ): View {
        $userId = $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | Validasi Kepemilikan Pesanan
        |--------------------------------------------------------------------------
        |
        | Cari ulang pesanan menggunakan ID pesanan
        | dan ID customer yang sedang login.
        |
        | Jika pesanan tidak ditemukan atau bukan
        | milik customer, tampilkan halaman 404.
        |
        */

        $customerOrder = Order::query()
            ->whereKey($order->getKey())
            ->where('user_id', $userId)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Memuat Detail Pesanan
        |--------------------------------------------------------------------------
        |
        | Informasi lengkap baru dimuat setelah
        | kepemilikan pesanan berhasil diperiksa.
        |
        */

        $customerOrder->load([
            'user',
            'promoCode',
            'items.participant',
            'items.package.program',
            'payments',
            'invoice',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rekening Pembayaran
        |--------------------------------------------------------------------------
        |
        | Informasi rekening diambil dari tabel
        | settings yang dikelola oleh admin.
        |
        */

        $bankAccount = Setting::paymentBankAccount();

        $bankAccountIsConfigured =
            Setting::hasPaymentBankAccount();

        /*
        |--------------------------------------------------------------------------
        | Variabel untuk Blade
        |--------------------------------------------------------------------------
        |
        | Gunakan nama $order agar tetap sesuai
        | dengan customer.orders.show.
        |
        */

        $order = $customerOrder;

        return view(
            'customer.orders.show',
            compact(
                'order',
                'bankAccount',
                'bankAccountIsConfigured'
            )
        );
    }
}
