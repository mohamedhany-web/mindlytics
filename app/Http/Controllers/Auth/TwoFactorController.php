<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * عرض صفحة إدخال رمز المصادقة الثنائية (بعد كلمة المرور عند الدخول)
     */
    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }
        $userId = $request->session()->get('login.id');
        $user = User::find($userId);
        if (!$user || !$user->requiresTwoFactor() || !$user->hasTwoFactorEnabled()) {
            $request->session()->forget(['login.id', 'login.remember']);
            return redirect()->route('login');
        }
        return view('auth.two-factor.challenge');
    }

    /**
     * التحقق من رمز المصادقة الثنائية وإكمال تسجيل الدخول
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'رمز التحقق مطلوب',
            'code.size' => 'رمز التحقق يتكون من 6 أرقام',
        ]);

        if (!$request->session()->has('login.id')) {
            return redirect()->route('login')->withErrors(['code' => 'انتهت الجلسة. يرجى تسجيل الدخول مرة أخرى.']);
        }

        $user = User::find($request->session()->get('login.id'));
        if (!$user || !$user->hasTwoFactorEnabled()) {
            $request->session()->forget(['login.id', 'login.remember']);
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $secret = $user->two_factor_secret ? decrypt($user->two_factor_secret) : '';
        $valid = $secret && $google2fa->verifyKey($secret, $request->code, 2);

        if (!$valid) {
            // التحقق من رموز الاسترداد (مشفرة في DB إن استخدمت)
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $codeToCheck = str_replace(' ', '', $request->code);
            if (in_array($codeToCheck, $recoveryCodes)) {
                $recoveryCodes = array_values(array_filter($recoveryCodes, fn($c) => $c !== $codeToCheck));
                $user->forceFill(['two_factor_recovery_codes' => $recoveryCodes])->save();
                $valid = true;
            }
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        $remember = $request->session()->get('login.remember', false);
        $request->session()->forget(['login.id', 'login.remember']);
        $request->session()->regenerate();

        Auth::login($user, $remember);
        $user->update(['last_login_at' => now()]);

        if ($user->isEmployee()) {
            return redirect()->intended(route('employee.dashboard'));
        }
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isInstructor()) {
            return redirect()->intended(route('instructor.courses.index'));
        }
        return redirect()->intended(route('dashboard'));
    }

    /**
     * عرض صفحة إعداد المصادقة الثنائية (للمدربين/الإدمن/الموظفين)
     */
    public function showSetup(Request $request)
    {
        $user = $request->user();
        if (!$user->requiresTwoFactor()) {
            abort(403, 'المصادقة الثنائية متاحة للإدمن فقط.');
        }
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route($this->getDashboardRoute($user))
                ->with('info', 'المصادقة الثنائية مفعّلة مسبقاً.');
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey(32);
        $request->session()->put('two_factor.setup_secret', $secret);

        $appName = config('app.name', 'Mindlytics');
        $qrCodeUrl = $google2fa->getQRCodeUrl($appName, $user->email ?? $user->id . '@mindlytics.local', $secret);

        return view('auth.two-factor.setup', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * تأكيد تفعيل المصادقة الثنائية بعد إدخال الرمز
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'رمز التحقق مطلوب',
            'code.size' => 'رمز التحقق يتكون من 6 أرقام',
        ]);

        $user = $request->user();
        if (!$user->requiresTwoFactor()) {
            abort(403);
        }
        $secret = $request->session()->get('two_factor.setup_secret');
        if (!$secret) {
            return redirect()->route('two-factor.setup')->withErrors(['code' => 'انتهت الجلسة. يرجى البدء من جديد.']);
        }

        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($secret, $request->code, 2)) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        $recoveryCodes = collect(range(1, 8))->map(fn() => Str::random(10))->values()->all();
        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => array_map(fn($c) => $c, $recoveryCodes),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('two_factor.setup_secret');

        $dashboardRoute = $this->getDashboardRoute($user);
        return redirect()->route($dashboardRoute)
            ->with('success', 'تم تفعيل المصادقة الثنائية بنجاح.')
            ->with('recovery_codes', $recoveryCodes);
    }

    /**
     * تعطيل المصادقة الثنائية (يتطلب كلمة المرور)
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], ['password.required' => 'كلمة المرور مطلوبة للتأكيد.']);

        $user = $request->user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'كلمة المرور غير صحيحة.']);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $dashboardRoute = $this->getDashboardRoute($user);
        return redirect()->route($dashboardRoute)->with('success', 'تم تعطيل المصادقة الثنائية.');
    }

    protected function getDashboardRoute(User $user): string
    {
        if ($user->isEmployee()) {
            return 'employee.dashboard';
        }
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return 'admin.dashboard';
        }
        if ($user->isInstructor()) {
            return 'instructor.courses.index';
        }
        return 'dashboard';
    }
}
