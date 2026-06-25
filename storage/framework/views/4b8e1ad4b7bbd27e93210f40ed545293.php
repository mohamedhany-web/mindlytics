

<?php $__env->startSection('title', 'تفاصيل الصلاحية'); ?>
<?php $__env->startSection('header', 'تفاصيل الصلاحية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- معلومات الصلاحية -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($permission->display_name); ?></h3>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($permission->name); ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('admin.permissions.edit', $permission)); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
                    <a href="<?php echo e(route('admin.permissions.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">الاسم</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono"><?php echo e($permission->name); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">الاسم المعروض</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($permission->display_name); ?></dd>
                </div>
                <?php if($permission->group): ?>
                <div>
                    <dt class="text-sm font-medium text-gray-500">المجموعة</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($permission->group); ?></dd>
                </div>
                <?php endif; ?>
                <?php if($permission->description): ?>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">الوصف</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($permission->description); ?></dd>
                </div>
                <?php endif; ?>
                <div>
                    <dt class="text-sm font-medium text-gray-500">عدد الأدوار المرتبطة</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($permission->roles->count()); ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- الأدوار المرتبطة -->
    <?php if($permission->roles->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الأدوار المرتبطة بهذه الصلاحية</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $permission->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 mb-1">
                                    <?php echo e($role->display_name); ?>

                                </h5>
                                <p class="text-xs text-gray-500 mb-2">
                                    <?php echo e($role->name); ?>

                                </p>
                                <?php if($role->is_system): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        دور نظامي
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo e(route('admin.roles.show', $role)); ?>" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-tag text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أدوار مرتبطة</h3>
            <p class="text-gray-500">هذه الصلاحية غير مرتبطة بأي دور حالياً</p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/permissions/show.blade.php ENDPATH**/ ?>