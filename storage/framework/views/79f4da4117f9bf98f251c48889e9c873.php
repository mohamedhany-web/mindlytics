<?php $__env->startSection('title', __('instructor.submissions_of') . ': ' . $assignment->title . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.submissions_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="hover:text-sky-600"><?php echo e(__('instructor.assignments')); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.assignments.show', $assignment)); ?>" class="hover:text-sky-600"><?php echo e($assignment->title); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e(__('instructor.submissions_title')); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800"><?php echo e(__('instructor.submissions_of')); ?>: <?php echo e($assignment->title); ?></h1>
                <p class="text-sm text-slate-600 mt-0.5"><?php echo e(__('instructor.max_score_points')); ?>: <?php echo e($assignment->max_score); ?></p>
            </div>
            <a href="<?php echo e(route('instructor.assignments.show', $assignment)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                <i class="fas fa-arrow-right"></i> <?php echo e(__('instructor.back_to_assignment')); ?>

            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 font-bold text-slate-800"><?php echo e(__('instructor.submissions_list')); ?></div>
        <div class="overflow-x-auto">
            <?php if($submissions->count() > 0): ?>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.student_or_group')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.submission_date')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.score_label')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('common.status')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3 text-sm text-slate-800">
                                    <?php if($sub->group_id && $sub->relationLoaded('group')): ?>
                                        <span class="text-sky-600"><?php echo e($sub->group->name ?? __('instructor.group_fallback')); ?></span>
                                        <span class="text-slate-500">(<?php echo e($sub->student->name ?? '—'); ?>)</span>
                                    <?php else: ?>
                                        <?php echo e($sub->student->name ?? '—'); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($sub->submitted_at?->format('Y/m/d H:i')); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <?php if($sub->score !== null): ?>
                                        <span class="font-semibold text-slate-800"><?php echo e($sub->score); ?>/<?php echo e($assignment->max_score); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($sub->status === 'graded'): ?>
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-emerald-100 text-emerald-700"><?php echo e(__('instructor.graded_status')); ?></span>
                                    <?php elseif($sub->status === 'returned'): ?>
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-sky-100 text-sky-700"><?php echo e(__('instructor.returned_status')); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-amber-100 text-amber-700"><?php echo e(__('instructor.pending_review')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" onclick="toggleDetail(<?php echo e($sub->id); ?>)" class="text-sky-600 hover:text-sky-700 text-sm font-semibold">
                                        <i class="fas fa-eye"></i> <?php echo e(__('instructor.view_grade')); ?>

                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-<?php echo e($sub->id); ?>" class="hidden bg-slate-50/70">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="space-y-4 max-w-3xl">
                                        <?php if($sub->content): ?>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1"><?php echo e(__('instructor.content_label')); ?></h4>
                                                <div class="text-sm text-slate-600 whitespace-pre-wrap rounded-lg bg-white p-3 border border-slate-200"><?php echo e($sub->content); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($sub->attachments && count($sub->attachments) > 0): ?>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1"><?php echo e(__('instructor.attachments_label')); ?></h4>
                                                <ul class="text-sm space-y-1">
                                                    <?php $__currentLoopData = $sub->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                                            $url = $path ? (str_starts_with($path, 'http') ? $path : url('storage/' . $path)) : '#';
                                                            $label = is_array($att) ? ($att['name'] ?? basename($path ?? __('instructor.attachment_fallback'))) : basename($att);
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="text-sky-600 hover:underline">
                                                                <?php echo e($label); ?>

                                                            </a>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($sub->feedback): ?>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1"><?php echo e(__('instructor.feedback_label')); ?></h4>
                                                <p class="text-sm text-slate-600"><?php echo e($sub->feedback); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('instructor.assignments.grade', [$assignment, $sub])); ?>" method="POST" class="flex flex-wrap items-end gap-4 pt-2 border-t border-slate-200">
                                            <?php echo csrf_field(); ?>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.score_label')); ?> (0–<?php echo e($assignment->max_score); ?>)</label>
                                                <input type="number" name="score" min="0" max="<?php echo e($assignment->max_score); ?>" value="<?php echo e(old('score', $sub->score)); ?>" class="w-24 px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                            </div>
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.feedback_label')); ?></label>
                                                <input type="text" name="feedback" value="<?php echo e(old('feedback', $sub->feedback)); ?>" placeholder="<?php echo e(__('instructor.optional_comment')); ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('common.status')); ?></label>
                                                <select name="status" class="px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                                    <option value="submitted" <?php echo e($sub->status === 'submitted' ? 'selected' : ''); ?>><?php echo e(__('instructor.pending_review')); ?></option>
                                                    <option value="graded" <?php echo e($sub->status === 'graded' ? 'selected' : ''); ?>><?php echo e(__('instructor.graded_status')); ?></option>
                                                    <option value="returned" <?php echo e($sub->status === 'returned' ? 'selected' : ''); ?>><?php echo e(__('instructor.returned_status')); ?></option>
                                                </select>
                                            </div>
                                            <button type="submit" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm">
                                                <i class="fas fa-check"></i> <?php echo e(__('instructor.save_grade')); ?>

                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="p-3 border-t border-slate-200"><?php echo e($submissions->links()); ?></div>
            <?php else: ?>
                <p class="p-8 text-center text-slate-500"><?php echo e(__('instructor.no_submissions_yet')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($submissions->count() > 0): ?>
<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('hidden');
}
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/assignments/submissions.blade.php ENDPATH**/ ?>