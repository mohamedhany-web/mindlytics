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
                'waInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500',
                'waSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500',
                'waTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-y min-h-[120px]',
                'waLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'waBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-green-500 hover:from-emerald-700 hover:to-green-600 text-white text-sm font-semibold shadow-lg shadow-emerald-500/20 transition-all',
                'waBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'waBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all',
                'waSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
            ]);
        });

        View::composer(['messaging::admin.meta-social.*'], function ($view) {
            $view->with([
                'smInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500',
                'smSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500',
                'smTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 resize-y min-h-[120px]',
                'smLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'smBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-500 hover:from-sky-700 hover:to-blue-600 text-white text-sm font-semibold shadow-lg shadow-sky-500/20 transition-all',
                'smBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'smBtnMeta' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0866FF] hover:bg-[#0654d4] text-white text-sm font-semibold shadow-lg shadow-[#0866FF]/20 transition-all',
                'smSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
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
