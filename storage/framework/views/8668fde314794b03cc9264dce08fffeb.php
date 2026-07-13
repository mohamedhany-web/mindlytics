

<?php $__env->startSection('title', 'مركز المحاسبة'); ?>
<?php $__env->startSection('header', 'مركز المحاسبة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $cardThemes = [
        'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'amber'   => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'sky'     => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'rose'    => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
        'violet'  => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'indigo'  => ['border' => 'border-indigo-200/70', 'bg' => 'from-white via-white to-indigo-50/60', 'label' => 'text-indigo-800/80', 'value' => 'from-indigo-700 to-violet-600', 'icon' => 'from-indigo-500 to-violet-600', 'desc' => 'text-indigo-700/70'],
        'teal'    => ['border' => 'border-teal-200/70', 'bg' => 'from-white via-white to-teal-50/60', 'label' => 'text-teal-800/80', 'value' => 'from-teal-700 to-emerald-600', 'icon' => 'from-teal-500 to-emerald-600', 'desc' => 'text-teal-700/70'],
        'green'   => ['border' => 'border-green-200/70', 'bg' => 'from-white via-white to-green-50/60', 'label' => 'text-green-800/80', 'value' => 'from-green-700 to-emerald-600', 'icon' => 'from-green-500 to-emerald-600', 'desc' => 'text-green-700/70'],
    ];

    $sectionThemes = [
        'sky'     => ['header' => 'from-sky-500 to-blue-600', 'link' => 'from-sky-500 to-blue-600', 'hover' => 'hover:border-sky-300 hover:bg-sky-50/40'],
        'slate'   => ['header' => 'from-slate-600 to-slate-700', 'link' => 'from-slate-500 to-slate-600', 'hover' => 'hover:border-slate-300 hover:bg-slate-50/60'],
        'emerald' => ['header' => 'from-emerald-500 to-teal-600', 'link' => 'from-emerald-500 to-teal-600', 'hover' => 'hover:border-emerald-300 hover:bg-emerald-50/40'],
        'violet'  => ['header' => 'from-violet-500 to-purple-600', 'link' => 'from-violet-500 to-purple-600', 'hover' => 'hover:border-violet-300 hover:bg-violet-50/40'],
        'amber'   => ['header' => 'from-amber-500 to-orange-500', 'link' => 'from-amber-500 to-orange-500', 'hover' => 'hover:border-amber-300 hover:bg-amber-50/40'],
        'indigo'  => ['header' => 'from-indigo-500 to-violet-600', 'link' => 'from-indigo-500 to-violet-600', 'hover' => 'hover:border-indigo-300 hover:bg-indigo-50/40'],
        'rose'    => ['header' => 'from-rose-500 to-red-500', 'link' => 'from-rose-500 to-red-500', 'hover' => 'hover:border-rose-300 hover:bg-rose-50/40'],
    ];

    $primaryStats = [
        ['label' => 'إيرادات الشهر', 'value' => number_format($snapshot['revenue_month'], 2), 'desc' => ($snapshot['month_label'] ?? '') . ' — مدفوعات مكتملة', 'icon' => 'fas fa-chart-line', 'theme' => 'emerald'],
        ['label' => 'فواتير معلّقة', 'value' => number_format($snapshot['pending_invoices']), 'desc' => number_format($snapshot['pending_invoices_amount'], 2) . ' ج.م', 'icon' => 'fas fa-file-invoice', 'theme' => 'amber'],
        ['label' => 'طلبات معلّقة', 'value' => number_format($snapshot['pending_orders']), 'desc' => 'طلبات شراء بانتظار المعالجة', 'icon' => 'fas fa-shopping-cart', 'theme' => 'sky'],
        ['label' => 'سحوبات معلّقة', 'value' => number_format($snapshot['pending_withdrawals']), 'desc' => number_format($snapshot['pending_withdrawals_amount'], 2) . ' ج.م', 'icon' => 'fas fa-hand-holding-usd', 'theme' => 'rose'],
    ];

    $secondaryStats = [
        ['label' => 'اشتراكات نشطة', 'value' => number_format($snapshot['active_subscriptions']), 'desc' => 'اشتراكات سارية', 'icon' => 'fas fa-calendar-check', 'theme' => 'violet'],
        ['label' => 'اتفاقيات تقسيط', 'value' => number_format($snapshot['installment_agreements_active']), 'desc' => 'نشطة أو متأخرة', 'icon' => 'fas fa-handshake', 'theme' => 'indigo'],
        ['label' => 'أقساط مستحقة', 'value' => number_format($snapshot['installment_pending_total'], 2), 'desc' => 'ج.م — مجموع الأقساط', 'icon' => 'fas fa-percentage', 'theme' => 'violet'],
        ['label' => 'تسجيل أوفلاين (الشهر)', 'value' => number_format($snapshot['offline_paid_month'], 2), 'desc' => 'ج.م — مدفوعات التسجيل', 'icon' => 'fas fa-building', 'theme' => 'teal'],
    ];

    $hasGateway = Route::has('admin.accounting.gateway-operations');
    $gatewayStats = $hasGateway ? [
        ['label' => 'بوابات الدفع — محصّل', 'value' => number_format($snapshot['gateway_online_month_gross'] ?? 0, 2), 'desc' => 'ج.م — إجمالي الشهر', 'icon' => 'fas fa-plug', 'theme' => 'indigo'],
        ['label' => 'عمولات البوابات', 'value' => number_format($snapshot['gateway_fees_month'] ?? 0, 2), 'desc' => 'ج.م — الشهر الحالي', 'icon' => 'fas fa-coins', 'theme' => 'amber'],
    ] : [];

    $visibleSections = collect($sections)->map(function ($section) {
        $items = collect($section['items'] ?? [])
            ->filter(fn ($item) => Route::has($item['route'] ?? ''))
            ->values()
            ->all();

        return array_merge($section, ['items' => $items]);
    })->filter(fn ($section) => count($section['items']) > 0)->values()->all();

    $pendingAlerts = collect([
        ['show' => ($snapshot['pending_invoices'] ?? 0) > 0, 'text' => number_format($snapshot['pending_invoices']) . ' فاتورة معلّقة'],
        ['show' => ($snapshot['pending_orders'] ?? 0) > 0, 'text' => number_format($snapshot['pending_orders']) . ' طلب شراء معلّق'],
        ['show' => ($snapshot['pending_withdrawals'] ?? 0) > 0, 'text' => number_format($snapshot['pending_withdrawals']) . ' طلب سحب بانتظار المراجعة'],
    ])->filter(fn ($a) => $a['show'])->pluck('text')->all();
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مركز المحاسبة والمالية</h2>
                    <p class="text-xs text-slate-600">نقطة دخول واحدة للسجلات، التقارير، التقسيط، المدربين، والتصدير.</p>
                    <p class="text-[11px] text-slate-500 mt-1"><?php echo e($snapshot['month_label'] ?? ''); ?></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-home text-slate-500"></i>
                    لوحة التحكم
                </a>
                <?php if(Route::has('admin.accounting.insights')): ?>
                <a href="<?php echo e(route('admin.accounting.insights')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-bar text-emerald-600"></i>
                    مؤشرات المحاسبة
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    التقارير والـ Excel
                </a>
            </div>
        </div>
    </section>

    <?php if($pendingAlerts !== []): ?>
    <section class="rounded-2xl border border-amber-200 bg-amber-50 shadow-lg overflow-hidden">
        <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2 text-amber-900">
                <i class="fas fa-bell"></i>
                <span class="text-sm font-bold">يتطلب متابعة</span>
            </div>
            <p class="text-xs font-semibold text-amber-800"><?php echo e(implode(' · ', $pendingAlerts)); ?></p>
        </div>
    </section>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <?php $__currentLoopData = $primaryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $theme = $cardThemes[$card['theme']] ?? $cardThemes['sky']; ?>
            <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-5 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold <?php echo e($theme['label']); ?> mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-2xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                    </div>
                </div>
                <p class="text-xs font-medium <?php echo e($theme['desc']); ?> truncate"><?php echo e($card['desc']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <?php $__currentLoopData = $secondaryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $theme = $cardThemes[$card['theme']] ?? $cardThemes['sky']; ?>
            <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-5 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold <?php echo e($theme['label']); ?> mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-2xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                    </div>
                </div>
                <p class="text-xs font-medium <?php echo e($theme['desc']); ?> truncate"><?php echo e($card['desc']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($gatewayStats !== []): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-sm">
                    <i class="fas fa-plug"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">بوابات الدفع</h3>
                    <p class="text-[11px] text-slate-500">ملخص الشهر الحالي</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.accounting.gateway-operations')); ?>" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 hover:text-indigo-900">
                سجل العمليات والربط المحاسبي
                <i class="fas fa-arrow-left text-[10px]"></i>
            </a>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = $gatewayStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $theme = $cardThemes[$card['theme']] ?? $cardThemes['indigo']; ?>
                <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-5 shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold <?php echo e($theme['label']); ?> mb-1"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                            <p class="text-xs font-medium <?php echo e($theme['desc']); ?> mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-black text-slate-900">اختصارات سريعة</h3>
        </div>
        <div class="p-4 flex flex-wrap gap-2">
            <?php if(Route::has('admin.accounting.installments')): ?>
            <a href="<?php echo e(route('admin.accounting.installments')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-violet-800 rounded-xl border border-violet-200 bg-violet-50 hover:bg-violet-100">
                <i class="fas fa-percentage"></i>
                لوحة التقسيط
            </a>
            <?php endif; ?>
            <?php if(Route::has('admin.accounting.gateway-operations')): ?>
            <a href="<?php echo e(route('admin.accounting.gateway-operations')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-indigo-800 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100">
                <i class="fas fa-plug"></i>
                بوابات الدفع
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.accounting.chart')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-slate-800 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100">
                <i class="fas fa-sitemap"></i>
                شجرة الحسابات
            </a>
            <a href="<?php echo e(route('admin.invoices.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-amber-800 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100">
                <i class="fas fa-file-invoice"></i>
                الفواتير
            </a>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-emerald-800 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100">
                <i class="fas fa-credit-card"></i>
                المدفوعات
            </a>
            <a href="<?php echo e(route('admin.expenses.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-800 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100">
                <i class="fas fa-receipt"></i>
                المصروفات
            </a>
            <?php if(Route::has('admin.withdrawals.index')): ?>
            <a href="<?php echo e(route('admin.withdrawals.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-sky-800 rounded-xl border border-sky-200 bg-sky-50 hover:bg-sky-100">
                <i class="fas fa-hand-holding-usd"></i>
                طلبات السحب
            </a>
            <?php endif; ?>
        </div>
    </section>

    
    <?php $__currentLoopData = $visibleSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $themeKey = $section['theme'] ?? 'slate';
            $sectionTheme = $sectionThemes[$themeKey] ?? $sectionThemes['slate'];
        ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($sectionTheme['header']); ?> flex items-center justify-center text-white shadow-sm">
                    <i class="fas <?php echo e($section['icon'] ?? 'fa-folder-open'); ?> text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900"><?php echo e($section['title']); ?></h2>
                    <p class="text-[11px] text-slate-500"><?php echo e(count($section['items'])); ?> رابط</p>
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route($item['route'])); ?>"
                           class="group flex items-start gap-3 rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50/40 p-4 transition-all <?php echo e($sectionTheme['hover']); ?> hover:shadow-md">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br <?php echo e($sectionTheme['link']); ?> text-white shadow-sm">
                                <i class="fas <?php echo e($item['icon']); ?> text-sm"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-bold text-slate-900 group-hover:text-slate-800"><?php echo e($item['label']); ?></span>
                                <?php if(!empty($item['hint'])): ?>
                                    <span class="mt-1 block text-xs text-slate-500 leading-snug"><?php echo e($item['hint']); ?></span>
                                <?php endif; ?>
                            </span>
                            <i class="fas fa-chevron-left text-slate-300 group-hover:text-slate-500 mt-1 text-xs shrink-0"></i>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\accounting\hub.blade.php ENDPATH**/ ?>