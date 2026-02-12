<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>تسجيل الدخول - Mindlytics</title>

    
    <link rel="preload" href="<?php echo e($authBackgroundUrl ?? asset('images/brainstorm-meeting.jpg')); ?>" as="image">

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

        .login-nav-mobile-only {
            display: none;
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

        .login-wrapper {
            height: 100vh;
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        /* RTL: القسم الأيمن = النموذج الأبيض، الأيسر = الخلفية الملونة */
        .login-container {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: stretch;
            position: relative;
            flex-direction: row-reverse;
        }

        /* لوحة النموذج - بيضاء نظيفة */
        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 56px;
            background: #ffffff;
            position: relative;
            height: 100%;
            overflow-y: auto;
            z-index: 1;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.06);
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .login-form-wrapper h2 {
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .login-page-title {
            user-select: none;
            caret-color: transparent;
        }

        .btn-google:hover {
            border-color: #dadce0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* قسم خلفية الترحيب - صورة brainstorm-meeting */
        .login-visual-section {
            flex: 1.1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: url('<?php echo e($authBackgroundUrl ?? asset("images/brainstorm-meeting.jpg")); ?>') center center / cover no-repeat;
            position: relative;
            overflow: hidden;
            height: 100%;
            z-index: 1;
        }

        .login-visual-section::before {
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
            max-width: 480px;
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
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
            backdrop-filter: blur(8px);
        }

        .shape-1 {
            width: 280px;
            height: 280px;
            top: -80px;
            left: -80px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 180px;
            height: 180px;
            bottom: -40px;
            right: -40px;
            animation-delay: 5s;
        }

        .shape-3 {
            width: 120px;
            height: 120px;
            top: 45%;
            right: 15%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(20px, -20px) rotate(120deg); }
            66% { transform: translate(-15px, 15px) rotate(240deg); }
        }

        /* حقول الإدخال - رمادي فاتح، زوايا دائرية */
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

        /* زر تسجيل الدخول - ألوان المنصة */
        .btn-login {
            background: var(--color-primary);
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: var(--color-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        /* أزرار تسجيل الدخول الاجتماعي */
        .btn-social {
            background: #fff;
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 700;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .btn-social:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .link-primary {
            color: var(--color-primary);
            font-weight: 700;
            text-decoration: underline;
        }

        .link-primary:hover {
            color: var(--color-primary-hover);
        }

        @media (max-width: 1024px) {
            body { overflow-y: auto; }
            .login-wrapper { height: auto; min-height: 100vh; }
            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }
            .login-form-section {
                padding: 40px 24px;
                box-shadow: none;
            }
            .login-visual-section {
                padding: 40px 24px;
                height: auto;
                min-height: 280px;
            }
            .login-form-wrapper { max-width: 100%; }
            .visual-content { max-width: 100%; }
        }

        /* ─── هواتف: ترحيب أصغر وصفحة منظمة ─── */
        @media (max-width: 640px) {
            .login-wrapper {
                padding-top: 0;
            }

            .login-visual-section {
                padding: 1.25rem 1rem 1rem;
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
                padding: 1.25rem 1rem 1.5rem;
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

            /* رسالة الترحيب: أصغر ومنظمة على الهاتف */
            .visual-title {
                font-size: 1rem !important;
                line-height: 1.35 !important;
                margin-bottom: 0.25rem !important;
            }

            .visual-desc {
                font-size: 0.7rem !important;
                margin-bottom: 0.4rem !important;
                line-height: 1.35 !important;
            }

            .visual-badges {
                gap: 0.35rem !important;
                margin-top: 0.4rem !important;
            }

            .visual-badges > div {
                padding: 0.3rem 0.45rem !important;
            }

            .visual-badges span {
                font-size: 0.6rem !important;
            }

            .visual-badges i {
                font-size: 0.6rem !important;
            }

            .login-form-wrapper .text-center.mb-8 {
                margin-bottom: 1.5rem !important;
            }

            .login-form-wrapper .text-center .mb-4 {
                margin-bottom: 0.75rem !important;
            }

            .login-form-wrapper h2 {
                font-size: 1.5rem !important;
                margin-bottom: 0.5rem !important;
            }

            .login-form-wrapper .text-center p {
                font-size: 0.9rem !important;
            }

            .login-form-wrapper .mb-8 {
                margin-bottom: 1.5rem !important;
            }

            .form-input {
                padding: 0.75rem 1rem !important;
                font-size: 1rem !important;
            }

            .btn-login {
                padding: 0.875rem 1.25rem !important;
                font-size: 1rem !important;
            }

            .shape-1 {
                width: 100px;
                height: 100px;
            }

            .shape-2 {
                width: 80px;
                height: 80px;
            }

            .shape-3 {
                width: 60px;
                height: 60px;
            }

            form.space-y-5 > * + * {
                margin-top: 1rem !important;
            }

            form .border-t-2.pt-6 {
                padding-top: 1.25rem !important;
                margin-top: 1.25rem !important;
            }
        }

        @media (max-width: 480px) {
            .login-visual-section {
                padding: 1rem 0.75rem 0.75rem;
            }

            .login-form-section {
                padding: 1rem 0.75rem 1.25rem;
            }

            .visual-title {
                font-size: 1rem !important;
            }

            .visual-desc {
                font-size: 0.75rem !important;
            }

            .visual-badges > div {
                padding: 0.35rem 0.5rem !important;
            }

            .visual-badges span,
            .visual-badges i {
                font-size: 0.65rem !important;
            }

            .login-form-wrapper h2 {
                font-size: 1.35rem !important;
            }

            .login-form-wrapper .text-center p {
                font-size: 0.875rem !important;
            }

            .form-input {
                padding: 0.75rem 1rem !important;
                font-size: 1rem !important;
            }

            .btn-login {
                padding: 0.875rem 1.25rem !important;
                font-size: 1rem !important;
            }
        }

        /* ─── تصميم الهاتف: نافبار موحد + بطاقة ترحيب + نموذج بعرض كامل ─── */
        .login-mobile-wrap {
            display: none;
            min-height: 100vh;
            background: #f9fafb;
            padding-bottom: 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .login-nav-mobile-only {
                display: block !important;
            }

            .login-wrapper {
                padding-top: 5rem;
                height: auto;
                min-height: 100vh;
                width: 100%;
            }

            .login-container {
                display: none !important;
            }

            .login-mobile-wrap {
                display: block;
                width: 100%;
                padding: 0 0.75rem 2rem;
                box-sizing: border-box;
            }

            .login-mobile-welcome {
                margin: 0.5rem 0 0;
                width: 100%;
                box-sizing: border-box;
            }

            .login-mobile-form-wrap {
                width: 100%;
                padding: 1rem 0.75rem 0;
                box-sizing: border-box;
            }

            .login-mobile-form-wrap .section-title,
            .login-mobile-form-wrap .section-subtitle {
                text-align: center;
            }

            .login-mobile-form-card {
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

        @media (max-width: 480px) {
            .login-mobile-wrap {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .login-mobile-form-wrap {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .login-mobile-form-card {
                padding: 1.25rem 1rem;
            }
        }

        /* النافبار الموحد: المسافة من .login-wrapper كافية */

        /* بطاقة الترحيب - صورة brainstorm-meeting */
        .login-mobile-welcome {
            margin: 0.5rem 0 0;
            padding: 2rem 1rem 2rem;
            min-height: 9rem;
            background: url('<?php echo e($authBackgroundUrl ?? asset("images/brainstorm-meeting.jpg")); ?>') center center / cover no-repeat;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }

        .login-mobile-welcome::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.8) 0%, rgba(37, 99, 235, 0.75) 100%);
            z-index: 0;
        }

        .login-mobile-welcome::after {
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

        .login-mobile-welcome .welcome-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 0.5rem 0;
            position: relative;
            z-index: 1;
        }

        .login-mobile-welcome .welcome-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.95);
            line-height: 1.5;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .login-mobile-form-wrap {
            padding: 1.5rem 0.75rem 0;
            width: 100%;
            max-width: 100%;
        }

        .login-mobile-form-wrap .section-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            text-align: center;
            margin: 0 0 0.35rem 0;
        }

        .login-mobile-form-wrap .section-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            margin: 0 0 1.25rem 0;
        }

        .login-mobile-form-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.5rem 1.25rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .login-mobile-form-card .form-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .login-mobile-form-card .form-input:focus {
            background: #fff;
            border-color: var(--color-primary);
            outline: none;
            box-shadow: 0 0 0 3px var(--color-primary-light);
        }

        .login-mobile-form-card .form-input.pl-12 {
            padding-left: 2.75rem;
        }

        .login-mobile-form-card label {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
        }

        .login-mobile-form-card .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .login-mobile-form-card .input-wrap .input-icon {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .login-mobile-form-card .btn-login {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            border-radius: 12px;
        }
    </style>
</head>
<body x-data="{ showPassword: false }">
    <div class="login-nav-mobile-only">
        <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- Login Wrapper -->
    <div class="login-wrapper">
        <!-- تصميم الهاتف فقط -->
        <div class="login-mobile-wrap">
            <!-- بطاقة الترحيب -->
            <div class="login-mobile-welcome">
                <h1 class="welcome-title">مرحباً بعودتك</h1>
                <p class="welcome-desc">سجّل دخولك لمتابعة دوراتك وإنجازاتك</p>
            </div>

            <!-- تسجيل الدخول: عنوان + بطاقة بيضاء -->
            <div class="login-mobile-form-wrap">
                <h2 class="section-title">تسجيل الدخول</h2>
                <p class="section-subtitle">أدخل بياناتك للوصول إلى حسابك</p>

                <div class="login-mobile-form-card">
                    <form action="<?php echo e(route('login')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="input-wrap">
                            <label for="email_mobile">البريد الإلكتروني</label>
                            <div class="relative">
                                <i class="input-icon fas fa-envelope"></i>
                                <input type="email" name="email" id="email_mobile" value="<?php echo e(old('email')); ?>" required autocomplete="email"
                                       class="form-input w-full" placeholder="example@email.com" dir="ltr">
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div style="display:none;" aria-hidden="true">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="input-wrap">
                            <label for="password_mobile">كلمة المرور</label>
                            <div class="relative">
                                <i class="input-icon fas fa-lock"></i>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password_mobile" required
                                       class="form-input w-full pl-12" placeholder=".........">
                                <button type="button" @click="showPassword = !showPassword" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                                    <i x-show="showPassword" class="fas fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-3 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                <span class="text-[var(--text-muted)]">تذكرني</span>
                            </label>
                            <a href="#" class="link-primary text-sm">نسيت كلمة المرور؟</a>
                        </div>
                        <button type="submit" class="btn-login w-full py-3 rounded-xl text-white font-bold text-base flex items-center justify-center gap-2">
                            <span>تسجيل الدخول</span>
                        </button>
                        <div class="relative my-4">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[var(--input-border)]"></div></div>
                            <div class="relative flex justify-center text-sm"><span class="bg-white px-3 text-[var(--text-muted)]">أو</span></div>
                        </div>
                        <a href="<?php echo e(route('auth.google.redirect')); ?>" class="btn-social btn-google w-full flex items-center justify-center gap-3 py-3 rounded-xl" aria-label="تسجيل الدخول أو إنشاء حساب بـ Google">
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            <span>متابعة بـ Google</span>
                        </a>
                        <div class="text-center pt-4 mt-4 border-t border-[var(--input-border)]">
                            <p class="text-sm text-[var(--text-muted)]">ليس لديك حساب؟ <a href="<?php echo e(route('register')); ?>" class="link-primary font-bold">سجّل الآن</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- تصميم الديسكتوب/تابلت -->
        <div class="login-container">
            <!-- Right Section: Visual Content -->
            <div class="login-visual-section">
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

            <!-- القسم الأيمن في RTL: لوحة النموذج البيضاء -->
            <div class="login-form-section">
                <div class="login-form-wrapper">
                    <h2 class="login-page-title text-2xl md:text-3xl font-black text-[var(--text-dark)] text-center mb-8">
                        تسجيل الدخول إلى <span class="text-[var(--color-primary)]">Mindlytics</span>
                    </h2>

                    <form action="<?php echo e(route('login')); ?>" method="POST" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        
                        <div>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="<?php echo e(old('email')); ?>"
                                   required 
                                   autocomplete="email"
                                   class="form-input w-full px-4 py-3.5 rounded-xl text-[var(--text-dark)] font-medium <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="البريد الإلكتروني" 
                                   dir="ltr"
                                   autofocus>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div style="display: none;" aria-hidden="true">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   class="form-input w-full px-4 py-3.5 pr-12 pl-4 rounded-xl text-[var(--text-dark)] font-medium <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="كلمة المرور">
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--color-primary)] transition-colors focus:outline-none">
                                <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                                <i x-show="showPassword" class="fas fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" id="remember" 
                                       class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                <span class="text-[var(--text-muted)] font-medium">تذكرني</span>
                            </label>
                            <a href="#" class="link-primary text-sm">نسيت كلمة المرور؟</a>
                        </div>

                        <button type="submit" 
                                class="btn-login w-full py-3.5 rounded-xl text-white font-bold text-base flex items-center justify-center gap-2">
                            تسجيل الدخول
                        </button>
                    </form>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[var(--input-border)]"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-3 text-[var(--text-muted)]">أو سجّل الدخول باستخدام</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="<?php echo e(route('auth.google.redirect')); ?>" class="btn-social btn-google w-full flex items-center justify-center gap-3" aria-label="تسجيل الدخول أو إنشاء حساب طالب بـ Google">
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            <span>متابعة بـ Google</span>
                        </a>
                    </div>

                    <p class="text-center text-sm text-[var(--text-muted)] mt-8">
                        ليس لديك حساب؟ <a href="<?php echo e(route('register')); ?>" class="link-primary font-bold">سجّل الآن</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/auth/login.blade.php ENDPATH**/ ?>