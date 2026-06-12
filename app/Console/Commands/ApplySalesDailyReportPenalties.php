<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SalesDailyReportService;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplySalesDailyReportPenalties extends Command
{
    protected $signature = 'sales:apply-daily-report-penalties {--date= : تاريخ Y-m-d (افتراضي: أمس)}';

    protected $description = 'تطبيق خصومات تلقائية على موظفي المبيعات الذين لم يسلّموا التقرير اليومي';

    public function handle(SalesDailyReportService $service): int
    {
        if (! SalesDailyReportSettings::enabled() || ! SalesDailyReportSettings::penaltyEnabled()) {
            $this->info('الخصم التلقائي للتقارير اليومية معطّل.');

            return self::SUCCESS;
        }

        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $reps = User::salesEmployees()->where('is_active', true)->get();
        $applied = 0;

        foreach ($reps as $rep) {
            $deduction = $service->applyPenaltyForDate($rep, $date);
            if ($deduction) {
                $applied++;
                $this->line("خصم: {$rep->name} — {$date->toDateString()} — {$deduction->deduction_number}");
            }
        }

        $this->info("تم. خصومات جديدة: {$applied} من {$reps->count()} موظف.");

        return self::SUCCESS;
    }
}
