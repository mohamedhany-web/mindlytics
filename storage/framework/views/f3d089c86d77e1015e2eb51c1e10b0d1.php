

<?php $__env->startSection('title', 'تفاصيل الباقة'); ?>
<?php $__env->startSection('header', 'تفاصيل الباقة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- معلومات الباقة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($package->name); ?></h1>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.packages.edit', $package)); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.packages.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if($package->thumbnail): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة الباقة</label>
                <img src="<?php echo e(asset('storage/' . $package->thumbnail)); ?>" alt="<?php echo e($package->name); ?>" class="w-full h-64 object-cover rounded-lg border border-gray-300">
            </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <p class="text-gray-600"><?php echo e($package->description ?? 'لا يوجد وصف'); ?></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">السعر</label>
                        <p class="text-2xl font-bold text-sky-600">
                            <?php echo e(number_format($package->price, 2)); ?> ج.م
                        </p>
                        <?php if($package->original_price && $package->original_price > $package->price): ?>
                        <p class="text-sm text-gray-500 line-through"><?php echo e(number_format($package->original_price, 2)); ?> ج.م</p>
                        <p class="text-sm text-green-600">خصم <?php echo e($package->discount_percentage); ?>%</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">عدد الكورسات</label>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($package->courses_count ?? 0); ?></p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        <?php echo e($package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e($package->is_active ? 'نشط' : 'معطل'); ?>

                    </span>
                    <?php if($package->is_featured): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        مميز
                    </span>
                    <?php endif; ?>
                    <?php if($package->is_popular): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        الأكثر شعبية
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- المميزات -->
    <?php if($package->features && count($package->features) > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">المميزات</h2>
        <ul class="space-y-2">
            <?php $__currentLoopData = $package->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex items-center text-gray-700">
                <i class="fas fa-check-circle text-green-500 ml-3"></i>
                <?php echo e($feature); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- الكورسات في الباقة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">الكورسات في الباقة (<?php echo e($package->courses->count()); ?>)</h2>
        </div>

        <?php if($package->courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $package->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <h3 class="font-semibold text-gray-900 mb-2"><?php echo e($course->title); ?></h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">
                        <?php if($course->price > 0): ?>
                            <?php echo e(number_format($course->price, 2)); ?> ج.م
                        <?php else: ?>
                            <span class="text-green-600">مجاني</span>
                        <?php endif; ?>
                    </span>
                    <a href="<?php echo e(route('admin.advanced-courses.show', $course)); ?>" class="text-sky-600 hover:text-sky-900">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-gray-500 text-center py-8">لا توجد كورسات في هذه الباقة</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/packages/show.blade.php ENDPATH**/ ?>