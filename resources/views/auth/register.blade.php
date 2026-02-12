<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء حساب - Mindlytics</title>

    <!-- خط عربي - تحميل غير معطل (تحسين FCP/LCP) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap"></noscript>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - تحميل غير معطل -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <style>
        :root {
            --color-primary: #2563eb;
            --color-primary-hover: #1d4ed8;
            --color-primary-light: rgba(37, 99, 235, 0.12);
            --input-bg: #f3f4f6;
            --input-border: #e5e7eb;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }

        * {
            font-family: 'Cairo', 'Noto Sans Arabic', sans-serif;
        }

        body {
            overflow: hidden;
            background: #f9fafb;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* النافبار على الهاتف - نفس ألوان النافبار الرئيسية */
        .navbar-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%) !important;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .register-nav-mobile-only {
            display: none;
        }

        .register-wrapper {
            height: 100vh;
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        /* نفس ترتيب تسجيل الدخول: RTL - النموذج يمين، الخلفية يسار */
        .register-container {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: stretch;
            position: relative;
            flex-direction: row-reverse;
        }

        /* لوحة النموذج - بيضاء مثل تسجيل الدخول */
        .register-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 48px 56px;
            background: #ffffff;
            position: relative;
            height: 100%;
            overflow-y: auto;
            z-index: 1;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.06);
        }

        .register-form-wrapper {
            width: 100%;
            max-width: 750px;
            position: relative;
            z-index: 1;
        }

        .register-form-wrapper h2 {
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .register-page-title {
            user-select: none;
            caret-color: transparent;
        }

        /* قسم الخلفية - صورة brainstorm مثل تسجيل الدخول */
        .register-visual-section {
            flex: 1.1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: url('{{ $authBackgroundUrl ?? asset("images/brainstorm-meeting.jpg") }}') center center / cover no-repeat;
            position: relative;
            overflow: hidden;
            height: 100%;
            z-index: 1;
        }

        .register-visual-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(30, 64, 175, 0.75) 0%, rgba(37, 99, 235, 0.7) 30%, rgba(59, 130, 246, 0.65) 100%);
            z-index: 0;
        }

        .visual-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            animation: float 20s infinite ease-in-out;
            backdrop-filter: blur(10px);
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: 5s;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 10%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        /* حقول الإدخال - مثل تسجيل الدخول */
        .form-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px var(--color-primary-light);
            outline: none;
            background: #fff;
        }

        .form-input:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        /* زر إنشاء الحساب - ألوان المنصة مثل تسجيل الدخول */
        .btn-register {
            background: var(--color-primary);
            transition: all 0.2s ease;
        }

        .btn-register:hover {
            background: var(--color-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        .link-primary {
            color: var(--color-primary);
            font-weight: 700;
            text-decoration: underline;
        }

        .link-primary:hover {
            color: var(--color-primary-hover);
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.875rem;
        }

        .form-grid-full {
            grid-column: 1 / -1;
        }

        /* صف كود الدولة + رقم الهاتف */
        .phone-country-row {
            display: flex;
            align-items: stretch;
            min-height: 2.75rem;
        }
        .phone-country-row select {
            min-width: 8.5rem;
            max-width: 11rem;
            padding-right: 0.5rem;
            padding-left: 0.5rem;
            cursor: pointer;
        }
        @media (min-width: 641px) {
            .phone-country-row select {
                -webkit-appearance: none;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%233b82f6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.5rem center;
                background-size: 1.25rem;
                padding-right: 2rem;
            }
        }
        .phone-country-row select option {
            padding: 0.5rem;
            font-size: 0.875rem;
            direction: ltr;
            text-align: left;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            body {
                overflow-y: auto;
            }

            .register-wrapper {
                height: auto;
                min-height: 100vh;
            }

            .register-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .register-visual-section {
                padding: 40px 24px;
                height: auto;
                min-height: 280px;
                width: 100%;
            }

            .register-form-section {
                padding: 40px 24px;
                height: auto;
                min-height: auto;
                width: 100%;
                margin-top: 0;
                box-shadow: none;
            }

            .register-form-wrapper {
                max-width: 100%;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .visual-content {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .register-wrapper {
                padding-top: 0;
            }

            .register-visual-section {
                padding: 1.25rem 1rem 1rem;
                min-height: auto;
                width: 100%;
                display: block;
            }

            .register-form-section {
                padding: 1.25rem 1rem 1.5rem;
                min-height: auto;
                width: 100%;
                display: block;
            }

            .register-form-wrapper {
                max-width: 100%;
                width: 100%;
            }
            .register-form-section {
                overflow-x: hidden;
            }
            .register-form-wrapper form {
                min-width: 0;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .form-grid label {
                font-size: 0.875rem;
            }

            .visual-content {
                max-width: 100%;
                width: 100%;
            }

            .visual-content h1 {
                font-size: 1.75rem !important;
                margin-bottom: 1rem !important;
            }

            .visual-content p {
                font-size: 0.9rem !important;
                margin-bottom: 1.5rem !important;
            }

            .register-form-wrapper h2 {
                font-size: 1.75rem !important;
                margin-bottom: 0.5rem !important;
            }

            .form-input {
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
            }

            .phone-country-row {
                min-height: 3rem;
            }
            .phone-country-row select {
                min-width: 8.5rem;
                max-width: 50%;
                padding: 0.75rem 2rem 0.75rem 1rem !important;
                font-size: 0.9rem !important;
            }
            .phone-country-row input {
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
            }

            .btn-register {
                padding: 0.875rem 1rem !important;
                font-size: 0.95rem !important;
            }

            .shape-1 {
                width: 150px;
                height: 150px;
            }

            .shape-2 {
                width: 120px;
                height: 120px;
            }

            .shape-3 {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .register-visual-section {
                padding: 35px 18px;
                width: 100%;
            }

            .register-form-section {
                padding: 35px 18px;
                width: 100%;
            }

            .visual-content h1 {
                font-size: 1.5rem !important;
            }

            .register-form-wrapper h2 {
                font-size: 1.5rem !important;
            }

            .visual-content .flex {
                flex-direction: column;
                gap: 0.75rem !important;
            }

            .visual-content .flex > div {
                width: 100%;
            }

            /* هاتف: حقل الهاتف بالكامل مع ظهور الأكواد */
            .phone-country-row {
                flex-wrap: nowrap;
                width: 100%;
                min-height: 3.25rem;
            }
            .phone-country-row select {
                min-width: 7.5rem;
                max-width: 45%;
                flex-shrink: 0;
                font-size: 0.85rem !important;
                padding-right: 1.75rem !important;
            }
            .phone-country-row input {
                flex: 1;
                min-width: 0;
            }
        }

        /* ─── تصميم الهاتف: مثل تسجيل الدخول ─── */
        .register-mobile-wrap {
            display: none;
            min-height: 100vh;
            background: #f9fafb;
            padding-bottom: 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .register-nav-mobile-only {
                display: block !important;
            }
            .register-wrapper {
                padding-top: 5rem;
                height: auto;
                min-height: 100vh;
                width: 100%;
            }
            .register-container {
                display: none !important;
            }
            .register-mobile-wrap {
                display: block;
                width: 100%;
                padding: 0 0.75rem 2rem;
                box-sizing: border-box;
            }
            .register-mobile-welcome {
                margin: 0.5rem 0 0;
                width: 100%;
                box-sizing: border-box;
            }
            .register-mobile-form-wrap {
                width: 100%;
                padding: 1rem 0.75rem 0;
                box-sizing: border-box;
            }
            .register-mobile-form-card {
                width: 100%;
                max-width: none;
                box-sizing: border-box;
            }
            body {
                overflow-y: auto;
                overflow-x: hidden;
                width: 100%;
            }
            html {
                width: 100%;
                overflow-x: hidden;
            }
        }

        .register-mobile-welcome {
            margin: 0.5rem 0 0;
            padding: 2rem 1rem 2rem;
            min-height: 9rem;
            background: url('{{ $authBackgroundUrl ?? asset("images/brainstorm-meeting.jpg") }}') center center / cover no-repeat;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }
        .register-mobile-welcome::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.8) 0%, rgba(37, 99, 235, 0.75) 100%);
            z-index: 0;
        }
        .register-mobile-welcome::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -5%;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            z-index: 0;
        }
        .register-mobile-welcome .welcome-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 0.5rem 0;
            position: relative;
            z-index: 1;
        }
        .register-mobile-welcome .welcome-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.95);
            line-height: 1.5;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .register-mobile-form-wrap {
            padding: 1.5rem 0.75rem 0;
            width: 100%;
            max-width: 100%;
        }
        .register-mobile-form-wrap .section-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            text-align: center;
            margin: 0 0 0.35rem 0;
        }
        .register-mobile-form-wrap .section-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            margin: 0 0 1.25rem 0;
        }
        .register-mobile-form-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.5rem 1.25rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        .register-mobile-form-card .form-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .register-mobile-form-card .form-input:focus {
            background: #fff;
            border-color: var(--color-primary);
            outline: none;
            box-shadow: 0 0 0 3px var(--color-primary-light);
        }
        .register-mobile-form-card .form-input.pl-12 {
            padding-left: 2.75rem;
        }
        .register-mobile-form-card label {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
        }
        .register-mobile-form-card .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .register-mobile-form-card .input-wrap .input-icon {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }
        .register-mobile-form-card .btn-register {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            border-radius: 12px;
        }
    </style>
</head>
<body x-data="{ showPassword: false, showPasswordConfirm: false }">
    <div class="register-nav-mobile-only">
        @include('components.unified-navbar')
    </div>

    <!-- Register Wrapper -->
    <div class="register-wrapper">
        <!-- تصميم الهاتف فقط -->
        <div class="register-mobile-wrap">
            <div class="register-mobile-welcome">
                <h1 class="welcome-title">انضم إلينا</h1>
                <p class="welcome-desc">أنشئ حسابك وابدأ رحلتك التعليمية مع Mindlytics</p>
            </div>

            <div class="register-mobile-form-wrap">
                <h2 class="section-title">إنشاء حساب</h2>
                <p class="section-subtitle">أدخل بياناتك لإنشاء حسابك الجديد</p>

                <div class="register-mobile-form-card">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        @php
                            $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
                            $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
                        @endphp

                        <div class="bg-[var(--color-primary-light)] border border-[var(--input-border)] rounded-xl p-2.5 mb-3">
                            <p class="text-xs font-bold text-[var(--text-dark)]">تسجيل الطلاب فقط</p>
                        </div>

                        <div class="input-wrap">
                            <label for="name_m">الاسم الكامل</label>
                            <div class="relative">
                                <i class="input-icon fas fa-user"></i>
                                <input type="text" name="name" id="name_m" value="{{ old('name') }}" required class="form-input w-full" placeholder="الاسم الكامل">
                            </div>
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="input-wrap">
                            <label>رقم الهاتف</label>
                            <div class="phone-country-row flex rounded-xl overflow-hidden border border-[var(--input-border)] bg-[var(--input-bg)] focus-within:border-[var(--color-primary)]">
                                <select name="country_code" required class="form-input shrink-0 py-2.5 rounded-none border-0 border-l border-[var(--input-border)] text-sm min-w-[5rem]" dir="ltr">
                                    @foreach($phoneCountries ?? [] as $c)
                                        <option value="{{ $c['dial_code'] }}" {{ old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : '' }}>{{ $c['dial_code'] }} {{ $c['name_ar'] }}</option>
                                    @endforeach
                                </select>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required class="form-input flex-1 min-w-0 py-2.5 px-3 border-0 text-sm" placeholder="xxxxxxxx" dir="ltr">
                            </div>
                            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="input-wrap">
                            <label for="email_m">البريد الإلكتروني (اختياري)</label>
                            <div class="relative">
                                <i class="input-icon fas fa-envelope"></i>
                                <input type="email" name="email" id="email_m" value="{{ old('email') }}" class="form-input w-full" placeholder="example@email.com" dir="ltr">
                            </div>
                            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="input-wrap">
                            <label for="referral_m">كود الإحالة (اختياري)</label>
                            <div class="relative">
                                <i class="input-icon fas fa-gift"></i>
                                <input type="text" name="referral_code" id="referral_m" value="{{ request()->get('ref') ?? old('referral_code') }}" class="form-input w-full" placeholder="REF123456" dir="ltr">
                            </div>
                        </div>

                        <div class="input-wrap">
                            <label for="password_m">كلمة المرور</label>
                            <div class="relative">
                                <i class="input-icon fas fa-lock"></i>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password_m" required class="form-input w-full pl-12" placeholder=".........">
                                <button type="button" @click="showPassword = !showPassword" class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]"><i x-show="!showPassword" class="fas fa-eye text-sm"></i><i x-show="showPassword" class="fas fa-eye-slash text-sm"></i></button>
                            </div>
                            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="input-wrap">
                            <label for="password_confirmation_m">تأكيد كلمة المرور</label>
                            <div class="relative">
                                <i class="input-icon fas fa-lock"></i>
                                <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation_m" required class="form-input w-full pl-12" placeholder=".........">
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)]"><i x-show="!showPasswordConfirm" class="fas fa-eye text-sm"></i><i x-show="showPasswordConfirm" class="fas fa-eye-slash text-sm"></i></button>
                            </div>
                        </div>

                        <div class="flex items-start gap-2 mb-4">
                            <input type="checkbox" id="terms_m" required class="mt-0.5 h-4 w-4 rounded border-[var(--input-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <label for="terms_m" class="text-xs text-[var(--text-muted)]">أوافق على <a href="#" class="link-primary">شروط الاستخدام</a> و <a href="#" class="link-primary">سياسة الخصوصية</a></label>
                        </div>

                        <button type="submit" class="btn-register w-full py-3 rounded-xl text-white font-bold flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            <span>إنشاء الحساب</span>
                        </button>

                        <div class="text-center pt-4 mt-4 border-t border-[var(--input-border)]">
                            <p class="text-sm text-[var(--text-muted)] mb-1">لديك حساب بالفعل؟</p>
                            <a href="{{ route('login') }}" class="link-primary text-sm inline-flex items-center gap-1">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>سجّل الدخول</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="register-container">
            <!-- القسم البصري - صورة brainstorm مثل تسجيل الدخول -->
            <div class="register-visual-section">
                <div class="floating-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
                <div class="visual-content">
                    <h1 class="text-xl sm:text-2xl md:text-4xl lg:text-5xl font-black mb-2 md:mb-6 leading-tight text-white drop-shadow-lg visual-title">
                        معاً نُطوّر مهاراتنا
                    </h1>
                    <p class="text-xs sm:text-sm md:text-lg lg:text-xl text-white/90 mb-3 md:mb-8 leading-relaxed font-bold px-1 md:px-2 drop-shadow-md visual-desc">
                        تعلّم، تشارك، وتنمو في بيئة واحدة
                    </p>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-4 px-1 md:px-2 visual-badges">
                        <div class="flex items-center gap-1.5 md:gap-2 bg-white/10 backdrop-blur-md px-2 py-1.5 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-check-circle text-white/90 text-xs md:text-base"></i>
                            <span class="font-bold text-[10px] md:text-sm text-white">تعلم فعّال</span>
                        </div>
                        <div class="flex items-center gap-1.5 md:gap-2 bg-white/10 backdrop-blur-md px-2 py-1.5 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-users text-white/90 text-xs md:text-base"></i>
                            <span class="font-bold text-[10px] md:text-sm text-white">تعاون ومشاركة</span>
                        </div>
                        <div class="flex items-center gap-1.5 md:gap-2 bg-white/10 backdrop-blur-md px-2 py-1.5 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-chart-line text-white/90 text-xs md:text-base"></i>
                            <span class="font-bold text-[10px] md:text-sm text-white">نمو مستمر</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- لوحة النموذج البيضاء - مثل تسجيل الدخول -->
            <div class="register-form-section">
                <div class="register-form-wrapper">
                    <h2 class="register-page-title text-2xl md:text-3xl font-black text-[var(--text-dark)] text-center mb-6">
                        إنشاء حساب في <span class="text-[var(--color-primary)]">Mindlytics</span>
                    </h2>

                    <!-- Register Form -->
                    <form action="{{ route('register') }}" method="POST" class="space-y-2.5 md:space-y-3">
                        @csrf
                        
                        <!-- Student Notice -->
                        <div class="bg-[var(--color-primary-light)] border border-[var(--input-border)] rounded-xl p-3 mb-4">
                            <p class="text-sm font-bold text-[var(--text-dark)]">تسجيل الطلاب فقط — أدخل بياناتك لإنشاء حسابك</p>
                        </div>

                        <!-- Form Grid -->
                        @php
                            $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
                            $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
                        @endphp
                        <div class="form-grid">
                            <!-- الاسم الكامل -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    الاسم الكامل
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       required 
                                       class="form-input w-full px-4 py-3 rounded-xl text-[var(--text-dark)] font-medium @error('name') border-red-500 @enderror" 
                                       placeholder="أدخل اسمك الكامل">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- رقم الهاتف مع كود الدولة -->
                            <div>
                                <label for="phone" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    رقم الهاتف
                                </label>
                                <div class="phone-country-row flex rounded-xl overflow-hidden border border-[var(--input-border)] bg-[var(--input-bg)] transition-all focus-within:border-[var(--color-primary)] focus-within:ring-2 focus-within:ring-[var(--color-primary-light)] @error('phone') border-red-500 @enderror">
                                    <select name="country_code" 
                                            id="country_code" 
                                            required
                                            class="form-input shrink-0 py-2.5 rounded-l-xl rounded-r-none border-0 border-l border-[var(--input-border)] text-[var(--text-dark)] font-medium text-sm bg-transparent focus:ring-0"
                                            dir="ltr"
                                            aria-label="كود الدولة">
                                        @foreach($phoneCountries ?? [] as $c)
                                            <option value="{{ $c['dial_code'] }}" {{ old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : '' }}>
                                                {{ $c['dial_code'] }} {{ $c['name_ar'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone') }}"
                                           required 
                                           class="form-input flex-1 min-w-0 px-3 py-2.5 rounded-r-xl rounded-l-none border-0 text-[var(--text-dark)] font-medium text-sm bg-transparent focus:ring-0 @error('phone') border-red-500 @enderror" 
                                           placeholder="xxxxxxxx" 
                                           dir="ltr"
                                           aria-label="رقم الهاتف">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div>
                                <label for="email" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    البريد الإلكتروني (اختياري)
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="form-input w-full px-4 py-3 rounded-xl text-[var(--text-dark)] font-medium @error('email') border-red-500 @enderror" 
                                       placeholder="example@email.com"
                                       dir="ltr">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- كود الإحالة -->
                            @php
                                $referralCode = request()->get('ref') ?? old('referral_code');
                            @endphp
                            <div>
                                <label for="referral_code" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    كود الإحالة (اختياري)
                                </label>
                                <input type="text" 
                                       name="referral_code" 
                                       id="referral_code" 
                                       value="{{ $referralCode }}"
                                       class="form-input w-full px-4 py-3 rounded-xl text-[var(--text-dark)] font-medium uppercase" 
                                       placeholder="REF123456"
                                       dir="ltr">
                            </div>

                            <!-- كلمة المرور -->
                            <div>
                                <label for="password" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    كلمة المرور
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           id="password" 
                                           required 
                                           class="form-input w-full px-4 py-3 pr-10 pl-11 rounded-xl text-[var(--text-dark)] font-medium @error('password') border-red-500 @enderror" 
                                           placeholder="أدخل كلمة مرور قوية">
                                    <button type="button" 
                                            @click="showPassword = !showPassword" 
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--color-primary)] transition-colors focus:outline-none">
                                        <i x-show="!showPassword" class="fas fa-eye text-xs"></i>
                                        <i x-show="showPassword" class="fas fa-eye-slash text-xs"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-[var(--text-dark)] mb-1.5">
                                    تأكيد كلمة المرور
                                </label>
                                <div class="relative">
                                    <input :type="showPasswordConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           id="password_confirmation" 
                                           required 
                                           class="form-input w-full px-4 py-3 pr-10 pl-11 rounded-xl text-[var(--text-dark)] font-medium" 
                                           placeholder="أعد إدخال كلمة المرور">
                                    <button type="button" 
                                            @click="showPasswordConfirm = !showPasswordConfirm" 
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--color-primary)] transition-colors focus:outline-none">
                                        <i x-show="!showPasswordConfirm" class="fas fa-eye text-xs"></i>
                                        <i x-show="showPasswordConfirm" class="fas fa-eye-slash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- موافقة على الشروط -->
                        <div class="flex items-start pt-1">
                            <input type="checkbox" 
                                   id="terms" 
                                   required
class="mt-0.5 h-4 w-4 rounded border-[var(--input-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <label for="terms" class="mr-2 text-sm text-[var(--text-dark)] font-medium leading-tight">
                                أوافق على
                                <a href="#" class="link-primary underline">شروط الاستخدام</a>
                                و
                                <a href="#" class="link-primary underline">سياسة الخصوصية</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="btn-register w-full py-3 rounded-xl text-white font-black text-base shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 mt-4">
                            <i class="fas fa-user-plus text-lg"></i>
                            <span>إنشاء الحساب</span>
                        </button>

                        <!-- Login Link -->
                        <div class="text-center pt-6 mt-6 border-t border-[var(--input-border)]">
                            <p class="text-sm text-[var(--text-muted)] mb-2">لديك حساب بالفعل؟</p>
                            <a href="{{ route('login') }}" class="link-primary inline-flex items-center gap-2 text-sm">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>سجّل الدخول</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
