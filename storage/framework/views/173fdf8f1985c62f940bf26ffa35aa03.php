

<?php $__env->startSection('title', 'إعدادات التقرير اليومي'); ?>
<?php $__env->startSection('header', 'إعدادات التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 max-w-3xl">
    <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="text-sm text-emerald-700 font-semibold mb-4 inline-block"><i class="fas fa-arrow-right ml-1"></i> التقارير اليومية</a>
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl border p-6">
        <?php echo $__env->make('admin.sales.daily-reports._settings_form', [
            'formAction' => route('admin.sales.daily-reports.settings.update'),
            'method' => 'PUT',
            'settings' => $settings,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/daily-reports/settings.blade.php ENDPATH**/ ?>