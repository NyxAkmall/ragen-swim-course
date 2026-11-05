<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\Coach;
use App\Models\Location;
use App\Models\WeeklySchedule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class WeeklyScheduleGenerator
{
    public function generate(int $weeks = 4): int
    {
        $weeks = max(1, min($weeks, 12));
        $now = CarbonImmutable::now();
        $created = 0;

        WeeklySchedule::query()
            ->where('is_active', true)
            ->chunkById(100, function ($templates) use ($weeks, $now, &$created) {
                foreach ($templates as $template) {
                    $day = (int) $template->day_of_week;

                    if ($day < 1 || $day > 7) {
                        continue;
                    }

                    $firstDate = $now->startOfWeek()
                        ->addDays($day - 1);

                    $firstStart = $firstDate
                        ->setTimeFromTimeString($template->start_time);

                    if ($firstStart->lessThanOrEqualTo($now)) {
                        $firstDate = $firstDate->addWeek();
                    }

                    if ($template->start_date) {
                        $startDate = CarbonImmutable::parse(
                            $template->start_date
                        )->startOfDay();

                        while ($firstDate->lessThan($startDate)) {
                            $firstDate = $firstDate->addWeek();
                        }
                    }

                    $available = 0;

                    for ($week = 0; $week < 52 && $available < $weeks; $week++) {
                        $date = $firstDate->addWeeks($week);

                        $available += DB::transaction(function () use (
                            $template,
                            $date,
                            &$created
                        ) {
                            $lockedTemplate = WeeklySchedule::query()
                                ->lockForUpdate()
                                ->find($template->id);

                            if (!$lockedTemplate || !$lockedTemplate->is_active) {
                                return;
                            }

                            $dateString = $date->toDateString();

                            if (
                                $lockedTemplate->start_date &&
                                $dateString < \Carbon\CarbonImmutable::parse(
                                    $lockedTemplate->start_date
                                )->toDateString()
                            ) {
                                return;
                            }

                            $exists = ClassSchedule::query()
                                ->where('weekly_schedule_id', $lockedTemplate->id)
                                ->whereDate('class_date', $dateString)
                                ->exists();

                            if ($exists) {
                                return ClassSchedule::query()
                                    ->where('weekly_schedule_id', $lockedTemplate->id)
                                    ->whereDate('class_date', $dateString)
                                    ->where('status', 'scheduled')
                                    ->exists() ? 1 : 0;
                            }

                            Coach::query()
                                ->whereKey($lockedTemplate->coach_id)
                                ->lockForUpdate()
                                ->firstOrFail();

                            Location::query()
                                ->whereKey($lockedTemplate->location_id)
                                ->lockForUpdate()
                                ->firstOrFail();

                            $conflicts = ClassSchedule::query()
                                ->whereDate('class_date', $dateString)
                                ->where('status', '!=', 'cancelled')
                                ->where('start_time', '<', $lockedTemplate->end_time)
                                ->where('end_time', '>', $lockedTemplate->start_time);

                            $coachConflict = (clone $conflicts)
                                ->where('coach_id', $lockedTemplate->coach_id)
                                ->exists();

                            $locationConflict = (clone $conflicts)
                                ->where('location_id', $lockedTemplate->location_id)
                                ->exists();

                            if ($coachConflict || $locationConflict) {
                                logger()->warning(
                                    'Jadwal mingguan dilewati karena bentrok.',
                                    [
                                        'weekly_schedule_id' => $lockedTemplate->id,
                                        'class_date' => $dateString,
                                        'coach_conflict' => $coachConflict,
                                        'location_conflict' => $locationConflict,
                                    ]
                                );

                                return;
                            }

                            ClassSchedule::create([
                                'weekly_schedule_id' => $lockedTemplate->id,
                                'program_id' => $lockedTemplate->program_id,
                                'coach_id' => $lockedTemplate->coach_id,
                                'location_id' => $lockedTemplate->location_id,
                                'title' => $lockedTemplate->title,
                                'swimming_level' => $lockedTemplate->swimming_level,
                                'class_date' => $dateString,
                                'start_time' => $lockedTemplate->start_time,
                                'end_time' => $lockedTemplate->end_time,
                                'capacity' => $lockedTemplate->capacity,
                                'status' => 'scheduled',
                                'notes' => $lockedTemplate->notes,
                            ]);

                            $created++;

                            return 1;
                        });
                    }
                }
            });

        return $created;
    }
}
