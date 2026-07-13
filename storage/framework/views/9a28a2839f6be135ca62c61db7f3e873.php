<?php
    $icon = $icon ?? 'fas fa-list';
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $actions = $actions ?? null;
?>
<div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
            <i class="<?php echo e($icon); ?> text-lg"></i>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900"><?php echo e($title); ?></h3>
            <?php if($subtitle): ?>
                <p class="text-xs text-slate-600 font-medium mt-1"><?php echo $subtitle; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php if(!empty($actions)): ?>
        <div class="flex flex-wrap items-center gap-2 shrink-0"><?php echo $actions; ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\_section-head.blade.php ENDPATH**/ ?>