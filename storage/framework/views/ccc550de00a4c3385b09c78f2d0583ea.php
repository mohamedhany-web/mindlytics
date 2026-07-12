

<?php $__env->startSection('title', $plan->title); ?>
<?php $__env->startSection('header', 'تفاصيل الخطة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.investment._nav', ['active' => 'plans'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._header', [
        'title' => $plan->title,
        'subtitle' => $plan->planTypeLabel() . ' · ' . $plan->riskLevelLabel(),
        'icon' => 'fas fa-chart-pie',
        'actions' => '
            <a href="' . route('investment.show', $plan->slug) . '" target="_blank" class="' . $invBtnSecondary . '"><i class="fas fa-external-link-alt"></i> معاينة</a>
            <a href="' . route('admin.investment.plans.edit', $plan) . '" class="' . $invBtnPrimary . '"><i class="fas fa-edit"></i> تعديل</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="<?php echo e($invSectionClass); ?>">
                <?php echo $__env->make('admin.investment._section-head', ['icon' => 'fas fa-file-alt', 'title' => 'تفاصيل الخطة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="p-6">
                    <p class="text-slate-700 whitespace-pre-wrap leading-relaxed"><?php echo e($plan->description ?: '—'); ?></p>
                    <?php if($plan->benefits): ?>
                        <h4 class="font-bold text-slate-900 mt-6 mb-2">المزايا</h4>
                        <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($plan->benefits); ?></p>
                    <?php endif; ?>
                    <?php if($plan->eligibility_criteria): ?>
                        <h4 class="font-bold text-slate-900 mt-6 mb-2">شروط الأهلية</h4>
                        <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($plan->eligibility_criteria); ?></p>
                    <?php endif; ?>
                    <?php if($plan->terms_summary): ?>
                        <h4 class="font-bold text-slate-900 mt-6 mb-2">ملخص الشروط</h4>
                        <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($plan->terms_summary); ?></p>
                    <?php endif; ?>
                    <?php if($plan->legal_notes): ?>
                        <h4 class="font-bold text-slate-900 mt-6 mb-2">ملاحظات قانونية</h4>
                        <p class="text-slate-700 whitespace-pre-wrap text-sm"><?php echo e($plan->legal_notes); ?></p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <div class="space-y-6">
            <section class="<?php echo e($invSectionClass); ?>">
                <?php echo $__env->make('admin.investment._section-head', ['icon' => 'fas fa-info-circle', 'title' => 'ملخص الأرقام'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="p-5 text-sm space-y-3">
                    <p><span class="text-slate-500">الحد الأدنى:</span> <strong class="text-base"><?php echo e($plan->formattedMinInvestment()); ?></strong></p>
                    <?php if($plan->max_investment): ?><p><span class="text-slate-500">الحد الأقصى:</span> <strong><?php echo e(number_format($plan->max_investment, 0)); ?> <?php echo e($plan->currency); ?></strong></p><?php endif; ?>
                    <?php if($plan->duration_months): ?><p><span class="text-slate-500">المدة:</span> <strong><?php echo e($plan->duration_months); ?> شهر</strong></p><?php endif; ?>
                    <?php if($plan->expected_return_min): ?><p><span class="text-slate-500">العائد:</span> <strong><?php echo e($plan->expected_return_min); ?>% — <?php echo e($plan->expected_return_max); ?>%</strong></p><?php endif; ?>
                    <p><span class="text-slate-500">نموذج العائد:</span> <?php echo e($plan->returnModelLabel()); ?></p>
                    <p><span class="text-slate-500">الطلبات:</span> <?php echo e($plan->inquiries_count); ?> (<?php echo e($plan->pending_count); ?> جديد)</p>
                    <p><span class="text-slate-500">الحالة:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold <?php echo e($plan->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($plan->is_active ? 'نشطة' : 'متوقفة'); ?></span>
                    </p>
                </div>
            </section>
            <form method="POST" action="<?php echo e(route('admin.investment.plans.destroy', $plan)); ?>" onsubmit="return confirm('حذف هذه الخطة؟');">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-full text-sm text-rose-700 border border-rose-200 rounded-xl py-2.5 hover:bg-rose-50 bg-white">حذف الخطة</button>
            </form>
        </div>
    </div>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-inbox',
            'title' => 'طلبات على هذه الخطة',
            'subtitle' => '<span class="font-bold text-amber-600">' . $inquiries->total() . '</span> طلب',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">المستثمر</th>
                        <th class="px-6 py-4 text-right">المبلغ</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">التاريخ</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $inquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="inv-table-row">
                            <td class="px-6 py-4"><a href="<?php echo e(route('admin.investment.inquiries.show', $inq)); ?>" class="font-semibold text-slate-900 hover:text-amber-700"><?php echo e($inq->full_name); ?></a></td>
                            <td class="px-6 py-4 font-mono tabular-nums"><?php echo e($inq->proposed_amount ? number_format($inq->proposed_amount, 0) . ' ' . $inq->currency : '—'); ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800"><?php echo e($inq->statusLabel()); ?></span></td>
                            <td class="px-6 py-4 text-xs text-slate-500"><?php echo e($inq->created_at?->format('Y-m-d')); ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo e(route('admin.investment.inquiries.show', $inq)); ?>" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد طلبات بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($inquiries->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($inquiries->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\plans\show.blade.php ENDPATH**/ ?>