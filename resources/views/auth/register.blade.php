@extends('layouts.site-auth')

@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
    $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
    $selectedDial = old('country_code', $defaultCountry['dial_code'] ?? '+20');
    $defaultPromo = old('promo_code', request('promo', request('code')));
    $defaultRef = old('referral_code', request('ref'));
    $openCodes = filled($defaultPromo) || filled($defaultRef) || $errors->has('promo_code') || $errors->has('referral_code');
@endphp

@section('title', __('auth.register'))
@section('aside_title', $t('ابدأ رحلتك في عالم البرمجيات', 'Start your software career'))
@section('aside_text', $t('حساب واحد تتابع منه كورساتك ودفعاتك وواجباتك وشهاداتك.', 'One account for your courses, cohorts, assignments and certificates.'))

@section('content')
    <h1>{{ __('auth.register') }}</h1>
    <p class="a-sub">{{ __('auth.register_subtitle') }}</p>

    @if(! empty($scholarshipProgram))
        <div class="a-note a-note-warn">
            <b>{{ $t('تسجيل في منحة:', 'Scholarship registration:') }} {{ $scholarshipProgram->name }}</b>
            <div style="font-size: 13px; margin-top: 4px">{{ $t('بعد إنشاء الحساب ستُسجَّل في المنحة وتنتظر تفعيل الأكاديمية لفتح الكورس.', 'After you sign up you are enrolled in the scholarship and wait for the academy to activate the course.') }}</div>
        </div>
    @endif

    @if($errors->any() && ! $errors->hasAny(['name', 'phone', 'email', 'password', 'promo_code', 'referral_code', 'country_code']))
        <div class="a-alert a-alert-err" role="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ $registerFormAction ?? route('register') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px">
        @csrf

        <label class="h-field" for="name">{{ __('auth.full_name') }}
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" placeholder="{{ __('auth.enter_full_name') }}" @error('name') aria-invalid="true" @enderror>
            @error('name')<span class="h-field-error">{{ $message }}</span>@enderror
        </label>

        <div class="h-field">
            <label for="phone">{{ __('auth.phone_number') }}</label>
            <div class="a-phone" dir="ltr">
                <select name="country_code" id="country_code" required aria-label="{{ __('auth.country_code_aria') }}">
                    @foreach($phoneCountries as $c)
                        <option value="{{ $c['dial_code'] }}" @selected($selectedDial === $c['dial_code'])>{{ $c['dial_code'] }} {{ $siteAr ? ($c['name_ar'] ?? $c['name'] ?? '') : ($c['name_en'] ?? $c['name'] ?? $c['name_ar'] ?? '') }}</option>
                    @endforeach
                </select>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required autocomplete="tel-national" inputmode="tel" placeholder="1xxxxxxxxx" aria-label="{{ __('auth.phone_aria') }}" @error('phone') aria-invalid="true" @enderror>
            </div>
            @error('phone')<span class="h-field-error">{{ $message }}</span>@enderror
        </div>

        <label class="h-field" for="email">{{ __('auth.email') }}
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" placeholder="name@example.com" @error('email') aria-invalid="true" @enderror>
            @error('email')<span class="h-field-error">{{ $message }}</span>@enderror
        </label>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px">
            <div class="h-field">
                <label for="password">{{ __('auth.password') }}</label>
                <div class="a-pass">
                    <input type="password" name="password" id="password" required autocomplete="new-password" minlength="8" @error('password') aria-invalid="true" @enderror>
                    <button type="button" data-toggle-password="password" data-show="{{ $t('إظهار', 'Show') }}" data-hide="{{ $t('إخفاء', 'Hide') }}" aria-pressed="false" aria-label="{{ $t('إظهار كلمة المرور', 'Show password') }}">{{ $t('إظهار', 'Show') }}</button>
                </div>
            </div>
            <div class="h-field">
                <label for="password_confirmation">{{ __('auth.password_confirmation') }}</label>
                <div class="a-pass">
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" minlength="8">
                    <button type="button" data-toggle-password="password_confirmation" data-show="{{ $t('إظهار', 'Show') }}" data-hide="{{ $t('إخفاء', 'Hide') }}" aria-pressed="false" aria-label="{{ $t('إظهار تأكيد كلمة المرور', 'Show password confirmation') }}">{{ $t('إظهار', 'Show') }}</button>
                </div>
            </div>
        </div>
        <span class="h-field-hint" style="margin-top: -8px">{{ $t('8 حروف على الأقل.', 'At least 8 characters.') }}</span>
        @error('password')<span class="h-field-error" style="margin-top: -8px">{{ $message }}</span>@enderror

        @if(empty($scholarshipProgram))
            <details class="a-codes" @if($openCodes) open @endif>
                <summary>{{ $t('عندك كود إحالة أو كود خصم؟', 'Have a referral or discount code?') }}</summary>
                <div class="a-codes-body">
                    <label class="h-field" for="referral_code">{{ $t('كود الإحالة (اختياري)', 'Referral code (optional)') }}
                        <input type="text" name="referral_code" id="referral_code" value="{{ $defaultRef }}" dir="ltr" style="text-transform: uppercase" placeholder="REF...">
                        @error('referral_code')<span class="h-field-error">{{ $message }}</span>@enderror
                    </label>
                    <label class="h-field" for="promo_code">{{ $t('كود خصم الورشة (اختياري)', 'Workshop discount code (optional)') }}
                        <input type="text" name="promo_code" id="promo_code" value="{{ $defaultPromo }}" dir="ltr" style="text-transform: uppercase" placeholder="WS-XXXXXX">
                        <span class="h-field-hint">{{ $t('لو حضرت ورشة وأخدت كود خصم، اكتبه هنا عشان يتفعّل على حسابك.', 'If you attended a workshop and got a code, enter it to activate it on your account.') }}</span>
                        @error('promo_code')<span class="h-field-error">{{ $message }}</span>@enderror
                    </label>
                </div>
            </details>
        @endif

        <label class="a-check" style="align-items: flex-start; line-height: 1.6">
            <input type="checkbox" id="terms" required style="margin-top: 3px">
            <span>{{ __('auth.agree_terms') }} <a href="{{ route('public.terms') }}" target="_blank" class="h-link">{{ __('auth.terms_of_use') }}</a> {{ __('auth.and') }} <a href="{{ route('public.privacy') }}" target="_blank" class="h-link">{{ __('auth.privacy_policy') }}</a></span>
        </label>

        <button type="submit" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ __('auth.create_account_btn') }}</button>
    </form>

    <p class="a-switch">{{ __('auth.already_have_account') }} <a href="{{ route('login') }}" class="h-link">{{ __('auth.go_to_login') }}</a></p>
@endsection
