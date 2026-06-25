

<?php $__env->startSection('title', 'الشارات'); ?>
<?php $__env->startSection('header', 'الشارات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الشارات</h1>
                <p class="text-gray-600 mt-1">إدارة شارات الطلاب</p>
            </div>
            <a href="<?php echo e(route('admin.badges.create')); ?>" 
               class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-plus mr-2"></i>
                إضافة شارة جديدة
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    <?php if(isset($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">إجمالي الشارات</div>
            <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['total'] ?? 0); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">النشطة</div>
            <div class="text-2xl font-bold text-green-600 mt-2"><?php echo e($stats['active'] ?? 0); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- قائمة الشارات -->
    <?php if(isset($badges) && $badges->count() > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow text-center">
            <div class="mb-4">
                <?php if($badge->icon): ?>
                <i class="<?php echo e($badge->icon); ?> text-5xl text-sky-600" style="color: <?php echo e($badge->color ?? '#0ea5e9'); ?>"></i>
                <?php else: ?>
                <i class="fas fa-award text-5xl text-sky-600"></i>
                <?php endif; ?>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo e($badge->name); ?></h3>
            <?php if($badge->description): ?>
            <p class="text-sm text-gray-600 mb-4"><?php echo e(Str::limit($badge->description, 60)); ?></p>
            <?php endif; ?>
            <div class="space-y-2">
                <div class="text-sm">
                    <span class="text-gray-600">عدد الحاصلين:</span>
                    <span class="font-medium text-gray-900 mr-1"><?php echo e($badge->users_count ?? 0); ?></span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php echo e($badge->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo e($badge->is_active ? 'نشط' : 'معطل'); ?>

                </span>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="<?php echo e(route('admin.badges.show', $badge)); ?>" class="flex-1 text-center text-sky-600 hover:text-sky-900 text-sm font-medium py-2 px-4 bg-sky-50 rounded-lg">عرض</a>
                <a href="<?php echo e(route('admin.badges.edit', $badge)); ?>" class="flex-1 text-center text-gray-600 hover:text-gray-900 text-sm font-medium py-2 px-4 bg-gray-50 rounded-lg">تعديل</a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-4">
        <?php echo e($badges->links()); ?>

    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-award text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">لا توجد شارات</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/badges/index.blade.php ENDPATH**/ ?>