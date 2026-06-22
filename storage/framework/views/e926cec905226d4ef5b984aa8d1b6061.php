

<?php $__env->startSection('title', 'مراجعة مخالصة'); ?>
<?php $__env->startSection('header', 'مخالصة ' . $placeSettlement->period_month . ' — ' . ($placeSettlement->location?->name ?? '')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6 max-w-5xl">
    <?php if(session('success')): ?><div class="rounded-lg bg-emerald-50 border p-3"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="rounded-lg bg-rose-50 border p-3"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <div class="bg-white rounded-xl border p-6 grid md:grid-cols-3 gap-4">
        <div><span class="text-slate-500 text-sm">رقم المخالصة</span><p class="font-bold"><?php echo e($placeSettlement->settlement_number); ?></p></div>
        <div><span class="text-slate-500 text-sm">الساعات</span><p class="font-bold"><?php echo e(number_format((float) $placeSettlement->total_hours, 2)); ?></p></div>
        <div><span class="text-slate-500 text-sm">المبلغ</span><p class="font-bold text-lg"><?php echo e(number_format((float) $placeSettlement->total_amount, 2)); ?> <?php echo e($placeSettlement->currency); ?></p></div>
        <div><span class="text-slate-500 text-sm">سعر الساعة</span><p><?php echo e(number_format((float) $placeSettlement->hourly_rate, 2)); ?></p></div>
        <div><span class="text-slate-500 text-sm">الحالة</span><p class="font-semibold"><?php echo e($placeSettlement->status_label); ?></p></div>
        <?php if($placeSettlement->expense): ?>
            <div><span class="text-slate-500 text-sm">المصروف</span>
                <p><a href="<?php echo e(route('admin.expenses.show', $placeSettlement->expense)); ?>" class="text-blue-600"><?php echo e($placeSettlement->expense->expense_number); ?></a></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-2 text-right">التاريخ</th><th class="px-4 py-2 text-right">ساعات</th><th class="px-4 py-2 text-right">الحالة</th></tr></thead>
            <tbody class="divide-y">
                <?php $__currentLoopData = $placeSettlement->usageLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                        <td class="px-4 py-2"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                        <td class="px-4 py-2"><?php echo e($log->status_label); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($placeSettlement->status === 'submitted'): ?>
        <form action="<?php echo e(route('admin.place-settlements.approve', $placeSettlement)); ?>" method="POST" class="bg-white rounded-xl border p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <h3 class="font-bold">اعتماد المخالصة وإنشاء مصروف + فاتورة</h3>
            <div>
                <label class="block text-sm text-slate-600 mb-1">المحفظة للخصم</label>
                <select name="wallet_id" class="w-full max-w-md rounded-lg border-slate-300">
                    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($w->id); ?>" <?php if($placeSettlement->wallet_id == $w->id || $placeSettlement->location?->default_wallet_id == $w->id): echo 'selected'; endif; ?>>
                            <?php echo e($w->name); ?> (<?php echo e(number_format((float) $w->balance, 2)); ?> ج.م)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold" onclick="return confirm('اعتماد المخالصة؟');">اعتماد</button>
        </form>
    <?php endif; ?>

    <?php if(in_array($placeSettlement->status, ['approved', 'paid'], true) && $placeSettlement->status !== 'closed'): ?>
        <form action="<?php echo e(route('admin.place-settlements.close', $placeSettlement)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg font-semibold" onclick="return confirm('إقفال الشهر؟ لن يُسمح بمزيد من التسجيل.');">إقفال الشهر</button>
        </form>
    <?php endif; ?>

    <?php if($placeSettlement->expense && $placeSettlement->expense->status === 'pending'): ?>
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm">
            المصروف بانتظار الموافقة من <a href="<?php echo e(route('admin.expenses.show', $placeSettlement->expense)); ?>" class="text-blue-600 font-medium">صفحة المصروفات</a> لخصم المبلغ من المحفظة.
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\place-settlements\show.blade.php ENDPATH**/ ?>