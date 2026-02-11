<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class GoogleAuthController extends Controller
{
    /**
     * توجيه المستخدم إلى صفحة تسجيل الدخول بـ Google
     */
    public function redirect()
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'تسجيل الدخول بـ Google غير مفعّل. يرجى استخدام البريد وكلمة المرور، أو تواصل مع الإدارة.']);
        }
        return app(SocialiteFactory::class)->driver('google')->redirect();
    }

    /**
     * معالجة callback من Google: إنشاء أو تسجيل دخول حساب طالب
     */
    public function callback()
    {
        try {
            $googleUser = app(SocialiteFactory::class)->driver('google')->user();
        } catch (\Exception $e) {
            \Log::warning('Google OAuth error', ['message' => $e->getMessage()]);
            return redirect()->route('login')
                ->withErrors(['email' => 'فشل تسجيل الدخول عبر Google. حاول مرة أخرى أو سجّل الدخول بالبريد وكلمة المرور.']);
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: explode('@', $email)[0]);

        if (empty($email)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'لم نتمكن من الحصول على بريدك من Google. يرجى السماح بمشاركة البريد أو التسجيل يدوياً.']);
        }

        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
        }

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(64)),
                'google_id' => $googleId,
                'role' => 'student',
                'is_active' => true,
            ]);
        } else {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleId,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        }

        if (!$user->is_active) {
            return redirect()->route('login')
                ->withErrors(['email' => 'حسابك غير نشط. يرجى التواصل مع الإدارة.']);
        }

        Auth::login($user, true);
        request()->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        if ($user->isEmployee()) {
            return redirect()->intended(route('employee.dashboard'));
        }
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isInstructor()) {
            return redirect()->intended(route('instructor.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
