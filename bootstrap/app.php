<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security Headers - يجب أن يكون أول middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        // بعد SecurityHeaders؛ قبل بقية مجموعة web ليكون الفرع متاحاً في كل الطلبات العامة.
        $middleware->prependToGroup('web', \App\Http\Middleware\ResolveBranchFromHost::class);

        // تحديد لغة الموقع من ?lang= أو الجلسة (لجميع الصفحات)
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        
        // Input Sanitization - تنظيف المدخلات
        $middleware->appendToGroup('web', \App\Http\Middleware\InputSanitizationMiddleware::class);
        
        // File Upload Security - حماية رفع الملفات
        $middleware->appendToGroup('web', \App\Http\Middleware\FileUploadSecurityMiddleware::class);
        
        // إضافة Middleware مراقبة الأنشطة لجميع الطلبات
        $middleware->append(\App\Http\Middleware\LogActivityMiddleware::class);
        
        // إضافة Middleware للتحقق من حالة المستخدم لجميع الطلبات المصادقة عليها
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckActiveStatus::class);

        // إلزام الإدمن والمدربين والموظفين بتفعيل المصادقة الثنائية (2FA) قبل الوصول لأي صفحة
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureTwoFactorEnabled::class);
        
        // تسجيل Middlewares للأدوار والصلاحيات
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
            'ownership' => \App\Http\Middleware\EnsureOwnership::class,
            'guest-only' => \App\Http\Middleware\EnsureGuestOnly::class,
            'prevent-concurrent' => \App\Http\Middleware\PreventConcurrentSessions::class,
            'landing.locale' => \App\Http\Middleware\SetLandingLocale::class,
            'community.contributor' => \App\Http\Middleware\EnsureCommunityContributor::class,
            'sales.employee' => \App\Http\Middleware\EnsureSalesEmployee::class,
            'moderator.employee' => \App\Http\Middleware\EnsureModeratorEmployee::class,
            'api.student' => \App\Http\Middleware\EnsureApiStudent::class,
            'api.instructor' => \App\Http\Middleware\EnsureApiInstructor::class,
            'branch.office' => \App\Http\Middleware\BranchOfficePanel::class,
            'place.office' => \App\Http\Middleware\PlaceOfficePanel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // عدم تسجيل استثناء "غير مصادق" كخطأ (سلوك متوقع عند زيارة صفحة محمية دون تسجيل الدخول)
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        // معالجة ValidationException أولاً (قبل HttpException لأنها ترث منه): إعادة توجيه مع أخطاء الحقول — لا 500
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withInput()->withErrors($e->errors());
        });

        // معالجة "غير مصادق": إعادة توجيه لصفحة تسجيل الدخول بدلاً من 500
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'يجب تسجيل الدخول'], 401);
            }
            // إذا كان الطلب من مسارات المجتمع → تسجيل دخول المجتمع، وإلا → الأكاديمية
            $loginRoute = $request->is('community') || $request->is('community/*')
                ? route('community.login')
                : ($e->redirectTo($request) ?? route('login'));
            return redirect()->guest($loginRoute);
        });

        // توجيه الأخطاء إلى صفحاتنا المخصصة
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد قليل.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }

            $retryAfter = (int) ($e->getHeaders()['Retry-After'] ?? 60);

            // تسجيل جديد: ارجع للنموذج برسالة واضحة بدل صفحة 429
            if ($request->is('register') && $request->isMethod('POST')) {
                return redirect()->route('register')
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors([
                        'email' => 'محاولات تسجيل كثيرة. انتظر '.max(1, (int) ceil($retryAfter / 60)).' دقيقة ثم حاول مرة أخرى.',
                    ]);
            }

            return response()->view('errors.429', ['retry_after' => $retryAfter], 429)
                ->withHeaders(['Retry-After' => $retryAfter]);
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'الصفحة غير موجودة'], 404);
            }
            return response()->view('errors.404', [], 404);
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح بالوصول'], 403);
            }
            return response()->view('errors.403', [], 403);
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            $statusCode = $e->getStatusCode();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage() ?: 'حدث خطأ'], $statusCode);
            }

            // في وضع Debug: عرض Whoops للـ 500 بدل الصفحة المخصصة
            if ($statusCode === 500 && config('app.debug')) {
                return null;
            }
            
            if ($statusCode === 503 && view()->exists('errors.503')) {
                return response()->view('errors.503', [], 503);
            }
            
            if ($statusCode === 500 && view()->exists('errors.500')) {
                return response()->view('errors.500', [], 500);
            }
            
            if ($statusCode === 403 && view()->exists('errors.403')) {
                return response()->view('errors.403', [], 403);
            }
            
            if ($statusCode === 404 && view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
        });
        
        // تسجيل الأخطاء في لوحة الإدارة (قاعدة البيانات)
        $exceptions->reportable(function (\Throwable $e) {
            try {
                app(\App\Services\PlatformErrorLogger::class)->recordException($e, request());
            } catch (\Throwable) {
                // لا نكسر الطلب الأصلي
            }
        });

        // معالجة الأخطاء العامة
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
                }
                return redirect()->back()->withInput()->withErrors($e->errors());
            }
            // طلبات اتفاقيات الموظفين (إنشاء/تحديث): إعادة توجيه لصفحة النموذج مع رسالة الخطأ بدلاً من 500
            if (!$request->expectsJson() && $request->isMethod('POST') && $request->is('*employee-agreements*')) {
                \Illuminate\Support\Facades\Log::error('Employee agreement request failed', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $msg = mb_substr($e->getMessage(), 0, 400);
                return redirect()->to(url('/admin/employee-agreements/create'))
                    ->with('error', 'حدث خطأ: ' . $msg);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug') ? $e->getMessage() : 'حدث خطأ في الخادم',
                    'file' => config('app.debug') ? $e->getFile() : null,
                    'line' => config('app.debug') ? $e->getLine() : null,
                ], 500);
            }

            // في وضع Debug: عرض صفحة Whoops التفصيلية بدل الصفحة المخصصة
            if (config('app.debug')) {
                return null;
            }
            
            if (view()->exists('errors.500')) {
                return response()->view('errors.500', [
                    'message' => 'حدث خطأ في الخادم',
                ], 500);
            }
        });
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        $schedule->command('sales:send-followup-reminders')->dailyAt('08:00');
        $schedule->command('sales:remind-daily-report')->everyFifteenMinutes();
        $schedule->command('sales:evaluate-enforcement')->dailyAt('09:30');
        $schedule->command('sales:apply-daily-report-penalties')->dailyAt('01:30');
        $schedule->command('sales:enforce-import-contact')->dailyAt('10:00');
        $schedule->command('employees:remind-daily-report')->dailyAt('16:30');
        $schedule->command('employees:apply-daily-report-penalties')->dailyAt('02:00');
        $schedule->command('marketing:remind-today-events')
            ->dailyAt(\App\Support\MarketingPlanSettings::reminderTime());
        $schedule->command('marketing:apply-execution-penalties')
            ->dailyAt(\App\Support\MarketingPlanSettings::confirmationDeadlineTime());

        // طابور الواتساب فقط — معزول عن sales والتسجيل وغيرها
        $schedule->command('queue:work --queue=whatsapp --stop-when-empty --max-time=55 --max-jobs=50')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->create();
