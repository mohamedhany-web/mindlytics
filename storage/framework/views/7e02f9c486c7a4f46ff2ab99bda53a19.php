<?php $__env->startSection('title', 'إدارة الفواتير - Mindlytics'); ?>
<?php $__env->startSection('header', 'إدارة الفواتير'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        [
            'label' => 'إجمالي الفواتير',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-file-invoice',
            'color' => 'blue',
            'description' => 'كل الفواتير المسجلة في المنصة',
        ],
        [
            'label' => 'فواتير معلقة',
            'value' => number_format($stats['pending'] ?? 0),
            'icon' => 'fas fa-hourglass-half',
            'color' => 'amber',
            'description' => 'بإنتظار الدفع',
        ],
        [
            'label' => 'فواتير مدفوعة',
            'value' => number_format($stats['paid'] ?? 0),
            'icon' => 'fas fa-check-circle',
            'color' => 'emerald',
            'description' => 'تم دفعها بنجاح',
        ],
        [
            'label' => 'فواتير متأخرة',
            'value' => number_format($stats['overdue'] ?? 0),
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'rose',
            'description' => 'تجاوزت تاريخ الاستحقاق',
        ],
    ];

    $statusBadges = [
        'paid' => ['label' => 'مدفوعة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'overdue' => ['label' => 'متأخرة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
    ];
?>

<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة إدارة الفواتير</h2>
                    <p class="text-sm text-slate-600 mt-1">متابعة الفواتير، المدفوعات، وحالة الاستحقاق عبر المنصة.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.invoices.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-plus"></i>
                إنشاء فاتورة جديدة
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $colorClasses = [
                        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
                        'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
                        'rose' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
                    ];
                    $colors = $colorClasses[$card['color']] ?? $colorClasses['blue'];
                ?>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars($card['label'])); ?></p>
                            <p class="text-2xl font-black text-slate-900"><?php echo e(htmlspecialchars($card['value'])); ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-lg <?php echo e($colors['bg']); ?> flex items-center justify-center <?php echo e($colors['text']); ?> shadow-sm">
                            <i class="<?php echo e($card['icon']); ?> text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars($card['description'])); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 xl:grid-cols-3">
            <div class="border-b border-slate-200 xl:border-b-0 xl:border-l px-6 py-5 bg-slate-50">
                <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600"></i>
                    البحث والفلترة
                </h3>
                <p class="text-xs text-slate-600 mb-5">فلترة الفواتير حسب الحالة أو بيانات العميل.</p>
                <form method="GET" action="<?php echo e(route('admin.invoices.index')); ?>" id="filterForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600 text-sm"></i>
                            البحث
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-blue-500"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" value="<?php echo e(htmlspecialchars(request('search') ?? '')); ?>" maxlength="255" placeholder="رقم الفاتورة، اسم العميل، أو رقم الهاتف" oninput="sanitizeInput(this)" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-tag text-blue-600 text-sm"></i>
                            الحالة
                        </label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">جميع الحالات</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                            <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>مدفوعة</option>
                            <option value="overdue" <?php echo e(request('status') == 'overdue' ? 'selected' : ''); ?>>متأخرة</option>
                            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-search"></i>
                        بحث متقدم
                    </button>
                </form>
            </div>
            <div class="xl:col-span-2">
                <div class="border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">الفواتير الحديثة</h3>
                        <p class="text-xs text-slate-600 mt-1">آخر الفواتير مرتبة من الأحدث إلى الأقدم.</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg"><?php echo e($invoices->total()); ?> فاتورة</span>
                </div>
            </div>
        </div>
        <div class="p-6">

            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 flex">
                                <i class="fas fa-file-invoice text-lg"></i>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900"><?php echo e(htmlspecialchars($invoice->invoice_number)); ?></p>
                                        <p class="text-xs text-slate-600 mt-0.5"><?php echo e(htmlspecialchars($invoice->user->name ?? 'غير معروف')); ?> - <?php echo e(htmlspecialchars($invoice->user->phone ?? '-')); ?></p>
                                    </div>
                                    <?php $badge = $statusBadges[$invoice->status] ?? null; ?>
                                    <?php if($badge): ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold <?php echo e($badge['classes']); ?>">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            <?php echo e(htmlspecialchars($badge['label'])); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-100">
                                    <div class="flex items-center gap-4 text-xs text-slate-600">
                                        <span><i class="fas fa-coins text-blue-600 ml-1"></i> <?php echo e(number_format($invoice->total_amount, 2)); ?> ج.م</span>
                                        <span><i class="fas fa-calendar text-slate-500 ml-1"></i> <?php echo e($invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-'); ?></span>
                                        <?php if($invoice->due_date && $invoice->due_date->isPast() && $invoice->status != 'paid'): ?>
                                            <span class="text-rose-500"><i class="fas fa-exclamation-triangle ml-1"></i> متأخرة</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php echo e(route('admin.invoices.show', $invoice)); ?>" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                        عرض
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                                <i class="fas fa-file-invoice text-2xl text-slate-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">لا توجد فواتير</p>
                                <p class="text-xs text-slate-500 mt-1">لم يتم إنشاء أي فواتير بعد</p>
                            </div>
                            <a href="<?php echo e(route('admin.invoices.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 transition-all duration-200">
                                <i class="fas fa-plus"></i>
                                إنشاء فاتورة جديدة
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if(isset($invoices) && $invoices->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200">
                <?php echo e($invoices->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
function sanitizeInput(input) {
    input.value = input.value.replace(/[<>'"&]/g, '');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/invoices/index.blade.php ENDPATH**/ ?>