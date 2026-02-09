<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Mindlytics - أكاديمية البرمجة'); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">

    <!-- الخطوط العربية - موحّد مع الصفحة الرئيسية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Resource Hints للأداء -->
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - محسّن -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- Custom Styles from welcome.blade.php -->
    <?php echo $__env->make('layouts.public-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">
    
    <!-- Navigation Header - نفس الناف بار من الصفحة الرئيسية -->
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer - نفس فوتر الصفحة الرئيسية -->
    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        // تأثير الناف بار عند السكرول - محسّن للأداء
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                let ticking = false;
                let isScrolled = false;
                
                function handleScroll() {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                            const shouldBeScrolled = currentScroll > 100;
                            
                            if (shouldBeScrolled !== isScrolled) {
                                if (shouldBeScrolled) {
                                    navbar.classList.add('scrolled');
                                } else {
                                    navbar.classList.remove('scrolled');
                                }
                                isScrolled = shouldBeScrolled;
                            }
                            
                            ticking = false;
                        });
                        ticking = true;
                    }
                }
                
                window.addEventListener('scroll', handleScroll, { passive: true });
            }
        });
    </script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/layouts/public.blade.php ENDPATH**/ ?>