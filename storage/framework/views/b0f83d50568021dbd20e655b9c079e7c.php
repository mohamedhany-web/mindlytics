<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التسجيل مغلق — <?php echo e($program->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-2xl border border-amber-200 shadow-lg p-8 text-center space-y-4">
        <h1 class="text-xl font-black text-slate-900">التسجيل مغلق</h1>
        <p class="text-slate-600">منحة «<?php echo e($program->name); ?>» لا تقبل تسجيلات جديدة في الوقت الحالي.</p>
        <a href="<?php echo e(route('login')); ?>" class="inline-flex px-5 py-2.5 rounded-xl bg-slate-800 text-white font-semibold">تسجيل الدخول</a>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\scholarships\closed.blade.php ENDPATH**/ ?>