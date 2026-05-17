<?php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($__pageLocale); ?>" dir="<?php echo e($__pageRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>انتهت الورشة — <?php echo e($workshop->title); ?> | Mindlytics</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-900 via-slate-950 to-slate-900 min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-2xl shadow-orange-900/40 mb-8">
            <i class="fas fa-flag-checkered text-3xl"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-white mb-3">
            انتهت الورشة
        </h1>
        <p class="text-lg text-slate-200 font-semibold mb-2">
            <?php echo e($workshop->title); ?>

        </p>
        <p class="text-sm text-slate-400 leading-relaxed mb-8">
            لم يعد التسجيل في هذه الورشة متاحاً. نفس الرابط يعرض هذه الصفحة ليعرف الزائر أن الحجز أُغلق.
            <?php if($workshop->starts_at): ?>
                <span class="block mt-3 text-slate-500">كانت مجدولة: <?php echo e($workshop->starts_at->format('Y-m-d H:i')); ?></span>
            <?php endif; ?>
        </p>
        <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 px-6 py-3 text-sm font-semibold text-white transition-colors">
            <i class="fas fa-home"></i>
            العودة للرئيسية
        </a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/workshop-ended.blade.php ENDPATH**/ ?>