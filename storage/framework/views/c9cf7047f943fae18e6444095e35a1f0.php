

<?php $__env->startSection('title', 'مجموعات واتساب'); ?>
<?php $__env->startSection('header', 'مجموعات واتساب — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p); ?>

<div class="p-4 md:p-6 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء وإدارة مجموعات واتساب الحقيقية من المنصة</p>
        </div>
        <a href="<?php echo e($r('create')); ?>" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
            <i class="fas fa-plus ml-1"></i> مجموعة جديدة
        </a>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="sales-panel p-4 text-sm <?php echo e(($bridge['connected'] ?? false) ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50'); ?>">
        <p class="font-bold mb-1"><i class="fab fa-whatsapp text-emerald-600 ml-1"></i> جلسة الجسر</p>
        <p><?php echo e(($bridge['connected'] ?? false) ? 'متصل' : ($bridge['error'] ?? 'غير متصل')); ?></p>
    </div>

    <?php echo $__env->make('employee.sales.whatsapp-groups._list', ['groups' => $groups, 'r' => $r], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\whatsapp-groups\index.blade.php ENDPATH**/ ?>