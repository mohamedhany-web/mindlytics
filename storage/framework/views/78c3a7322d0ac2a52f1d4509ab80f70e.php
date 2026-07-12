<div class="flex <?php echo e($msg->isInbound() ? 'justify-start' : 'justify-end'); ?>">
    <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl px-4 py-2.5 shadow-sm text-sm whitespace-pre-wrap break-words
        <?php echo e($msg->isInbound()
            ? 'bg-white text-slate-800 rounded-tl-sm'
            : 'bg-emerald-100 text-emerald-950 rounded-tr-sm border border-emerald-200'); ?>">
        <p><?php echo e($msg->displayBody()); ?></p>
        <?php if($msg->error_message && $msg->status === 'failed'): ?>
            <p class="text-[10px] text-rose-600 mt-1"><?php echo e($msg->error_message); ?></p>
        <?php endif; ?>
        <div class="flex items-center gap-2 mt-1 text-[10px] opacity-60">
            <span><?php echo e($msg->created_at?->format('Y-m-d H:i')); ?></span>
            <?php if(!$msg->isInbound()): ?>
                <span>· <?php echo e($msg->sentBy?->name ?? 'النظام'); ?></span>
                <?php if(in_array($msg->status, ['delivered', 'read'], true)): ?>
                    <i class="fas fa-check-double text-sky-600"></i>
                <?php elseif($msg->status === 'sent'): ?>
                    <i class="fas fa-check"></i>
                <?php elseif($msg->status === 'failed'): ?>
                    <i class="fas fa-times text-rose-500"></i>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\_inbox_message.blade.php ENDPATH**/ ?>