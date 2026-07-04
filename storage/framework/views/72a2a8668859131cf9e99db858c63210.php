
<section class="<?php echo e($smSectionClass ?? 'rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden'); ?>">
    <div class="px-5 py-5 sm:px-6 border-b border-slate-200 bg-gradient-to-r from-sky-50/80 via-white to-blue-50/50 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-sky-500/25 flex-shrink-0">
                <i class="<?php echo e($icon ?? 'fab fa-meta'); ?> text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900"><?php echo e($title); ?></h2>
                <?php if(!empty($subtitle)): ?>
                    <p class="text-sm text-slate-600 mt-0.5"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if(!empty($actions)): ?>
            <div class="flex flex-wrap items-center gap-2">
                <?php echo $actions; ?>

            </div>
        <?php endif; ?>
    </div>
    <?php if(!empty($statCards) && is_array($statCards)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-<?php echo e(min(count($statCards), 4)); ?> gap-3 p-4 sm:p-5">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm card-hover-effect">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg'] ?? 'bg-sky-100'); ?> flex items-center justify-center <?php echo e($card['text'] ?? 'text-sky-600'); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon'] ?? 'fas fa-chart-bar'); ?> text-sm"></i>
                        </div>
                    </div>
                    <?php if(!empty($card['description'])): ?>
                        <p class="text-[11px] text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</section>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/meta-social/_page-header.blade.php ENDPATH**/ ?>