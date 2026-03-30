

<?php $__env->startSection('title', 'تفاصيل الاتفاقية'); ?>
<?php $__env->startSection('header', 'تفاصيل الاتفاقية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- معلومات الاتفاقية -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($agreement->title); ?></h1>
                <p class="text-gray-600 mt-1">رقم الاتفاقية: <?php echo e($agreement->agreement_number); ?></p>
            </div>
            <span class="px-4 py-2 text-sm font-semibold rounded-full
                <?php if($agreement->status === 'active'): ?> bg-green-100 text-green-800
                <?php elseif($agreement->status === 'suspended'): ?> bg-yellow-100 text-yellow-800
                <?php elseif($agreement->status === 'terminated'): ?> bg-red-100 text-red-800
                <?php elseif($agreement->status === 'completed'): ?> bg-blue-100 text-blue-800
                <?php else: ?> bg-gray-100 text-gray-800
                <?php endif; ?>">
                <?php if($agreement->status === 'active'): ?> نشطة
                <?php elseif($agreement->status === 'suspended'): ?> معلقة
                <?php elseif($agreement->status === 'terminated'): ?> منتهية
                <?php elseif($agreement->status === 'completed'): ?> مكتملة
                <?php else: ?> مسودة
                <?php endif; ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">الراتب الأساسي</p>
                <p class="text-3xl font-bold text-green-600"><?php echo e(number_format($agreement->salary, 2)); ?> ج.م</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ البدء</p>
                <p class="text-xl font-semibold text-gray-900"><?php echo e($agreement->start_date->format('Y-m-d')); ?></p>
            </div>
            <?php if($agreement->end_date): ?>
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ الانتهاء</p>
                <p class="text-xl font-semibold text-gray-900"><?php echo e($agreement->end_date->format('Y-m-d')); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php if($agreement->description): ?>
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed"><?php echo e($agreement->description); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- شروط العقد -->
    <?php if($agreement->contract_terms): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">شروط العقد</h2>
        <div class="prose max-w-none">
            <p class="text-gray-900 whitespace-pre-wrap"><?php echo e($agreement->contract_terms); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- بنود الاتفاقية -->
    <?php if($agreement->agreement_terms): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">بنود الاتفاقية</h2>
        <div class="prose max-w-none">
            <p class="text-gray-900 whitespace-pre-wrap"><?php echo e($agreement->agreement_terms); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي الخصومات</p>
            <p class="text-2xl font-bold text-red-600"><?php echo e(number_format($stats['total_deductions'], 2)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي المدفوعات</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($stats['total_payments'], 2)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">الدفعات المعلقة</p>
            <p class="text-2xl font-bold text-yellow-600"><?php echo e($stats['pending_payments']); ?></p>
        </div>
    </div>

    <!-- الخصومات -->
    <?php if($agreement->deductions->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">الخصومات</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الخصم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $agreement->deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($deduction->deduction_number); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($deduction->title); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <?php if($deduction->type === 'tax'): ?> ضريبة
                                <?php elseif($deduction->type === 'insurance'): ?> تأمين
                                <?php elseif($deduction->type === 'loan'): ?> قرض
                                <?php elseif($deduction->type === 'penalty'): ?> غرامة
                                <?php else: ?> أخرى
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600"><?php echo e(number_format($deduction->amount, 2)); ?> ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($deduction->deduction_date->format('Y-m-d')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- المدفوعات -->
    <?php if($agreement->payments->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">سجل المدفوعات</h2>
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
                    <?php $__currentLoopData = $agreement->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($payment->payment_number); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($payment->payment_date->format('Y-m-d')); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e(number_format($payment->base_salary, 2)); ?> ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo e(number_format($payment->total_deductions, 2)); ?> ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600"><?php echo e(number_format($payment->net_salary, 2)); ?> ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php if($payment->status === 'paid'): ?> bg-green-100 text-green-800
                                <?php elseif($payment->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($payment->status === 'overdue'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($payment->status === 'paid'): ?> مدفوعة
                                <?php elseif($payment->status === 'pending'): ?> معلقة
                                <?php elseif($payment->status === 'overdue'): ?> متأخرة
                                <?php else: ?> ملغاة
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex justify-end">
        <a href="<?php echo e(route('employee.agreements.index')); ?>" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/agreements/show.blade.php ENDPATH**/ ?>