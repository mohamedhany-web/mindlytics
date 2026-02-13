

<?php $__env->startSection('title', 'تقارير المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-widest text-white/70 mb-3">تقارير المحفظة</p>
                    <h1 class="text-3xl sm:text-4xl font-bold"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h1>
                    <p class="mt-3 text-white/80 flex items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo e($wallet->user?->name ?? 'غير مرتبط بمستخدم'); ?></span>
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-right">
                    <div class="bg-white/15 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">الرصيد الحالي</p>
                        <p class="text-xl font-semibold"><?php echo e(number_format($wallet->balance, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">الرصيد المعلق</p>
                        <p class="text-lg font-semibold"><?php echo e(number_format($wallet->pending_balance ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">عدد التقارير</p>
                        <p class="text-lg font-semibold"><?php echo e($reports->count()); ?></p>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">آخر تحديث</p>
                        <p class="text-sm font-semibold"><?php echo e($wallet->updated_at?->format('Y-m-d H:i') ?? 'غير متوفر'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">إنشاء تقرير جديد</h2>
                    <p class="text-sm text-gray-500">حدد نطاق التاريخ ثم أنشئ تقريراً مفصلاً للمحفظة.</p>
                </div>
                <form action="<?php echo e(route('admin.wallets.generate-report', $wallet)); ?>" method="POST" class="flex flex-col md:flex-row md:items-center gap-3">
                    <?php echo csrf_field(); ?>
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col">
                            <label class="text-xs font-medium text-gray-500 mb-1">من</label>
                            <input type="date" name="from" class="rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-medium text-gray-500 mb-1">إلى</label>
                            <input type="date" name="to" class="rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 text-sm font-semibold shadow-lg shadow-sky-500/20 transition">
                            <i class="fas fa-file-export ml-1"></i>
                            إنشاء تقرير
                        </button>
                        <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-right ml-1"></i>
                            العودة للتفاصيل
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">إجمالي الإيداعات</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">إجمالي السحوبات</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الرصيد النهائي</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الفرق</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-sky-50/70 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo e($report->created_at?->format('Y-m-d H:i') ?? 'غير معروف'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    <?php echo e($report->title ?? 'تقرير بدون عنوان'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-emerald-600">
                                    <?php echo e(number_format($report->total_deposits ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-rose-600">
                                    <?php echo e(number_format($report->total_withdrawals ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    <?php echo e(number_format($report->ending_balance ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-semibold <?php echo e(($report->difference ?? 0) == 0 ? 'text-gray-500' : (($report->difference ?? 0) > 0 ? 'text-emerald-600' : 'text-rose-600')); ?>">
                                    <?php echo e(number_format($report->difference ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                                    <?php if($report->notes): ?>
                                        <div class="text-xs font-normal text-gray-500 mt-1"><?php echo e($report->notes); ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-sky-100 flex items-center justify-center text-sky-500">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <p class="font-semibold text-gray-700">لا توجد تقارير محفوظة لهذه المحفظة حتى الآن.</p>
                                        <p class="text-sm text-gray-500">استخدم النموذج أعلاه لإنشاء تقرير جديد.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/wallets/reports.blade.php ENDPATH**/ ?>