<?php
    $statusLabels = [
        'draft' => 'مسودة',
        'pending' => 'معلقة',
        'partial' => 'مدفوعة جزئياً',
        'paid' => 'مدفوعة',
        'overdue' => 'متأخرة',
        'cancelled' => 'ملغاة',
        'refunded' => 'مستردة',
    ];
    $selected = $selected ?? old('status', 'pending');
?>
<?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($value); ?>" <?php if($selected === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/invoices/partials/status-options.blade.php ENDPATH**/ ?>