<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * خريطة الـ polymorphic types المحفوظة في أعمدة *_type
 * (curriculum_items.item_type، offline_curriculum_items.item_type،
 * course_community_reactions.reactable_type، personal_access_tokens.tokenable_type).
 *
 * المفاتيح هي نفس النصوص المحفوظة حالياً في الداتابيز ومكتوبة كنص ثابت عمداً (وليس ::class):
 * عند نقل Model إلى app/Domains يتغير ::class، لكن المفتاح يبقى كما هو فتبقى البيانات القديمة صالحة
 * ولا نحتاج migration. builder.blade.php والـ validation في CurriculumController يرسلان هذه النصوص أيضاً.
 *
 * بعد النقل: أي مقارنة مثل ->where('item_type', Lecture::class) يجب أن تصبح
 * ->where('item_type', Relation::getMorphAlias(Lecture::class)).
 */
final class MorphMap
{
    /** @return array<string, class-string> */
    public static function map(): array
    {
        return [
            // Shared
            'App\Models\User' => \App\Models\User::class,

            // curriculum_items.item_type
            'App\Models\Lecture' => \App\Domains\Learning\Models\Lecture::class,
            'App\Models\Assignment' => \App\Domains\Learning\Models\Assignment::class,
            'App\Models\AdvancedExam' => \App\Domains\Learning\Models\AdvancedExam::class,
            'App\Models\CourseLesson' => \App\Domains\Learning\Models\CourseLesson::class,
            'App\Models\LearningPattern' => \App\Domains\Learning\Models\LearningPattern::class,

            // offline_curriculum_items.item_type
            'App\Models\OfflineLecture' => \App\Domains\Offline\Models\OfflineLecture::class,
            'App\Models\OfflineCourseResource' => \App\Domains\Offline\Models\OfflineCourseResource::class,
            'App\Models\OfflineActivity' => \App\Domains\Offline\Models\OfflineActivity::class,
            'App\Models\OfflineCurriculumNote' => \App\Domains\Offline\Models\OfflineCurriculumNote::class,

            // course_community_reactions.reactable_type
            'App\Models\CourseCommunityPost' => \App\Domains\Community\Models\CourseCommunityPost::class,
            'App\Models\CourseCommunityComment' => \App\Domains\Community\Models\CourseCommunityComment::class,

            // journey_share_events.shareable_type (يُكتب ويُقارن عبر Relation::getMorphAlias)
            'App\Models\PortfolioProject' => \App\Domains\Community\Models\PortfolioProject::class,
            'App\Models\JourneyProfile' => \App\Domains\Community\Models\JourneyProfile::class,
        ];
    }

    public static function register(): void
    {
        // morphMap وليس enforceMorphMap: activity_logs وغيرها تحفظ أسماء كلاسات لا تمر بالخريطة.
        Relation::morphMap(self::map());
    }
}
