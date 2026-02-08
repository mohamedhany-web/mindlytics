<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من أن المستخدم نشط وغير محظور
 */
class CheckActiveStatus
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

        // التحقق من أن المستخدم غير محذوف
        if (!$user) {
            Auth::logout();
            return redirect('/login')->with('error', 'حسابك غير موجود');
        }

        // التحقق من حالة الحساب (إذا كان هناك حقل status)
        if (isset($user->status) && $user->status !== 'active') {
            Auth::logout();
            
            $message = match($user->status) {
                'suspended' => 'حسابك معطل مؤقتاً. يرجى الاتصال بالإدارة',
                'banned' => 'حسابك محظور. يرجى الاتصال بالإدارة',
                'inactive' => 'حسابك غير نشط. يرجى الاتصال بالإدارة',
                default => 'حسابك غير نشط. يرجى الاتصال بالإدارة'
            };
            
            return redirect('/login')->with('error', $message);
        }

        // التحقق من البريد الإلكتروني (إذا كان مطلوب التحقق)
        if (config('auth.verify_email', false) && !$user->email_verified_at) {
            if (!$request->routeIs('verification.notice') && !$request->routeIs('verification.verify')) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'يرجى التحقق من بريدك الإلكتروني');
            }
        }

        return $next($request);
    }
}
