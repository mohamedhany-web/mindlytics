<?php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($__pageLocale); ?>" dir="<?php echo e($__pageRtl ? 'rtl' : 'ltr'); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title><?php echo e($learningPath->name ?? __('public.learning_path_detail_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>

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

            /* حاوية الفيديو 16:9 */
            .intro-video-container {
                position: relative;
                width: 100%;
                padding-bottom: 56.25%; /* 16:9 */
                height: 0;
                background: #000;
                border-radius: 1rem;
                overflow: hidden;
            }

            .intro-video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: none;
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
                <div class="relative fade-in-up max-w-xl" style="animation-delay: 0.2s;">
                    <?php if($learningPath->video_url ?? null): ?>
                    <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                        <div class="text-center mb-3">
                            <h2 class="text-lg font-bold text-gray-900 mb-0.5 flex items-center justify-center gap-2">
                                <i class="fas fa-play-circle text-blue-600 text-base"></i>
                                <?php echo e(__('public.path_intro_card_title')); ?>

                            </h2>
                            <p class="text-gray-500 text-sm"><?php echo e(__('public.path_intro_card_sub')); ?></p>
                        </div>
                        <?php
                            $pathIntroVideoUrl = trim((string) ($learningPath->video_url ?? ''));
                        ?>
                        <?php echo $__env->make('partials.intro-video-embed', [
                            'url' => $pathIntroVideoUrl,
                            'title' => __('public.path_intro_video_title'),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <?php else: ?>
                    <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-200">
                        <div class="text-center text-gray-500 py-6">
                            <i class="fas fa-video text-2xl mb-2 text-gray-300"></i>
                            <p class="text-sm"><?php echo e(__('public.no_intro_video')); ?></p>
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
                            <?php echo e(__('public.about_path')); ?>

                        </h2>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <?php if($learningPath->description && trim($learningPath->description) !== ''): ?>
                                <p class="text-lg mb-4"><?php echo e($learningPath->description); ?></p>
                            <?php else: ?>
                                <p class="text-lg mb-4"><?php echo e(__('public.path_description_fallback_long')); ?></p>
                                <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-xl p-6 border border-blue-100 mt-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4"><?php echo e(__('public.path_highlights_title')); ?></h3>
                                    <ul class="space-y-3 text-gray-700">
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span><?php echo e(__('public.path_highlight_1')); ?></span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span><?php echo e(__('public.path_highlight_2')); ?></span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span><?php echo e(__('public.path_highlight_3')); ?></span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                            <span><?php echo e(__('public.path_highlight_4')); ?></span>
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
                            <?php echo e(__('public.courses_in_path')); ?>

                        </h2>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $learningPath->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all duration-300 hover:shadow-md group">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <?php echo e($index + 1); ?>

                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-black text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                        <?php echo e($course->localized('title')); ?>

                                    </h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        <?php echo e(Str::limit($course->localized('description') ?: __('public.course_fallback_short'), 100)); ?>

                                    </p>
                                    <div class="flex items-center gap-4 flex-wrap">
                                        <?php if($course->lessons_count > 0): ?>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-play-circle text-blue-600"></i>
                                            <span><?php echo e($course->lessons_count); ?> <?php echo e(__('public.lesson_single')); ?></span>
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
                                            <span><?php echo e(number_format($course->price, 0)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                                        </div>
                                        <?php else: ?>
                                        <div class="flex items-center gap-2 text-sm font-bold text-green-600">
                                            <i class="fas fa-gift"></i>
                                            <span><?php echo e(__('public.free_price')); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <span><?php echo e(__('public.view_btn')); ?></span>
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
                                <h3 class="text-2xl font-black text-gray-900 mb-6 text-center"><?php echo e(__('public.path_info_title')); ?></h3>
                            
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border-2 border-blue-100">
                                        <span class="text-gray-700 font-semibold"><?php echo e(__('public.path_courses_count_label')); ?></span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($learningPath->courses_count ?? 0); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border-2 border-green-100">
                                        <span class="text-gray-700 font-semibold"><?php echo e(__('public.path_total_lessons')); ?></span>
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
                                        <span class="text-gray-700 font-semibold"><?php echo e(__('public.path_free_courses')); ?></span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($freeCourses); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-blue-50 rounded-xl border-2 border-orange-100">
                                        <span class="text-gray-700 font-semibold"><?php echo e(__('public.path_paid_courses')); ?></span>
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
                                    <h3 class="text-xl font-black text-gray-900 mb-2"><?php echo e(__('public.subscribe_path')); ?></h3>
                                </div>
                                
                                <!-- Price: سعر المسار مستقل عن أسعار الكورسات -->
                                <div class="text-center mb-6">
                                    <p class="text-xs text-gray-500 mb-1">سعر الاشتراك في المسار</p>
                                    <?php if(($learningPath->price ?? 0) > 0): ?>
                                        <div class="text-3xl font-black text-blue-600 mb-1"><?php echo e(number_format($learningPath->price, 0)); ?> <span class="text-lg text-gray-600"><?php echo e(__('public.currency_egp')); ?></span></div>
                                    <?php else: ?>
                                        <div class="text-3xl font-black text-green-600 mb-1"><?php echo e(__('public.free_price')); ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Path Features -->
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><?php echo e(__('public.lifetime_access')); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><?php echo e(__('public.certificate_on_completion')); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><?php echo e(__('public.practical_projects')); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><?php echo e(__('public.direct_support')); ?></span>
                                    </div>
                                </div>

                                <!-- CTA Button -->
                                <div class="mt-6">
                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if($isEnrolled ?? false): ?>
                                            <a href="<?php echo e(route('student.learning-path.show', $learningPath->slug)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                <i class="fas fa-play"></i>
                                                <?php echo e(__('public.start_now')); ?>

                                            </a>
                                        <?php elseif(isset($enrollment) && $enrollment->status === 'pending'): ?>
                                            <div class="inline-flex items-center justify-center gap-2 bg-yellow-100 text-yellow-800 px-6 py-3 rounded-xl font-bold text-sm border-2 border-yellow-300 w-full">
                                                <i class="fas fa-clock"></i>
                                                <?php echo e(__('public.enrollment_pending')); ?>

                                            </div>
                                        <?php else: ?>
                                            <?php if(($learningPath->price ?? 0) > 0): ?>
                                                <a href="<?php echo e(route('public.learning-path.checkout', Str::slug($learningPath->name))); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <?php echo e(__('public.subscribe_path_btn')); ?>

                                                </a>
                                            <?php else: ?>
                                                <form action="<?php echo e(route('public.learning-path.enroll.free', Str::slug($learningPath->name))); ?>" method="POST" class="mb-3">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                                        <i class="fas fa-user-plus"></i>
                                                        <?php echo e(__('public.enroll_free')); ?>

                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(auth()->guard()->guest()): ?>
                                        <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                            <i class="fas fa-user-plus"></i>
                                            <?php echo e(__('public.register_to_enroll')); ?>

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

    <!-- Reviews -->
    <section class="py-10 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900">التقييمات والمراجعات</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-bold text-yellow-600"><?php echo e(number_format((float) ($reviewsAvg ?? 0), 1)); ?></span>
                            <span class="text-gray-400">/ 5</span>
                            <span class="text-gray-500">— (<?php echo e(number_format((int) ($reviewsCount ?? 0))); ?> مراجعة)</span>
                        </p>
                    </div>
                </div>

                <?php if(session('success')): ?>
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                    <?php if($isEnrolled ?? false): ?>
                        <form action="<?php echo e(route('public.learning-path.reviews.store', $learningPath->slug)); ?>" method="POST" class="mb-6 space-y-3">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">تقييمك <span class="text-rose-500">*</span></label>
                                <select name="rating" required class="w-full max-w-md rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">اختر التقييم</option>
                                    <?php for($i=5; $i>=1; $i--): ?>
                                        <option value="<?php echo e($i); ?>" <?php if((string) old('rating') === (string) $i): echo 'selected'; endif; ?>><?php echo e($i); ?> / 5</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">اكتب مراجعتك <span class="text-rose-500">*</span></label>
                                <textarea name="comment" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" placeholder="شارك رأيك في المسار"><?php echo e(old('comment')); ?></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all">
                                <i class="fas fa-paper-plane"></i>
                                نشر
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold">
                            يجب أن تكون مشتركاً في المسار لتتمكن من إضافة تقييم.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold">
                        <a class="text-blue-700 hover:underline" href="<?php echo e(route('login', ['redirect' => url()->current()])); ?>">سجّل دخولك</a>
                        لإضافة تقييم.
                    </div>
                <?php endif; ?>

                <?php if(isset($approvedReviews) && $approvedReviews->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $approvedReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-bold text-gray-900 text-sm"><?php echo e($r->user->name ?? 'طالب'); ?></div>
                                    <div class="flex items-center gap-1 text-xs">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo e($i <= (int) $r->rating ? 'text-yellow-400' : 'text-gray-300'); ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="text-gray-700 text-sm mt-2 whitespace-pre-wrap"><?php echo e($r->comment ?? ''); ?></div>
                                <div class="text-xs text-gray-400 mt-2"><?php echo e($r->created_at?->format('Y-m-d')); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-sm text-gray-500">لا توجد مراجعات منشورة بعد.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Related Paths -->
    <?php if(isset($relatedPaths) && $relatedPaths->count() > 0): ?>
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    <?php echo e(__('public.related_paths_title')); ?> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500"><?php echo e(__('public.related_paths_highlight')); ?></span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto"><?php echo e(__('public.explore_more_paths')); ?></p>
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
                            <?php echo e(Str::limit($path->description ?? __('public.path_description_fallback'), 80)); ?>

                        </p>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            <div>
                                <span class="text-2xl font-black text-sky-600"><?php echo e(number_format($path->price ?? 0, 0)); ?></span>
                                <span class="text-sm text-gray-500"><?php echo e(__('public.currency_egp')); ?></span>
                            </div>
                            <a href="<?php echo e(route('public.learning-path.show', $path->slug)); ?>" class="bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fas fa-eye ml-2"></i>
                                <?php echo e(__('public.view_btn')); ?>

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
    
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/learning-path-show.blade.php ENDPATH**/ ?>