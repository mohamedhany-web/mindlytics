

<?php $__env->startSection('title', 'مدربو المنح'); ?>
<?php $__env->startSection('header', 'قسم المنح'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'instructors'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._page-header', [
        'title' => 'مدربو المنح',
        'subtitle' => 'المدربون المعيّنون لبرامج المنح الدراسية.',
        'icon' => 'fas fa-chalkboard-teacher',
        'statCards' => [
            ['label' => 'مدربون', 'value' => number_format($overview['instructors_total'] ?? 0), 'icon' => 'fas fa-chalkboard-teacher', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
            ['label' => 'منح نشطة', 'value' => number_format($overview['programs_active'] ?? 0), 'icon' => 'fas fa-award', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'طلاب مفعّلون', 'value' => number_format($overview['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'بانتظار التفعيل', 'value' => number_format($overview['registered'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <form method="get" class="flex flex-col sm:flex-row gap-3">
                <input name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو البريد أو الهاتف…" class="<?php echo e($schInputClass); ?> flex-1">
                <button type="submit" class="<?php echo e($schBtnPrimary); ?>">بحث</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-right">المدرب</th>
                        <th class="px-4 py-3 text-right">البريد</th>
                        <th class="px-4 py-3 text-center">منح</th>
                        <th class="px-4 py-3 text-center">مفعّلون</th>
                        <th class="px-4 py-3 text-center">بانتظار</th>
                        <th class="px-4 py-3 text-left">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-slate-900"><?php echo e($instructor->name); ?></td>
                            <td class="px-4 py-3"><?php echo e($instructor->email); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($instructor->programs_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-emerald-700 font-semibold"><?php echo e($instructor->activated_students_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-700 font-semibold"><?php echo e($instructor->pending_students_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('admin.scholarships.instructors.show', $instructor)); ?>" class="text-violet-600 font-semibold hover:underline">تفاصيل</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا يوجد مدربون معيّنون لمنح بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($instructors->hasPages()): ?><div class="p-4 border-t border-slate-200"><?php echo e($instructors->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/instructors/index.blade.php ENDPATH**/ ?>