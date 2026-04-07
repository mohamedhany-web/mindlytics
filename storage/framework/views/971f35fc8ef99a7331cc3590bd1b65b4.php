<?php $__env->startSection('title', __('student.my_groups_title') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('student.my_groups_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800"><?php echo e(__('student.my_groups_title')); ?></h1>
                <p class="text-sm text-slate-600 mt-0.5"><?php echo e(__('student.my_groups_subtitle')); ?></p>
            </div>
        </div>
    </div>

    <?php if($groups->isEmpty()): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <p class="text-slate-600 font-medium"><?php echo e(__('student.no_groups')); ?></p>
            <p class="text-sm text-slate-500 mt-1"><?php echo e(__('student.no_groups_desc')); ?></p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('student.groups.show', $group)); ?>"
                   class="block rounded-2xl bg-white border border-slate-200 shadow-sm p-5 hover:border-sky-200 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-800 truncate"><?php echo e($group->name); ?></h3>
                            <p class="text-sm text-slate-500 mt-0.5"><?php echo e($group->course->title ?? '—'); ?></p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-slate-500">
                                    <?php echo e($group->members->count()); ?> / <?php echo e($group->max_members); ?> <?php echo e(__('student.member_singular')); ?>

                                </span>
                                <?php if($group->leader): ?>
                                    <span class="text-xs text-amber-600"><?php echo e(__('student.leader_label')); ?>: <?php echo e($group->leader->name); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <i class="fas fa-chevron-left text-slate-400 shrink-0 mt-1"></i>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/groups/index.blade.php ENDPATH**/ ?>