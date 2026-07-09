

<?php $__env->startSection('title', 'الخطط الاستثمارية'); ?>
<?php $__env->startSection('header', 'الخطط الاستثمارية'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._header', [
        'title' => 'الخطط الاستثمارية',
        'subtitle' => 'تعريف فرص الاستثمار، الحدود، العوائد، والشروط لكل خطة',
        'icon' => 'fas fa-layer-group',
        'actions' => '<a href="' . route('admin.investment.plans.create') . '" class="' . $invBtnPrimary . '"><i class="fas fa-plus"></i><span>خطة جديدة</span></a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._stats-grid', ['cards' => [
        ['label' => 'إجمالي الخطط', 'value' => number_format($overview['plans_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => number_format($overview['plans_active'] ?? 0) . ' نشطة'],
        ['label' => 'طلبات المستثمرين', 'value' => number_format($overview['inquiries_total'] ?? 0), 'icon' => 'fas fa-inbox', 'description' => number_format($overview['pending'] ?? 0) . ' جديد'],
        ['label' => 'قيد المراجعة', 'value' => number_format($overview['under_review'] ?? 0), 'icon' => 'fas fa-search', 'description' => 'تحتاج متابعة'],
        ['label' => 'مبالغ مقترحة', 'value' => number_format($overview['proposed_total'] ?? 0, 0), 'icon' => 'fas fa-coins', 'description' => 'EGP'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._nav', ['active' => 'plans'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-filter',
            'title' => 'البحث والفلترة',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="<?php echo e($invLabelClass); ?>"><i class="fas fa-search text-amber-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم الخطة أو الرابط" class="<?php echo e($invInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($invLabelClass); ?>"><i class="fas fa-tag text-amber-600 text-sm"></i> نوع الاستثمار</label>
                    <select name="plan_type" class="<?php echo e($invSelectClass); ?>">
                        <option value="">كل الأنواع</option>
                        <?php $__currentLoopData = \App\Models\InvestmentPlan::planTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(request('plan_type') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 <?php echo e($invBtnPrimary); ?>"><i class="fas fa-search"></i><span>بحث</span></button>
                    <?php if(request()->anyFilled(['search', 'plan_type'])): ?>
                        <a href="<?php echo e(route('admin.investment.plans.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-layer-group',
            'title' => 'قائمة الخطط',
            'subtitle' => '<span class="font-bold text-amber-600">' . $plans->total() . '</span> خطة',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الخطة</th>
                        <th class="px-6 py-4 text-right">النوع</th>
                        <th class="px-6 py-4 text-center">الحد الأدنى</th>
                        <th class="px-6 py-4 text-center">الطلبات</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="inv-table-row">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900"><?php echo e($plan->title); ?></p>
                                <p class="text-xs text-slate-500 font-mono" dir="ltr"><?php echo e($plan->slug); ?></p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800"><?php echo e($plan->planTypeLabel()); ?></td>
                            <td class="px-6 py-4 text-center font-bold tabular-nums"><?php echo e($plan->formattedMinInvestment()); ?></td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums"><?php echo e($plan->inquiries_count); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold <?php echo e($plan->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span><?php echo e($plan->is_active ? 'نشطة' : 'متوقفة'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('admin.investment.plans.show', $plan)); ?>" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                    <a href="<?php echo e(route('admin.investment.plans.edit', $plan)); ?>" class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg shadow-sm" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500 font-medium">لا توجد خطط بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($plans->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($plans->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\plans\index.blade.php ENDPATH**/ ?>