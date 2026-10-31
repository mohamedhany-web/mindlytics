<?php

namespace App\Domains\Scholarship;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * مجال المنح الدراسية (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require من
 * routes/web/auth.php و routes/web/admin.php و routes/web/instructor.php في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعات admin/instructor (prefix/name/middleware) جزء من سلوك المطابقة.
 */
class ScholarshipServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('scholarship::admin.programs.index') و view('scholarship::instructor.index') و view('scholarship::public.show')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'scholarship');

        // كلاسات Tailwind المشتركة في صفحات لوحة المنح (كانت في AppServiceProvider على admin.scholarships.*)
        View::composer(['scholarship::admin.*'], function ($view) {
            $view->with([
                'schInputClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all',
                'schSelectClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all',
                'schTextareaClass' => 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y min-h-[100px] transition-all',
                'schLabelClass' => 'block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2',
                'schBtnPrimary' => 'inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200 text-sm',
                'schBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'schBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition-all',
                'schSectionClass' => 'rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden',
            ]);
        });
    }
}
