<?php $__env->startSection('title', $offlineCourse->title); ?>

<?php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
    $isRtl = app()->getLocale() === 'ar';

    $resourcesCount = $offlineCourse->resources()
        ->active()
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $lecturesCount = $offlineCourse->offlineLectures()
        ->active()
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $activitiesCount = $offlineCourse->activities()
        ->where('status', 'published')
        ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
            $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
        }))
        ->count();

    $examsCount = \App\Models\AdvancedExam::query()
        ->where('offline_course_id', $offlineCourse->id)
        ->where('is_active', true)
        ->where('is_published', true)
        ->count();
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.oc_breadcrumb')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('los.page_title')); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route($sg . '.index')); ?>"><?php echo e($listTitle); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->title, 36)); ?></span>
            </nav>
            <h1><?php echo e($offlineCourse->title); ?></h1>
            <p class="sub">
                <?php echo e($offlineCourse->instructor->name ?? '—'); ?>

                <?php if($enrollment->group): ?>
                    · <?php echo e($enrollment->group->name); ?>

                <?php endif; ?>
            </p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($channelLabel); ?></span>
            <span class="oc-signal"><?php echo e(__('student.oc_progress', ['pct' => number_format($enrollment->progress, 0)])); ?></span>
        </div>
    </header>

    <section class="oc-stage" aria-label="<?php echo e(__('student.oc_overview')); ?>">
        <div class="oc-eyebrow"><?php echo e(__('student.oc_course_space')); ?> <em><?php echo e($channelLabel); ?></em></div>
        <h2><?php echo e($offlineCourse->title); ?></h2>
        <?php if(filled($offlineCourse->description)): ?>
            <p class="oc-copy"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->description, 220)); ?></p>
        <?php endif; ?>
        <div class="oc-meter" role="progressbar" aria-valuenow="<?php echo e((int) $enrollment->progress); ?>" aria-valuemin="0" aria-valuemax="100">
            <i style="width:<?php echo e(min(100, (float) $enrollment->progress)); ?>%"></i>
        </div>
        <div class="oc-nav">
            <a class="oc-chip" href="<?php echo e(route($sg . '.curriculum', $offlineCourse)); ?>"><i class="fas fa-sitemap"></i> <?php echo e(__('student.oc_curriculum')); ?></a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>"><i class="fas fa-calendar-alt"></i> <?php echo e(__('student.oc_schedule')); ?></a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>"><i class="fas fa-file-alt"></i> <?php echo e(__('student.oc_resources')); ?></a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>"><i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('student.oc_lectures')); ?></a>
            <a class="oc-chip" href="<?php echo e(route('student.exams.index')); ?>"><i class="fas fa-clipboard-check"></i> <?php echo e(__('student.oc_exams')); ?></a>
        </div>
    </section>

    <ul class="oc-facts" style="margin-bottom:20px">
        <li>
            <span class="k"><?php echo e(__('student.oc_instructor')); ?></span>
            <span class="v"><?php echo e($offlineCourse->instructor->name ?? '—'); ?></span>
        </li>
        <?php if($offlineCourse->locationModel || $offlineCourse->location): ?>
            <li>
                <span class="k"><?php echo e($isOnline ? __('student.oc_platform_location') : __('student.oc_location')); ?></span>
                <span class="v"><?php echo e($offlineCourse->locationModel->name ?? $offlineCourse->location ?? '—'); ?></span>
            </li>
        <?php endif; ?>
        <?php if($offlineCourse->start_date): ?>
            <li>
                <span class="k"><?php echo e(__('student.oc_start_date')); ?></span>
                <span class="v"><?php echo e($offlineCourse->start_date->format('Y-m-d')); ?></span>
            </li>
        <?php endif; ?>
        <?php if($enrollment->group): ?>
            <li>
                <span class="k"><?php echo e(__('student.oc_group')); ?></span>
                <span class="v"><?php echo e($enrollment->group->name); ?></span>
            </li>
        <?php endif; ?>
    </ul>

    <div class="oc-hub" aria-label="<?php echo e(__('student.oc_sections')); ?>">
        <a href="<?php echo e(route($sg . '.curriculum', $offlineCourse)); ?>">
            <span class="ico"><i class="fas fa-sitemap"></i></span>
            <strong><?php echo e(__('student.oc_hub_curriculum')); ?></strong>
            <span><?php echo e(__('student.oc_hub_curriculum_desc')); ?></span>
        </a>
        <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>">
            <span class="ico"><i class="fas fa-calendar-alt"></i></span>
            <strong><?php echo e(__('student.oc_hub_schedule')); ?></strong>
            <span><?php echo e(__('student.oc_hub_schedule_desc')); ?></span>
        </a>
        <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>">
            <span class="count"><?php echo e($resourcesCount); ?></span>
            <span class="ico"><i class="fas fa-file-alt"></i></span>
            <strong><?php echo e(__('student.oc_resources')); ?></strong>
            <span><?php echo e(__('student.oc_hub_resources_desc')); ?></span>
        </a>
        <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>">
            <span class="count"><?php echo e($lecturesCount); ?></span>
            <span class="ico"><i class="fas fa-chalkboard-teacher"></i></span>
            <strong><?php echo e(__('student.oc_lectures')); ?></strong>
            <span><?php echo e(__('student.oc_hub_lectures_desc')); ?></span>
        </a>
        <a href="#activities-required">
            <span class="count"><?php echo e($activitiesCount); ?></span>
            <span class="ico"><i class="fas fa-tasks"></i></span>
            <strong><?php echo e(__('student.oc_hub_activities')); ?></strong>
            <span><?php echo e(__('student.oc_hub_activities_desc')); ?></span>
        </a>
        <a href="<?php echo e(route('student.exams.index')); ?>">
            <span class="count"><?php echo e($examsCount); ?></span>
            <span class="ico"><i class="fas fa-clipboard-check"></i></span>
            <strong><?php echo e(__('student.oc_exams')); ?></strong>
            <span><?php echo e(__('student.oc_hub_exams_desc')); ?></span>
        </a>
    </div>

    <?php if($enrollment->group): ?>
        <div class="oc-panel" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
            <div>
                <p class="oc-label" style="margin-bottom:4px"><?php echo e(__('student.oc_sessions_schedule')); ?></p>
                <p style="margin:0;font-size:14px;font-weight:700">
                    <?php echo e(__('student.oc_group_name', ['name' => $enrollment->group->name])); ?>

                    <?php if(($enrollment->group->sessions_count ?? 0) > 0): ?>
                        — <?php echo e(__('student.oc_sessions_count', ['count' => $enrollment->group->sessions_count])); ?>

                    <?php endif; ?>
                </p>
            </div>
            <a class="oc-btn" href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>">
                <i class="fas fa-calendar-alt text-xs"></i> <?php echo e(__('student.oc_open_calendar')); ?>

            </a>
        </div>
    <?php endif; ?>

    <?php if((float) $enrollment->total_amount > 0): ?>
        <div class="oc-panel">
            <p class="oc-label"><?php echo e(__('student.oc_payment_status')); ?></p>
            <?php
                $pTexts = [
                    'paid' => __('student.oc_payment_paid'),
                    'partial' => __('student.oc_payment_partial'),
                    'unpaid' => __('student.oc_payment_unpaid'),
                ];
                $pBadge = ['paid' => 'oc-badge-ok', 'partial' => 'oc-badge-warn', 'unpaid' => 'oc-badge-bad'];
            ?>
            <div class="oc-facts">
                <li>
                    <span class="k"><?php echo e(__('student.oc_status')); ?></span>
                    <span class="v"><span class="oc-badge <?php echo e($pBadge[$enrollment->payment_status] ?? ''); ?>"><?php echo e($pTexts[$enrollment->payment_status] ?? '—'); ?></span></span>
                </li>
                <li>
                    <span class="k"><?php echo e(__('student.oc_paid_amount')); ?></span>
                    <span class="v"><?php echo e(number_format($enrollment->paid_amount, 2)); ?> <?php echo e(__('student.oc_currency')); ?></span>
                </li>
                <li>
                    <span class="k"><?php echo e(__('student.oc_remaining')); ?></span>
                    <span class="v" style="color:<?php echo e((float) $enrollment->remaining_amount > 0 ? '#b91c1c' : '#047857'); ?>">
                        <?php echo e(number_format($enrollment->remaining_amount, 2)); ?> <?php echo e(__('student.oc_currency')); ?>

                    </span>
                </li>
            </div>
        </div>
    <?php endif; ?>

    <div id="activities-required">
        <p class="oc-section-title"><?php echo e(__('student.oc_required_activities')); ?></p>
        <?php if($pendingActivities->count() > 0): ?>
            <div class="oc-list">
                <?php $__currentLoopData = $pendingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route($sg . '.activities.show', [$offlineCourse, $activity])); ?>" class="oc-row">
                        <div class="oc-ico warn"><i class="fas fa-tasks"></i></div>
                        <div class="oc-body">
                            <h3><?php echo e($activity->title); ?></h3>
                            <p class="meta">
                                <?php echo e($activity->type); ?>

                                <?php if($activity->due_date): ?> · <?php echo e($activity->due_date->format('Y-m-d')); ?> <?php endif; ?>
                                · <?php echo e(__('student.oc_points', ['count' => $activity->max_score])); ?>

                            </p>
                        </div>
                        <span class="oc-side"><?php echo e(__('student.oc_submit')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[10px]"></i></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="oc-panel" style="font-size:13px;color:var(--ml-muted)"><?php echo e(__('student.oc_no_required_activities')); ?></div>
        <?php endif; ?>
    </div>

    <?php if($completedActivities->count() > 0): ?>
        <p class="oc-section-title" style="margin-top:20px"><?php echo e(__('student.oc_completed_activities')); ?></p>
        <div class="oc-list">
            <?php $__currentLoopData = $completedActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $submission = $activity->submissions->firstWhere('student_id', auth()->id()); ?>
                <a href="<?php echo e(route($sg . '.activities.show', [$offlineCourse, $activity])); ?>" class="oc-row">
                    <div class="oc-ico" style="background:rgba(16,185,129,0.12);color:#047857"><i class="fas fa-check"></i></div>
                    <div class="oc-body">
                        <h3><?php echo e($activity->title); ?></h3>
                        <?php if($submission && $submission->score !== null): ?>
                            <p class="meta"><?php echo e(__('student.oc_graded', ['score' => $submission->score, 'max' => $activity->max_score])); ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="oc-side"><?php echo e(__('student.oc_view')); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/show.blade.php ENDPATH**/ ?>