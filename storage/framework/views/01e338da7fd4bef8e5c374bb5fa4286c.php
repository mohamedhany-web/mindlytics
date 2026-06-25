<?php $__env->startSection('title', 'قوالب التقييم — HR'); ?>
<?php $__env->startSection('header', 'قوالب التقييم — HR'); ?>

<?php $__env->startSection('content'); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.hr._nav', ['active' => 'rubrics'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => 'قوالب التقييم',
        'subtitle' => 'أنشئ معايير التقييم (key / label / weight / max) لاستخدامها في احتساب درجة المتقدم.',
        'icon' => 'fas fa-star-half-alt',
        'actions' => '<a href="' . route('admin.hr.rubrics.create') . '" class="' . $hrBtnPrimary . '"><i class="fas fa-plus"></i> قالب جديد</a>',
        'statCards' => [
            ['label' => 'إجمالي القوالب', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-list', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'قالب افتراضي', 'value' => ($stats['default'] ?? 0) > 0 ? 'نعم' : 'لا', 'icon' => 'fas fa-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($hrSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-table text-pink-600"></i>
                قائمة القوالب
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">افتراضي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المنشئ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $rubrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rubric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?php echo e($rubric->name); ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">معايير: <?php echo e(count($rubric->normalizedCriteria())); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($rubric->is_default): ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">افتراضي</span>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-slate-700"><?php echo e($rubric->creator?->name ?: '—'); ?></td>
                            <td class="px-6 py-4 text-left">
                                <a href="<?php echo e(route('admin.hr.rubrics.edit', $rubric)); ?>" class="<?php echo e($hrBtnPrimary); ?> !px-3 !py-2 text-xs">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">لا توجد قوالب بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200"><?php echo e($rubrics->links()); ?></div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/hr/rubrics/index.blade.php ENDPATH**/ ?>