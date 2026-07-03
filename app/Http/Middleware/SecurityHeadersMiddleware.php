<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     * إضافة Security Headers لحماية التطبيق
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers الأساسية (دون CSP مؤقتاً)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // microphone/camera=(self) مطلوب لصندوق واتساب والامتحانات — () كانت تمنع getUserMedia حتى مع موافقة المتصفح
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(self), camera=(self)');
        
        // Content Security Policy - محسّن للواجهة الأمامية
        // تعطيل CSP مؤقتاً في بيئة التطوير لتجنب مشاكل الواجهة
        if (!config('app.debug') || !env('DISABLE_CSP', true)) {
            // Fawaterk (فواتيرك): قد تعمل على app.* أو staging.* حسب بيئة FAWATERAK_ENV
            // نسمح بالاثنين لتفادي تعطل الدفع عند تبديل البيئة.
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "https://app.fawaterk.com " .
                   "https://staging.fawaterk.com " .
                   "https://cdn.tailwindcss.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdnjs.cloudflare.com " .
                   "https://unpkg.com " .
                   "https://fonts.googleapis.com; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "https://fonts.googleapis.com " .
                   "https://cdnjs.cloudflare.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdn.tailwindcss.com; " .
                   "font-src 'self' data: " .
                   "https://fonts.gstatic.com " .
                   "https://cdnjs.cloudflare.com " .
                   "https://cdn.jsdelivr.net; " .
                   "img-src 'self' data: https: blob:; " .
                   "connect-src 'self' https: ws: wss: " .
                   "https://app.fawaterk.com " .
                   "https://staging.fawaterk.com; " .
                   "frame-src 'self' " .
                   "https://app.fawaterk.com " .
                   "https://staging.fawaterk.com " .
                   "https://www.youtube.com " .
                   "https://player.vimeo.com " .
                   "https://www.google.com; " .
                   "object-src 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self'; " .
                   "worker-src 'self' blob:; " .
                   "manifest-src 'self';";
            
            $response->headers->set('Content-Security-Policy', $csp);
        }
        
        // Strict Transport Security (HTTPS only)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
