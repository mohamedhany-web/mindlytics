

<?php $__env->startSection('title', 'مجموعة جديدة'); ?>
<?php $__env->startSection('header', 'مجموعة عملاء جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl bg-white border rounded-xl p-6">
    <form method="post" action="<?php echo e(route('admin.sales.groups.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">اسم المجموعة</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">موظف المبيعات</label>
                <select name="assigned_to" required class="w-full border rounded-lg px-3 py-2">
                    <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rep->id); ?>" <?php if(old('assigned_to') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2"><?php echo e(old('description')); ?></textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">إنشاء — ثم أضف العملاء</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\create.blade.php ENDPATH**/ ?>