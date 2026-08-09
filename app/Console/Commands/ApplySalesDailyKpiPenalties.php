<?php

namespace App\Console\Commands;

use App\Services\SalesDailyKpiPenaltyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplySalesDailyKpiPenalties extends Command
{
    protected $signature = 'sales:apply-daily-kpi-penalties {--date= : تاريخ Y-m-d (افتراضي: أمس)}';

    protected $description = 'تطبيق خصومات KPI اليومي على موظفي المبيعات الذين لم يحققوا الأهداف الموثّقة';

    public function handle(SalesDailyKpiPenaltyService $service): int
    {
        if (! $service->enabled()) {
            $this->info('خصم KPI اليومي معطّل.');

            return self::SUCCESS;
        }

        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $applied = $service->applyForAllReps($date);
        $this->info("تم. خصومات KPI جديدة: {$applied} — التاريخ {$date->toDateString()}");

        return self::SUCCESS;
    }
}
