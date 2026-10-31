<?php

namespace App\Domains\Messaging;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * مجال الرسائل: WhatsApp و Meta (Social + Ads) (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة.
 * ⚠️ روابط الـ webhooks (webhooks/whatsapp, webhooks/meta-social) مسجّلة عند Meta ومستثناة من CSRF
 * في bootstrap/app.php – لا تتغير.
 */
class MessagingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('messaging::admin.whatsapp.index') و view('messaging::admin.meta-social.inbox') و view('messaging::employee.whatsapp.queue')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'messaging');

        // كلاسات Tailwind المشتركة (كانت في AppServiceProvider على admin.whatsapp.* و admin.meta-social.*)
        View::composer(['messaging::admin.whatsapp.*'], function ($view) {
            $view->with([
                'waInputClass' => 'crm-input w-full',
                'waSelectClass' => 'crm-input w-full',
                'waTextareaClass' => 'crm-input w-full min-h-[120px]',
                'waLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'waBtnPrimary' => 'crm-btn crm-btn-primary',
                'waBtnSecondary' => 'crm-btn crm-btn-slate',
                'waBtnDark' => 'crm-btn crm-btn-dark',
                'waSectionClass' => 'crm-card overflow-hidden',
            ]);
        });

        View::composer(['messaging::admin.meta-social.*'], function ($view) {
            $view->with([
                'smInputClass' => 'crm-input w-full',
                'smSelectClass' => 'crm-input w-full',
                'smTextareaClass' => 'crm-input w-full min-h-[120px]',
                'smLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'smBtnPrimary' => 'crm-btn crm-btn-primary',
                'smBtnSecondary' => 'crm-btn crm-btn-slate',
                'smBtnMeta' => 'crm-btn crm-btn-dark',
                'smSectionClass' => 'crm-card overflow-hidden',
            ]);
        });

        // كانت في withSchedule داخل bootstrap/app.php
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            // طابور الواتساب فقط — معزول عن sales والتسجيل وغيرها
            $schedule->command('queue:work --queue=whatsapp --stop-when-empty --max-time=55 --max-jobs=50')
                ->everyMinute()
                ->withoutOverlapping()
                ->runInBackground();
        });
    }
}
