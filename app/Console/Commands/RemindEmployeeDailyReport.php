<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmployeeDailyReportService;
use Illuminate\Console\Command;

class RemindEmployeeDailyReport extends Command
{
    protected $signature = 'employees:remind-daily-report';

    protected $description = 'Remind employees to submit daily work reports';

    public function handle(EmployeeDailyReportService $service): int
    {
        $sent = 0;
        $employees = User::employees()->where('is_active', true)->get();

        foreach ($employees as $emp) {
            if (! $service->employeeRequiresReport($emp, today())) {
                continue;
            }
            $service->sendReminder($emp);
            $sent++;
        }

        $this->info("Employee daily report reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
