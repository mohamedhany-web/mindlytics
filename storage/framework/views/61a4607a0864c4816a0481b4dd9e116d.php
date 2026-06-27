<?php $__env->startSection('title', __('instructor.edit_assignment_title') . ' - ' . $assignment->title); ?>
<?php $__env->startSection('header', __('instructor.edit_assignment_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="hover:text-sky-600"><?php echo e(__('instructor.assignments')); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e(__('common.edit')); ?></span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800"><?php echo e(__('instructor.edit_assignment_title')); ?>: <?php echo e($assignment->title); ?></h1>
    </div>

    <form action="<?php echo e(route('instructor.assignments.update', $assignment)); ?>" method="POST" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="advanced_course_id" value="<?php echo e($assignment->advanced_course_id ?? $assignment->course_id); ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="group_id" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.for_group_optional')); ?></label>
                <select name="group_id" id="group_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                    <option value=""><?php echo e(__('instructor.for_all_students_option')); ?></option>
                    <?php $__currentLoopData = $courseGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e(old('group_id', $assignment->group_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="mt-1 text-xs text-slate-500"><?php echo e(__('instructor.group_assignment_hint')); ?></p>
                <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.assignment_title_required')); ?> <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="<?php echo e(old('title', $assignment->title)); ?>" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.description')); ?></label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"><?php echo e(old('description', $assignment->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2">
                <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.instructions_label')); ?></label>
                <textarea name="instructions" id="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"><?php echo e(old('instructions', $assignment->instructions)); ?></textarea>
                <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="due_date" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.due_date')); ?></label>
                <input type="datetime-local" name="due_date" id="due_date" value="<?php echo e(old('due_date', $assignment->due_date?->format('Y-m-d\TH:i'))); ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="max_score" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.total_score_label')); ?> <span class="text-red-500">*</span></label>
                <input type="number" name="max_score" id="max_score" value="<?php echo e(old('max_score', $assignment->max_score)); ?>" min="1" max="1000" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <?php $__errorArgs = ['max_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 hover:bg-slate-50 w-full">
                    <input type="checkbox" name="allow_late_submission" value="1" <?php echo e(old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                    <span class="text-sm font-medium text-slate-700"><?php echo e(__('instructor.allow_late_submission_label')); ?></span>
                </label>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('common.status')); ?> <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                    <option value="draft" <?php echo e(old('status', $assignment->status) == 'draft' ? 'selected' : ''); ?>><?php echo e(__('instructor.draft')); ?></option>
                    <option value="published" <?php echo e(old('status', $assignment->status) == 'published' ? 'selected' : ''); ?>><?php echo e(__('instructor.published')); ?></option>
                    <option value="archived" <?php echo e(old('status', $assignment->status) == 'archived' ? 'selected' : ''); ?>><?php echo e(__('instructor.archived')); ?></option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5 border-t border-slate-200">
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-center"><?php echo e(__('common.cancel')); ?></a>
            <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold"><?php echo e(__('instructor.save_changes')); ?></button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\assignments\edit.blade.php ENDPATH**/ ?>