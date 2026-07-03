

<?php $__env->startSection('title', 'الرقابة والجودة'); ?>
<?php $__env->startSection('header', 'الرقابة والجودة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">لوحة الرقابة والجودة</h1>
                <p class="text-gray-600 mt-1">متابعة ورصد جميع العمليات في النظام</p>
            </div>
        </div>
    </div>

    <!-- إحصائيات الطلاب -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات الطلاب</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الطلاب</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($studentStats['total']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700"><?php echo e($studentStats['active']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">تسجيلات هذا الشهر</p>
                    <p class="text-3xl font-black text-purple-700"><?php echo e($studentStats['enrollments_this_month']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مسجلون حديثاً</p>
                    <p class="text-3xl font-black text-blue-700"><?php echo e($studentStats['recent_registrations']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات المدربين -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات المدربين</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المدربين</p>
                    <p class="text-3xl font-black text-indigo-700"><?php echo e($instructorStats['total']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700"><?php echo e($instructorStats['active']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مع اتفاقيات</p>
                    <p class="text-3xl font-black text-purple-700"><?php echo e($instructorStats['with_agreements']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الموظفين -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات الموظفين</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الموظفين</p>
                    <p class="text-3xl font-black text-emerald-700"><?php echo e($employeeStats['total']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                    <p class="text-3xl font-black text-green-700"><?php echo e($employeeStats['active']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مهام معلقة</p>
                    <p class="text-3xl font-black text-yellow-700"><?php echo e($employeeStats['pending_tasks']); ?></p>
                </div>
            </div>
            <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 shadow-xl" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
                <div class="relative z-10">
                    <p class="text-sm font-semibold text-gray-600 mb-1">مهام متأخرة</p>
                    <p class="text-3xl font-black text-red-700"><?php echo e($employeeStats['overdue_tasks']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- العمليات المعلقة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">العمليات المعلقة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">تسجيلات أونلاين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700"><?php echo e($pendingOperations['pending_enrollments']); ?></p>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">تسجيلات أوفلاين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700"><?php echo e($pendingOperations['pending_offline_enrollments']); ?></p>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <p class="text-sm text-gray-600 mb-1">مهام موظفين معلقة</p>
                <p class="text-2xl font-bold text-yellow-700"><?php echo e($pendingOperations['pending_tasks']); ?></p>
            </div>
        </div>
    </div>

    <!-- النشاطات الأخيرة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">النشاطات الأخيرة</h2>
        <div class="space-y-3">
            <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo e($activity->description); ?></p>
                            <p class="text-sm text-gray-600">
                                <?php echo e($activity->user->name ?? 'نظام'); ?> - <?php echo e($activity->created_at->diffForHumans()); ?>

                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\quality-control\index.blade.php ENDPATH**/ ?>