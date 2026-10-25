<?php

namespace App\Domains\Learning;

use App\Domains\Learning\Console\Commands\FixCertificateCourseForeignKeyCommand;
use App\Domains\Learning\Console\Commands\RestoreCourseProgressFromResyncLogCommand;
use App\Domains\Learning\Console\Commands\ResyncCourseProgressCommand;
use App\Domains\Learning\Console\Commands\VerifyCourseDeleteCommand;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Learning\Models\Assignment;
use App\Domains\Learning\Models\Exam;
use App\Domains\Learning\Models\ExamAttempt;
use App\Domains\Learning\Models\Lecture;
use App\Domains\Learning\Models\LectureAssignment;
use App\Domains\Learning\Models\StudentCourseEnrollment;
use App\Domains\Learning\Observers\AdvancedCourseObserver;
use App\Domains\Learning\Observers\AssignmentObserver;
use App\Domains\Learning\Observers\EnrollmentObserver;
use App\Domains\Learning\Observers\ExamAttemptObserver;
use App\Domains\Learning\Observers\ExamObserver;
use App\Domains\Learning\Observers\LectureAssignmentObserver;
use App\Domains\Learning\Observers\LectureObserver;
use Illuminate\Support\ServiceProvider;

/**
 * مجال التعلّم: الكورسات الأونلاين، المنهج، المحاضرات، الواجبات، الاختبارات، بنوك الأسئلة، المسارات، الشهادات، الفيديو
 * (docs/MODULARIZATION_PLAN.md).
 *
 * الـ routes لا تُحمَّل من هنا: ملفات routes/ داخل المجال تُستدعى بـ require في نفس مواضعها القديمة.
 * ⚠️ curriculum_items.item_type يحفظ أسماء الكلاسات القديمة (App\Models\Lecture …) ويرسلها المنهج من الـ JS:
 * الكتابة والمقارنة والتحقق عبر Relation::getMorphAlias / getMorphedModel و App\Support\MorphMap.
 */
class LearningServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('learning::admin.advanced-courses.index') و view('learning::student.my-courses.learn') و view('learning::instructor.curriculum.index')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'learning');

        // كانت في AppServiceProvider (نفس الترتيب)
        StudentCourseEnrollment::observe(EnrollmentObserver::class);
        Exam::observe(ExamObserver::class);
        AdvancedCourse::observe(AdvancedCourseObserver::class);
        ExamAttempt::observe(ExamAttemptObserver::class);
        Lecture::observe(LectureObserver::class);
        Assignment::observe(AssignmentObserver::class);
        LectureAssignment::observe(LectureAssignmentObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                FixCertificateCourseForeignKeyCommand::class,
                ResyncCourseProgressCommand::class,
                RestoreCourseProgressFromResyncLogCommand::class,
                VerifyCourseDeleteCommand::class,
            ]);
        }
    }
}
