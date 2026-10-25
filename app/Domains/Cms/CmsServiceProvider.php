<?php

namespace App\Domains\Cms;

use Illuminate\Support\ServiceProvider;

/**
 * مجال المحتوى والدعم: المدونة، الأسئلة الشائعة، الوسائط، رسائل التواصل، تذاكر الدعم، التقويم، تقارير الطلاب
 * (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعاتها (prefix/name/middleware) جزء من سلوك المطابقة.
 */
class CmsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('cms::admin.blog.index') و view('cms::public.contact')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'cms');
    }
}
