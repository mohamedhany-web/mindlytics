

<?php $__env->startSection('title', 'عميل محتمل جديد'); ?>
<?php $__env->startSection('header', 'عميل محتمل جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="post" action="<?php echo e(route('employee.sales.leads.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('employee.sales._lead_fields', ['lead' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">حفظ</button>
                <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/leads/create.blade.php ENDPATH**/ ?>