<?php $__env->startSection('title', 'شجرة الحسابات'); ?>
<?php $__env->startSection('header', 'شجرة الحسابات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roots = $chart['roots'] ?? [];
    $currency = $chart['currency'] ?? 'EGP';
    $stats = $stats ?? ['total' => 0, 'linked' => 0, 'asset' => 0, 'liability' => 0, 'equity' => 0, 'revenue' => 0, 'expense' => 0];

    $cardThemes = [
        'amber'   => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'rose'    => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
        'violet'  => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'orange'  => ['border' => 'border-orange-200/70', 'bg' => 'from-white via-white to-orange-50/60', 'label' => 'text-orange-800/80', 'value' => 'from-orange-700 to-amber-600', 'icon' => 'from-orange-500 to-amber-500', 'desc' => 'text-orange-700/70'],
        'sky'     => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'slate'   => ['border' => 'border-slate-200/70', 'bg' => 'from-white via-white to-slate-50/60', 'label' => 'text-slate-800/80', 'value' => 'from-slate-700 to-slate-600', 'icon' => 'from-slate-500 to-slate-600', 'desc' => 'text-slate-700/70'],
    ];

    $typeCards = [
        ['label' => 'أصول', 'value' => number_format($stats['asset']), 'desc' => 'حسابات الأصول', 'icon' => 'fas fa-coins', 'theme' => 'amber'],
        ['label' => 'خصوم', 'value' => number_format($stats['liability']), 'desc' => 'التزامات وذمم', 'icon' => 'fas fa-arrow-down', 'theme' => 'rose'],
        ['label' => 'حقوق ملكية', 'value' => number_format($stats['equity']), 'desc' => 'رأس المال والاحتياطيات', 'icon' => 'fas fa-balance-scale', 'theme' => 'violet'],
        ['label' => 'إيرادات', 'value' => number_format($stats['revenue']), 'desc' => 'إيرادات التشغيل', 'icon' => 'fas fa-chart-line', 'theme' => 'emerald'],
        ['label' => 'مصروفات', 'value' => number_format($stats['expense']), 'desc' => 'تكاليف وتشغيل', 'icon' => 'fas fa-fire', 'theme' => 'orange'],
        ['label' => 'روابط النظام', 'value' => number_format($stats['linked']), 'desc' => 'حسابات مربوطة بصفحات', 'icon' => 'fas fa-link', 'theme' => 'sky'],
    ];

    $rootThemes = [
        'asset' => ['header' => 'from-amber-500 to-orange-500', 'badge' => 'bg-amber-100 text-amber-800 border-amber-200', 'border' => 'border-amber-200/80', 'bg' => 'from-amber-50/40 to-white'],
        'liability' => ['header' => 'from-rose-500 to-red-500', 'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 'border' => 'border-rose-200/80', 'bg' => 'from-rose-50/40 to-white'],
        'equity' => ['header' => 'from-violet-500 to-purple-600', 'badge' => 'bg-violet-100 text-violet-800 border-violet-200', 'border' => 'border-violet-200/80', 'bg' => 'from-violet-50/40 to-white'],
        'revenue' => ['header' => 'from-emerald-500 to-teal-600', 'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'border' => 'border-emerald-200/80', 'bg' => 'from-emerald-50/40 to-white'],
        'expense' => ['header' => 'from-orange-500 to-amber-500', 'badge' => 'bg-orange-100 text-orange-800 border-orange-200', 'border' => 'border-orange-200/80', 'bg' => 'from-orange-50/40 to-white'],
    ];
?>

<div class="space-y-6" x-data="chartOfAccountsPage()">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">شجرة الحسابات</h2>
                    <p class="text-xs text-slate-600">خريطة تفاعلية تربط الأصول والخصوم والإيرادات والمصروفات بصفحات النظام.</p>
                    <p class="text-[11px] text-slate-500 mt-1">العملة المرجعية: <span class="font-bold text-slate-700"><?php echo e($currency); ?></span> · <?php echo e(number_format($stats['total'])); ?> حساب · <?php echo e(count($roots)); ?> جذور</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-calculator text-sky-600"></i>
                    مركز المحاسبة
                </a>
                <?php if(Route::has('admin.accounting.insights')): ?>
                <a href="<?php echo e(route('admin.accounting.insights')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-bar text-emerald-600"></i>
                    المؤشرات
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    التقارير
                </a>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6 gap-4">
        <?php $__currentLoopData = $typeCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $theme = $cardThemes[$card['theme']] ?? $cardThemes['sky']; ?>
            <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-4 shadow-lg">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold <?php echo e($theme['label']); ?> mb-0.5"><?php echo e($card['label']); ?></p>
                        <p class="text-xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                        <p class="text-[10px] font-medium <?php echo e($theme['desc']); ?> truncate mt-0.5"><?php echo e($card['desc']); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="<?php echo e($card['icon']); ?> text-xs"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">الهيكل التفصيلي</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">اضغط على السهم لطي أو فتح الفروع. الروابط الزرقاء تفتح الصفحة في لوحة التحكم.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative min-w-[200px] flex-1 sm:flex-none">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="search" x-model="$store.chartTreeSearch.query" placeholder="بحث بالاسم أو الكود…"
                           class="w-full sm:w-56 pr-9 pl-3 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                </div>
                <button type="button" @click="toggleAll(true)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-expand-alt"></i>
                    فتح الكل
                </button>
                <button type="button" @click="toggleAll(false)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-compress-alt"></i>
                    طي الكل
                </button>
            </div>
        </div>

        <div class="p-4 space-y-4 bg-gradient-to-b from-slate-50/40 to-white">
            <?php $__currentLoopData = $roots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $rootType = $root['type'] ?? 'asset';
                    $rootTheme = $rootThemes[$rootType] ?? $rootThemes['asset'];
                ?>
                <div class="rounded-2xl border <?php echo e($rootTheme['border']); ?> bg-gradient-to-br <?php echo e($rootTheme['bg']); ?> shadow-sm overflow-hidden"
                     x-show="rootVisible(<?php echo \Illuminate\Support\Js::from($root)->toHtml() ?>)"
                     x-transition>
                    <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b border-slate-200/80 bg-white/70">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br <?php echo e($rootTheme['header']); ?> flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-folder-tree text-sm"></i>
                        </div>
                        <span class="font-mono text-sm font-black px-2.5 py-1 rounded-lg border <?php echo e($rootTheme['badge']); ?>"><?php echo e($root['code'] ?? ''); ?></span>
                        <span class="text-base font-black text-slate-900"><?php echo e($root['name'] ?? ''); ?></span>
                    </div>
                    <?php if(!empty($root['description'])): ?>
                        <p class="px-4 py-2 text-xs text-slate-600 border-b border-slate-100 bg-white/50"><?php echo e($root['description']); ?></p>
                    <?php endif; ?>
                    <div class="px-3 py-3 sm:px-4">
                        <?php echo $__env->make('admin.accounting.partials.chart-node', [
                            'nodes' => $root['children'] ?? [],
                            'depth' => 0,
                            'rootType' => $rootType,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <p x-show="$store.chartTreeSearch.query.trim() !== '' && !hasVisibleRoots(<?php echo \Illuminate\Support\Js::from($roots)->toHtml() ?>)"
               class="text-center text-sm font-semibold text-slate-500 py-8">
                لا توجد نتائج مطابقة لـ «<span x-text="$store.chartTreeSearch.query"></span>»
            </p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs text-slate-600">
        الشجرة للعرض والتوافق الداخلي؛ لا تُحدّث قيود اليومية تلقائياً.
        للأرقام التفصيلية استخدم
        <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="font-bold text-sky-700 hover:underline">التقارير المحاسبية</a>
        والتصدير إلى Excel.
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('chartTreeSearch', { query: '' });

    window.chartNodeMatches = function (node, query) {
        const q = (query || '').trim().toLowerCase();
        if (!q) return true;
        const hay = [node.code || '', node.name || '', node.description || ''].join(' ').toLowerCase();
        if (hay.includes(q)) return true;
        return (node.children || []).some((child) => window.chartNodeMatches(child, q));
    };

    Alpine.data('chartOfAccountsPage', () => ({
        init() {
            this.$watch('$store.chartTreeSearch.query', (q) => {
                if ((q || '').trim()) {
                    window.dispatchEvent(new CustomEvent('chart-tree-toggle', { detail: { open: true } }));
                }
            });
        },
        toggleAll(open) {
            window.dispatchEvent(new CustomEvent('chart-tree-toggle', { detail: { open } }));
        },
        rootVisible(root) {
            return window.chartNodeMatches(root, Alpine.store('chartTreeSearch').query);
        },
        hasVisibleRoots(roots) {
            const q = Alpine.store('chartTreeSearch').query;
            if (!q.trim()) return true;
            return roots.some((root) => window.chartNodeMatches(root, q));
        },
    }));
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/chart.blade.php ENDPATH**/ ?>