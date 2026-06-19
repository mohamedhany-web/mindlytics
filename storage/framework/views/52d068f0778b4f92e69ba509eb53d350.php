<?php $lead = $lead ?? null; ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php echo $__env->make('employee.sales._lead_fields_inner', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\_lead_fields.blade.php ENDPATH**/ ?>