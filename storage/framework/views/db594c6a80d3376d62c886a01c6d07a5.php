<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title><?php echo e($learningPath->name ?? 'تفاصيل المسار التعليمي'); ?> - Mindlytics - أكاديمية البرمجة</title>

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
        
        <!-- Plyr - Custom Video Player -->
        <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
        
        <!-- YouTube IFrame API -->
        <script src="https://www.youtube.com/iframe_api"></script>
        
        <?php echo $__env->make('layouts.public-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
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
                padding-top: 0 !important;
                margin-top: 0 !important;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            html {
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                scroll-behavior: smooth;
            }
            
            body > * {
                flex-shrink: 0;
            }
            
            main {
                flex: 1 0 auto;
            }

            * {
                box-sizing: border-box;
            }

            /* Enhanced Navbar Styles */
            .navbar-gradient {
                background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1), 0 0 40px rgba(59, 130, 246, 0.2);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                backdrop-filter: blur(20px) saturate(180%);
                border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            }

            @keyframes patternShift {
                0% { background-position: 0 0; }
                100% { background-position: 20px 20px; }
            }

            @keyframes gradientFlow {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            @keyframes shine {
                0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            }

            /* Enhanced Hero Section */
            .hero-section {
                background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
                position: relative;
                overflow: hidden;
            }

            .animated-background {
                position: absolute;
                inset: 0;
                overflow: hidden;
                z-index: 0;
                pointer-events: none;
            }

            /* Floating Circles */
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
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(14, 165, 233, 0.2));
                top: 10%;
                right: 10%;
                animation-delay: 0s;
            }

            .floating-circle.circle-2 {
                width: 200px;
                height: 200px;
                background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), rgba(59, 130, 246, 0.2));
                bottom: 20%;
                left: 15%;
                animation-delay: 5s;
            }

            .floating-circle.circle-3 {
                width: 250px;
                height: 250px;
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.3), rgba(34, 197, 94, 0.2));
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

            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* مشغل الفيديو المخصص */
            .custom-video-player-wrapper {
                position: relative;
                width: 100%;
                border-radius: 1rem;
                overflow: hidden;
                background: #000;
            }

            /* تخصيص Plyr Player */
            .custom-video-player-wrapper .plyr {
                border-radius: 1rem;
            }

            .custom-video-player-wrapper .plyr__video-wrapper {
                background: #000;
                border-radius: 1rem;
                position: relative;
                overflow: hidden;
            }

            /* إخفاء علامات YouTube من Plyr */
            .custom-video-player-wrapper .plyr__video-embed {
                position: relative;
                overflow: hidden;
            }

            .custom-video-player-wrapper .plyr__video-embed iframe {
                border: none;
                position: relative;
            }

            /* إخفاء جميع عناصر YouTube */
            .custom-video-player-wrapper .plyr__video-embed::before,
            .custom-video-player-wrapper .plyr__video-embed::after {
                display: none !important;
            }

            /* مشغل YouTube المخصص */
            .custom-youtube-player-container {
                position: relative;
                width: 100%;
                padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
                height: 0;
                background: #000;
                border-radius: 1rem;
                overflow: hidden;
                margin: 0;
            }

            .custom-youtube-player-container #custom-video-player {
                position: absolute;
                top: -60px; /* إخفاء الجزء العلوي الذي يحتوي على مشاركة ومشاهدة لاحقاً */
                left: 0;
                width: 100%;
                height: calc(100% + 120px); /* زيادة الارتفاع لإخفاء الأجزاء العلوية والسفلية */
                border: none;
            }

            /* إخفاء جميع عناصر YouTube */
            .custom-youtube-player-container iframe {
                border: none !important;
                position: absolute !important;
                top: -60px !important;
                left: 0 !important;
                width: 100% !important;
                height: calc(100% + 120px) !important;
            }

            /* تخصيص ألوان المشغل */
            .custom-video-player-wrapper .plyr__control--overlaid {
                background: rgba(59, 130, 246, 0.9) !important;
            }

            .custom-video-player-wrapper .plyr__control--overlaid:hover {
                background: rgba(37, 99, 235, 0.95) !important;
            }

            .custom-video-player-wrapper .plyr__progress__played {
                background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
            }

            .custom-video-player-wrapper .plyr__volume__progress {
                background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
            }

            .custom-video-player-wrapper .plyr__control.plyr__tab-focus,
            .custom-video-player-wrapper .plyr__control:hover {
                background: rgba(59, 130, 246, 0.1) !important;
            }

            /* Pulse Animation */
            .pulse-animation {
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                    opacity: 1;
                }
                50% {
                    transform: scale(1.05);
                    opacity: 0.9;
                }
            }
        </style>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false }"
      :class="{ 'overflow-hidden': mobileMenu }">

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <main class="pt-0 mt-0">
    
    <!-- Hero Section -->
    <section class="hero-section relative overflow-hidden min-h-[60vh] flex items-center pt-16 lg:pt-20">
        <!-- Animated Background -->
        <div class="animated-background absolute inset-0 overflow-hidden">
            <!-- Floating Circles -->
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-8 lg:py-10">
            <!-- Breadcrumb -->
            <nav class="mb-4 text-gray-600 text-sm flex items-center fade-in-up">
                <a href="<?php echo e(url('/')); ?>" class="hover:text-blue-600 transition-colors">الرئيسية</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="hover:text-blue-600 transition-colors">المسارات التعليمية</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium"><?php echo e(Str::limit($learningPath->name ?? 'المسار', 30)); ?></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                <!-- Path Info -->
                <div class="slide-in-left">
                    <div class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-blue-500 to-green-500 rounded-full shadow-md mb-4 fade-in-up">
                        <i class="fas fa-route text-white text-xs"></i>
                        <span class="text-white font-bold text-sm">مسار تعليمي شامل</span>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-3 leading-tight text-gray-900 fade-in-up" style="animation-delay: 0.1s;">
                        <?php echo e($learningPath->name ?? 'اسم المسار'); ?>

                    </h1>
                    
                    <p class="text-base md:text-lg text-gray-600 mb-5 leading-relaxed fade-in-up" style="animation-delay: 0.2s;">
                        <?php echo e($learningPath->description ?? 'مسار تعليمي شامل ومتخصص'); ?>

                    </p>

                    <!-- Path Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-blue-600 mb-2"><?php echo e($learningPath->courses_count ?? 0); ?></div>
                            <div class="text-sm text-gray-600 font-medium">كورس</div>
                        </div>
                        <?php
                            $totalLessons = ($learningPath->courses ?? collect())->sum('lessons_count') ?? 0;
                        ?>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-green-600 mb-2"><?php echo e($totalLessons); ?></div>
                            <div class="text-sm text-gray-600 font-medium">درس</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-xl font-black text-gray-700 mb-2">
                                شامل
                            </div>
                            <div class="text-sm text-gray-600 font-medium">المستوى</div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 fade-in-up" style="animation-delay: 0.3s;">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if($isEnrolled ?? false): ?>
                                <a href="<?php echo e(route('student.learning-path.show', $learningPath->slug)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-play-circle"></i>
                                    ابدأ التعلم الآن
                                </a>
                            <?php elseif(isset($enrollment) && $enrollment->status === 'pending'): ?>
                                <div class="inline-flex items-center justify-center gap-2 bg-yellow-100 text-yellow-800 px-6 py-3 rounded-full font-bold text-base border-2 border-yellow-300">
                                    <i class="fas fa-clock"></i>
                                    طلبك قيد المراجعة
                                </div>
                            <?php else: ?>
                                <?php if(($learningPath->price ?? 0) > 0): ?>
                                    <a href="<?php echo e(route('public.learning-path.checkout', Str::slug($learningPath->name))); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-shopping-cart"></i>
                                        اشترك في المسار
                                    </a>
                                <?php else: ?>
                                    <form action="<?php echo e(route('public.learning-path.enroll.free', Str::slug($learningPath->name))); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                            <i class="fas fa-user-plus"></i>
                                            اشترك مجاناً
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(auth()->guard()->guest()): ?>
                            <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-user-plus"></i>
                                سجل للاشتراك
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-full font-bold text-base border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300">
                            <i class="fas fa-arrow-right"></i>
                            جميع المسارات
                        </a>
                    </div>
                </div>

                <!-- Video Introduction Section (Moved from top) -->
                <div class="relative fade-in-up" style="animation-delay: 0.2s;">
                    <?php if($learningPath->video_url ?? null): ?>
                    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl border border-gray-200 hover:shadow-2xl transition-all duration-300">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-2">
                                <i class="fas fa-play-circle text-blue-600 ml-2"></i>
                                مقدمة المسار التعليمي
                            </h2>
                            <p class="text-gray-600">شاهد هذا الفيديو للتعرف على المسار التعليمي</p>
                        </div>
                        <div class="custom-video-player-wrapper">
                            <?php
                                $videoUrl = $learningPath->video_url;
                                $videoId = null;
                                $videoType = null;
                                
                                // استخراج ID من رابط YouTube
                                if (strpos($videoUrl, 'youtube.com/watch') !== false || strpos($videoUrl, 'youtu.be/') !== false) {
                                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                                    if (isset($matches[1])) {
                                        $videoId = $matches[1];
                                        $videoType = 'youtube';
                                    }
                                } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                                    preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $videoUrl, $matches);
                                    if (isset($matches[1])) {
                                        $videoId = $matches[1];
                                        $videoType = 'vimeo';
                                    }
                                } elseif (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $videoUrl)) {
                                    $videoType = 'html5';
                                }
                            ?>
                            
                            <?php if($videoType === 'youtube' && $videoId): ?>
                                <!-- مشغل الفيديو المخصص - YouTube -->
                                <div id="custom-video-player-container" class="custom-youtube-player-container">
                                    <div id="custom-video-player"></div>
                                </div>
                            <?php elseif($videoType === 'vimeo' && $videoId): ?>
                                <!-- مشغل الفيديو المخصص - Vimeo -->
                                <div class="plyr__video-embed" id="custom-video-player" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo e($videoId); ?>"></div>
                            <?php elseif($videoType === 'html5'): ?>
                                <!-- مشغل الفيديو المخصص - HTML5 -->
                                <video id="custom-video-player" class="plyr__video" playsinline controls>
                                    <source src="<?php echo e($videoUrl); ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <div class="bg-gray-100 rounded-lg p-8 text-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                                    <p class="text-gray-600">رابط الفيديو غير مدعوم</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl border border-gray-200">
                        <div class="text-center text-gray-500 py-12">
                            <i class="fas fa-video text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg">لا يوجد فيديو مقدمة متاح</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Path Details Section -->
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- About Path -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            عن المسار التعليمي
                        </h2>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <?php if($learningPath->description && trim($learningPath->description) !== ''): ?>
                                <p class="text-lg mb-4"><?php echo e($learningPath->description); ?></p>
                            <?php else: ?>
                                <p class="text-lg mb-4">مسار تعليمي شامل ومتخصص يضم مجموعة من الكورسات المتنوعة التي تغطي جميع جوانب هذا المجال. تم تصميم هذا المسار بعناية ليوفر لك تجربة تعليمية متكاملة من البداية حتى الاحتراف.</p>
                                <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-xl p-6 border border-blue-100 mt-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">ما يميز هذا المسار:</h3>
                                    <ul class="space-y-3 text-gray-700">
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span>محتوى شامل ومنظم يغطي جميع الأساسيات والمفاهيم المتقدمة</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span>مشاريع عملية تطبيقية لتعزيز التعلم</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span>دعم مستمر من المدربين والزملاء</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span>شهادة إتمام معتمدة في نهاية المسار</span>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Courses in Path -->
                    <?php if($learningPath->courses && $learningPath->courses->count() > 0): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.1s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                            الكورسات في هذا المسار
                        </h2>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $learningPath->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all duration-300 hover:shadow-md group">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <?php echo e($index + 1); ?>

                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-black text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                        <?php echo e($course->title); ?>

                                    </h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        <?php echo e(Str::limit($course->description ?? 'كورس برمجي شامل', 100)); ?>

                                    </p>
                                    <div class="flex items-center gap-4 flex-wrap">
                                        <?php if($course->lessons_count > 0): ?>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-play-circle text-blue-600"></i>
                                            <span><?php echo e($course->lessons_count); ?> درس</span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($course->academicSubject): ?>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-book text-green-600"></i>
                                            <span><?php echo e($course->academicSubject->name); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if(($course->price ?? 0) > 0): ?>
                                        <div class="flex items-center gap-2 text-sm font-bold text-blue-600">
                                            <i class="fas fa-tag"></i>
                                            <span><?php echo e(number_format($course->price, 0)); ?> ج.م</span>
                                        </div>
                                        <?php else: ?>
                                        <div class="flex items-center gap-2 text-sm font-bold text-green-600">
                                            <i class="fas fa-gift"></i>
                                            <span>مجاني</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <span>عرض</span>
                                        <i class="fas fa-arrow-left text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="space-y-6 sticky top-24">
                        <!-- Path Info Card -->
                        <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-6 lg:p-8 border-2 border-gray-100 hover:border-blue-200 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/50 to-green-100/50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-gray-900 mb-6 text-center">معلومات المسار</h3>
                            
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border-2 border-blue-100">
                                        <span class="text-gray-700 font-semibold">عدد الكورسات</span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($learningPath->courses_count ?? 0); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border-2 border-green-100">
                                        <span class="text-gray-700 font-semibold">إجمالي الدروس</span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($totalLessons); ?></span>
                                    </div>
                                    
                                    <?php
                                        $freeCourses = ($learningPath->courses ?? collect())->filter(function($course) {
                                            return ($course->price ?? 0) == 0;
                                        })->count();
                                        $paidCourses = ($learningPath->courses ?? collect())->filter(function($course) {
                                            return ($course->price ?? 0) > 0;
                                        })->count();
                                    ?>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl border-2 border-purple-100">
                                        <span class="text-gray-700 font-semibold">كورسات مجانية</span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($freeCourses); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-blue-50 rounded-xl border-2 border-orange-100">
                                        <span class="text-gray-700 font-semibold">كورسات مدفوعة</span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($paidCourses); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Subscription Card -->
                        <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-6 lg:p-8 border-2 border-gray-100 hover:border-blue-200 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/50 to-green-100/50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="relative z-10">
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                        <i class="fas fa-user-plus text-white text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 mb-2">اشترك في المسار</h3>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-center mb-6">
                                    <?php if(($learningPath->price ?? 0) > 0): ?>
                                        <div class="text-3xl font-black text-blue-600 mb-1"><?php echo e(number_format($learningPath->price, 0)); ?> <span class="text-lg text-gray-600">ج.م</span></div>
                                    <?php else: ?>
                                        <div class="text-3xl font-black text-green-600 mb-1">مجاني</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Path Features -->
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>وصول مدى الحياة</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>شهادة إتمام معتمدة</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>مشاريع عملية</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span>دعم مباشر</span>
                                    </div>
                                </div>

                                <!-- CTA Button -->
                                <div class="mt-6">
                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if($isEnrolled ?? false): ?>
                                            <a href="<?php echo e(route('student.learning-path.show', $learningPath->slug)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                <i class="fas fa-play"></i>
                                                ابدأ الآن
                                            </a>
                                        <?php elseif(isset($enrollment) && $enrollment->status === 'pending'): ?>
                                            <div class="inline-flex items-center justify-center gap-2 bg-yellow-100 text-yellow-800 px-6 py-3 rounded-xl font-bold text-sm border-2 border-yellow-300 w-full">
                                                <i class="fas fa-clock"></i>
                                                طلبك قيد المراجعة
                                            </div>
                                        <?php else: ?>
                                            <?php if(($learningPath->price ?? 0) > 0): ?>
                                                <a href="<?php echo e(route('public.learning-path.checkout', Str::slug($learningPath->name))); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    اشترك في المسار
                                                </a>
                                            <?php else: ?>
                                                <form action="<?php echo e(route('public.learning-path.enroll.free', Str::slug($learningPath->name))); ?>" method="POST" class="mb-3">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                        <i class="fas fa-user-plus"></i>
                                                        اشترك مجاناً
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(auth()->guard()->guest()): ?>
                                        <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                            <i class="fas fa-user-plus"></i>
                                            سجل للاشتراك
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Paths -->
    <?php if(isset($relatedPaths) && $relatedPaths->count() > 0): ?>
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    مسارات <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">ذات صلة</span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">استكشف المزيد من المسارات التعليمية</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <?php $__currentLoopData = $relatedPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-2 border-gray-200 hover:border-blue-300 transition-all duration-300 hover:shadow-2xl group h-full">
                    <div class="h-48 bg-gradient-to-br flex items-center justify-center relative overflow-hidden
                        <?php if($loop->index % 3 == 0): ?> from-sky-400 via-sky-500 to-sky-600
                        <?php elseif($loop->index % 3 == 1): ?> from-blue-500 via-blue-600 to-blue-700
                        <?php else: ?> from-indigo-500 via-indigo-600 to-indigo-700
                        <?php endif; ?>">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-300"></div>
                        <i class="fas fa-route text-white text-6xl pulse-animation relative z-10 group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    
                    <div class="p-6 bg-white flex-grow flex flex-col">
                        <h3 class="text-xl font-black text-gray-900 mb-3 group-hover:text-sky-600 transition-colors">
                            <?php echo e($path->name); ?>

                        </h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed line-clamp-2">
                            <?php echo e(Str::limit($path->description ?? 'مسار تعليمي شامل', 80)); ?>

                        </p>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            <div>
                                <span class="text-2xl font-black text-sky-600"><?php echo e(number_format($path->price ?? 0, 0)); ?></span>
                                <span class="text-sm text-gray-500">ج.م</span>
                            </div>
                            <a href="<?php echo e(route('public.learning-path.show', $path->slug)); ?>" class="bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-eye ml-2"></i>
                                عرض
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    </main>
    
    <!-- Unified Footer -->
    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Plyr JavaScript -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    
    <!-- Script لمشغل الفيديو المخصص -->
    <script>
        <?php if(isset($videoType) && $videoType === 'youtube' && isset($videoId) && $videoId): ?>
        let youtubePlayer;
        
        // تهيئة YouTube IFrame API
        function onYouTubeIframeAPIReady() {
            youtubePlayer = new YT.Player('custom-video-player', {
                height: '100%',
                width: '100%',
                videoId: '<?php echo e($videoId); ?>',
                playerVars: {
                    'autoplay': 0,
                    'controls': 1,
                    'rel': 0,
                    'showinfo': 0,
                    'modestbranding': 1,
                    'iv_load_policy': 3,
                    'cc_load_policy': 0,
                    'disablekb': 0,
                    'fs': 1,
                    'playsinline': 1,
                    'origin': window.location.origin,
                    'widget_referrer': window.location.origin,
                    'enablejsapi': 1,
                    'autohide': 1,
                    'wmode': 'opaque',
                    'loop': 0,
                    'playlist': ''
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            // إخفاء جميع علامات YouTube
            hideAllYouTubeBranding();
            setInterval(hideAllYouTubeBranding, 1000);
        }

        function onPlayerStateChange(event) {
            setTimeout(hideAllYouTubeBranding, 500);
        }

        function hideAllYouTubeBranding() {
            try {
                const container = document.querySelector('.custom-youtube-player-container');
                const iframe = container ? container.querySelector('iframe') : null;
                
                if (!iframe) return;
                
                // إضافة CSS لإخفاء علامات YouTube باستخدام CSS clipping
                const style = document.createElement('style');
                style.id = 'youtube-complete-hide';
                style.textContent = `
                    /* إخفاء جميع عناصر YouTube باستخدام CSS clipping */
                    .custom-youtube-player-container {
                        position: relative !important;
                        overflow: hidden !important;
                    }
                    
                    .custom-youtube-player-container #custom-video-player {
                        position: absolute !important;
                        top: -60px !important;
                        left: 0 !important;
                        width: 100% !important;
                        height: calc(100% + 120px) !important;
                    }
                    
                    .custom-youtube-player-container iframe {
                        border: none !important;
                        position: absolute !important;
                        top: -60px !important;
                        left: 0 !important;
                        width: 100% !important;
                        height: calc(100% + 120px) !important;
                    }
                `;
                
                const oldStyle = document.getElementById('youtube-complete-hide');
                if (oldStyle) oldStyle.remove();
                document.head.appendChild(style);
            } catch(e) {
                console.log('Hiding YouTube branding...');
            }
        }

        // التأكد من تحميل API
        if (typeof YT !== 'undefined' && YT.Player) {
            onYouTubeIframeAPIReady();
        } else {
            window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
        }
        <?php else: ?>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة مشغل الفيديو المخصص للـ Vimeo و HTML5
            const player = new Plyr('#custom-video-player', {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'duration',
                    'mute',
                    'volume',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen'
                ],
                settings: ['quality', 'speed'],
                quality: {
                    default: 720,
                    options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240]
                },
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
                },
                keyboard: {
                    focused: true,
                    global: false
                },
                tooltips: {
                    controls: true,
                    seek: true
                },
                hideControls: true,
                clickToPlay: true,
                autoplay: false,
                volume: 1,
                muted: false,
                ratio: '16:9',
                youtube: {
                    noCookie: false,
                    rel: 0,
                    showinfo: 0,
                    iv_load_policy: 3,
                    modestbranding: 1,
                    controls: 0,
                    disablekb: 0,
                    fs: 0,
                    cc_load_policy: 0,
                    playsinline: 1,
                    origin: window.location.origin,
                    enablejsapi: 1,
                    autohide: 1,
                    wmode: 'opaque'
                },
                vimeo: {
                    byline: false,
                    portrait: false,
                    title: false,
                    transparent: false
                }
            });

            // إخفاء جميع علامات YouTube بعد تحميل المشغل
            player.on('ready', function() {
                hideYouTubeBranding();
            });

            // إخفاء العلامات عند تغيير حالة الفيديو
            player.on('play', function() {
                hideYouTubeBranding();
            });

            player.on('pause', function() {
                hideYouTubeBranding();
            });

            function hideYouTubeBranding() {
                try {
                    const iframe = document.querySelector('.plyr__video-embed iframe');
                    if (!iframe || !iframe.src.includes('youtube')) return;
                    
                    // إضافة CSS قوي جداً لإخفاء جميع علامات YouTube
                    const style = document.createElement('style');
                    style.id = 'youtube-hide-style';
                    style.textContent = `
                        /* إخفاء جميع عناصر YouTube */
                        .plyr__video-embed {
                            position: relative !important;
                            overflow: hidden !important;
                        }
                        
                        .plyr__video-embed iframe {
                            border: none !important;
                            position: relative !important;
                        }
                        
                        /* إخفاء عناصر YouTube عبر CSS - بدون overlays */
                        .plyr__video-embed::before,
                        .plyr__video-embed::after {
                            display: none !important;
                            content: none !important;
                        }
                        
                        /* إخفاء أي عناصر YouTube داخل iframe */
                        .plyr__video-embed iframe[src*="youtube"] {
                            pointer-events: auto !important;
                        }
                    `;
                    
                    // إزالة الستايل القديم إذا كان موجوداً
                    const oldStyle = document.getElementById('youtube-hide-style');
                    if (oldStyle) {
                        oldStyle.remove();
                    }
                    
                    document.head.appendChild(style);
                    
                    // تعديل iframe لإخفاء جميع العلامات
                    setTimeout(function() {
                        if (iframe) {
                            let src = iframe.src;
                            
                            // إزالة جميع المعاملات الحالية وإضافة معاملات جديدة
                            const baseUrl = src.split('?')[0];
                            const params = new URLSearchParams();
                            
                            // إضافة معاملات لإخفاء جميع العلامات
                            params.set('modestbranding', '1');
                            params.set('rel', '0');
                            params.set('showinfo', '0');
                            params.set('controls', '0');
                            params.set('fs', '0');
                            params.set('iv_load_policy', '3');
                            params.set('cc_load_policy', '0');
                            params.set('playsinline', '1');
                            params.set('enablejsapi', '1');
                            params.set('autohide', '1');
                            params.set('wmode', 'opaque');
                            params.set('origin', window.location.origin);
                            
                            const newSrc = baseUrl + '?' + params.toString();
                            
                            if (iframe.src !== newSrc) {
                                iframe.src = newSrc;
                            }
                        }
                    }, 500);
                } catch(e) {
                    console.log('Hiding YouTube branding...');
                }
            }
            
            // إخفاء العلامات بشكل دوري ومستمر
            setInterval(hideYouTubeBranding, 1500);
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/learning-path-show.blade.php ENDPATH**/ ?>