<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityService;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * تنظيف وتأمين جميع المدخلات
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من SQL Injection
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                // تخطي CSRF token
                if ($key === '_token') {
                    continue;
                }

                // التحقق من SQL Injection
                if ($this->securityService->detectSQLInjection($value)) {
                    $this->securityService->logSuspiciousActivity('SQL Injection Attempt', $request, "Field: {$key}");
                    abort(403, 'طلب غير صالح');
                }

                // التحقق من XSS
                if ($this->securityService->detectXSS($value)) {
                    $this->securityService->logSuspiciousActivity('XSS Attempt', $request, "Field: {$key}");
                    abort(403, 'طلب غير صالح');
                }
            }
        }

        // تنظيف المدخلات النصية
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value) && $key !== '_token' && $key !== 'password' && $key !== 'password_confirmation') {
                $input[$key] = $this->securityService->sanitizeInput($value);
            }
        }
        $request->merge($input);

        return $next($request);
    }
}
