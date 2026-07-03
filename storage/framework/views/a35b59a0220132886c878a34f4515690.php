

<?php $__env->startSection('title', 'تعديل إضافة خارجية'); ?>
<?php $__env->startSection('header', 'تعديل إضافة — ' . $employee_addition->addition_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <a href="<?php echo e(route('admin.employee-additions.show', $employee_addition)); ?>" class="inline-flex items-center gap-1 text-sm text-slate-600 mb-4 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> العودة للتفاصيل
    </a>

    <form method="post" action="<?php echo e(route('admin.employee-additions.update', $employee_addition)); ?>" class="rounded-2xl bg-white border p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm">
            <span class="text-slate-500">الموظف:</span>
            <span class="font-semibold text-slate-900"><?php echo e($employee_addition->employee->name ?? '—'); ?></span>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">العنوان *</label>
            <input type="text" name="title" value="<?php echo e(old('title', $employee_addition->title)); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">المبلغ *</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount', $employee_addition->amount)); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">النوع</label>
                <select name="type" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <?php $__currentLoopData = \App\Models\EmployeeSalaryAddition::typeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('type', $employee_addition->type) === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">التاريخ</label>
                <input type="date" name="addition_date" value="<?php echo e(old('addition_date', $employee_addition->addition_date->format('Y-m-d'))); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="applied" <?php if(old('status', $employee_addition->status) === 'applied'): echo 'selected'; endif; ?>>مطبقة</option>
                    <option value="pending" <?php if(old('status', $employee_addition->status) === 'pending'): echo 'selected'; endif; ?>>معلقة</option>
                    <option value="cancelled" <?php if(old('status', $employee_addition->status) === 'cancelled'): echo 'selected'; endif; ?>>ملغاة</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">الوصف</label>
            <textarea name="description" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"><?php echo e(old('description', $employee_addition->description)); ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">ملاحظات داخلية</label>
            <textarea name="notes" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"><?php echo e(old('notes', $employee_addition->notes)); ?></textarea>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm">حفظ التعديلات</button>
            <a href="<?php echo e(route('admin.employee-additions.show', $employee_addition)); ?>" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-semibold text-sm">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-additions\edit.blade.php ENDPATH**/ ?>