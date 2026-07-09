

<?php $__env->startSection('title', 'حضور '.$employee->name); ?>
<?php $__env->startSection('header', 'حضور '.$employee->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'أيام مكتملة', 'value' => $summary['completed_days']],
            ['label' => 'تأخير', 'value' => $summary['late_days']],
            ['label' => 'إجمالي ساعات', 'value' => $summary['total_hours']],
            ['label' => 'أيام نشطة', 'value' => $summary['active_days']],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500"><?php echo e($s['label']); ?></p>
                <p class="text-2xl font-bold"><?php echo e($s['value']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p><a href="<?php echo e(route('employee.sales-manager.attendance.index')); ?>" class="text-sm text-emerald-700 font-semibold">← العودة لحضور الفريق</a></p>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">دخول</th>
                <th class="px-4 py-3 text-right">خروج</th>
                <th class="px-4 py-3 text-right">الحالة</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($rec->work_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_in_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_out_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e(\App\Models\EmployeeAttendanceRecord::statusLabels()[$rec->status] ?? $rec->status); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php if($records->hasPages()): ?><div class="px-4 py-3"><?php echo e($records->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\attendance\employee.blade.php ENDPATH**/ ?>