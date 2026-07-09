

<?php $__env->startSection('title', 'سجل معاملات المحفظة'); ?>
<?php $__env->startSection('header', 'سجل معاملات المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $typeStyles = [
        'deposit' => ['label' => 'إيداع', 'classes' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fa-arrow-down', 'amount' => 'text-emerald-600'],
        'withdrawal' => ['label' => 'سحب', 'classes' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fa-arrow-up', 'amount' => 'text-rose-600'],
        'refund' => ['label' => 'استرداد', 'classes' => 'bg-orange-100 text-orange-800 border-orange-200', 'icon' => 'fa-undo', 'amount' => 'text-orange-600'],
        'commission' => ['label' => 'عمولة', 'classes' => 'bg-violet-100 text-violet-800 border-violet-200', 'icon' => 'fa-percent', 'amount' => 'text-violet-600'],
        'bonus' => ['label' => 'مكافأة', 'classes' => 'bg-sky-100 text-sky-800 border-sky-200', 'icon' => 'fa-gift', 'amount' => 'text-sky-600'],
        'deduction' => ['label' => 'خصم', 'classes' => 'bg-slate-100 text-slate-800 border-slate-200', 'icon' => 'fa-minus-circle', 'amount' => 'text-slate-600'],
    ];
    $currency = $wallet->currency ?? 'ج.م';
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-gradient-to-br from-sky-600 via-sky-700 to-indigo-700 text-white shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-white/70">سجل المعاملات</p>
                <h1 class="text-2xl sm:text-3xl font-black mt-1"><?php echo e($wallet->name ?? 'محفظة #' . $wallet->id); ?></h1>
                <p class="text-sm text-white/80 mt-2 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span><i class="fas fa-wallet ml-1"></i> <?php echo e(\App\Models\Wallet::typeLabel($wallet->type)); ?></span>
                    <?php if($wallet->account_number): ?>
                        <span><i class="fas fa-hashtag ml-1"></i> <?php echo e($wallet->account_number); ?></span>
                    <?php endif; ?>
                    <?php if($wallet->bank_name): ?>
                        <span><i class="fas fa-university ml-1"></i> <?php echo e($wallet->bank_name); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white/15 hover:bg-white/25 px-4 py-2.5 text-sm font-semibold transition">
                    <i class="fas fa-arrow-right"></i>
                    تفاصيل المحفظة
                </a>
                <a href="<?php echo e(route('admin.wallets.reports', $wallet)); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white text-sky-700 px-4 py-2.5 text-sm font-semibold shadow transition hover:bg-sky-50">
                    <i class="fas fa-chart-bar"></i>
                    التقارير
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-white/10 px-4 py-3">
                <p class="text-[11px] text-white/70">الرصيد الحالي</p>
                <p class="text-lg font-black tabular-nums"><?php echo e(number_format((float) $wallet->balance, 2)); ?> <?php echo e($currency); ?></p>
            </div>
            <div class="bg-white/10 px-4 py-3">
                <p class="text-[11px] text-white/70">إجمالي الإيداعات</p>
                <p class="text-lg font-bold text-emerald-200 tabular-nums">+<?php echo e(number_format($stats['deposits'] ?? 0, 2)); ?></p>
            </div>
            <div class="bg-white/10 px-4 py-3">
                <p class="text-[11px] text-white/70">إجمالي المسحوبات</p>
                <p class="text-lg font-bold text-rose-200 tabular-nums">-<?php echo e(number_format($stats['outgoing'] ?? 0, 2)); ?></p>
            </div>
            <div class="bg-white/10 px-4 py-3">
                <p class="text-[11px] text-white/70">استردادات</p>
                <p class="text-lg font-bold text-orange-200 tabular-nums"><?php echo e(number_format($stats['refunds'] ?? 0, 2)); ?></p>
            </div>
            <div class="bg-white/10 px-4 py-3 col-span-2 lg:col-span-1">
                <p class="text-[11px] text-white/70"><?php echo e($stats['total'] ?? 0); ?> معاملة</p>
                <p class="text-lg font-bold tabular-nums">صافي: <?php echo e(number_format($stats['net'] ?? 0, 2)); ?></p>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="رقم دفعة، معاملة، عميل، ملاحظة..."
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">النوع</label>
                <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $typeStyles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(request('type') === $key): echo 'selected'; endif; ?>><?php echo e($meta['label']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">من تاريخ</label>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">إلى تاريخ</label>
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 text-sm font-semibold">
                    <i class="fas fa-filter"></i> تطبيق
                </button>
                <?php if(request()->anyFilled(['search', 'type', 'from', 'to'])): ?>
                    <a href="<?php echo e(route('admin.wallets.transactions', $wallet)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-times"></i> مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-black text-slate-900">المعاملات</h2>
                <p class="text-xs text-slate-500"><?php echo e($transactions->total()); ?> نتيجة</p>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $meta = $typeStyles[$wt->type] ?? ['label' => $wt->type, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fa-circle', 'amount' => 'text-slate-900'];
                    $isIn = $wt->isIncoming();
                    $note = $wt->noteText();
                    $payment = $wt->payment;
                    $txn = $wt->transaction;
                ?>
                <details class="group">
                    <summary class="list-none cursor-pointer hover:bg-slate-50/80 transition px-4 py-4 sm:px-5">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <span class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center border <?php echo e($meta['classes']); ?>">
                                    <i class="fas <?php echo e($meta['icon']); ?> text-sm"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900"><?php echo e($meta['label']); ?></span>
                                        <span class="text-xs text-slate-400">#<?php echo e($wt->id); ?></span>
                                        <?php if($wt->status && $wt->status !== 'completed'): ?>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800"><?php echo e($wt->status); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 tabular-nums">
                                        <i class="far fa-clock ml-0.5"></i>
                                        <?php echo e($wt->created_at?->format('Y-m-d H:i')); ?>

                                        <?php if($wt->creator): ?>
                                            · <i class="fas fa-user-shield ml-0.5"></i> <?php echo e($wt->creator->name); ?>

                                        <?php endif; ?>
                                    </p>
                                    <?php if($note !== ''): ?>
                                        <p class="text-sm text-slate-600 mt-1 line-clamp-1"><?php echo e($note); ?></p>
                                    <?php endif; ?>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <?php if($payment): ?>
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                                                <i class="fas fa-money-check-alt ml-0.5"></i>
                                                دفعة: <?php echo e($payment->payment_number); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($txn): ?>
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 border border-violet-100">
                                                <i class="fas fa-exchange-alt ml-0.5"></i>
                                                معاملة: <?php echo e($txn->transaction_number ?? '#'.$txn->id); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($payment?->user): ?>
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-slate-50 text-slate-700 border border-slate-200">
                                                <i class="fas fa-user ml-0.5"></i>
                                                <?php echo e($payment->user->name); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-1 sm:text-left flex-shrink-0 sm:min-w-[140px]">
                                <p class="text-lg font-black tabular-nums <?php echo e($meta['amount']); ?>">
                                    <?php echo e($isIn ? '+' : '−'); ?><?php echo e(number_format((float) $wt->amount, 2)); ?> <?php echo e($currency); ?>

                                </p>
                                <p class="text-xs text-slate-500 tabular-nums">
                                    بعد: <?php echo e(number_format((float) ($wt->balance_after ?? 0), 2)); ?>

                                </p>
                                <span class="text-[10px] text-sky-600 font-semibold group-open:hidden sm:mt-1">
                                    <i class="fas fa-chevron-down ml-0.5"></i> تفاصيل
                                </span>
                            </div>
                        </div>
                    </summary>

                    <div class="px-4 pb-4 sm:px-5 sm:pl-16 border-t border-slate-100 bg-slate-50/50">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm mt-3">
                            <?php if($wt->balance_before !== null): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">الرصيد قبل</dt>
                                <dd class="font-bold text-slate-900 tabular-nums"><?php echo e(number_format((float) $wt->balance_before, 2)); ?> <?php echo e($currency); ?></dd>
                            </div>
                            <?php endif; ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">الرصيد بعد</dt>
                                <dd class="font-bold text-slate-900 tabular-nums"><?php echo e(number_format((float) ($wt->balance_after ?? 0), 2)); ?> <?php echo e($currency); ?></dd>
                            </div>
                            <?php if($wt->reference_number): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">رقم مرجعي</dt>
                                <dd class="font-semibold text-slate-900"><?php echo e($wt->reference_number); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if($payment): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">الدفعة</dt>
                                <dd>
                                    <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="font-semibold text-sky-600 hover:text-sky-800">
                                        <?php echo e($payment->payment_number); ?>

                                        <i class="fas fa-external-link-alt text-[10px] mr-0.5"></i>
                                    </a>
                                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e(number_format((float) $payment->amount, 2)); ?> ج.م · <?php echo e($payment->payment_method ?? '—'); ?></p>
                                </dd>
                            </div>
                            <?php endif; ?>
                            <?php if($payment?->invoice): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">الفاتورة</dt>
                                <dd>
                                    <a href="<?php echo e(route('admin.invoices.show', $payment->invoice)); ?>" class="font-semibold text-sky-600 hover:text-sky-800">
                                        <?php echo e($payment->invoice->invoice_number); ?>

                                        <i class="fas fa-external-link-alt text-[10px] mr-0.5"></i>
                                    </a>
                                </dd>
                            </div>
                            <?php endif; ?>
                            <?php if($txn): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">المعاملة المالية</dt>
                                <dd>
                                    <a href="<?php echo e(route('admin.transactions.show', $txn)); ?>" class="font-semibold text-sky-600 hover:text-sky-800">
                                        <?php echo e($txn->transaction_number ?? '#'.$txn->id); ?>

                                        <i class="fas fa-external-link-alt text-[10px] mr-0.5"></i>
                                    </a>
                                    <?php if($txn->user): ?>
                                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($txn->user->name); ?></p>
                                    <?php endif; ?>
                                </dd>
                            </div>
                            <?php endif; ?>
                            <?php if($payment?->user): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">العميل</dt>
                                <dd class="font-semibold text-slate-900"><?php echo e($payment->user->name); ?></dd>
                                <p class="text-xs text-slate-500"><?php echo e($payment->user->phone ?? $payment->user->email ?? ''); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if($wt->creator): ?>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                                <dt class="text-xs text-slate-500">نُفِّذت بواسطة</dt>
                                <dd class="font-semibold text-slate-900"><?php echo e($wt->creator->name); ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                        <?php if($note !== ''): ?>
                        <div class="mt-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                            <p class="text-xs font-semibold text-slate-500 mb-1">الوصف / الملاحظات</p>
                            <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($note); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </details>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                    <p class="font-semibold text-slate-900">لا توجد معاملات</p>
                    <p class="text-sm text-slate-500 mt-1">لم يُسجَّل أي حركة على هذه المحفظة أو لا توجد نتائج للفلتر.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($transactions->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($transactions->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>

<style>
    details > summary::-webkit-details-marker { display: none; }
    details[open] > summary .group-open\:hidden { display: none; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\wallets\transactions.blade.php ENDPATH**/ ?>