<?php if($groups->isEmpty()): ?>
    <div class="sales-panel p-10 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-emerald-50 flex items-center justify-center">
            <i class="fab fa-whatsapp text-2xl text-emerald-500"></i>
        </div>
        <p class="text-slate-700 font-semibold mb-1">لا توجد مجموعات واتساب بعد</p>
        <p class="text-sm text-slate-500 mb-5">أنشئ مجموعة على Meta Cloud وأرسل دعوات للعملاء بقالب Group Invite</p>
        <a href="<?php echo e($r('create')); ?>" class="btn-wa-primary">
            <i class="fas fa-plus"></i> إنشاء مجموعة
        </a>
    </div>
<?php else: ?>
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($r('show', $group)); ?>" class="wa-group-card block">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-bold text-slate-900 line-clamp-2"><?php echo e($group->subject); ?></h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold shrink-0 <?php echo e($group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'); ?>">
                        <?php echo e($group->statusLabel()); ?>

                    </span>
                </div>
                <?php if($group->description): ?>
                    <p class="text-xs text-slate-500 mt-2 line-clamp-2"><?php echo e($group->description); ?></p>
                <?php endif; ?>
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600">
                    <span><i class="fas fa-users ml-1 text-slate-400"></i> <?php echo e($group->participants_count); ?> مدعو</span>
                    <span class="text-slate-800 font-semibold">إدارة ←</span>
                </div>
                <?php if($group->salesLeadGroup): ?>
                    <p class="text-[10px] text-sky-700 mt-2 truncate"><i class="fas fa-layer-group ml-1"></i> <?php echo e($group->salesLeadGroup->name); ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($groups->hasPages()): ?>
        <div class="mt-4"><?php echo e($groups->links()); ?></div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\_list.blade.php ENDPATH**/ ?>