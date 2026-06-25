<?php
    $title = $title ?? '';
    $description = $description ?? '';
    $icon = $icon ?? 'fa-percentage';
    $iconGradient = $iconGradient ?? 'from-violet-500 to-purple-600';
    $meta = $meta ?? null;
    $actions = $actions ?? [];
?>
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br <?php echo e($iconGradient); ?> flex items-center justify-center text-white shadow-md">
                <i class="fas <?php echo e($icon); ?>"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900"><?php echo e($title); ?></h2>
                <?php if($description): ?>
                    <p class="text-xs text-slate-600"><?php echo e($description); ?></p>
                <?php endif; ?>
                <?php if($meta): ?>
                    <p class="text-[11px] text-slate-500 mt-1"><?php echo e($meta); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if($actions !== []): ?>
            <div class="flex flex-wrap items-center gap-2">
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $style = $action['style'] ?? 'secondary';
                        $classes = match ($style) {
                            'primary' => 'text-white bg-violet-600 hover:bg-violet-700 border-transparent',
                            'success' => 'text-white bg-emerald-600 hover:bg-emerald-700 border-transparent',
                            'warning' => 'text-slate-900 bg-amber-400 hover:bg-amber-300 border-transparent',
                            default => 'text-slate-700 border-slate-300 hover:bg-white',
                        };
                    ?>
                    <?php if(!empty($action['route']) && Route::has($action['route'])): ?>
                        <a href="<?php echo e(route($action['route'], $action['params'] ?? [])); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border <?php echo e($classes); ?>">
                            <?php if(!empty($action['icon'])): ?>
                                <i class="fas <?php echo e($action['icon']); ?> <?php echo e($style === 'secondary' ? 'text-slate-500' : ''); ?>"></i>
                            <?php endif; ?>
                            <?php echo e($action['label']); ?>

                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/partials/header.blade.php ENDPATH**/ ?>