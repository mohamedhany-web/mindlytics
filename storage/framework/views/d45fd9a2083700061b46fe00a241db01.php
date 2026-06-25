

<?php $__env->startSection('title', 'برامج الإحالات - Mindlytics'); ?>
<?php $__env->startSection('header', 'برامج الإحالات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'referrals'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white/95 border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-sky-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-gift text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">برامج الإحالات</h2>
                    <p class="text-sm text-slate-600 mt-1">خصومات للمحالين ومكافآت للمحيلين — <a href="<?php echo e(route('admin.workshop-promo-codes.index')); ?>" class="text-violet-600 font-semibold underline">أكواد الورش</a> منفصلة للحضور</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i> برنامج جديد
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'إجمالي البرامج', 'value' => $stats['total'], 'icon' => 'fa-list', 'from' => 'sky-500', 'to' => 'blue-600'],
            ['label' => 'نشطة', 'value' => $stats['active'], 'icon' => 'fa-check-circle', 'from' => 'emerald-500', 'to' => 'teal-600'],
            ['label' => 'معطّلة', 'value' => $stats['inactive'], 'icon' => 'fa-pause-circle', 'from' => 'amber-500', 'to' => 'orange-600'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-3xl font-black text-slate-900"><?php echo e(number_format($card['value'])); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-<?php echo e($card['from']); ?> to-<?php echo e($card['to']); ?> text-white flex items-center justify-center shadow-md">
                        <i class="fas <?php echo e($card['icon']); ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($programs->count() > 0): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3 text-right font-bold">البرنامج</th>
                            <th class="px-5 py-3 text-right font-bold">خصم المحال</th>
                            <th class="px-5 py-3 text-right font-bold">مكافأة المحيل</th>
                            <th class="px-5 py-3 text-right font-bold">صلاحية الخصم</th>
                            <th class="px-5 py-3 text-right font-bold">الحالة</th>
                            <th class="px-5 py-3 text-center font-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900"><?php echo e($program->name); ?></div>
                                    <?php if($program->description): ?>
                                        <div class="text-xs text-slate-500 mt-0.5"><?php echo e(Str::limit($program->description, 60)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 font-semibold">
                                    <?php if($program->discount_type === 'percentage'): ?>
                                        <?php echo e(number_format($program->discount_value, 0)); ?>%
                                    <?php else: ?>
                                        <?php echo e(number_format($program->discount_value, 2)); ?> ج.م
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <?php if($program->referrer_reward_value): ?>
                                        <?php if($program->referrer_reward_type === 'percentage'): ?> <?php echo e(number_format($program->referrer_reward_value, 0)); ?>%
                                        <?php elseif($program->referrer_reward_type === 'points'): ?> <?php echo e(number_format($program->referrer_reward_value, 0)); ?> نقطة
                                        <?php else: ?> <?php echo e(number_format($program->referrer_reward_value, 2)); ?> ج.م <?php endif; ?>
                                    <?php else: ?> <span class="text-slate-400">—</span> <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-slate-600"><?php echo e($program->discount_valid_days); ?> يوم</td>
                                <td class="px-5 py-4">
                                    <?php if($program->is_active && $program->isValid()): ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">نشط</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="<?php echo e(route('admin.referral-programs.show', $program)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100"><i class="fas fa-eye text-xs"></i></a>
                                        <a href="<?php echo e(route('admin.referral-programs.edit', $program)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100"><i class="fas fa-edit text-xs"></i></a>
                                        <form action="<?php echo e(route('admin.referral-programs.destroy', $program)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف البرنامج؟');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"><i class="fas fa-trash text-xs"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100"><?php echo e($programs->links()); ?></div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl bg-white border border-slate-200 p-12 text-center shadow-sm">
            <i class="fas fa-gift text-5xl text-slate-300 mb-4"></i>
            <p class="text-slate-600 font-semibold mb-4">لا توجد برامج إحالات</p>
            <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white font-bold">إنشاء برنامج</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/referral-programs/index.blade.php ENDPATH**/ ?>