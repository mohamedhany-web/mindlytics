<?php

namespace App\Domains\Community\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * خدمة تسجيل مستخدمين جدد في مجتمع البيانات.
 * نفس قواعد المنصة الرئيسية (نفس جدول المستخدمين) مع إمكانية التوسع لاحقاً.
 */
class CommunityRegistrationService
{
    public function validateAndCreate(Request $request): User
    {
        $countries = config('phone_countries.countries', []);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
            throw new ValidationException($validator);
        }

        $country = collect($countries)->firstWhere('dial_code', $request->country_code);
        if (!$country || !isset($country['validation']['regex'])) {
            throw ValidationException::withMessages(['phone' => 'كود الدولة غير مدعوم.']);
        }

        $nationalNumber = preg_replace('/\D/', '', $request->phone);
        $nationalNumber = ltrim($nationalNumber, '0');
        if (!preg_match($country['validation']['regex'], $nationalNumber)) {
            $example = $country['example'] ?? $country['placeholder'] ?? '';
            throw ValidationException::withMessages(['phone' => 'رقم الهاتف غير صحيح لهذه الدولة. مثال: ' . $example]);
        }

        $dial = $country['dial_code'] ?? '';
        $fullPhone = ($dial === '' || $dial === 'OTHER') ? ('OTHER_' . $nationalNumber) : ($dial . $nationalNumber);
        if (User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages(['phone' => 'رقم الهاتف مسجل مسبقاً']);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $fullPhone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => true,
        ]);

        \App\Jobs\ProcessStudentRegistration::dispatch(
            $user->id,
            $request->referral_code
        )->onQueue('registrations');

        return $user;
    }

    public function getPhoneCountriesData(): array
    {
        $countries = config('phone_countries.countries', []);
        $defaultCountry = collect($countries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        return ['countries' => $countries, 'defaultCountry' => $defaultCountry];
    }
}
