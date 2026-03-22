<?php $__env->startSection('title', __('instructor.agreements_system') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.agreements_system')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-contract text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1"><?php echo e(__('instructor.instructor_panel')); ?></p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 truncate"><?php echo e(__('instructor.agreements_system')); ?></h1>
                    <p class="text-sm text-slate-500 mt-0.5"><?php echo e(__('instructor.my_contract_with_platform')); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.total_earned')); ?></p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-tight tabular-nums"><?php echo e(number_format($stats['total_earned'], 2)); ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.pending')); ?></p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-tight tabular-nums"><?php echo e(number_format($stats['pending_amount'], 2)); ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?php echo e(__('public.currency_egp')); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-clock text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2"><?php echo e(__('instructor.total_payments')); ?></p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 leading-tight tabular-nums"><?php echo e(number_format($stats['total_payments'])); ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?php echo e(__('instructor.payments_log')); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                    <i class="fas fa-receipt text-sky-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($activeAgreement): ?>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <i class="fas fa-circle text-[6px] text-emerald-500"></i>
                    <?php echo e(__('instructor.active_status')); ?>

                </span>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 truncate"><?php echo e($activeAgreement->title); ?></h2>
            </div>
            <a href="<?php echo e(route('instructor.agreements.show', $activeAgreement)); ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm border border-sky-700/20 transition-colors flex-shrink-0">
                <i class="fas fa-eye text-sm"></i>
                <?php echo e(__('instructor.view_details')); ?>

            </a>
        </div>
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.agreement_number')); ?></p>
                    <p class="text-base font-bold text-slate-900 font-mono"><?php echo e($activeAgreement->agreement_number); ?></p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e(__('instructor.type')); ?></p>
                    <p class="text-base font-bold text-slate-900">
                        <?php if(($activeAgreement->billing_type ?? '') === 'course_percentage'): ?>
                            نسبة من الكورس
                        <?php elseif($activeAgreement->type == 'course_price'): ?>
                            <?php echo e(__('instructor.course_price')); ?>

                        <?php elseif($activeAgreement->type == 'hourly_rate'): ?>
                            <?php echo e(__('instructor.hourly_rate')); ?>

                        <?php else: ?>
                            <?php echo e(__('instructor.monthly_salary')); ?>

                        <?php endif; ?>
                    </p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1"><?php echo e((($activeAgreement->billing_type ?? '') === 'course_percentage') ? 'نسبة المدرب' : __('instructor.rate')); ?></p>
                    <p class="text-base font-bold text-slate-900">
                        <?php if(($activeAgreement->billing_type ?? '') === 'course_percentage'): ?>
                            <?php echo e(number_format($activeAgreement->course_percentage ?? 0, 2)); ?>%
                        <?php else: ?>
                            <?php echo e(number_format($activeAgreement->rate ?? 0, 2)); ?> <?php echo e(__('public.currency_egp')); ?>

                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-handshake text-sky-600 text-sm"></i>
                </span>
                <?php echo e(__('instructor.all_agreements')); ?>

            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.agreement_number')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.title')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.type')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.rate')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('common.status')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.start_date')); ?></th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-center text-xs font-bold text-slate-600 uppercase tracking-wide"><?php echo e(__('instructor.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-900 font-mono text-sm"><?php echo e($agreement->agreement_number); ?></span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <p class="font-medium text-slate-800 text-sm max-w-[220px] sm:max-w-xs truncate" title="<?php echo e($agreement->title); ?>"><?php echo e($agreement->title); ?></p>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border
                                <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?> bg-violet-50 text-violet-800 border-violet-100
                                <?php elseif($agreement->type == 'course_price'): ?> bg-sky-50 text-sky-800 border-sky-100
                                <?php elseif($agreement->type == 'hourly_rate'): ?> bg-purple-50 text-purple-800 border-purple-100
                                <?php else: ?> bg-indigo-50 text-indigo-800 border-indigo-100
                                <?php endif; ?>">
                                <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
                                    نسبة من الكورس
                                <?php elseif($agreement->type == 'course_price'): ?>
                                    <?php echo e(__('instructor.course_price')); ?>

                                <?php elseif($agreement->type == 'hourly_rate'): ?>
                                    <?php echo e(__('instructor.hourly_rate')); ?>

                                <?php else: ?>
                                    <?php echo e(__('instructor.monthly_salary')); ?>

                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
                                <span class="text-sm font-bold text-slate-800"><?php echo e(number_format($agreement->course_percentage ?? 0, 2)); ?>%</span>
                            <?php else: ?>
                                <span class="text-sm font-bold text-slate-800 tabular-nums"><?php echo e(number_format($agreement->rate ?? 0, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                <?php if($agreement->status == 'active'): ?> bg-emerald-50 text-emerald-800 border-emerald-100
                                <?php elseif($agreement->status == 'draft'): ?> bg-slate-100 text-slate-700 border-slate-200
                                <?php elseif($agreement->status == 'suspended'): ?> bg-amber-50 text-amber-800 border-amber-100
                                <?php elseif($agreement->status == 'terminated'): ?> bg-rose-50 text-rose-800 border-rose-100
                                <?php else: ?> bg-sky-50 text-sky-800 border-sky-100
                                <?php endif; ?>">
                                <?php if($agreement->status == 'active'): ?> <?php echo e(__('instructor.active_status')); ?>

                                <?php elseif($agreement->status == 'draft'): ?> <?php echo e(__('instructor.draft')); ?>

                                <?php elseif($agreement->status == 'suspended'): ?> <?php echo e(__('instructor.suspended')); ?>

                                <?php elseif($agreement->status == 'terminated'): ?> <?php echo e(__('instructor.terminated')); ?>

                                <?php else: ?> <?php echo e(__('instructor.agreement_completed')); ?>

                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-slate-600 tabular-nums">
                            <?php echo e($agreement->start_date->format('Y-m-d')); ?>

                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center whitespace-nowrap">
                            <a href="<?php echo e(route('instructor.agreements.show', $agreement)); ?>"
                               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-100 transition-colors"
                               title="<?php echo e(__('common.view')); ?>">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    <i class="fas fa-handshake text-slate-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?php echo e(__('instructor.no_agreements')); ?></p>
                                    <p class="text-sm text-slate-500 mt-1"><?php echo e(__('instructor.no_agreements_description')); ?></p>
                                </div>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/agreements/index.blade.php ENDPATH**/ ?>