<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>إتمام الطلب - {{ isset($course) ? $course->title : ($learningPath->name ?? 'الطلب') }} - Mindlytics</title>

    <!-- خط عربي -->
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
            overflow-x: hidden;
            background: #f8fafc;
            width: 100%;
            max-width: 100vw;
            position: relative;
            padding-top: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        body > * {
            flex-shrink: 0;
        }
        
        main {
            flex: 1 0 auto;
        }

        html {
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        * {
            box-sizing: border-box;
        }

        /* Enhanced Navbar Styles - Same as other pages */
        #navbar.navbar-gradient,
        .navbar-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1), 0 0 40px rgba(59, 130, 246, 0.2) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000 !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            width: 100%;
        }

        .navbar-gradient::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), rgba(16, 185, 129, 0.6), rgba(255, 255, 255, 0.6), transparent);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .navbar-gradient::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Mobile Menu Styles */
        @media (max-width: 1023px) {
            body.overflow-hidden {
                overflow: hidden !important;
                position: fixed !important;
                width: 100% !important;
            }
            
            .mobile-menu-overlay {
                position: fixed !important;
                inset: 0 !important;
                z-index: 9999 !important;
            }
            
            .mobile-menu-sidebar {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100vh !important;
                height: 100dvh !important;
                z-index: 10000 !important;
            }
        }

        /* Nav Link Styles */
        .nav-link {
            position: relative;
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link:hover::before {
            opacity: 1;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #ecfdf5 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            pointer-events: none;
            animation: pulseGradient 5s ease-in-out infinite;
        }

        @keyframes pulseGradient {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.9;
                transform: scale(1.1);
            }
        }

        /* Animated Background Elements */
        .animated-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        /* Floating Circles */
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25), rgba(59, 130, 246, 0.08), transparent);
            filter: blur(40px);
            animation: floatCircle 20s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            bottom: 15%;
            left: 15%;
            animation-delay: 4s;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25), rgba(16, 185, 129, 0.08), transparent);
        }

        .circle-3 {
            width: 350px;
            height: 350px;
            top: 50%;
            left: 50%;
            animation-delay: 8s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.06), transparent);
        }

        @keyframes floatCircle {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.6;
            }
            33% {
                transform: translate(50px, -50px) scale(1.2);
                opacity: 0.8;
            }
            66% {
                transform: translate(-30px, 30px) scale(0.9);
                opacity: 0.5;
            }
        }

        /* Floating Code Symbols */
        .floating-code-symbol {
            position: absolute;
            color: rgba(59, 130, 246, 0.08);
            font-size: 1.2rem;
            font-weight: 700;
            animation: floatCode 15s ease-in-out infinite;
            will-change: transform, opacity;
            z-index: 0;
        }
        
        /* Ensure navbar is above everything */
        nav#navbar,
        nav.navbar-gradient {
            z-index: 1000 !important;
        }
        
        /* Hero section should be below navbar */
        .hero-section {
            z-index: 1;
            position: relative;
        }

        .code-symbol-1 { top: 15%; left: 10%; animation-delay: 0s; }
        .code-symbol-2 { top: 35%; right: 15%; animation-delay: 2s; }
        .code-symbol-3 { bottom: 25%; left: 20%; animation-delay: 4s; }
        .code-symbol-4 { top: 60%; right: 30%; animation-delay: 6s; }

        @keyframes floatCode {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0.08;
            }
            50% {
                transform: translate(30px, -30px) rotate(180deg);
                opacity: 0.12;
            }
        }

        /* Floating Particles */
        .floating-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            animation: floatParticle 12s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .particle-1 { top: 20%; left: 15%; animation-delay: 0s; }
        .particle-2 { top: 50%; right: 20%; animation-delay: 2s; background: rgba(16, 185, 129, 0.3); }
        .particle-3 { bottom: 30%; left: 25%; animation-delay: 4s; }

        @keyframes floatParticle {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translate(50px, -50px) scale(2);
                opacity: 1;
            }
        }

        /* Hero Glow */
        .hero-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15), rgba(16, 185, 129, 0.1), transparent);
            border-radius: 50%;
            filter: blur(80px);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulseGlow 4s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                opacity: 0.8;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        /* Fade in animations */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Course Card Styles */
        .course-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false }"
      :class="{ 'overflow-hidden': mobileMenu }">

    @include('components.unified-navbar')
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section relative overflow-hidden py-12 lg:py-16">
            <!-- Animated Background -->
            <div class="animated-background absolute inset-0 overflow-hidden">
                <div class="floating-circle circle-1"></div>
                <div class="floating-circle circle-2"></div>
                <div class="floating-circle circle-3"></div>
                
                <div class="floating-code-symbol code-symbol-1">&lt;/&gt;</div>
                <div class="floating-code-symbol code-symbol-2">{ }</div>
                <div class="floating-code-symbol code-symbol-3">( )</div>
                <div class="floating-code-symbol code-symbol-4">[ ]</div>
                
                <div class="floating-particle particle-1"></div>
                <div class="floating-particle particle-2"></div>
                <div class="floating-particle particle-3"></div>
            </div>
            
            <!-- Hero Glow -->
            <div class="hero-glow"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-6 text-gray-600 text-sm flex items-center fade-in-up">
                    <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">الرئيسية</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="{{ route('public.courses') }}" class="hover:text-blue-600 transition-colors">الكورسات</a>
                    <span class="mx-2 text-gray-400">/</span>
                    @if(isset($course))
                        <a href="{{ route('public.course.show', $course->id) }}" class="hover:text-blue-600 transition-colors">{{ Str::limit($course->title ?? 'الكورس', 30) }}</a>
                    @elseif(isset($learningPath))
                        <a href="{{ route('public.learning-path.show', Str::slug($learningPath->name)) }}" class="hover:text-blue-600 transition-colors">{{ Str::limit($learningPath->name ?? 'المسار', 30) }}</a>
                    @endif
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-gray-900 font-medium">إتمام الطلب</span>
                </nav>

                <div class="text-center mb-8 fade-in-up" style="animation-delay: 0.1s;">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                        إتمام الطلب
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600">
                        خطوة أخيرة للحصول على {{ isset($course) ? 'الكورس' : 'المسار التعليمي' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Checkout Form Section -->
        <section class="py-8 md:py-12 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Course Summary Card -->
                    <div class="lg:col-span-1">
                        <div class="course-card p-6 sticky top-24 fade-in-up" style="animation-delay: 0.2s;">
                            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                                <i class="fas fa-shopping-bag text-blue-600"></i>
                                ملخص الطلب
                            </h3>
                            
                            <!-- Course/Learning Path Info -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        @if(isset($course))
                                            <i class="fas fa-code text-white text-xl"></i>
                                        @else
                                            <i class="fas fa-route text-white text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 text-base mb-1 line-clamp-2">
                                            @if(isset($course))
                                                {{ $course->title }}
                                            @else
                                                {{ $learningPath->name }}
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            @if(isset($course))
                                                {{ $course->academicSubject->name ?? 'غير محدد' }}
                                            @else
                                                مسار تعليمي شامل
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">السعر:</span>
                                    <span class="text-xl font-black text-blue-600">
                                        {{ number_format((isset($course) ? $course->price : $learningPath->price) ?? 0, 2) }} 
                                        <span class="text-sm text-gray-600">ج.م</span>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t-2 border-gray-300">
                                    <span class="text-gray-900 font-bold text-lg">الإجمالي:</span>
                                    <span class="text-2xl font-black text-green-600">
                                        {{ number_format((isset($course) ? $course->price : $learningPath->price) ?? 0, 2) }} 
                                        <span class="text-base text-gray-600">ج.م</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Course/Learning Path Features -->
                            <div class="space-y-3">
                                <h4 class="text-sm font-bold text-gray-900 mb-3">مميزات {{ isset($course) ? 'الكورس' : 'المسار' }}:</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>وصول مدى الحياة</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>شهادة إتمام معتمدة</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>دعم فني مباشر</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>مشاريع عملية</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- طرق الدفع المتاحة (فيزا، محفظة، تقسيط عبر كاشير) -->
                    <div class="lg:col-span-2">
                        <div class="course-card p-6 md:p-8 fade-in-up" style="animation-delay: 0.3s;">
                            <h2 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                                <i class="fas fa-credit-card text-blue-600"></i>
                                طرق الدفع المتاحة
                            </h2>
                            
                            @if(session('error'))
                                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl fade-in-up">
                                    <p class="text-red-700 text-sm flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ session('error') }}
                                    </p>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl fade-in-up">
                                    <ul class="list-disc list-inside space-y-1 text-red-700 text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl fade-in-up">
                                    <p class="text-green-700 text-sm flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        {{ session('success') }}
                                    </p>
                                </div>
                            @endif

                            <!-- طرق الدفع المسموحة من كاشير -->
                            <div class="mb-6 p-5 bg-slate-50 rounded-xl border-2 border-slate-200">
                                <p class="text-sm font-bold text-gray-900 mb-4">يمكنك الدفع بإحدى الطرق التالية:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-slate-200">
                                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                                            <i class="fas fa-credit-card text-2xl text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">البطاقات</p>
                                            <p class="text-xs text-gray-600">فيزا، ماستركارد، ميزة</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-slate-200">
                                        <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                                            <i class="fas fa-wallet text-2xl text-emerald-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">المحفظة الإلكترونية</p>
                                            <p class="text-xs text-gray-600">فودافون كاش وغيرها</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-slate-200">
                                        <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-2xl text-amber-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">التقسيط</p>
                                            <p class="text-xs text-gray-600">تقسيط عبر البنوك</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-4 text-xs text-gray-500">
                                    <i class="fas fa-info-circle text-blue-500 ml-1"></i>
                                    عند الضغط على «متابعة للدفع» ستُنقل لصفحة دفع آمنة لاختيار طريقة الدفع وإتمام العملية.
                                </p>
                            </div>

                            {{-- نموذج الدفع أونلاين --}}
                            <form
                                x-data="checkoutKashierHandler('{{ isset($course) ? 'course' : 'path' }}', '{{ isset($course) ? route('public.course.checkout.kashier', $course->id) : route('public.learning-path.checkout.kashier', Str::slug($learningPath->name)) }}')"
                                @submit.prevent="startPayment"
                                @if(!isset($course)) x-init="startPayment()" @endif
                            >
                                @csrf
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button type="submit" 
                                            :disabled="isSubmitting"
                                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                        <i class="fas fa-lock" x-show="!isSubmitting"></i>
                                        <i class="fas fa-spinner fa-spin" x-show="isSubmitting" x-cloak></i>
                                        <span x-text="isSubmitting ? 'جاري فتح صفحة الدفع...' : 'متابعة للدفع'"></span>
                                    </button>
                                    <a href="{{ isset($course) ? route('public.course.show', $course->id) : route('public.learning-path.show', Str::slug($learningPath->name)) }}" 
                                       :class="{ 'pointer-events-none opacity-50': isSubmitting }"
                                       class="inline-flex items-center justify-center gap-2 bg-white text-gray-700 px-6 py-4 rounded-full font-bold text-lg border-2 border-gray-300 hover:bg-gray-50 transition-all duration-300">
                                        <i class="fas fa-arrow-right"></i>
                                        <span>إلغاء</span>
                                    </a>
                                </div>
                                <!-- رسالة خطأ أسفل الأزرار عند فشل بدء الدفع -->
                                <p x-show="error" x-text="error" class="mt-3 text-xs text-red-600 text-center" x-cloak></p>
                                <p class="mt-4 text-xs text-gray-500 text-center">
                                    <i class="fas fa-shield-alt ml-1"></i>
                                    تفعيل فوري بعد إتمام الدفع بنجاح
                                </p>
                            </form>

                            {{-- في حالة مسار تعليمي: عرض الـ iframe مباشرة داخل الصفحة --}}
                            @if(!isset($course))
                                <div class="mt-6">
                                    <template x-if="!error">
                                        <div>
                                            <div x-show="!sessionUrl" class="flex items-center justify-center h-40">
                                                <div class="flex flex-col items-center gap-3 text-gray-500">
                                                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                                                    <p class="text-sm">جاري تجهيز صفحة الدفع الآمنة...</p>
                                                </div>
                                            </div>
                                            <iframe x-show="sessionUrl" :src="sessionUrl" class="w-full h-[520px] border-0 rounded-2xl shadow-inner" allow="payment *; fullscreen *"></iframe>
                                        </div>
                                    </template>
                                    <template x-if="error">
                                        <div class="p-4 mt-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl">
                                            <p x-text="error"></p>
                                        </div>
                                    </template>
                                </div>
                            @endif

                            <!-- مودال الدفع (iframe) -->
                            <div x-show="showModal && kind === 'course'" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
                                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                                        <h3 class="text-lg font-bold text-gray-900">
                                            إتمام الدفع الآمن
                                        </h3>
                                        <button type="button" @click="closeModal" class="p-2 rounded-xl text-gray-500 hover:bg-gray-200 hover:text-gray-800 transition-colors">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="flex-1 min-h-0">
                                        <template x-if="error">
                                            <div class="p-4 text-sm text-red-600">
                                                <p x-text="error"></p>
                                            </div>
                                        </template>
                                        <template x-if="!error">
                                            <div class="w-full h-full">
                                                <iframe x-show="sessionUrl" :src="sessionUrl" class="w-full h-[70vh] border-0" allow="payment *; fullscreen *"></iframe>
                                                <div x-show="!sessionUrl" class="flex items-center justify-center h-[70vh]">
                                                    <div class="flex flex-col items-center gap-3 text-gray-500">
                                                        <i class="fas fa-spinner fa-spin text-2xl"></i>
                                                        <p class="text-sm">جاري تحميل صفحة الدفع...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.unified-footer')

    <script>
        function checkoutKashierHandler(type, endpoint) {
            return {
                kind: type,
                isSubmitting: false,
                showModal: false,
                sessionUrl: '',
                error: '',
                async startPayment() {
                    this.isSubmitting = true;
                    this.error = '';
                    this.sessionUrl = '';
                    try {
                        const csrfMeta = document.querySelector('meta[name=\"csrf-token\"]');
                        const token = csrfMeta ? csrfMeta.getAttribute('content') : (document.querySelector('input[name=\"_token\"]')?.value || '');
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ source: type }),
                        });
                        const text = await response.text();
                        let data = {};
                        try {
                            data = text ? JSON.parse(text) : {};
                        } catch (e) {
                            console.error('Failed to parse payment response JSON', e, text);
                        }
                        if (!response.ok) {
                            throw new Error(data.message || 'فشل إنشاء جلسة الدفع. حاول مرة أخرى.');
                        }
                        if (!data.session_url) {
                            throw new Error('لم يتم استلام رابط جلسة الدفع من بوابة الدفع.');
                        }
                        this.sessionUrl = data.session_url;
                        this.showModal = true;
                    } catch (e) {
                        this.error = e.message || 'حدث خطأ أثناء الاتصال ببوابة الدفع.';
                        this.showModal = true;
                        console.error('Payment start error', e);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                closeModal() {
                    this.showModal = false;
                    this.sessionUrl = '';
                    this.error = '';
                },
            };
        }

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
