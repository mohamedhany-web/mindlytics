<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\PlatformSettings;
use App\Support\SiteBranding;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        $this->app->scoped(\App\Support\BranchContext::class, fn () => new \App\Support\BranchContext(null));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تحميل دوال المساعدة (تُحمّل من هنا لضمان توفرها حتى قبل composer dump-autoload)
        $filesystemHelper = app_path('Helpers/FilesystemHelper.php');
        if (file_exists($filesystemHelper)) {
            require_once $filesystemHelper;
        }

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
        $brandingViews = [
            'layouts.admin',
            'layouts.admin-sidebar',
            'layouts.public',
            'layouts.student-dashboard',
            'layouts.employee',
            'layouts.place-manager',
            'layouts.admin-community',
            'layouts.app',
            'layouts.instructor-sidebar',
            'layouts.student-sidebar',
            // صفحات عامة تستخدم unified-navbar بدون layouts.public
            'courses',
            'course-show',
            'public.learning-paths',
            'public.learning-path-show',
            'auth.login',
            'auth.register',
            'auth.forgot-password',
            'auth.reset-password',
            'auth.two-factor.setup',
            'auth.two-factor.challenge',
            'welcome',
            'community.layouts.guest',
        ];
        View::composer($brandingViews, function ($view) {
            $view->with('platformLogoUrl', SiteBranding::logoUrl());
            $view->with('platformFaviconUrl', SiteBranding::faviconUrl());
        });

        View::composer([
            'admin.hr.jobs.*',
            'admin.hr.applications.*',
            'admin.hr.rubrics.*',
        ], function ($view) {
            $view->with([
                'hrInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500',
                'hrSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500',
                'hrTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 resize-y min-h-[100px]',
                'hrLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'hrBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white text-sm font-semibold shadow-lg shadow-pink-500/20 transition-all',
                'hrBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'hrBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all',
                'hrSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
            ]);
        });

        View::composer(['admin.scholarships.*'], function ($view) {
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

        View::composer(['admin.investment.*'], function ($view) {
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

        View::composer(['admin.whatsapp.*'], function ($view) {
            $view->with([
                'waInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500',
                'waSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500',
                'waTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-y min-h-[120px]',
                'waLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'waBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-green-500 hover:from-emerald-700 hover:to-green-600 text-white text-sm font-semibold shadow-lg shadow-emerald-500/20 transition-all',
                'waBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'waBtnDark' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all',
                'waSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
            ]);
        });

        View::composer(['admin.meta-social.*'], function ($view) {
            $view->with([
                'smInputClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500',
                'smSelectClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500',
                'smTextareaClass' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 resize-y min-h-[120px]',
                'smLabelClass' => 'block text-xs font-semibold text-slate-700 mb-1.5',
                'smBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-500 hover:from-sky-700 hover:to-blue-600 text-white text-sm font-semibold shadow-lg shadow-sky-500/20 transition-all',
                'smBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-all',
                'smBtnMeta' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0866FF] hover:bg-[#0654d4] text-white text-sm font-semibold shadow-lg shadow-[#0866FF]/20 transition-all',
                'smSectionClass' => 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden',
            ]);
        });

        View::composer(['components.unified-footer', 'public.contact'], function ($view) {
            $view->with('platformContact', PlatformSettings::contactPage());
        });

        View::composer(['layouts.employee', 'employee.attendance.locked'], function ($view) {
            try {
                $user = auth()->user();
                if ($user?->isSubjectToWorkSchedule()) {
                    $view->with('employeeAttendance', app(\App\Services\EmployeeAttendanceService::class)->getState($user));
                }
            } catch (\Throwable $e) {
                report($e);
                $view->with('employeeAttendance', [
                    'mode' => 'exempt',
                    'can_access' => true,
                    'message' => '',
                ]);
            }
        });

        // إجبار روابط الموقع على HTTPS: الإنتاج، أو عندما يكون APP_URL أصلاً https (يشمل استضافة خلف بروكسي)
        $appUrl = (string) config('app.url', '');
        if ($appUrl !== '' && ($this->app->environment('production') || str_starts_with($appUrl, 'https://'))) {
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
        \App\Models\EmployeeSalaryDeduction::observe(\App\Observers\EmployeeSalaryDeductionObserver::class);

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

        \Illuminate\Support\Facades\Log::listen(function (\Illuminate\Log\Events\MessageLogged $event) {
            if (! in_array($event->level, ['error', 'critical', 'alert', 'emergency', 'warning'], true)) {
                return;
            }

            try {
                app(\App\Services\PlatformErrorLogger::class)->recordLog(
                    $event->level,
                    (string) $event->message,
                    is_array($event->context) ? $event->context : []
                );
            } catch (\Throwable) {
                // تجاهل
            }
        });

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ? true : null;
            }
        });

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // تسجيل حساب جديد — 15 محاولة كل 15 دقيقة لكل IP (يسمح بتصحيح الأخطاء)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(15, 15)->by($request->ip());
        });

        // تسجيل الدخول
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(20, 15)->by($request->ip());
        });

        // استعادة كلمة المرور
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinutes(8, 15)->by($request->ip());
        });
    }
}
