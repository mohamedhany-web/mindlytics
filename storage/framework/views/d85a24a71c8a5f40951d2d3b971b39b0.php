

<?php $__env->startSection('title', $employee_addition->addition_number); ?>
<?php $__env->startSection('header', 'إضافة — ' . $employee_addition->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-4">
    <a href="<?php echo e(route('admin.employee-additions.index')); ?>" class="text-sm text-slate-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="rounded-2xl bg-white border p-6 space-y-3">
        <p><strong>الرقم:</strong> <?php echo e($employee_addition->addition_number); ?></p>
        <p><strong>الموظف:</strong> <?php echo e($employee_addition->employee->name ?? '—'); ?></p>
        <p><strong>المبلغ:</strong> <span class="text-emerald-700 font-bold"><?php echo e(number_format($employee_addition->amount, 2)); ?> ج.م</span></p>
        <p><strong>النوع:</strong> <?php echo e(\App\Models\EmployeeSalaryAddition::typeLabels()[$employee_addition->type] ?? ''); ?></p>
        <p><strong>التاريخ:</strong> <?php echo e($employee_addition->addition_date->format('Y-m-d')); ?></p>
        <p><strong>الحالة:</strong> <?php echo e($employee_addition->status); ?></p>
        <?php if($employee_addition->description): ?><p class="whitespace-pre-wrap"><?php echo e($employee_addition->description); ?></p><?php endif; ?>
        <div class="flex flex-wrap gap-2 pt-2">
            <a href="<?php echo e(route('admin.employee-additions.edit', $employee_addition)); ?>" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تعديل</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-additions\show.blade.php ENDPATH**/ ?>