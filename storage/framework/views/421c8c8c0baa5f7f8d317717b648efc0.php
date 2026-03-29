

<?php $__env->startSection('title', 'الراتب والمحاسبة'); ?>
<?php $__env->startSection('header', 'الراتب والمحاسبة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($activeAgreement): ?>
        <!-- الكاردات في بداية الصفحة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الراتب الأساسي</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e(number_format($stats['base_salary'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">خصومات الشهر الحالي</p>
                        <p class="text-3xl font-black text-red-700"><?php echo e(number_format($stats['current_month_deductions'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-minus-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">صافي الراتب</p>
                        <p class="text-3xl font-black text-blue-700"><?php echo e(number_format($stats['net_salary'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الاستحقاق -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الاستحقاق</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ الاستحقاق القادم</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php if($stats['next_payment_date']): ?>
                            <?php echo e($stats['next_payment_date']->format('Y-m-d')); ?>

                        <?php else: ?>
                            غير محدد
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي المدفوعات</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($stats['total_paid'], 2)); ?> ج.م</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl mb-4"></i>
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">لا توجد اتفاقية نشطة</h3>
            <p class="text-yellow-700">لم يتم تعيين اتفاقية عمل نشطة لك حتى الآن</p>
        </div>
    <?php endif; ?>

    <!-- سجل المدفوعات (يظهر لكل موظف لديه مدفوعات) -->
    <?php if(isset($allPayments) && $allPayments->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-list-alt text-blue-600 mr-2"></i>سجل المدفوعات</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الدفعة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الاستحقاق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">صافي الراتب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إيصال التحويل</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $allPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($payment->payment_number); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($payment->payment_date->format('Y-m-d')); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600"><?php echo e(number_format($payment->net_salary, 2)); ?> ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($payment->status === 'paid'): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">مدفوعة</span>
                                <?php if($payment->paid_at): ?><span class="block text-xs text-gray-500 mt-1"><?php echo e($payment->paid_at->format('Y-m-d')); ?></span><?php endif; ?>
                            <?php elseif($payment->status === 'pending'): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">معلقة</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><?php echo e($payment->status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($payment->status === 'paid' && $payment->transfer_receipt_path): ?>
                                <a href="<?php echo e(asset('storage/' . $payment->transfer_receipt_path)); ?>" target="_blank" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-receipt"></i> تحميل الإيصال</a>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if($activeAgreement): ?>
        <!-- الدفعات القادمة -->
        <?php if($upcomingPayments->count() > 0): ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الدفعات القادمة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الدفعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الراتب الأساسي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الخصومات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">صافي الراتب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $upcomingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($payment->payment_number); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($payment->payment_date->format('Y-m-d')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e(number_format($payment->base_salary, 2)); ?> ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo e(number_format($payment->total_deductions, 2)); ?> ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600"><?php echo e(number_format($payment->net_salary, 2)); ?> ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">معلقة</span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- الخصومات الأخيرة -->
        <?php if($recentDeductions->count() > 0): ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الخصومات الأخيرة</h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $recentDeductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900"><?php echo e($deduction->title); ?></h4>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($deduction->description); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($deduction->deduction_date->format('Y-m-d')); ?></p>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-bold text-red-600">-<?php echo e(number_format($deduction->amount, 2)); ?> ج.م</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php if($deduction->type === 'tax'): ?> bg-blue-100 text-blue-800
                                <?php elseif($deduction->type === 'insurance'): ?> bg-purple-100 text-purple-800
                                <?php elseif($deduction->type === 'loan'): ?> bg-orange-100 text-orange-800
                                <?php elseif($deduction->type === 'penalty'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($deduction->type === 'tax'): ?> ضريبة
                                <?php elseif($deduction->type === 'insurance'): ?> تأمين
                                <?php elseif($deduction->type === 'loan'): ?> قرض
                                <?php elseif($deduction->type === 'penalty'): ?> غرامة
                                <?php else: ?> أخرى
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- رابط الاتفاقية -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">اتفاقية العمل</h3>
                    <p class="text-sm text-gray-600 mt-1">رقم الاتفاقية: <?php echo e($activeAgreement->agreement_number); ?></p>
                </div>
                <a href="<?php echo e(route('employee.agreements.show', $activeAgreement)); ?>" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-file-contract mr-2"></i>عرض الاتفاقية
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/accounting/index.blade.php ENDPATH**/ ?>