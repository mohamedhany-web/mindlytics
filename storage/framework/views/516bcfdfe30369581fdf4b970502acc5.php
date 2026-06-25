

<?php
    $statusLabels = [
        'scheduled' => ['مجدولة', 'bg-sky-100 text-sky-800 border-sky-200'],
        'completed' => ['مكتملة', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
        'cancelled' => ['ملغاة', 'bg-red-100 text-red-800 border-red-200'],
    ];
    [$stLabel, $stClass] = $statusLabels[$session->status] ?? ['غير محدد', 'bg-slate-100 text-slate-700 border-slate-200'];
    $fmtTime = function ($t) {
        if ($t === null || $t === '') {
            return '—';
        }
        if (is_string($t)) {
            return strlen($t) >= 5 ? substr($t, 0, 5) : $t;
        }

        return (string) $t;
    };
?>

<?php $__env->startSection('title', ($session->title ?: 'جلسة') . ' — ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'تفاصيل الجلسة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="<?php echo e(route('instructor.offline-courses.index', ['channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600"><?php echo e(($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين'); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600">الجلسات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($session->title ?: 'جلسة'); ?></span>
        </nav>

        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-slate-900"><?php echo e($session->title ?: 'جلسة تدريبية'); ?></h1>
                <?php if($session->group): ?>
                    <p class="text-slate-600 mt-1 flex items-center gap-2 flex-wrap">
                        <i class="fas fa-users text-violet-500"></i>
                        <span class="font-semibold"><?php echo e($session->group->name); ?></span>
                        <?php if($session->group->location): ?>
                            <span class="text-slate-400">·</span>
                            <span class="text-sm"><?php echo e($session->group->location); ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            <span class="text-sm px-3 py-1 rounded-lg border font-bold <?php echo e($stClass); ?>"><?php echo e($stLabel); ?></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">تاريخ الجلسة</p>
                <p class="text-lg font-semibold text-slate-800"><?php echo e($session->session_date->translatedFormat('l j F Y')); ?></p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">الوقت</p>
                <p class="text-lg font-semibold text-slate-800">من <?php echo e($fmtTime($session->start_time)); ?> إلى <?php echo e($fmtTime($session->end_time)); ?></p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">المدة</p>
                <p class="text-lg font-semibold text-slate-800"><?php echo e((int) $session->duration_minutes); ?> دقيقة</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">مكان الجلسة</p>
                <p class="text-lg font-semibold text-slate-800"><?php echo e($session->location ?: (optional($session->group)->location ?? '—')); ?></p>
            </div>
        </div>

        <?php if($session->instructor): ?>
            <div class="rounded-xl border border-slate-100 bg-white p-4 mb-6">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">المدرّب المعيّن على الجلسة</p>
                <p class="font-semibold text-slate-800"><?php echo e($session->instructor->name ?? ('#'.$session->instructor_id)); ?></p>
            </div>
        <?php endif; ?>

        <?php if(filled($session->notes)): ?>
            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4 mb-6">
                <p class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-2">ملاحظات الجلسة</p>
                <div class="text-slate-800 text-sm leading-relaxed whitespace-pre-line"><?php echo e($session->notes); ?></div>
            </div>
        <?php endif; ?>

        <div class="border-t border-slate-100 pt-6">
            <h2 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-violet-500"></i>
                المحاضرات المرتبطة بهذه الجلسة في النظام
            </h2>
            <?php if($session->lectures->isEmpty()): ?>
                <p class="text-sm text-slate-500">لا توجد محاضرة مربوطة بعد. يمكنك إنشاء محاضرة واختيار هذه الجلسة من <a href="<?php echo e(route('instructor.offline-courses.curriculum.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="text-violet-600 font-semibold hover:underline">بناء المنهج</a> أو من <a href="<?php echo e(route('instructor.offline-courses.lectures.create', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="text-violet-600 font-semibold hover:underline">إضافة محاضرة</a>.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $session->lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                            <div>
                                <span class="font-semibold text-slate-800"><?php echo e($lec->title); ?></span>
                                <?php if(!$lec->is_active): ?>
                                    <span class="text-xs text-slate-500 mr-2">(معطّلة)</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo e(route('instructor.offline-courses.lectures.edit', ['offlineCourse' => $offlineCourse, 'lecture' => $lec, 'channel' => ($channel ?? 'offline')])); ?>" class="text-sm font-bold text-violet-600 hover:text-violet-800">تعديل المحاضرة</a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100">
            <a href="<?php echo e(route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-800 font-semibold hover:bg-slate-200 text-sm">
                <i class="fas fa-arrow-right"></i>
                العودة لقائمة الجلسات
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/offline-courses/lectures/group-session-show.blade.php ENDPATH**/ ?>