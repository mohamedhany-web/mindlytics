<?php $__env->startSection('title', 'تعديل منحة'); ?>
<?php $__env->startSection('header', 'تعديل منحة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'programs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => 'تعديل: ' . $program->name,
        'subtitle' => 'تحديث بيانات المنحة والمدرب ومواعيد التسجيل',
        'icon' => 'fas fa-edit',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?> max-w-3xl">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">بيانات المنحة</h3>
        </div>
        <form method="POST" action="<?php echo e(route('admin.scholarships.programs.update', $program)); ?>" class="p-6 space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-award text-blue-600 text-sm"></i> اسم المنحة *</label>
                <input type="text" name="name" value="<?php echo e(old('name', $program->name)); ?>" required class="<?php echo e($schInputClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-align-right text-blue-600 text-sm"></i> الوصف</label>
                <textarea name="description" rows="4" class="<?php echo e($schTextareaClass); ?>"><?php echo e(old('description', $program->description)); ?></textarea>
            </div>
            <div>
                <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-chalkboard-teacher text-blue-600 text-sm"></i> المدرب *</label>
                <select name="instructor_id" required class="<?php echo e($schSelectClass); ?>">
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($instructor->id); ?>" <?php if(old('instructor_id', $program->instructor_id) == $instructor->id): echo 'selected'; endif; ?>><?php echo e($instructor->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="<?php echo e($schLabelClass); ?>">بداية التسجيل</label>
                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', optional($program->starts_at)->format('Y-m-d\TH:i'))); ?>" class="<?php echo e($schInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($schLabelClass); ?>">نهاية التسجيل</label>
                    <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', optional($program->ends_at)->format('Y-m-d\TH:i'))); ?>" class="<?php echo e($schInputClass); ?>">
                </div>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $program->is_active) ? 'checked' : ''); ?> class="rounded border-slate-300 text-blue-600">
                <span class="text-sm text-slate-700 font-medium">المنحة نشطة</span>
            </label>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200">
                <button type="submit" class="<?php echo e($schBtnPrimary); ?>"><i class="fas fa-save"></i><span>حفظ التعديلات</span></button>
                <a href="<?php echo e(route('admin.scholarships.programs.show', $program)); ?>" class="<?php echo e($schBtnSecondary); ?>">رجوع</a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\programs\edit.blade.php ENDPATH**/ ?>