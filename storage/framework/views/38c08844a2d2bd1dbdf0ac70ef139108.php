

<?php $__env->startSection('title', 'تقرير طالب - ' . ($student->name ?? $student->email)); ?>
<?php $__env->startSection('header', 'تقرير طالب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.student-reports.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors">تقارير الطلاب</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($student->name ?? $student->email); ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-sky-600"></i>
                    <?php echo e($student->name ?? $student->email); ?>

                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    المجموعة: <?php echo e($enrollment->group?->name ?? '—'); ?> · القناة: <?php echo e(($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين'); ?>

                </p>
            </div>
            <a href="<?php echo e(route('instructor.offline-courses.student-reports.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-xs font-bold text-slate-500">الحضور (آخر 200 سجل)</div>
            <div class="mt-2 flex flex-wrap gap-2 text-sm">
                <?php
                    $present = $attendance->where('status','present')->count();
                    $absent = $attendance->where('status','absent')->count();
                    $late = $attendance->where('status','late')->count();
                    $excused = $attendance->where('status','excused')->count();
                ?>
                <span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">حاضر: <?php echo e($present); ?></span>
                <span class="px-2 py-1 rounded-lg bg-rose-50 text-rose-700 font-semibold">غائب: <?php echo e($absent); ?></span>
                <span class="px-2 py-1 rounded-lg bg-amber-50 text-amber-700 font-semibold">متأخر: <?php echo e($late); ?></span>
                <span class="px-2 py-1 rounded-lg bg-slate-50 text-slate-700 font-semibold">مستأذن: <?php echo e($excused); ?></span>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-xs font-bold text-slate-500">تسليمات الأنشطة (آخر 200)</div>
            <div class="mt-2 text-sm text-slate-700 font-semibold"><?php echo e($activitySubmissions->count()); ?> تسليم</div>
            <?php $avgAct = $activitySubmissions->whereNotNull('score')->avg('score'); ?>
            <div class="mt-1 text-sm text-slate-600">متوسط الدرجة: <?php echo e($avgAct !== null ? number_format((float)$avgAct, 1) : '—'); ?></div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-xs font-bold text-slate-500">امتحانات الأكاديمية (آخر 200)</div>
            <div class="mt-2 text-sm text-slate-700 font-semibold"><?php echo e($examAttempts->count()); ?> محاولة</div>
            <?php $avgEx = $examAttempts->whereNotNull('score')->avg('score'); ?>
            <div class="mt-1 text-sm text-slate-600">متوسط الدرجة: <?php echo e($avgEx !== null ? number_format((float)$avgEx, 1) : '—'); ?></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-calendar-check text-emerald-600"></i>
                سجل الحضور
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-slate-600">
                        <th class="text-right py-3 px-3 font-bold">التاريخ</th>
                        <th class="text-right py-3 px-3 font-bold">الحالة</th>
                        <th class="text-right py-3 px-3 font-bold">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $attendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="py-3 px-3 text-slate-700"><?php echo e(optional($a->attendance_date)->format('Y-m-d')); ?></td>
                            <td class="py-3 px-3 text-slate-700"><?php echo e($a->status); ?></td>
                            <td class="py-3 px-3 text-slate-600"><?php echo e($a->notes ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($attendance->isEmpty()): ?>
                        <tr><td colspan="3" class="py-8 text-center text-slate-500">لا يوجد سجلات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\offline-courses\student-reports\show.blade.php ENDPATH**/ ?>