<?php

namespace App\Domains\Places;

use Illuminate\Support\ServiceProvider;

/**
 * مجال الأماكن (مقرات الكورسات الحضورية): سجل الاستخدام، المصروفات اليومية، التسويات الشهرية، وبوابة المكان
 * (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة.
 */
class PlacesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('places::admin.settlements.index') و view('places::office.dashboard')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'places');
    }
}
