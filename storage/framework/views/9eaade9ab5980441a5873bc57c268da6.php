<?php
    $badge = $badge ?? null;
    $subtitle = $subtitle ?? null;
    $backUrl = $backUrl ?? null;
    $backLabel = $backLabel ?? 'جميع الوظائف';
    $metaChips = $metaChips ?? [];
?>

<section class="hero-section relative overflow-hidden min-h-[48vh] flex items-center pt-24 pb-14 lg:pt-32 lg:pb-20">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 max-w-5xl">
        <?php if($backUrl): ?>
            <a href="<?php echo e($backUrl); ?>" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-900 text-sm font-bold mb-6 transition-colors">
                <i class="fas fa-arrow-right"></i>
                <?php echo e($backLabel); ?>

            </a>
        <?php endif; ?>

        <?php if($badge): ?>
            <div class="text-center mb-5 fade-in-up">
                <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold">
                    <i class="fas fa-briefcase text-blue-600"></i>
                    <?php echo e($badge); ?>

                </span>
            </div>
        <?php endif; ?>

        <div class="text-center fade-in-up" style="animation-delay: 0.05s;">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-blue-900 leading-tight mb-4">
                <?php echo e($title); ?>

            </h1>
            <?php if($subtitle): ?>
                <p class="text-lg md:text-xl text-blue-700/90 max-w-3xl mx-auto leading-relaxed font-medium">
                    <?php echo e($subtitle); ?>

                </p>
            <?php endif; ?>
        </div>

        <?php if(!empty($metaChips)): ?>
            <div class="mt-8 flex flex-wrap justify-center gap-2 fade-in-up" style="animation-delay: 0.1s;">
                <?php $__currentLoopData = $metaChips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="job-meta-chip <?php echo e($chip['tone'] ?? 'blue'); ?>">
                        <?php if(!empty($chip['icon'])): ?>
                            <i class="<?php echo e($chip['icon']); ?>"></i>
                        <?php endif; ?>
                        <?php echo e($chip['label']); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/careers/_hero.blade.php ENDPATH**/ ?>