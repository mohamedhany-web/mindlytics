<?php $learnLocale = app()->getLocale(); ?>
<!DOCTYPE html>
<html lang="<?php echo e($learnLocale); ?>" dir="rtl" class="learn-rtl-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Mindlytics')); ?> - <?php echo $__env->yieldContent('title', __('student.learn')); ?></title>
    <?php echo $__env->make('components.favicon-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php echo $__env->yieldPushContent('meta'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="learn-immersive-body learn-rtl antialiased" dir="rtl">
    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\layouts\learn-immersive.blade.php ENDPATH**/ ?>