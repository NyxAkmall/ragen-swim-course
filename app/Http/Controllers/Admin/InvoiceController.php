<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Menampilkan seluruh invoice untuk Admin.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $date = $request->input('date');

        /*
        |--------------------------------------------------------------------------
        | Query Invoice
        |--------------------------------------------------------------------------
        */

        $query = Invoice::query()
            ->with([
                'order.user',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        |
        | Admin dapat mencari berdasarkan nomor invoice,
        | kode order, nama customer, atau email customer.
        |
        */

        if ($search !== '') {
            $query->where(function ($invoiceQuery) use ($search) {
                $invoiceQuery
                    ->where(
                        'invoice_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'order',
                        function ($orderQuery) use ($search) {
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

        if (
            $status
            && in_array(
                $status,
                [
                    'unpaid',
                    'paid',
                    'cancelled',
                    'refunded',
                ],
                true
            )
        ) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal Terbit
        |--------------------------------------------------------------------------
        */

        if ($date) {
            $query->whereDate('issued_at', $date);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $invoices = $query
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik Invoice
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total' => Invoice::count(),

            'unpaid' => Invoice::where(
                'status',
                'unpaid'
            )->count(),

            'paid' => Invoice::where(
                'status',
                'paid'
            )->count(),

            'cancelled' => Invoice::where(
                'status',
                'cancelled'
            )->count(),

            'refunded' => Invoice::where(
                'status',
                'refunded'
            )->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Tampilkan Halaman
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.invoices.index',
            compact(
                'invoices',
                'statistics',
                'search',
                'status',
                'date'
            )
        );
    }

    /**
     * Menampilkan detail invoice untuk Admin.
     *
     * Admin dapat melihat invoice milik seluruh customer.
     * Halaman ini juga akan menyediakan fitur cetak.
     */
    public function show(Invoice $invoice): View
    {
        /*
        |--------------------------------------------------------------------------
        | Muat Relasi Invoice
        |--------------------------------------------------------------------------
        */

        $invoice->load([
            'order.user',
            'order.promoCode',
            'order.items.participant',
            'order.items.package.program',
            'order.payments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan Order Tersedia
        |--------------------------------------------------------------------------
        */

        $order = $invoice->order;

        abort_if(
            $order === null,
            404,
            'Order untuk invoice ini tidak ditemukan.'
        );

        /*
        |--------------------------------------------------------------------------
        | Tampilkan Detail Invoice
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.invoices.show',
            compact(
                'invoice',
                'order'
            )
        );
    }
}
