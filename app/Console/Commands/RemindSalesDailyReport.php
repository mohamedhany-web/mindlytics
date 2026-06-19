<?php

namespace App\Console\Commands;

use App\Models\SalesDailyReport;
use App\Models\User;
use App\Services\SalesDailyReportService;
use App\Services\SalesNotificationService;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindSalesDailyReport extends Command
{
    protected $signature = 'sales:remind-daily-report';

    protected $description = 'Remind sales reps to submit daily report before deadline (in-app + email)';

    public function handle(SalesNotificationService $notifications, SalesDailyReportService $dailyReports): int
    {
        if (! SalesDailyReportSettings::enabled()) {
            $this->info('Daily reports disabled — skipped.');

            return self::SUCCESS;
        }

        if (! $this->isReminderWindowNow()) {
            $this->info('Outside reminder window — skipped.');

            return self::SUCCESS;
        }

        $count = 0;
        $reps = User::salesEmployees()->where('is_active', true)->get();

        foreach ($reps as $rep) {
            if (! $dailyReports->isWorkDay(today(), $rep)) {
                continue;
            }

            $submitted = SalesDailyReport::forUser($rep->id)
                ->whereDate('report_date', today())
                ->where('status', SalesDailyReport::STATUS_SUBMITTED)
                ->exists();

            if (! $submitted) {
                $dailyReports->syncAutoDraft($rep, today());
                $notifications->notifyDailyReportReminder($rep);
                $count++;
            }
        }

        $this->info("Daily report reminders sent: {$count}");

        return self::SUCCESS;
    }

    private function isReminderWindowNow(): bool
    {
        $settings = SalesDailyReportSettings::all();
        $deadline = (string) ($settings['deadline_time'] ?? '23:59');
        $minutesBefore = max(5, (int) ($settings['reminder_minutes_before'] ?? 15));

        if (! preg_match('/^(\d{1,2}):(\d{2})$/', $deadline, $m)) {
            return now()->format('H:i') === '17:00';
        }

        $deadlineAt = Carbon::today()->setTime((int) $m[1], (int) $m[2]);
        $reminderStart = $deadlineAt->copy()->subMinutes($minutesBefore);
        $reminderEnd = $reminderStart->copy()->addMinutes(14);

        return now()->betweenIncluded($reminderStart, $reminderEnd);
    }
}
