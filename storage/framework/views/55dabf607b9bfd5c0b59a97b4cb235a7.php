<?php $__env->startSection('title', 'مهام التسويق اليوم'); ?>
<?php $__env->startSection('header', 'مهام التسويق — اليوم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $contentTypes = \App\Services\MarketingPlanEventAutomationService::contentTypeLabels();
?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <section class="rounded-2xl bg-gradient-to-l from-pink-50 to-white border border-pink-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-gray-900">محتوى اليوم — خطة التسويق</h2>
                <p class="text-xs text-gray-600 mt-1">أكّد تنفيذ كل بند بعد الرفع. عدم التأكيد = غرامة تلقائية.</p>
            </div>
            <div class="flex gap-2 text-center">
                <div class="rounded-xl bg-white border px-4 py-2"><p class="text-[10px] text-gray-500">الإجمالي</p><p class="text-xl font-black"><?php echo e($stats['total']); ?></p></div>
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-2"><p class="text-[10px] text-amber-700">بانتظار التأكيد</p><p class="text-xl font-black text-amber-800"><?php echo e($stats['pending']); ?></p></div>
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2"><p class="text-[10px] text-emerald-700">مُؤكَّد</p><p class="text-xl font-black text-emerald-800"><?php echo e($stats['confirmed']); ?></p></div>
            </div>
        </div>
    </section>

    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-2xl border bg-white p-5 shadow-sm <?php echo e($ev->isConfirmed() ? 'border-emerald-200' : 'border-amber-200'); ?>">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-pink-100 text-pink-800"><?php echo e($contentTypes[$ev->content_type] ?? $ev->content_type); ?></span>
                            <?php if($ev->platform): ?>
                                <span class="text-xs font-semibold text-gray-600"><span class="inline-block w-2 h-2 rounded-full ml-1" style="background:<?php echo e($ev->platform->color_hex); ?>"></span><?php echo e($ev->platform->displayName()); ?></span>
                            <?php endif; ?>
                            <span class="text-xs text-gray-500"><?php echo e($ev->starts_at->format('H:i')); ?></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900"><?php echo e($ev->title); ?></h3>
                        <?php if($ev->body): ?><p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap"><?php echo e($ev->body); ?></p><?php endif; ?>
                        <p class="text-xs text-gray-500 mt-2">الخطة: <?php echo e($ev->plan->title ?? '—'); ?></p>
                        <?php if($ev->employeeTask): ?>
                            <a href="<?php echo e(route('employee.tasks.show', $ev->employeeTask)); ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 mt-2">
                                <i class="fas fa-tasks"></i> مهمة مرتبطة #<?php echo e($ev->employeeTask->id); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="shrink-0 text-left">
                        <?php if($ev->isConfirmed()): ?>
                            <span class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-100 text-emerald-800 text-sm font-bold">
                                <i class="fas fa-check-circle"></i> تم التأكيد
                            </span>
                            <p class="text-[10px] text-gray-500 mt-1"><?php echo e($ev->execution_confirmed_at?->format('Y-m-d H:i')); ?></p>
                        <?php elseif($ev->requires_confirmation): ?>
                            <form method="post" action="<?php echo e(route('employee.marketing-today.confirm', $ev)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-bold shadow-sm">
                                    <i class="fas fa-check"></i> تأكيد: تم التنفيذ / الرفع
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-xs text-gray-500">لا يتطلب تأكيداً</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-12 text-center text-gray-500">
                <i class="fas fa-calendar-check text-4xl text-gray-300 mb-3"></i>
                <p class="font-semibold">لا مهام تسويق مجدولة لك اليوم.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/marketing-today/index.blade.php ENDPATH**/ ?>