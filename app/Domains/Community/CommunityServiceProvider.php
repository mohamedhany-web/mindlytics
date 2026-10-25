<?php

namespace App\Domains\Community;

use Illuminate\Support\ServiceProvider;

/**
 * مجال المجتمع: بوابة المجتمع (المساهمون، البيانات، النماذج، المسابقات)، مجتمع الكورس، المحادثات بين الطلاب،
 * الرحلة والبورتفوليو، الأوسمة والإنجازات (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة،
 * لأن ترتيب الـ routes ومجموعاتها (prefix/name/middleware) جزء من سلوك المطابقة.
 * ⚠️ course_community_reactions.reactable_type و journey_share_events.shareable_type تحفظ أسماء الكلاسات القديمة
 * (App\Models\…): الكتابة والمقارنة عبر Relation::getMorphAlias و App\Support\MorphMap.
 */
class CommunityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('community::portal.dashboard') و view('community::student.portfolio.index') و view('community::public.journey.show')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'community');
    }
}
