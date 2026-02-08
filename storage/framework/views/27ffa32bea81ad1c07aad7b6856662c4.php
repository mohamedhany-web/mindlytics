<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Mindlytics')); ?> - <?php echo $__env->yieldContent('title', 'لوحة التحكم'); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">

    <!-- Fonts -->
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
            background: #f9fafb;
            overflow-x: hidden;
        }

        /* Sidebar - يتناسب مع لوحة التحكم */
        .student-sidebar {
            background: #ffffff;
            border-left: 1px solid rgb(226 232 240);
            width: 280px;
            box-shadow: -1px 0 6px rgba(0, 0, 0, 0.04);
        }

        .nav-card {
            background: transparent;
            border: none;
            border-radius: 12px;
            padding: 10px 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgb(14 165 233);
            opacity: 0;
            border-radius: 0 3px 3px 0;
            transition: opacity 0.2s;
        }

        .nav-card:hover {
            background: rgb(241 245 249);
        }

        .nav-card.active {
            background: rgb(224 242 254);
            box-shadow: none;
        }

        .nav-card.active::before {
            opacity: 1;
        }

        .nav-card.active .nav-icon {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
        }

        .nav-card.active .font-black { color: rgb(17 24 39); }
        .nav-card.active .text-xs { color: rgb(75 85 99); }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
            flex-shrink: 0;
            line-height: 1;
            text-align: center;
        }
        .nav-icon i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
        .nav-card:hover .nav-icon { transform: scale(1.05); }

        /* Navbar - يتناسب مع لوحة التحكم */
        .student-header {
            background: #ffffff;
            border-bottom: 1px solid rgb(226 232 240);
            min-height: 64px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        @media (max-width: 640px) {
            .student-header {
                min-height: 56px;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }

        .search-command {
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }
        
        @media (max-width: 640px) {
            .search-command {
                padding: 8px 12px;
                border-radius: 10px;
            }
        }

        .search-command:focus-within {
            border-color: rgb(14 165 233);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1), 0 2px 8px rgba(14, 165, 233, 0.12);
        }

        .quick-action-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            color: rgb(100 116 139);
            position: relative;
            line-height: 1;
            text-align: center;
        }
        .quick-action-btn:hover {
            background: rgb(224 242 254);
            border-color: rgb(186 230 253);
            color: rgb(14 165 233);
        }

        .quick-action-btn i {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background: rgb(239 68 68);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: 700;
            border: 2px solid white;
            line-height: 1;
            text-align: center;
        }
        
        .notification-badge span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        .user-menu-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-menu-btn:hover {
            background: rgb(248 250 252);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgb(14 165 233), rgb(2 132 199));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 1px 4px rgba(14, 165, 233, 0.25);
            transition: all 0.2s ease;
            line-height: 1;
            text-align: center;
        }
        
        .user-avatar img {
            object-fit: cover;
            object-position: center;
        }
        
        .user-avatar:not(:has(img)) {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu-btn:hover .user-avatar {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        /* Dropdown - نفس أسلوب بطاقات لوحة التحكم */
        .dropdown-menu {
            background: white;
            border: 1px solid rgb(226 232 240);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .dropdown-item {
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
        }
        .dropdown-item:hover {
            background: rgb(248 250 252);
        }
        
        .dropdown-item i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        /* Scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgb(14 165 233), rgb(2 132 199));
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgb(2 132 199), rgb(14 165 233));
        }

        .logo-section {
            background: rgb(248 250 252);
            border-bottom: 1px solid rgb(226 232 240);
        }

        /* Fix Logo Alignment */
        .logo-section img,
        .student-sidebar img[alt*="Logo"],
        .navbar-gradient img[alt*="Logo"] {
            transform: none !important;
            rotate: 0deg !important;
            object-fit: contain !important;
            object-position: center center !important;
            display: block !important;
            margin: 0 auto !important;
        }

        .stats-card {
            transition: all 0.2s ease;
        }
        .stats-card:hover {
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
        }

        .user-profile-card {
            background: rgb(248 250 252);
            border-top: 1px solid rgb(226 232 240);
        }
        .user-profile-inner {
            transition: all 0.2s ease;
        }
        .user-profile-inner:hover {
            border-color: rgb(186 230 253);
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.08);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .student-sidebar {
                width: 320px;
                max-width: 85vw;
                min-width: 280px;
            }

            .nav-card {
                padding: 12px 14px;
            }

            .nav-icon {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .student-sidebar {
                width: 300px;
                max-width: 80vw;
                min-width: 260px;
            }
            
            .student-header {
                padding-left: 1rem;
                padding-right: 1rem;
                height: auto;
                min-height: 64px;
            }
            
            .search-command {
                padding: 8px 12px;
            }
        }

        @media (max-width: 640px) {
            .student-sidebar {
                width: 280px;
                max-width: 85vw;
                min-width: 0;
            }

            .logo-section {
                padding: 0.875rem;
            }

            .logo-section .w-12 {
                width: 2.5rem;
                height: 2.5rem;
            }

            .stats-card {
                padding: 0.625rem;
            }

            .stats-card .text-lg {
                font-size: 1.125rem;
            }

            .nav-card {
                padding: 10px 12px;
                margin-bottom: 4px;
            }

            .nav-icon {
                width: 32px;
                height: 32px;
                font-size: 13px;
            }

            .user-profile-card {
                padding: 0.625rem;
            }
            
            .student-header {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                gap: 0.5rem;
            }
            
            .quick-action-btn {
                width: 38px;
                height: 38px;
            }
            
            .user-avatar {
                width: 34px;
                height: 34px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .student-sidebar {
                width: 260px;
                max-width: 90vw;
            }
            
            .student-header {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .quick-action-btn {
                width: 36px;
                height: 36px;
            }
            
            .quick-action-btn i {
                font-size: 12px;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body x-data="{ 
    sidebarOpen: window.innerWidth >= 1024
}" 
x-init="
    function removeDarkMode() {
        document.documentElement.classList.remove('dark');
    }
    removeDarkMode();
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    removeDarkMode();
                }
            }
        });
    });
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    setInterval(removeDarkMode, 100);
    
    window.addEventListener('resize', () => {
        sidebarOpen = window.innerWidth >= 1024;
    });
">
    <div class="flex h-screen overflow-hidden">
        <?php if(auth()->guard()->check()): ?>
            <!-- Clean Sidebar -->
            <aside x-show="sidebarOpen || window.innerWidth >= 1024"
                   x-transition:enter="transition ease-out duration-150"
                   x-transition:enter-start="opacity-0 translate-x-full"
                   x-transition:enter-end="opacity-100 translate-x-0"
                   x-transition:leave="transition ease-in duration-100"
                   x-transition:leave-start="opacity-100 translate-x-0"
                   x-transition:leave-end="opacity-0 translate-x-full"
                   class="student-sidebar flex-shrink-0 fixed lg:static inset-y-0 right-0 z-50 lg:z-auto"
                   style="will-change: transform, opacity;">
                <?php echo $__env->make('layouts.student-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>

            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen && window.innerWidth < 1024"
                 @click="sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                 style="will-change: opacity;"></div>
        <?php endif; ?>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0">
            <?php if(auth()->guard()->check()): ?>
                <!-- Enhanced Header -->
                <header class="student-header flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-5 flex-1 min-w-0">
                        <!-- Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden p-2 sm:p-2.5 rounded-xl bg-gradient-to-br from-sky-500/10 to-sky-400/10 hover:from-sky-500/20 hover:to-sky-400/20 transition-all duration-300 flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-bars text-sky-500 text-sm sm:text-base"></i>
                        </button>

                        <!-- Enhanced Search - Mobile -->
                        <div class="flex md:hidden items-center flex-1 min-w-0 ml-2">
                            <div class="search-command flex items-center gap-2 w-full">
                                <i class="fas fa-search text-sky-500 text-xs sm:text-sm flex-shrink-0"></i>
                                <input type="text" 
                                       placeholder="بحث..." 
                                       class="flex-1 bg-transparent border-none outline-none text-xs sm:text-sm text-gray-700 placeholder-gray-400 font-medium min-w-0">
                            </div>
                        </div>

                        <!-- Enhanced Search - Desktop -->
                        <div class="hidden md:flex items-center flex-1 max-w-2xl min-w-0">
                            <div class="search-command flex items-center gap-3 w-full">
                                <i class="fas fa-search text-sky-500 text-sm flex-shrink-0"></i>
                                <input type="text" 
                                       placeholder="ابحث عن كورسات، دروس، امتحانات..." 
                                       class="flex-1 bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 font-medium min-w-0">
                                <kbd class="hidden lg:flex items-center gap-1 px-2.5 py-1 bg-gradient-to-br from-sky-500/10 to-sky-400/10 rounded text-xs font-bold text-sky-500 border border-sky-500/20 flex-shrink-0">
                                    <span>Ctrl</span>
                                    <span>K</span>
                                </kbd>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3 flex-shrink-0">
                        <!-- Quick Actions - Desktop Only -->
                        <div class="hidden lg:flex items-center gap-2">
                            <a href="<?php echo e(route('academic-years')); ?>" class="quick-action-btn" title="تصفح الكورسات">
                                <i class="fas fa-search text-sm"></i>
                            </a>
                            <a href="<?php echo e(route('my-courses.index')); ?>" class="quick-action-btn" title="كورساتي">
                                <i class="fas fa-book-open text-sm"></i>
                            </a>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="quick-action-btn relative">
                                <i class="fas fa-bell text-xs sm:text-sm"></i>
                                <span class="notification-badge text-[9px] sm:text-[10px]">3</span>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute left-0 mt-3 w-72 sm:w-80 md:w-96 dropdown-menu z-50 overflow-hidden">
                                <div class="p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-sky-400/10 to-sky-500/10">
                                    <h3 class="font-bold text-gray-900 text-xs sm:text-sm flex items-center gap-2">
                                        <i class="fas fa-bell text-sky-500 flex items-center justify-center"></i>
                                        <span>الإشعارات</span>
                                    </h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <div class="p-4 sm:p-6 text-center text-gray-500 text-xs sm:text-sm">
                                        <i class="fas fa-bell-slash text-xl sm:text-2xl mb-2 opacity-30 inline-flex items-center justify-center"></i>
                                        <p>لا توجد إشعارات جديدة</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="user-menu-btn flex items-center gap-1.5 sm:gap-2 md:gap-3 p-1 sm:p-1.5 md:p-2 rounded-xl">
                                <div class="user-avatar flex-shrink-0">
                                    <?php if(auth()->user()->profile_image): ?>
                                        <img src="<?php echo e(asset(auth()->user()->profile_image)); ?>" alt="" class="w-full h-full rounded-lg object-cover">
                                    <?php else: ?>
                                        <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="hidden sm:block md:hidden lg:block text-right min-w-0">
                                    <div class="text-xs sm:text-sm font-bold text-gray-900 truncate"><?php echo e(auth()->user()->name); ?></div>
                                    <div class="text-[10px] sm:text-xs text-gray-500">طالب</div>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] sm:text-xs text-gray-400 hidden sm:block transition-transform flex-shrink-0" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute left-0 mt-3 w-56 sm:w-64 dropdown-menu z-50 overflow-hidden">
                                <div class="p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-sky-400/10 to-sky-500/10">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center text-white font-bold text-sm sm:text-base shadow-lg flex-shrink-0">
                                            <?php if(auth()->user()->profile_image): ?>
                                                <img src="<?php echo e(asset(auth()->user()->profile_image)); ?>" alt="" class="w-full h-full rounded-xl object-cover">
                                            <?php else: ?>
                                                <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-gray-900 text-xs sm:text-sm truncate"><?php echo e(auth()->user()->name); ?></div>
                                            <div class="text-[10px] sm:text-xs text-gray-600 truncate"><?php echo e(auth()->user()->email); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-1.5 sm:p-2">
                                    <a href="<?php echo e(route('profile')); ?>" class="dropdown-item flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm text-gray-700 font-medium">
                                        <i class="fas fa-user w-4 sm:w-5 text-sky-500 flex-shrink-0"></i>
                                        <span>الملف الشخصي</span>
                                    </a>
                                    <a href="<?php echo e(route('settings')); ?>" class="dropdown-item flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm text-gray-700 font-medium">
                                        <i class="fas fa-cog w-4 sm:w-5 text-gray-500 flex-shrink-0"></i>
                                        <span>الإعدادات</span>
                                    </a>
                                    <hr class="my-1.5 sm:my-2 border-gray-200">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="w-full dropdown-item flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm text-red-600 font-medium">
                                            <i class="fas fa-sign-out-alt w-4 sm:w-5 flex-shrink-0"></i>
                                            <span>تسجيل الخروج</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto bg-gray-50 min-w-0 w-full">
                <div class="w-full max-w-full p-4 sm:p-6 lg:p-8">
                    <?php if(session('success')): ?>
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    
    <script>
        function removeDarkMode() {
            document.documentElement.classList.remove('dark');
        }
        
        removeDarkMode();
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (document.documentElement.classList.contains('dark')) {
                        removeDarkMode();
                    }
                }
            });
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
        
        setInterval(removeDarkMode, 50);
        
        document.addEventListener('DOMContentLoaded', removeDarkMode);
        window.addEventListener('load', removeDarkMode);
        window.addEventListener('pageshow', removeDarkMode);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/layouts/student-dashboard.blade.php ENDPATH**/ ?>