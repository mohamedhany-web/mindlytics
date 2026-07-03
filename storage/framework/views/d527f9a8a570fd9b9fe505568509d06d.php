

<?php $__env->startSection('title', $whatsappGroup->subject); ?>
<?php $__env->startSection('header', 'مجموعة واتساب: '.$whatsappGroup->subject); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold text-slate-900"><?php echo e($whatsappGroup->subject); ?></h2>
                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold <?php echo e($whatsappGroup->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($whatsappGroup->statusLabel()); ?></span>
            </div>
            <?php if($whatsappGroup->description): ?>
                <p class="text-sm text-slate-500 mt-1"><?php echo e($whatsappGroup->description); ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo e($r('index')); ?>" class="btn-wa-secondary">← المجموعات</a>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php echo $__env->make('employee.sales.whatsapp-groups._show_body', compact('r', 'whatsappGroup', 'inviteTemplates', 'availableLeads', 'crmGroups'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\show.blade.php ENDPATH**/ ?>