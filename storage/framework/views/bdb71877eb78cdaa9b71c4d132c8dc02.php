<?php $__env->startSection('title', __('student.exams_page_title')); ?>

<?php
    $completedExams = $availableExams->filter(function ($exam) {
        return $exam->last_attempt && $exam->last_attempt->status === 'completed';
    });
    $canAttemptCount = $availableExams->where('can_attempt', true)->count();
    $avgScore = $completedExams->where('best_score', '!=', null)->avg('best_score');
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .ex-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .ex-card {
        display: flex;
        flex-direction: column;
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        overflow: hidden;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease;
    }
    .ex-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    }
    .ex-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 16px;
        background: var(--ml-well);
        border-bottom: 1px solid var(--ml-line);
    }
    .ex-card-head h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
    }
    .ex-card-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
    .ex-course {
        margin: 0;
        font-size: 12px;
        color: var(--ml-muted);
        line-height: 1.5;
    }
    .ex-course strong { color: var(--ml-ink); font-weight: 700; }
    .ex-facts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }
    @media (min-width: 480px) {
        .ex-facts { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .ex-facts > div {
        padding: 8px 10px;
        border-radius: 10px;
        background: var(--ml-well);
    }
    .ex-facts .k { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 2px; }
    .ex-facts .v { font-size: 13px; font-weight: 700; color: var(--ml-ink); }
    .ex-attempt {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        background: rgba(73, 164, 162, 0.1);
        border: 1px solid rgba(73, 164, 162, 0.2);
        font-size: 12px;
        color: var(--ml-ink);
    }
    .ex-attempt .score { font-weight: 700; color: var(--ml-teal-deep); }
    .ex-desc {
        margin: 0;
        font-size: 12px;
        color: var(--ml-muted);
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ex-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid var(--ml-line);
        margin-top: auto;
    }
    .ex-times { font-size: 11px; color: var(--ml-muted); line-height: 1.5; }
    .ex-times span { color: var(--ml-ink); font-weight: 600; }
    .ex-lock {
        display: inline-flex; align-items: center; gap: 6px;
        min-height: 40px; padding: 0 14px; border-radius: 12px;
        font-size: 13px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .ex-lock.bad { background: rgba(239, 68, 68, 0.1); color: #b91c1c; }
    .ex-shield {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
        padding-top: 10px; border-top: 1px solid var(--ml-line);
        font-size: 11px; color: #92400e;
    }
    .ex-shield .tag {
        padding: 2px 8px; border-radius: 6px;
        background: rgba(245, 158, 11, 0.14); font-weight: 700;
    }
    .ex-done { display: flex; flex-direction: column; gap: 8px; }
    .ex-done-row {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: 12px; padding: 12px 14px;
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
    }
    .ex-done-row h4 { margin: 0 0 4px; font-size: 14px; font-weight: 700; }
    .ex-done-row .meta { margin: 0; font-size: 12px; color: var(--ml-muted); }
    .ex-score { text-align: center; }
    .ex-score .pct { display: block; font-size: 1.15rem; font-weight: 700; }
    .ex-score .pct.ok { color: #047857; }
    .ex-score .pct.bad { color: #b91c1c; }
    .ex-score .st { font-size: 11px; color: var(--ml-muted); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.exams_page_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.exams_page_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.exams_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($availableExams->count()); ?> <?php echo e(__('student.available')); ?></span>
            <?php if($canAttemptCount > 0): ?>
                <span class="oc-signal oc-signal-hot"><?php echo e($canAttemptCount); ?> <?php echo e(__('student.can_attempt_label')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <section class="oc-stage" aria-label="ملخص الامتحانات">
        <div class="oc-eyebrow">تقييمات الكورسات المفعّلة</div>
        <h2><?php echo e(__('student.exams_page_title')); ?></h2>
        <p class="oc-copy"><?php echo e(__('student.exams_subtitle')); ?></p>
        <div class="oc-nav">
            <a class="oc-btn oc-btn-quiet" href="<?php echo e(route('my-courses.index')); ?>">
                <i class="fas fa-book-open text-xs"></i> <?php echo e(__('student.my_courses_link')); ?>

            </a>
        </div>
    </section>

    <?php if($availableExams->count() > 0): ?>
        <div class="oc-pulse" aria-label="إحصائيات">
            <div>
                <span class="lbl"><?php echo e(__('student.available')); ?></span>
                <span class="val teal"><?php echo e($availableExams->count()); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.completed')); ?></span>
                <span class="val"><?php echo e($completedExams->count()); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.can_attempt_label')); ?></span>
                <span class="val hot"><?php echo e($canAttemptCount); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.avg_results_label')); ?></span>
                <span class="val"><?php echo e($avgScore ? number_format($avgScore, 1) : 0); ?>%</span>
            </div>
        </div>

        <p class="oc-section-title">الامتحانات المتاحة</p>
        <div class="ex-grid">
            <?php $__currentLoopData = $availableExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $courseTitle = $exam->offlineCourse->title ?? $exam->course->title ?? '—';
                    $isOffline = (bool) $exam->offline_course_id;
                ?>
                <article class="ex-card">
                    <div class="ex-card-head">
                        <h3><?php echo e($exam->title); ?></h3>
                        <?php if($exam->can_attempt): ?>
                            <span class="oc-badge oc-badge-ok"><?php echo e(__('student.available')); ?></span>
                        <?php else: ?>
                            <span class="oc-badge oc-badge-warn"><?php echo e(__('student.not_available_now')); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="ex-card-body">
                        <p class="ex-course">
                            <strong><?php echo e($courseTitle); ?></strong>
                            <?php if($isOffline): ?>
                                · <span class="oc-badge oc-badge-warn" style="vertical-align:middle"><?php echo e(__('student.offline_badge')); ?></span>
                            <?php elseif(optional($exam->course)->academicSubject): ?>
                                · <?php echo e($exam->course->academicSubject->name); ?>

                            <?php endif; ?>
                        </p>

                        <div class="ex-facts">
                            <div>
                                <span class="k"><?php echo e(__('student.duration_label')); ?></span>
                                <span class="v"><?php echo e($exam->duration_minutes); ?> <?php echo e(__('student.minutes')); ?></span>
                            </div>
                            <div>
                                <span class="k"><?php echo e(__('student.questions_label')); ?></span>
                                <span class="v"><?php echo e($exam->questions_count); ?></span>
                            </div>
                            <div>
                                <span class="k"><?php echo e(__('student.passing_marks_label')); ?></span>
                                <span class="v"><?php echo e($exam->passing_marks); ?>%</span>
                            </div>
                            <div>
                                <span class="k"><?php echo e(__('student.attempts_label')); ?></span>
                                <span class="v"><?php echo e($exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed); ?></span>
                            </div>
                        </div>

                        <?php if($exam->user_attempts > 0): ?>
                            <div class="ex-attempt">
                                <span>
                                    <?php echo e(__('student.your_attempts')); ?>:
                                    <strong><?php echo e($exam->user_attempts); ?></strong>
                                    <?php echo e(__('student.of_attempts')); ?>

                                    <?php echo e($exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed); ?>

                                </span>
                                <?php if($exam->best_score !== null): ?>
                                    <span class="score"><?php echo e(__('student.best_score_label')); ?>: <?php echo e(number_format($exam->best_score, 1)); ?>%</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($exam->description): ?>
                            <p class="ex-desc"><?php echo e($exam->description); ?></p>
                        <?php endif; ?>

                        <div class="ex-foot">
                            <div class="ex-times">
                                <?php if($exam->start_time): ?>
                                    <?php echo e(__('student.starts_at')); ?>: <span><?php echo e($exam->start_time->format('Y-m-d H:i')); ?></span>
                                <?php endif; ?>
                                <?php if($exam->end_time): ?>
                                    <?php if($exam->start_time): ?><br><?php endif; ?>
                                    <?php echo e(__('student.ends_at')); ?>: <span><?php echo e($exam->end_time->format('Y-m-d H:i')); ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if($exam->can_attempt): ?>
                                    <a href="<?php echo e(route('student.exams.show', $exam)); ?>" class="oc-btn">
                                        <i class="fas fa-play text-xs"></i>
                                        <?php echo e(__('student.start_exam')); ?>

                                    </a>
                                <?php elseif($exam->user_attempts >= $exam->attempts_allowed && $exam->attempts_allowed > 0): ?>
                                    <span class="ex-lock bad">
                                        <i class="fas fa-ban text-xs"></i>
                                        <?php echo e(__('student.attempts_exhausted')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="ex-lock">
                                        <i class="fas fa-lock text-xs"></i>
                                        <?php echo e(__('student.not_available_now')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone): ?>
                            <div class="ex-shield">
                                <strong><i class="fas fa-shield-alt"></i> <?php echo e(__('student.protected_exam')); ?>:</strong>
                                <?php if($exam->prevent_tab_switch): ?><span class="tag"><?php echo e(__('student.no_tab_switch')); ?></span><?php endif; ?>
                                <?php if($exam->require_camera): ?><span class="tag"><?php echo e(__('student.camera_label')); ?></span><?php endif; ?>
                                <?php if($exam->require_microphone): ?><span class="tag"><?php echo e(__('student.microphone_label')); ?></span><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($completedExams->count() > 0): ?>
            <p class="oc-section-title"><?php echo e(__('student.completed_exams_title')); ?></p>
            <div class="ex-done">
                <?php $__currentLoopData = $completedExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="ex-done-row">
                        <div class="min-w-0">
                            <h4><?php echo e($exam->title); ?></h4>
                            <p class="meta">
                                <?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?>

                                · <?php echo e($exam->last_attempt->created_at->diffForHumans()); ?>

                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="ex-score">
                                <span class="pct <?php echo e($exam->last_attempt->result_color == 'green' ? 'ok' : 'bad'); ?>">
                                    <?php echo e(number_format($exam->last_attempt->percentage, 1)); ?>%
                                </span>
                                <span class="st"><?php echo e($exam->last_attempt->result_status); ?></span>
                            </div>
                            <?php if($exam->show_results_immediately): ?>
                                <a href="<?php echo e(route('student.exams.result', [$exam, $exam->last_attempt])); ?>" class="oc-chip">
                                    <i class="fas fa-chart-line"></i>
                                    <?php echo e(__('student.view_result')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-clipboard-check"></i></div>
            <h3><?php echo e(__('student.no_exams_available')); ?></h3>
            <p><?php echo e(__('student.no_exams_desc')); ?></p>
            <div style="margin-top:16px">
                <a href="<?php echo e(route('my-courses.index')); ?>" class="oc-btn">
                    <i class="fas fa-book-open text-xs"></i>
                    <?php echo e(__('student.view_my_courses')); ?>

                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/exams/index.blade.php ENDPATH**/ ?>