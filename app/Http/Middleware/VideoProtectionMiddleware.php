<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VideoProtectionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // إضافة headers لحماية إضافية
        $response = $next($request);
        
        // منع التخزين المؤقت
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        // منع التضمين في مواقع أخرى
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        
        // حماية إضافية
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        
        // منع حفظ الصفحة
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        
        return $response;
    }
}
