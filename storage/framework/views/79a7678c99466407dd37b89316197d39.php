<?php $__env->startSection('title', 'مدربو المنح - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم المنح'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._header', [
        'title' => 'مدربو المنح',
        'subtitle' => 'المدربون المعيّنون لبرامج المنح الدراسية',
        'icon' => 'fas fa-chalkboard-teacher',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'مدربون', 'value' => number_format($overview['instructors_total'] ?? 0), 'icon' => 'fas fa-chalkboard-teacher', 'description' => 'مدربون معيّنون'],
        ['label' => 'منح نشطة', 'value' => number_format($overview['programs_active'] ?? 0), 'icon' => 'fas fa-award', 'description' => 'برامج فعّالة'],
        ['label' => 'طلاب مفعّلون', 'value' => number_format($overview['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'وصول نشط'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($overview['registered'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'description' => 'طلبات معلّقة'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'instructors'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-filter text-lg"></i></div>
                <div><h3 class="text-lg font-black text-slate-900">البحث</h3></div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم أو البريد أو الهاتف" class="<?php echo e($schInputClass); ?>">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="<?php echo e($schBtnPrimary); ?>"><i class="fas fa-search"></i><span>بحث</span></button>
                    <?php if(request()->filled('search')): ?>
                        <a href="<?php echo e(route('admin.scholarships.instructors.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-chalkboard-teacher text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة المدربين</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1"><span class="font-bold text-blue-600"><?php echo e($instructors->total()); ?></span> مدرب</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">المدرب</th>
                        <th class="px-6 py-4 text-right">البريد</th>
                        <th class="px-6 py-4 text-center">منح</th>
                        <th class="px-6 py-4 text-center">مفعّلون</th>
                        <th class="px-6 py-4 text-center">بانتظار</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="sch-avatar-gradient w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md"><?php echo e(mb_substr($instructor->name, 0, 1, 'UTF-8')); ?></div>
                                    <span class="font-bold text-slate-900"><?php echo e($instructor->name); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?php echo e($instructor->email); ?></td>
                            <td class="px-6 py-4 text-center font-bold tabular-nums"><?php echo e($instructor->programs_count ?? 0); ?></td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600 tabular-nums"><?php echo e($instructor->activated_students_count ?? 0); ?></td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums"><?php echo e($instructor->pending_students_count ?? 0); ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo e(route('admin.scholarships.instructors.show', $instructor)); ?>" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500">لا يوجد مدربون معيّنون</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($instructors->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($instructors->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\instructors\index.blade.php ENDPATH**/ ?>