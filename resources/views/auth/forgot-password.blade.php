@extends('layouts.site-auth')

@php($siteAr = app()->getLocale() === 'ar')

@section('title', __('auth.forgot_password'))

@section('content')
    <a href="{{ route('login') }}" class="h-link" style="font-size: 14px">{{ $siteAr ? '→' : '←' }} {{ __('auth.login') }}</a>
    <h1>{{ __('auth.forgot_password') }}</h1>
    <p class="a-sub">{{ __('auth.forgot_password_help') }}</p>

    @if(session('status'))
        <div class="a-alert a-alert-ok" role="status">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="a-alert a-alert-err" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" style="display: flex; flex-direction: column; gap: 18px">
        @csrf
        <label class="h-field" for="email">{{ __('auth.email') }}
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" autofocus>
        </label>
        <button type="submit" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ __('auth.send_reset_link') }}</button>
    </form>

    <p class="a-switch"><a href="{{ route('login') }}" class="h-link">{{ __('auth.go_to_login') }}</a></p>
@endsection
