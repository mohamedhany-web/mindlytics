

<?php $__env->startSection('title', $instructor->name); ?>
<?php $__env->startSection('header', 'مدرب المنح'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'instructors'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._page-header', [
        'title' => $instructor->name,
        'subtitle' => $instructor->email . ($instructor->phone ? ' | ' . $instructor->phone : ''),
        'icon' => 'fas fa-chalkboard-teacher',
        'statCards' => [
            ['label' => 'منح', 'value' => number_format($programs->count()), 'icon' => 'fas fa-award', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'طلاب مفعّلون', 'value' => number_format($programs->sum('activated_count')), 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'بانتظار التفعيل', 'value' => number_format($programs->sum('pending_count')), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-slate-900">منح هذا المدرب</div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50">
                    <div>
                        <p class="font-bold text-slate-900"><?php echo e($program->name); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($program->pending_count); ?> بانتظار — <?php echo e($program->activated_count); ?> مفعّل</p>
                    </div>
                    <a href="<?php echo e(route('admin.scholarships.programs.show', $program)); ?>" class="text-violet-600 font-semibold text-sm hover:underline">إدارة المنحة</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-5 py-8 text-center text-slate-500">لا توجد منح.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-slate-900">طلاب مفعّلون</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">المنحة</th>
                        <th class="px-4 py-3 text-center">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $activatedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold"><?php echo e($registration->user?->name); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($registration->user?->email); ?></div>
                            </td>
                            <td class="px-4 py-3"><?php echo e($registration->program?->name); ?></td>
                            <td class="px-4 py-3 text-center"><?php echo e($registration->activated_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">لا يوجد طلاب مفعّلون.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($activatedStudents->hasPages()): ?><div class="p-4"><?php echo e($activatedStudents->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/instructors/show.blade.php ENDPATH**/ ?>