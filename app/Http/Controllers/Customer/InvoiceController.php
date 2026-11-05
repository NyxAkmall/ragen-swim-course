<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Menampilkan daftar invoice milik customer.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $invoices = Invoice::query()
            ->with('order')
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'customer.invoices.index',
            compact('invoices')
        );
    }

    /**
     * Menampilkan detail invoice customer.
     *
     * Kepemilikan invoice diperiksa sebelum
     * memuat informasi lengkap pesanan.
     */
    public function show(
        Request $request,
        Invoice $invoice
    ): View {
        $userId = $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | Validasi Kepemilikan Invoice
        |--------------------------------------------------------------------------
        |
        | Invoice hanya boleh diakses oleh customer
        | yang memiliki pesanan terkait.
        |
        | Invoice milik customer lain akan
        | menghasilkan HTTP 404.
        |
        */

        $isOwned = Invoice::query()
            ->whereKey($invoice->getKey())
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->exists();

        abort_unless(
            $isOwned,
            404,
            'Invoice tidak ditemukan.'
        );

        /*
        |--------------------------------------------------------------------------
        | Memuat Detail Invoice
        |--------------------------------------------------------------------------
        |
        | Relasi baru dimuat setelah kepemilikan
        | customer berhasil diverifikasi.
        |
        */

        $invoice->load([
            'order.user',
            'order.items.participant',
            'order.items.package.program',
        ]);

        $order = $invoice->order;

        abort_if(
            $order === null
                || (int) $order->user_id !== (int) $userId,
            404,
            'Invoice tidak ditemukan.'
        );

        return view(
            'customer.invoices.show',
            compact(
                'invoice',
                'order'
            )
        );
    }
}
