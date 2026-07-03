

<?php $__env->startSection('title', 'إرسال واتساب يدوي — ' . $workshop->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $total = count($recipients);
    $contactedCount = collect($recipients)->where('contacted', true)->count();
    $pendingCount = $total - $contactedCount;
    $markUrlTemplate = route('admin.workshops.whatsapp-contacted', ['workshop' => $workshop->id, 'registration' => '__ID__']);
?>

<div class="p-4 sm:p-6 lg:p-8 space-y-5" style="background: #f8fafc; min-height: 100vh;">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-5 border-b border-slate-100 bg-gradient-to-r from-green-50 to-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-600"></i>
                        إرسال واتساب — <?php echo e($workshop->title); ?>

                    </h1>
                    <p class="text-sm text-slate-600 mt-1">
                        اضغط «فتح واتساب» لكل مشترك — يُفتح WhatsApp Web بالرسالة جاهزة ويُسجَّل التواصل تلقائياً.
                    </p>
                </div>
                <a href="<?php echo e(route('admin.workshops.show', $workshop)); ?>"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shrink-0">
                    <i class="fas fa-arrow-right"></i>
                    العودة للورشة
                </a>
            </div>
        </div>

        
        <div class="p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="font-bold text-slate-800">
                    التقدم: <span id="progress-done"><?php echo e($contactedCount); ?></span> / <span id="progress-total"><?php echo e($total); ?></span>
                </span>
                <span class="text-xs text-slate-500">
                    متبقي: <strong id="progress-pending" class="text-amber-700"><?php echo e($pendingCount); ?></strong>
                </span>
            </div>
            <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                <div id="progress-bar"
                     class="h-full bg-gradient-to-r from-green-500 to-emerald-400 transition-all duration-500"
                     style="width: <?php echo e($total > 0 ? round($contactedCount / $total * 100) : 0); ?>%"></div>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <p class="text-xs font-bold text-slate-500 mb-2">قالب الرسالة (يُخصَّص لكل مشترك):</p>
        <div class="text-sm text-slate-800 whitespace-pre-line rounded-xl bg-slate-50 border border-slate-200 p-4"><?php echo e($message); ?></div>
    </section>

    
    <div class="flex flex-wrap gap-2">
        <button type="button" data-filter="all"
                class="wa-filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-green-600 text-white">
            الكل (<?php echo e($total); ?>)
        </button>
        <button type="button" data-filter="pending"
                class="wa-filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            لم يُتواصل (<?php echo e($pendingCount); ?>)
        </button>
        <button type="button" data-filter="done"
                class="wa-filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            تم التواصل (<?php echo e($contactedCount); ?>)
        </button>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">#</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الاسم</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الرقم</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الحضور</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="recipients-body">
                    <?php $__currentLoopData = $recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="wa-row <?php echo e($item['contacted'] ? 'wa-done' : 'wa-pending'); ?>"
                            data-status="<?php echo e($item['contacted'] ? 'done' : 'pending'); ?>"
                            data-id="<?php echo e($item['registration_id']); ?>">
                            <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($i + 1); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($item['name']); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700 dir-ltr text-right font-mono"><?php echo e($item['phone_display'] ?? $item['phone']); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($item['attendance'] ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <span class="wa-status-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold
                                    <?php echo e($item['contacted'] ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'); ?>">
                                    <?php if($item['contacted']): ?>
                                        <i class="fas fa-check-circle"></i>
                                        <span class="wa-status-text">تم التواصل</span>
                                        <?php if($item['contacted_at']): ?>
                                            <span class="wa-status-time text-[10px] opacity-80">(<?php echo e($item['contacted_at']); ?>)</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <i class="fas fa-clock"></i>
                                        <span class="wa-status-text">في الانتظار</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        class="wa-open-btn inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-sm font-bold transition-all
                                            <?php echo e($item['contacted'] ? 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' : 'bg-green-600 text-white hover:bg-green-700 shadow-sm'); ?>"
                                        data-url="<?php echo e($item['url']); ?>"
                                        data-reg-id="<?php echo e($item['registration_id']); ?>"
                                        <?php echo e($item['contacted'] ? '' : ''); ?>>
                                    <i class="fab fa-whatsapp"></i>
                                    <span><?php echo e($item['contacted'] ? 'إعادة الفتح' : 'فتح واتساب'); ?></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const markUrlTemplate = <?php echo json_encode($markUrlTemplate, 15, 512) ?>;
    let doneCount = <?php echo e($contactedCount); ?>;
    const totalCount = <?php echo e($total); ?>;

    function markUrl(regId) {
        return markUrlTemplate.replace('__ID__', regId);
    }

    function updateProgress() {
        const pending = totalCount - doneCount;
        document.getElementById('progress-done').textContent = doneCount;
        document.getElementById('progress-pending').textContent = pending;
        document.getElementById('progress-bar').style.width = (totalCount > 0 ? Math.round(doneCount / totalCount * 100) : 0) + '%';
    }

    function setRowContacted(row, contactedAt) {
        row.dataset.status = 'done';
        row.classList.remove('wa-pending');
        row.classList.add('wa-done');

        const badge = row.querySelector('.wa-status-badge');
        badge.className = 'wa-status-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
        badge.innerHTML = '<i class="fas fa-check-circle"></i><span class="wa-status-text">تم التواصل</span>'
            + (contactedAt ? '<span class="wa-status-time text-[10px] opacity-80">(' + contactedAt + ')</span>' : '');

        const btn = row.querySelector('.wa-open-btn');
        btn.className = 'wa-open-btn inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-sm font-bold transition-all bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200';
        btn.querySelector('span').textContent = 'إعادة الفتح';

        if (!row.dataset.wasCounted) {
            doneCount++;
            row.dataset.wasCounted = '1';
            updateProgress();
        }
    }

    document.querySelectorAll('.wa-open-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const url = this.dataset.url;
            const regId = this.dataset.regId;
            const row = this.closest('.wa-row');

            window.open(url, '_blank', 'noopener');

            if (row.dataset.status === 'done') return;

            try {
                const res = await fetch(markUrl(regId), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    setRowContacted(row, data.contacted_at || '');
                }
            } catch (e) {
                console.warn('mark contacted failed', e);
            }
        });
    });

    // Filters
    document.querySelectorAll('.wa-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const filter = this.dataset.filter;
            document.querySelectorAll('.wa-filter-btn').forEach(b => {
                b.className = 'wa-filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50';
            });
            this.className = 'wa-filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-green-600 text-white';

            document.querySelectorAll('.wa-row').forEach(row => {
                const show = filter === 'all'
                    || (filter === 'pending' && row.dataset.status === 'pending')
                    || (filter === 'done' && row.dataset.status === 'done');
                row.style.display = show ? '' : 'none';
            });
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\whatsapp-launch.blade.php ENDPATH**/ ?>