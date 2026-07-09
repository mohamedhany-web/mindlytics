<?php
    $badge = match($status) {
        'sent' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'failed' => 'bg-rose-100 text-rose-800 border-rose-200',
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        'cancelled' => 'bg-slate-200 text-slate-700 border-slate-300',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
?>
<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border <?php echo e($badge); ?>"><?php echo e($label); ?></span>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\batches\_status-badge.blade.php ENDPATH**/ ?>