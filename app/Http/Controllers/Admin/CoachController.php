<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CoachController extends Controller
{
    /**
     * Daftar coach.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $coaches = Coach::query()
            ->with('user')
            ->withCount([
                'schedules',
                'progressRecords',
                'assessments',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('specialization', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
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
            'total' => Coach::count(),
            'active' => Coach::where('status', 'active')->count(),
            'inactive' => Coach::where('status', 'inactive')->count(),
            'total_schedules' => Coach::withCount('schedules')
                ->get()
                ->sum('schedules_count'),
        ];

        return view('admin.coaches.index', compact(
            'coaches',
            'statistics',
            'search',
            'status'
        ));
    }

    /**
     * Form tambah coach.
     */
    public function create(): View
    {
        return view('admin.coaches.create');
    }

    /**
     * Menyimpan akun dan profil coach.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'specialization' => [
                'nullable',
                'string',
                'max:150',
            ],
            'experience_years' => [
                'nullable',
                'integer',
                'min:0',
                'max:80',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ], $this->validationMessages());

        $coach = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'coach',
                'status' => $validated['status'],
            ]);

            return Coach::create([
                'user_id' => $user->id,
                'bio' => $validated['bio'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'experience_years' => $validated['experience_years'] ?? 0,
                'status' => $validated['status'],
            ]);
        }, 3);

        return redirect()
            ->route('admin.coaches.show', $coach)
            ->with('success', 'Coach baru berhasil ditambahkan.');
    }

    /**
     * Detail coach.
     */
    public function show(Coach $coach): View
    {
        $coach->load([
            'user',
            'schedules' => function ($query) {
                $query
                    ->with(['program', 'location'])
                    ->withCount('bookings')
                    ->orderByDesc('class_date')
                    ->orderByDesc('start_time');
            },
        ]);

        $coach->loadCount([
            'schedules',
            'progressRecords',
            'assessments',
        ]);

        return view('admin.coaches.show', compact('coach'));
    }

    /**
     * Form edit coach.
     */
    public function edit(Coach $coach): View
    {
        $coach->load('user');

        return view('admin.coaches.edit', compact('coach'));
    }

    /**
     * Memperbarui data coach.
     */
    public function update(
        Request $request,
        Coach $coach
    ): RedirectResponse {
        $coach->load('user');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($coach->user_id),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')
                    ->ignore($coach->user_id),
            ],
            'specialization' => [
                'nullable',
                'string',
                'max:150',
            ],
            'experience_years' => [
                'nullable',
                'integer',
                'min:0',
                'max:80',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ], $this->validationMessages());

        DB::transaction(function () use ($coach, $validated) {
            $coach->user->update([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'],
            ]);

            $coach->update([
                'bio' => $validated['bio'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'experience_years' => $validated['experience_years'] ?? 0,
            ]);
        }, 3);

        return redirect()
            ->route('admin.coaches.show', $coach)
            ->with('success', 'Data coach berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan coach.
     *
     * Coach tidak dapat dinonaktifkan jika masih
     * memiliki jadwal mendatang dengan booking aktif.
     */
    public function updateStatus(
        Request $request,
        Coach $coach
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        try {
            DB::transaction(function () use ($coach, $validated) {
                $lockedCoach = Coach::query()
                    ->whereKey($coach->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $user = User::query()
                    ->whereKey($lockedCoach->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $newStatus = $validated['status'];

                if (
                    $lockedCoach->status === $newStatus
                    && $user->status === $newStatus
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Status coach sudah berada pada status tersebut.',
                    ]);
                }

                if ($newStatus === 'inactive') {
                    $hasActiveBookings = $lockedCoach
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
                                'Coach belum dapat dinonaktifkan karena masih memiliki jadwal mendatang dengan booking aktif. Selesaikan atau pindahkan booking tersebut terlebih dahulu.',
                        ]);
                    }
                }

                $lockedCoach->update([
                    'status' => $newStatus,
                ]);

                $user->update([
                    'status' => $newStatus,
                ]);
            }, 3);
        } catch (ValidationException $exception) {
            return back()->with(
                'error',
                collect($exception->errors())
                    ->flatten()
                    ->first() ?? 'Status coach gagal diperbarui.'
            );
        }

        $message = $validated['status'] === 'active'
            ? 'Coach berhasil diaktifkan kembali.'
            : 'Coach berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * Pesan validasi.
     */
    private function validationMessages(): array
    {
        return [
            'name.required' =>
                'Nama coach wajib diisi.',
            'name.max' =>
                'Nama coach maksimal 150 karakter.',

            'email.required' =>
                'Email coach wajib diisi.',
            'email.email' =>
                'Format email tidak valid.',
            'email.max' =>
                'Email maksimal 150 karakter.',
            'email.unique' =>
                'Email tersebut sudah digunakan akun lain.',

            'phone.required' =>
                'Nomor telepon coach wajib diisi.',
            'phone.max' =>
                'Nomor telepon maksimal 20 karakter.',
            'phone.unique' =>
                'Nomor telepon tersebut sudah digunakan akun lain.',

            'password.required' =>
                'Password coach wajib diisi.',
            'password.confirmed' =>
                'Konfirmasi password tidak sama.',

            'specialization.max' =>
                'Spesialisasi maksimal 150 karakter.',

            'experience_years.integer' =>
                'Pengalaman harus berupa angka.',
            'experience_years.min' =>
                'Pengalaman tidak boleh kurang dari 0 tahun.',
            'experience_years.max' =>
                'Pengalaman maksimal 80 tahun.',

            'bio.max' =>
                'Biografi maksimal 3000 karakter.',

            'status.required' =>
                'Status coach wajib dipilih.',
            'status.in' =>
                'Status coach tidak valid.',
        ];
    }

    /**
     * Menghapus coach yang belum memiliki riwayat operasional.
     */
    public function destroy(Coach $coach): RedirectResponse
    {
        try {
            DB::transaction(function () use ($coach) {
                $lockedCoach = Coach::query()
                    ->lockForUpdate()
                    ->findOrFail($coach->id);

                $user = User::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedCoach->user_id);

                if ($user->role !== 'coach') {
                    throw ValidationException::withMessages([
                        'delete' => 'Akun tidak memiliki role coach yang sesuai.',
                    ]);
                }

                if (
                    $lockedCoach->status !== 'inactive'
                    || $user->status !== 'inactive'
                ) {
                    throw ValidationException::withMessages([
                        'delete' => 'Nonaktifkan coach terlebih dahulu sebelum menghapusnya.',
                    ]);
                }

                if ($lockedCoach->schedules()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Coach masih memiliki riwayat jadwal kelas.',
                    ]);
                }

                if (
                    \App\Models\WeeklySchedule::query()
                        ->where('coach_id', $lockedCoach->id)
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'delete' => 'Coach masih terhubung dengan jadwal mingguan.',
                    ]);
                }

                if ($lockedCoach->progressRecords()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Coach memiliki riwayat perkembangan peserta.',
                    ]);
                }

                if ($lockedCoach->assessments()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Coach memiliki riwayat penilaian peserta.',
                    ]);
                }

                if ($user->participants()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Akun masih memiliki data peserta.',
                    ]);
                }

                if ($user->orders()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Akun masih memiliki riwayat order.',
                    ]);
                }

                if ($user->systemNotifications()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Akun masih memiliki riwayat notifikasi.',
                    ]);
                }

                $lockedCoach->delete();
                $user->delete();
            }, 3);
        } catch (ValidationException $exception) {
            return back()->with(
                'error',
                collect($exception->errors())->flatten()->first()
                    ?? 'Coach tidak dapat dihapus.'
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);

            return back()->with(
                'error',
                'Coach masih terhubung dengan data lain dan tidak dapat dihapus.'
            );
        }

        return redirect()
            ->route('admin.coaches.index')
            ->with('success', 'Coach dan akun terkait berhasil dihapus.');
    }
}
