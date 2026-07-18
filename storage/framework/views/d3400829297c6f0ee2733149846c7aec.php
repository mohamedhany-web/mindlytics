<?php $__env->startSection('title', $offlineCourse->title); ?>

<?php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? 'أونلاين' : 'أوفلاين';
    $listTitle = $isOnline ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين';

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
            <nav class="oc-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
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
            <span class="oc-signal"><?php echo e(number_format($enrollment->progress, 0)); ?>٪ تقدّم</span>
        </div>
    </header>

    <section class="oc-stage" aria-label="نظرة عامة">
        <div class="oc-eyebrow">مساحة الكورس <em><?php echo e($channelLabel); ?></em></div>
        <h2><?php echo e($offlineCourse->title); ?></h2>
        <?php if(filled($offlineCourse->description)): ?>
            <p class="oc-copy"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->description, 220)); ?></p>
        <?php endif; ?>
        <div class="oc-meter" role="progressbar" aria-valuenow="<?php echo e((int) $enrollment->progress); ?>" aria-valuemin="0" aria-valuemax="100">
            <i style="width:<?php echo e(min(100, (float) $enrollment->progress)); ?>%"></i>
        </div>
        <div class="oc-nav">
            <a class="oc-chip" href="<?php echo e(route($sg . '.curriculum', $offlineCourse)); ?>"><i class="fas fa-sitemap"></i> المنهج</a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>"><i class="fas fa-calendar-alt"></i> التقويم</a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>"><i class="fas fa-file-alt"></i> الموارد</a>
            <a class="oc-chip" href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>"><i class="fas fa-chalkboard-teacher"></i> المحاضرات</a>
            <a class="oc-chip" href="<?php echo e(route('student.exams.index')); ?>"><i class="fas fa-clipboard-check"></i> الاختبارات</a>
        </div>
    </section>

    <ul class="oc-facts" style="margin-bottom:20px">
        <li>
            <span class="k">المدرب</span>
            <span class="v"><?php echo e($offlineCourse->instructor->name ?? '—'); ?></span>
        </li>
        <?php if($offlineCourse->locationModel || $offlineCourse->location): ?>
            <li>
                <span class="k"><?php echo e($isOnline ? 'المنصة / المكان' : 'المكان'); ?></span>
                <span class="v"><?php echo e($offlineCourse->locationModel->name ?? $offlineCourse->location ?? '—'); ?></span>
            </li>
        <?php endif; ?>
        <?php if($offlineCourse->start_date): ?>
            <li>
                <span class="k">تاريخ البدء</span>
                <span class="v"><?php echo e($offlineCourse->start_date->format('Y-m-d')); ?></span>
            </li>
        <?php endif; ?>
        <?php if($enrollment->group): ?>
            <li>
                <span class="k">المجموعة</span>
                <span class="v"><?php echo e($enrollment->group->name); ?></span>
            </li>
        <?php endif; ?>
    </ul>

    <div class="oc-hub" aria-label="أقسام الكورس">
        <a href="<?php echo e(route($sg . '.curriculum', $offlineCourse)); ?>">
            <span class="ico"><i class="fas fa-sitemap"></i></span>
            <strong>المنهج والتوصيف</strong>
            <span>وصف الكورس وهيكل المحتوى</span>
        </a>
        <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>">
            <span class="ico"><i class="fas fa-calendar-alt"></i></span>
            <strong>تقويم الجلسات</strong>
            <span>المواعيد والاختبارات المجدولة</span>
        </a>
        <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>">
            <span class="count"><?php echo e($resourcesCount); ?></span>
            <span class="ico"><i class="fas fa-file-alt"></i></span>
            <strong>الموارد</strong>
            <span>فتح وتحميل الملفات</span>
        </a>
        <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>">
            <span class="count"><?php echo e($lecturesCount); ?></span>
            <span class="ico"><i class="fas fa-chalkboard-teacher"></i></span>
            <strong>المحاضرات</strong>
            <span>دروس مجموعتك</span>
        </a>
        <a href="#activities-required">
            <span class="count"><?php echo e($activitiesCount); ?></span>
            <span class="ico"><i class="fas fa-tasks"></i></span>
            <strong>الأنشطة</strong>
            <span>المطلوب تسليمه</span>
        </a>
        <a href="<?php echo e(route('student.exams.index')); ?>">
            <span class="count"><?php echo e($examsCount); ?></span>
            <span class="ico"><i class="fas fa-clipboard-check"></i></span>
            <strong>الاختبارات</strong>
            <span>الدخول من واجهة الطالب</span>
        </a>
    </div>

    <?php if($enrollment->group): ?>
        <div class="oc-panel" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
            <div>
                <p class="oc-label" style="margin-bottom:4px">الجلسات والمواعيد</p>
                <p style="margin:0;font-size:14px;font-weight:700">
                    مجموعة <?php echo e($enrollment->group->name); ?>

                    <?php if(($enrollment->group->sessions_count ?? 0) > 0): ?>
                        — <?php echo e($enrollment->group->sessions_count); ?> جلسة
                    <?php endif; ?>
                </p>
            </div>
            <a class="oc-btn" href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>">
                <i class="fas fa-calendar-alt text-xs"></i> فتح التقويم
            </a>
        </div>
    <?php endif; ?>

    <?php if((float) $enrollment->total_amount > 0): ?>
        <div class="oc-panel">
            <p class="oc-label">حالة الدفع</p>
            <?php
                $pTexts = ['paid' => 'مكتمل', 'partial' => 'جزئي', 'unpaid' => 'لم يتم الدفع'];
                $pBadge = ['paid' => 'oc-badge-ok', 'partial' => 'oc-badge-warn', 'unpaid' => 'oc-badge-bad'];
            ?>
            <div class="oc-facts">
                <li>
                    <span class="k">الحالة</span>
                    <span class="v"><span class="oc-badge <?php echo e($pBadge[$enrollment->payment_status] ?? ''); ?>"><?php echo e($pTexts[$enrollment->payment_status] ?? '—'); ?></span></span>
                </li>
                <li>
                    <span class="k">المدفوع</span>
                    <span class="v"><?php echo e(number_format($enrollment->paid_amount, 2)); ?> ج.م</span>
                </li>
                <li>
                    <span class="k">المتبقي</span>
                    <span class="v" style="color:<?php echo e((float) $enrollment->remaining_amount > 0 ? '#b91c1c' : '#047857'); ?>">
                        <?php echo e(number_format($enrollment->remaining_amount, 2)); ?> ج.م
                    </span>
                </li>
            </div>
        </div>
    <?php endif; ?>

    <div id="activities-required">
        <p class="oc-section-title">الأنشطة المطلوبة</p>
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
                                · <?php echo e($activity->max_score); ?> نقطة
                            </p>
                        </div>
                        <span class="oc-side">تسليم <i class="fas fa-arrow-left text-[10px]"></i></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="oc-panel" style="font-size:13px;color:var(--ml-muted)">لا توجد أنشطة مطلوبة حالياً.</div>
        <?php endif; ?>
    </div>

    <?php if($completedActivities->count() > 0): ?>
        <p class="oc-section-title" style="margin-top:20px">الأنشطة المكتملة</p>
        <div class="oc-list">
            <?php $__currentLoopData = $completedActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $submission = $activity->submissions->firstWhere('student_id', auth()->id()); ?>
                <a href="<?php echo e(route($sg . '.activities.show', [$offlineCourse, $activity])); ?>" class="oc-row">
                    <div class="oc-ico" style="background:rgba(16,185,129,0.12);color:#047857"><i class="fas fa-check"></i></div>
                    <div class="oc-body">
                        <h3><?php echo e($activity->title); ?></h3>
                        <?php if($submission && $submission->score !== null): ?>
                            <p class="meta">تم التصحيح: <?php echo e($submission->score); ?>/<?php echo e($activity->max_score); ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="oc-side">عرض</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\offline-courses\show.blade.php ENDPATH**/ ?>