

<?php $__env->startSection('title', 'تقارير أعضاء الفريق'); ?>
<?php $__env->startSection('header', 'تقارير أعضاء الفريق'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 max-w-md">
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-slate-500">مُسلَّمة</p><p class="text-2xl font-bold"><?php echo e($stats['submitted']); ?></p></div>
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-slate-500">بانتظار مراجعتك</p><p class="text-2xl font-bold text-amber-700"><?php echo e($stats['pending_review']); ?></p></div>
    </div>
    <form method="GET" class="flex flex-wrap gap-3 bg-white rounded-xl border p-4">
        <input type="date" name="from" value="<?php echo e($from->format('Y-m-d')); ?>" class="px-3 py-2 border rounded-lg text-sm">
        <input type="date" name="to" value="<?php echo e($to->format('Y-m-d')); ?>" class="px-3 py-2 border rounded-lg text-sm">
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">كل الحالات</option>
            <option value="submitted" <?php if(request('status')==='submitted'): echo 'selected'; endif; ?>>مُسلَّم</option>
            <option value="draft" <?php if(request('status')==='draft'): echo 'selected'; endif; ?>>مسودة</option>
        </select>
        <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
    </form>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">الموظف</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">مراجع</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium"><?php echo e($r->user->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->report_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->status === 'submitted' ? 'مُسلَّم' : 'مسودة'); ?></td>
                        <td class="px-4 py-3"><?php echo e($r->manager_reviewed_at ? 'نعم' : '—'); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('employee.sales-manager.daily-reports.show', $r)); ?>" class="text-emerald-700 font-semibold">عرض</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد تقارير.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($reports->hasPages()): ?><div class="px-4 py-3"><?php echo e($reports->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\daily-reports\index.blade.php ENDPATH**/ ?>