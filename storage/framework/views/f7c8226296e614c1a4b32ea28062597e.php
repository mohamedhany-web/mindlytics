<?php $__env->startSection('title', __('instructor.edit_group_title') . ' - ' . $group->name); ?>
<?php $__env->startSection('header', __('instructor.edit_group_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="<?php echo e(route('instructor.groups.index')); ?>" class="hover:text-sky-600"><?php echo e(__('instructor.groups')); ?></a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('instructor.groups.show', $group)); ?>" class="hover:text-sky-600"><?php echo e($group->name); ?></a>
                <span class="mx-2">/</span>
                <span><?php echo e(__('common.edit')); ?></span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('instructor.edit_group_title')); ?></h1>
        </div>

        <form action="<?php echo e(route('instructor.groups.update', $group)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- الكورس -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('instructor.course_label')); ?> <span class="text-red-500">*</span>
                </label>
                <select name="course_id" id="course_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value=""><?php echo e(__('instructor.choose_course_option')); ?></option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $group->course_id) == $course->id ? 'selected' : ''); ?>>
                            <?php echo e($course->title); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- اسم المجموعة -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('instructor.group_name_required')); ?> <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name', $group->name)); ?>" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                       placeholder="<?php echo e(__('instructor.group_name_required')); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('instructor.description')); ?>

                </label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                          placeholder="<?php echo e(__('instructor.group_description_placeholder')); ?>"><?php echo e(old('description', $group->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- القائد -->
            <div>
                <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('instructor.group_leader_label')); ?>

                </label>
                <select name="leader_id" id="leader_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value=""><?php echo e(__('instructor.no_leader_short')); ?></option>
                    <?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($member->id); ?>" <?php echo e(old('leader_id', $group->leader_id) == $member->id ? 'selected' : ''); ?>>
                            <?php echo e($member->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e(__('instructor.can_choose_leader_hint')); ?></p>
                <?php $__errorArgs = ['leader_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الحد الأقصى للأعضاء -->
            <div>
                <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('instructor.max_members_label')); ?> <span class="text-red-500">*</span>
                </label>
                <input type="number" name="max_members" id="max_members" value="<?php echo e(old('max_members', $group->max_members)); ?>" 
                       min="2" max="50" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e(__('instructor.min_max_members_hint')); ?></p>
                <?php $__errorArgs = ['max_members'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الحالة -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <?php echo e(__('common.status')); ?> <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="active" <?php echo e(old('status', $group->status) == 'active' ? 'selected' : ''); ?>><?php echo e(__('instructor.active')); ?></option>
                    <option value="inactive" <?php echo e(old('status', $group->status) == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('instructor.inactive')); ?></option>
                    <option value="archived" <?php echo e(old('status', $group->status) == 'archived' ? 'selected' : ''); ?>><?php echo e(__('instructor.archived')); ?></option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الأزرار -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="<?php echo e(route('instructor.groups.show', $group)); ?>" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?php echo e(__('common.cancel')); ?>

                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    <i class="fas fa-save ml-2"></i>
                    <?php echo e(__('instructor.save_changes')); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\groups\edit.blade.php ENDPATH**/ ?>