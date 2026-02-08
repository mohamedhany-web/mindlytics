<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من أن المستخدم غير مسجل دخول
 * يستخدم لصفحات التسجيل والدخول فقط
 */
class EnsureGuestOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // إذا كان المستخدم مسجل دخول، يتم توجيهه للـ dashboard المناسب
            $user = Auth::user();
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('info', 'أنت مسجل دخول بالفعل');
            } elseif ($user->isInstructor()) {
                return redirect()->route('instructor.courses.index')->with('info', 'أنت مسجل دخول بالفعل');
            } else {
                return redirect()->route('dashboard')->with('info', 'أنت مسجل دخول بالفعل');
            }
        }

        return $next($request);
    }
}
