

<?php $__env->startSection('title', 'طلبات واتساب'); ?>
<?php $__env->startSection('header', 'طلبات واتساب الجديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-l from-emerald-50 via-white to-teal-50/40 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600"></i>
                    طابور الطلبات
                </h2>
                <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                    محادثات واردة من أرقام غير مسجّلة كـ leads. اضغط «قبول» لتصبح العميل مسنداً إليك وتفتح المحادثة.
                </p>
            </div>
            <div class="shrink-0 text-center sm:text-left">
                <p class="text-3xl font-black text-emerald-700 tabular-nums"><?php echo e($conversations->total()); ?></p>
                <p class="text-xs text-slate-500">طلب في الانتظار</p>
            </div>
        </div>
    </div>

    <?php if(! $queueEnabled): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            طابور الانتظار غير مفعّل حالياً. راجع إعدادات الواتساب (<code class="text-xs">WHATSAPP_ASSIGNMENT_STRATEGY=manual_queue</code>).
        </div>
    <?php endif; ?>

    <div class="space-y-3" id="wa-queue-list">
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-emerald-200 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xl">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h3 class="font-bold text-slate-900"><?php echo e($conversation->displayName()); ?></h3>
                            <span class="text-xs text-slate-500 tabular-nums dir-ltr"><?php echo e($conversation->formattedPhone()); ?></span>
                        </div>
                        <p class="text-sm text-slate-600 line-clamp-2 mb-2">
                            <?php echo e($conversation->last_message_preview ?: '—'); ?>

                        </p>
                        <p class="text-xs text-slate-400">
                            <?php if($conversation->last_message_at): ?>
                                <?php echo e($conversation->last_message_at->diffForHumans()); ?>

                            <?php endif; ?>
                            <?php if($conversation->unread_count > 0): ?>
                                · <span class="text-emerald-700 font-semibold"><?php echo e($conversation->unread_count); ?> غير مقروء</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <form method="POST" action="<?php echo e(route('employee.sales.whatsapp.queue.accept', $conversation)); ?>" class="shrink-0">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors shadow-sm">
                            <i class="fas fa-check"></i>
                            قبول
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-slate-50 flex items-center justify-center">
                    <i class="fas fa-inbox text-2xl text-slate-400"></i>
                </div>
                <p class="font-bold text-slate-900 mb-1">لا توجد طلبات حالياً</p>
                <p class="text-sm text-slate-500">ستظهر هنا المحادثات الواردة من أرقام جديدة</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($conversations->hasPages()): ?>
        <div><?php echo e($conversations->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    setInterval(() => {
        if (document.hidden) return;
        fetch('<?php echo e(route('employee.sales.whatsapp.queue.count')); ?>', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('wa-queue-badge');
                if (!badge) return;
                const count = data.count || 0;
                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
            })
            .catch(() => {});
    }, 15000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/whatsapp/queue.blade.php ENDPATH**/ ?>