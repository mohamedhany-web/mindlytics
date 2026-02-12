

<?php $__env->startSection('title', 'اتفاقيات المدربين - الأوفلاين'); ?>
<?php $__env->startSection('header', 'اتفاقيات المدربين - الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">اتفاقيات المدربين - الأوفلاين</h1>
                <p class="text-gray-600 mt-1">إدارة اتفاقيات المدربين للكورسات الأوفلاين</p>
            </div>
            <a href="<?php echo e(route('admin.offline-agreements.create')); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة اتفاقية جديدة
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" action="<?php echo e(route('admin.offline-agreements.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="البحث في الاتفاقيات..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                    </select>
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                    <select name="instructor_id" id="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع المدربين</option>
                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(request('instructor_id') == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        بحث
                    </button>
                    <?php if(request()->hasAny(['search', 'status', 'instructor_id'])): ?>
                        <a href="<?php echo e(route('admin.offline-agreements.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة الاتفاقيات -->
    <?php if($agreements->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-indigo-100/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 truncate"><?php echo e($agreement->title); ?></h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php if($agreement->status === 'active'): ?> bg-green-100 text-green-800
                            <?php elseif($agreement->status === 'draft'): ?> bg-yellow-100 text-yellow-800
                            <?php elseif($agreement->status === 'completed'): ?> bg-blue-100 text-blue-800
                            <?php else: ?> bg-red-100 text-red-800
                            <?php endif; ?>">
                            <?php if($agreement->status === 'active'): ?> نشط
                            <?php elseif($agreement->status === 'draft'): ?> مسودة
                            <?php elseif($agreement->status === 'completed'): ?> مكتمل
                            <?php else: ?> ملغي
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-hashtag text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الرقم:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($agreement->agreement_number); ?></span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-user-tie text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">المدرب:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($agreement->instructor->name); ?></span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-money-bill-wave text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">المبلغ:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e(number_format($agreement->total_amount, 2)); ?> ر.س</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">تاريخ البدء:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($agreement->start_date->format('Y-m-d')); ?></span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2">
                    <a href="<?php echo e(route('admin.offline-agreements.show', $agreement)); ?>" 
                       class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i>عرض
                    </a>
                    <a href="<?php echo e(route('admin.offline-agreements.edit', $agreement)); ?>" 
                       class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                    <form action="<?php echo e(route('admin.offline-agreements.destroy', $agreement)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-contract text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد اتفاقيات</p>
            <p class="text-sm text-gray-600 mb-6">ابدأ بإضافة اتفاقية جديدة</p>
            <a href="<?php echo e(route('admin.offline-agreements.create')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus"></i>
                <span>إضافة اتفاقية</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if($agreements->hasPages()): ?>
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
        <?php echo e($agreements->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-agreements/index.blade.php ENDPATH**/ ?>