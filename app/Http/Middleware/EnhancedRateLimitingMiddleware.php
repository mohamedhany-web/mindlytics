<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\SecurityService;
use Symfony\Component\HttpFoundation\Response;

class EnhancedRateLimitingMiddleware
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * Rate Limiting محسن مع تسجيل المحاولات المشبوهة
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        // التحقق من IP المشبوه
        if ($this->securityService->isSuspiciousIP($request->ip())) {
            $this->securityService->logSuspiciousActivity('Suspicious IP Access', $request);
            abort(403, 'الوصول محظور');
        }

        // Rate Limiting
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            // تسجيل المحاولة المشبوهة
            $this->securityService->logSuspiciousActivity(
                'Rate Limit Exceeded',
                $request,
                "Max attempts: {$maxAttempts}, Retry after: {$seconds} seconds"
            );

            return response()->json([
                'message' => 'تم تجاوز عدد الطلبات المسموح. يرجى المحاولة لاحقاً.',
                'retry_after' => $seconds
            ], 429)->withHeaders([
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($key)));

        return $response;
    }

    /**
     * إنشاء مفتاح فريد للطلب
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return 'rate_limit:' . $user->id . ':' . $request->ip();
        }

        return 'rate_limit:' . $request->ip() . ':' . $request->path();
    }
}
