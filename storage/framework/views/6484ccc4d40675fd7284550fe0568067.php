<?php
    $heroTitle = $heroTitle ?? 'مركز المبيعات';
    $heroSubtitle = $heroSubtitle ?? '';
    $heroIcon = $heroIcon ?? 'fa-chart-line';
    $heroIconFrom = $heroIconFrom ?? 'emerald-500';
    $heroIconTo = $heroIconTo ?? 'teal-600';
    $backUrl = $backUrl ?? null;
    $backLabel = $backLabel ?? 'مركز المبيعات';
?>
<div class="welcome-section dashboard-card relative overflow-hidden">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shrink-0">
                <i class="fas <?php echo e($heroIcon); ?> text-2xl"></i>
            </div>
            <div>
                <?php if($backUrl): ?>
                    <a href="<?php echo e($backUrl); ?>" class="text-sm text-emerald-700 font-semibold hover:underline mb-1 inline-flex items-center gap-1">
                        <i class="fas fa-arrow-right text-xs"></i> <?php echo e($backLabel); ?>

                    </a>
                <?php endif; ?>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900"><?php echo e($heroTitle); ?></h2>
                <?php if($heroSubtitle): ?>
                    <p class="text-gray-600 text-sm sm:text-base mt-1 font-medium"><?php echo e($heroSubtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if(isset($heroActions)): ?>
            <div class="flex flex-wrap gap-2 shrink-0"><?php echo $heroActions; ?></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/_hero.blade.php ENDPATH**/ ?>