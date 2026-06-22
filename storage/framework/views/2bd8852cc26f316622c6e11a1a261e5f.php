<?php
    $active = $active ?? '';
    $navItems = [
        'dashboard' => ['route' => 'admin.whatsapp.index', 'icon' => 'fas fa-tachometer-alt', 'label' => 'لوحة الواتساب'],
        'send' => ['route' => 'admin.whatsapp.send', 'icon' => 'fas fa-paper-plane', 'label' => 'إرسال رسالة'],
        'messages' => ['route' => 'admin.whatsapp.messages', 'icon' => 'fas fa-list', 'label' => 'سجل الرسائل'],
        'batches' => ['route' => 'admin.whatsapp.batches.index', 'icon' => 'fas fa-layer-group', 'label' => 'دفعات الإرسال'],
        'settings' => ['route' => 'admin.whatsapp.settings', 'icon' => 'fas fa-plug', 'label' => 'إعدادات الربط'],
    ];
?>
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border-2 border-slate-200/50 shadow-sm">
    <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route($item['route'])); ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === $key ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white shadow-md shadow-emerald-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
            <i class="<?php echo e($item['icon']); ?>"></i>
            <?php echo e($item['label']); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('admin.messages.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-sky-700 border border-sky-200 bg-sky-50 hover:bg-sky-100 transition-all mr-auto">
        <i class="fas fa-envelope"></i>
        الرسائل العامة
    </a>
</nav>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\_nav.blade.php ENDPATH**/ ?>