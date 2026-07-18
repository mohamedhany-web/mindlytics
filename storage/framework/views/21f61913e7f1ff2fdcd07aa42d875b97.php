

<?php $__env->startSection('title', __('student.oc_curriculum_page_title', ['title' => $offlineCourse->title])); ?>

<?php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
    $stats = $curriculumStats ?? ['sections' => 0, 'items' => 0];
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .oc-cur-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }
    .oc-cur-main { min-width: 0; display: flex; flex-direction: column; gap: 16px; }
    .oc-cur-aside { min-width: 0; display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1100px) {
        .oc-cur-aside-sticky { position: sticky; top: 12px; }
    }
    @media (max-width: 1099px) {
        .oc-cur-layout { grid-template-columns: 1fr; }
    }

    .oc-prose {
        margin: 0;
        font-size: 14px;
        line-height: 1.75;
        color: var(--ml-ink);
        white-space: pre-wrap;
        word-break: break-word;
    }
    .oc-note {
        padding: 14px 16px;
        border-radius: var(--ml-r);
        border: 1px solid rgba(255, 210, 63, 0.45);
        background: rgba(255, 210, 63, 0.12);
        font-size: 13px;
        line-height: 1.7;
        color: var(--ml-yellow-ink);
        white-space: pre-wrap;
    }

    .oc-instructor {
        display: flex; flex-direction: column; align-items: center; text-align: center; gap: 10px;
    }
    .oc-instructor img, .oc-instructor .av {
        width: 88px; height: 88px; border-radius: 18px; object-fit: cover;
        border: 1px solid var(--ml-line);
    }
    .oc-instructor .av {
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(145deg, #49A4A2, #2f7f7d); color: #fff;
        font-size: 1.75rem; font-weight: 800;
    }
    .oc-instructor strong { font-size: 15px; }
    .oc-instructor p { margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.55; }

    .oc-quick a {
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        padding: 11px 12px; border-radius: 10px; text-decoration: none !important;
        border: 1px solid var(--ml-line); background: var(--ml-well);
        color: var(--ml-ink) !important; font-size: 13px; font-weight: 700;
        transition: background var(--ml-fast) ease, border-color var(--ml-fast) ease;
    }
    .oc-quick a:hover {
        background: rgba(73, 164, 162, 0.1);
        border-color: rgba(73, 164, 162, 0.35);
    }
    .oc-quick a span { display: inline-flex; align-items: center; gap: 8px; }
    .oc-quick a i.lead { color: var(--ml-teal-deep); width: 1rem; text-align: center; }
    .oc-quick a i.chev { font-size: 10px; color: var(--ml-muted); }

    /* Structure tree */
    .oc-tree { display: flex; flex-direction: column; gap: 10px; }
    .oc-sec {
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        background: var(--ml-surface);
        overflow: hidden;
    }
    .oc-sec[style*="margin-inline-start"] { border-inline-start: 3px solid rgba(73,164,162,0.35); }
    .oc-sec > details { margin: 0; }
    .oc-sec-sum {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 12px 14px; cursor: pointer; list-style: none;
        background: var(--ml-well);
        transition: background var(--ml-fast) ease;
    }
    .oc-sec-sum::-webkit-details-marker { display: none; }
    .oc-sec-sum::marker { content: ''; display: none; }
    .oc-sec-sum:hover { background: rgba(73, 164, 162, 0.08); }
    .oc-sec-ico {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep);
    }
    .oc-sec-body { min-width: 0; flex: 1; }
    .oc-sec-body .tag {
        display: inline-flex; margin-bottom: 4px; padding: 1px 7px; border-radius: 6px;
        font-size: 10px; font-weight: 700; background: rgba(26,34,56,0.06); color: var(--ml-muted);
    }
    .oc-sec-body h3 { margin: 0; font-size: 14px; font-weight: 700; line-height: 1.35; }
    .oc-sec-body p { margin: 4px 0 0; font-size: 12px; color: var(--ml-muted); line-height: 1.5; }
    .oc-sec-meta {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px; flex-shrink: 0;
    }
    .oc-sec-meta .pill {
        display: inline-flex; align-items: center; gap: 4px; min-height: 24px; padding: 0 8px;
        border-radius: 6px; font-size: 11px; font-weight: 700;
        background: var(--ml-surface); border: 1px solid var(--ml-line); color: var(--ml-muted);
    }
    .oc-sec-meta .chev {
        width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--ml-line);
        background: var(--ml-surface); display: flex; align-items: center; justify-content: center;
        color: var(--ml-muted); transition: transform 0.2s ease;
    }
    details[open] > .oc-sec-sum .chev { transform: rotate(180deg); color: var(--ml-teal-deep); }

    .oc-items { list-style: none; margin: 0; padding: 0; }
    .oc-item {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 12px 14px; border-top: 1px solid var(--ml-line);
        text-decoration: none !important; color: inherit !important;
        transition: background var(--ml-fast) ease;
    }
    .oc-item:hover { background: rgba(73, 164, 162, 0.06); }
    .oc-item .dot {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 12px; color: #fff;
    }
    .oc-item .dot.lecture { background: var(--ml-teal); }
    .oc-item .dot.resource { background: #64748b; }
    .oc-item .dot.activity { background: #d97706; }
    .oc-item .dot.exam { background: #059669; }
    .oc-item .dot.note { background: #94a3b8; }
    .oc-item .txt { min-width: 0; flex: 1; }
    .oc-item .txt .kind { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 2px; text-transform: uppercase; }
    .oc-item .txt strong { display: block; font-size: 13px; font-weight: 700; line-height: 1.35; }
    .oc-item .txt p { margin: 4px 0 0; font-size: 12px; color: var(--ml-muted); line-height: 1.5; white-space: pre-line; }
    .oc-item .go { font-size: 11px; font-weight: 700; color: var(--ml-teal-deep); white-space: nowrap; align-self: center; }
    .oc-children { padding: 0 10px 10px; display: flex; flex-direction: column; gap: 8px; }
</style>
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
                <a href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->title, 28)); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.oc_curriculum')); ?></span>
            </nav>
            <h1><?php echo e(__('student.oc_curriculum')); ?></h1>
            <p class="sub"><?php echo e($offlineCourse->title); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($channelLabel); ?></span>
            <span class="oc-signal"><?php echo e(__('student.oc_sections_count', ['count' => $stats['sections']])); ?></span>
            <span class="oc-signal oc-signal-hot"><?php echo e(__('student.oc_items_count', ['count' => $stats['items']])); ?></span>
        </div>
    </header>

    <section class="oc-stage" aria-label="<?php echo e(__('student.oc_curriculum_summary_aria')); ?>">
        <div class="oc-eyebrow"><?php echo e(__('student.oc_curriculum_eyebrow')); ?> <em><?php echo e($channelLabel); ?></em></div>
        <h2><?php echo e($offlineCourse->title); ?></h2>
        <p class="oc-copy">
            <?php if($enrollment->group): ?>
                <?php echo e(__('student.oc_your_group', ['name' => $enrollment->group->name])); ?> —
            <?php endif; ?>
            <?php echo e(__('student.oc_curriculum_intro')); ?>

        </p>
        <div class="oc-nav" style="margin-top:14px">
            <a class="oc-btn" href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>"><i class="fas fa-chalkboard-teacher text-xs"></i> <?php echo e(__('student.oc_lectures')); ?></a>
            <a class="oc-btn oc-btn-quiet" href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>"><i class="fas fa-calendar-alt text-xs"></i> <?php echo e(__('student.oc_schedule')); ?></a>
            <a class="oc-btn oc-btn-quiet" href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>"><?php echo e(__('student.oc_course_page')); ?></a>
        </div>
    </section>

    <div class="oc-pulse" aria-label="<?php echo e(__('student.oc_curriculum_stats_aria')); ?>">
        <div>
            <span class="lbl"><?php echo e(__('student.oc_curriculum_sections')); ?></span>
            <span class="val teal"><?php echo e($stats['sections']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.oc_linked_items')); ?></span>
            <span class="val"><?php echo e($stats['items']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.oc_channel')); ?></span>
            <span class="val" style="font-size:1rem"><?php echo e($channelLabel); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.oc_group')); ?></span>
            <span class="val" style="font-size:1rem"><?php echo e($enrollment->group->name ?? '—'); ?></span>
        </div>
    </div>

    <div class="oc-cur-layout">
        <div class="oc-cur-main">
            <?php if(filled($offlineCourse->description)): ?>
                <section class="oc-panel" aria-labelledby="course-desc-heading">
                    <p class="oc-label" id="course-desc-heading"><?php echo e(__('student.oc_course_description')); ?></p>
                    <p class="oc-prose"><?php echo e($offlineCourse->description); ?></p>
                </section>
            <?php endif; ?>

            <?php if(filled($offlineCourse->notes)): ?>
                <section class="oc-panel" aria-labelledby="course-notes-heading">
                    <p class="oc-label" id="course-notes-heading"><?php echo e(__('student.oc_additional_notes')); ?></p>
                    <div class="oc-note"><?php echo e($offlineCourse->notes); ?></div>
                </section>
            <?php endif; ?>

            <section aria-labelledby="curriculum-structure-heading">
                <p class="oc-section-title" id="curriculum-structure-heading"><?php echo e(__('student.oc_curriculum_structure')); ?></p>

                <?php if($curriculumRoots->isNotEmpty()): ?>
                    <div class="oc-tree">
                        <?php echo $__env->make('student.offline-courses.partials.curriculum-sections', [
                            'sections' => $curriculumRoots,
                            'offlineCourse' => $offlineCourse,
                            'channel' => $channel,
                            'studentRouteGroup' => $studentRouteGroup,
                            'depth' => 0,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php else: ?>
                    <div class="oc-empty">
                        <div class="icon"><i class="fas fa-folder-open"></i></div>
                        <h3><?php echo e(__('student.oc_no_curriculum')); ?></h3>
                        <p><?php echo e(__('student.oc_no_curriculum_desc')); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <aside class="oc-cur-aside">
            <div class="oc-panel oc-cur-aside-sticky">
                <p class="oc-label"><?php echo e(__('student.oc_instructor')); ?></p>
                <div class="oc-instructor">
                    <?php if($offlineCourse->instructor?->profile_image_url): ?>
                        <img src="<?php echo e($offlineCourse->instructor->profile_image_url); ?>" alt="">
                    <?php else: ?>
                        <div class="av" aria-hidden="true"><?php echo e(mb_substr($offlineCourse->instructor->name ?? __('student.oc_instructor_initial'), 0, 1)); ?></div>
                    <?php endif; ?>
                    <strong><?php echo e($offlineCourse->instructor->name ?? '—'); ?></strong>
                    <?php if(filled($offlineCourse->instructor?->bio)): ?>
                        <p><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->instructor->bio, 160)); ?></p>
                    <?php else: ?>
                        <p><?php echo e(__('student.oc_no_instructor_bio')); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="oc-panel oc-quick">
                <p class="oc-label"><?php echo e(__('student.oc_quick_nav')); ?></p>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>">
                        <span><i class="fas fa-file-alt lead"></i> <?php echo e(__('student.oc_resources')); ?></span>
                        <i class="fas fa-chevron-left chev"></i>
                    </a>
                    <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>">
                        <span><i class="fas fa-chalkboard-teacher lead"></i> <?php echo e(__('student.oc_lectures')); ?></span>
                        <i class="fas fa-chevron-left chev"></i>
                    </a>
                    <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>">
                        <span><i class="fas fa-calendar-alt lead"></i> <?php echo e(__('student.oc_schedule')); ?></span>
                        <i class="fas fa-chevron-left chev"></i>
                    </a>
                    <a href="<?php echo e(route('student.exams.index')); ?>">
                        <span><i class="fas fa-clipboard-check lead"></i> <?php echo e(__('student.oc_exams')); ?></span>
                        <i class="fas fa-chevron-left chev"></i>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/curriculum.blade.php ENDPATH**/ ?>