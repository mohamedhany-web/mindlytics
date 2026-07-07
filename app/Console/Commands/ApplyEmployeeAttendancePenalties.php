<?php

namespace App\Console\Commands;

use App\Services\EmployeeAttendancePenaltyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyEmployeeAttendancePenalties extends Command
{
    protected $signature = 'employees:apply-attendance-penalties {--date=}';

    protected $description = 'Apply salary deductions for employee late arrival, absence, and incomplete hours';

    public function handle(EmployeeAttendancePenaltyService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : yesterday();

        $counts = $service->processDate($date);

        $this->info(sprintf(
            'Attendance penalties for %s — late: %d, absence: %d, incomplete: %d',
            $date->toDateString(),
            $counts['late'],
            $counts['absence'],
            $counts['incomplete'],
        ));

        return self::SUCCESS;
    }
}
