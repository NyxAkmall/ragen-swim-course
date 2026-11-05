<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Coach;
use App\Models\Location;
use App\Models\Program;
use App\Models\WeeklySchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $date = trim((string) $request->input('date', ''));

        $schedules = ClassSchedule::query()
            ->with(['program', 'coach.user', 'location'])
            ->withCount([
                'bookings as active_bookings_count' => fn ($q) =>
                    $q->whereIn('status', ['pending', 'confirmed']),
                'waitingLists as active_offers_count' => fn ($q) => $q
                    ->where('status', 'offered')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now()),
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhereHas('program', fn ($p) =>
                            $p->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('coach.user', fn ($u) =>
                            $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('location', fn ($l) =>
                            $l->where('name', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%"));
                });
            })
            ->when(
                in_array($status, ['scheduled', 'completed', 'cancelled'], true),
                fn ($q) => $q->where('status', $status)
            )
            ->when($date !== '', fn ($q) =>
                $q->whereDate('class_date', $date))
            ->orderByDesc('class_date')
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total' => ClassSchedule::count(),
            'scheduled' => ClassSchedule::where('status', 'scheduled')->count(),
            'completed' => ClassSchedule::where('status', 'completed')->count(),
            'cancelled' => ClassSchedule::where('status', 'cancelled')->count(),
        ];

        return view('admin.class-schedules.index', compact(
            'schedules',
            'statistics',
            'search',
            'status',
            'date'
        ));
    }

    public function create(): View
    {
        $programs = Program::where('status', 'active')
            ->orderBy('name')
            ->get();

        $coaches = Coach::with('user')
            ->where('status', 'active')
            ->whereHas('user', fn ($q) => $q->where('status', 'active'))
            ->get()
            ->sortBy(fn ($coach) => $coach->user?->name ?? '')
            ->values();

        $locations = Location::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.class-schedules.create', compact(
            'programs',
            'coaches',
            'locations'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateScheduleInput($request);

        $startsAt = Carbon::parse(
            $validated['class_date'] . ' ' . $validated['start_time'],
            'Asia/Jakarta'
        );

        if ($startsAt->lessThanOrEqualTo(now('Asia/Jakarta'))) {
            throw ValidationException::withMessages([
                'class_date' => 'Tanggal dan waktu mulai kelas harus di masa depan.',
            ]);
        }

        // AUTO_RECURRING_CLASS_SCHEDULE
        $schedule = DB::transaction(function () use ($validated) {
            $this->lockResources(
                (int) $validated['coach_id'],
                (int) $validated['location_id']
            );

            $this->validateActiveRelations($validated);
            $this->validateScheduleConflict($validated);

            $dayOfWeek = Carbon::parse($validated['class_date'])->dayOfWeekIso;

            $conflicts = WeeklySchedule::query()
                ->where('is_active', true)
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time']);

            if (
                (clone $conflicts)
                    ->where('coach_id', $validated['coach_id'])
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'start_time' => 'Coach sudah memiliki jadwal mingguan yang bertabrakan.',
                ]);
            }

            if (
                (clone $conflicts)
                    ->where('location_id', $validated['location_id'])
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'start_time' => 'Lokasi sudah digunakan oleh jadwal mingguan lain.',
                ]);
            }

            $template = WeeklySchedule::create([
                'program_id' => $validated['program_id'],
                'coach_id' => $validated['coach_id'],
                'location_id' => $validated['location_id'],
                'title' => $validated['title'],
                'swimming_level' => $validated['swimming_level'],
                'day_of_week' => $dayOfWeek,
                'start_date' => $validated['class_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'capacity' => $validated['capacity'],
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]);

            return ClassSchedule::create([
                ...$validated,
                'weekly_schedule_id' => $template->id,
                'status' => 'scheduled',
            ]);
        }, 3);

        try {
            app(\App\Services\WeeklyScheduleGenerator::class)->generate(4);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()
            ->route('admin.class-schedules.show', $schedule)
            ->with('success', 'Jadwal kelas berhasil dibuat dan otomatis berulang setiap minggu.');
    }

    public function show(ClassSchedule $classSchedule): View
    {
        $classSchedule->load([
            'program',
            'coach.user',
            'location',
            'bookings' => fn ($q) => $q
                ->with([
                    'participant.user',
                    'participantPackage.package',
                    'attendance',
                    'progressRecord',
                ])
                ->orderByDesc('booked_at'),
        ]);

        $bookings = $classSchedule->bookings;

        $activeCount = $bookings
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $activeOffersCount = $this->activeOffersCount($classSchedule);

        $statistics = [
            'active_offers' => $activeOffersCount,
            'total_bookings' => $bookings->count(),
            'active_bookings' => $activeCount,
            'completed_bookings' => $bookings
                ->where('status', 'completed')
                ->count(),
            'remaining_slots' => max(
                0,
                (int) $classSchedule->capacity - $activeCount - $activeOffersCount
            ),
        ];

        return view('admin.class-schedules.show', compact(
            'classSchedule',
            'statistics'
        ));
    }

    public function edit(ClassSchedule $classSchedule): View
    {
        $programs = Program::query()
            ->where(fn ($q) => $q
                ->where('status', 'active')
                ->orWhere('id', $classSchedule->program_id))
            ->orderBy('name')
            ->get();

        $coaches = Coach::with('user')
            ->where(fn ($q) => $q
                ->where('status', 'active')
                ->orWhere('id', $classSchedule->coach_id))
            ->get()
            ->sortBy(fn ($coach) => $coach->user?->name ?? '')
            ->values();

        $locations = Location::query()
            ->where(fn ($q) => $q
                ->where('status', 'active')
                ->orWhere('id', $classSchedule->location_id))
            ->orderBy('name')
            ->get();

        $activeBookingsCount = $classSchedule->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $activeOffersCount = $this->activeOffersCount($classSchedule);

        return view('admin.class-schedules.edit', compact(
            'classSchedule',
            'programs',
            'coaches',
            'locations',
            'activeBookingsCount',
            'activeOffersCount'
        ));
    }

    public function update(
        Request $request,
        ClassSchedule $classSchedule
    ): RedirectResponse {
        if ($classSchedule->status !== 'scheduled') {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with(
                    'error',
                    'Hanya jadwal berstatus terjadwal yang dapat diedit.'
                );
        }

        $validated = $this->validateScheduleInput($request);

        DB::transaction(function () use ($classSchedule, $validated) {
            $this->lockResources(
                (int) $validated['coach_id'],
                (int) $validated['location_id'],
                (int) $classSchedule->coach_id,
                (int) $classSchedule->location_id
            );

            $lockedSchedule = ClassSchedule::query()
                ->whereKey($classSchedule->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSchedule->status !== 'scheduled') {
                throw ValidationException::withMessages([
                    'status' => 'Status jadwal telah berubah. Muat ulang halaman.',
                ]);
            }

            $activeCount = $lockedSchedule->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            $activeOffersCount = $this->activeOffersCount($lockedSchedule);
            $occupiedCount = $activeCount + $activeOffersCount;

            if ((int) $validated['capacity'] < $occupiedCount) {
                throw ValidationException::withMessages([
                    'capacity' =>
                        "Kapasitas tidak boleh kurang dari {$occupiedCount} slot terpakai (booking aktif dan penawaran Waiting List).",
                ]);
            }

            if ($occupiedCount > 0) {
                $this->validateProtectedFields(
                    $lockedSchedule,
                    $validated
                );
            }

            $this->validateActiveRelations(
                $validated,
                $lockedSchedule
            );

            $this->validateScheduleConflict(
                $validated,
                $lockedSchedule->id
            );

            $lockedSchedule->update($validated);
        }, 3);

        return redirect()
            ->route('admin.class-schedules.show', $classSchedule)
            ->with('success', 'Jadwal kelas berhasil diperbarui.');
    }

    public function updateStatus(
        Request $request,
        ClassSchedule $classSchedule
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:scheduled,completed,cancelled',
            ],
        ]);

        $newStatus = $validated['status'];

        $cancellationReason = trim(
            (string) $request->input('cancellation_reason', '')
        );

        if (
            $newStatus === 'cancelled'
            && (
                $cancellationReason === ''
                || mb_strlen($cancellationReason) > 1000
            )
        ) {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with(
                    'error',
                    'Alasan libur wajib diisi (maksimal 1000 karakter).'
                );
        }

        if ($newStatus === $classSchedule->status) {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with('success', 'Status jadwal tidak berubah.');
        }

        if ($classSchedule->status !== 'scheduled') {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with(
                    'error',
                    'Jadwal yang sudah selesai atau dibatalkan tidak dapat diubah kembali.'
                );
        }

        if (!in_array($newStatus, ['completed', 'cancelled'], true)) {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with('error', 'Perubahan status tidak diizinkan.');
        }

        try {
            DB::transaction(function () use (
                $classSchedule,
                $newStatus,
                $cancellationReason
            ) {
                $lockedSchedule = ClassSchedule::query()
                    ->whereKey($classSchedule->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedSchedule->status !== 'scheduled') {
                    throw ValidationException::withMessages([
                        'status' => 'Status jadwal sudah berubah.',
                    ]);
                }

                if ($newStatus === 'completed') {
                    $error = $this->getScheduleCompletionError(
                        $lockedSchedule
                    );

                    if ($error !== null) {
                        throw ValidationException::withMessages([
                            'status' => $error,
                        ]);
                    }
                }

                if ($newStatus === 'cancelled') {
                    $bookings = Booking::query()
                        ->where('class_schedule_id', $lockedSchedule->id)
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get();

                    $bookingIds = $bookings->pluck('id');

                    $hasAttendance = $bookingIds->isNotEmpty()
                        && \App\Models\Attendance::query()
                            ->whereIn('booking_id', $bookingIds)
                            ->exists();

                    $hasProcessedBooking = $bookings->contains(
                        fn ($booking) =>
                            $booking->status === 'completed'
                            || (bool) $booking->session_deducted
                    );

                    if ($hasAttendance || $hasProcessedBooking) {
                        throw ValidationException::withMessages([
                            'status' =>
                                'Kelas tidak dapat diliburkan karena sudah '
                                . 'ada absensi atau sesi yang diproses. '
                                . 'Hubungi admin untuk koreksi data.',
                        ]);
                    }

                    $ids = $bookings
                        ->filter(
                            fn ($booking) => in_array(
                                $booking->status,
                                ['pending', 'confirmed'],
                                true
                            )
                        )
                        ->pluck('id');

                    if ($ids->isNotEmpty()) {
                        Booking::whereIn('id', $ids)->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancellation_reason' =>
                                'Kelas diliburkan oleh admin: '
                                . $cancellationReason,
                        ]);
                    }
                }

                // Jadwal yang tidak lagi terjadwal tidak boleh menyisakan
                // antrean maupun penawaran aktif. Riwayat accepted dipertahankan.
                $lockedSchedule->waitingLists()
                    ->whereIn('status', ['waiting', 'offered'])
                    ->update([
                        'status' => 'cancelled',
                        'expires_at' => null,
                    ]);

                $lockedSchedule->update([
                    'status' => $newStatus,
                ]);
            }, 3);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.class-schedules.show', $classSchedule)
                ->with(
                    'error',
                    collect($exception->errors())->flatten()->first()
                        ?? 'Status jadwal gagal diperbarui.'
                );
        }

        $message = $newStatus === 'completed'
            ? 'Jadwal berhasil ditandai selesai.'
            : 'Jadwal dibatalkan dan booking aktif telah dibatalkan.';

        return redirect()
            ->route('admin.class-schedules.show', $classSchedule)
            ->with('success', $message);
    }

    private function validateScheduleInput(Request $request): array
    {
        return $request->validate([
            'program_id' => [
                'required', 'integer', 'exists:programs,id',
            ],
            'coach_id' => [
                'required', 'integer', 'exists:coaches,id',
            ],
            'location_id' => [
                'required', 'integer', 'exists:locations,id',
            ],
            'title' => [
                'required', 'string', 'max:150',
            ],
            'swimming_level' => [
                'required',
                'in:all,beginner,basic,intermediate,advanced',
            ],
            'class_date' => [
                'required', 'date',
            ],
            'start_time' => [
                'required', 'date_format:H:i',
            ],
            'end_time' => [
                'required', 'date_format:H:i', 'after:start_time',
            ],
            'capacity' => [
                'required', 'integer', 'min:1', 'max:1000',
            ],
            'notes' => [
                'nullable', 'string', 'max:5000',
            ],
        ]);
    }

    private function activeOffersCount(ClassSchedule $schedule): int
    {
        return $schedule->waitingLists()
            ->where('status', 'offered')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->count();
    }

    private function validateProtectedFields(
        ClassSchedule $schedule,
        array $validated
    ): void {
        $fields = [
            'program_id',
            'swimming_level',
            'class_date',
            'start_time',
            'end_time',
        ];

        foreach ($fields as $field) {
            $current = $schedule->{$field};
            $new = $validated[$field];

            if ($field === 'class_date') {
                $current = $schedule->class_date->format('Y-m-d');
                $new = Carbon::parse($new)->format('Y-m-d');
            } elseif (in_array($field, ['start_time', 'end_time'], true)) {
                $current = substr((string) $current, 0, 5);
                $new = substr((string) $new, 0, 5);
            }

            if ((string) $current !== (string) $new) {
                throw ValidationException::withMessages([
                    $field =>
                        'Program, level, tanggal, dan waktu tidak dapat diubah selama masih ada booking aktif atau penawaran Waiting List aktif.',
                ]);
            }
        }
    }

    private function validateActiveRelations(
        array $validated,
        ?ClassSchedule $current = null
    ): void {
        $program = Program::findOrFail($validated['program_id']);

        if (
            $program->status !== 'active'
            && (!$current || (int) $current->program_id !== (int) $program->id)
        ) {
            throw ValidationException::withMessages([
                'program_id' => 'Program yang dipilih sedang nonaktif.',
            ]);
        }

        $coach = Coach::with('user')
            ->findOrFail($validated['coach_id']);

        $sameCoach = $current
            && (int) $current->coach_id === (int) $coach->id;

        if (
            ($coach->status !== 'active'
                || !$coach->user
                || $coach->user->status !== 'active')
            && !$sameCoach
        ) {
            throw ValidationException::withMessages([
                'coach_id' => 'Coach yang dipilih sedang nonaktif.',
            ]);
        }

        $location = Location::findOrFail($validated['location_id']);

        if (
            $location->status !== 'active'
            && (!$current || (int) $current->location_id !== (int) $location->id)
        ) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi yang dipilih sedang nonaktif.',
            ]);
        }
    }

    private function lockResources(
        int $coachId,
        int $locationId,
        ?int $oldCoachId = null,
        ?int $oldLocationId = null
    ): void {
        $coachIds = array_values(array_unique(array_filter([
            $coachId,
            $oldCoachId,
        ])));

        $locationIds = array_values(array_unique(array_filter([
            $locationId,
            $oldLocationId,
        ])));

        sort($coachIds);
        sort($locationIds);

        Coach::query()
            ->whereIn('id', $coachIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        Location::query()
            ->whereIn('id', $locationIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function validateScheduleConflict(
        array $validated,
        ?int $ignoreId = null
    ): void {
        $query = ClassSchedule::query()
            ->whereDate('class_date', $validated['class_date'])
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time']);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if (
            (clone $query)
                ->where('coach_id', $validated['coach_id'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'start_time' =>
                    'Coach sudah memiliki jadwal yang bertabrakan.',
            ]);
        }

        if (
            (clone $query)
                ->where('location_id', $validated['location_id'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'start_time' =>
                    'Lokasi sudah digunakan pada waktu tersebut.',
            ]);
        }
    }

    private function getScheduleCompletionError(
        ClassSchedule $schedule
    ): ?string {
        $end = Carbon::parse(
            $schedule->class_date->format('Y-m-d')
            . ' '
            . $schedule->end_time
        );

        if (now()->lt($end)) {
            return 'Kelas belum dapat diselesaikan sebelum '
                . $end->format('d-m-Y H:i') . ' WIB.';
        }

        $pending = $schedule->bookings()
            ->where('status', 'pending')
            ->count();

        if ($pending > 0) {
            return "Masih ada {$pending} booking menunggu konfirmasi.";
        }

        $confirmed = $schedule->bookings()
            ->where('status', 'confirmed')
            ->count();

        if ($confirmed > 0) {
            return "Masih ada {$confirmed} booking yang belum diproses kehadirannya.";
        }

        return null;
    }

    public function destroy(ClassSchedule $classSchedule): RedirectResponse
    {
        try {
            DB::transaction(function () use ($classSchedule) {
                $locked = ClassSchedule::query()
                    ->lockForUpdate()
                    ->findOrFail($classSchedule->id);

                if ($locked->status !== 'scheduled') {
                    throw ValidationException::withMessages([
                        'delete' => 'Hanya jadwal berstatus terjadwal yang dapat dihapus.',
                    ]);
                }

                if ($locked->weekly_schedule_id !== null) {
                    throw ValidationException::withMessages([
                        'delete' => 'Jadwal berulang tidak dapat dihapus. Gunakan fitur Liburkan Pertemuan.',
                    ]);
                }

                if ($locked->bookings()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Jadwal memiliki riwayat booking peserta.',
                    ]);
                }

                if ($locked->waitingLists()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Jadwal memiliki riwayat waiting list.',
                    ]);
                }

                $locked->delete();
            }, 3);
        } catch (ValidationException $exception) {
            return back()->with(
                'error',
                collect($exception->errors())->flatten()->first()
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);

            return back()->with(
                'error',
                'Jadwal masih terhubung dengan data lain.'
            );
        }

        return redirect()
            ->route('admin.class-schedules.index')
            ->with('success', 'Jadwal kelas berhasil dihapus.');
    }
}
