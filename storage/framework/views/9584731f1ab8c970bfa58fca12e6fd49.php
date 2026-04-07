

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> مركز المبيعات</a>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.leads.export', request()->query())); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-lg text-sm font-bold shadow-md hover:from-emerald-700 hover:to-teal-700 border border-emerald-500/30">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
            <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                <i class="fas fa-plus"></i> جديد
            </a>
        </div>
    </div>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">المرحلة</label>
            <select name="stage" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('stage') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">الأولوية</label>
            <select name="priority" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('priority') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">المتابعة</label>
            <select name="follow_up" class="border rounded-lg px-3 py-2 text-sm min-w-[140px]">
                <option value="">الكل</option>
                <option value="overdue" <?php if(request('follow_up') === 'overdue'): echo 'selected'; endif; ?>>متأخرة</option>
                <option value="today" <?php if(request('follow_up') === 'today'): echo 'selected'; endif; ?>>اليوم</option>
                <option value="week" <?php if(request('follow_up') === 'week'): echo 'selected'; endif; ?>>خلال أسبوع</option>
                <option value="none" <?php if(request('follow_up') === 'none'): echo 'selected'; endif; ?>>بدون موعد</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">ترتيب</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="" <?php if(!request('sort')): echo 'selected'; endif; ?>>آخر تحديث</option>
                <option value="priority" <?php if(request('sort') === 'priority'): echo 'selected'; endif; ?>>الأولوية (عاجل أولاً)</option>
                <option value="follow_up" <?php if(request('sort') === 'follow_up'): echo 'selected'; endif; ?>>أقرب متابعة</option>
                <option value="last_contact" <?php if(request('sort') === 'last_contact'): echo 'selected'; endif; ?>>آخر تواصل</option>
                <option value="value" <?php if(request('sort') === 'value'): echo 'selected'; endif; ?>>أعلى قيمة متوقعة</option>
            </select>
        </div>
        <div class="flex items-center gap-2 pb-2">
            <input type="checkbox" name="stale" value="1" id="stale" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" <?php if(request()->boolean('stale')): echo 'checked'; endif; ?>>
            <label for="stale" class="text-sm text-gray-700">بلا تواصل <?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم</label>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، هاتف، بريد..." class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">تصفية</button>
        <?php if(request()->hasAny(['stage','priority','follow_up','sort','stale','search'])): ?>
            <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">إعادة ضبط</a>
        <?php endif; ?>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto shadow-sm">
        <table class="w-full min-w-[880px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">التواصل</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">المرحلة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">الأولوية</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">متابعة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">آخر تواصل</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $row = 'hover:bg-gray-50/80';
                    if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                        $row .= ' bg-rose-50/60';
                    } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                        $row .= ' bg-amber-50/50';
                    }
                ?>
                <tr class="<?php echo e($row); ?>">
                    <td class="py-3 px-4 font-medium text-gray-900"><?php echo e($lead->name); ?></td>
                    <td class="py-3 px-4 text-gray-600"><?php echo e($lead->phone ?? '—'); ?> <?php if($lead->email): ?><br><span class="text-xs"><?php echo e($lead->email); ?></span><?php endif; ?></td>
                    <td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-medium"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span></td>
                    <td class="py-3 px-4">
                        <?php $pr = $lead->priority ?? 'normal'; ?>
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold
                            <?php if($pr === 'urgent'): ?> bg-rose-100 text-rose-800
                            <?php elseif($pr === 'high'): ?> bg-orange-100 text-orange-800
                            <?php elseif($pr === 'low'): ?> bg-slate-100 text-slate-700
                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>"><?php echo e(\App\Models\SalesLead::priorityLabel($pr)); ?></span>
                    </td>
                    <td class="py-3 px-4 text-xs <?php if($lead->isFollowUpOverdue()): ?> text-rose-600 font-semibold <?php else: ?> text-gray-600 <?php endif; ?>"><?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="py-3 px-4 text-gray-600 text-xs"><?php echo e($lead->last_contacted_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="py-3 px-4">
                        <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="text-emerald-600 font-medium hover:underline">عرض</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="py-12 text-center text-gray-500">لا توجد سجلات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-2"><?php echo e($leads->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/leads/index.blade.php ENDPATH**/ ?>