<?php $__env->startSection('title', 'المحاضرات'); ?>
<?php $__env->startSection('header', 'المحاضرات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">المحاضرات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">المحاضرات</h1>
                <p class="text-sm text-white/90 mt-1">اختر كورساً لعرض محاضراته وإدارتها (عرض / إضافة / تعديل / حذف)</p>
            </div>
        </div>
    </div>

    <!-- قائمة الكورسات -->
    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.lectures.by-course', $course)); ?>"
                   class="group block bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-indigo-200 overflow-hidden transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-indigo-700 transition-colors line-clamp-2"><?php echo e($course->title); ?></h3>
                                <?php if($course->instructor): ?>
                                    <p class="text-sm text-gray-500 mt-1 truncate"><?php echo e($course->instructor->name); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold">
                                <i class="fas fa-video text-indigo-500"></i>
                                <?php echo e($course->lectures_count ?? 0); ?> محاضرة
                            </span>
                            <span class="text-indigo-600 text-sm font-bold group-hover:underline">
                                فتح
                                <i class="fas fa-arrow-left mr-1"></i>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد كورسات</h3>
            <p class="text-gray-500 mb-6">لا يوجد كورسات نشطة. أضف كورسات من قسم الكورسات أولاً ثم يمكنك إدارة المحاضرات من هنا.</p>
            <a href="<?php echo e(route('admin.advanced-courses.index')); ?>"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-book"></i>
                الكورسات
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/lectures/index.blade.php ENDPATH**/ ?>