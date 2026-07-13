<?php $cards = $cards ?? []; ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
            <div class="flex items-center justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 mb-1"><?php echo e($card['label']); ?></p>
                    <p class="text-3xl font-black text-slate-900 tabular-nums"><?php echo e($card['value']); ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="<?php echo e($card['icon'] ?? 'fas fa-chart-line'); ?>"></i>
                </div>
            </div>
            <?php if(!empty($card['description'])): ?>
                <p class="text-xs text-slate-600 font-medium"><?php echo e($card['description']); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\_stats-grid.blade.php ENDPATH**/ ?>