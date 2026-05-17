<?php $__env->startSection('title', $pattern->title . ' - ' . $course->title); ?>
<?php $__env->startSection('header', $pattern->title); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('student.learning-patterns.partials.pattern-content', ['embed' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('student.learning-patterns.partials.pattern-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/learning-patterns/show.blade.php ENDPATH**/ ?>