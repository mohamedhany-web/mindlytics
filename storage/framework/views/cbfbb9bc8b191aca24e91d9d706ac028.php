<?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-50 border-2 border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-semibold text-sm"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="rounded-xl bg-rose-50 border-2 border-rose-200 text-rose-800 px-5 py-4 flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
        <span class="font-semibold text-sm"><?php echo e(session('error')); ?></span>
    </div>
<?php endif; ?>

<?php if(isset($errors) && $errors->any()): ?>
    <div class="rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء:</p>
        <ul class="list-disc list-inside space-y-0.5 mr-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($e); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/_alerts.blade.php ENDPATH**/ ?>