<?php
    $icon = $icon ?? 'fas fa-chart-pie';
    $subtitle = $subtitle ?? null;
?>
<div class="bg-gradient-to-r from-amber-50/80 via-white to-orange-50/50 rounded-2xl p-6 border border-amber-200/60 shadow-lg">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                <i class="<?php echo e($icon); ?> text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-1"><?php echo e($title); ?></h1>
                <?php if($subtitle): ?>
                    <p class="text-sm sm:text-base text-slate-600 font-medium"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if(!empty($actions)): ?>
            <div class="flex flex-wrap items-center gap-2 shrink-0"><?php echo $actions; ?></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\_header.blade.php ENDPATH**/ ?>