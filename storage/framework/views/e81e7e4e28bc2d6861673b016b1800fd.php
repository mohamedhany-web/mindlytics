

<?php $__env->startSection('title', 'تقارير فرق المبيعات'); ?>
<?php $__env->startSection('header', 'تقارير فرق المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-5">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option value="submitted" <?php if(request('status')==='submitted'): echo 'selected'; endif; ?>>مُرسَل</option>
                <option value="draft" <?php if(request('status')==='draft'): echo 'selected'; endif; ?>>مسودة</option>
            </select>
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="px-3 py-2 border rounded-lg text-sm">
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="px-3 py-2 border rounded-lg text-sm">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm">تصفية</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">الفريق</th>
                <th class="px-4 py-3 text-right">المدير</th>
                <th class="px-4 py-3 text-right">تقارير مستلمة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($r->report_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->team->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->manager->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->reports_received); ?>/<?php echo e($r->team_members_count); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->isSubmitted() ? 'مُرسَل' : 'مسودة'); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.sales.team-daily-reports.show', $r)); ?>" class="text-emerald-700 font-semibold">عرض</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">لا توجد تقارير.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($reports->hasPages()): ?><div class="px-4 py-3"><?php echo e($reports->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\team-daily-reports\index.blade.php ENDPATH**/ ?>