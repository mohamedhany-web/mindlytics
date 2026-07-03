

<?php $__env->startSection('title', 'مجموعات واتساب'); ?>
<?php $__env->startSection('header', 'مجموعات واتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">أنشئ مجموعات واتساب حقيقية، أضف العملاء، واضبط الإعدادات من المنصة</p>
        </div>
        <a href="<?php echo e($r('create')); ?>" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
            <i class="fas fa-plus ml-1"></i> مجموعة واتساب جديدة
        </a>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="sales-panel p-4 text-sm <?php echo e(($bridge['connected'] ?? false) ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50'); ?>">
        <p class="font-bold text-slate-800 mb-1"><i class="fab fa-whatsapp text-emerald-600 ml-1"></i> حالة جلسة الواتساب (الجسر)</p>
        <?php if($bridge['connected'] ?? false): ?>
            <p class="text-emerald-800">متصل — يمكنك إنشاء المجموعات وإدارتها.</p>
        <?php else: ?>
            <p class="text-amber-900"><?php echo e($bridge['error'] ?? 'غير متصل'); ?></p>
            <p class="text-xs text-amber-800 mt-1">مجموعات واتساب الحقيقية تعمل عبر الجسر (whatsapp-web.js) وليس Meta Cloud API. اطلب من الإدارة تفعيل الجسر وربط QR.</p>
        <?php endif; ?>
    </div>

    <?php echo $__env->make('employee.sales.whatsapp-groups._list', ['groups' => $groups, 'r' => $r], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\index.blade.php ENDPATH**/ ?>