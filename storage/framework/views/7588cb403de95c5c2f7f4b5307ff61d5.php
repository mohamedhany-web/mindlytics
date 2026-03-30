

<?php $__env->startSection('title', 'تفاصيل التسجيل - الأوفلاين'); ?>
<?php $__env->startSection('header', 'تفاصيل التسجيل - الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تفاصيل التسجيل</h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل تسجيل الطالب في الكورس الأوفلاين</p>
            </div>
            <a href="<?php echo e(route('admin.offline-enrollments.index')); ?>" 
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات التسجيل -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">معلومات التسجيل</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الطالب</p>
                            <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineEnrollment->student->name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($offlineEnrollment->student->phone); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الكورس</p>
                            <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineEnrollment->course->title); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($offlineEnrollment->course->instructor->name); ?></p>
                        </div>
                        <?php if($offlineEnrollment->group): ?>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">المجموعة</p>
                            <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineEnrollment->group->name); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الحالة</p>
                            <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                ];
                                $statusTexts = [
                                    'pending' => 'في الانتظار',
                                    'active' => 'نشط',
                                    'completed' => 'مكتمل',
                                    'suspended' => 'معلق',
                                ];
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($statusColors[$offlineEnrollment->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($statusTexts[$offlineEnrollment->status] ?? $offlineEnrollment->status); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">تاريخ التسجيل</p>
                            <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineEnrollment->enrolled_at->format('Y-m-d')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإجراءات -->
        <div>
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
                <div class="space-y-3">
                    <form action="<?php echo e(route('admin.offline-enrollments.update-status', $offlineEnrollment)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">تغيير الحالة</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" <?php echo e($offlineEnrollment->status == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                                <option value="active" <?php echo e($offlineEnrollment->status == 'active' ? 'selected' : ''); ?>>نشط</option>
                                <option value="completed" <?php echo e($offlineEnrollment->status == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                                <option value="suspended" <?php echo e($offlineEnrollment->status == 'suspended' ? 'selected' : ''); ?>>معلق</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>تحديث الحالة
                        </button>
                    </form>
                    
                    <form action="<?php echo e(route('admin.offline-enrollments.destroy', $offlineEnrollment)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-2"></i>حذف التسجيل
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-enrollments/show.blade.php ENDPATH**/ ?>