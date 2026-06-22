

<?php $__env->startSection('title', 'إعدادات التقرير اليومي'); ?>
<?php $__env->startSection('header', 'إعدادات التقرير اليومي للموظفين'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <a href="<?php echo e(route('admin.employee-daily-reports.index')); ?>" class="text-sm text-slate-600 mb-4 inline-block"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <?php if(session('success')): ?><div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <form method="post" action="<?php echo e(route('admin.employee-daily-reports.settings.update')); ?>" class="rounded-2xl bg-white border p-6 space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <label class="flex items-center gap-2"><input type="checkbox" name="enabled" value="1" class="rounded" <?php if($settings['enabled'] ?? true): echo 'checked'; endif; ?>> تفعيل التقارير اليومية</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="penalty_enabled" value="1" class="rounded" <?php if($settings['penalty_enabled'] ?? true): echo 'checked'; endif; ?>> تفعيل الغرامة التلقائية</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="exclude_sales_employees" value="1" class="rounded" <?php if($settings['exclude_sales_employees'] ?? true): echo 'checked'; endif; ?>> استثناء موظفي المبيعات (لديهم نظام منفصل)</label>
        <div>
            <label class="block text-sm font-semibold mb-1">مبلغ الغرامة (ج.م)</label>
            <input type="number" name="penalty_amount" step="0.01" min="0" value="<?php echo e($settings['penalty_amount'] ?? 50); ?>" class="w-full rounded-xl border px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-6 py-2.5 bg-sky-600 text-white rounded-xl font-semibold text-sm">حفظ</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-daily-reports\settings.blade.php ENDPATH**/ ?>