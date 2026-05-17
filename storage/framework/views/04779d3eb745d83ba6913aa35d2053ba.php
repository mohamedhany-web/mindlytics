

<?php $__env->startSection('title', 'تفاصيل المكان'); ?>
<?php $__env->startSection('header', 'تفاصيل المكان'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($offlineLocation->name); ?></h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل المكان</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.offline-locations.index')); ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i>العودة
                </a>
                <a href="<?php echo e(route('admin.offline-locations.edit', $offlineLocation)); ?>" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>تعديل
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الكورسات</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total_courses']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-book-reader text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الكورسات النشطة</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['active_courses']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المكان -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات المكان</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if($offlineLocation->address): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">العنوان</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->address); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->city): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">المدينة</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->city); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->phone): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">رقم الهاتف</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->phone); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->capacity > 0): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">السعة</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->capacity); ?> شخص</p>
            </div>
            <?php endif; ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">الحالة</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($offlineLocation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                    <?php echo e($offlineLocation->is_active ? 'نشط' : 'غير نشط'); ?>

                </span>
            </div>
        </div>
        <?php if($offlineLocation->description): ?>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed"><?php echo e($offlineLocation->description); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- الكورسات المرتبطة -->
    <?php if($offlineLocation->courses->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">الكورسات المرتبطة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $offlineLocation->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.offline-courses.show', $course)); ?>" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900"><?php echo e($course->title); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo e($course->instructor->name); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e($course->status === 'active' ? 'نشط' : $course->status); ?>

                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/offline-locations/show.blade.php ENDPATH**/ ?>