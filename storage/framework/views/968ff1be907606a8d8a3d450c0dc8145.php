

<?php $__env->startSection('title', 'سجل أنشطة المبيعات'); ?>
<?php $__env->startSection('header', 'سجل أنشطة المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <p class="text-sm text-gray-600 max-w-3xl">كل إجراءات موظفي المبيعات والإدارة على العملاء المحتملين تُسجَّل هنا (عرض، إنشاء، تعديل، حذف، نشاط، إعادة إسناد).</p>
    <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="text-sm text-emerald-700 font-medium hover:underline">← العملاء المحتملون</a>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">المستخدم</label>
            <select name="user_id" class="border rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="">الكل</option>
                <?php $__currentLoopData = $filterUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>" <?php if(request('user_id') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">نوع الحدث</label>
            <select name="action" class="border rounded-lg px-3 py-2 text-sm min-w-[200px]">
                <option value="">الكل</option>
                <?php $__currentLoopData = $actionLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if(request('action') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">بحث في الوصف / الرابط</label>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right py-3 px-3 font-semibold text-gray-700">الوقت</th>
                    <th class="text-right py-3 px-3 font-semibold text-gray-700">المستخدم</th>
                    <th class="text-right py-3 px-3 font-semibold text-gray-700">الحدث</th>
                    <th class="text-right py-3 px-3 font-semibold text-gray-700">الوصف</th>
                    <th class="text-right py-3 px-3 font-semibold text-gray-700">الرابط</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80 align-top">
                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                    <td class="py-2 px-3 text-gray-900"><?php echo e($log->user->name ?? '—'); ?></td>
                    <td class="py-2 px-3"><span class="text-xs font-semibold text-emerald-800"><?php echo e($actionLabels[$log->action] ?? $log->action); ?></span></td>
                    <td class="py-2 px-3 text-gray-700 max-w-md"><?php echo e(\Illuminate\Support\Str::limit($log->description, 200)); ?></td>
                    <td class="py-2 px-3 text-xs text-gray-500 max-w-[200px] truncate" title="<?php echo e($log->url); ?>"><?php echo e(\Illuminate\Support\Str::limit($log->url, 40)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="py-12 text-center text-gray-500">لا سجلات مطابقة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-2"><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/audit/index.blade.php ENDPATH**/ ?>