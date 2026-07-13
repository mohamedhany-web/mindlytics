

<?php $__env->startSection('title', 'رقابة المتابعات'); ?>
<?php $__env->startSection('header', 'رقابة متابعات الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $filterLabels = [
        'overdue' => 'متأخرة',
        'today' => 'اليوم',
        'week' => 'خلال 7 أيام',
        'none' => 'بدون موعد',
        'stale' => 'بلا تواصل',
        'all' => 'كل المفتوحة',
    ];
?>
<div class="space-y-5">
    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-sky-50/50 px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-teal-600"></i>
                    رقابة المتابعات — <?php echo e($team->name); ?>

                </h1>
                <p class="text-xs text-slate-600 mt-1">متابعة الفريق: المتأخرون، مواعيد اليوم، ومن لم يُتواصل معهم.</p>
            </div>
            <a href="<?php echo e(route('employee.sales-manager.leads.index')); ?>" class="text-xs font-bold text-teal-700 hover:underline">عملاء الفريق ←</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <?php $__currentLoopData = [
            ['key' => 'overdue', 'label' => 'متأخرة', 'color' => 'text-rose-700'],
            ['key' => 'today', 'label' => 'اليوم', 'color' => 'text-amber-700'],
            ['key' => 'week', 'label' => 'خلال أسبوع', 'color' => 'text-teal-700'],
            ['key' => 'stale', 'label' => 'بلا تواصل', 'color' => 'text-orange-700'],
            ['key' => 'none', 'label' => 'بدون موعد', 'color' => 'text-slate-700'],
            ['key' => 'all', 'label' => 'مفتوحة', 'color' => 'text-slate-900'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('employee.sales-manager.follow-ups.index', array_filter(['filter' => $card['key'], 'assignee' => request('assignee'), 'stage' => request('stage'), 'search' => request('search')]))); ?>"
               class="rounded-xl border bg-white p-4 hover:border-teal-300 transition-colors <?php echo e($filter === $card['key'] ? 'border-teal-400 ring-1 ring-teal-100' : 'border-slate-200'); ?>">
                <p class="text-[11px] text-slate-500 font-semibold"><?php echo e($card['label']); ?></p>
                <p class="text-2xl font-black tabular-nums <?php echo e($card['color']); ?>"><?php echo e($counts[$card['key']] ?? 0); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($members->isNotEmpty()): ?>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">ملخص حسب الموظف</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-2 text-right">الموظف</th>
                            <th class="px-4 py-2 text-right">متأخر</th>
                            <th class="px-4 py-2 text-right">اليوم</th>
                            <th class="px-4 py-2 text-right">بلا تواصل</th>
                            <th class="px-4 py-2 text-right">بدون موعد</th>
                            <th class="px-4 py-2 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $row = $byMember->get($m->user_id); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-semibold text-slate-900"><?php echo e($m->user->name ?? '—'); ?></td>
                                <td class="px-4 py-2.5 tabular-nums <?php echo e((int) ($row->overdue_count ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-slate-600'); ?>"><?php echo e((int) ($row->overdue_count ?? 0)); ?></td>
                                <td class="px-4 py-2.5 tabular-nums text-slate-700"><?php echo e((int) ($row->today_count ?? 0)); ?></td>
                                <td class="px-4 py-2.5 tabular-nums <?php echo e((int) ($row->stale_count ?? 0) > 0 ? 'text-orange-700 font-bold' : 'text-slate-600'); ?>"><?php echo e((int) ($row->stale_count ?? 0)); ?></td>
                                <td class="px-4 py-2.5 tabular-nums text-slate-700"><?php echo e((int) ($row->none_count ?? 0)); ?></td>
                                <td class="px-4 py-2.5">
                                    <a href="<?php echo e(route('employee.sales-manager.follow-ups.index', ['filter' => 'overdue', 'assignee' => $m->user_id])); ?>"
                                       class="text-xs font-bold text-teal-700 hover:underline">عرض</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $filterLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('employee.sales-manager.follow-ups.index', array_filter(['filter' => $key, 'assignee' => request('assignee'), 'stage' => request('stage'), 'search' => request('search')]))); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border
                   <?php echo e($filter === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200'); ?>">
                    <?php echo e($label); ?>

                    <span class="tabular-nums opacity-80"><?php echo e($counts[$key] ?? 0); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..."
                   class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <select name="assignee" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->user_id); ?>" <?php if(request('assignee') == $m->user_id): echo 'selected'; endif; ?>><?php echo e($m->user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="stage" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل المراحل</option>
                <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('stage') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold">الموظف</th>
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
                        $daysSinceContact = null;
                        if ($lead->last_contacted_at) {
                            $daysSinceContact = (int) $lead->last_contacted_at->diffInDays(now());
                        } elseif ($lead->created_at) {
                            $daysSinceContact = (int) $lead->created_at->diffInDays(now());
                        }
                        $badges = [];
                        if ($lead->isFollowUpOverdue()) $badges[] = ['متأخر', 'bg-rose-50 text-rose-700'];
                        elseif ($lead->next_follow_up_at?->isToday()) $badges[] = ['اليوم', 'bg-amber-50 text-amber-800'];
                        elseif ($lead->next_follow_up_at?->isFuture()) $badges[] = ['قادم', 'bg-teal-50 text-teal-800'];
                        elseif (! $lead->next_follow_up_at) $badges[] = ['بدون موعد', 'bg-slate-100 text-slate-600'];
                        if ($lead->isStaleContact()) $badges[] = ['بلا تواصل', 'bg-orange-50 text-orange-800'];
                    ?>
                    <tr class="hover:bg-slate-50 <?php echo e($lead->isFollowUpOverdue() ? 'bg-rose-50/30' : ''); ?>">
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900"><?php echo e($lead->name); ?></p>
                            <p class="text-[11px] text-slate-500 dir-ltr"><?php echo e($lead->phone ?? ''); ?></p>
                        </td>
                        <td class="px-4 py-3 text-slate-700"><?php echo e($lead->assignee->name ?? '—'); ?></td>
                        <td class="px-4 py-3 hidden md:table-cell text-slate-600"><?php echo e(\App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap <?php if($lead->isFollowUpOverdue()): ?> text-rose-700 font-bold <?php else: ?> text-slate-700 <?php endif; ?>">
                            <?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500">
                            <?php echo e($lead->last_contacted_at?->format('Y-m-d') ?? '—'); ?>

                            <?php if($daysSinceContact !== null): ?>
                                <span class="block text-[10px]">منذ <?php echo e($daysSinceContact); ?> يوم</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $cls]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($cls); ?>"><?php echo e($label); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('employee.sales-manager.leads.show', $lead)); ?>" class="text-xs font-bold text-emerald-700 hover:underline">عرض</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">لا توجد نتائج لهذا الفلتر.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($leads->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\follow-ups\index.blade.php ENDPATH**/ ?>