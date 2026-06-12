

<?php $__env->startSection('title', 'رقابة الطلاب'); ?>
<?php $__env->startSection('header', 'رقابة الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">رقابة الطلاب</h1>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">جميع الحالات</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>غير نشط</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التسجيلات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكورسات المكتملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخر نشاط</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><?php echo e($student->name); ?></td>
                        <td class="px-6 py-4"><?php echo e($student->enrollments_count); ?></td>
                        <td class="px-6 py-4"><?php echo e($student->completed_courses); ?></td>
                        <td class="px-6 py-4"><?php echo e($student->last_activity ? $student->last_activity->diffForHumans() : '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4"><?php echo e($students->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/quality-control/students.blade.php ENDPATH**/ ?>