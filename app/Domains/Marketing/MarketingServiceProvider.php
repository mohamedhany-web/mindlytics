<?php

namespace App\Domains\Marketing;

use App\Domains\Marketing\Console\Commands\ApplyMarketingPlanExecutionPenalties;
use App\Domains\Marketing\Console\Commands\RemindMarketingPlanEvents;
use App\Domains\Marketing\Support\MarketingPlanSettings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

/**
 * مجال التسويق: الحملات، خطط المشرفين، المونتاج، دورات التصميم، الإحالات، الاستبيانات، المناطق والتحليلات
 * (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعاتها (prefix/name/middleware) جزء من سلوك المطابقة.
 * الـ middleware (moderator.employee و RecordMarketingRegionVisit على مجموعة web) مسجّلة في bootstrap/app.php.
 */
class MarketingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('marketing::admin.popup-ads.index') و view('marketing::employee.marketing-plans.index')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'marketing');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApplyMarketingPlanExecutionPenalties::class,
                RemindMarketingPlanEvents::class,
            ]);
        }

        // كانت في withSchedule داخل bootstrap/app.php
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('marketing:remind-today-events')
                ->dailyAt(MarketingPlanSettings::reminderTime());
            $schedule->command('marketing:apply-execution-penalties')
                ->dailyAt(MarketingPlanSettings::confirmationDeadlineTime());
        });
    }
}
