<?php
    $tabs = [
        'referrals' => ['label' => 'برامج الإحالة', 'route' => route('admin.referral-programs.index'), 'icon' => 'fa-gift'],
        'promo' => ['label' => 'أكواد الورش', 'route' => route('admin.workshop-promo-codes.index'), 'icon' => 'fa-ticket-alt'],
        'list' => ['label' => 'سجل الإحالات', 'route' => route('admin.referrals.index'), 'icon' => 'fa-users'],
    ];
?>
<nav class="flex flex-wrap gap-2 p-1 rounded-2xl bg-white border border-slate-200 shadow-sm">
    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($tab['route']); ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all <?php echo e(($active ?? '') === $key ? 'bg-gradient-to-r from-sky-600 to-violet-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50'); ?>">
            <i class="fas <?php echo e($tab['icon']); ?>"></i>
            <?php echo e($tab['label']); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/marketing/_tabs.blade.php ENDPATH**/ ?>