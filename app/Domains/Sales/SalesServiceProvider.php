<?php

namespace App\Domains\Sales;

use App\Domains\Sales\Console\Commands\ApplySalesDailyKpiPenalties;
use App\Domains\Sales\Console\Commands\ApplySalesDailyReportPenalties;
use App\Domains\Sales\Console\Commands\EnforceSalesImportContact;
use App\Domains\Sales\Console\Commands\EvaluateSalesEnforcement;
use App\Domains\Sales\Console\Commands\RemindSalesDailyReport;
use App\Domains\Sales\Console\Commands\SendSalesFollowupReminders;
use App\Domains\Accounting\Events\PaymentRefunded;
use App\Domains\Accounting\Models\Order;
use App\Domains\Sales\Services\SalesCommissionReversalService;
use App\Domains\Sales\Services\SalesLeadOrderLinker;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * مجال المبيعات (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require من
 * routes/web/admin.php و routes/web/employee.php و routes/web/public.php في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعات admin/employee (prefix/name/middleware) جزء من سلوك المطابقة.
 * الـ middleware aliases (sales.employee / sales.manager / sales.staff) مسجّلة في bootstrap/app.php.
 */
class SalesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('sales::admin.leads.index') و view('sales::employee.dashboard') و view('sales::manager.dashboard')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'sales');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApplySalesDailyKpiPenalties::class,
                ApplySalesDailyReportPenalties::class,
                EnforceSalesImportContact::class,
                EvaluateSalesEnforcement::class,
                RemindSalesDailyReport::class,
                SendSalesFollowupReminders::class,
            ]);
        }

        // كانت في withSchedule داخل bootstrap/app.php
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('sales:send-followup-reminders')->dailyAt('08:00');
            $schedule->command('sales:remind-daily-report')->everyFifteenMinutes();
            $schedule->command('sales:evaluate-enforcement')->dailyAt('09:30');
            $schedule->command('sales:apply-daily-report-penalties')->dailyAt('01:30');
            $schedule->command('sales:apply-daily-kpi-penalties')->dailyAt('01:45');
            $schedule->command('sales:enforce-import-contact')->dailyAt('10:00');
        });

        // ربط الـ Lead بالطلب المدفوع عند اعتماده من أي مسار (يدوي، كاشير، فواتيرك، أوفلاين) — docs/SALES_INTEGRITY.md
        Order::saved(function (Order $order): void {
            if ($order->status === Order::STATUS_APPROVED && ($order->wasRecentlyCreated || $order->wasChanged('status'))) {
                DB::afterCommit(fn () => app(SalesLeadOrderLinker::class)->handleApprovedOrder($order));
            }
        });

        // استرجاع العمولة عند استرداد قيمة الطلب
        Event::listen(PaymentRefunded::class, [SalesCommissionReversalService::class, 'handle']);
    }
}
