<?php $__env->startSection('title', 'تفاصيل المخالصة'); ?>
<?php $__env->startSection('header', 'مخالصة ' . $settlement->period_month); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6 max-w-4xl">
    <?php if(session('success')): ?><div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-emerald-800"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-rose-800"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <div class="bg-white rounded-xl border p-6 grid md:grid-cols-2 gap-4">
        <div><span class="text-slate-500 text-sm">رقم المخالصة</span><p class="font-bold"><?php echo e($settlement->settlement_number); ?></p></div>
        <div><span class="text-slate-500 text-sm">الحالة</span><p class="font-bold"><?php echo e($settlement->status_label); ?></p></div>
        <div><span class="text-slate-500 text-sm">إجمالي الساعات المعتمدة</span><p class="font-bold text-violet-700"><?php echo e(number_format((float) $settlement->total_hours, 2)); ?></p></div>
        <div><span class="text-slate-500 text-sm">المبلغ</span><p class="font-bold"><?php echo e(number_format((float) $settlement->total_amount, 2)); ?> <?php echo e($settlement->currency); ?></p></div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <h2 class="px-4 py-3 font-bold border-b bg-slate-50">سجلات الشهر</h2>
        <table class="min-w-full text-sm">
            <thead><tr class="bg-slate-50"><th class="px-4 py-2 text-right">التاريخ</th><th class="px-4 py-2 text-right">ساعات</th><th class="px-4 py-2 text-right">الحالة</th></tr></thead>
            <tbody class="divide-y">
                <?php $__currentLoopData = $settlement->usageLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                        <td class="px-4 py-2"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                        <td class="px-4 py-2"><?php echo e($log->status_label); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($settlement->status === 'open'): ?>
        <form action="<?php echo e(route('place.office.settlements.submit', $settlement)); ?>" method="POST" onsubmit="return confirm('إرسال المخالصة للمراجعة؟');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white rounded-lg font-semibold">إرسال للمراجعة</button>
        </form>
    <?php endif; ?>

    <?php if($settlement->invoice): ?>
        <a href="<?php echo e(route('place.office.invoices.show', $settlement->invoice)); ?>" class="inline-block text-violet-600 font-medium">عرض فاتورة الدفع</a>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/settlements/show.blade.php ENDPATH**/ ?>