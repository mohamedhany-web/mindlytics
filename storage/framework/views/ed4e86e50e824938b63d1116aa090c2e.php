<?php $__env->startSection('title', $group->name . ' - Mindlytics'); ?>
<?php $__env->startSection('header', $group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="space-y-6">
        <!-- الهيدر -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <nav class="text-sm text-slate-500 mb-2">
                        <a href="<?php echo e(route('instructor.groups.index')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('instructor.groups')); ?></a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-700 font-semibold"><?php echo e($group->name); ?></span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e($group->name); ?></h1>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
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
                    <p class="text-sm text-slate-600"><?php echo e($group->course->title ?? __('instructor.not_specified')); ?></p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="<?php echo e(route('instructor.groups.edit', $group)); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-edit"></i> <?php echo e(__('common.edit')); ?>

                    </a>
                    <a href="<?php echo e(route('instructor.groups.index')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                        <i class="fas fa-arrow-right"></i> <?php echo e(__('instructor.back')); ?>

                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="lg:col-span-2 space-y-6">
                <?php if($group->description): ?>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-3"><?php echo e(__('instructor.description')); ?></h3>
                    <p class="text-slate-600 leading-relaxed"><?php echo e($group->description); ?></p>
                </div>
                <?php endif; ?>

                <!-- الأعضاء -->
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800"><?php echo e(__('instructor.members_label')); ?></h3>
                        <span class="text-sm font-medium text-slate-600">
                            <?php echo e($group->members->count()); ?> / <?php echo e($group->max_members); ?>

                        </span>
                    </div>

                    <?php if($group->members->count() > 0): ?>
                        <ul class="space-y-2">
                            <?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold shrink-0">
                                        <?php echo e(mb_substr($member->name ?? '?', 0, 1)); ?>

                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 truncate"><?php echo e($member->name); ?></div>
                                        <div class="text-sm text-slate-500 truncate"><?php echo e($member->email ?? '—'); ?></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <?php if($member->pivot->role == 'leader'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                                            <i class="fas fa-crown"></i> <?php echo e(__('instructor.group_leader')); ?>

                                        </span>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('instructor.groups.remove-member', $group)); ?>" method="POST" class="inline"
                                          onsubmit="return confirm('<?php echo e(__('instructor.confirm_remove_member')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <input type="hidden" name="user_id" value="<?php echo e($member->id); ?>">
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="<?php echo e(__('instructor.remove_member_title')); ?>">
                                            <i class="fas fa-user-minus text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-8 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-slate-600 font-medium"><?php echo e(__('instructor.no_members_in_group')); ?></p>
                            <p class="text-sm text-slate-500 mt-1"><?php echo e(__('instructor.add_members_from_right')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- واجبات المجموعة -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800"><?php echo e(__('instructor.group_assignments_title')); ?></h3>
                    <a href="<?php echo e(route('instructor.assignments.create')); ?>?advanced_course_id=<?php echo e($group->course_id); ?>&group_id=<?php echo e($group->id); ?>"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-colors">
                        <i class="fas fa-plus"></i> <?php echo e(__('instructor.add_assignment_to_group')); ?>

                    </a>
                </div>
                <?php if(isset($groupAssignments) && $groupAssignments->count() > 0): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $groupAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="min-w-0 flex-1">
                                    <a href="<?php echo e(route('instructor.assignments.show', $a)); ?>" class="font-semibold text-slate-800 hover:text-sky-600 truncate block"><?php echo e($a->title); ?></a>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                        <?php if($a->due_date): ?>
                                            <span><i class="fas fa-calendar ml-1"></i> <?php echo e($a->due_date->format('Y/m/d')); ?></span>
                                        <?php endif; ?>
                                        <span><?php echo e($a->submissions_count ?? 0); ?> <?php echo e(__('instructor.submission_single')); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-semibold px-2 py-1 rounded
                                        <?php if($a->status == 'published'): ?> bg-emerald-100 text-emerald-700
                                        <?php elseif($a->status == 'draft'): ?> bg-slate-200 text-slate-600
                                        <?php else: ?> bg-slate-100 text-slate-500
                                        <?php endif; ?>"><?php echo e($a->status == 'published' ? __('instructor.published') : ($a->status == 'draft' ? __('instructor.draft') : __('instructor.archived'))); ?></span>
                                    <a href="<?php echo e(route('instructor.assignments.edit', $a)); ?>" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="<?php echo e(__('common.edit')); ?>"><i class="fas fa-edit text-sm"></i></a>
                                    <a href="<?php echo e(route('instructor.assignments.submissions', $a)); ?>" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="<?php echo e(__('instructor.submissions_title')); ?>"><i class="fas fa-inbox text-sm"></i></a>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-6 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <p class="text-slate-600 font-medium"><?php echo e(__('instructor.no_group_assignments')); ?></p>
                        <p class="text-sm text-slate-500 mt-1"><?php echo e(__('instructor.add_group_assignment_hint')); ?></p>
                        <a href="<?php echo e(route('instructor.assignments.create')); ?>?advanced_course_id=<?php echo e($group->course_id); ?>&group_id=<?php echo e($group->id); ?>"
                           class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold">
                            <i class="fas fa-plus"></i> <?php echo e(__('instructor.add_assignment_to_group')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4"><?php echo e(__('instructor.group_info_title')); ?></h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500 mb-0.5"><?php echo e(__('instructor.course_label')); ?></dt>
                            <dd class="font-medium text-slate-800"><?php echo e($group->course->title ?? '—'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5"><?php echo e(__('instructor.max_members_label')); ?></dt>
                            <dd class="font-medium text-slate-800"><?php echo e($group->max_members); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5"><?php echo e(__('instructor.current_members_count')); ?></dt>
                            <dd class="font-medium text-slate-800"><?php echo e($group->members->count()); ?></dd>
                        </div>
                        <?php if($group->leader): ?>
                        <div>
                            <dt class="text-slate-500 mb-0.5"><?php echo e(__('instructor.group_leader_label')); ?></dt>
                            <dd class="font-medium text-slate-800"><?php echo e($group->leader->name); ?></dd>
                        </div>
                        <?php endif; ?>
                        <div>
                            <dt class="text-slate-500 mb-0.5"><?php echo e(__('instructor.created_at_label')); ?></dt>
                            <dd class="font-medium text-slate-800"><?php echo e($group->created_at->format('Y/m/d')); ?></dd>
                        </div>
                    </dl>
                </div>

                <?php if(!$group->isFull()): ?>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4"><?php echo e(__('instructor.add_member_title')); ?></h3>
                    <form action="<?php echo e(route('instructor.groups.add-member', $group)); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="add_user_id" class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('instructor.students')); ?></label>
                            <select name="user_id" id="add_user_id" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value=""><?php echo e(__('instructor.choose_student_option')); ?></option>
                                <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!$group->members->contains($enrollment->user_id)): ?>
                                    <option value="<?php echo e($enrollment->user->id); ?>"><?php echo e($enrollment->user->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label for="add_role" class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('instructor.role_label')); ?></label>
                            <select name="role" id="add_role"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="member"><?php echo e(__('instructor.member_option')); ?></option>
                                <option value="leader"><?php echo e(__('instructor.leader_option')); ?></option>
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i> <?php echo e(__('instructor.add_btn')); ?>

                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\groups\show.blade.php ENDPATH**/ ?>