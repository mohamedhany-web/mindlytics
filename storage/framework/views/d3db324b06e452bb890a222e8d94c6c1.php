<?php $pmLocale = app()->getLocale(); $pmRtl = $pmLocale === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e($pmLocale); ?>" dir="<?php echo e($pmRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'لوحة المكان'); ?> - <?php echo e(config('app.name')); ?></title>

    <?php echo $__env->make('components.favicon-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .place-manager-sidebar-nav {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .place-manager-sidebar-nav::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
         x-init="
            window.addEventListener('close-sidebar', () => { sidebarOpen = false; });
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => { if (window.innerWidth >= 1024) sidebarOpen = false; }, 150);
            });
         "
         @close-sidebar.window="sidebarOpen = false">
        <div class="flex min-h-screen lg:h-screen overflow-x-hidden">
            <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:right-0 lg:z-20 flex-shrink-0 inset-y-0">
                <?php echo $__env->make('layouts.place-manager-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>

            <div x-show="sidebarOpen" x-cloak
                 @click.away="if (window.innerWidth < 1024) sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 lg:hidden"
                 style="display: none;">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
                <div class="absolute inset-y-0 right-0 flex flex-col w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform transition-transform duration-150 ease-out border-l border-slate-700/50"
                     :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
                    <div class="absolute top-4 left-4 z-50">
                        <button @click="sidebarOpen = false" class="flex items-center justify-center h-10 w-10 rounded-full bg-slate-700/50 hover:bg-slate-600/50 text-slate-200 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <?php echo $__env->make('layouts.place-manager-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <div class="flex flex-col flex-1 min-w-0 lg:pr-64 w-full lg:h-screen">
                <header class="sticky top-0 z-30 flex-shrink-0 flex h-14 sm:h-16 bg-gradient-to-r from-slate-50 via-blue-50 to-slate-100 shadow-lg border-b border-slate-200/50 backdrop-blur-sm">
                    <button @click="sidebarOpen = true" class="px-3 sm:px-4 border-l border-slate-200/50 text-slate-700 hover:bg-slate-100/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-400 lg:hidden transition-colors">
                        <i class="fas fa-bars text-base sm:text-lg"></i>
                    </button>

                    <div class="flex-1 px-3 sm:px-6 flex justify-between items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                            <?php echo $__env->yieldContent('header', 'لوحة المكان'); ?>
                        </h1>

                        <div class="relative" x-data="{ open: false }">
                            <?php $user = auth()->user(); $profileImage = $user->profile_image_url; ?>
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <?php if($profileImage): ?>
                                    <img src="<?php echo e($profileImage); ?>" alt="<?php echo e($user->name); ?>" class="w-8 h-8 rounded-full object-cover border-2 border-blue-200">
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                                    </div>
                                <?php endif; ?>
                                <span class="hidden sm:block"><?php echo e($user->name); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="<?php echo e(route('place.office.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-home mr-2"></i>لوحة التحكم
                                </a>
                                <a href="<?php echo e(route('place.office.profile')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>الملف الشخصي
                                </a>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto bg-gray-50">
                    <div class="p-3 sm:p-4 md:p-6">
                        <?php if(session('success')): ?>
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(session('error')): ?>
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                                <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($errors->any()): ?>
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                                <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                            </div>
                        <?php endif; ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/layouts/place-manager.blade.php ENDPATH**/ ?>