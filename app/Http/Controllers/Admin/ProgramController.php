<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Daftar program renang.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $programs = Program::query()
            ->withCount(['packages', 'schedules'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($status, ['active', 'inactive'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total' => Program::count(),
            'active' => Program::where('status', 'active')->count(),
            'inactive' => Program::where('status', 'inactive')->count(),
            'packages' => Program::withCount('packages')
                ->get()
                ->sum('packages_count'),
        ];

        return view('admin.programs.index', compact(
            'programs',
            'statistics',
            'search',
            'status'
        ));
    }

    /**
     * Form tambah program.
     */
    public function create(): View
    {
        return view('admin.programs.create');
    }

    /**
     * Menyimpan program baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->programRules(true),
            $this->validationMessages()
        );

        $program = DB::transaction(function () use ($validated) {
            $slug = $this->generateUniqueSlug($validated['name']);

            return Program::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'short_description' =>
                    $validated['short_description'] ?? null,
                'description' =>
                    $validated['description'] ?? null,
                'duration_minutes' =>
                    $validated['duration_minutes'],
                'status' => $validated['status'],
            ]);
        }, 3);

        return redirect()
            ->route('admin.programs.show', $program)
            ->with('success', 'Program renang berhasil ditambahkan.');
    }

    /**
     * Detail program.
     */
    public function show(Program $program): View
    {
        $program->load([
            'packages' => function ($query) {
                $query
                    ->orderBy('price')
                    ->orderBy('total_sessions')
                    ->orderBy('name');
            },

            'schedules' => function ($query) {
                $query
                    ->with(['coach.user', 'location'])
                    ->withCount('bookings')
                    ->orderByDesc('class_date')
                    ->orderByDesc('start_time')
                    ->limit(10);
            },
        ]);

        $program->loadCount(['packages', 'schedules']);

        $statistics = [
            'packages' => $program->packages_count,
            'active_packages' => $program->packages
                ->where('status', 'active')
                ->count(),
            'schedules' => $program->schedules_count,
            'duration_minutes' => $program->duration_minutes,
        ];

        return view('admin.programs.show', compact(
            'program',
            'statistics'
        ));
    }

    /**
     * Form edit program.
     */
    public function edit(Program $program): View
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Memperbarui informasi program.
     */
    public function update(
        Request $request,
        Program $program
    ): RedirectResponse {
        $validated = $request->validate(
            $this->programRules(false, $program),
            $this->validationMessages()
        );

        DB::transaction(function () use ($program, $validated) {
            $lockedProgram = Program::query()
                ->whereKey($program->id)
                ->lockForUpdate()
                ->firstOrFail();

            $slug = $lockedProgram->slug;

            if ($lockedProgram->name !== $validated['name']) {
                $slug = $this->generateUniqueSlug(
                    $validated['name'],
                    $lockedProgram->id
                );
            }

            $lockedProgram->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'short_description' =>
                    $validated['short_description'] ?? null,
                'description' =>
                    $validated['description'] ?? null,
                'duration_minutes' =>
                    $validated['duration_minutes'],
            ]);
        }, 3);

        return redirect()
            ->route('admin.programs.show', $program)
            ->with('success', 'Data program berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan program.
     *
     * Program tidak boleh dinonaktifkan jika masih
     * mempunyai jadwal mendatang dengan booking aktif.
     */
    public function updateStatus(
        Request $request,
        Program $program
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        try {
            $changed = DB::transaction(
                function () use ($program, $validated) {
                    $lockedProgram = Program::query()
                        ->whereKey($program->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $newStatus = $validated['status'];

                    if ($lockedProgram->status === $newStatus) {
                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Perlindungan Jadwal dan Booking
                    |--------------------------------------------------------------------------
                    |
                    | Penonaktifan ditolak jika masih ada
                    | jadwal mendatang dengan booking
                    | pending atau confirmed.
                    |
                    */

                    if ($newStatus === 'inactive') {
                        $hasActiveBookings = $lockedProgram
                            ->schedules()
                            ->where('status', 'scheduled')
                            ->whereDate(
                                'class_date',
                                '>=',
                                today()->toDateString()
                            )
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
                                    'Program belum dapat dinonaktifkan karena masih memiliki jadwal mendatang dengan booking aktif. Selesaikan atau pindahkan booking tersebut terlebih dahulu.',
                            ]);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Perbarui Status
                    |--------------------------------------------------------------------------
                    */

                    $lockedProgram->update([
                        'status' => $newStatus,
                    ]);

                    return true;
                },
                3
            );
        } catch (ValidationException $exception) {
            return back()->with(
                'error',
                collect($exception->errors())
                    ->flatten()
                    ->first()
                    ?? 'Status program gagal diperbarui.'
            );
        }

        if (!$changed) {
            return back()->with(
                'warning',
                'Status program sudah berada pada status tersebut.'
            );
        }

        $message = $validated['status'] === 'active'
            ? 'Program berhasil diaktifkan kembali.'
            : 'Program berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * Aturan validasi program.
     */
    private function programRules(
        bool $isCreate,
        ?Program $program = null
    ): array {
        $nameUniqueRule = Rule::unique('programs', 'name');

        if (!$isCreate && $program !== null) {
            $nameUniqueRule->ignore($program->id);
        }

        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
                $nameUniqueRule,
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:480',
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
            'name.required' =>
                'Nama program wajib diisi.',
            'name.max' =>
                'Nama program maksimal 100 karakter.',
            'name.unique' =>
                'Nama program tersebut sudah digunakan.',

            'short_description.max' =>
                'Deskripsi singkat maksimal 255 karakter.',

            'description.max' =>
                'Deskripsi program maksimal 5.000 karakter.',

            'duration_minutes.required' =>
                'Durasi program wajib diisi.',
            'duration_minutes.integer' =>
                'Durasi program harus berupa angka.',
            'duration_minutes.min' =>
                'Durasi program minimal 15 menit.',
            'duration_minutes.max' =>
                'Durasi program maksimal 480 menit.',

            'status.required' =>
                'Status program wajib dipilih.',
            'status.in' =>
                'Status program tidak valid.',
        ];
    }

    /**
     * Membuat slug unik.
     */
    private function generateUniqueSlug(
        string $name,
        ?int $ignoreProgramId = null
    ): string {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'program';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            Program::query()
                ->when(
                    $ignoreProgramId !== null,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreProgramId
                    )
                )
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    public function destroy(Program $program): RedirectResponse
    {
        try {
            DB::transaction(function () use ($program) {
                $locked = Program::query()
                    ->lockForUpdate()
                    ->findOrFail($program->id);

                if ($locked->packages()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Program masih memiliki paket.',
                    ]);
                }

                if ($locked->schedules()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Program masih memiliki jadwal kelas.',
                    ]);
                }

                if (DB::table('weekly_schedules')
                    ->where('program_id', $locked->id)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Program masih digunakan oleh template jadwal mingguan.',
                    ]);
                }

                if ($locked->leads()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Program masih memiliki data calon pelanggan.',
                    ]);
                }

                $locked->delete();
            }, 3);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->with('error', 'Program masih terhubung dengan data lain.');
        }

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }
}
