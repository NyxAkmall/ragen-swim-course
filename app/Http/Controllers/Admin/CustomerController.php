<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar seluruh customer.
     */
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->query('search', '')
        );

        $status = trim(
            (string) $request->query('status', '')
        );

        $customers = User::query()
            ->where('role', 'customer')
            ->withCount([
                'participants',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'email',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->when(
                in_array(
                    $status,
                    ['active', 'inactive', 'suspended'],
                    true
                ),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistik Customer
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total' => User::query()
                ->where('role', 'customer')
                ->count(),

            'active' => User::query()
                ->where('role', 'customer')
                ->where('status', 'active')
                ->count(),

            'suspended' => User::query()
                ->where('role', 'customer')
                ->where('status', 'suspended')
                ->count(),

            'participants' => User::query()
                ->where('role', 'customer')
                ->withCount('participants')
                ->get()
                ->sum('participants_count'),
        ];


        return view('admin.customers.index', [
            'customers' => $customers,
            'statistics' => $statistics,
            'search' => $search,
            'status' => $status,
        ]);
    }


    /**
     * Menampilkan detail satu customer beserta
     * seluruh peserta yang dimilikinya.
     */
    public function show(User $customer): View
    {
        abort_unless(
            $customer->role === 'customer',
            404
        );

        $customer->load([
            'participants' => function ($query) {
                $query
                    ->withCount([
                        'bookings',
                        'progressRecords',
                    ])
                    ->with([
                        'participantPackages' => function ($packageQuery) {
                            $packageQuery
                                ->with('package.program')
                                ->orderByDesc('id');
                        },
                    ])
                    ->orderBy('full_name');
            },
        ]);

        $customer->loadCount([
            'participants',
            'orders',
        ]);


        return view('admin.customers.show', [
            'customer' => $customer,
        ]);
    }


    /**
     * Mengubah status akun customer.
     *
     * Status yang diperbolehkan dari halaman admin:
     * - active
     * - suspended
     */
    public function updateStatus(
        Request $request,
        User $customer
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Pastikan user yang diubah benar-benar customer
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $customer->role === 'customer',
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'status' => [
                'required',
                'in:active,suspended',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hindari update yang sebenarnya tidak diperlukan
        |--------------------------------------------------------------------------
        */

        if ($customer->status === $validated['status']) {

            return back()->with(
                'warning',
                'Status customer sudah berada pada status tersebut.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Status
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $customer,
            $validated
        ) {
            $customer->update([
                'status' => $validated['status'],
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Pesan
        |--------------------------------------------------------------------------
        */

        $message = $validated['status'] === 'active'
            ? 'Akun customer berhasil diaktifkan kembali.'
            : 'Akun customer berhasil ditangguhkan.';


        return back()->with(
            'success',
            $message
        );
    }

    /**
     * Menghapus akun customer yang belum digunakan.
     */
    public function destroy(User $customer): RedirectResponse
    {
        abort_unless($customer->role === 'customer', 404);

        try {
            DB::transaction(function () use ($customer) {
                $locked = User::query()
                    ->lockForUpdate()
                    ->findOrFail($customer->id);

                if ($locked->role !== 'customer') {
                    abort(404);
                }

                if (!in_array(
                    $locked->status,
                    ['inactive', 'suspended'],
                    true
                )) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'delete' => 'Nonaktifkan atau tangguhkan akun customer terlebih dahulu.',
                    ]);
                }

                if ($locked->participants()->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'delete' => 'Customer masih memiliki data peserta dan tidak dapat dihapus.',
                    ]);
                }

                if ($locked->orders()->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'delete' => 'Customer memiliki riwayat order dan tidak dapat dihapus.',
                    ]);
                }

                if ($locked->coach()->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'delete' => 'Akun masih terhubung dengan profil coach.',
                    ]);
                }

                if ($locked->systemNotifications()->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'delete' => 'Customer masih memiliki riwayat notifikasi.',
                    ]);
                }

                $locked->delete();
            }, 3);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()->with(
                'error',
                collect($exception->errors())->flatten()->first()
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);

            return back()->with(
                'error',
                'Customer masih terhubung dengan data lain dan tidak dapat dihapus.'
            );
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }
}
