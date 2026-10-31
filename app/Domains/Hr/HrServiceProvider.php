<?php

namespace App\Domains\Hr;

use App\Domains\Hr\Console\Commands\ApplyEmployeeAttendancePenalties;
use App\Domains\Hr\Console\Commands\ApplyEmployeeDailyReportPenalties;
use App\Domains\Hr\Console\Commands\CheckEmployeePresence;
use App\Domains\Hr\Console\Commands\CleanupOutOfEmploymentDeductions;
use App\Domains\Hr\Console\Commands\RemindEmployeeDailyReport;
use App\Domains\Hr\Models\EmployeeSalaryDeduction;
use App\Domains\Hr\Observers\EmployeeSalaryDeductionObserver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * مجال الموارد البشرية (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require من
 * routes/web/admin.php و routes/web/employee.php و routes/web/public.php في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعات admin/employee (prefix/name/middleware) جزء من سلوك المطابقة.
 */
class HrServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('hr::admin.employees.index') و view('hr::employee.tasks.index') و view('hr::public.careers.index')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'hr');

        // كلاسات Tailwind المشتركة في صفحات التوظيف (كانت في AppServiceProvider على admin.hr.*)
        View::composer([
            'hr::admin.recruitment.jobs.*',
            'hr::admin.recruitment.applications.*',
            'hr::admin.recruitment.rubrics.*',
        ], function ($view) {
            $view->with([
                'hrInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500',
                'hrSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500',
                'hrTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 resize-y min-h-[100px]',
                'hrLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'hrBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white text-sm font-semibold shadow-lg shadow-pink-500/20 transition-all',
                'hrBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'hrBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all',
                'hrSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
            ]);
        });

        EmployeeSalaryDeduction::observe(EmployeeSalaryDeductionObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApplyEmployeeAttendancePenalties::class,
                ApplyEmployeeDailyReportPenalties::class,
                CheckEmployeePresence::class,
                CleanupOutOfEmploymentDeductions::class,
                RemindEmployeeDailyReport::class,
            ]);
        }

        // كانت في withSchedule داخل bootstrap/app.php
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('employees:remind-daily-report')->dailyAt('16:30');
            $schedule->command('employees:apply-daily-report-penalties')->dailyAt('02:00');
            $schedule->command('employees:apply-attendance-penalties')->dailyAt('02:30');
            $schedule->command('employees:check-presence')->everyMinute();
        });
    }
}
