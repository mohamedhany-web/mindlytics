

<?php $__env->startSection('title', ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : __('student.offline_courses_title')); ?>

<?php
    $isOnline = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $pageTitle = $isOnline ? 'كورساتي الأونلاين' : __('student.offline_courses_title');
    $pageSub = $isOnline
        ? 'تظهر هنا فقط الكورسات الأونلاين المفعّلة في بوابة الطالب.'
        : __('student.offline_courses_subtitle');
    $badgeLabel = $isOnline ? 'أونلاين' : __('student.offline_badge');
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e($pageTitle); ?></span>
            </nav>
            <h1><?php echo e($pageTitle); ?></h1>
            <p class="sub"><?php echo e($pageSub); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($stats['total_offline']); ?> <?php echo e(__('student.courses_count_label')); ?></span>
            <span class="oc-signal oc-signal-hot"><?php echo e($stats['total_activities']); ?> <?php echo e(__('student.activities_label')); ?></span>
        </div>
    </header>

    <div class="oc-pulse" aria-label="ملخص">
        <div>
            <span class="lbl"><?php echo e(__('student.courses_count_label')); ?></span>
            <span class="val teal"><?php echo e($stats['total_offline']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.activities_label')); ?></span>
            <span class="val hot"><?php echo e($stats['total_activities']); ?></span>
        </div>
        <?php if(($bookings ?? collect())->isNotEmpty()): ?>
            <div>
                <span class="lbl">حجوزات معلّقة</span>
                <span class="val"><?php echo e(($bookings ?? collect())->count()); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="oc-list" role="list">
        <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $course = $enrollment->course; ?>
            <a href="<?php echo e(route($sg . '.show', $course->id)); ?>" class="oc-row" role="listitem">
                <div class="oc-ico" aria-hidden="true">
                    <i class="fas <?php echo e($isOnline ? 'fa-laptop-house' : 'fa-chalkboard-teacher'); ?>"></i>
                </div>
                <div class="oc-body">
                    <h3><?php echo e($course->title); ?></h3>
                    <p class="meta">
                        <?php echo e($course->instructor->name ?? '—'); ?>

                        <?php if($course->locationModel || $course->location): ?>
                            · <?php echo e($course->locationModel->name ?? $course->location ?? '—'); ?>

                        <?php endif; ?>
                        <?php if($enrollment->group): ?>
                            · <?php echo e($enrollment->group->name); ?>

                        <?php endif; ?>
                    </p>
                    <div class="oc-prog">
                        <div class="bar"><i style="width:<?php echo e(min(100, (float) $enrollment->progress)); ?>%"></i></div>
                        <span class="pct"><?php echo e(number_format($enrollment->progress, 0)); ?>٪</span>
                        <span class="oc-badge oc-badge-live"><?php echo e($badgeLabel); ?></span>
                        <?php if((float) $enrollment->total_amount > 0): ?>
                            <?php
                                $pMap = ['paid' => 'oc-badge-ok', 'partial' => 'oc-badge-warn', 'unpaid' => 'oc-badge-bad'];
                                $pLabels = ['paid' => 'مدفوع', 'partial' => 'جزئي', 'unpaid' => 'غير مدفوع'];
                            ?>
                            <span class="oc-badge <?php echo e($pMap[$enrollment->payment_status] ?? 'oc-badge-warn'); ?>">
                                <?php echo e($pLabels[$enrollment->payment_status] ?? ''); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="oc-side">عرض <i class="fas fa-arrow-left text-[10px]"></i></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if(($bookings ?? collect())->isEmpty()): ?>
                <div class="oc-empty">
                    <div class="icon"><i class="fas <?php echo e($isOnline ? 'fa-laptop-house' : 'fa-chalkboard-teacher'); ?>"></i></div>
                    <h3><?php echo e(__('student.no_offline_courses')); ?></h3>
                    <p><?php echo e(__('student.no_offline_courses_desc')); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php $__currentLoopData = ($bookings ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $course = $booking->course;
                $bookingOk = $booking->status === 'approved';
            ?>
            <?php if($course): ?>
                <div class="oc-row is-static" role="listitem">
                    <div class="oc-ico warn" aria-hidden="true"><i class="fas fa-hourglass-half"></i></div>
                    <div class="oc-body">
                        <h3><?php echo e($course->title); ?></h3>
                        <p class="meta">
                            <?php echo e($course->instructor->name ?? '—'); ?>

                            · تاريخ الحجز: <?php echo e(optional($booking->created_at)->format('Y-m-d')); ?>

                            <?php if($booking->assignedGroup || $booking->requestedGroup): ?>
                                · <?php echo e($booking->assignedGroup->name ?? $booking->requestedGroup->name); ?>

                            <?php endif; ?>
                        </p>
                        <span class="oc-badge <?php echo e($bookingOk ? 'oc-badge-ok' : 'oc-badge-warn'); ?>">
                            <?php echo e($bookingOk ? 'حجز مقبول' : 'حجز قيد المراجعة'); ?>

                        </span>
                        <p class="meta" style="margin-top:8px">سيظهر الكورس كمفعّل بعد اعتماد التسجيل النهائي من الإدارة.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($enrollments->hasPages()): ?>
        <div style="margin-top:20px;display:flex;justify-content:center">
            <?php echo e($enrollments->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/index.blade.php ENDPATH**/ ?>