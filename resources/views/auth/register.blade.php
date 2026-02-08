<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء حساب - Mindlytics</title>

    <!-- خط عربي أصيل -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Cairo', 'Noto Sans Arabic', sans-serif;
        }

        body {
            overflow: hidden;
            background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Register Container */
        .register-wrapper {
            height: 100vh;
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        .register-container {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: stretch;
            position: relative;
        }

        .register-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 25px 40px;
            background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
            position: relative;
            height: 100%;
            overflow-y: auto;
            z-index: 1;
        }

        .register-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .register-form-wrapper {
            width: 100%;
            max-width: 750px;
            position: relative;
            z-index: 1;
        }

        .register-visual-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #1e3a8a 100%);
            position: relative;
            overflow: hidden;
            height: 100%;
            z-index: 1;
        }

        .register-visual-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
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

        /* Input Styles */
        .form-input {
            background: linear-gradient(to bottom, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2), 0 4px 12px rgba(59, 130, 246, 0.1);
            outline: none;
            background: linear-gradient(to bottom, #ffffff 0%, #f0f9ff 100%);
            transform: translateY(-1px);
        }

        .form-input:hover {
            border-color: #7dd3fc;
            background: linear-gradient(to bottom, #ffffff 0%, #f0f9ff 100%);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        /* Button */
        .btn-register {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4), 0 4px 12px rgba(37, 99, 235, 0.3);
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
                padding: 50px 30px;
                height: auto;
                min-height: auto;
                width: 100%;
                position: relative;
                margin-bottom: 0;
            }

            .register-visual-section::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 5%;
                right: 5%;
                height: 4px;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 20%, rgba(255, 255, 255, 0.6) 50%, rgba(255, 255, 255, 0.4) 80%, transparent 100%);
                border-radius: 2px;
            }

            .register-form-section {
                padding: 50px 30px;
                height: auto;
                min-height: auto;
                width: 100%;
                position: relative;
                margin-top: 0;
            }

            .register-form-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 5%;
                right: 5%;
                height: 4px;
                background: linear-gradient(90deg, transparent 0%, rgba(44, 169, 189, 0.3) 20%, rgba(44, 169, 189, 0.5) 50%, rgba(44, 169, 189, 0.3) 80%, transparent 100%);
                border-radius: 2px;
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
                padding: 40px 20px;
                min-height: auto;
                width: 100%;
                display: block;
            }

            .register-visual-section::after {
                height: 3px;
                left: 10%;
                right: 10%;
            }

            .register-form-section {
                padding: 40px 20px;
                min-height: auto;
                width: 100%;
                display: block;
            }

            .register-form-section::before {
                height: 3px;
                left: 10%;
                right: 10%;
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
    </style>
</head>
<body x-data="{ showPassword: false, showPasswordConfirm: false }">
    <!-- Register Wrapper -->
    <div class="register-wrapper">
        <div class="register-container">
            <!-- Right Section: Visual Content -->
            <div class="register-visual-section">
                <div class="floating-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
                <div class="visual-content">
                    <div class="mb-8 md:mb-10">
                        <div class="relative inline-flex items-center justify-center">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl blur-xl opacity-50"></div>
                            <div class="relative inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-2xl shadow-2xl overflow-hidden bg-white/10 backdrop-blur-md border-2 border-white/20">
                                <img src="{{ asset('logo-removebg-preview.png') }}" alt="Mindlytics Logo" class="w-full h-full object-contain rounded-2xl">
                            </div>
                        </div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black mb-4 md:mb-6 leading-tight text-white drop-shadow-lg">
                        انضم إلينا في<br>
                        <span class="text-blue-200">Mindlytics</span>
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg lg:text-xl text-white/90 mb-6 md:mb-10 leading-relaxed font-bold px-2 drop-shadow-md">
                        ابدأ رحلتك التعليمية معنا وطور مهاراتك البرمجية
                    </p>
                    <div class="flex flex-wrap justify-center gap-3 md:gap-4 px-2">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-graduation-cap text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">تعلم احترافي</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-certificate text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">شهادات معتمدة</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-users text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">مجتمع نشط</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Section: Register Form -->
            <div class="register-form-section">
                <div class="register-form-wrapper">
                    <!-- Header -->
                    <div class="text-center mb-5 md:mb-6">
                        <div class="relative inline-flex items-center justify-center mb-3 md:mb-4">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl blur-md opacity-40"></div>
                            <div class="relative inline-flex items-center justify-center w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-600 rounded-xl shadow-lg">
                                <i class="fas fa-user-plus text-white text-lg md:text-xl"></i>
                            </div>
                        </div>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-blue-900 mb-1 md:mb-2">إنشاء حساب جديد</h2>
                        <p class="text-blue-700 text-xs md:text-sm font-semibold">انضم إلى منصة التعلم واستكشف عالم المعرفة</p>
                    </div>

                    <!-- Register Form -->
                    <form action="{{ route('register') }}" method="POST" class="space-y-2.5 md:space-y-3">
                        @csrf
                        
                        <!-- Student Notice -->
                        <div class="bg-gradient-to-r from-blue-50/90 to-blue-100/70 border-2 border-blue-200/60 rounded-lg p-3 mb-3 shadow-lg">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas fa-user-graduate text-white text-base"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-blue-900">تسجيل الطلاب</p>
                                    <p class="text-xs text-blue-700 mt-0.5 font-semibold">التسجيل متاح فقط للطلاب</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Grid -->
                        @php
                            $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
                            $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
                        @endphp
                        <div class="form-grid">
                            <!-- الاسم الكامل -->
                            <div>
                                <label for="name" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-user text-blue-700 ml-1 text-xs"></i>
                                    الاسم الكامل
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       required 
                                       class="form-input w-full px-3 py-2.5 rounded-lg text-gray-900 font-medium text-sm @error('name') border-red-500 @enderror" 
                                       placeholder="أدخل اسمك الكامل">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- رقم الهاتف مع كود الدولة -->
                            <div>
                                <label for="phone" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-phone text-blue-700 ml-1 text-xs"></i>
                                    رقم الهاتف
                                </label>
                                <div class="phone-country-row flex rounded-lg overflow-hidden border-2 border-[#bae6fd] bg-gradient-to-b transition-all focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/20 focus-within:shadow-[0_0_0_4px_rgba(59,130,246,0.2)] hover:border-[#7dd3fc] @error('phone') border-red-500 @enderror" style="background: linear-gradient(to bottom, #f0f9ff 0%, #e0f2fe 100%);">
                                    <select name="country_code" 
                                            id="country_code" 
                                            required
                                            class="form-input shrink-0 py-2.5 rounded-l-lg rounded-r-none border-0 border-l-2 border-[#bae6fd] text-gray-900 font-medium text-sm bg-transparent focus:ring-0 focus:border-blue-500"
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
                                           class="form-input flex-1 min-w-0 px-3 py-2.5 rounded-r-lg rounded-l-none border-0 text-gray-900 font-medium text-sm bg-transparent focus:ring-0 focus:border-0 @error('phone') border-red-500 @enderror" 
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
                                <label for="email" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-envelope text-blue-700 ml-1 text-xs"></i>
                                    البريد الإلكتروني (اختياري)
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="form-input w-full px-3 py-2.5 rounded-lg text-gray-900 font-medium text-sm @error('email') border-red-500 @enderror" 
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
                                <label for="referral_code" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-gift text-blue-700 ml-1 text-xs"></i>
                                    كود الإحالة (اختياري)
                                </label>
                                <input type="text" 
                                       name="referral_code" 
                                       id="referral_code" 
                                       value="{{ $referralCode }}"
                                       class="form-input w-full px-3 py-2.5 rounded-lg text-gray-900 font-medium text-sm uppercase" 
                                       placeholder="REF123456"
                                       dir="ltr">
                            </div>

                            <!-- كلمة المرور -->
                            <div>
                                <label for="password" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-lock text-blue-700 ml-1 text-xs"></i>
                                    كلمة المرور
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           id="password" 
                                           required 
                                           class="form-input w-full px-3 py-2.5 pr-9 pl-10 rounded-lg text-gray-900 font-medium text-sm @error('password') border-red-500 @enderror" 
                                           placeholder="أدخل كلمة مرور قوية">
                                    <button type="button" 
                                            @click="showPassword = !showPassword" 
                                            class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-700 transition-colors focus:outline-none">
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
                                <label for="password_confirmation" class="block text-xs font-black text-blue-900 mb-1.5">
                                    <i class="fas fa-lock text-blue-700 ml-1 text-xs"></i>
                                    تأكيد كلمة المرور
                                </label>
                                <div class="relative">
                                    <input :type="showPasswordConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           id="password_confirmation" 
                                           required 
                                           class="form-input w-full px-3 py-2.5 pr-9 pl-10 rounded-lg text-gray-900 font-medium text-sm" 
                                           placeholder="أعد إدخال كلمة المرور">
                                    <button type="button" 
                                            @click="showPasswordConfirm = !showPasswordConfirm" 
                                            class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-700 transition-colors focus:outline-none">
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
                                   class="mt-0.5 h-3.5 w-3.5 text-blue-700 focus:ring-blue-700 border-gray-300 rounded">
                            <label for="terms" class="mr-2 text-xs text-blue-700 font-semibold leading-tight">
                                أوافق على 
                                <a href="#" class="text-blue-700 hover:text-blue-900 underline font-bold">شروط الاستخدام</a>
                                و
                                <a href="#" class="text-blue-700 hover:text-blue-900 underline font-bold">سياسة الخصوصية</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="btn-register w-full py-3 rounded-xl text-white font-black text-base shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 mt-4">
                            <i class="fas fa-user-plus text-lg"></i>
                            <span>إنشاء الحساب</span>
                        </button>

                        <!-- Login Link -->
                        <div class="text-center pt-4 mt-4 border-t-2 border-gray-200">
                            <p class="text-xs text-gray-600 mb-2 font-semibold">
                                لديك حساب بالفعل؟
                            </p>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-900 font-black transition-colors text-sm">
                                <i class="fas fa-sign-in-alt text-base"></i>
                                <span>سجل الدخول</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
