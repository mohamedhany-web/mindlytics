<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observers للنماذج - مع تحسينات الأداء
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\StudentCourseEnrollment::observe(\App\Observers\EnrollmentObserver::class);
        \App\Models\Exam::observe(\App\Observers\ExamObserver::class);
        \App\Models\AdvancedCourse::observe(\App\Observers\AdvancedCourseObserver::class);
        \App\Models\ExamAttempt::observe(\App\Observers\ExamAttemptObserver::class);
        
        // Observers للتقويم والإشعارات
        \App\Models\Lecture::observe(\App\Observers\LectureObserver::class);
        \App\Models\Assignment::observe(\App\Observers\AssignmentObserver::class);
        \App\Models\LectureAssignment::observe(\App\Observers\LectureAssignmentObserver::class);

        // تفعيل Event Listeners لتسجيل النشاطات
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogLoginActivity::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Listeners\LogLogoutActivity::class
        );

        // Security Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            [\App\Listeners\SecurityEventListener::class, 'handleFailedLogin']
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            [\App\Listeners\SecurityEventListener::class, 'handleSuccessfulLogin']
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            [\App\Listeners\SecurityEventListener::class, 'handleLogout']
        );

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ? true : null;
            }
        });
    }
}
