<?php $__env->startSection('title', 'تعديل عميل محتمل'); ?>
<?php $__env->startSection('header', 'تعديل عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> العودة للتفاصيل</a>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-2"><i class="fas fa-exclamation-circle ml-1"></i> لم يتم الحفظ — راجع الحقول التالية:</p>
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="post" action="<?php echo e(route('employee.sales.leads.update', $lead)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('employee.sales._lead_fields', ['lead' => $lead, 'groups' => $groups ?? collect()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">حفظ</button>
                <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/sales/leads/edit.blade.php ENDPATH**/ ?>