<?php
    $typeLabels = [
        'course' => 'كورس أونلاين',
        'subscription' => 'اشتراك',
        'membership' => 'عضوية',
        'learning_path' => 'مسار تعليمي',
        'offline_course' => 'كورس أوفلاين',
        'other' => 'أخرى',
    ];
    $selected = $selected ?? old('type', 'course');
?>
<?php $__currentLoopData = $typeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($value); ?>" <?php if($selected === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/invoices/partials/type-options.blade.php ENDPATH**/ ?>