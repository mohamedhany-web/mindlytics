

<?php $__env->startSection('title', 'رقابة الموظفين'); ?>
<?php $__env->startSection('header', 'رقابة الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">رقابة الموظفين</h1>
        <form method="GET" class="mb-6">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي المهام</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مكتملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">معلقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">متأخرة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><?php echo e($employee->name); ?></td>
                        <td class="px-6 py-4"><?php echo e($employee->tasks_count); ?></td>
                        <td class="px-6 py-4"><?php echo e($employee->completed_tasks); ?></td>
                        <td class="px-6 py-4"><?php echo e($employee->pending_tasks); ?></td>
                        <td class="px-6 py-4 text-red-600 font-semibold"><?php echo e($employee->overdue_tasks); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4"><?php echo e($employees->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\quality-control\employees.blade.php ENDPATH**/ ?>