

<?php $__env->startSection('title', 'برامج الولاء'); ?>
<?php $__env->startSection('header', 'برامج الولاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">برامج الولاء</h1>
                <p class="text-gray-600 mt-1">إدارة برامج نقاط الولاء</p>
            </div>
            <button onclick="document.getElementById('createProgramModal').classList.remove('hidden')" 
               class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-plus mr-2"></i>
                إضافة برنامج جديد
            </button>
        </div>
    </div>

    <!-- الإحصائيات -->
    <?php if(isset($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">إجمالي البرامج</div>
            <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['total'] ?? 0); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">النشطة</div>
            <div class="text-2xl font-bold text-green-600 mt-2"><?php echo e($stats['active'] ?? 0); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- قائمة البرامج -->
    <?php if(isset($programs) && $programs->count() > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900"><?php echo e($program->name); ?></h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php echo e($program->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo e($program->is_active ? 'نشط' : 'معطل'); ?>

                </span>
            </div>
            <?php if($program->description): ?>
            <p class="text-sm text-gray-600 mb-4"><?php echo e(Str::limit($program->description, 100)); ?></p>
            <?php endif; ?>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">نقاط لكل شراء:</span>
                    <span class="font-medium text-gray-900"><?php echo e($program->points_per_purchase ?? 0); ?></span>
                </div>
                <?php if($program->points_per_referral): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">نقاط لكل إحالة:</span>
                    <span class="font-medium text-gray-900"><?php echo e($program->points_per_referral); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">عدد المستخدمين:</span>
                    <span class="font-medium text-gray-900"><?php echo e($program->users_count ?? 0); ?></span>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?php echo e(route('admin.loyalty.show', $program)); ?>" class="text-sky-600 hover:text-sky-900 text-sm font-medium">عرض التفاصيل</a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-star text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">لا توجد برامج ولاء</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/loyalty/index.blade.php ENDPATH**/ ?>