

<?php $__env->startSection('title', 'متابعاتي'); ?>
<?php $__env->startSection('header', 'متابعاتي — Next Follow'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $filterLabels = [
        'overdue' => 'متأخرة',
        'today' => 'اليوم',
        'week' => 'خلال 7 أيام',
        'none' => 'بدون موعد',
        'stale' => 'بلا تواصل',
        'all' => 'الكل',
    ];
    $redirectTo = request()->fullUrl();
?>
<div class="space-y-5">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-emerald-50/40 px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-teal-600"></i> متابعاتي
                </h1>
                <p class="text-xs text-slate-600 mt-1">جدول مواعيد Next Follow — المتأخرة واليوم والقادمة، مع من لم يُتواصل معهم.</p>
            </div>
            <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="text-xs font-bold text-teal-700 hover:underline">كل العملاء ←</a>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <?php $__currentLoopData = $filterLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('employee.sales.follow-ups.index', array_filter(['filter' => $key, 'search' => request('search')]))); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors
               <?php echo e($filter === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'); ?>">
                <?php echo e($label); ?>

                <span class="tabular-nums opacity-80"><?php echo e($counts[$key] ?? 0); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="GET" class="flex gap-2">
        <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
        <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو الهاتف..."
               class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">بحث</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold hidden sm:table-cell">الهاتف</th>
                    <th class="px-4 py-3 text-right font-semibold hidden md:table-cell">المرحلة</th>
                    <th class="px-4 py-3 text-right font-semibold">المتابعة</th>
                    <th class="px-4 py-3 text-right font-semibold hidden lg:table-cell">آخر تواصل</th>
                    <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $status = '—';
                        $statusClass = 'text-slate-500';
                        if ($lead->isFollowUpOverdue()) {
                            $status = 'متأخر';
                            $statusClass = 'text-rose-700 bg-rose-50';
                        } elseif ($lead->next_follow_up_at && $lead->next_follow_up_at->isToday()) {
                            $status = 'اليوم';
                            $statusClass = 'text-amber-800 bg-amber-50';
                        } elseif ($lead->next_follow_up_at && $lead->next_follow_up_at->isFuture()) {
                            $status = 'قادم';
                            $statusClass = 'text-teal-800 bg-teal-50';
                        } elseif (! $lead->next_follow_up_at) {
                            $status = 'بدون موعد';
                            $statusClass = 'text-slate-600 bg-slate-100';
                        }
                        if ($lead->isStaleContact()) {
                            $status = $status === '—' ? 'بلا تواصل' : $status.' · بلا تواصل';
                        }
                    ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="font-bold text-slate-900 hover:text-teal-700"><?php echo e($lead->name); ?></a>
                            <?php if($lead->category): ?>
                                <span class="block text-[11px] text-slate-500"><?php echo e($lead->category->name); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell dir-ltr text-slate-600"><?php echo e($lead->phone ?? '—'); ?></td>
                        <td class="px-4 py-3 hidden md:table-cell text-slate-600"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap <?php if($lead->isFollowUpOverdue()): ?> text-rose-700 font-bold <?php else: ?> text-slate-700 <?php endif; ?>">
                            <?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500 whitespace-nowrap">
                            <?php echo e($lead->last_contacted_at?->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($statusClass); ?>"><?php echo e($status); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1 justify-end">
                                <button type="button"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100"
                                        title="تحديد Next Follow"
                                        onclick="openNextFollowModal(<?php echo e($lead->id); ?>, <?php echo \Illuminate\Support\Js::from($lead->name)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))->toHtml() ?>)">
                                    <i class="fas fa-calendar-plus text-xs"></i>
                                </button>
                                <form method="post" action="<?php echo e(route('employee.sales.leads.quick-activity', $lead)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="call">
                                    <input type="hidden" name="redirect_to" value="<?php echo e($redirectTo); ?>">
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="سجّل مكالمة">
                                        <i class="fas fa-phone text-xs"></i>
                                    </button>
                                </form>
                                <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="فتح">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                            لا توجد متابعات في هذا الفلتر.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($leads->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </div>
</div>

<div id="next-follow-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">تحديد Next Follow</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="nf-lead-name"></p>
            </div>
            <button type="button" onclick="closeNextFollowModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fas fa-times"></i></button>
        </div>
        <form method="post" id="nf-form" class="p-5 space-y-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="redirect_to" value="<?php echo e($redirectTo); ?>">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">موعد المتابعة</label>
                <input type="datetime-local" name="next_follow_up_at" id="nf-datetime" required
                       min="<?php echo e(now()->addMinute()->format('Y-m-d\TH:i')); ?>"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظة (اختياري)</label>
                <input type="text" name="note" maxlength="500" placeholder="مثال: متابعة عرض السعر"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div class="flex gap-2 justify-end pt-1">
                <button type="button" onclick="closeNextFollowModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">حفظ الموعد</button>
            </div>
        </form>
    </div>
</div>
<script>
function openNextFollowModal(leadId, name, datetime) {
    var modal = document.getElementById('next-follow-modal');
    document.getElementById('nf-form').action = <?php echo json_encode(url('/employee/sales/leads'), 15, 512) ?> + '/' + leadId + '/next-follow';
    document.getElementById('nf-lead-name').textContent = name || '';
    document.getElementById('nf-datetime').value = datetime || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeNextFollowModal() {
    var modal = document.getElementById('next-follow-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('next-follow-modal').addEventListener('click', function (e) {
    if (e.target === this) closeNextFollowModal();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\follow-ups\index.blade.php ENDPATH**/ ?>