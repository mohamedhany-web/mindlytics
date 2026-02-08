<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * جعل المصادقة الثنائية (2FA) إلزامية للإدمن ومدير العام فقط.
 * من لم يفعّل 2FA بعد يُوجّه لصفحة الإعداد ولا يستطيع الوصول لأي صفحة أخرى.
 */
class EnsureTwoFactorEnabled
{
    /** المسارات المسموح بها قبل تفعيل 2FA */
    protected array $except = [
        'two-factor.setup',
        'two-factor.enable',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // إذا كان إلزام 2FA للأدمن معطّلاً من الإعدادات (للمرونة عند فقدان الرمز أو مشكلة)
        if (!$user->requiresTwoFactor() || !config('app.admin_2fa_required', true)) {
            return $next($request);
        }

        // من لديه 2FA مفعّلة يمرّ بشكل طبيعي
        if ($user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // مسموح له فقط بزيارة إعداد 2FA أو التفعيل أو تسجيل الخروج
        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        return redirect()->route('two-factor.setup')
            ->with('warning', 'المصادقة الثنائية إلزامية للدخول إلى النظام. يرجى تفعيلها الآن.');
    }
}
