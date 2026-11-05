<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklySchedule;
use App\Models\Program;
use App\Models\Coach;
use App\Models\Location;
use App\Services\WeeklyScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WeeklyScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input("search", ""));

        $schedules = WeeklySchedule::query()
            ->with(["program", "coach.user", "location"])
            ->withCount("classSchedules")
            ->when($search !== "", function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where("title", "like", "%{$search}%")
                        ->orWhereHas("program", fn ($q) =>
                            $q->where("name", "like", "%{$search}%"))
                        ->orWhereHas("coach.user", fn ($q) =>
                            $q->where("name", "like", "%{$search}%"))
                        ->orWhereHas("location", fn ($q) =>
                            $q->where("name", "like", "%{$search}%"));
                });
            })
            ->orderBy("day_of_week")
            ->orderBy("start_time")
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            "total" => WeeklySchedule::count(),
            "active" => WeeklySchedule::where("is_active", true)->count(),
            "inactive" => WeeklySchedule::where("is_active", false)->count(),
        ];

        return view("admin.weekly-schedules.index", compact(
            "schedules",
            "statistics",
            "search"
        ));
    }

    public function create(): View
    {
        $programs = Program::where("status", "active")
            ->orderBy("name")
            ->get();

        $coaches = Coach::with("user")
            ->where("status", "active")
            ->whereHas("user", fn ($q) => $q->where("status", "active"))
            ->get()
            ->sortBy(fn ($coach) => $coach->user?->name ?? "")
            ->values();

        $locations = Location::where("status", "active")
            ->orderBy("name")
            ->get();

        return view("admin.weekly-schedules.create", compact(
            "programs",
            "coaches",
            "locations"
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateInput($request);
        $this->validateRelations($data);

        $weekly = DB::transaction(function () use ($data) {
            $this->lockWeeklyResources($data);
            $this->validateWeeklyConflict($data);

            return WeeklySchedule::create([
                ...$data,
                'is_active' => true,
            ]);
        }, 3);

        return redirect()
            ->route('admin.weekly-schedules.index')
            ->with('success', 'Template jadwal mingguan berhasil ditambahkan.');
    }

    public function edit(WeeklySchedule $weeklySchedule): View
    {
        $programs = Program::orderBy('name')->get();

        $coaches = Coach::with('user')
            ->get()
            ->sortBy(fn ($coach) => $coach->user?->name ?? '')
            ->values();

        $locations = Location::orderBy('name')->get();

        return view('admin.weekly-schedules.edit', compact(
            'weeklySchedule',
            'programs',
            'coaches',
            'locations'
        ));
    }

    public function update(
        Request $request,
        WeeklySchedule $weeklySchedule
    ): RedirectResponse {
        $data = $this->validateInput($request);
        $this->validateRelations($data, $weeklySchedule);

        DB::transaction(function () use ($weeklySchedule, $data) {
            $locked = WeeklySchedule::query()
                ->lockForUpdate()
                ->findOrFail($weeklySchedule->id);

            $this->lockWeeklyResources($data);
            if ($locked->is_active) {
                $this->validateWeeklyConflict($data, $locked->id);
            }

            $locked->update($data);
        }, 3);

        return redirect()
            ->route('admin.weekly-schedules.index')
            ->with(
                'success',
                'Template diperbarui. Jadwal kelas yang sudah dibuat tidak berubah.'
            );
    }

    public function updateStatus(
        \Illuminate\Http\Request $request,
        WeeklySchedule $weeklySchedule
    ): RedirectResponse {
        $action = $request->validate([
            'action' => ['required', 'in:start,stop'],
        ])['action'];

        $changed = DB::transaction(function () use ($weeklySchedule, $action) {
            $locked = WeeklySchedule::query()
                ->lockForUpdate()
                ->findOrFail($weeklySchedule->id);

            if ($action === 'stop' && !$locked->is_active) {
                return false;
            }

            if ($action === 'start' && $locked->is_active) {
                return false;
            }

            if (!$locked->is_active) {
                $data = $locked->only([
                    'program_id', 'coach_id', 'location_id',
                    'day_of_week', 'start_time', 'end_time',
                ]);

                // Aktivasi ulang wajib memakai relasi yang masih aktif.
                $this->validateRelations($data);
                $this->lockWeeklyResources($data);
                $this->validateWeeklyConflict($data, $locked->id);
            }

            $wasActive = (bool) $locked->is_active;

            $locked->update([
                'is_active' => !$wasActive,
            ]);

            if ($wasActive) {
                $futureSchedules = $locked->classSchedules()
                    ->where('status', 'scheduled')
                    ->whereDate('class_date', '>=', now('Asia/Jakarta')->toDateString())
                    ->lockForUpdate()
                    ->get();

                foreach ($futureSchedules as $schedule) {
                    $startsAt = \Carbon\Carbon::parse(
                        $schedule->class_date->format('Y-m-d') . ' ' . $schedule->start_time,
                        'Asia/Jakarta'
                    );

                    if ($startsAt->lessThanOrEqualTo(now('Asia/Jakarta'))) {
                        continue;
                    }

                    if ($schedule->bookings()->exists()) {
                        continue;
                    }

                    if ($schedule->waitingLists()->exists()) {
                        continue;
                    }

                    $schedule->update([
                        'status' => 'cancelled',
                    ]);
                }
            }

            return true;
        }, 3);

        if ($action === 'start' && $changed) {
            try {
                $created = app(WeeklyScheduleGenerator::class)->generate(4);

                return back()->with(
                    'success',
                    "Pengulangan diaktifkan. Generator membuat {$created} jadwal baru."
                );
            } catch (\Throwable $exception) {
                report($exception);

                return back()->with(
                    'warning',
                    'Pengulangan sudah aktif, tetapi pembuatan jadwal otomatis gagal. Sistem akan mencoba kembali melalui cron.'
                );
            }
        }

        return back()->with(
            'success',
            $changed
                ? 'Status pengulangan berhasil diperbarui.'
                : 'Status pengulangan sudah sesuai.'
        );
    }

    private function validateInput(Request $request): array
    {
        return $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'coach_id' => ['required', 'integer', 'exists:coaches,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'title' => ['required', 'string', 'max:150'],
            'swimming_level' => [
                'required',
                'in:all,beginner,basic,intermediate,advanced',
            ],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function validateRelations(
        array $data,
        ?WeeklySchedule $current = null
    ): void {
        $program = Program::findOrFail($data['program_id']);

        if (
            $program->status !== 'active'
            && (!$current || $current->program_id != $program->id)
        ) {
            throw ValidationException::withMessages([
                'program_id' => 'Program sedang nonaktif.',
            ]);
        }

        $coach = Coach::with('user')->findOrFail($data['coach_id']);

        if (
            ($coach->status !== 'active'
                || !$coach->user
                || $coach->user->status !== 'active')
            && (!$current || $current->coach_id != $coach->id)
        ) {
            throw ValidationException::withMessages([
                'coach_id' => 'Coach sedang nonaktif.',
            ]);
        }

        $location = Location::findOrFail($data['location_id']);

        if (
            $location->status !== 'active'
            && (!$current || $current->location_id != $location->id)
        ) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi sedang nonaktif.',
            ]);
        }
    }


    private function lockWeeklyResources(array $data): void
    {
        Coach::whereKey($data['coach_id'])
            ->lockForUpdate()
            ->firstOrFail();

        Location::whereKey($data['location_id'])
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function validateWeeklyConflict(
        array $data,
        ?int $ignoreId = null
    ): void {
        $query = WeeklySchedule::query()
            ->where('is_active', true)
            ->where('day_of_week', $data['day_of_week'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ((clone $query)
            ->where('coach_id', $data['coach_id'])
            ->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'Coach sudah memiliki jadwal mingguan pada waktu tersebut.',
            ]);
        }

        if ((clone $query)
            ->where('location_id', $data['location_id'])
            ->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'Lokasi sudah digunakan oleh jadwal mingguan lain.',
            ]);
        }
    }

    public function destroy(WeeklySchedule $weeklySchedule): RedirectResponse
    {
        try {
            DB::transaction(function () use ($weeklySchedule) {
                $locked = WeeklySchedule::query()
                    ->lockForUpdate()
                    ->findOrFail($weeklySchedule->id);

                if ($locked->is_active) {
                    throw ValidationException::withMessages([
                        'delete' => 'Nonaktifkan template terlebih dahulu.',
                    ]);
                }

                if ($locked->classSchedules()->exists()) {
                    throw ValidationException::withMessages([
                        'delete' => 'Template masih memiliki riwayat jadwal kelas.',
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
                'Template masih terhubung dengan data lain.'
            );
        }

        return redirect()
            ->route('admin.weekly-schedules.index')
            ->with('success', 'Template mingguan berhasil dihapus.');
    }
}
