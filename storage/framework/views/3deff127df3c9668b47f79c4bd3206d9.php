<?php $__env->startSection('title', 'سجل اليومي'); ?>
<?php $__env->startSection('header', 'سجل اليومي — ' . $location->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.12) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="dashboard-card rounded-2xl p-5 sm:p-6 shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900">سجل الساعات والمصاريف</h2>
                <p class="text-gray-600 text-sm mt-1"><?php echo e($location->name); ?></p>
            </div>
            <a href="<?php echo e(route('place.office.usage-logs.create')); ?>"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg">
                <i class="fas fa-plus"></i> تسجيل جديد
            </a>
        </div>
    </div>

    <div class="dashboard-card rounded-2xl shadow-xl overflow-hidden">
        <div class="relative z-10 px-5 py-4 border-b border-slate-200/80 bg-white/50">
            <h3 class="font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-clock text-blue-600"></i> ساعات الاستخدام
            </h3>
        </div>
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-bold">النوع</th>
                        <th class="px-4 py-3 text-right font-bold">الكورس</th>
                        <th class="px-4 py-3 text-right font-bold">ساعات</th>
                        <th class="px-4 py-3 text-right font-bold hidden md:table-cell">مصاريف مرفقة</th>
                        <th class="px-4 py-3 text-right font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/60">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap"><?php echo e($log->usage_date->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3"><?php echo e($log->usage_type_label); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($log->offlineCourse?->title ?? '—'); ?></td>
                            <td class="px-4 py-3 font-bold text-blue-700 tabular-nums"><?php echo e(number_format((float) $log->hours, 2)); ?></td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <?php if($log->dailyExpenses->count()): ?>
                                    <span class="text-violet-700 font-medium"><?php echo e($log->dailyExpenses->count()); ?> بند</span>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold
                                    <?php if($log->status === 'approved'): ?> bg-emerald-100 text-emerald-800
                                    <?php elseif($log->status === 'rejected'): ?> bg-rose-100 text-rose-800
                                    <?php else: ?> bg-amber-100 text-amber-800 <?php endif; ?>">
                                    <?php echo e($log->status_label); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد سجلات ساعات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($logs->hasPages()): ?>
            <div class="relative z-10 px-4 py-3 border-t border-slate-100"><?php echo e($logs->links()); ?></div>
        <?php endif; ?>
    </div>

    <div class="dashboard-card rounded-2xl shadow-xl overflow-hidden">
        <div class="relative z-10 px-5 py-4 border-b border-violet-200/80 bg-violet-50/50">
            <h3 class="font-black text-violet-900 flex items-center gap-2">
                <i class="fas fa-receipt text-violet-600"></i> مصاريف يومية (منفصلة)
            </h3>
            <p class="text-xs text-violet-700 mt-1">فواتير مسجّلة بدون ساعات في نفس اليوم</p>
        </div>
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-bold">البيان</th>
                        <th class="px-4 py-3 text-right font-bold">الفئة</th>
                        <th class="px-4 py-3 text-right font-bold">المبلغ</th>
                        <th class="px-4 py-3 text-right font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/60">
                    <?php $__empty_1 = true; $__currentLoopData = $standaloneExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-violet-50/40 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap"><?php echo e($expense->expense_date->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3"><?php echo e($expense->title); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($expense->category_label); ?></td>
                            <td class="px-4 py-3 font-bold text-violet-700 tabular-nums"><?php echo e(number_format($expense->lineTotal(), 2)); ?> ج.م</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold
                                    <?php if($expense->status === 'approved'): ?> bg-emerald-100 text-emerald-800
                                    <?php elseif($expense->status === 'rejected'): ?> bg-rose-100 text-rose-800
                                    <?php else: ?> bg-amber-100 text-amber-800 <?php endif; ?>">
                                    <?php echo e($expense->status_label); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد مصاريف منفصلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($standaloneExpenses->hasPages()): ?>
            <div class="relative z-10 px-4 py-3 border-t border-slate-100"><?php echo e($standaloneExpenses->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/usage-logs/index.blade.php ENDPATH**/ ?>