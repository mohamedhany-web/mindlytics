<?php

namespace App\Console\Commands;

use App\Services\MarketingPlanEventAutomationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyMarketingPlanExecutionPenalties extends Command
{
    protected $signature = 'marketing:apply-execution-penalties {--date=}';

    protected $description = 'غرامات عدم تأكيد تنفيذ محتوى خطة التسويق';

    public function handle(MarketingPlanEventAutomationService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : today();

        $count = $service->applyExecutionPenalties($date);
        $this->info("تم تطبيق {$count} غرامة/غرامات لتاريخ {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
