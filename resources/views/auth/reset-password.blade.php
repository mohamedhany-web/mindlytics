@extends('layouts.site-auth')

@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
@endphp

@section('title', __('auth.reset_password'))

@section('content')
    <a href="{{ route('login') }}" class="h-link" style="font-size: 14px">{{ $siteAr ? '→' : '←' }} {{ __('auth.login') }}</a>
    <h1>{{ __('auth.reset_password') }}</h1>
    <p class="a-sub">{{ __('auth.reset_password_help') }}</p>

    @if($errors->any())
        <div class="a-alert a-alert-err" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 18px">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="h-field" for="email">{{ __('auth.email') }}
            <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" required autocomplete="email" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}">
        </label>
        <div class="h-field">
            <label for="password">{{ __('auth.password') }}</label>
            <div class="a-pass">
                <input type="password" name="password" id="password" required autocomplete="new-password" minlength="8" autofocus>
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
        <button type="submit" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ __('auth.reset_password') }}</button>
    </form>
@endsection
