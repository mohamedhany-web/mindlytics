<?php $__env->startSection('title', 'طلبات التصميم'); ?>
<?php $__env->startSection('header', 'طلبات التصميم (مشرف → مصمم)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.design-cycles.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-plus"></i>
            طلب تصميم جديد
        </a>
        <form method="get" class="flex flex-wrap items-center gap-2">
            <select name="status" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                <?php $__currentLoopData = [
                    'pending_design' => 'بانتظار المصمم',
                    'design_in_progress' => 'قيد التنفيذ',
                    'design_submitted' => 'تم تسليم التصميم',
                    'moderator_delivery_pending' => 'بانتظار تسليمك النهائي',
                    'completed' => 'مكتملة',
                    'cancelled' => 'ملغاة',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('status') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">المصمم</th>
                        <th class="text-right px-4 py-3 font-semibold">حد التسليم</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $cycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs"><?php echo e($c->id); ?></td>
                            <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($c->title); ?></td>
                            <td class="px-4 py-3"><?php echo e($c->designer->name ?? '—'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap"><?php echo e($c->deadline_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold bg-fuchsia-50 text-fuchsia-800 border border-fuchsia-100">
                                    <?php echo e(\App\Models\DesignTaskCycle::statusLabel($c->status)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="<?php echo e(route('employee.design-cycles.show', $c)); ?>" class="text-fuchsia-700 hover:text-fuchsia-900 font-semibold">تفاصيل</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($cycles->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100"><?php echo e($cycles->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\design-cycles\index.blade.php ENDPATH**/ ?>