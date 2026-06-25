<?php
    $active = $active ?? '';
    $items = [
        ['key' => 'dashboard', 'route' => 'admin.accounting.installments', 'label' => 'لوحة التقسيط', 'icon' => 'fa-tachometer-alt'],
        ['key' => 'plans', 'route' => 'admin.installments.plans.index', 'label' => 'خطط التقسيط', 'icon' => 'fa-layer-group'],
        ['key' => 'agreements', 'route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-handshake'],
        ['key' => 'manual-booking', 'route' => 'admin.installments.agreements.manual-booking', 'label' => 'حجز + تقسيط', 'icon' => 'fa-user-plus'],
    ];
?>
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2">
        <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] font-bold text-slate-600 rounded-lg border border-slate-200 hover:bg-white">
            <i class="fas fa-calculator text-sky-600"></i>
            مركز المحاسبة
        </a>
        <span class="text-slate-300 hidden sm:inline">|</span>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(! Route::has($item['route'])) continue; ?>
            <?php $isActive = $active === $item['key']; ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors
                      <?php echo e($isActive ? 'text-violet-800 border-violet-300 bg-violet-50 shadow-sm' : 'text-slate-700 border-slate-200 hover:bg-slate-50'); ?>">
                <i class="fas <?php echo e($item['icon']); ?> <?php echo e($isActive ? 'text-violet-600' : 'text-slate-500'); ?>"></i>
                <?php echo e($item['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/partials/nav.blade.php ENDPATH**/ ?>