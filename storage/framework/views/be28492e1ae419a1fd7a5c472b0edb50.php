

<?php $__env->startSection('title', 'الحضور والغياب - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'الحضور والغياب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors">
                <?php echo e($offlineCourse->title); ?>

            </a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الحضور والغياب</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-emerald-600"></i>
                    الحضور والغياب
                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    اختر محاضرة/جلسة لعرض الطلاب وتسجيل حضورهم.
                </p>
            </div>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <?php if($sessions->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-calendar-xmark text-4xl mb-3 opacity-50"></i>
                <p>لا توجد جلسات/محاضرات بعد.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-4 sm:p-5 hover:bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800">
                                    <?php echo e($s->title ?: 'جلسة'); ?>

                                    — <?php echo e($s->group?->name ?? 'مجموعة'); ?>

                                </div>
                                <div class="text-sm text-slate-600 mt-1">
                                    <?php echo e(optional($s->session_date)->format('Y-m-d')); ?>

                                    <?php if($s->start_time): ?>
                                        · <?php echo e($s->start_time); ?><?php echo e($s->end_time ? ' - '.$s->end_time : ''); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?php echo e(route('instructor.offline-courses.attendance.sessions.show', ['offlineCourse' => $offlineCourse, 'session' => $s, 'channel' => ($channel ?? 'offline')])); ?>"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors">
                                <i class="fas fa-clipboard-list"></i>
                                فتح الحضور
                            </a>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            <div class="p-4 sm:p-5 border-t border-slate-200">
                <?php echo e($sessions->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/offline-courses/attendance/index.blade.php ENDPATH**/ ?>