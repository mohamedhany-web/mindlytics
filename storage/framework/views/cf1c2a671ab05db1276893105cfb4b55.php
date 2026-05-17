

<?php $__env->startSection('title', 'إدارة الأدوار'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">إدارة الأدوار</h1>
        <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn-primary">
            <i class="fas fa-plus ml-2"></i>
            إضافة دور جديد
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم المعروض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصلاحيات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدمين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900"><?php echo e($role->name); ?></span>
                            <?php if($role->is_system): ?>
                                <span class="mr-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">نظام</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo e($role->display_name); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($role->description ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="badge badge-primary"><?php echo e($role->permissions->count()); ?> صلاحية</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="badge badge-secondary"><?php echo e($role->users->count()); ?> مستخدم</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('admin.roles.show', $role)); ?>" class="text-sky-600 hover:text-sky-900 mr-4">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if(!$role->is_system): ?>
                            <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">لا توجد أدوار</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/roles/index.blade.php ENDPATH**/ ?>