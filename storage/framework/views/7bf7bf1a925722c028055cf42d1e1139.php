<?php $__env->startSection('title', 'متابعة دفعة واتساب #' . $batch->id . ' - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'batches'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $failedItemsCount = (int) $batch->items()->where('status', 'failed')->count();
        $pendingItemsCount = $batch->pendingCount();
        $autoDriveBatch = ! $batch->isFinished() || $pendingItemsCount > 0;
        $backUrl = $workshop
            ? route('admin.workshops.show', $workshop)
            : ($salesGroup ?? null
                ? route('admin.sales.groups.show', $salesGroup)
                : route('admin.whatsapp.batches.index'));
    ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => $batch->title ?: ('دفعة #' . $batch->id),
        'subtitle' => 'متابعة حية — يتحدّث تلقائياً كل 3 ثوانٍ أثناء الإرسال.',
        'icon' => 'fas fa-tasks',
        'actions' => '<a href="' . $backUrl . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع</a>',
        'statCards' => [
            ['label' => 'إجمالي', 'value' => $batch->total_count, 'icon' => 'fas fa-users', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
            ['label' => 'تم الإرسال', 'value' => $batch->sent_count, 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'فشل', 'value' => $batch->failed_count, 'icon' => 'fas fa-times-circle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'متبقي', 'value' => $batch->pendingCount(), 'icon' => 'fas fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <section class="<?php echo e($waSectionClass); ?>" id="batch-progress-panel">
        <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span id="batch-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                        <?php if($batch->status === 'processing'): ?> bg-sky-100 text-sky-800 border-sky-200
                        <?php elseif($batch->status === 'completed'): ?> bg-emerald-100 text-emerald-800 border-emerald-200
                        <?php elseif($batch->status === 'cancelled'): ?> bg-slate-200 text-slate-800 border-slate-300
                        <?php else: ?> bg-amber-100 text-amber-800 border-amber-200 <?php endif; ?>">
                        <i id="batch-status-icon" class="fas <?php echo e($batch->status === 'processing' ? 'fa-spinner fa-spin' : 'fa-info-circle'); ?>"></i>
                        <span id="batch-status-label"><?php echo e($batch->statusLabel()); ?></span>
                    </span>
                    <?php if(!$batch->isFinished()): ?>
                        <span class="text-xs text-slate-500">الإرسال يعمل تلقائياً — تُعالَج الدفعة من هذه الصفحة ومن طابور whatsapp كل دقيقة</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm font-bold text-slate-700 tabular-nums"><span id="batch-progress-text"><?php echo e($batch->progressPercent()); ?></span>%</p>
            </div>
            <div class="h-3 rounded-full bg-slate-200 overflow-hidden">
                <div id="batch-progress-bar" class="h-full bg-gradient-to-r from-emerald-500 to-green-400 transition-all duration-500" style="width: <?php echo e($batch->progressPercent()); ?>%"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center text-xs">
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
            <?php if(!$batch->isFinished() && $pendingItemsCount > 0): ?>
                <div class="flex flex-wrap items-center gap-3 px-5 pb-5">
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.batches.cancel', $batch)); ?>"
                          onsubmit="return confirm('إيقاف الإرسال؟\n\nلن تُرسل الرسائل المتبقية (<?php echo e($pendingItemsCount); ?>). الرسائل التي أُرسلت بالفعل تبقى كما هي.');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                            <i class="fas fa-stop"></i>
                            إيقاف الإرسال
                        </button>
                    </form>
                    <?php if($failedItemsCount > 0): ?>
                        <form method="POST" action="<?php echo e(route('admin.whatsapp.batches.retry', $batch)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="<?php echo e($waBtnPrimary); ?> !text-sm">
                                <i class="fas fa-redo"></i>
                                إعادة إرسال الفاشلة فقط (<?php echo e($failedItemsCount); ?>)
                            </button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.batches.retry', $batch)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="<?php echo e($waBtnSecondary); ?> !text-sm">
                            <i class="fas fa-play"></i>
                            متابعة المعلّقة (<?php echo e($pendingItemsCount); ?>)
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-500 w-full">الرسائل المرسلة بنجاح لا تُعاد. إذا بقيت «في الانتظار» أكثر من دقيقة، جرّب «متابعة المعلّقة».</p>
                </div>
            <?php elseif($failedItemsCount > 0 && $batch->status !== 'cancelled'): ?>
                <div class="flex flex-wrap items-center gap-3 px-5 pb-5 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-sm text-rose-800 font-semibold w-full sm:w-auto">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo e($failedItemsCount); ?> رسالة فشل إرسالها — يمكنك إعادة إرسالها فقط دون المساس بالرسائل الناجحة.
                    </p>
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.batches.retry', $batch)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                            <i class="fas fa-redo"></i>
                            إعادة إرسال كل الفاشلة (<?php echo e($failedItemsCount); ?>)
                        </button>
                    </form>
                    <p class="text-[11px] text-rose-700/80 w-full">أو أعد إرسال رسالة واحدة من عمود «إجراء» في الجدول أدناه.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-check text-emerald-600"></i>
                تفاصيل المستلمين
            </h3>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = ['all' => 'الكل', 'sent' => 'تم الإرسال', 'failed' => 'فشل', 'pending' => 'في الانتظار']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.whatsapp.batches.show', ['batch' => $batch, 'filter' => $key === 'all' ? null : $key])); ?>"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all <?php echo e(($filter ?? 'all') === $key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300'); ?>">
                        <?php echo e($label); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">#</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الاسم</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الهاتف</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">ملاحظة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white" id="batch-items-tbody">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80" data-item-id="<?php echo e($item->id); ?>">
                            <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($item->sort_order + 1); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($item->recipient_name ?: '—'); ?></td>
                            <td class="px-4 py-3 text-sm font-mono text-slate-700"><?php echo e($item->phone); ?></td>
                            <td class="px-4 py-3 item-status-cell">
                                <?php echo $__env->make('admin.whatsapp.batches._status-badge', ['status' => $item->status, 'label' => $item->statusLabel()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 item-error-cell">
                                <?php if($item->status === 'sent' && $item->sent_at): ?>
                                    <span class="text-emerald-700"><?php echo e($item->sent_at->format('Y-m-d H:i')); ?></span>
                                <?php elseif($item->error_message): ?>
                                    <span class="text-rose-700" title="<?php echo e($item->error_message); ?>"><?php echo e(Str::limit($item->error_message, 60)); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <?php if($item->status === 'failed' && $batch->status !== 'cancelled'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.whatsapp.batches.items.retry', [$batch, $item])); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-600 hover:bg-rose-700 text-white"
                                                title="إعادة إرسال هذه الرسالة فقط">
                                            <i class="fas fa-redo text-[10px]"></i>
                                            إعادة الإرسال
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-slate-300">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد عناصر.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200"><?php echo e($items->links()); ?></div>
        <?php endif; ?>
    </section>
</div>

<?php if($autoDriveBatch): ?>
<script>
(function () {
    const statusUrl = <?php echo json_encode(route('admin.whatsapp.batches.status', $batch), 512) ?>;
    const processUrl = <?php echo json_encode(route('admin.whatsapp.batches.process', $batch), 512) ?>;
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    let pollTimer = null;
    let processing = false;

    function badgeHtml(status, label) {
        const map = {
            sent: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            failed: 'bg-rose-100 text-rose-800 border-rose-200',
            pending: 'bg-amber-100 text-amber-800 border-amber-200',
        };
        const cls = map[status] || 'bg-slate-100 text-slate-700 border-slate-200';
        return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border ' + cls + '">' + label + '</span>';
    }

    function applyStatus(data) {
        document.getElementById('stat-sent').textContent = data.sent;
        document.getElementById('stat-failed').textContent = data.failed;
        document.getElementById('stat-pending').textContent = data.pending;
        document.getElementById('batch-progress-text').textContent = data.progress;
        document.getElementById('batch-progress-bar').style.width = data.progress + '%';
        document.getElementById('batch-status-label').textContent = data.status_label;

        if (data.finished) {
            clearInterval(pollTimer);
            document.getElementById('batch-status-icon').className = 'fas fa-check-circle';
            location.reload();
        }
    }

    async function poll() {
        try {
            const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            applyStatus(await res.json());
        } catch (e) {}
    }

    async function driveProcess() {
        if (processing) return;
        processing = true;
        try {
            const res = await fetch(processUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (res.ok) {
                const data = await res.json();
                if (data.finished !== undefined) {
                    applyStatus(data);
                }
            }
        } catch (e) {
            /* fallback to cron queue */
        } finally {
            processing = false;
        }
    }

    pollTimer = setInterval(poll, 4000);
    setInterval(driveProcess, 12000);
    poll();
    driveProcess();
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\batches\show.blade.php ENDPATH**/ ?>