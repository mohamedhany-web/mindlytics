

<?php $__env->startSection('title', 'مجموعة جديدة'); ?>
<?php $__env->startSection('header', 'مجموعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto bg-white border border-slate-200 rounded-xl p-6">
    <form method="post" action="<?php echo e(route('employee.sales.groups.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-medium mb-1">اسم المجموعة</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2"><?php echo e(old('description')); ?></textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">إنشاء</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/groups/create.blade.php ENDPATH**/ ?>