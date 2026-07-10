

<?php $__env->startSection('title', 'عملاء الفريق'); ?>
<?php $__env->startSection('header', 'عملاء الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $req = request();
    $activePreset = 'all';
    if ($req->boolean('stale')) {
        $activePreset = 'stale';
    } elseif ($req->get('follow_up') === 'today') {
        $activePreset = 'today';
    } elseif ($req->get('follow_up') === 'overdue') {
        $activePreset = 'overdue';
    } elseif ($req->get('stage') === 'new') {
        $activePreset = 'new';
    }
?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">عملاء فريق <?php echo e($team->name); ?></h1>
                <p class="text-sm text-slate-500 mt-1">عرض وتحويل Leads بين أعضاء الفريق</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('employee.sales-manager.follow-ups.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold"><i class="fas fa-clipboard-list"></i> رقابة المتابعات</a>
                <a href="<?php echo e(route('employee.sales-manager.transfer.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold"><i class="fas fa-exchange-alt"></i> تحويل جماعي</a>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales-manager.leads.index')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e($activePreset === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200'); ?>">الكل</a>
            <a href="<?php echo e(route('employee.sales-manager.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up'])); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e($activePreset === 'overdue' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-600 border-slate-200'); ?>">
                متأخرة <span class="tabular-nums opacity-80"><?php echo e($quickCounts['overdue'] ?? 0); ?></span>
            </a>
            <a href="<?php echo e(route('employee.sales-manager.leads.index', ['follow_up' => 'today', 'sort' => 'follow_up'])); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e($activePreset === 'today' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-600 border-slate-200'); ?>">
                اليوم <span class="tabular-nums opacity-80"><?php echo e($quickCounts['today'] ?? 0); ?></span>
            </a>
            <a href="<?php echo e(route('employee.sales-manager.leads.index', ['stale' => 1])); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e($activePreset === 'stale' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-slate-600 border-slate-200'); ?>">
                بلا تواصل <span class="tabular-nums opacity-80"><?php echo e($quickCounts['stale'] ?? 0); ?></span>
            </a>
            <a href="<?php echo e(route('employee.sales-manager.leads.index', ['stage' => 'new'])); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e($activePreset === 'new' ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-slate-600 border-slate-200'); ?>">
                جديد <span class="tabular-nums opacity-80"><?php echo e($quickCounts['new'] ?? 0); ?></span>
            </a>
        </div>

        <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
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
            <select name="follow_up" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل المتابعات</option>
                <option value="overdue" <?php if(request('follow_up') === 'overdue'): echo 'selected'; endif; ?>>متأخرة</option>
                <option value="today" <?php if(request('follow_up') === 'today'): echo 'selected'; endif; ?>>اليوم</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold">مسند إلى</th>
                    <th class="px-4 py-3 text-right font-semibold">المرحلة</th>
                    <th class="px-4 py-3 text-right font-semibold">متابعة</th>
                    <th class="px-4 py-3 text-right font-semibold hidden md:table-cell">آخر تواصل</th>
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 <?php echo e($lead->isFollowUpOverdue() ? 'bg-rose-50/40' : ''); ?>">
                        <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($lead->name); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($lead->assignee->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e(\App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage); ?></td>
                        <td class="px-4 py-3 <?php if($lead->isFollowUpOverdue()): ?> text-rose-700 font-bold <?php else: ?> text-slate-500 <?php endif; ?>">
                            <?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-500">
                            <?php echo e($lead->last_contacted_at?->format('Y-m-d') ?? '—'); ?>

                            <?php if($lead->isStaleContact()): ?>
                                <span class="block text-[10px] text-orange-700 font-bold">بلا تواصل</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('employee.sales-manager.leads.show', $lead)); ?>" class="text-emerald-700 font-semibold hover:underline">عرض</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد نتائج.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($leads->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\leads\index.blade.php ENDPATH**/ ?>