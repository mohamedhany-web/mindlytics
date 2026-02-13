
<?php $__env->startSection('title', 'محافظ المنصة - التقارير المحاسبية'); ?>
<?php $__env->startSection('header', 'محافظ المنصة'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="text-sky-600 hover:text-sky-700">التقارير المحاسبية</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">محافظ المنصة</span>
    </nav>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">محافظ المنصة</h2>
                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'wallets']))); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
            <p class="text-sm text-slate-500">إجمالي المحافظ: <?php echo e($stats['wallet_stats']['total_wallets']); ?> — إجمالي الأرصدة: <?php echo e(number_format($stats['wallet_stats']['total_balance'], 2)); ?> ج.م</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">اسم المحفظة</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">رقم الحساب</th>
                        <th class="px-4 py-3">الرصيد</th>
                        <th class="px-4 py-3">المعلق</th>
                        <th class="px-4 py-3">معاملات</th>
                        <th class="px-4 py-3">نشطة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.wallets.show', $w)); ?>" class="font-semibold text-sky-600 hover:text-sky-700"><?php echo e($w->name ?? '—'); ?></a></td>
                        <td class="px-4 py-3"><?php echo e(\App\Models\Wallet::typeLabel($w->type ?? '')); ?></td>
                        <td class="px-4 py-3 font-mono"><?php echo e($w->account_number ?? '—'); ?></td>
                        <td class="px-4 py-3 font-semibold text-emerald-600"><?php echo e(number_format($w->balance, 2)); ?> ج.م</td>
                        <td class="px-4 py-3"><?php echo e(number_format($w->pending_balance ?? 0, 2)); ?> ج.م</td>
                        <td class="px-4 py-3"><?php echo e($w->transactions_count ?? 0); ?></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($w->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($w->is_active ? 'نعم' : 'لا'); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">لا توجد محافظ.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-200"><?php echo e($items->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/accounting/reports-wallets.blade.php ENDPATH**/ ?>