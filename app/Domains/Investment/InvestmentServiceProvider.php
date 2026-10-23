<?php

namespace App\Domains\Investment;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * مجال الاستثمار (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require من
 * ملفات routes الويب في نفس مواضعها القديمة، لأن ترتيب
 * الـ routes ومجموعة admin (prefix/name/middleware) جزء من سلوك المطابقة.
 */
class InvestmentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('investment::admin.plans.index') و view('investment::public.show')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'investment');

        // كلاسات Tailwind المشتركة في صفحات لوحة الاستثمار (كانت في AppServiceProvider على admin.investment.*)
        View::composer(['investment::admin.*'], function ($view) {
            $view->with([
                'invInputClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all',
                'invSelectClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all',
                'invTextareaClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-y min-h-[100px] transition-all',
                'invLabelClass' => 'block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2',
                'invBtnPrimary' => 'inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-600 to-orange-500 hover:from-amber-700 hover:to-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200 text-sm',
                'invBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'invBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition-all',
                'invSectionClass' => 'rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden',
            ]);
        });
    }
}
