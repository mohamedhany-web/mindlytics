<?php $__env->startSection('title', __('instructor.groups')); ?>
<?php $__env->startSection('header', __('instructor.groups')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800"><?php echo e(__('instructor.groups')); ?></h1>
                <p class="text-sm text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_student_groups')); ?></p>
            </div>
            <a href="<?php echo e(route('instructor.groups.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span><?php echo e(__('instructor.create_new_group')); ?></span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('instructor.total')); ?></p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['total'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('instructor.active')); ?></p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['active'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('instructor.inactive')); ?></p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['inactive'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600"><i class="fas fa-ban"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo e(__('instructor.total_members')); ?></p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo e($stats['total_members'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-user-friends"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('instructor.courses')); ?></label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value=""><?php echo e(__('instructor.all_courses')); ?></option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('common.status')); ?></label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value=""><?php echo e(__('instructor.all')); ?></option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>><?php echo e(__('instructor.active')); ?></option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('instructor.inactive')); ?></option>
                    <option value="archived" <?php echo e(request('status') == 'archived' ? 'selected' : ''); ?>><?php echo e(__('instructor.archived')); ?></option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-1"><?php echo e(__('common.search')); ?></label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('instructor.search_placeholder')); ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search ml-1"></i> <?php echo e(__('common.search')); ?>

                </button>
                <?php if(request()->anyFilled(['course_id', 'status', 'search'])): ?>
                    <a href="<?php echo e(route('instructor.groups.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if($groups->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="p-5 flex-1">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <h3 class="text-lg font-bold text-slate-800 truncate"><?php echo e($group->name); ?></h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0
                                <?php if($group->status == 'active'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($group->status == 'inactive'): ?> bg-amber-100 text-amber-700
                                <?php else: ?> bg-slate-100 text-slate-600
                                <?php endif; ?>">
                                <?php if($group->status == 'active'): ?> <?php echo e(__('instructor.active')); ?>

                                <?php elseif($group->status == 'inactive'): ?> <?php echo e(__('instructor.inactive')); ?>

                                <?php else: ?> <?php echo e(__('instructor.archived')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if($group->description): ?>
                            <p class="text-sm text-slate-600 mb-3 line-clamp-2"><?php echo e($group->description); ?></p>
                        <?php endif; ?>
                        <div class="space-y-2 text-sm text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-book text-sky-500 w-4"></i>
                                <span><?php echo e($group->course->title ?? '—'); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-violet-500 w-4"></i>
                                <span><?php echo e($group->members_count ?? 0); ?> / <?php echo e($group->max_members ?? '—'); ?> <?php echo e(__('instructor.members')); ?></span>
                            </div>
                            <?php if($group->leader): ?>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-crown text-amber-500 w-4"></i>
                                    <span><?php echo e($group->leader->name); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 p-4 bg-slate-50/50">
                        <a href="<?php echo e(route('instructor.groups.show', $group)); ?>" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-eye"></i> <?php echo e(__('instructor.view_details')); ?>

                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm"><?php echo e($groups->links()); ?></div>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2"><?php echo e(__('instructor.no_groups')); ?></h3>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('instructor.no_groups_description')); ?></p>
            <a href="<?php echo e(route('instructor.groups.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> <?php echo e(__('instructor.create_new_group')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\groups\index.blade.php ENDPATH**/ ?>