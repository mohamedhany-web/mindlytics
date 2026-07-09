

<?php $__env->startSection('title', 'إدارة المسار - ' . $learningPath->name); ?>
<?php $__env->startSection('header', 'إدارة المسار - ' . $learningPath->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .course-item {
        transition: all 0.3s ease;
    }
    
    .course-item:hover {
        transform: translateX(-5px);
    }
    
    .sortable-ghost {
        opacity: 0.4;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-50 via-blue-50 to-green-50 rounded-2xl p-6 border-2 border-green-200 shadow-lg">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <?php if($learningPath->icon): ?>
                        <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-blue-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="<?php echo e($learningPath->icon); ?> text-3xl"></i>
                        </div>
                    <?php else: ?>
                        <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-blue-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-route text-3xl"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900"><?php echo e($learningPath->name); ?></h1>
                        <?php if($learningPath->code): ?>
                            <p class="text-sm text-gray-600 mt-1">الرمز: <?php echo e($learningPath->code); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($learningPath->description): ?>
                    <p class="text-gray-700 leading-relaxed"><?php echo e($learningPath->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Path Description Editor -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-edit text-blue-600"></i>
            وصف المسار التعليمي
        </h2>
        <form action="#" method="POST" id="descriptionForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <textarea name="description" rows="6" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="اكتب وصفاً شاملاً للمسار التعليمي يوضح الهدف منه ومحتواه..."><?php echo e($learningPath->description); ?></textarea>
            <div class="mt-4 flex justify-end">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    حفظ الوصف
                </button>
            </div>
        </form>
    </div>

    <!-- Courses Management -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-blue-600"></i>
                ترتيب الكورسات في المسار
            </h2>
            <span class="text-sm text-gray-600"><?php echo e($learningPath->courses_count); ?> كورس</span>
        </div>

        <div class="p-6">
            <?php if($learningPath->courses->count() > 0): ?>
                <div class="space-y-3" id="coursesList">
                    <?php $__currentLoopData = $learningPath->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="course-item bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-4 border-2 border-blue-200 hover:border-blue-300 transition-all cursor-move" data-course-id="<?php echo e($course->id); ?>">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                    <?php echo e($index + 1); ?>

                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-black text-gray-900 mb-1"><?php echo e($course->title); ?></h3>
                                <?php if($course->academicSubject): ?>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-book mr-1"></i>
                                        <?php echo e($course->academicSubject->name); ?>

                                    </p>
                                <?php endif; ?>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                    <?php if($course->lessons_count > 0): ?>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-play-circle text-blue-600"></i>
                                            <?php echo e($course->lessons_count); ?> درس
                                        </span>
                                    <?php endif; ?>
                                    <?php if($course->instructor): ?>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-user-tie text-green-600"></i>
                                            <?php echo e($course->instructor->name); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="button" 
                                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors"
                                        title="ترتيب الكورس">
                                    <i class="fas fa-grip-vertical text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600">لا توجد كورسات في هذا المسار حالياً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Path Structure -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-sitemap text-green-600"></i>
            هيكل المسار التعليمي
        </h2>
        
        <?php if($learningPath->academic_subjects->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $learningPath->academic_subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-all">
                    <h3 class="text-lg font-black text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-600"></i>
                        <?php echo e($subject->name); ?>

                    </h3>
                    <?php if($subject->advancedCourses && $subject->advancedCourses->count() > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <?php $__currentLoopData = $subject->advancedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 text-sm mb-1"><?php echo e($course->title); ?></h4>
                                        <?php if($course->lessons_count > 0): ?>
                                            <p class="text-xs text-gray-600">
                                                <i class="fas fa-play-circle mr-1"></i>
                                                <?php echo e($course->lessons_count); ?> درس
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">لا توجد كورسات في هذه المادة</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-600">لا توجد مواد دراسية في هذا المسار</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const coursesList = document.getElementById('coursesList');
        if (coursesList) {
            new Sortable(coursesList, {
                animation: 150,
                handle: '.fa-grip-vertical',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // هنا يمكن إرسال ترتيب جديد للخادم
                    console.log('New order:', evt.newIndex);
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\learning-path\show.blade.php ENDPATH**/ ?>