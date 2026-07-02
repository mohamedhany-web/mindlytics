

<?php $__env->startSection('title', 'المنح الدراسية'); ?>
<?php $__env->startSection('header', 'قسم المنح'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'programs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._page-header', [
        'title' => 'المنح الدراسية',
        'subtitle' => 'كل منحة لها رابط تسجيل خاص، كورس معزول، ومدرب مخصص.',
        'icon' => 'fas fa-award',
        'actions' => '<a href="' . route('admin.scholarships.programs.create') . '" class="' . $schBtnPrimary . '"><i class="fas fa-plus"></i> منحة جديدة</a>',
        'statCards' => [
            ['label' => 'إجمالي المنح', 'value' => number_format($overview['programs_total'] ?? 0), 'icon' => 'fas fa-award', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'نشطة', 'value' => number_format($overview['programs_active'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'مسجّلون', 'value' => number_format($overview['registrations_total'] ?? 0), 'icon' => 'fas fa-users', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مفعّلون', 'value' => number_format($overview['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <form method="get" class="flex flex-col sm:flex-row gap-3">
                <input name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو الرابط…" class="<?php echo e($schInputClass); ?> flex-1">
                <button type="submit" class="<?php echo e($schBtnPrimary); ?>">بحث</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-right">المنحة</th>
                        <th class="px-4 py-3 text-right">المدرب</th>
                        <th class="px-4 py-3 text-center">مسجّل</th>
                        <th class="px-4 py-3 text-center">مفعّل</th>
                        <th class="px-4 py-3 text-center">بانتظار</th>
                        <th class="px-4 py-3 text-center">الحالة</th>
                        <th class="px-4 py-3 text-left">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900"><?php echo e($program->name); ?></div>
                                <div class="text-xs text-slate-500" dir="ltr"><?php echo e($program->slug); ?></div>
                            </td>
                            <td class="px-4 py-3"><?php echo e($program->instructor?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e($program->registrations_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-emerald-700 font-semibold"><?php echo e($program->activated_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-amber-700 font-semibold"><?php echo e($program->pending_count ?? 0); ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($program->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                                    <?php echo e($program->is_active ? 'نشطة' : 'متوقفة'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <a href="<?php echo e(route('admin.scholarships.programs.show', $program)); ?>" class="text-violet-600 font-semibold hover:underline">إدارة</a>
                                    <a href="<?php echo e(route('admin.scholarships.students.index', ['program_id' => $program->id])); ?>" class="text-sky-600 font-semibold hover:underline">الطلاب</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">لا توجد منح بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($programs->hasPages()): ?><div class="p-4 border-t border-slate-200"><?php echo e($programs->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/programs/index.blade.php ENDPATH**/ ?>