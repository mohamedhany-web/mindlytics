<?php $__env->startSection('title', __('instructor.agreement_details_title') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.agreement_details_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#2CA9BD] via-[#65DBE4] to-[#2CA9BD] flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-file-contract text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900"><?php echo e($agreement->title); ?></h2>
                    <p class="text-sm text-slate-600 mt-1"><?php echo e(__('instructor.agreement_number')); ?>: <span class="font-semibold"><?php echo e($agreement->agreement_number ?? 'N/A'); ?></span></p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.agreements.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                <?php echo e(__('instructor.back')); ?>

            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="dashboard-card rounded-xl border-2 border-emerald-200/50 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-emerald-700 mb-2"><?php echo e(__('instructor.total_payments')); ?></p>
                <p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($stats['total_earned'], 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-amber-200/50 bg-gradient-to-br from-amber-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-amber-700 mb-2"><?php echo e(__('instructor.pending')); ?></p>
                <p class="text-2xl font-black text-amber-700"><?php echo e(number_format($stats['pending_amount'], 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-blue-200/50 bg-gradient-to-br from-blue-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-blue-700 mb-2"><?php echo e(__('instructor.total_payments')); ?></p>
                <p class="text-2xl font-black text-blue-700"><?php echo e($stats['total_payments']); ?></p>
            </div>
            <div class="dashboard-card rounded-xl border-2 border-green-200/50 bg-gradient-to-br from-green-50 to-white p-5 shadow-lg">
                <p class="text-xs font-semibold text-green-700 mb-2"><?php echo e(__('instructor.paid')); ?></p>
                <p class="text-2xl font-black text-green-700"><?php echo e($stats['paid_payments']); ?></p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        <?php echo e(__('instructor.agreement_info')); ?>

                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-xl border-2 border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 mb-1"><?php echo e(__('instructor.agreement_type_label')); ?></p>
                            <p class="text-sm font-black text-slate-900">
                                <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
                                    نسبة من الكورس
                                    <?php if($agreement->advancedCourse): ?>
                                        <span class="block text-xs font-normal text-slate-600 mt-1"><?php echo e($agreement->advancedCourse->title); ?></span>
                                    <?php endif; ?>
                                <?php elseif($agreement->type == 'course_price'): ?>
                                    <?php echo e(__('instructor.course_price_full')); ?>

                                <?php elseif($agreement->type == 'hourly_rate'): ?>
                                    <?php echo e(__('instructor.hourly_rate_recorded')); ?>

                                <?php else: ?>
                                    <?php echo e(__('instructor.monthly_salary')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-xl border-2 border-purple-100">
                            <p class="text-xs font-semibold text-purple-700 mb-1"><?php echo e((($agreement->billing_type ?? '') === 'course_percentage') ? 'نسبة المدرب' : __('instructor.rate')); ?></p>
                            <p class="text-sm font-black text-slate-900">
                                <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
                                    <?php echo e(number_format($agreement->course_percentage ?? 0, 2)); ?>%
                                <?php else: ?>
                                    <?php echo e(number_format($agreement->rate, 2)); ?> <?php echo e(__('public.currency_egp')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-white p-4 rounded-xl border-2 border-emerald-100">
                            <p class="text-xs font-semibold text-emerald-700 mb-1"><?php echo e(__('common.status')); ?></p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                <?php if($agreement->status == 'active'): ?> bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                <?php elseif($agreement->status == 'draft'): ?> bg-gray-100 text-gray-700 border-2 border-gray-200
                                <?php elseif($agreement->status == 'suspended'): ?> bg-amber-100 text-amber-700 border-2 border-amber-200
                                <?php elseif($agreement->status == 'terminated'): ?> bg-rose-100 text-rose-700 border-2 border-rose-200
                                <?php else: ?> bg-blue-100 text-blue-700 border-2 border-blue-200
                                <?php endif; ?>">
                                <?php if($agreement->status == 'active'): ?> <?php echo e(__('instructor.active_status')); ?>

                                <?php elseif($agreement->status == 'draft'): ?> <?php echo e(__('instructor.draft')); ?>

                                <?php elseif($agreement->status == 'suspended'): ?> <?php echo e(__('instructor.suspended')); ?>

                                <?php elseif($agreement->status == 'terminated'): ?> <?php echo e(__('instructor.terminated')); ?>

                                <?php else: ?> <?php echo e(__('instructor.agreement_completed')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-xl border-2 border-indigo-100">
                            <p class="text-xs font-semibold text-indigo-700 mb-1"><?php echo e(__('instructor.start_date')); ?></p>
                            <p class="text-sm font-black text-slate-900"><?php echo e($agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-'); ?></p>
                        </div>
                        <?php if($agreement->end_date): ?>
                        <div class="bg-gradient-to-br from-orange-50 to-white p-4 rounded-xl border-2 border-orange-100">
                            <p class="text-xs font-semibold text-orange-700 mb-1"><?php echo e(__('instructor.end_date')); ?></p>
                            <p class="text-sm font-black text-slate-900"><?php echo e($agreement->end_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($agreement->description): ?>
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.description')); ?></p>
                        <p class="text-sm text-slate-700 leading-relaxed"><?php echo e($agreement->description); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($agreement->terms): ?>
                    <div class="bg-gradient-to-br from-slate-50 to-white p-4 rounded-xl border-2 border-slate-100">
                        <p class="text-xs font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.contract_terms')); ?></p>
                        <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed"><?php echo e($agreement->terms); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($agreement->notes): ?>
                    <div class="bg-gradient-to-br from-amber-50 to-white p-4 rounded-xl border-2 border-amber-100">
                        <p class="text-xs font-semibold text-amber-700 mb-2"><?php echo e(__('instructor.notes')); ?></p>
                        <div class="text-sm text-amber-800 whitespace-pre-line leading-relaxed"><?php echo e($agreement->notes); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
            
            <?php
                $activationPayments = $agreement->payments->where('type', 'course_activation');
            ?>
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-blue-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-blue-50 to-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-blue-600"></i>
                            تفعيلات الطلاب وحصتي من كل شراء
                        </h3>
                        <p class="text-sm text-slate-600 mt-1">عند كل تفعيل لطالب في الكورس تظهر نسبتك من مبلغ الشراء وحصتك.</p>
                    </div>
                    <a href="<?php echo e(route('instructor.agreements.export-activations', $agreement)); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-500/25 transition-all duration-200 border-2 border-emerald-500/30 whitespace-nowrap">
                        <i class="fas fa-file-excel"></i>
                        تصدير إلى Excel
                    </a>
                </div>
                <?php if($activationPayments->isNotEmpty()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-widest text-slate-700">
                                <th class="px-6 py-4 text-right">التاريخ</th>
                                <th class="px-6 py-4 text-right">الطالب</th>
                                <th class="px-6 py-4 text-right">مبلغ الشراء (ج.م)</th>
                                <th class="px-6 py-4 text-right">نسبتي</th>
                                <th class="px-6 py-4 text-right">حصتي (ج.م)</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            <?php $__currentLoopData = $activationPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-600"><?php echo e($p->created_at?->format('Y-m-d') ?? '—'); ?></td>
                                <td class="px-6 py-4 font-medium text-slate-900"><?php echo e($p->enrollment?->student?->name ?? '—'); ?></td>
                                <td class="px-6 py-4"><?php echo e($p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—'); ?></td>
                                <td class="px-6 py-4 font-semibold text-blue-700"><?php echo e(number_format($agreement->course_percentage ?? 0, 2)); ?>%</td>
                                <td class="px-6 py-4 font-black text-slate-900"><?php echo e(number_format($p->amount, 2)); ?> ج.م</td>
                                <td class="px-6 py-4">
                                    <?php if($p->status === 'paid'): ?>
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مدفوع</span>
                                    <?php elseif($p->status === 'approved'): ?>
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">موافق عليه</span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">قيد المراجعة</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 sm:px-8 lg:px-12 bg-gradient-to-r from-blue-50 to-white border-t-2 border-blue-100">
                    <p class="text-base font-black text-slate-900">
                        إجمالي أرباحي من هذه الاتفاقية: <span class="text-blue-700"><?php echo e(number_format($activationPayments->sum('amount'), 2)); ?> ج.م</span>
                    </p>
                </div>
                <?php else: ?>
                <div class="px-5 py-8 sm:px-8 lg:px-12 text-center text-slate-500">
                    <i class="fas fa-user-graduate text-4xl text-slate-300 mb-3"></i>
                    <p class="font-medium">لا توجد تفعيلات للطلاب حتى الآن.</p>
                    <p class="text-sm mt-1">عند تفعيل أي طالب في هذا الكورس ستظهر هنا نسبتك وحصتك تلقائياً.</p>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <!-- Payments -->
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-receipt text-[#2CA9BD]"></i>
                        <?php echo e(__('instructor.payments_log')); ?>

                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-bold uppercase tracking-widest text-slate-700">
                                <th class="px-6 py-4 text-right"><?php echo e(__('instructor.payment_number')); ?></th>
                                <th class="px-6 py-4 text-right"><?php echo e(__('instructor.type')); ?></th>
                                <th class="px-6 py-4 text-right"><?php echo e(__('instructor.amount')); ?></th>
                                <th class="px-6 py-4 text-right"><?php echo e(__('common.status')); ?></th>
                                <th class="px-6 py-4 text-right"><?php echo e(__('common.date')); ?></th>
                                <th class="px-6 py-4 text-right"><?php echo e(__('instructor.transfer_receipt')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $agreement->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900"><?php echo e($payment->payment_number ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                            $typeLabels = [
                                                'course_completion' => __('instructor.course_price_full'),
                                                'course_sale' => __('instructor.course_price'),
                                                'course_price' => __('instructor.course_price'),
                                                'hourly_teaching' => __('instructor.hourly_rate_recorded'),
                                                'lecture_hour' => __('instructor.hourly_rate_recorded'),
                                                'hourly_rate' => __('instructor.hourly_rate'),
                                                'monthly_salary' => __('instructor.monthly_salary'),
                                                'bonus' => __('instructor.bonus'),
                                                'other' => __('instructor.other'),
                                                'course_activation' => 'نسبة من تفعيل الطالب',
                                            ];
                                            $typeLabel = $typeLabels[$payment->type] ?? ($payment->type ?? __('instructor.not_specified'));
                                        ?>
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                                <?php echo e($typeLabel); ?>

                                            </span>
                                            <?php if($payment->type === 'course_activation' && $payment->enrollment): ?>
                                                <p class="text-xs text-slate-500 mt-1">الطالب: <?php echo e($payment->enrollment->student->name ?? '—'); ?></p>
                                                <p class="text-xs text-slate-500 mt-1">مبلغ التفعيل: <?php echo e(number_format($payment->enrollment->final_price ?? 0, 2)); ?> ج.م → حصتك <?php echo e(number_format($payment->amount, 2)); ?> ج.م</p>
                                            <?php endif; ?>
                                            <?php if($payment->course): ?>
                                                <p class="text-xs text-slate-500 mt-1"><?php echo e($payment->course->title ?? ''); ?></p>
                                            <?php endif; ?>
                                            <?php if($payment->lecture): ?>
                                                <p class="text-xs text-slate-500 mt-1"><?php echo e($payment->lecture->title ?? ''); ?></p>
                                            <?php endif; ?>
                                            <?php if($payment->hours_count): ?>
                                                <p class="text-xs text-slate-500 mt-1"><?php echo e($payment->hours_count); ?> <?php echo e(__('instructor.hour')); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900"><?php echo e(number_format($payment->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
                                            <?php if($payment->status == 'paid'): ?> bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                                            <?php elseif($payment->status == 'approved'): ?> bg-amber-100 text-amber-700 border-2 border-amber-200
                                            <?php else: ?> bg-gray-100 text-gray-700 border-2 border-gray-200
                                            <?php endif; ?>">
                                            <?php if($payment->status == 'paid'): ?> <?php echo e(__('instructor.received')); ?>

                                            <?php elseif($payment->status == 'approved'): ?> <?php echo e(__('instructor.approved')); ?>

                                            <?php else: ?> <?php echo e(__('instructor.pending_review')); ?>

                                            <?php endif; ?>
                                        </span>
                                        <?php if($payment->status == 'paid'): ?>
                                            <p class="text-xs text-emerald-600 mt-1"><?php echo e(__('instructor.amount_transferred_info')); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600"><?php echo e($payment->created_at->format('Y-m-d')); ?></td>
                                    <td class="px-6 py-4">
                                        <?php if($payment->status == 'paid' && $payment->transfer_receipt_path): ?>
                                            <a href="<?php echo e(asset('storage/' . $payment->transfer_receipt_path)); ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                                                <i class="fas fa-receipt"></i>
                                                <?php echo e(__('instructor.download_receipt')); ?>

                                            </a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                                <i class="fas fa-receipt text-slate-400 text-2xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900"><?php echo e(__('instructor.no_payments')); ?></p>
                                                <p class="text-sm text-slate-600 mt-1"><?php echo e(__('instructor.no_payments_yet')); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                        <?php echo e(__('instructor.quick_tips')); ?>

                    </h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <span class="text-sm font-bold text-blue-800"><?php echo e(__('instructor.tips')); ?></span>
                        </div>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1.5 font-medium">
                            <li>• <?php echo e(__('instructor.tips_follow_payments')); ?></li>
                            <li>• <?php echo e(__('instructor.tips_pending')); ?></li>
                            <li>• <?php echo e(__('instructor.tips_completed')); ?></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\agreements\show.blade.php ENDPATH**/ ?>