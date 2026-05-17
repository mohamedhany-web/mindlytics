

<?php $__env->startSection('title', 'العملاء المحتملون — المبيعات'); ?>
<?php $__env->startSection('header', 'المبيعات — العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('admin.sales.audit-log.index')); ?>" class="text-sm text-emerald-700 font-medium hover:underline">سجل أنشطة المبيعات</a>
            <span class="text-gray-300">|</span>
            <a href="<?php echo e(route('admin.sales.transfer.index')); ?>" class="text-sm text-slate-700 font-bold hover:underline">تحويل بيانات موظف</a>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.sales.leads.export', request()->query())); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold shadow-lg hover:from-emerald-700 hover:to-teal-700 border border-emerald-400/40">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
            <a href="<?php echo e(route('admin.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg">
                <i class="fas fa-plus"></i> عميل جديد
            </a>
        </div>
    </div>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">موظف المبيعات</label>
            <select name="assigned_to" class="border rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="">الكل</option>
                <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($rep->id); ?>" <?php if(request('assigned_to') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
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
            <select name="follow_up" class="border rounded-lg px-3 py-2 text-sm min-w-[130px]">
                <option value="">الكل</option>
                <option value="overdue" <?php if(request('follow_up') === 'overdue'): echo 'selected'; endif; ?>>متأخرة</option>
                <option value="today" <?php if(request('follow_up') === 'today'): echo 'selected'; endif; ?>>اليوم</option>
                <option value="week" <?php if(request('follow_up') === 'week'): echo 'selected'; endif; ?>>خلال أسبوع</option>
                <option value="none" <?php if(request('follow_up') === 'none'): echo 'selected'; endif; ?>>بدون موعد</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">ترتيب</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm min-w-[150px]">
                <option value="" <?php if(!request('sort')): echo 'selected'; endif; ?>>آخر تحديث</option>
                <option value="priority" <?php if(request('sort') === 'priority'): echo 'selected'; endif; ?>>الأولوية</option>
                <option value="follow_up" <?php if(request('sort') === 'follow_up'): echo 'selected'; endif; ?>>متابعة</option>
                <option value="last_contact" <?php if(request('sort') === 'last_contact'): echo 'selected'; endif; ?>>آخر تواصل</option>
                <option value="value" <?php if(request('sort') === 'value'): echo 'selected'; endif; ?>>قيمة متوقعة</option>
            </select>
        </div>
        <div class="flex items-center gap-2 pb-2">
            <input type="checkbox" name="stale" value="1" id="stale_ad" class="rounded border-gray-300" <?php if(request()->boolean('stale')): echo 'checked'; endif; ?>>
            <label for="stale_ad" class="text-sm text-gray-700">بلا تواصل <?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم</label>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="اسم، هاتف، بريد...">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium">تصفية</button>
        <?php if(request()->hasAny(['assigned_to','stage','priority','follow_up','sort','stale','search'])): ?>
            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">إعادة ضبط</a>
        <?php endif; ?>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">مسند إلى</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">المرحلة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">أولوية</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">متابعة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">آخر تواصل</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">أنشئ</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $row = 'hover:bg-gray-50/80';
                    if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                        $row .= ' bg-rose-50/50';
                    } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                        $row .= ' bg-amber-50/40';
                    }
                    $pr = $lead->priority ?? 'normal';
                ?>
                <tr class="<?php echo e($row); ?>">
                    <td class="py-3 px-4 font-medium text-gray-900"><?php echo e($lead->name); ?></td>
                    <td class="py-3 px-4 text-gray-700"><?php echo e($lead->assignee->name ?? '—'); ?></td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                            <?php if($pr === 'urgent'): ?> bg-rose-100 text-rose-800
                            <?php elseif($pr === 'high'): ?> bg-orange-100 text-orange-800
                            <?php elseif($pr === 'low'): ?> bg-slate-100 text-slate-700
                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>"><?php echo e(\App\Models\SalesLead::priorityLabel($pr)); ?></span>
                    </td>
                    <td class="py-3 px-4 text-xs <?php if($lead->isFollowUpOverdue()): ?> text-rose-600 font-semibold <?php else: ?> text-gray-600 <?php endif; ?>"><?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="py-3 px-4 text-gray-600 text-xs"><?php echo e($lead->last_contacted_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="py-3 px-4 text-gray-500 text-xs"><?php echo e($lead->created_at->format('Y-m-d')); ?></td>
                    <td class="py-3 px-4">
                        <a href="<?php echo e(route('admin.sales.leads.show', $lead)); ?>" class="text-emerald-600 font-semibold hover:underline">عرض</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="py-12 text-center text-gray-500">لا توجد سجلات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-2"><?php echo e($leads->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/index.blade.php ENDPATH**/ ?>