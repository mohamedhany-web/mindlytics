

<?php $__env->startSection('title', 'تعديل موعد عمل'); ?>
<?php $__env->startSection('header', 'تعديل موعد عمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل موعد عمل</h1>
                <p class="text-gray-600 mt-1"><?php echo e($schedule->name); ?></p>
            </div>
            <a href="<?php echo e(route('admin.work-schedules.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form method="post" action="<?php echo e(route('admin.work-schedules.update', $schedule)); ?>" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">بيانات الموعد</h2>
                <?php echo $__env->make('admin.work-schedules._form', ['schedule' => $schedule], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?php echo e(route('admin.work-schedules.index')); ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث الموعد
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\work-schedules\edit.blade.php ENDPATH**/ ?>