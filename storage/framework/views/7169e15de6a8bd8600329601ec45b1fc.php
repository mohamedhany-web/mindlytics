

<?php $__env->startSection('title', 'اتفاقياتي'); ?>
<?php $__env->startSection('header', 'اتفاقياتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if($agreements->count() > 0): ?>
        <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo e($agreement->title); ?></h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">رقم الاتفاقية</p>
                            <p class="font-semibold text-gray-900"><?php echo e($agreement->agreement_number); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الراتب الأساسي</p>
                            <p class="font-semibold text-green-600 text-lg"><?php echo e(number_format($agreement->salary, 2)); ?> ج.م</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">تاريخ البدء</p>
                            <p class="font-semibold text-gray-900"><?php echo e($agreement->start_date->format('Y-m-d')); ?></p>
                        </div>
                    </div>

                    <?php if($agreement->description): ?>
                    <p class="text-gray-600 mb-4"><?php echo e($agreement->description); ?></p>
                    <?php endif; ?>

                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span><i class="fas fa-calendar mr-1"></i> من <?php echo e($agreement->start_date->format('Y-m-d')); ?></span>
                        <?php if($agreement->end_date): ?>
                            <span><i class="fas fa-calendar-check mr-1"></i> إلى <?php echo e($agreement->end_date->format('Y-m-d')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <a href="<?php echo e(route('employee.agreements.show', $agreement)); ?>" class="ml-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-eye mr-2"></i>عرض التفاصيل
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <i class="fas fa-file-contract text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد اتفاقيات</h3>
            <p class="text-gray-600">لم يتم إنشاء أي اتفاقيات لك حتى الآن</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/agreements/index.blade.php ENDPATH**/ ?>