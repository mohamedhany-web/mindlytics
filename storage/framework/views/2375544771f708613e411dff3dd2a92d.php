

<?php $__env->startSection('title', 'طلبات واتساب'); ?>
<?php $__env->startSection('header', 'توزيع طلبات واتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-4">
    <div class="rounded-xl border border-emerald-200 bg-gradient-to-l from-emerald-50 via-white to-teal-50/40 px-4 py-3 sm:px-5 sm:py-3.5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600"></i>
                    طابور الطلبات (للتوزيع)
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-0.5 leading-relaxed">
                    يظهر فقط من <strong>ردّ على الواتساب</strong> ولم يُسند بعد — مستلمو دفعات الإرسال بدون رد لا يظهرون هنا. وزّع كل طلب على موظف من فريقك.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <div class="text-center rounded-lg bg-white/80 border border-emerald-100 px-3 py-1.5">
                    <p class="text-xl sm:text-2xl font-black text-emerald-700 tabular-nums leading-none" id="wa-queue-total"><?php echo e($conversations->total()); ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">في الانتظار</p>
                </div>
                <a href="<?php echo e($inboxUrl); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-inbox text-emerald-600"></i>
                    محادثات الفريق
                </a>
            </div>
        </div>
    </div>

    <?php if(! $queueEnabled): ?>
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            طابور الانتظار غير مفعّل حالياً. راجع إعدادات الواتساب (<code class="text-xs">WHATSAPP_ASSIGNMENT_STRATEGY=manual_queue</code>).
        </div>
    <?php endif; ?>

    <?php if(! $hasTeam): ?>
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            لا يوجد فريق مسند لك كمدير مبيعات — لن تتمكن من توزيع الطلبات حتى يُنشأ فريق ويربط بأعضاء.
        </div>
    <?php elseif($teamMembers->isEmpty()): ?>
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            فريقك لا يحتوي على موظفي مبيعات حالياً. أضف أعضاء للفريق لتتمكن من التوزيع.
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5" id="wa-queue-list">
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm hover:border-emerald-300 hover:shadow transition-all flex flex-col gap-2 min-h-0">
                <div class="flex items-start gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <h3 class="font-bold text-sm text-slate-900 truncate" title="<?php echo e($conversation->displayName()); ?>">
                                <?php echo e($conversation->displayName()); ?>

                            </h3>
                            <?php if($conversation->unread_count > 0): ?>
                                <span class="shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold tabular-nums">
                                    <?php echo e($conversation->unread_count); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-500 tabular-nums dir-ltr truncate mt-0.5"><?php echo e($conversation->formattedPhone()); ?></p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed min-h-[2.25rem]" title="<?php echo e($conversation->last_message_preview ?: ''); ?>">
                    <?php echo e($conversation->last_message_preview ?: '—'); ?>

                </p>

                <div class="mt-auto pt-1 border-t border-slate-100 space-y-2">
                    <p class="text-[10px] text-slate-400 truncate">
                        <?php if($conversation->last_message_at): ?>
                            <?php echo e($conversation->last_message_at->diffForHumans()); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </p>

                    <?php if($hasTeam && $teamMembers->isNotEmpty()): ?>
                        <form method="POST" action="<?php echo e(route('employee.sales-manager.whatsapp.queue.assign', $conversation)); ?>" class="flex flex-col gap-1.5">
                            <?php echo csrf_field(); ?>
                            <select name="assigned_to" required
                                    class="w-full text-[11px] rounded-md border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                                <option value="">اختر موظف المبيعات…</option>
                                <?php $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(! $membership->user) continue; ?>
                                    <option value="<?php echo e($membership->user->id); ?>"><?php echo e($membership->user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-1 w-full px-2.5 py-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition-colors">
                                <i class="fas fa-user-check text-[10px]"></i>
                                توزيع
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-[11px] text-rose-600 font-semibold">تعذّر التوزيع — راجع فريقك</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full rounded-xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-50 flex items-center justify-center">
                    <i class="fas fa-inbox text-xl text-slate-400"></i>
                </div>
                <p class="font-bold text-slate-900 mb-1">لا توجد طلبات حالياً</p>
                <p class="text-sm text-slate-500">ستظهر هنا فقط المحادثات التي ردّ أصحابها ولم تُوزَّع بعد</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($conversations->hasPages()): ?>
        <div class="pt-1 flex justify-center"><?php echo e($conversations->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    setInterval(() => {
        if (document.hidden) return;
        fetch('<?php echo e(route('employee.sales-manager.whatsapp.queue.count')); ?>', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('wa-queue-badge');
                const total = document.getElementById('wa-queue-total');
                const count = data.count || 0;
                if (badge) {
                    badge.textContent = count;
                    badge.classList.toggle('hidden', count === 0);
                }
                if (total) total.textContent = count;
            })
            .catch(() => {});
    }, 15000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp\queue.blade.php ENDPATH**/ ?>