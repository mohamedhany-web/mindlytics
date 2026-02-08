<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>403 - غير مصرح | Mindlytics</title>
    
    <!-- خط عربي أصيل -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
            min-height: 100vh;
        }

        .hero-section {
            background: linear-gradient(to bottom, #fef2f2, #fee2e2, #ffffff);
            position: relative;
            overflow: hidden;
            min-h-screen;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .animated-background {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            animation: floatCircle 20s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .floating-circle.circle-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(251, 113, 133, 0.2));
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .floating-circle.circle-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(251, 113, 133, 0.3), rgba(252, 165, 165, 0.2));
            bottom: 20%;
            left: 15%;
            animation-delay: 5s;
        }

        .floating-circle.circle-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, rgba(252, 165, 165, 0.3), rgba(239, 68, 68, 0.2));
            top: 50%;
            right: 50%;
            animation-delay: 10s;
        }

        @keyframes floatCircle {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.6;
            }
            25% {
                transform: translate(30px, -30px) scale(1.1);
                opacity: 0.8;
            }
            50% {
                transform: translate(-20px, 20px) scale(0.9);
                opacity: 0.7;
            }
            75% {
                transform: translate(20px, 30px) scale(1.05);
                opacity: 0.75;
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <section class="hero-section relative overflow-hidden">
        <div class="animated-background absolute inset-0 overflow-hidden">
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16 lg:py-20">
            <div class="text-center">
                <div class="inline-flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 rounded-full shadow-lg mb-6 fade-in-up">
                    <i class="fas fa-ban text-white text-sm"></i>
                    <span class="text-white font-bold text-sm">غير مصرح</span>
                </div>

                <h1 class="text-8xl md:text-9xl font-black mb-6 leading-tight text-gray-900 fade-in-up" style="animation-delay: 0.1s;">
                    403
                </h1>
                
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4 text-gray-900 fade-in-up" style="animation-delay: 0.2s;">
                    غير مصرح بالوصول
                </h2>
                
                <p class="text-lg md:text-xl text-gray-600 mb-8 leading-relaxed fade-in-up max-w-2xl mx-auto" style="animation-delay: 0.3s;">
                    عذراً، ليس لديك الصلاحيات اللازمة للوصول إلى هذه الصفحة.
                    <br class="hidden md:block">
                    يرجى التواصل مع المسؤول إذا كنت تعتقد أن هذا خطأ.
                </p>

                <div class="bg-white rounded-2xl p-6 md:p-8 mb-8 border border-gray-200 shadow-xl fade-in-up max-w-2xl mx-auto" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                            <i class="fas fa-info-circle text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">معلومات إضافية</h3>
                    </div>
                    <div class="text-gray-700 text-right space-y-3">
                        <div class="flex items-start justify-center gap-3 bg-red-50 rounded-xl p-4">
                            <i class="fas fa-shield-alt text-red-600 mt-0.5"></i>
                            <span class="text-sm">هذه الصفحة محمية وتتطلب صلاحيات خاصة</span>
                        </div>
                        <div class="flex items-start justify-center gap-3 bg-red-50 rounded-xl p-4">
                            <i class="fas fa-user-lock text-red-600 mt-0.5"></i>
                            <span class="text-sm">تأكد من تسجيل الدخول بحساب لديه الصلاحيات المطلوبة</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center fade-in-up" style="animation-delay: 0.5s;">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-pink-500 text-white px-8 py-4 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-home"></i>
                        <span>العودة للوحة التحكم</span>
                    </a>
                    <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 bg-white text-red-600 px-8 py-4 rounded-full font-bold text-base border-2 border-red-600 hover:bg-red-50 transition-all duration-300">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة للصفحة السابقة</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

