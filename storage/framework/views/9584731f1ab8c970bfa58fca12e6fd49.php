

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> مركز المبيعات</a>
        <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
            <i class="fas fa-plus"></i> جديد
        </a>
    </div>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200">
        <div>
            <label class="block text-xs text-gray-500 mb-1">المرحلة</label>
            <select name="stage" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('stage') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، هاتف، بريد..." class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto shadow-sm">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">التواصل</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">المرحلة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">متابعة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80">
                    <td class="py-3 px-4 font-medium text-gray-900"><?php echo e($lead->name); ?></td>
                    <td class="py-3 px-4 text-gray-600"><?php echo e($lead->phone ?? '—'); ?> <?php if($lead->email): ?><br><span class="text-xs"><?php echo e($lead->email); ?></span><?php endif; ?></td>
                    <td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-medium"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span></td>
                    <td class="py-3 px-4 text-gray-600 text-xs"><?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="py-3 px-4">
                        <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="text-emerald-600 font-medium hover:underline">عرض</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="py-12 text-center text-gray-500">لا توجد سجلات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-2"><?php echo e($leads->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/leads/index.blade.php ENDPATH**/ ?>