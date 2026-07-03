<?php $__env->startSection('title', $pattern->title); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('student.learning-patterns.partials.pattern-content', ['embed' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('student.learning-patterns.partials.pattern-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.embed', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\learning-patterns\embed.blade.php ENDPATH**/ ?>