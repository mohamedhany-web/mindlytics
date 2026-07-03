

<?php $__env->startSection('title', 'مجموعات واتساب'); ?>
<?php $__env->startSection('header', 'مجموعات واتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = null;
?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات واتساب</h2>
            <p class="text-sm text-slate-500 mt-0.5">إنشاء مجموعات Meta Cloud وإرسال دعوات للعملاء</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="btn-wa-secondary">مركز المبيعات</a>
            <a href="<?php echo e($r('create')); ?>" class="btn-wa-primary">
                <i class="fas fa-plus"></i> مجموعة جديدة
            </a>
        </div>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <p class="text-xs text-slate-500">المجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['total'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">نشطة</p>
            <p class="text-2xl font-bold text-emerald-700 tabular-nums"><?php echo e($stats['active'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">المدعوون</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($stats['participants'] ?? 0); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">Meta Cloud</p>
            <p class="text-sm font-bold <?php echo e(($cloud['connected'] ?? false) ? 'text-emerald-700' : 'text-amber-700'); ?> mt-1">
                <?php echo e(($cloud['connected'] ?? false) ? 'متصل' : 'غير جاهز'); ?>

            </p>
        </div>
    </div>

    <?php echo $__env->make('employee.sales.whatsapp-groups._cloud_status', ['cloud' => $cloud, 'settingsUrl' => $settingsUrl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('employee.sales.whatsapp-groups._list', ['groups' => $groups, 'r' => $r], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\index.blade.php ENDPATH**/ ?>