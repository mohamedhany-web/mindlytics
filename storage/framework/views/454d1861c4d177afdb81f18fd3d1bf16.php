

<?php $__env->startSection('title', 'المدفوعات - التقارير المحاسبية - Mindlytics'); ?>
<?php $__env->startSection('header', 'المدفوعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="text-sky-600 hover:text-sky-700">التقارير المحاسبية</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">المدفوعات</span>
    </nav>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">المدفوعات في الفترة</h2>
                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'payments']))); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            </div>
            <form method="GET" action="<?php echo e(route('admin.accounting.reports.payments')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الفترة</label>
                    <select name="period" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="day" <?php echo e(($period ?? '') == 'day' ? 'selected' : ''); ?>>اليوم</option>
                        <option value="week" <?php echo e(($period ?? '') == 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                        <option value="month" <?php echo e(($period ?? '') == 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                        <option value="year" <?php echo e(($period ?? '') == 'year' ? 'selected' : ''); ?>>هذه السنة</option>
                        <option value="all" <?php echo e(($period ?? '') == 'all' ? 'selected' : ''); ?>>الكل</option>
                        <option value="custom" <?php echo e(($period ?? '') == 'custom' ? 'selected' : ''); ?>>مخصص</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                </div>
            </form>
            <p class="text-sm text-slate-500 mt-3">من <?php echo e($startDate->format('Y-m-d')); ?> إلى <?php echo e($endDate->format('Y-m-d')); ?> — إجمالي المدفوعات: <?php echo e($stats['total_payments']); ?></p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">رقم الدفعة</th>
                        <th class="px-4 py-3">العميل</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="font-semibold text-sky-600 hover:text-sky-700"><?php echo e($payment->payment_number); ?></a>
                        </td>
                        <td class="px-4 py-3"><?php echo e($payment->user->name ?? '—'); ?></td>
                        <td class="px-4 py-3 font-semibold text-emerald-600"><?php echo e(number_format($payment->amount, 2)); ?> ج.م</td>
                        <td class="px-4 py-3">
                            <?php if($payment->status == 'completed'): ?><span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">مكتملة</span>
                            <?php elseif($payment->status == 'pending'): ?><span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">معلقة</span>
                            <?php else: ?><span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700">فاشلة</span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo e($payment->created_at->format('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد مدفوعات في هذه الفترة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-200">
            <?php echo e($items->links()); ?>

        </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/accounting/reports-payments.blade.php ENDPATH**/ ?>