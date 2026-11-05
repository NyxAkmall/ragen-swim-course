<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Menampilkan daftar paket.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $programId = $request->integer('program');

        $packages = Package::query()
            ->with('program')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('program', function ($programQuery) use ($search) {
                            $programQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                in_array($status, ['active', 'inactive'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                $programId > 0,
                fn ($query) => $query->where('program_id', $programId)
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $programs = Program::query()
            ->orderBy('name')
            ->get();

        $statistics = [
            'total' => Package::count(),
            'active' => Package::where('status', 'active')->count(),
            'inactive' => Package::where('status', 'inactive')->count(),
            'programs' => Program::whereHas('packages')->count(),
        ];

        return view('admin.packages.index', compact(
            'packages',
            'programs',
            'statistics',
            'search',
            'status',
            'programId'
        ));
    }

    /**
     * Form tambah paket.
     */
    public function create(Request $request): View
    {
        $programs = Program::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $selectedProgramId = $request->integer('program');

        return view('admin.packages.create', compact(
            'programs',
            'selectedProgramId'
        ));
    }

    /**
     * Menyimpan paket baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->packageRules(true),
            $this->validationMessages()
        );

        $package = DB::transaction(function () use ($validated) {
            $program = Program::query()
                ->whereKey($validated['program_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($program->status !== 'active') {
                throw ValidationException::withMessages([
                    'program_id' => 'Program sedang nonaktif.',
                ]);
            }

            return Package::create([
                'program_id' => $validated['program_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'total_sessions' => $validated['total_sessions'],
                'price' => $validated['price'],
                'promo_enabled' => (bool) ($validated['promo_enabled'] ?? false),
                'promo_name' => $validated['promo_name'] ?? null,
                'promo_price' => $validated['promo_price'] ?? null,
                'promo_start_date' => $validated['promo_start_date'] ?? null,
                'promo_end_date' => $validated['promo_end_date'] ?? null,
                'validity_days' => $validated['validity_days'] ?? null,
                'status' => $validated['status'],
            ]);
        }, 3);

        return redirect()
            ->route('admin.packages.show', $package)
            ->with('success', 'Paket renang berhasil ditambahkan.');
    }

    /**
     * Detail paket.
     */
    public function show(Package $package): View
    {
        $package->load('program');

        return view('admin.packages.show', [
            'package' => $package,
        ]);
    }

    /**
     * Form edit paket.
     */
    public function edit(Package $package): View
    {
        $package->load('program');

        $programs = Program::query()
            ->where(function ($query) use ($package) {
                $query
                    ->where('status', 'active')
                    ->orWhere('id', $package->program_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.packages.edit', compact(
            'package',
            'programs'
        ));
    }

    /**
     * Memperbarui paket.
     *
     * Program, jumlah sesi, dan masa berlaku
     * tidak dapat diubah jika sudah ada pembelian.
     */
    public function update(
        Request $request,
        Package $package
    ): RedirectResponse {
        $validated = $request->validate(
            $this->packageRules(false, $package),
            $this->validationMessages()
        );

        DB::transaction(function () use ($package, $validated) {
            $lockedPackage = Package::query()
                ->whereKey($package->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasPurchaseHistory = $lockedPackage
                ->orderItems()
                ->exists();

            if ($hasPurchaseHistory) {
                $this->validatePurchasedPackageChanges(
                    $lockedPackage,
                    $validated
                );
            }

            $program = Program::query()
                ->whereKey($validated['program_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $program->status !== 'active'
                && (int) $program->id !== (int) $lockedPackage->program_id
            ) {
                throw ValidationException::withMessages([
                    'program_id' =>
                        'Program tujuan sedang nonaktif.',
                ]);
            }

            $lockedPackage->update([
                'program_id' => $validated['program_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'total_sessions' => $validated['total_sessions'],
                'price' => $validated['price'],
                'promo_enabled' => (bool) ($validated['promo_enabled'] ?? false),
                'promo_name' => $validated['promo_name'] ?? null,
                'promo_price' => $validated['promo_price'] ?? null,
                'promo_start_date' => $validated['promo_start_date'] ?? null,
                'promo_end_date' => $validated['promo_end_date'] ?? null,
                'validity_days' => $validated['validity_days'] ?? null,
            ]);
        }, 3);

        return redirect()
            ->route('admin.packages.show', $package)
            ->with('success', 'Data paket berhasil diperbarui.');
    }

    /**
     * Mengubah status penawaran paket.
     *
     * Status master paket tidak mengubah
     * status paket yang dimiliki peserta.
     */
    public function updateStatus(
        Request $request,
        Package $package
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ], $this->validationMessages());

        $changed = DB::transaction(function () use ($package, $validated) {
            $lockedPackage = Package::query()
                ->whereKey($package->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPackage->status === $validated['status']) {
                return false;
            }

            if ($validated['status'] === 'active') {
                $program = Program::query()
                    ->whereKey($lockedPackage->program_id)
                    ->firstOrFail();

                if ($program->status !== 'active') {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Paket tidak dapat diaktifkan karena programnya nonaktif.',
                    ]);
                }
            }

            $lockedPackage->update([
                'status' => $validated['status'],
            ]);

            return true;
        }, 3);

        if (!$changed) {
            return back()->with(
                'warning',
                'Status paket sudah berada pada status tersebut.'
            );
        }

        return back()->with(
            'success',
            $validated['status'] === 'active'
                ? 'Paket berhasil diaktifkan kembali.'
                : 'Paket berhasil dinonaktifkan.'
        );
    }

    /**
     * Memeriksa perubahan pada paket yang
     * sudah memiliki riwayat pembelian.
     */
    private function validatePurchasedPackageChanges(
        Package $package,
        array $validated
    ): void {
        if ((int) $package->program_id !== (int) $validated['program_id']) {
            throw ValidationException::withMessages([
                'program_id' =>
                    'Program tidak dapat diubah karena paket sudah pernah dibeli customer. Buat paket baru untuk program berbeda.',
            ]);
        }

        if (
            (int) $package->total_sessions
            !== (int) $validated['total_sessions']
        ) {
            throw ValidationException::withMessages([
                'total_sessions' =>
                    'Jumlah sesi tidak dapat diubah karena paket sudah pernah dibeli customer. Buat paket baru untuk jumlah sesi berbeda.',
            ]);
        }

        $oldValidity = $package->validity_days === null
            ? null
            : (int) $package->validity_days;

        $newValidity = $validated['validity_days'] ?? null;

        $newValidity = $newValidity === null
            ? null
            : (int) $newValidity;

        if ($oldValidity !== $newValidity) {
            throw ValidationException::withMessages([
                'validity_days' =>
                    'Masa berlaku tidak dapat diubah karena paket sudah pernah dibeli customer. Buat paket baru untuk masa berlaku berbeda.',
            ]);
        }
    }

    /**
     * Aturan validasi paket.
     */
    private function packageRules(
        bool $isCreate,
        ?Package $package = null
    ): array {
        $programRule = Rule::exists('programs', 'id');

        if ($isCreate) {
            $programRule->where('status', 'active');
        } elseif ($package !== null) {
            $programRule->where(function ($query) use ($package) {
                $query->where(function ($subQuery) use ($package) {
                    $subQuery
                        ->where('status', 'active')
                        ->orWhere('id', $package->program_id);
                });
            });
        }

        $rules = [
            'program_id' => [
                'required',
                'integer',
                $programRule,
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'total_sessions' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
                'decimal:0,2',
            ],
            'promo_enabled' => ['sometimes','boolean'],
            'promo_name' => ['nullable','string','max:150','required_if:promo_enabled,1'],
            'promo_price' => ['nullable','numeric','min:0','max:9999999999.99','decimal:0,2','lt:price','required_if:promo_enabled,1'],
            'promo_start_date' => ['nullable','date_format:Y-m-d'],
            'promo_end_date' => ['nullable','date_format:Y-m-d','after_or_equal:promo_start_date'],
            'validity_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],
        ];

        if ($isCreate) {
            $rules['status'] = [
                'required',
                Rule::in(['active', 'inactive']),
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
            'program_id.required' =>
                'Program wajib dipilih.',
            'program_id.integer' =>
                'Program tidak valid.',
            'program_id.exists' =>
                'Program tidak ditemukan atau sedang nonaktif.',

            'name.required' =>
                'Nama paket wajib diisi.',
            'name.max' =>
                'Nama paket maksimal 100 karakter.',

            'description.max' =>
                'Deskripsi maksimal 5.000 karakter.',

            'total_sessions.required' =>
                'Jumlah sesi wajib diisi.',
            'total_sessions.integer' =>
                'Jumlah sesi harus berupa bilangan bulat.',
            'total_sessions.min' =>
                'Jumlah sesi minimal 1.',
            'total_sessions.max' =>
                'Jumlah sesi maksimal 999.',

            'price.required' =>
                'Harga paket wajib diisi.',
            'price.numeric' =>
                'Harga paket harus berupa angka.',
            'price.min' =>
                'Harga tidak boleh negatif.',
            'price.max' =>
                'Harga paket terlalu besar.',
            'price.decimal' =>
                'Harga maksimal memiliki dua angka desimal.',

            'promo_enabled.boolean' => 'Status promo tidak valid.',
            'promo_name.required_if' => 'Nama promo wajib diisi ketika promo aktif.',
            'promo_name.max' => 'Nama promo maksimal 150 karakter.',
            'promo_price.required_if' => 'Harga promo wajib diisi ketika promo aktif.',
            'promo_price.numeric' => 'Harga promo harus berupa angka.',
            'promo_price.min' => 'Harga promo tidak boleh negatif.',
            'promo_price.max' => 'Harga promo terlalu besar.',
            'promo_price.decimal' => 'Harga promo maksimal dua angka desimal.',
            'promo_price.lt' => 'Harga promo harus lebih rendah dari harga normal.',
            'promo_start_date.date_format' => 'Tanggal mulai promo harus berformat YYYY-MM-DD.',
            'promo_end_date.date_format' => 'Tanggal akhir promo harus berformat YYYY-MM-DD.',
            'promo_end_date.after_or_equal' => 'Tanggal akhir promo tidak boleh sebelum tanggal mulai.',

            'validity_days.integer' =>
                'Masa berlaku harus berupa bilangan bulat.',
            'validity_days.min' =>
                'Masa berlaku minimal 1 hari.',
            'validity_days.max' =>
                'Masa berlaku maksimal 3.650 hari.',

            'status.required' =>
                'Status paket wajib dipilih.',
            'status.in' =>
                'Status paket tidak valid.',
        ];
    }
    public function destroy(Package $package): RedirectResponse
    {
        try {
            DB::transaction(function () use ($package) {
                $locked = Package::query()
                    ->lockForUpdate()
                    ->findOrFail($package->id);

                if ($locked->orderItems()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Paket sudah memiliki riwayat pembelian.',
                    ]);
                }

                if ($locked->participantPackages()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Paket masih dimiliki peserta.',
                    ]);
                }

                $locked->delete();
            }, 3);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->with('error', 'Paket masih terhubung dengan data lain.');
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
