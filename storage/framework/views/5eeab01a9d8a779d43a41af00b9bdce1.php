<?php $connected = (bool) ($cloud['connected'] ?? false); ?>
<div class="flex flex-wrap items-center justify-between gap-3 sales-panel px-4 py-3">
    <div class="flex flex-wrap items-center gap-3">
        <span class="wa-cloud-pill <?php echo e($connected ? 'wa-cloud-pill--ok' : 'wa-cloud-pill--warn'); ?>">
            <i class="fab fa-whatsapp"></i>
            <?php echo e($connected ? ($cloud['label'] ?? 'Meta Cloud متصل') : 'Meta Cloud غير جاهز'); ?>

        </span>
        <?php if($connected): ?>
            <span class="text-xs text-slate-500">مجموعات واتساب عبر Meta Cloud API</span>
        <?php else: ?>
            <span class="text-xs text-amber-800"><?php echo e($cloud['error'] ?? 'أكمل الربط من إعدادات الواتساب'); ?></span>
        <?php endif; ?>
    </div>
    <?php if(($settingsUrl ?? null) && ! $connected): ?>
        <a href="<?php echo e($settingsUrl); ?>" class="text-xs font-semibold text-sky-700 hover:underline">إعدادات الربط ←</a>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/whatsapp-groups/_cloud_status.blade.php ENDPATH**/ ?>