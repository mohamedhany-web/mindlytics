<?php $__env->startSection('title', 'إضافة خارجية'); ?>
<?php $__env->startSection('header', 'إضافة خارجية لموظف'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <form method="post" action="<?php echo e(route('admin.employee-additions.store')); ?>" class="rounded-2xl bg-white border p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-semibold mb-1">الموظف *</label>
            <select name="employee_id" required class="w-full rounded-xl border px-3 py-2 text-sm">
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">العنوان *</label>
            <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm" placeholder="مثال: مكافأة أداء">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">المبلغ *</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">النوع</label>
                <select name="type" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <?php $__currentLoopData = \App\Models\EmployeeSalaryAddition::typeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('type', 'bonus') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">التاريخ</label>
                <input type="date" name="addition_date" value="<?php echo e(old('addition_date', today()->toDateString())); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="applied" <?php if(old('status', 'applied') === 'applied'): echo 'selected'; endif; ?>>مطبقة فوراً</option>
                    <option value="pending">معلقة</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">الوصف</label>
            <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"><?php echo e(old('description')); ?></textarea>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm">حفظ الإضافة</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/employee-additions/create.blade.php ENDPATH**/ ?>