

<?php $__env->startSection('title', $group->name); ?>
<?php $__env->startSection('header', 'مجموعة: '.$group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <?php if(session('success')): ?><div class="text-sm text-emerald-700"><?php echo e(session('success')); ?></div><?php endif; ?>

    <form method="post" action="<?php echo e(route('admin.sales.groups.update', $group)); ?>" class="bg-white border rounded-xl p-5 space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">الاسم</label>
                <input type="text" name="name" value="<?php echo e(old('name', $group->name)); ?>" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">موظف مسند</label>
                <select name="assigned_to" required class="w-full border rounded-lg px-3 py-2">
                    <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rep->id); ?>" <?php if(old('assigned_to', $group->assigned_to) == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <input type="text" name="description" value="<?php echo e(old('description', $group->description)); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">اختر العملاء (يُعاد إسنادهم للموظف المحدد)</label>
            <div class="max-h-80 overflow-y-auto border rounded-lg p-3 space-y-1 text-sm">
                <?php $__currentLoopData = $availableLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="lead_ids[]" value="<?php echo e($lead->id); ?>" class="rounded"
                            <?php if(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id): echo 'checked'; endif; ?>>
                        <span><?php echo e($lead->name); ?></span>
                        <span class="text-slate-400 text-xs"><?php echo e($lead->phone); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">حفظ</button>
    </form>

    <form method="post" action="<?php echo e(route('admin.sales.groups.destroy', $group)); ?>" onsubmit="return confirm('حذف المجموعة؟')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="text-rose-700 text-sm">حذف المجموعة</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\show.blade.php ENDPATH**/ ?>