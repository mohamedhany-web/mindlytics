<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /** مسار صورة خلفية صفحات تسجيل الدخول/إنشاء الحساب في التخزين (نفس أسلوب مسارات التعلم) */
    public const AUTH_BACKGROUND_STORAGE_PATH = 'auth-pages/brainstorm-meeting.jpg';

    /** مسار لوجو المنصة في التخزين (يُعرض من /storage/ مثل الكورسات والصور) */
    public const SITE_LOGO_STORAGE_PATH = 'site/logo.png';
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
        // ضمان وجود صورة الخلفية في التخزين (نفس مسار صور المسارات) لتعمل على السيرفر عبر /storage/
        $authStoragePath = self::AUTH_BACKGROUND_STORAGE_PATH;
        $disk = Storage::disk('public');
        if (!$disk->exists($authStoragePath)) {
            $sources = ['images/brainstorm-meeting.jpg', 'images/brainstorm-meeting.png'];
            foreach ($sources as $source) {
                $publicPath = public_path($source);
                if (File::isFile($publicPath)) {
                    $dir = dirname($authStoragePath);
                    if (!$disk->exists($dir)) {
                        $disk->makeDirectory($dir);
                    }
                    $disk->put($authStoragePath, File::get($publicPath));
                    break;
                }
            }
        }

        // صورة خلفية صفحات تسجيل الدخول وإنشاء الحساب: دائماً من التخزين (نفس عرض صور المسارات)
        View::composer(['auth.login', 'auth.register'], function ($view) {
            $path = self::AUTH_BACKGROUND_STORAGE_PATH;
            if (Storage::disk('public')->exists($path)) {
                $view->with('authBackgroundUrl', asset('storage/' . $path));
            } else {
                $view->with('authBackgroundUrl', asset('images/brainstorm-meeting.jpg'));
            }
        });

        // لوجو المنصة: نسخ إلى التخزين إن لم يكن موجوداً (نفس أسلوب صورة تسجيل الدخول)
        $logoPath = self::SITE_LOGO_STORAGE_PATH;
        if (!$disk->exists($logoPath)) {
            $logoSource = public_path('logo-removebg-preview.png');
            if (File::isFile($logoSource)) {
                $dir = dirname($logoPath);
                if (!$disk->exists($dir)) {
                    $disk->makeDirectory($dir);
                }
                $disk->put($logoPath, File::get($logoSource));
            }
        }
        // حساب رابط اللوجو عند عرض الصفحة (مثل authBackgroundUrl) لضمان ظهور الصورة مع الطلب الحالي
        View::composer(['layouts.instructor-sidebar', 'layouts.student-sidebar', 'layouts.app', 'layouts.admin'], function ($view) use ($disk, $logoPath) {
            $url = $disk->exists($logoPath)
                ? asset('storage/' . $logoPath)
                : asset('logo-removebg-preview.png');
            $view->with('platformLogoUrl', $url);
        });

        // إجبار روابط الموقع على HTTPS في الإنتاج (حل مشكلة عدم ظهور الصور عند Mixed Content)
        if ($this->app->environment('production') && config('app.url')) {
            URL::forceScheme('https');
            $publicUrl = config('filesystems.disks.public.url');
            if ($publicUrl && str_starts_with($publicUrl, 'http://')) {
                config(['filesystems.disks.public.url' => 'https://' . substr($publicUrl, 7)]);
            }
        }

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
