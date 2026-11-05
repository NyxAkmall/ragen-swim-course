<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Menampilkan daftar lokasi latihan.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $query = Location::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('facilities', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $locations = $query
            ->withCount('schedules')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total' => Location::count(),
            'active' => Location::where('status', 'active')->count(),
            'inactive' => Location::where('status', 'inactive')->count(),
            'schedules' => DB::table('class_schedules')->count(),
        ];

        return view('admin.locations.index', compact(
            'locations',
            'statistics',
            'search',
            'status'
        ));
    }

    /**
     * Menampilkan form tambah lokasi.
     */
    public function create(): View
    {
        return view('admin.locations.create');
    }

    /**
     * Menyimpan lokasi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->locationRules(true),
            $this->validationMessages()
        );

        $location = DB::transaction(function () use ($validated) {
            return Location::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'google_maps_url' =>
                    $validated['google_maps_url'] ?? null,
                'description' =>
                    $validated['description'] ?? null,
                'facilities' =>
                    $validated['facilities'] ?? null,
                'status' => $validated['status'],
                'customer_entry_fee' =>
                    $validated['customer_entry_fee'],
                'coach_entry_fee' =>
                    $validated['coach_entry_fee'],
                'customer_ticket_status' => $validated['customer_ticket_status'] ?? 'unconfigured',
                'customer_ticket_payer' => $validated['customer_ticket_payer'] ?? 'customer',
                'customer_ticket_payment_method' => $validated['customer_ticket_payment_method'] ?? 'direct',
                'coach_ticket_status' => $validated['coach_ticket_status'] ?? 'unconfigured',
                'coach_ticket_payer' => $validated['coach_ticket_payer'] ?? 'ragen',
                'full_include_enabled' => $validated['full_include_enabled'] ?? false,
                'full_include_name' => $validated['full_include_name'] ?? null,
                'full_include_price' => $validated['full_include_price'] ?? null,
                'full_include_start_date' => $validated['full_include_start_date'] ?? null,
                'full_include_end_date' => $validated['full_include_end_date'] ?? null,
            ]);
        }, 3);

        return redirect()
            ->route('admin.locations.show', $location)
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail lokasi.
     */
    public function show(Location $location): View
    {
        $location->load([
            'schedules' => function ($query) {
                $query
                    ->with([
                        'program',
                        'coach.user',
                    ])
                    ->withCount([
                        'bookings' => function ($bookingQuery) {
                            $bookingQuery->whereIn('status', [
                                'pending',
                                'confirmed',
                                'completed',
                            ]);
                        },
                    ])
                    ->orderByDesc('class_date')
                    ->orderByDesc('start_time')
                    ->limit(10);
            },
        ]);

        $location->loadCount('schedules');

        $statistics = [
            'schedules' => $location->schedules_count,

            'upcoming' => $location
                ->schedules()
                ->where('status', 'scheduled')
                ->whereDate('class_date', '>=', today())
                ->count(),

            'completed' => $location
                ->schedules()
                ->where('status', 'completed')
                ->count(),

            'cancelled' => $location
                ->schedules()
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view('admin.locations.show', compact(
            'location',
            'statistics'
        ));
    }

    /**
     * Menampilkan form edit lokasi.
     */
    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Memperbarui informasi lokasi.
     */
    public function update(
        Request $request,
        Location $location
    ): RedirectResponse {
        $validated = $request->validate(
            $this->locationRules(false),
            $this->validationMessages()
        );

        DB::transaction(function () use ($location, $validated) {
            $lockedLocation = Location::query()
                ->whereKey($location->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedLocation->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'google_maps_url' =>
                    $validated['google_maps_url'] ?? null,
                'description' =>
                    $validated['description'] ?? null,
                'facilities' =>
                    $validated['facilities'] ?? null,
                'customer_entry_fee' =>
                    $validated['customer_entry_fee'],
                'coach_entry_fee' =>
                    $validated['coach_entry_fee'],
                'customer_ticket_status' => $validated['customer_ticket_status'] ?? $lockedLocation->customer_ticket_status,
                'customer_ticket_payer' => $validated['customer_ticket_payer'] ?? $lockedLocation->customer_ticket_payer,
                'customer_ticket_payment_method' => $validated['customer_ticket_payment_method'] ?? $lockedLocation->customer_ticket_payment_method,
                'coach_ticket_status' => $validated['coach_ticket_status'] ?? $lockedLocation->coach_ticket_status,
                'coach_ticket_payer' => $validated['coach_ticket_payer'] ?? $lockedLocation->coach_ticket_payer,
                'full_include_enabled' => $validated['full_include_enabled'] ?? $lockedLocation->full_include_enabled,
                'full_include_name' => array_key_exists('full_include_name', $validated) ? $validated['full_include_name'] : $lockedLocation->full_include_name,
                'full_include_price' => array_key_exists('full_include_price', $validated) ? $validated['full_include_price'] : $lockedLocation->full_include_price,
                'full_include_start_date' => array_key_exists('full_include_start_date', $validated) ? $validated['full_include_start_date'] : $lockedLocation->full_include_start_date,
                'full_include_end_date' => array_key_exists('full_include_end_date', $validated) ? $validated['full_include_end_date'] : $lockedLocation->full_include_end_date,
            ]);
        }, 3);

        return redirect()
            ->route('admin.locations.show', $location)
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan lokasi.
     *
     * Lokasi tidak boleh dinonaktifkan jika masih
     * memiliki jadwal mendatang dengan booking aktif.
     */
    public function updateStatus(
        Request $request,
        Location $location
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ], [
            'status.required' => 'Status lokasi wajib dipilih.',
            'status.in' => 'Status lokasi tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($location, $validated) {
                $lockedLocation = Location::query()
                    ->whereKey($location->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $newStatus = $validated['status'];

                if ($lockedLocation->status === $newStatus) {
                    return;
                }

                if ($newStatus === 'inactive') {
                    $hasActiveBookings = $lockedLocation
                        ->schedules()
                        ->where('status', 'scheduled')
                        ->whereDate('class_date', '>=', today())
                        ->whereHas('bookings', function ($query) {
                            $query->whereIn('status', [
                                'pending',
                                'confirmed',
                            ]);
                        })
                        ->exists();

                    if ($hasActiveBookings) {
                        throw ValidationException::withMessages([
                            'status' =>
                                'Lokasi belum dapat dinonaktifkan karena masih memiliki jadwal mendatang dengan booking aktif. Selesaikan atau pindahkan booking tersebut terlebih dahulu.',
                        ]);
                    }
                }

                $lockedLocation->update([
                    'status' => $newStatus,
                ]);
            }, 3);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.locations.show', $location)
                ->with(
                    'error',
                    collect($exception->errors())
                        ->flatten()
                        ->first()
                        ?? 'Status lokasi gagal diperbarui.'
                );
        }

        $message = $validated['status'] === 'active'
            ? 'Lokasi berhasil diaktifkan.'
            : 'Lokasi berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.locations.show', $location)
            ->with('success', $message);
    }

    /**
     * Aturan validasi form lokasi.
     */
    private function locationRules(bool $isCreate): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'address' => [
                'required',
                'string',
                'max:1000',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'google_maps_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'facilities' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'customer_entry_fee' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
                'decimal:0,2',
            ],

            'coach_entry_fee' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
                'decimal:0,2',
            ],
        ];

        $rules["customer_ticket_status"] = ["sometimes", "in:unconfigured,free,paid"];
        $rules["customer_ticket_payer"] = ["sometimes", "in:customer,ragen,pool"];
        $rules["customer_ticket_payment_method"] = ["sometimes", "in:direct,through_ragen"];
        $rules["coach_ticket_status"] = ["sometimes", "in:unconfigured,free,paid"];
        $rules["coach_ticket_payer"] = ["sometimes", "in:customer,coach,ragen,pool"];
        $rules["customer_entry_fee"][] = \Illuminate\Validation\Rule::when(request()->input("customer_ticket_status") === "paid", ["gt:0"]);
        $rules["coach_entry_fee"][] = \Illuminate\Validation\Rule::when(request()->input("coach_ticket_status") === "paid", ["gt:0"]);
        $rules["customer_entry_fee"][] = \Illuminate\Validation\Rule::when(request()->input("customer_ticket_status") === "free", ["in:0,0.00"]);
        $rules["coach_entry_fee"][] = \Illuminate\Validation\Rule::when(request()->input("coach_ticket_status") === "free", ["in:0,0.00"]);
        $rules["full_include_enabled"] = ["sometimes", "boolean"];
        $rules["full_include_name"] = ["nullable", "required_if:full_include_enabled,1", "string", "max:255"];
        $rules["full_include_price"] = ["nullable", "required_if:full_include_enabled,1", "numeric", "min:0", "max:9999999999.99", "decimal:0,2"];
        $rules["full_include_start_date"] = ["nullable", "date"];
        $rules["full_include_end_date"] = ["nullable", "date", "after_or_equal:full_include_start_date"];

        if ($isCreate) {
            $rules['status'] = [
                'required',
                'in:active,inactive',
            ];
        }

        return $rules;
    }

    /**
     * Pesan validasi form lokasi.
     */
    private function validationMessages(): array
    {
        return [
            'name.required' =>
                'Nama lokasi wajib diisi.',
            'name.string' =>
                'Nama lokasi harus berupa teks.',
            'name.max' =>
                'Nama lokasi maksimal 150 karakter.',

            'address.required' =>
                'Alamat lokasi wajib diisi.',
            'address.string' =>
                'Alamat lokasi harus berupa teks.',
            'address.max' =>
                'Alamat lokasi terlalu panjang.',

            'city.required' =>
                'Kota wajib diisi.',
            'city.string' =>
                'Kota harus berupa teks.',
            'city.max' =>
                'Nama kota maksimal 100 karakter.',

            'google_maps_url.url' =>
                'Link Google Maps harus berupa URL yang valid.',
            'google_maps_url.max' =>
                'Link Google Maps maksimal 500 karakter.',

            'description.string' =>
                'Deskripsi harus berupa teks.',
            'description.max' =>
                'Deskripsi maksimal 5.000 karakter.',

            'facilities.string' =>
                'Fasilitas harus berupa teks.',
            'facilities.max' =>
                'Fasilitas maksimal 5.000 karakter.',

            'status.required' =>
                'Status lokasi wajib dipilih.',
            'status.in' =>
                'Status lokasi tidak valid.',

            'customer_entry_fee.required' =>
                'Biaya masuk customer wajib diisi.',
            'customer_entry_fee.numeric' =>
                'Biaya masuk customer harus berupa angka.',
            'customer_entry_fee.min' =>
                'Biaya masuk customer tidak boleh negatif.',
            'customer_entry_fee.max' =>
                'Biaya masuk customer terlalu besar.',
            'customer_entry_fee.decimal' =>
                'Biaya masuk customer maksimal 2 angka desimal.',

            'coach_entry_fee.required' =>
                'Biaya masuk coach wajib diisi.',
            'coach_entry_fee.numeric' =>
                'Biaya masuk coach harus berupa angka.',
            'coach_entry_fee.min' =>
                'Biaya masuk coach tidak boleh negatif.',
            'coach_entry_fee.max' =>
                'Biaya masuk coach terlalu besar.',
            'coach_entry_fee.decimal' =>
                'Biaya masuk coach maksimal 2 angka desimal.',
        ];
    }
    public function destroy(Location $location): RedirectResponse
    {
        try {
            DB::transaction(function () use ($location) {
                $locked = Location::query()
                    ->lockForUpdate()
                    ->findOrFail($location->id);

                if ($locked->schedules()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Lokasi masih digunakan oleh jadwal kelas.',
                    ]);
                }

                if (\Illuminate\Support\Facades\DB::table('weekly_schedules')
                    ->where('location_id', $locked->id)->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Lokasi masih digunakan oleh template jadwal mingguan.',
                    ]);
                }

                $locked->delete();
            }, 3);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->with('error', 'Lokasi masih terhubung dengan data lain.');
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
