<?php $__env->startSection('title', 'عمليات بوابات الدفع'); ?>
<?php $__env->startSection('header', 'عمليات بوابات الدفع'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $hasFilters = $gateway || $from || $to || ($q ?? '') !== '';
    $filterLabel = collect([
        $gateway ? \App\Models\Payment::gatewayLabel($gateway) : null,
        $from ? 'من ' . $from : null,
        $to ? 'إلى ' . $to : null,
        ($q ?? '') !== '' ? 'بحث: ' . $q : null,
    ])->filter()->implode(' · ');

    $pageStats = [
        ['label' => 'عدد العمليات', 'value' => number_format($summary['operations_count']), 'desc' => $hasFilters ? 'بعد الفلاتر' : 'كل العمليات', 'icon' => 'fas fa-list-ol', 'theme' => 'indigo'],
        ['label' => 'إجمالي المحصّل', 'value' => number_format($summary['gross_total'], 2), 'desc' => 'ج.م — gross', 'icon' => 'fas fa-arrow-down', 'theme' => 'emerald'],
        ['label' => 'عمولات البوابة', 'value' => number_format($summary['fees_total'], 2), 'desc' => 'ج.م — fees', 'icon' => 'fas fa-percentage', 'theme' => 'amber'],
        ['label' => 'صافي بعد العمولة', 'value' => number_format($summary['net_after_fees'], 2), 'desc' => 'ج.م — تقديري', 'icon' => 'fas fa-wallet', 'theme' => 'violet'],
    ];

    $accountingNav = [
        ['key' => 'hub', 'route' => 'admin.accounting.hub', 'label' => 'مركز المحاسبة', 'icon' => 'fa-calculator'],
        ['key' => 'insights', 'route' => 'admin.accounting.insights', 'label' => 'المؤشرات', 'icon' => 'fa-chart-bar'],
        ['key' => 'chart', 'route' => 'admin.accounting.chart', 'label' => 'شجرة الحسابات', 'icon' => 'fa-sitemap'],
        ['key' => 'gateway', 'route' => 'admin.accounting.gateway-operations', 'label' => 'بوابات الدفع', 'icon' => 'fa-plug'],
        ['key' => 'reports', 'route' => 'admin.accounting.reports', 'label' => 'التقارير', 'icon' => 'fa-file-excel'],
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-plug"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">عمليات بوابات الدفع</h2>
                    <p class="text-xs text-slate-600">كل دفعة أونلاين مرتبطة بالفاتورة والطلب ومعاملات الدفتر (تحصيل + عمولة).</p>
                    <?php if($hasFilters): ?>
                        <p class="text-[11px] text-indigo-700 font-semibold mt-1"><?php echo e($filterLabel); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-credit-card text-emerald-600"></i>
                    المدفوعات
                </a>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-shopping-cart text-sky-600"></i>
                    الطلبات
                </a>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white rounded-xl bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-exchange-alt"></i>
                    المعاملات
                </a>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2">
            <?php $__currentLoopData = $accountingNav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(! Route::has($item['route'])) continue; ?>
                <?php $isActive = ($item['key'] ?? '') === 'gateway'; ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors
                          <?php echo e($isActive ? 'text-indigo-800 border-indigo-300 bg-indigo-50 shadow-sm' : 'text-slate-700 border-slate-200 hover:bg-slate-50'); ?>">
                    <i class="fas <?php echo e($item['icon']); ?> <?php echo e($isActive ? 'text-indigo-600' : 'text-slate-500'); ?>"></i>
                    <?php echo e($item['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center text-white text-sm">
                    <i class="fas fa-filter"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900">فلترة العمليات</h3>
            </div>
            <?php if($hasFilters): ?>
                <a href="<?php echo e(route('admin.accounting.gateway-operations')); ?>" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                    <i class="fas fa-times-circle"></i> إزالة الفلاتر
                </a>
            <?php endif; ?>
        </div>
        <form method="get" action="<?php echo e(route('admin.accounting.gateway-operations')); ?>" class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">البوابة</label>
                <select name="gateway" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    <option value="">— الكل —</option>
                    <?php $__currentLoopData = $gatewayOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g); ?>" <?php if($gateway === $g): echo 'selected'; endif; ?>><?php echo e(\App\Models\Payment::gatewayLabel($g)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="<?php echo e($from); ?>" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="<?php echo e($to); ?>" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">بحث (رقم دفعة، فاتورة، طلب…)</label>
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="مثال: PAY- أو INV- أو رقم الطلب"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            </div>
            <div class="flex flex-wrap gap-2 md:col-span-2 lg:col-span-5 pt-1">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm font-bold hover:bg-indigo-700">
                    <i class="fas fa-search"></i>
                    تطبيق
                </button>
                <a href="<?php echo e(route('admin.accounting.gateway-operations')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    إعادة ضبط
                </a>
                <?php if(Route::has('admin.system-settings.index')): ?>
                <a href="<?php echo e(route('admin.system-settings.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100">
                    <i class="fas fa-cog"></i>
                    إعدادات عمولة البوابة
                </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <?php echo $__env->make('admin.installments.partials.stats', ['stats' => $pageStats], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($byGateway->isNotEmpty()): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-black text-slate-900">ملخص حسب البوابة</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">بعد تطبيق الفلاتر الحالية</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">البوابة</th>
                        <th class="px-4 py-3 text-right font-bold">العدد</th>
                        <th class="px-4 py-3 text-right font-bold">إجمالي</th>
                        <th class="px-4 py-3 text-right font-bold">عمولات</th>
                        <th class="px-4 py-3 text-right font-bold">صافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $byGateway; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $gross = (float) $row->gross;
                            $fees = (float) $row->fees;
                        ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 font-semibold text-slate-900">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <?php echo e(\App\Models\Payment::gatewayLabel($row->payment_gateway)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 tabular-nums"><?php echo e(number_format((int) $row->cnt)); ?></td>
                            <td class="px-4 py-3 text-emerald-700 font-mono tabular-nums"><?php echo e(number_format($gross, 2)); ?></td>
                            <td class="px-4 py-3 text-amber-700 font-mono tabular-nums"><?php echo e(number_format($fees, 2)); ?></td>
                            <td class="px-4 py-3 text-violet-800 font-mono font-semibold tabular-nums"><?php echo e(number_format($gross - $fees, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black text-slate-900">سجل العمليات</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    معاملة <span class="text-emerald-700 font-bold">دائن</span> = تحصيل العميل ·
                    <span class="text-amber-700 font-bold">مدين</span> = عمولة البوابة
                </p>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg tabular-nums">
                <?php echo e(number_format($payments->total())); ?> عملية
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-3 text-right font-bold">التاريخ</th>
                        <th class="px-3 py-3 text-right font-bold">البوابة</th>
                        <th class="px-3 py-3 text-right font-bold">الدفعة</th>
                        <th class="px-3 py-3 text-right font-bold">الإجمالي</th>
                        <th class="px-3 py-3 text-right font-bold">العمولة</th>
                        <th class="px-3 py-3 text-right font-bold">الصافي</th>
                        <th class="px-3 py-3 text-right font-bold">الفاتورة</th>
                        <th class="px-3 py-3 text-right font-bold">الطلب</th>
                        <th class="px-3 py-3 text-right font-bold">المعاملات</th>
                        <th class="px-3 py-3 text-right font-bold">العميل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $fee = (float) ($payment->gateway_fee_amount ?? 0);
                            $net = (float) $payment->amount - $fee;
                            $creditTx = $payment->transactions->firstWhere('type', 'credit');
                            $feeTx = $payment->transactions->firstWhere('type', 'debit');
                        ?>
                        <tr class="hover:bg-slate-50/80 align-top">
                            <td class="px-3 py-3 text-slate-600 whitespace-nowrap text-xs"><?php echo e($payment->paid_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-100 px-2 py-1 text-[11px] font-bold">
                                    <?php echo e(\App\Models\Payment::gatewayLabel($payment->payment_gateway)); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="font-mono text-sky-700 font-semibold hover:underline text-xs"><?php echo e($payment->payment_number); ?></a>
                            </td>
                            <td class="px-3 py-3 font-mono text-emerald-700 tabular-nums"><?php echo e(number_format((float) $payment->amount, 2)); ?></td>
                            <td class="px-3 py-3 font-mono text-amber-700 tabular-nums"><?php echo e($fee > 0 ? number_format($fee, 2) : '—'); ?></td>
                            <td class="px-3 py-3 font-mono font-semibold text-violet-800 tabular-nums"><?php echo e(number_format($net, 2)); ?></td>
                            <td class="px-3 py-3">
                                <?php if($payment->invoice): ?>
                                    <a href="<?php echo e(route('admin.invoices.show', $payment->invoice)); ?>" class="text-sky-700 font-mono text-xs hover:underline"><?php echo e($payment->invoice->invoice_number); ?></a>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3">
                                <?php if($payment->order_id): ?>
                                    <a href="<?php echo e(route('admin.orders.show', $payment->order_id)); ?>" class="text-slate-800 font-mono text-xs hover:underline">#<?php echo e($payment->order_id); ?></a>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-xs space-y-1 min-w-[120px]">
                                <?php if($creditTx): ?>
                                    <div><span class="text-emerald-600 font-bold">دائن</span> <a href="<?php echo e(route('admin.transactions.show', $creditTx)); ?>" class="font-mono text-sky-700 hover:underline"><?php echo e($creditTx->transaction_number); ?></a></div>
                                <?php endif; ?>
                                <?php if($feeTx): ?>
                                    <div><span class="text-amber-700 font-bold">مدين</span> <a href="<?php echo e(route('admin.transactions.show', $feeTx)); ?>" class="font-mono text-sky-700 hover:underline"><?php echo e($feeTx->transaction_number); ?></a></div>
                                <?php endif; ?>
                                <?php if(!$creditTx && !$feeTx): ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-slate-700 max-w-[140px] truncate text-xs" title="<?php echo e($payment->user?->name); ?>"><?php echo e($payment->user?->name ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center">
                                <i class="fas fa-plug text-3xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-semibold text-slate-600">لا توجد عمليات مطابقة</p>
                                <p class="text-xs text-slate-500 mt-1">جرّب توسيع نطاق التاريخ أو إزالة الفلاتر.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($payments->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50"><?php echo e($payments->links()); ?></div>
        <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs text-slate-600">
        العمليات المعروضة هي مدفوعات أونلاين عبر بوابة فقط. للتحصيل اليدوي أو التحويلات راجع
        <a href="<?php echo e(route('admin.payments.index')); ?>" class="font-bold text-indigo-700 hover:underline">سجل المدفوعات</a>.
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/gateway-operations.blade.php ENDPATH**/ ?>