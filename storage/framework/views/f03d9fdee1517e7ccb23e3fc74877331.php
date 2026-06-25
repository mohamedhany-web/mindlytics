

<?php $__env->startSection('title', 'متابعة واتساب — '.$group->name); ?>
<?php $__env->startSection('header', 'متابعة إرسال واتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-4" id="wa-batch-page"
     data-status-url="<?php echo e(route('employee.sales.groups.whatsapp-batches.status', [$group, $batch])); ?>">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900"><?php echo e($batch->title ?: ('دفعة #'.$batch->id)); ?></h2>
            <p class="text-sm text-slate-500">مجموعة: <?php echo e($group->name); ?></p>
        </div>
        <a href="<?php echo e(route('employee.sales.groups.show', $group)); ?>" class="px-4 py-2 text-sm border border-slate-200 rounded-lg">رجوع للمجموعة</a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="sales-panel p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span id="batch-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                <?php if($batch->status === 'processing'): ?> bg-sky-100 text-sky-800 border-sky-200
                <?php elseif($batch->status === 'completed'): ?> bg-emerald-100 text-emerald-800 border-emerald-200
                <?php else: ?> bg-amber-100 text-amber-800 border-amber-200 <?php endif; ?>">
                <i id="batch-status-icon" class="fas <?php echo e($batch->status === 'processing' ? 'fa-spinner fa-spin' : 'fa-info-circle'); ?>"></i>
                <span id="batch-status-label"><?php echo e($batch->statusLabel()); ?></span>
            </span>
            <span class="text-sm font-bold tabular-nums"><span id="batch-progress-text"><?php echo e($batch->progressPercent()); ?></span>%</span>
        </div>
        <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
            <div id="batch-progress-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width: <?php echo e($batch->progressPercent()); ?>%"></div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center text-xs">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 py-2">
                <p class="text-emerald-800 font-bold text-lg tabular-nums" id="stat-sent"><?php echo e($batch->sent_count); ?></p>
                <p class="text-emerald-700/80">نجح</p>
            </div>
            <div class="rounded-lg bg-rose-50 border border-rose-200 py-2">
                <p class="text-rose-800 font-bold text-lg tabular-nums" id="stat-failed"><?php echo e($batch->failed_count); ?></p>
                <p class="text-rose-700/80">فشل</p>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-200 py-2">
                <p class="text-amber-800 font-bold text-lg tabular-nums" id="stat-pending"><?php echo e($batch->pendingCount()); ?></p>
                <p class="text-amber-700/80">متبقي</p>
            </div>
        </div>
        <p class="text-[11px] text-slate-500">الإرسال يعمل في الخلفية — تُحدَّث الصفحة كل 3 ثوانٍ.</p>
    </div>

    <div class="sales-panel overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 font-bold text-sm">تفاصيل المستلمين</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600">
                    <tr>
                        <th class="text-right p-3">الاسم</th>
                        <th class="text-right p-3">الهاتف</th>
                        <th class="text-right p-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="p-3"><?php echo e($item->recipient_name); ?></td>
                            <td class="p-3 font-mono text-xs dir-ltr text-right"><?php echo e($item->phone); ?></td>
                            <td class="p-3">
                                <span class="text-xs font-semibold
                                    <?php if($item->status === 'sent'): ?> text-emerald-700
                                    <?php elseif($item->status === 'failed'): ?> text-rose-700
                                    <?php else: ?> text-amber-700 <?php endif; ?>">
                                    <?php echo e($item->statusLabel()); ?>

                                </span>
                                <?php if($item->error_message): ?>
                                    <p class="text-[10px] text-rose-600 mt-0.5"><?php echo e(Str::limit($item->error_message, 80)); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
            <div class="p-3"><?php echo e($items->links()); ?></div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const root = document.getElementById('wa-batch-page');
    if (!root) return;
    const url = root.dataset.statusUrl;
    let timer = null;

    async function poll() {
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('batch-progress-text').textContent = data.progress ?? 0;
            document.getElementById('batch-progress-bar').style.width = (data.progress ?? 0) + '%';
            document.getElementById('stat-sent').textContent = data.sent ?? 0;
            document.getElementById('stat-failed').textContent = data.failed ?? 0;
            document.getElementById('stat-pending').textContent = data.pending ?? 0;
            document.getElementById('batch-status-label').textContent = data.status_label ?? '';
            if (data.finished) {
                clearInterval(timer);
                const icon = document.getElementById('batch-status-icon');
                if (icon) icon.className = 'fas fa-check-circle';
            }
        } catch (_) {}
    }

    timer = setInterval(poll, 3000);
    poll();
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/sales/groups/whatsapp-batch.blade.php ENDPATH**/ ?>