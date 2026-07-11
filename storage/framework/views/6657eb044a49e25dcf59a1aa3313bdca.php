<?php $cards = $cards ?? []; ?>
<?php if(count($cards) > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?php echo e(min(count($cards), 4)); ?> gap-4 sm:gap-6">
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
            <div class="flex items-center justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 mb-2"><?php echo e($stat['label']); ?></p>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900"><?php echo e($stat['value']); ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 mr-3 sm:mr-0">
                    <i class="<?php echo e($stat['icon'] ?? 'fas fa-chart-bar'); ?> text-white text-xl"></i>
                </div>
            </div>
            <?php if(!empty($stat['description'])): ?>
                <p class="text-xs font-medium text-slate-600"><?php echo e($stat['description']); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/_stats-grid.blade.php ENDPATH**/ ?>