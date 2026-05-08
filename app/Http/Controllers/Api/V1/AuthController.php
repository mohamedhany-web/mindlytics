<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiUserResource;
use App\Jobs\ProcessStudentRegistration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * تسجيل دخول الموبايل — يطابق التحقق من الويب؛ حسابات تتطلب 2FA تُرشد للموقع.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        $key = 'login_attempts_'.$request->ip();
        $maxAttempts = 10;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($key, $decayMinutes * 60);

            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 422);
        }

        if (! $user->is_active) {
            RateLimiter::hit($key, $decayMinutes * 60);

            return response()->json([
                'message' => 'حسابك غير نشط. يرجى التواصل مع الإدارة.',
            ], 403);
        }

        if (! ($user->isStudent() || $user->isInstructor())) {
            return response()->json([
                'message' => 'غير مسموح بتسجيل الدخول لهذا النوع من الحسابات عبر التطبيق.',
                'code' => 'role_not_allowed',
            ], 403);
        }

        RateLimiter::clear($key);

        if (config('app.admin_2fa_required', true) && $user->requiresTwoFactor()) {
            return response()->json([
                'message' => 'يجب إكمال التحقق الثنائي من خلال الموقع الإلكتروني.',
                'code' => 'two_factor_required',
            ], 422);
        }

        $expiresAt = $request->boolean('remember')
            ? now()->addDays(30)
            : now()->addDays(7);

        $plainToken = $user->createToken('mobile', ['*'], $expiresAt)->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => new ApiUserResource($user),
        ]);
    }

    /**
     * إنشاء حساب طالب — نفس قواعد التسجيل في الويب.
     */
    public function register(Request $request): JsonResponse
    {
        $countries = config('phone_countries.countries', []);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => 'nullable|string|max:32',
        ], [
            'name.required' => 'الاسم مطلوب',
            'country_code.required' => 'كود الدولة مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        $country = collect($countries)->firstWhere('dial_code', $request->country_code);
        if (! $country || ! isset($country['validation']['regex'])) {
            return response()->json([
                'message' => 'كود الدولة غير مدعوم.',
                'errors' => ['country_code' => ['كود الدولة غير مدعوم.']],
            ], 422);
        }

        $nationalNumber = preg_replace('/\D/', '', (string) $request->phone);
        $nationalNumber = ltrim($nationalNumber, '0');
        if (! preg_match($country['validation']['regex'], $nationalNumber)) {
            $example = $country['example'] ?? $country['placeholder'] ?? '';

            return response()->json([
                'message' => 'رقم الهاتف غير صحيح لهذه الدولة.',
                'errors' => ['phone' => ["مثال: {$example}"]],
            ], 422);
        }

        $dial = $country['dial_code'] ?? '';
        $fullPhone = ($dial === '' || $dial === 'OTHER')
            ? ('OTHER_'.$nationalNumber)
            : ($dial.$nationalNumber);

        if (User::where('phone', $fullPhone)->exists()) {
            return response()->json([
                'message' => 'رقم الهاتف مسجل مسبقاً',
                'errors' => ['phone' => ['رقم الهاتف مسجل مسبقاً']],
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $fullPhone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => true,
        ]);

        $referral = $request->filled('referral_code')
            ? strtoupper(trim((string) $request->referral_code))
            : null;

        ProcessStudentRegistration::dispatch($user->id, $referral)->onQueue('registrations');

        $expiresAt = now()->addDays(14);
        $plainToken = $user->createToken('mobile', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => new ApiUserResource($user),
        ], 201);
    }

    public function user(Request $request): ApiUserResource
    {
        return new ApiUserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
