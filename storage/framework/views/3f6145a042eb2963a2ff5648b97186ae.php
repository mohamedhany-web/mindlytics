<?php $__env->startSection('title', __('student.achievements_title')); ?>
<?php $__env->startSection('header', __('student.achievements_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo e(__('student.achievements_title')); ?></h1>
        <?php if(isset($stats)): ?>
        <div class="mt-4">
            <div class="text-sm text-gray-600"><?php echo e(__('student.total_points')); ?></div>
            <div class="text-3xl font-bold text-sky-600 mt-2"><?php echo e($stats['total_points'] ?? 0); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <?php if(isset($achievements) && $achievements->count() > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-center">
                <?php if($achievement->achievement && $achievement->achievement->icon): ?>
                <i class="<?php echo e($achievement->achievement->icon); ?> text-6xl text-yellow-600 mb-4"></i>
                <?php else: ?>
                <i class="fas fa-trophy text-6xl text-yellow-600 mb-4"></i>
                <?php endif; ?>
                <h3 class="text-lg font-bold text-gray-900"><?php echo e($achievement->achievement->name ?? __('student.achievement_default')); ?></h3>
                <p class="text-sm text-gray-600 mt-2"><?php echo e($achievement->achievement->description ?? ''); ?></p>
                <?php if($achievement->points_earned): ?>
                <div class="mt-4 text-sky-600 font-bold">+<?php echo e($achievement->points_earned); ?> <?php echo e(__('student.points_earned')); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-4"><?php echo e($achievements->links()); ?></div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-trophy text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600"><?php echo e(__('student.no_achievements')); ?></p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\achievements\index.blade.php ENDPATH**/ ?>