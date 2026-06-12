

<?php $__env->startSection('title', 'تفاصيل طلب الإجازة'); ?>
<?php $__env->startSection('header', 'تفاصيل طلب الإجازة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- معلومات الطلب -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">معلومات الطلب</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                <?php if($leave->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                <?php elseif($leave->status === 'approved'): ?> bg-green-100 text-green-800
                <?php elseif($leave->status === 'rejected'): ?> bg-red-100 text-red-800
                <?php else: ?> bg-gray-100 text-gray-800
                <?php endif; ?>">
                <?php echo e($leave->status_label); ?>

            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">الموظف</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->employee->name); ?></p>
                <?php if($leave->employee->employee_code): ?>
                    <p class="text-sm text-gray-500 mt-1">الرمز: <?php echo e($leave->employee->employee_code); ?></p>
                <?php endif; ?>
                <?php if($leave->employee->employeeJob): ?>
                    <p class="text-sm text-gray-500">الوظيفة: <?php echo e($leave->employee->employeeJob->name); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">نوع الإجازة</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->type_label); ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">من تاريخ</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->start_date->format('Y-m-d')); ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">إلى تاريخ</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->end_date->format('Y-m-d')); ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">عدد الأيام</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->days); ?> يوم</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التقديم</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->created_at->format('Y-m-d H:i')); ?></p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">سبب الإجازة</label>
                <p class="text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg"><?php echo e($leave->reason); ?></p>
            </div>
        </div>
    </div>

    <!-- معلومات المراجعة -->
    <?php if($leave->status !== 'pending' && $leave->reviewer): ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">معلومات المراجعة</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تمت المراجعة بواسطة</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->reviewer->name); ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ المراجعة</label>
                <p class="text-base font-semibold text-gray-900"><?php echo e($leave->reviewed_at->format('Y-m-d H:i')); ?></p>
            </div>

            <?php if($leave->admin_notes): ?>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات الإدارة</label>
                <p class="text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg"><?php echo e($leave->admin_notes); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- إجراءات المراجعة -->
    <?php if($leave->status === 'pending'): ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">مراجعة الطلب</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الموافقة -->
            <form action="<?php echo e(route('admin.leaves.approve', $leave)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <label for="approve_notes" class="block text-sm font-medium text-gray-700">ملاحظات (اختياري)</label>
                    <textarea name="admin_notes" id="approve_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="ملاحظات حول الموافقة..."></textarea>
                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        الموافقة على الطلب
                    </button>
                </div>
            </form>

            <!-- الرفض -->
            <form action="<?php echo e(route('admin.leaves.reject', $leave)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <label for="reject_notes" class="block text-sm font-medium text-gray-700">سبب الرفض <span class="text-red-500">*</span></label>
                    <textarea name="admin_notes" id="reject_notes" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="اكتب سبب رفض الطلب..."></textarea>
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        رفض الطلب
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- الأزرار -->
    <div class="flex items-center justify-end">
        <a href="<?php echo e(route('admin.leaves.index')); ?>" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/leaves/show.blade.php ENDPATH**/ ?>