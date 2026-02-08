<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security Headers - يجب أن يكون أول middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // عدم تسجيل استثناء "غير مصادق" كخطأ (سلوك متوقع عند زيارة صفحة محمية دون تسجيل الدخول)
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
        ]);

        // معالجة "غير مصادق": إعادة توجيه لصفحة تسجيل الدخول بدلاً من 500
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'يجب تسجيل الدخول'], 401);
            }
            return redirect()->guest($e->redirectTo($request) ?? route('login'));
        });

        // توجيه الأخطاء إلى صفحاتنا المخصصة
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد قليل.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }
            
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
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
        
        // معالجة الأخطاء العامة
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // تسجيل الخطأ قبل عرض صفحة الخطأ
            \Illuminate\Support\Facades\Log::error('Unhandled exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug') ? $e->getMessage() : 'حدث خطأ في الخادم',
                    'file' => config('app.debug') ? $e->getFile() : null,
                    'line' => config('app.debug') ? $e->getLine() : null,
                ], 500);
            }
            
            if (view()->exists('errors.500')) {
                return response()->view('errors.500', [
                    'message' => config('app.debug') ? $e->getMessage() : 'حدث خطأ في الخادم',
                    'file' => config('app.debug') ? $e->getFile() : null,
                    'line' => config('app.debug') ? $e->getLine() : null,
                ], 500);
            }
        });
    })->create();
