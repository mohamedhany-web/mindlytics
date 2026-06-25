<?php $__env->startSection('title', 'خطة تسويق جديدة'); ?>
<?php $__env->startSection('header', 'إنشاء خطة تسويق'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <a href="<?php echo e(route('employee.marketing-plans.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-right"></i> العودة للقائمة
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
        <form method="post" action="<?php echo e(route('employee.marketing-plans.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الخطة <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملخص</label>
                <textarea name="summary" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500"><?php echo e(old('summary')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الأهداف / استراتيجية عامة</label>
                <textarea name="goals" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500"><?php echo e(old('goals')); ?></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">بداية (اختياري)</label>
                    <input type="date" name="start_date" value="<?php echo e(old('start_date')); ?>" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نهاية (اختياري)</label>
                    <input type="date" name="end_date" value="<?php echo e(old('end_date')); ?>" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    <?php $__currentLoopData = ['draft' => 'مسودة', 'active' => 'نشط', 'paused' => 'متوقف مؤقتاً', 'completed' => 'مكتمل']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v); ?>" <?php echo e(old('status', 'draft') === $v ? 'selected' : ''); ?>><?php echo e($l); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ربط بدورة تصميم (اختياري)</label>
                <select name="design_task_cycle_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    <option value="">— بدون ربط —</option>
                    <?php $__currentLoopData = $cycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e((string) old('design_task_cycle_id') === (string) $c->id ? 'selected' : ''); ?>>#<?php echo e($c->id); ?> — <?php echo e($c->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['design_task_cycle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-semibold">
                <i class="fas fa-save ml-2"></i> حفظ والمتابعة
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/marketing-plans/create.blade.php ENDPATH**/ ?>