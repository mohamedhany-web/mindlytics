

<?php $__env->startSection('title', 'جلسات الكورس ' . (($channel ?? 'offline') === 'online' ? 'الأونلاين' : 'الأوفلاين') . ' - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'جلسات الكورس ' . (($channel ?? 'offline') === 'online' ? 'الأونلاين' : 'الأوفلاين')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusLabels = [
        'scheduled' => ['مجدولة', 'bg-sky-100 text-sky-800 border-sky-200'],
        'completed' => ['مكتملة', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
        'cancelled' => ['ملغاة', 'bg-red-100 text-red-800 border-red-200'],
    ];
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
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index', ['channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600"><?php echo e(($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين'); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الجلسات</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-calendar-days text-violet-500"></i>
                    جلساتك في هذا الكورس
                </h1>
                <p class="text-sm text-slate-600 mt-1">كل الجلسات القادمة والسابقة حسب مجموعات الكورس (كما في التقويم). اضغط على جلسة لعرض تفاصيلها كاملة في صفحة مستقلة.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('instructor.offline-courses.curriculum.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-800 rounded-xl font-semibold border border-indigo-200 hover:bg-indigo-100 text-sm">
                    <i class="fas fa-sitemap"></i> المنهج
                </a>
                <a href="<?php echo e(route('instructor.offline-courses.lectures.create', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 text-sm">
                    <i class="fas fa-plus"></i> محاضرة جديدة
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <?php if($sessions->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-calendar-xmark text-4xl mb-3 opacity-50"></i>
                <p class="font-medium text-slate-700">لا توجد جلسات مسجّلة لهذا الكورس بعد.</p>
                <p class="text-sm mt-2 max-w-md mx-auto">تُنشأ الجلسات من لوحة الإدارة ضمن مجموعات الكورس؛ بعدها ستظهر هنا وفي تقويمك.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        [$stLabel, $stClass] = $statusLabels[$s->status] ?? ['غير محدد', 'bg-slate-100 text-slate-700 border-slate-200'];
                    ?>
                    <li>
                        <a href="<?php echo e(route('instructor.offline-courses.lectures.sessions.show', ['offlineCourse' => $offlineCourse, 'session' => $s, 'channel' => ($channel ?? 'offline')])); ?>"
                           class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 sm:p-5 hover:bg-violet-50/40 transition-colors group">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-slate-900 group-hover:text-violet-800"><?php echo e($s->title ?: 'جلسة'); ?></span>
                                    <span class="text-xs px-2 py-0.5 rounded-md border <?php echo e($stClass); ?> font-semibold"><?php echo e($stLabel); ?></span>
                                </div>
                                <p class="text-sm text-slate-600 mt-1">
                                    <i class="far fa-calendar ml-1 text-violet-500"></i><?php echo e($s->session_date->translatedFormat('l j F Y')); ?>

                                    <span class="mx-2 text-slate-300">|</span>
                                    <i class="far fa-clock ml-1 text-violet-500"></i><?php echo e($fmtTime($s->start_time)); ?> — <?php echo e($fmtTime($s->end_time)); ?>

                                    <span class="mx-2 text-slate-300">|</span>
                                    <?php echo e((int) $s->duration_minutes); ?> دقيقة
                                </p>
                                <?php if($s->group): ?>
                                    <p class="text-xs text-slate-500 mt-1"><i class="fas fa-users ml-1"></i><?php echo e($s->group->name); ?></p>
                                <?php endif; ?>
                                <?php if($s->location): ?>
                                    <p class="text-xs text-slate-500 mt-0.5"><i class="fas fa-location-dot ml-1"></i><?php echo e($s->location); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <?php if($s->lectures_count > 0): ?>
                                    <span class="text-xs font-semibold text-violet-700 bg-violet-100 px-2 py-1 rounded-lg"><?php echo e($s->lectures_count); ?> محاضرة مرتبطة</span>
                                <?php endif; ?>
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-violet-600">
                                    التفاصيل
                                    <i class="fas fa-chevron-left text-xs opacity-70"></i>
                                </span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/offline-courses/lectures/index.blade.php ENDPATH**/ ?>