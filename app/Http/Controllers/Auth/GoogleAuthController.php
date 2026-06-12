<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BranchContext;
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
                ->withErrors(['email' => __('auth.login_register_google_only')]);
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
                ->withErrors(['email' => __('auth.login_register_google_only')]);
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: explode('@', $email)[0]);

        if (empty($email)) {
            return redirect()->route('login')
                ->withErrors(['email' => __('auth.login_register_google_only')]);
        }

        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
        }

        if (!$user) {
            $branchId = app(BranchContext::class)->id();

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(64)),
                'google_id' => $googleId,
                'role' => 'student',
                'is_active' => true,
                'branch_id' => $branchId,
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

        $intended = session('url.intended');

        if ($user->isEmployee()) {
            return redirect()->intended(route('employee.dashboard'));
        }
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isInstructor()) {
            return redirect()->intended(route('instructor.dashboard'));
        }

        // إذا كان القصد من مجتمع الذكاء الاصطناعي والمساهم → لوحة المساهم
        if ($intended && str_contains($intended, 'community') && $user->is_community_contributor) {
            session()->forget('url.intended');
            return redirect()->route('community.contributor.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }
}
