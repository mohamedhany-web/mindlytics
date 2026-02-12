

<?php $__env->startSection('title', 'تفاصيل الاتفاقية - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل الاتفاقية'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-file-contract text-sky-600"></i>
                    <?php echo e($agreement->title); ?>

                </h2>
                <p class="text-sm text-slate-500 mt-2">رقم الاتفاقية: <span class="font-semibold"><?php echo e($agreement->agreement_number); ?></span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.agreements.edit', $agreement)); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-xl shadow hover:bg-amber-700 transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.agreements.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($stats['total_earned'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">معلق</p>
                <p class="text-2xl font-bold text-amber-600"><?php echo e(number_format($stats['pending_amount'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($stats['total_payments']); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">مدفوع</p>
                <p class="text-2xl font-bold text-emerald-600"><?php echo e($stats['paid_payments']); ?></p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الاتفاقية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">المدرب</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->instructor->name); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($agreement->instructor->phone); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">نوع الاتفاقية</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->type_label); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">السعر/المعدل</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e(number_format($agreement->rate, 2)); ?> ج.م</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($agreement->status == 'active' ? 'bg-emerald-100 text-emerald-700' : ($agreement->status == 'draft' ? 'bg-gray-100 text-gray-700' : 'bg-rose-100 text-rose-700')); ?>">
                                <?php echo e($agreement->status_label); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->start_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php if($agreement->end_date): ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->end_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($agreement->description): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700"><?php echo e($agreement->description); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($agreement->terms): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($agreement->terms); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Payments -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <th class="px-6 py-4 text-right">رقم الدفعة</th>
                                <th class="px-6 py-4 text-right">النوع</th>
                                <th class="px-6 py-4 text-right">المبلغ</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                                <th class="px-6 py-4 text-right">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white/80 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $agreement->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4"><?php echo e($payment->payment_number); ?></td>
                                    <td class="px-6 py-4"><?php echo e($payment->type_label); ?></td>
                                    <td class="px-6 py-4 font-semibold"><?php echo e(number_format($payment->amount, 2)); ?> ج.م</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($payment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status == 'approved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')); ?>">
                                            <?php echo e($payment->status_label); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500"><?php echo e($payment->created_at->format('Y-m-d')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد مدفوعات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">إجراءات سريعة</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-3">
                    <a href="<?php echo e(route('admin.agreements.edit', $agreement)); ?>" class="block w-full text-center px-4 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الاتفاقية
                    </a>
                    <?php if($agreement->status == 'active'): ?>
                        <form method="POST" action="<?php echo e(route('admin.agreements.update', $agreement)); ?>" class="inline-block w-full">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="suspended">
                            <button type="submit" class="block w-full text-center px-4 py-2.5 bg-amber-100 text-amber-700 rounded-xl hover:bg-amber-200 transition-all">
                                <i class="fas fa-pause ml-2"></i>
                                تعليق الاتفاقية
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/agreements/show.blade.php ENDPATH**/ ?>