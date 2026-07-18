<?php $__env->startSection('title', __('student.calendar_title')); ?>

<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $upcoming = $events->where('start_date', '>=', now())->take(10);
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
<style>
    .cal-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1099px) {
        .cal-layout { grid-template-columns: 1fr; }
    }
    .cal-aside { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1100px) {
        .cal-aside-sticky { position: sticky; top: 12px; }
    }
    .cal-board {
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        padding: 14px 16px 18px;
    }
    .cal-legend {
        display: flex; flex-wrap: wrap; gap: 12px 16px;
        margin-top: 14px; padding-top: 14px;
        border-top: 1px solid var(--ml-line);
        font-size: 12px; color: var(--ml-muted); font-weight: 600;
    }
    .cal-legend span {
        display: inline-flex; align-items: center; gap: 6px;
    }
    .cal-legend i {
        width: 12px; height: 12px; border-radius: 4px; display: inline-block;
    }
    .cal-event {
        display: flex; gap: 10px; padding: 10px 12px;
        border: 1px solid var(--ml-line); border-radius: 10px;
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, background var(--ml-fast) ease;
        cursor: pointer; background: var(--ml-surface);
    }
    .cal-event:hover {
        border-color: rgba(73, 164, 162, 0.4);
        background: rgba(73, 164, 162, 0.06);
    }
    .cal-event .dot {
        width: 10px; height: 10px; border-radius: 999px; margin-top: 5px; flex-shrink: 0;
    }
    .cal-event strong {
        display: block; font-size: 13px; font-weight: 700; line-height: 1.35;
        margin-bottom: 2px;
    }
    .cal-event .when { font-size: 11px; color: var(--ml-muted); }
    .cal-event .kind {
        margin-top: 4px; font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
    }
    .cal-stat-row {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; padding: 10px 12px; border-radius: 10px;
        background: var(--ml-well); margin-bottom: 8px;
        font-size: 13px; font-weight: 600; color: var(--ml-ink);
    }
    .cal-stat-row:last-child { margin-bottom: 0; }
    .cal-stat-row .n { font-weight: 700; color: var(--ml-teal-deep); font-size: 1rem; }

    /* FullCalendar LOS theme */
    .oc .fc { font-family: inherit; color: var(--ml-ink); }
    .oc .fc-theme-standard td, .oc .fc-theme-standard th,
    .oc .fc-theme-standard .fc-scrollgrid {
        border-color: var(--ml-line);
    }
    .oc .fc-col-header-cell-cushion,
    .oc .fc-daygrid-day-number {
        color: var(--ml-ink); text-decoration: none !important; font-weight: 600;
    }
    .oc .fc-toolbar-title { font-size: 1.05rem; font-weight: 700; }
    .oc .fc-button-primary {
        background: var(--ml-teal) !important;
        border-color: var(--ml-teal) !important;
        box-shadow: none !important;
        font-weight: 700;
        text-transform: none;
    }
    .oc .fc-button-primary:not(:disabled):hover,
    .oc .fc-button-primary:not(:disabled).fc-button-active {
        background: var(--ml-teal-deep) !important;
        border-color: var(--ml-teal-deep) !important;
    }
    .oc .fc-day-today { background: rgba(73, 164, 162, 0.08) !important; }
    .oc .fc-event {
        border-radius: 6px; border: 0; padding: 2px 4px; cursor: pointer;
    }
    .oc .fc-daygrid-event { white-space: normal; font-size: 0.8rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.calendar_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.calendar_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.calendar_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.calendar_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e(__('student.total_events')); ?>: <?php echo e($stats['total'] ?? 0); ?></span>
            <span class="oc-signal oc-signal-hot"><?php echo e(__('student.calendar_upcoming_count')); ?>: <?php echo e($stats['upcoming'] ?? 0); ?></span>
        </div>
    </header>

    <div class="oc-pulse" aria-label="<?php echo e(__('student.calendar_stats')); ?>">
        <div>
            <span class="lbl"><?php echo e(__('student.legend_exams')); ?></span>
            <span class="val teal"><?php echo e($stats['exams'] ?? 0); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.legend_lectures')); ?></span>
            <span class="val"><?php echo e($stats['lectures'] ?? 0); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.legend_assignments')); ?></span>
            <span class="val hot"><?php echo e($stats['assignments'] ?? 0); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.calendar_upcoming_count')); ?></span>
            <span class="val"><?php echo e($stats['upcoming'] ?? 0); ?></span>
        </div>
    </div>

    <div class="cal-layout">
        <div class="cal-board">
            <div id="calendar"></div>
            <div class="cal-legend">
                <span><i style="background:#ef4444"></i> <?php echo e(__('student.legend_exams')); ?></span>
                <span><i style="background:#49A4A2"></i> <?php echo e(__('student.legend_lectures')); ?></span>
                <span><i style="background:#f59e0b"></i> <?php echo e(__('student.legend_assignments')); ?></span>
                <span><i style="background:#10b981"></i> <?php echo e(__('student.other_events')); ?></span>
            </div>
        </div>

        <aside class="cal-aside">
            <div class="oc-panel cal-aside-sticky">
                <p class="oc-label"><?php echo e(__('student.upcoming_events')); ?></p>
                <div style="display:flex;flex-direction:column;gap:8px;max-height:28rem;overflow:auto">
                    <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $typeLabel = match ($event->type ?? '') {
                                'exam' => __('student.event_type_exam'),
                                'lecture' => __('student.event_type_lecture'),
                                'assignment' => __('student.event_type_assignment'),
                                default => __('student.event_type_event'),
                            };
                            $typeIcon = match ($event->type ?? '') {
                                'exam' => 'fa-clipboard-check',
                                'lecture' => 'fa-chalkboard-teacher',
                                'assignment' => 'fa-tasks',
                                default => 'fa-calendar-alt',
                            };
                        ?>
                        <a class="cal-event" href="<?php echo e($event->url ?? '#'); ?>">
                            <span class="dot" style="background:<?php echo e($event->color ?? '#49A4A2'); ?>"></span>
                            <div class="min-w-0">
                                <strong><?php echo e($event->title); ?></strong>
                                <div class="when">
                                    <?php echo e($event->start_date->format('d/m/Y')); ?>

                                    <?php if(!($event->is_all_day ?? false)): ?>
                                        · <?php echo e($event->start_date->format('H:i')); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="kind"><i class="fas <?php echo e($typeIcon); ?>"></i> <?php echo e($typeLabel); ?></div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="oc-empty" style="padding:24px 12px">
                            <div class="icon" style="width:44px;height:44px;font-size:18px"><i class="fas fa-calendar-times"></i></div>
                            <p style="margin:0"><?php echo e(__('student.no_upcoming_events')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.calendar_stats')); ?></p>
                <div class="cal-stat-row">
                    <span><i class="fas fa-clipboard-check text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i><?php echo e(__('student.legend_exams')); ?></span>
                    <span class="n"><?php echo e($stats['exams'] ?? 0); ?></span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-chalkboard-teacher text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i><?php echo e(__('student.legend_lectures')); ?></span>
                    <span class="n"><?php echo e($stats['lectures'] ?? 0); ?></span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-tasks text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i><?php echo e(__('student.legend_assignments')); ?></span>
                    <span class="n"><?php echo e($stats['assignments'] ?? 0); ?></span>
                </div>
                <div class="cal-stat-row">
                    <span><i class="fas fa-arrow-up text-xs" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i><?php echo e(__('student.calendar_upcoming_count')); ?></span>
                    <span class="n"><?php echo e($stats['upcoming'] ?? 0); ?></span>
                </div>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<?php if($isRtl): ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: <?php echo json_encode($isRtl ? 'ar' : 'en', 15, 512) ?>,
        direction: <?php echo json_encode($isRtl ? 'rtl' : 'ltr', 15, 512) ?>,
        initialView: 'dayGridMonth',
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: <?php echo json_encode(__('student.calendar_today'), 15, 512) ?>,
            month: <?php echo json_encode(__('student.calendar_month'), 15, 512) ?>,
            week: <?php echo json_encode(__('student.calendar_week'), 15, 512) ?>,
            day: <?php echo json_encode(__('student.calendar_day'), 15, 512) ?>
        },
        events: {
            url: <?php echo json_encode(route('calendar.events'), 15, 512) ?>,
            failure: function () {
                alert(<?php echo json_encode(__('student.calendar_load_error'), 15, 512) ?>);
            }
        },
        eventClick: function (info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        eventContent: function (arg) {
            return { html: '<div class="fc-event-title">' + arg.event.title + '</div>' };
        },
        height: 'auto',
        contentHeight: 600,
        firstDay: 6,
        weekends: true,
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });

    calendar.render();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/calendar/index.blade.php ENDPATH**/ ?>