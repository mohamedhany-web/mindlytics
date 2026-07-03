<?php $__env->startSection('title', 'منحة جديدة'); ?>
<?php $__env->startSection('header', 'إنشاء منحة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'programs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => 'إنشاء منحة جديدة',
        'subtitle' => 'سيتم إنشاء كورس معزول تلقائياً ورابط تسجيل خاص',
        'icon' => 'fas fa-plus-circle',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?> max-w-3xl">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">بيانات المنحة</h3>
        </div>
        <form method="POST" action="<?php echo e(route('admin.scholarships.programs.store')); ?>" class="p-6 space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-award text-blue-600 text-sm"></i> اسم المنحة *</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="<?php echo e($schInputClass); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-align-right text-blue-600 text-sm"></i> الوصف (للمدرب)</label>
                <textarea name="description" rows="4" class="<?php echo e($schTextareaClass); ?>"><?php echo e(old('description')); ?></textarea>
            </div>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-chalkboard-teacher text-blue-600 text-sm"></i> المدرب *</label>
                <select name="instructor_id" required class="<?php echo e($schSelectClass); ?>">
                    <option value="">اختر المدرب</option>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($instructor->id); ?>" <?php if(old('instructor_id') == $instructor->id): echo 'selected'; endif; ?>><?php echo e($instructor->name); ?> — <?php echo e($instructor->email); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-calendar text-blue-600 text-sm"></i> بداية التسجيل</label>
                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at')); ?>" class="<?php echo e($schInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-calendar text-blue-600 text-sm"></i> نهاية التسجيل</label>
                    <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at')); ?>" class="<?php echo e($schInputClass); ?>">
                </div>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-blue-600">
                <span class="text-sm text-slate-700 font-medium">المنحة نشطة</span>
            </label>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200">
                <button type="submit" class="<?php echo e($schBtnPrimary); ?>"><i class="fas fa-check"></i><span>إنشاء المنحة</span></button>
                <a href="<?php echo e(route('admin.scholarships.dashboard')); ?>" class="<?php echo e($schBtnSecondary); ?>">إلغاء</a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\programs\create.blade.php ENDPATH**/ ?>