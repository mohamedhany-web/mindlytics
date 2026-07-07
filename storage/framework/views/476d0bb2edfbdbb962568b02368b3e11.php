
<?php $__env->startSection('title', 'تعديل موعد عمل'); ?>
<?php $__env->startSection('header', 'تعديل موعد عمل'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-6 border">
    <form method="post" action="<?php echo e(route('admin.work-schedules.update', $schedule)); ?>" class="space-y-6">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <?php echo $__env->make('admin.work-schedules._form', ['schedule' => $schedule, 'dayOptions' => $dayOptions], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold">تحديث</button>
            <a href="<?php echo e(route('admin.work-schedules.index')); ?>" class="px-6 py-3 border rounded-xl">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/work-schedules/edit.blade.php ENDPATH**/ ?>