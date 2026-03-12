<?php $locale = app()->getLocale(); $rtl = $locale === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($rtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('auth.login')); ?> - <?php echo e(__('public.community_heading')); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Tajawal', 'Cairo', sans-serif; }
        body { background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%); min-height: 100vh; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-900 bg-slate-50">
    <header class="flex-shrink-0 border-b border-slate-200 bg-white/80 backdrop-blur-sm shadow-sm">
        <div class="max-w-6xl mx-auto py-4 px-4 sm:px-6 flex items-center justify-between">
            <a href="<?php echo e(route('public.community.index')); ?>" class="flex items-center gap-2 text-slate-800 font-bold text-lg hover:text-blue-600 transition-colors">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-md">
                    <i class="fas fa-database text-white"></i>
                </span>
                <span><?php echo e(__('public.community_heading')); ?></span>
            </a>
            <a href="<?php echo e(route('home')); ?>" class="text-sm text-slate-600 hover:text-slate-900 font-medium"><?php echo e(__('auth.home')); ?></a>
        </div>
    </header>
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <footer class="flex-shrink-0 py-4 text-center text-slate-500 text-sm border-t border-slate-200 bg-white/50">
        <?php echo $__env->yieldContent('footer', ''); ?>
    </footer>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/community/layouts/guest.blade.php ENDPATH**/ ?>