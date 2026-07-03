<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($program->name); ?> — منحة</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-2xl border border-slate-200 shadow-lg p-8 text-center space-y-4">
        <h1 class="text-2xl font-black text-slate-900"><?php echo e($program->name); ?></h1>
        <?php if($program->description): ?><p class="text-slate-600"><?php echo e($program->description); ?></p><?php endif; ?>
        <?php if($program->isRegistrationOpen()): ?>
            <a href="<?php echo e(route('scholarships.register', $program->slug)); ?>" class="inline-flex px-6 py-3 rounded-xl bg-violet-600 text-white font-bold">التسجيل في المنحة</a>
        <?php else: ?>
            <p class="text-amber-700 font-semibold">التسجيل مغلق حالياً.</p>
        <?php endif; ?>
        <a href="<?php echo e(route('login')); ?>" class="block text-sm text-slate-500 hover:underline">لديك حساب؟ سجّل الدخول</a>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\scholarships\show.blade.php ENDPATH**/ ?>