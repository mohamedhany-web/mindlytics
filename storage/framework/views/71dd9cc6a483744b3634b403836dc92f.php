

<?php $__env->startSection('title', 'حضور الفريق'); ?>
<?php $__env->startSection('header', 'حضور وغياب الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels();
?>
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'سجلات', 'value' => $stats['total']],
            ['label' => 'مكتمل', 'value' => $stats['completed']],
            ['label' => 'متأخر', 'value' => $stats['late']],
            ['label' => 'جاري العمل', 'value' => $stats['active_now']],
            ['label' => 'غياب', 'value' => $stats['absent']],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500"><?php echo e($s['label']); ?></p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($s['value']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="employee_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php if(request('employee_id') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">دخول</th>
                    <th class="px-4 py-3 text-right">خروج</th>
                    <th class="px-4 py-3 text-right">ساعات</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium"><?php echo e($rec->user->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->work_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_in_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_out_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->worked_minutes ? round($rec->worked_minutes / 60, 1) : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($statusLabels[$rec->status] ?? $rec->status); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد سجلات.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($records->hasPages()): ?><div class="px-4 py-3 border-t"><?php echo e($records->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\attendance\index.blade.php ENDPATH**/ ?>