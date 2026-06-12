

<?php $__env->startSection('title', 'إضافة دور جديد'); ?>
<?php $__env->startSection('header', 'إضافة دور جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">معلومات الدور</h3>
                    <p class="text-sm text-gray-500 mt-1">أدخل معلومات الدور الجديد</p>
                </div>
                <a href="<?php echo e(route('admin.roles.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.roles.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="p-6 space-y-6">
                <!-- اسم الدور -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم الدور <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: content_manager">
                    <p class="mt-1 text-xs text-gray-500">
                        اسم فريد باللغة الإنجليزية (بدون مسافات)
                    </p>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الاسم المعروض -->
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        الاسم المعروض <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="display_name" id="display_name" value="<?php echo e(old('display_name')); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: مدير المحتوى">
                    <?php $__errorArgs = ['display_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الوصف -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="وصف مختصر للدور (اختياري)"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الصلاحيات -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        الصلاحيات
                    </label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                        <?php if($permissions->count() > 0): ?>
                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-6 last:mb-0">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">
                                        <?php echo e($group ?? 'عام'); ?>

                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input type="checkbox" name="permissions[]" value="<?php echo e($permission->id); ?>"
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <div class="mr-3">
                                                    <span class="text-sm font-medium text-gray-900"><?php echo e($permission->display_name); ?></span>
                                                    <?php if($permission->description): ?>
                                                        <p class="text-xs text-gray-500"><?php echo e($permission->description); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 text-center py-4">
                                لا توجد صلاحيات متاحة. <a href="<?php echo e(route('admin.permissions.create')); ?>" class="text-blue-600 hover:underline">أضف صلاحيات جديدة</a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php $__errorArgs = ['permissions.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="<?php echo e(route('admin.roles.index')); ?>" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        حفظ الدور
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/roles/create.blade.php ENDPATH**/ ?>