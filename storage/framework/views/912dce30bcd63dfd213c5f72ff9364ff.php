

<?php $__env->startSection('title', 'تسجيل ساعات'); ?>
<?php $__env->startSection('header', 'تسجيل ساعات — ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 max-w-2xl">
    <form action="<?php echo e(route('place.office.usage-logs.store')); ?>" method="POST" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">تاريخ الاستخدام *</label>
            <input type="date" name="usage_date" value="<?php echo e(old('usage_date', now()->toDateString())); ?>" max="<?php echo e(now()->toDateString()); ?>" required
                   class="w-full rounded-lg border-slate-300">
            <?php $__errorArgs = ['usage_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">عدد الساعات *</label>
            <input type="number" name="hours" value="<?php echo e(old('hours')); ?>" step="0.25" min="0.25" max="24" required
                   class="w-full rounded-lg border-slate-300" placeholder="مثال: 3.5">
            <?php $__errorArgs = ['hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">وصف (اختياري)</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300"><?php echo e(old('description')); ?></textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white rounded-lg font-semibold">حفظ وإرسال للمراجعة</button>
            <a href="<?php echo e(route('place.office.usage-logs.index')); ?>" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\place-office\usage-logs\create.blade.php ENDPATH**/ ?>