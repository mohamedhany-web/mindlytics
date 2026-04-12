@php $authLocale = app()->getLocale(); $authRtl = $authLocale === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ $authLocale }}" dir="{{ $authRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.reset_password') }} - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ $platformFaviconUrl ?? asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ $platformLogoUrl ?? asset('logo-removebg-preview.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --color-primary: #2563eb; --color-primary-hover: #1d4ed8; --color-primary-light: rgba(37, 99, 235, 0.12); --input-bg: #f3f4f6; --input-border: #e5e7eb; --text-dark: #1f2937; --text-muted: #6b7280; }
        * { font-family: 'Cairo', 'Tajawal', sans-serif; }
        body { background: #f9fafb; min-height: 100vh; margin: 0; padding: 0; }
        .form-input { background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 12px; transition: all 0.2s ease; }
        .form-input:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-light); outline: none; background: #fff; }
        .btn-primary { background: var(--color-primary); transition: all 0.2s ease; }
        .btn-primary:hover { background: var(--color-primary-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35); }
        .link-primary { color: var(--color-primary); font-weight: 700; text-decoration: underline; }
        .link-primary:hover { color: var(--color-primary-hover); }
    </style>
</head>
<body x-data="{ showPassword: false, showPasswordConfirmation: false }">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
            <div class="w-full max-w-md">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[var(--text-muted)] hover:text-[var(--color-primary)] text-sm font-medium mb-8">
                    <i class="fas fa-arrow-{{ $authRtl ? 'right' : 'left' }}"></i>
                    {{ __('auth.login') }}
                </a>
                <h1 class="text-2xl lg:text-3xl font-black text-[var(--text-dark)] mb-2">{{ __('auth.reset_password') }}</h1>
                <p class="text-[var(--text-muted)] mb-8">{{ __('auth.reset_password_help') }}</p>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
                        @foreach ($errors->all() as $err) {{ $err }} @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div>
                        <label for="email" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">{{ __('auth.email') }}</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required autocomplete="email"
                               class="form-input w-full px-4 py-3.5 rounded-xl text-[var(--text-dark)] font-medium"
                               placeholder="example@email.com" dir="ltr">
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">{{ __('auth.new_password') }}</label>
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required autocomplete="new-password"
                               class="form-input w-full px-4 py-3.5 pr-12 rounded-xl text-[var(--text-dark)] font-medium"
                               placeholder=".........">
                        <button type="button" @click="showPassword = !showPassword" class="absolute left-3 top-[38px] -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--color-primary)]">
                            <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                            <i x-show="showPassword" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <label for="password_confirmation" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">{{ __('auth.password_confirmation') }}</label>
                        <input :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                               class="form-input w-full px-4 py-3.5 pr-12 rounded-xl text-[var(--text-dark)] font-medium"
                               placeholder=".........">
                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute left-3 top-[38px] -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--color-primary)]">
                            <i x-show="!showPasswordConfirmation" class="fas fa-eye text-sm"></i>
                            <i x-show="showPasswordConfirmation" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-white font-bold text-base flex items-center justify-center gap-2">
                        <i class="fas fa-key"></i>
                        {{ __('auth.reset_password') }}
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--text-muted)] mt-8">
                    <a href="{{ route('login') }}" class="link-primary">{{ __('auth.go_to_login') }}</a>
                </p>
            </div>
        </div>
        <div class="flex-1 min-h-[240px] lg:min-h-screen bg-cover bg-center relative" style="background-image: url('{{ $authBackgroundUrl ?? asset("images/brainstorm-meeting.jpg") }}');">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 to-blue-700/70"></div>
            <div class="relative z-10 flex flex-col items-center justify-center p-8 text-white text-center min-h-[240px] lg:min-h-screen">
                <h2 class="text-xl lg:text-3xl font-black mb-2">{{ config('app.name') }}</h2>
                <p class="text-white/90 text-sm lg:text-base">{{ __('auth.visual_desc') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
