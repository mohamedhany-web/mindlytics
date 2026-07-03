<?php if($groups->isEmpty()): ?>
    <div class="sales-panel p-8 text-center text-slate-600">
        <p class="mb-4">لا توجد مجموعات واتساب.</p>
        <a href="<?php echo e($r('create')); ?>" class="inline-flex px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold">إنشاء مجموعة</a>
    </div>
<?php else: ?>
    <div class="grid gap-3">
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($r('show', $group)); ?>" class="sales-panel p-4 block hover:border-emerald-300">
                <div class="flex justify-between gap-2">
                    <h3 class="font-bold"><?php echo e($group->subject); ?></h3>
                    <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100'); ?>"><?php echo e($group->statusLabel()); ?></span>
                </div>
                <p class="text-xs text-slate-500 mt-1"><?php echo e($group->participants_count); ?> عضو · <?php echo e($group->creator?->name); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-4"><?php echo e($groups->links()); ?></div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\_list.blade.php ENDPATH**/ ?>