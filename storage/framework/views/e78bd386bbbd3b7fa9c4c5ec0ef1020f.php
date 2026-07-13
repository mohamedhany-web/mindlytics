

<?php $__env->startSection('title', $instructor->name); ?>
<?php $__env->startSection('header', 'مدرب المنح'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="w-full space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => $instructor->name,
        'subtitle' => $instructor->email . ($instructor->phone ? ' | ' . $instructor->phone : ''),
        'icon' => 'fas fa-chalkboard-teacher',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'منح', 'value' => number_format($programs->count()), 'icon' => 'fas fa-award', 'description' => 'برامج معيّنة'],
        ['label' => 'طلاب مفعّلون', 'value' => number_format($programs->sum('activated_count')), 'icon' => 'fas fa-user-check', 'description' => 'وصول نشط'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($programs->sum('pending_count')), 'icon' => 'fas fa-hourglass-half', 'description' => 'طلبات معلّقة'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">منح هذا المدرب</h3>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="font-bold text-slate-900"><?php echo e($program->name); ?></p>
                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($program->pending_count); ?> بانتظار — <?php echo e($program->activated_count); ?> مفعّل</p>
                    </div>
                    <a href="<?php echo e(route('admin.scholarships.programs.show', $program)); ?>" class="text-sm font-semibold text-blue-600 hover:underline">إدارة المنحة</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center text-slate-500">لا توجد منح</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">طلاب مفعّلون</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">المنحة</th>
                        <th class="px-6 py-4 text-center">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $activatedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="sch-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900"><?php echo e($registration->user?->name); ?></td>
                            <td class="px-6 py-4"><?php echo e($registration->program?->name); ?></td>
                            <td class="px-6 py-4 text-center"><?php echo e($registration->activated_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">لا يوجد طلاب مفعّلون</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($activatedStudents->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($activatedStudents->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\instructors\show.blade.php ENDPATH**/ ?>