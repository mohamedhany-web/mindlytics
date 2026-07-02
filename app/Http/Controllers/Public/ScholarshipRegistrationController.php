<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipProgram;
use App\Models\User;
use App\Services\Scholarship\ScholarshipRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ScholarshipRegistrationController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        $program = ScholarshipProgram::query()
            ->where('slug', $slug)
            ->with(['instructor', 'course'])
            ->firstOrFail();

        if (! $program->isRegistrationOpen()) {
            return view('scholarships.closed', compact('program'));
        }

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'student') {
                app(ScholarshipRegistrationService::class)->attachUserToProgram($program, $user);

                return redirect()->route('dashboard')
                    ->with('success', 'تم تسجيلك في منحة «' . $program->name . '» — بانتظار تفعيل الأكاديمية.');
            }

            return redirect()->route('dashboard')
                ->with('warning', 'التسجيل في المنح متاح لحسابات الطلاب فقط. سجّل الخروج أو استخدم حساب طالب.');
        }

        return view('auth.register', $this->registerViewData($program));
    }

    public function register(Request $request, string $slug, ScholarshipRegistrationService $registrationService): RedirectResponse
    {
        $program = ScholarshipProgram::query()->where('slug', $slug)->firstOrFail();

        if (! $program->isRegistrationOpen()) {
            return $this->redirectToRegisterForm($program)
                ->withErrors(['error' => 'التسجيل في هذه المنحة مغلق حالياً.']);
        }

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'student') {
                $registrationService->attachUserToProgram($program, $user);

                return redirect()->route('dashboard')
                    ->with('success', 'تم تسجيلك في منحة «' . $program->name . '» — بانتظار تفعيل الأكاديمية.');
            }

            return redirect()->route('dashboard')
                ->with('warning', 'التسجيل في المنح متاح لحسابات الطلاب فقط.');
        }

        $countries = config('phone_countries.countries', []);

        $validator = Validator::make($request->only([
            'name', 'country_code', 'phone', 'email', 'password', 'password_confirmation',
        ]), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return $this->redirectToRegisterForm($program)->withErrors($validator);
        }

        $country = collect($countries)->firstWhere('dial_code', $request->country_code);
        if (! $country || ! isset($country['validation']['regex'])) {
            return $this->redirectToRegisterForm($program)
                ->withErrors(['phone' => 'كود الدولة غير مدعوم.']);
        }

        $nationalNumber = preg_replace('/\D/', '', $request->phone);
        $nationalNumber = ltrim($nationalNumber, '0');
        if (! preg_match($country['validation']['regex'], $nationalNumber)) {
            return $this->redirectToRegisterForm($program)
                ->withErrors(['phone' => 'رقم الهاتف غير صحيح لهذه الدولة.']);
        }

        $dial = $country['dial_code'] ?? '';
        $fullPhone = ($dial === '' || $dial === 'OTHER') ? ('OTHER_' . $nationalNumber) : ($dial . $nationalNumber);
        if (User::where('phone', $fullPhone)->exists()) {
            return $this->redirectToRegisterForm($program)
                ->withErrors(['phone' => 'رقم الهاتف مسجل مسبقاً']);
        }

        $registrationService->registerNewUser($program, [
            'name' => $request->name,
            'phone' => $fullPhone,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم إنشاء حسابك والتسجيل في منحة «' . $program->name . '» — بانتظار تفعيل الأكاديمية.');
    }

    public function landing(string $slug): View
    {
        $program = ScholarshipProgram::query()
            ->where('slug', $slug)
            ->with('instructor')
            ->firstOrFail();

        return view('scholarships.show', compact('program'));
    }

    /**
     * @return array<string, mixed>
     */
    private function registerViewData(ScholarshipProgram $program): array
    {
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        $authBackgroundUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            ? asset('storage/' . \App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            : asset('images/brainstorm-meeting.jpg');

        return [
            'phoneCountries' => $phoneCountries,
            'defaultCountry' => $defaultCountry,
            'authBackgroundUrl' => $authBackgroundUrl,
            'registerFormAction' => route('scholarships.register.post', $program->slug),
            'scholarshipProgram' => $program,
        ];
    }

    private function redirectToRegisterForm(ScholarshipProgram $program): RedirectResponse
    {
        return redirect()
            ->route('scholarships.register', $program->slug)
            ->withInput();
    }
}
