<?php

namespace App\Console\Commands;

use App\Services\WeeklyScheduleGenerator;
use Illuminate\Console\Command;
use Throwable;

class GenerateWeeklySchedules extends Command
{
    protected $signature = 'schedules:generate-weekly {--weeks=4}';

    protected $description = 'Membuat jadwal kelas dari template mingguan aktif';

    public function handle(WeeklyScheduleGenerator $generator): int
    {
        $weeks = filter_var(
            $this->option('weeks'),
            FILTER_VALIDATE_INT
        );

        if ($weeks === false || $weeks < 1 || $weeks > 12) {
            $this->error('Jumlah minggu harus antara 1 sampai 12.');
            return self::FAILURE;
        }

        try {
            $created = $generator->generate($weeks);
            $this->info("Berhasil membuat {$created} jadwal kelas.");
            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Generator gagal. Periksa log Laravel.');
            return self::FAILURE;
        }
    }
}
