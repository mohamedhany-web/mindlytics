<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware لمنع تسجيل الدخول المتزامن من أجهزة/جلسات متعددة
 * يحافظ على جلسة واحدة نشطة فقط لكل مستخدم
 */
class PreventConcurrentSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $sessionId = Session::getId();
        
        // المفتاح في الكاش لتخزين معرف الجلسة النشطة
        $cacheKey = "user_session_{$user->id}";
        
        // الحصول على معرف الجلسة المخزن
        $storedSessionId = Cache::get($cacheKey);
        
        // إذا لم تكن هناك جلسة مخزنة أو كانت هذه هي الجلسة النشطة
        if (!$storedSessionId || $storedSessionId === $sessionId) {
            // حفظ معرف الجلسة الحالية كجلسة نشطة (مدة أطول لتجنب المشاكل)
            Cache::put($cacheKey, $sessionId, now()->addDays(7));
            return $next($request);
        }
        
        // إذا كانت هناك جلسة نشطة أخرى، تسجيل الخروج من الجلسة الحالية
        \Log::info('جلسة متزامنة مكتشفة للمستخدم: ' . $user->id . ' - الجلسة الحالية: ' . $sessionId . ' - الجلسة المخزنة: ' . $storedSessionId);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('error', 'تم تسجيل الدخول من جهاز آخر. تم تسجيل الخروج تلقائياً.');
    }
}
