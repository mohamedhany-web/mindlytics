<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>تسجيل الدخول - Mindlytics</title>

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

        /* Navbar Styles */
        .navbar-gradient {
            background: linear-gradient(to bottom, rgba(240, 249, 255, 0.95), rgba(224, 242, 254, 0.9));
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        /* Login Container */
        .login-wrapper {
            height: 100vh;
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: stretch;
            position: relative;
        }

        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
            position: relative;
            height: 100%;
            overflow-y: auto;
            z-index: 1;
        }

        .login-form-section::before {
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

        .login-form-wrapper {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
        }

        .login-visual-section {
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

        .login-visual-section::before {
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
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4), 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            body {
                overflow-y: auto;
            }

            .login-wrapper {
                height: auto;
                min-height: 100vh;
            }

            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .login-visual-section {
                padding: 50px 30px;
                height: auto;
                min-height: auto;
                width: 100%;
                position: relative;
                margin-bottom: 0;
            }

            .login-visual-section::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 5%;
                right: 5%;
                height: 4px;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 20%, rgba(255, 255, 255, 0.6) 50%, rgba(255, 255, 255, 0.4) 80%, transparent 100%);
                border-radius: 2px;
            }

            .login-form-section {
                padding: 50px 30px;
                height: auto;
                min-height: auto;
                width: 100%;
                position: relative;
                margin-top: 0;
            }

            .login-form-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 5%;
                right: 5%;
                height: 4px;
                background: linear-gradient(90deg, transparent 0%, rgba(44, 169, 189, 0.3) 20%, rgba(44, 169, 189, 0.5) 50%, rgba(44, 169, 189, 0.3) 80%, transparent 100%);
                border-radius: 2px;
            }

            .login-form-section::before {
                display: none;
            }

            .login-form-wrapper {
                max-width: 100%;
            }

            .visual-content {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .login-wrapper {
                padding-top: 0;
            }

            .login-visual-section {
                padding: 40px 20px;
                min-height: auto;
                width: 100%;
                display: block;
                margin-bottom: 0;
            }

            .login-visual-section::after {
                height: 3px;
                left: 10%;
                right: 10%;
            }

            .login-form-section {
                padding: 40px 20px;
                min-height: auto;
                width: 100%;
                display: block;
                margin-top: 0;
            }

            .login-form-section::before {
                height: 3px;
                left: 10%;
                right: 10%;
            }

            .login-form-wrapper {
                max-width: 100%;
                width: 100%;
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

            .visual-content .mb-8 {
                margin-bottom: 1.5rem !important;
            }

            .login-form-wrapper .mb-8 {
                margin-bottom: 1.5rem !important;
            }

            .login-form-wrapper h2 {
                font-size: 1.75rem !important;
                margin-bottom: 0.5rem !important;
            }

            .login-form-wrapper p {
                font-size: 0.875rem !important;
            }

            .form-input {
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
            }

            .btn-login {
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

            form.space-y-5 > * + * {
                margin-top: 1rem !important;
            }
        }

        @media (max-width: 480px) {
            .login-visual-section {
                padding: 35px 18px;
                width: 100%;
            }

            .login-form-section {
                padding: 35px 18px;
                width: 100%;
            }

            .visual-content h1 {
                font-size: 1.5rem !important;
            }

            .login-form-wrapper h2 {
                font-size: 1.5rem !important;
            }

            .visual-content .flex {
                flex-direction: column;
                gap: 0.75rem !important;
            }

            .visual-content .flex > div {
                width: 100%;
            }
        }
    </style>
</head>
<body x-data="{ showPassword: false }">
    <!-- Login Wrapper -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Right Section: Visual Content -->
            <div class="login-visual-section">
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
                                <img src="<?php echo e(asset('logo-removebg-preview.png')); ?>" alt="Mindlytics Logo" class="w-full h-full object-contain rounded-2xl">
                            </div>
                        </div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black mb-4 md:mb-6 leading-tight text-white drop-shadow-lg">
                        مرحباً بك في<br>
                        <span class="text-blue-200">Mindlytics</span>
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg lg:text-xl text-white/90 mb-6 md:mb-10 leading-relaxed font-bold px-2 drop-shadow-md">
                        منصة متكاملة لإدارة وتطوير قدراتك الذهنية والتعليمية
                    </p>
                    <div class="flex flex-wrap justify-center gap-3 md:gap-4 px-2">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-check-circle text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">سهولة الاستخدام</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-shield-alt text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">أمان عالي</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-filter backdrop-blur-md px-3 py-2 md:px-5 md:py-3 rounded-lg md:rounded-xl border-2 border-white/30 shadow-xl hover:bg-white/20 transition-all">
                            <i class="fas fa-headset text-blue-200 text-sm md:text-base"></i>
                            <span class="font-black text-xs md:text-sm text-white">دعم فني متواصل</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Section: Login Form -->
            <div class="login-form-section">
                <div class="login-form-wrapper">
                    <!-- Header -->
                    <div class="text-center mb-8 md:mb-12">
                        <div class="relative inline-flex items-center justify-center mb-4 md:mb-6">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl blur-md opacity-40"></div>
                            <div class="relative inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-600 rounded-xl shadow-lg">
                                <i class="fas fa-sign-in-alt text-white text-xl md:text-2xl"></i>
                            </div>
                        </div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-blue-900 mb-2 md:mb-3">مرحباً بك</h2>
                        <p class="text-blue-700 text-sm md:text-base font-semibold">سجل دخولك للوصول إلى حسابك</p>
                    </div>

                    <!-- Login Form -->
                    <form action="<?php echo e(route('login')); ?>" method="POST" class="space-y-5 md:space-y-6">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-black text-blue-900 mb-2.5">
                                البريد الإلكتروني <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="<?php echo e(old('email')); ?>"
                                       required 
                                       autocomplete="email"
                                       class="form-input w-full px-4 py-3.5 pr-10 rounded-xl text-gray-900 font-medium <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="example@email.com" 
                                       dir="ltr"
                                       autofocus>
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs text-red-600 font-medium">
                                    <?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <!-- Honeypot Field (حماية من البوتات) -->
                        <div style="display: none;" aria-hidden="true">
                            <label for="website">الموقع الإلكتروني</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-black text-blue-900 mb-2.5">
                                كلمة المرور
                            </label>
                            <div class="relative">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-lock text-sm"></i>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       class="form-input w-full px-4 py-3.5 pr-10 pl-12 rounded-xl text-gray-900 font-medium <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="أدخل كلمة المرور">
                                <button type="button" 
                                        @click="showPassword = !showPassword" 
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                                    <i x-show="showPassword" class="fas fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs text-red-600 font-medium">
                                    <?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Remember & Forgot Password -->
                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-600 focus:ring-2">
                                <span class="mr-2 text-gray-600 font-medium">تذكرني</span>
                            </label>
                            <a href="#" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">
                                نسيت كلمة المرور؟
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="btn-login w-full py-3.5 md:py-4 rounded-xl text-white font-black text-base md:text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 mt-6 md:mt-10">
                            <i class="fas fa-sign-in-alt text-lg md:text-xl"></i>
                            <span>تسجيل الدخول</span>
                        </button>

                        <!-- Register Link -->
                        <div class="text-center pt-6 md:pt-8 mt-6 md:mt-8 border-t-2 border-gray-200">
                            <p class="text-sm md:text-base text-gray-600 mb-2 md:mb-3 font-semibold">
                                ليس لديك حساب؟
                            </p>
                            <a href="<?php echo e(route('register')); ?>" 
                               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-black transition-colors text-sm md:text-base">
                                <i class="fas fa-user-plus text-base md:text-lg"></i>
                                <span>سجل الآن</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/auth/login.blade.php ENDPATH**/ ?>