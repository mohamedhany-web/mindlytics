

<?php $__env->startSection('title', $assignment->title); ?>

<?php
    $statusKey = match ($submission?->status) {
        'graded' => 'student.assignment_status_graded',
        'submitted' => 'student.assignment_status_submitted',
        'draft' => 'student.assignment_status_draft',
        default => null,
    };
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .as-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 999px) {
        .as-layout { grid-template-columns: 1fr; }
    }
    .as-prose {
        margin: 0; font-size: 14px; line-height: 1.75; color: var(--ml-ink);
        white-space: pre-wrap; word-break: break-word;
    }
    .as-instruct {
        margin-top: 12px; padding: 14px 16px; border-radius: var(--ml-r);
        border: 1px solid rgba(73, 164, 162, 0.3);
        background: rgba(73, 164, 162, 0.08);
        font-size: 13px; line-height: 1.7; color: var(--ml-ink);
        white-space: pre-wrap;
    }
    .as-form label {
        display: block; margin-bottom: 6px;
        font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .as-form textarea,
    .as-form input[type="file"] {
        width: 100%; border: 1px solid var(--ml-line); border-radius: 12px;
        background: var(--ml-surface); color: var(--ml-ink);
        font-family: inherit; font-size: 13px;
    }
    .as-form textarea {
        padding: 12px 14px; min-height: 120px; resize: vertical; line-height: 1.6;
    }
    .as-form textarea:focus {
        outline: none; border-color: rgba(73, 164, 162, 0.55);
        box-shadow: 0 0 0 3px rgba(73, 164, 162, 0.15);
    }
    .as-form .hint { margin: 6px 0 0; font-size: 11px; color: var(--ml-muted); }
    .as-form .field { margin-bottom: 14px; }
    .as-sub-meta { font-size: 13px; color: var(--ml-muted); line-height: 1.7; }
    .as-sub-meta strong { color: var(--ml-ink); }
    .as-attach { list-style: none; margin: 10px 0 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
    .as-attach a {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 600; color: var(--ml-teal-deep); text-decoration: none;
    }
    .as-attach a:hover { text-decoration: underline; }
    .as-feedback {
        margin-top: 10px; padding: 12px 14px; border-radius: 10px;
        background: var(--ml-well); border: 1px solid var(--ml-line);
        font-size: 13px; line-height: 1.65; white-space: pre-wrap; color: var(--ml-ink);
    }
    .as-aside-sticky { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1000px) {
        .as-aside-sticky { position: sticky; top: 12px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <?php if(session('success')): ?>
        <div class="oc-panel" style="border-color:rgba(16,185,129,0.35);background:rgba(16,185,129,0.08);margin-bottom:16px;color:#047857;font-size:13px;font-weight:600">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px;font-weight:600">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.assignments_page_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route('student.assignments.index')); ?>"><?php echo e(__('student.assignments_page_title')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(\Illuminate\Support\Str::limit($assignment->title, 36)); ?></span>
            </nav>
            <h1><?php echo e($assignment->title); ?></h1>
            <p class="sub"><?php echo e($assignment->course->title ?? '—'); ?></p>
        </div>
        <div class="oc-signals">
            <?php if($submission): ?>
                <span class="oc-signal oc-signal-live"><?php echo e(__('student.assignment_submitted')); ?></span>
            <?php else: ?>
                <span class="oc-signal oc-signal-hot"><?php echo e(__('student.assignment_pending')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="as-layout">
        <div>
            <section class="oc-panel" aria-labelledby="as-details">
                <p class="oc-label" id="as-details"><?php echo e(__('student.assignment_details')); ?></p>
                <?php if($assignment->description): ?>
                    <p class="oc-label" style="margin-top:4px"><?php echo e(__('student.assignment_description')); ?></p>
                    <p class="as-prose"><?php echo e($assignment->description); ?></p>
                <?php endif; ?>
                <?php if($assignment->instructions): ?>
                    <p class="oc-label" style="margin-top:14px"><?php echo e(__('student.assignment_instructions')); ?></p>
                    <div class="as-instruct"><?php echo e($assignment->instructions); ?></div>
                <?php endif; ?>
                <ul class="oc-facts" style="margin-top:16px">
                    <li>
                        <span class="k"><?php echo e(__('student.assignment_score_label')); ?></span>
                        <span class="v"><?php echo e($assignment->max_score); ?></span>
                    </li>
                    <li>
                        <span class="k"><?php echo e(__('student.assignment_due_label')); ?></span>
                        <span class="v"><?php echo e($assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : __('student.not_specified')); ?></span>
                    </li>
                    <li>
                        <span class="k"><?php echo e(__('student.assignment_late_allowed')); ?></span>
                        <span class="v"><?php echo e($assignment->allow_late_submission ? __('student.assignment_late_yes') : __('student.assignment_late_no')); ?></span>
                    </li>
                    <?php if($assignment->lesson): ?>
                        <li>
                            <span class="k"><?php echo e(__('student.assignment_lesson_label')); ?></span>
                            <span class="v"><?php echo e($assignment->lesson->title); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </section>

            <section class="oc-panel" aria-labelledby="as-submit">
                <p class="oc-label" id="as-submit"><?php echo e(__('student.assignment_submit_section')); ?></p>
                <form method="POST" action="<?php echo e(route('student.assignments.submit', $assignment)); ?>" enctype="multipart/form-data" class="as-form">
                    <?php echo csrf_field(); ?>
                    <div class="field">
                        <label for="as-content"><?php echo e(__('student.assignment_content_label')); ?></label>
                        <textarea id="as-content" name="content" rows="5" placeholder="<?php echo e(__('student.assignment_content_placeholder')); ?>"><?php echo e(old('content', $submission->content ?? '')); ?></textarea>
                        <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="hint" style="color:#b91c1c"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="as-files"><?php echo e(__('student.assignment_attachments_label')); ?></label>
                        <input id="as-files" type="file" name="attachments[]" multiple />
                        <p class="hint"><?php echo e(__('student.assignment_attachments_hint')); ?></p>
                        <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="hint" style="color:#b91c1c"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="oc-btn">
                        <i class="fas fa-upload text-xs"></i>
                        <?php echo e(__('student.assignment_submit_btn')); ?>

                    </button>
                </form>

                <?php if($submission): ?>
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--ml-line)">
                        <p class="oc-label"><?php echo e(__('student.assignment_last_submission')); ?></p>
                        <div class="as-sub-meta">
                            <p>
                                <?php echo e(__('student.assignment_status_label')); ?>:
                                <strong><?php echo e($statusKey ? __($statusKey) : $submission->status); ?></strong>
                            </p>
                            <?php if($submission->submitted_at): ?>
                                <p><?php echo e(__('student.assignment_submitted_at')); ?>: <strong><?php echo e($submission->submitted_at->format('Y-m-d H:i')); ?></strong></p>
                            <?php endif; ?>
                            <?php if($submission->score !== null): ?>
                                <p>
                                    <?php echo e(__('student.assignment_score_label')); ?>:
                                    <strong style="color:var(--ml-teal-deep)"><?php echo e($submission->score); ?></strong>
                                    / <?php echo e($assignment->max_score); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                        <?php if($submission->feedback): ?>
                            <p class="oc-label" style="margin-top:12px"><?php echo e(__('student.assignment_feedback')); ?></p>
                            <div class="as-feedback"><?php echo e($submission->feedback); ?></div>
                        <?php endif; ?>
                        <?php if($submission->attachments && count($submission->attachments)): ?>
                            <p class="oc-label" style="margin-top:12px"><?php echo e(__('student.assignment_attachments_list')); ?></p>
                            <ul class="as-attach">
                                <?php $__currentLoopData = $submission->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                        $url = is_array($att) && !empty($att['url']) ? $att['url'] : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/'.$path)) : '#');
                                        $name = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                                    ?>
                                    <li>
                                        <a href="<?php echo e($url); ?>" target="_blank" rel="noopener">
                                            <i class="fas fa-paperclip text-xs"></i> <?php echo e($name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <aside class="as-aside-sticky">
            <div class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.assignment_course_label')); ?></p>
                <p style="margin:0;font-size:14px;font-weight:700;line-height:1.4"><?php echo e($assignment->course->title ?? '—'); ?></p>
                <?php if($assignment->teacher): ?>
                    <p style="margin:8px 0 0;font-size:12px;color:var(--ml-muted)"><?php echo e($assignment->teacher->name); ?></p>
                <?php endif; ?>
            </div>
            <div class="oc-panel">
                <a href="<?php echo e(route('student.assignments.index')); ?>" class="oc-btn oc-btn-quiet" style="width:100%">
                    <i class="fas fa-arrow-right text-xs"></i>
                    <?php echo e(__('student.assignment_back')); ?>

                </a>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\assignments\show.blade.php ENDPATH**/ ?>