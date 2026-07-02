<?php $__env->startSection('title', 'كود '.$workshopPromoCode->code); ?>
<?php $__env->startSection('header', 'تفاصيل كود الورشة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'promo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-violet-600 uppercase tracking-wide mb-1">كود الورشة</p>
                <h1 class="text-3xl font-black font-mono text-slate-900"><?php echo e($workshopPromoCode->code); ?></h1>
                <p class="text-slate-600 mt-1"><?php echo e($workshopPromoCode->title); ?></p>
                <?php if($workshopPromoCode->workshop): ?>
                    <p class="text-sm text-sky-700 mt-2"><i class="fas fa-chalkboard-teacher ml-1"></i> <?php echo e($workshopPromoCode->workshop->title); ?></p>
                <?php endif; ?>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.workshop-promo-codes.edit', $workshopPromoCode)); ?>" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-bold">تعديل</a>
                <a href="<?php echo e(route('admin.workshop-promo-codes.index')); ?>" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">رجوع</a>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-slate-50/50">
            <div><p class="text-xs text-slate-500">الخصم</p><p class="text-lg font-black"><?php echo e($workshopPromoCode->discountLabel()); ?></p></div>
            <div><p class="text-xs text-slate-500">ينتهي</p><p class="text-lg font-bold"><?php echo e($workshopPromoCode->expiryLabel()); ?></p></div>
            <div><p class="text-xs text-slate-500">التفعيلات</p><p class="text-lg font-bold"><?php echo e($stats['activations']); ?> <?php if($workshopPromoCode->max_activations): ?>/ <?php echo e($workshopPromoCode->max_activations); ?><?php endif; ?></p></div>
            <div><p class="text-xs text-slate-500">استُخدم</p><p class="text-lg font-bold text-emerald-700"><?php echo e($stats['used']); ?></p></div>
        </div>
    </section>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-black text-slate-900"><i class="fas fa-user-check text-violet-600 ml-2"></i>من فعّل الكود</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">تاريخ التفعيل</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">كوبون مرتبط</th>
                        <th class="px-4 py-3 text-right">مسند إلى</th>
                        <th class="px-4 py-3 text-right">متابعة</th>
                        <th class="px-4 py-3 text-right">المبيعات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $workshopPromoCode->activations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold"><?php echo e($act->user->name ?? '—'); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($act->user->email ?? ''); ?></div>
                            </td>
                            <td class="px-4 py-3"><?php echo e($act->activated_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <?php if($act->status === 'active'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مفعّل</span>
                                <?php elseif($act->status === 'used'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-700">استُخدم</span>
                                <?php elseif($act->status === 'expired'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">منتهي</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">ملغي</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs"><?php echo e($act->coupon?->code ?? '—'); ?></td>
                            <?php echo $__env->make('admin.workshop-promo-codes._activation_sales_cells', ['act' => $act], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">لم يفعّل أحد هذا الكود بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshop-promo-codes\show.blade.php ENDPATH**/ ?>