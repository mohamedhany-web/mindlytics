<?php $__env->startSection('title', 'خطط التسويق والمنصات'); ?>
<?php $__env->startSection('header', 'خطط التسويق والسوشيال ميديا'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.marketing-plans.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-plus"></i>
            خطة تسويق جديدة
        </a>
        <p class="text-sm text-gray-600 max-w-xl">نظّم المنصات، اربط الأحداث بالتقويم، واربط الخطة بدورة تصميم عند الحاجة. تظهر أحداث التسويق في <a href="<?php echo e(route('employee.calendar')); ?>" class="text-pink-600 font-semibold underline">تقويم الموظف</a>.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">منصات</th>
                        <th class="text-right px-4 py-3 font-semibold">أحداث</th>
                        <th class="text-right px-4 py-3 font-semibold">تحديث</th>
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $st = match($p->status) {
                                'draft' => ['مسودة', 'bg-gray-100 text-gray-800'],
                                'active' => ['نشط', 'bg-emerald-100 text-emerald-800'],
                                'paused' => ['متوقف', 'bg-amber-100 text-amber-800'],
                                'completed' => ['مكتمل', 'bg-slate-200 text-slate-800'],
                                default => [$p->status, 'bg-gray-100'],
                            };
                        ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs"><?php echo e($p->id); ?></td>
                            <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($p->title); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($st[1]); ?>"><?php echo e($st[0]); ?></span>
                            </td>
                            <td class="px-4 py-3"><?php echo e($p->platforms_count); ?></td>
                            <td class="px-4 py-3"><?php echo e($p->calendar_events_count); ?></td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap"><?php echo e($p->updated_at->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="<?php echo e(route('employee.marketing-plans.show', $p)); ?>" class="text-pink-700 hover:text-pink-900 font-semibold">إدارة</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد خطط بعد. أنشئ خطة لربط المنصات والتقويم.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($plans->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100"><?php echo e($plans->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/marketing-plans/index.blade.php ENDPATH**/ ?>