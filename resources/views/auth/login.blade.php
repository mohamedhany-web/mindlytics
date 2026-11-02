@extends('layouts.site-auth')

@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
@endphp

@section('title', __('auth.login'))

@section('content')
    <h1>{{ __('auth.welcome_back') }}</h1>
    <p class="a-sub">{{ __('auth.enter_credentials') }}</p>

    @if(session('status'))
        <div class="a-alert a-alert-ok" role="status">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="a-alert a-alert-err" role="alert">{{ session('error') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST" style="display: flex; flex-direction: column; gap: 18px">
        @csrf
        {{-- Honeypot: البوتات بتملاه، والإنسان مش بيشوفه --}}
        <div class="a-honeypot" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <label class="h-field" for="email">{{ __('auth.email') }}
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror autofocus>
        </label>
        @error('email')<p id="email-error" class="h-field-error" style="margin: -12px 0 0">{{ $message }}</p>@enderror

        <div class="h-field">
            <span style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px">
                <label for="password">{{ __('auth.password') }}</label>
                <a href="{{ route('password.request') }}" class="h-link" style="font-size: 13px">{{ __('auth.forgot_password') }}</a>
            </span>
            <div class="a-pass">
                <input type="password" name="password" id="password" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                <button type="button" data-toggle-password="password" data-show="{{ $t('إظهار', 'Show') }}" data-hide="{{ $t('إخفاء', 'Hide') }}" aria-pressed="false" aria-label="{{ $t('إظهار كلمة المرور', 'Show password') }}">{{ $t('إظهار', 'Show') }}</button>
            </div>
        </div>
        @error('password')<p id="password-error" class="h-field-error" style="margin: -12px 0 0">{{ $message }}</p>@enderror

        <label class="a-check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('auth.remember') }}</label>

        <button type="submit" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ __('auth.login') }}</button>
    </form>

    <p class="a-switch">{{ __('auth.no_account_question') }} <a href="{{ route('register') }}" class="h-link">{{ __('auth.no_account_register_now') }}</a></p>
@endsection
