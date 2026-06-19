<?php

namespace App\Console\Commands;

use App\Services\MarketingPlanEventAutomationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindMarketingPlanEvents extends Command
{
    protected $signature = 'marketing:remind-today-events {--date=}';

    protected $description = 'تذكير الموظفين بمحتوى خطة التسويق المجدول لليوم';

    public function handle(MarketingPlanEventAutomationService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : today();

        $count = $service->sendTodayReminders($date);
        $this->info("تم إرسال {$count} تذكير/تذكيرات لتاريخ {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
