<?php $__env->startSection('title', 'مجموعة جديدة'); ?>
<?php $__env->startSection('header', 'مجموعة عملاء جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl bg-white border rounded-xl p-6">
    <form method="post" action="<?php echo e(route('admin.sales.groups.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-medium mb-1">اسم المجموعة</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">موظفو المبيعات (يمكن اختيار أكثر من واحد أو الكل)</label>
            <div class="max-h-48 overflow-y-auto border rounded-lg p-3 space-y-2">
                <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="member_ids[]" value="<?php echo e($rep->id); ?>" class="rounded"
                            <?php if(collect(old('member_ids', []))->contains($rep->id)): echo 'checked'; endif; ?>>
                        <span><?php echo e($rep->name); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2"><?php echo e(old('description')); ?></textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">إنشاء — ثم أضف العملاء</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/groups/create.blade.php ENDPATH**/ ?>