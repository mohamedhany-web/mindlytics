

<?php $__env->startSection('title', 'طلاب المنح - Mindlytics'); ?>
<?php $__env->startSection('header', 'طلاب المنح'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-800',
        'activated' => 'bg-emerald-100 text-emerald-800',
        'rejected' => 'bg-rose-100 text-rose-800',
        'deactivated' => 'bg-slate-100 text-slate-700',
    ];
?>

<div class="space-y-6">
    <?php echo $__env->make('instructor.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- الهيدر -->
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-graduate text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">المنح الدراسية</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">طلاب المنح</h1>
                    <p class="text-sm text-slate-500 mt-0.5">جميع المسجّلين في منحك — يمكنك تفعيلهم أو رفضهم.</p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.scholarships.students.index', ['status' => 'registered'])); ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors flex-shrink-0">
                <i class="fas fa-user-clock"></i>
                <span>بانتظار التفعيل (<?php echo e($stats['registered']); ?>)</span>
            </a>
        </div>
    </div>

    <?php echo $__env->make('instructor.scholarships._nav', ['active' => 'students'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">الإجمالي</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['total']); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-users text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">بانتظار التفعيل</p>
                <p class="text-2xl sm:text-3xl font-bold text-amber-600"><?php echo e($stats['registered']); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مفعّلون</p>
                <p class="text-2xl sm:text-3xl font-bold text-emerald-600"><?php echo e($stats['activated']); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مرفوضون</p>
                <p class="text-2xl sm:text-3xl font-bold text-rose-600"><?php echo e($stats['rejected']); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center">
                <i class="fas fa-times-circle text-rose-600 text-lg"></i>
            </div>
        </div>
    </div>

    <?php echo $__env->make('instructor.scholarships._filters', [
        'programs' => $programs,
        'showProgramFilter' => true,
        'filterAction' => route('instructor.scholarships.students.index'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- جدول الطلاب -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 sm:px-6 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-list text-sky-600 text-xs"></i>
                </span>
                قائمة الطلاب
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">الطالب</th>
                        <th class="px-5 py-3 text-right font-semibold">المنحة</th>
                        <th class="px-5 py-3 text-center font-semibold">تاريخ التسجيل</th>
                        <th class="px-5 py-3 text-center font-semibold">الحالة</th>
                        <th class="px-5 py-3 text-left font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800"><?php echo e($registration->user?->name); ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?php echo e($registration->user?->email); ?><?php if($registration->user?->phone): ?> — <?php echo e($registration->user->phone); ?><?php endif; ?></div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="<?php echo e(route('instructor.scholarships.show', $registration->program)); ?>" class="text-sky-600 hover:text-sky-700 font-medium transition-colors">
                                    <?php echo e($registration->program?->name); ?>

                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-600"><?php echo e($registration->registered_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusBadges[$registration->status] ?? 'bg-slate-100 text-slate-700'); ?>">
                                    <?php echo e($registration->status_label); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5"><?php echo $__env->make('instructor.scholarships._registration-actions', ['registration' => $registration], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-user-graduate text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">لا يوجد طلاب مسجّلون في منحك.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($registrations->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200 flex justify-center">
                <?php echo e($registrations->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/scholarships/students/index.blade.php ENDPATH**/ ?>