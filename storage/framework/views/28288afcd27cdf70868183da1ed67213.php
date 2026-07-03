

<?php $__env->startSection('title', 'طلبات المستثمرين'); ?>
<?php $__env->startSection('header', 'طلبات المستثمرين'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._header', [
        'title' => 'طلبات المستثمرين',
        'subtitle' => 'متابعة ومراجعة طلبات الدخول في الاستثمار',
        'icon' => 'fas fa-handshake',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._nav', ['active' => 'inquiries'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-filter',
            'title' => 'البحث والفلترة',
            'subtitle' => 'ابحث بالاسم أو البريد أو الهاتف',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="sm:col-span-2">
                    <label class="<?php echo e($invLabelClass); ?>"><i class="fas fa-search text-amber-600 text-sm"></i> البحث</label>
                    <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف..." class="<?php echo e($invInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($invLabelClass); ?>"><i class="fas fa-flag text-amber-600 text-sm"></i> الحالة</label>
                    <select name="status" class="<?php echo e($invSelectClass); ?>">
                        <option value="">كل الحالات</option>
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($invLabelClass); ?>"><i class="fas fa-layer-group text-amber-600 text-sm"></i> الخطة</label>
                    <select name="plan_id" class="<?php echo e($invSelectClass); ?>">
                        <option value="">كل الخطط</option>
                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php if(request('plan_id') == $p->id): echo 'selected'; endif; ?>><?php echo e($p->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 <?php echo e($invBtnPrimary); ?>"><i class="fas fa-search"></i><span>بحث</span></button>
                    <?php if(request()->anyFilled(['search', 'status', 'plan_id'])): ?>
                        <a href="<?php echo e(route('admin.investment.inquiries.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($invSectionClass); ?>">
        <?php echo $__env->make('admin.investment._section-head', [
            'icon' => 'fas fa-inbox',
            'title' => 'قائمة الطلبات',
            'subtitle' => '<span class="font-bold text-amber-600">' . $inquiries->total() . '</span> طلب',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">المستثمر</th>
                        <th class="px-6 py-4 text-right">النوع</th>
                        <th class="px-6 py-4 text-right">الخطة</th>
                        <th class="px-6 py-4 text-center">المبلغ</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">التاريخ</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $inquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="inv-table-row">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?php echo e($inq->full_name); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($inq->email); ?> · <?php echo e($inq->phone); ?></div>
                            </td>
                            <td class="px-6 py-4"><?php echo e($inq->investorTypeLabel()); ?></td>
                            <td class="px-6 py-4"><?php echo e($inq->plan?->title ?? '—'); ?></td>
                            <td class="px-6 py-4 text-center font-mono tabular-nums"><?php echo e($inq->proposed_amount ? number_format($inq->proposed_amount, 0) . ' ' . $inq->currency : '—'); ?></td>
                            <td class="px-6 py-4 text-center"><span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-900"><?php echo e($inq->statusLabel()); ?></span></td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500"><?php echo e($inq->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo e(route('admin.investment.inquiries.show', $inq)); ?>" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-6 py-16 text-center text-slate-500 font-medium">لا توجد طلبات</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($inquiries->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($inquiries->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\inquiries\index.blade.php ENDPATH**/ ?>