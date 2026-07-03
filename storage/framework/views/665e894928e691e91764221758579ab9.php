

<?php $__env->startSection('title', 'مجموعة واتساب جديدة'); ?>
<?php $__env->startSection('header', 'مجموعة واتساب جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p);
    $settingsUrl = route('admin.whatsapp.settings');
?>

<div class="space-y-4 max-w-3xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعة واتساب جديدة</h2>
            <p class="text-sm text-slate-500 mt-0.5">تُنشأ عبر Meta Cloud — الدعوات تُرسل بقالب Group Invite</p>
        </div>
        <a href="<?php echo e($r('index')); ?>" class="btn-wa-secondary">← المجموعات</a>
    </div>

    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php echo $__env->make('employee.sales.whatsapp-groups._cloud_status', ['cloud' => $cloud, 'settingsUrl' => $settingsUrl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('employee.sales.whatsapp-groups._form_create', compact('r', 'crmGroups', 'prefillCrmGroupId', 'prefillParticipants', 'inviteTemplates'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/whatsapp-groups/create.blade.php ENDPATH**/ ?>