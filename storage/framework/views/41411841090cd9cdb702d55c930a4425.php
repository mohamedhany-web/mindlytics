

<?php $__env->startSection('title', 'تفاصيل الكورس الأوفلاين'); ?>
<?php $__env->startSection('header', 'تفاصيل الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($offlineCourse->title); ?></h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل الكورس الأوفلاين</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.offline-courses.index')); ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i>العودة
                </a>
                <a href="<?php echo e(route('admin.offline-courses.edit', $offlineCourse)); ?>" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>تعديل
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الطلاب</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total_students']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-green-100/60 via-emerald-100/40 to-green-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الطلاب النشطين</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['active_students']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-violet-100/40 to-purple-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">المجموعات</p>
                        <p class="text-3xl font-black text-purple-700"><?php echo e($stats['total_groups']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users-cog text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-orange-200/50 hover:border-orange-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 247, 237, 0.95) 50%, rgba(255, 237, 213, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-100/60 via-amber-100/40 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الأنشطة</p>
                        <p class="text-3xl font-black text-orange-700"><?php echo e($stats['total_activities']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الكورس</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">المدرب</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineCourse->instructor->name); ?></p>
            </div>
            <?php if($offlineCourse->locationModel): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">المكان</p>
                <p class="font-semibold text-gray-900 text-lg"><i class="fas fa-map-marker-alt mr-1 text-blue-600"></i><?php echo e($offlineCourse->locationModel->name); ?></p>
                <?php if($offlineCourse->locationModel->address): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($offlineCourse->locationModel->address); ?></p>
                <?php endif; ?>
            </div>
            <?php elseif($offlineCourse->location): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">الموقع</p>
                <p class="font-semibold text-gray-900 text-lg"><i class="fas fa-map-marker-alt mr-1 text-blue-600"></i><?php echo e($offlineCourse->location); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineCourse->start_date): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">تاريخ البدء</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineCourse->start_date->format('Y-m-d')); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineCourse->end_date): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">تاريخ الانتهاء</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineCourse->end_date->format('Y-m-d')); ?></p>
            </div>
            <?php endif; ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">الحالة</p>
                <?php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'active' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusTexts = [
                        'draft' => 'مسودة',
                        'active' => 'نشط',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                    ];
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($statusColors[$offlineCourse->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                    <?php echo e($statusTexts[$offlineCourse->status] ?? $offlineCourse->status); ?>

                </span>
            </div>
        </div>
        <?php if($offlineCourse->description): ?>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed"><?php echo e($offlineCourse->description); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- الروابط السريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="<?php echo e(route('admin.offline-courses.groups.index', $offlineCourse)); ?>" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">المجموعات</h3>
                    <p class="text-sm text-gray-600"><?php echo e($stats['total_groups']); ?> مجموعة</p>
                </div>
            </div>
        </a>
        <a href="<?php echo e(route('admin.offline-courses.enrollments.index', $offlineCourse)); ?>" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">التسجيلات</h3>
                    <p class="text-sm text-gray-600"><?php echo e($stats['total_students']); ?> طالب</p>
                </div>
            </div>
        </a>
        <?php if(Route::has('admin.offline-courses.activities.index')): ?>
        <a href="<?php echo e(route('admin.offline-courses.activities.index', $offlineCourse)); ?>" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">الأنشطة</h3>
                    <p class="text-sm text-gray-600"><?php echo e($stats['total_activities'] ?? 0); ?> نشاط</p>
                </div>
            </div>
        </a>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 opacity-90">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">الأنشطة</h3>
                    <p class="text-sm text-gray-600"><?php echo e($stats['total_activities'] ?? 0); ?> نشاط</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-courses/show.blade.php ENDPATH**/ ?>