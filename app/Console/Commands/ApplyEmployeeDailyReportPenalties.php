<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmployeeDailyReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyEmployeeDailyReportPenalties extends Command
{
    protected $signature = 'employees:apply-daily-report-penalties {--date=}';

    protected $description = 'Apply salary deductions for missing employee daily reports';

    public function handle(EmployeeDailyReportService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : yesterday();

        $count = 0;
        foreach (User::employees()->where('is_active', true)->get() as $emp) {
            if ($service->applyPenaltyForDate($emp, $date)) {
                $count++;
            }
        }

        $this->info("Penalties applied: {$count} for {$date->toDateString()}");

        return self::SUCCESS;
    }
}
