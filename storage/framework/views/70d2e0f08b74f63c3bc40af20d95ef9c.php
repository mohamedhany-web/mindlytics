<?php $locale = app()->getLocale(); $rtl = $locale === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($rtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('admin.community_dashboard')); ?> - <?php echo e(config('app.name')); ?></title>
    <?php echo $__env->make('components.favicon-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>* { font-family: 'Tajawal', 'Cairo', sans-serif; }</style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-slate-50 text-gray-900 min-h-screen">
    <div class="flex min-h-screen">
        <!-- شريط جانبي ـ مجتمع البيانات (كما في الصورة المرجعية) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 <?php echo e($rtl ? 'lg:right-0' : 'lg:left-0'); ?> bg-slate-900 border-<?php echo e($rtl ? 'l' : 'r'); ?> border-slate-700/50 z-30">
            <!-- الهيدر: مجتمع البيانات والذكاء الاصطناعي -->
            <div class="p-4 border-b border-slate-700/50">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400"><i class="fas fa-chevron-up text-xs"></i></span>
                    <span class="w-10 h-10 rounded-xl bg-slate-700/80 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users-cog text-cyan-400"></i>
                    </span>
                    <span class="text-white font-bold text-sm"><?php echo e(__('public.community_heading')); ?></span>
                </div>
            </div>
            <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
                <p class="px-3 py-2 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">مراقبة عامة</p>
                <a href="<?php echo e(route('admin.community.dashboard')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.dashboard') || request()->routeIs('admin.community') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>لوحة المراقبة</span>
                </a>
                <p class="px-3 py-2 mt-2 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">المحتوى</p>
                <a href="<?php echo e(route('admin.community.competitions.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.competitions.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-trophy w-5"></i>
                    <span><?php echo e(__('admin.community_competitions')); ?></span>
                </a>
                <a href="<?php echo e(route('admin.community.datasets.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.datasets.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-database w-5"></i>
                    <span><?php echo e(__('admin.community_datasets')); ?></span>
                </a>
                <p class="px-3 py-2 mt-2 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">المساهمون والمراجعة</p>
                <a href="<?php echo e(route('admin.community.submissions.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.submissions.index') || request()->routeIs('admin.community.submissions.dataset.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-paper-plane w-5"></i>
                    <span>مراجعة تقديمات البيانات</span>
                </a>
                <a href="<?php echo e(route('admin.community.submissions.models.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.submissions.models.*') || request()->routeIs('admin.community.submissions.model.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-brain w-5"></i>
                    <span>تقديمات النماذج (Model Zoo)</span>
                </a>
                <a href="<?php echo e(route('admin.community.contributors.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.contributors.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-user-plus w-5"></i>
                    <span>إنشاء وإدارة المساهمين</span>
                </a>
                <p class="px-3 py-2 mt-2 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">أخرى</p>
                <a href="<?php echo e(route('admin.community.discussions.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.discussions.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-comments w-5"></i>
                    <span><?php echo e(__('admin.community_discussions')); ?></span>
                </a>
                <a href="<?php echo e(route('admin.community.settings.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white <?php echo e(request()->routeIs('admin.community.settings.*') ? 'bg-slate-700/80 text-white' : ''); ?>">
                    <i class="fas fa-cog w-5"></i>
                    <span><?php echo e(__('admin.community_settings')); ?></span>
                </a>
            </nav>
            <div class="p-3 border-t border-slate-700/50">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white text-sm">
                    <i class="fas fa-arrow-left w-4"></i>
                    <span><?php echo e(__('admin.dashboard')); ?></span>
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-1">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-red-300 w-full text-sm">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span><?php echo e(__('auth.logout')); ?></span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex flex-col flex-1 min-w-0 <?php echo e($rtl ? 'lg:mr-64' : 'lg:ml-64'); ?>">
            <header class="flex-shrink-0 sticky top-0 z-20 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('admin.community.dashboard')); ?>" class="text-slate-600 hover:text-slate-900 font-semibold"><?php echo e(__('admin.community_section')); ?></a>
                    <span class="text-slate-400">/</span>
                    <span class="text-slate-900 font-bold"><?php echo $__env->yieldContent('header', __('admin.community_dashboard')); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-600"><?php echo e(auth()->user()->name); ?></span>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="<?php echo e(__('admin.dashboard')); ?>">
                        <i class="fas fa-th-large"></i>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-auto bg-gray-50 min-w-0 w-full">
                <div class="w-full max-w-full p-4 sm:p-6 lg:p-8">
                    <?php if(session('success')): ?>
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\layouts\admin-community.blade.php ENDPATH**/ ?>