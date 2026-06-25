<?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span class="font-semibold"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-circle text-red-600"></i>
        <span class="font-semibold"><?php echo e(session('error')); ?></span>
    </div>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/marketing/_flash.blade.php ENDPATH**/ ?>