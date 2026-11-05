<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Halaman Laporan Transaksi
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $startDate = $validated['start_date']
            ?? now()->startOfMonth()->format('Y-m-d');

        $endDate = $validated['end_date']
            ?? now()->format('Y-m-d');

        if ($startDate > $endDate) {
            return redirect()
                ->route('admin.reports.index')
                ->with(
                    'warning',
                    'Tanggal mulai tidak boleh melebihi tanggal akhir.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Query Dasar Berdasarkan Tanggal Order
        |--------------------------------------------------------------------------
        */

        $periodOrders = Order::query()
            ->whereDate('ordered_at', '>=', $startDate)
            ->whereDate('ordered_at', '<=', $endDate);

        /*
        |--------------------------------------------------------------------------
        | Statistik Order
        |--------------------------------------------------------------------------
        |
        | Statistik ini berdasarkan tanggal pembuatan order.
        | Kolom nominal yang benar adalah total_amount.
        |
        */

        $statistics = [
            'total_orders' => (clone $periodOrders)->count(),

            'pending_orders' => (clone $periodOrders)
                ->where('status', 'pending')
                ->count(),

            'paid_orders' => (clone $periodOrders)
                ->where('status', 'paid')
                ->count(),

            'cancelled_orders' => (clone $periodOrders)
                ->where('status', 'cancelled')
                ->count(),

            'expired_orders' => (clone $periodOrders)
                ->where('status', 'expired')
                ->count(),

            'refunded_orders' => (clone $periodOrders)
                ->where('status', 'refunded')
                ->count(),

            'paid_order_value' => (clone $periodOrders)
                ->where('status', 'paid')
                ->sum('total_amount'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Statistik Berdasarkan Tanggal Pembayaran
        |--------------------------------------------------------------------------
        |
        | Pendapatan periode dihitung dari order berstatus paid
        | dengan paid_at dalam rentang tanggal yang dipilih.
        |
        */

        $paidInPeriod = Order::query()
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate);

        $statistics['period_revenue'] = (clone $paidInPeriod)
            ->sum('total_amount');

        $statistics['period_paid_orders'] = (clone $paidInPeriod)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Daftar Transaksi
        |--------------------------------------------------------------------------
        */

        $orders = (clone $periodOrders)
            ->with([
                'user',
                'invoice',
            ])
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', [
            'orders' => $orders,
            'statistics' => $statistics,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Ekspor Laporan Transaksi CSV
    |--------------------------------------------------------------------------
    */

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'start_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $filename = sprintf(
            'laporan-transaksi-ragen-%s-sampai-%s.csv',
            $startDate,
            $endDate
        );

        return response()->streamDownload(
            function () use ($startDate, $endDate): void {
                $output = fopen('php://output', 'w');

                if ($output === false) {
                    return;
                }

                /*
                |----------------------------------------------------------
                | UTF-8 BOM
                |----------------------------------------------------------
                |
                | Membantu Microsoft Excel mengenali karakter Indonesia.
                |
                */

                fwrite($output, "\xEF\xBB\xBF");

                /*
                |----------------------------------------------------------
                | Header CSV
                |----------------------------------------------------------
                */

                fputcsv($output, [
                    'No',
                    'Tanggal Order',
                    'Kode Order',
                    'Nama Customer',
                    'Email Customer',
                    'Total (Rp)',
                    'Status Order',
                    'Tanggal Pembayaran',
                    'Nomor Invoice',
                ]);

                $statusLabels = [
                    'pending' => 'Menunggu',
                    'paid' => 'Lunas',
                    'cancelled' => 'Dibatalkan',
                    'expired' => 'Kedaluwarsa',
                    'refunded' => 'Refund',
                ];

                /*
                |----------------------------------------------------------
                | Pengamanan Isi Sel CSV
                |----------------------------------------------------------
                |
                | Teks yang diawali karakter formula Excel diberi
                | awalan apostrof agar tidak dijalankan sebagai formula.
                |
                */

                $safeCsvText = static function (
                    mixed $value
                ): string {
                    $text = (string) ($value ?? '');

                    if (preg_match('/^[\s]*[=+\-@]/u', $text)) {
                        return "'" . $text;
                    }

                    return $text;
                };

                /*
                |----------------------------------------------------------
                | Query Ekspor
                |----------------------------------------------------------
                |
                | Menggunakan lazy() agar data dibaca secara bertahap.
                | Relasi user dan invoice tetap dapat di-eager-load.
                |
                */

                $orders = Order::query()
                    ->with([
                        'user',
                        'invoice',
                    ])
                    ->whereDate(
                        'ordered_at',
                        '>=',
                        $startDate
                    )
                    ->whereDate(
                        'ordered_at',
                        '<=',
                        $endDate
                    )
                    ->orderBy('ordered_at')
                    ->orderBy('id')
                    ->lazy(500);

                $number = 0;

                foreach ($orders as $order) {
                    $number++;

                    $status = $order->status;

                    $statusLabel = $statusLabels[$status]
                        ?? ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                (string) $status
                            )
                        );

                    $orderedAt = $order->ordered_at
                        ? Carbon::parse(
                            $order->ordered_at
                        )->format('d/m/Y H:i')
                        : '';

                    $paidAt = $order->paid_at
                        ? Carbon::parse(
                            $order->paid_at
                        )->format('d/m/Y H:i')
                        : '';

                    fputcsv($output, [
                        $number,

                        $orderedAt,

                        $safeCsvText(
                            $order->order_code
                        ),

                        $safeCsvText(
                            $order->user?->name
                        ),

                        $safeCsvText(
                            $order->user?->email
                        ),

                        (float) $order->total_amount,

                        $statusLabel,

                        $paidAt,

                        $safeCsvText(
                            $order->invoice?->invoice_number
                        ),
                    ]);
                }

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache',
            ]
        );
    }
}
