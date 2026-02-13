<?php $__env->startSection('title', 'إدارة المعاملات المالية - Mindlytics'); ?>
<?php $__env->startSection('header', 'إدارة المعاملات المالية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي المعاملات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-exchange-alt', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'description' => 'كل المعاملات'],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تمت بنجاح'],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'في انتظار المعالجة'],
        ['label' => 'إجمالي المبلغ', 'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م', 'icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'description' => 'قيمة المكتملة'],
    ];
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
    ];
    $typeBadges = [
        'credit' => ['label' => 'إيراد', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200', 'icon' => 'fas fa-arrow-down'],
        'debit' => ['label' => 'مصروف', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200', 'icon' => 'fas fa-arrow-up'],
        'income' => ['label' => 'إيراد', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200', 'icon' => 'fas fa-arrow-down'],
        'expense' => ['label' => 'مصروف', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200', 'icon' => 'fas fa-arrow-up'],
        'transfer' => ['label' => 'تحويل', 'classes' => 'bg-blue-100 text-blue-700 border border-blue-200', 'icon' => 'fas fa-exchange-alt'],
        'refund' => ['label' => 'استرداد', 'classes' => 'bg-orange-100 text-orange-700 border border-orange-200', 'icon' => 'fas fa-undo'],
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة إدارة المعاملات المالية</h2>
                    <p class="text-xs text-slate-600">متابعة المعاملات والإيرادات والمصروفات.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.transactions.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition-all">
                <i class="fas fa-plus"></i>
                إضافة معاملة
            </a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">حسب النوع أو الحالة أو رقم المعاملة أو بيانات العميل.</p>
        </div>
        <div class="p-4">
            <form method="GET" action="<?php echo e(route('admin.transactions.index')); ?>" id="filterForm" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" maxlength="255" placeholder="رقم المعاملة، اسم العميل، هاتف" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div class="w-full sm:w-auto min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">النوع</label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">جميع الأنواع</option>
                        <option value="credit" <?php echo e(request('type') == 'credit' ? 'selected' : ''); ?>>إيراد</option>
                        <option value="debit" <?php echo e(request('type') == 'debit' ? 'selected' : ''); ?>>مصروف</option>
                        <option value="income" <?php echo e(request('type') == 'income' ? 'selected' : ''); ?>>إيراد</option>
                        <option value="expense" <?php echo e(request('type') == 'expense' ? 'selected' : ''); ?>>مصروف</option>
                        <option value="transfer" <?php echo e(request('type') == 'transfer' ? 'selected' : ''); ?>>تحويل</option>
                        <option value="refund" <?php echo e(request('type') == 'refund' ? 'selected' : ''); ?>>استرداد</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>فاشلة</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    <?php if(request()->anyFilled(['search', 'type', 'status'])): ?>
                    <a href="<?php echo e(route('admin.transactions.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">المعاملات</h3>
                <p class="text-xs text-slate-600">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200"><?php echo e($transactions->total()); ?> معاملة</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-3 space-y-1.5">
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 hover:border-blue-200 hover:bg-white transition-all overflow-hidden">
                        <div class="px-3 py-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <i class="fas fa-exchange-alt text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900"><?php echo e($transaction->transaction_number ?? '—'); ?></p>
                                            <p class="text-xs text-slate-600"><?php echo e($transaction->user->name ?? '—'); ?> · <?php echo e($transaction->user->phone ?? $transaction->user->email ?? '—'); ?></p>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <?php $typeMeta = $typeBadges[$transaction->type] ?? ['label' => $transaction->type, 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200', 'icon' => 'fas fa-circle']; ?>
                                            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold <?php echo e($typeMeta['classes']); ?>">
                                                <i class="<?php echo e($typeMeta['icon'] ?? 'fas fa-circle'); ?> text-[10px]"></i>
                                                <?php echo e($typeMeta['label']); ?>

                                            </span>
                                            <?php $statusMeta = $statusBadges[$transaction->status] ?? ['label' => $transaction->status ?? '—', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200']; ?>
                                            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold <?php echo e($statusMeta['classes']); ?>">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                <?php echo e($statusMeta['label']); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <?php
                                            $isCredit = in_array($transaction->type, ['credit', 'income']);
                                            $isDebit = in_array($transaction->type, ['debit', 'expense']);
                                        ?>
                                        <span class="font-semibold <?php echo e($isDebit ? 'text-rose-600' : ($isCredit ? 'text-emerald-600' : 'text-slate-900')); ?>">
                                            <i class="fas fa-coins ml-0.5"></i>
                                            <?php echo e($isDebit ? '-' : '+'); ?><?php echo e(number_format($transaction->amount, 2)); ?> ج.م
                                        </span>
                                        <span><i class="fas fa-calendar text-slate-500 ml-0.5"></i> <?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="<?php echo e(route('admin.transactions.show', $transaction)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 text-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fas fa-exchange-alt text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">لا توجد معاملات</p>
                        <p class="text-xs text-slate-500 mt-1">لم يتم تسجيل أي معاملات أو لا توجد نتائج للفلتر.</p>
                        <a href="<?php echo e(route('admin.transactions.create')); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة معاملة
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($transactions->hasPages()): ?>
                <div class="border-t border-slate-200 px-4 py-3">
                    <?php echo e($transactions->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.getElementById('filterForm') && document.getElementById('filterForm').addEventListener('submit', function() {
    var q = this.querySelector('input[name="search"]');
    if (q) q.value = (q.value || '').replace(/[<>'"&]/g, '').trim();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>