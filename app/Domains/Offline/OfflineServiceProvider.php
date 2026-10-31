<?php

namespace App\Domains\Offline;

use Illuminate\Support\ServiceProvider;

/**
 * مجال الكورسات الحضورية والمجموعات الأونلاين والورش (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة.
 * ⚠️ offline_curriculum_items.item_type يحفظ أسماء الكلاسات القديمة (App\Models\OfflineLecture …):
 * الكتابة والمقارنة عبر Relation::getMorphAlias / getMorphedModel و App\Support\MorphMap.
 */
class OfflineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('offline::admin.offline-courses.index') و view('offline::instructor.offline-curriculum.index')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'offline');
    }
}
