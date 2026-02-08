

<?php $__env->startSection('title', $offlineCourse->title); ?>
<?php $__env->startSection('header', $offlineCourse->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route('student.offline-courses.index')); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4"><?php echo e($offlineCourse->title); ?></h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المدرب</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($offlineCourse->instructor->name); ?></p>
                </div>
                <?php if($offlineCourse->locationModel): ?>
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المكان</p>
                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-map-marker-alt text-sky-500 ml-1"></i><?php echo e($offlineCourse->locationModel->name); ?></p>
                    <?php if($offlineCourse->locationModel->address): ?>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($offlineCourse->locationModel->address); ?></p>
                    <?php endif; ?>
                </div>
                <?php elseif($offlineCourse->location): ?>
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">الموقع</p>
                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-map-marker-alt text-sky-500 ml-1"></i><?php echo e($offlineCourse->location); ?></p>
                </div>
                <?php endif; ?>
                <?php if($offlineCourse->start_date): ?>
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">تاريخ البدء</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($offlineCourse->start_date->format('Y-m-d')); ?></p>
                </div>
                <?php endif; ?>
                <?php if($enrollment->group): ?>
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المجموعة</p>
                    <p class="text-sm font-semibold text-gray-900"><?php echo e($enrollment->group->name); ?></p>
                </div>
                <?php endif; ?>
                <div class="py-2.5 px-3 bg-sky-50 rounded-lg border border-sky-100">
                    <p class="text-xs font-medium text-gray-500 mb-1">التقدم</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-sky-500 h-2 rounded-full" style="width: <?php echo e(min($enrollment->progress, 100)); ?>%;"></div>
                        </div>
                        <span class="text-sm font-bold text-sky-600"><?php echo e(number_format($enrollment->progress, 0)); ?>%</span>
                    </div>
                </div>
            </div>
            <?php if($offlineCourse->description): ?>
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-2">الوصف</p>
                <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($offlineCourse->description); ?></p>
            </div>
            <?php endif; ?>
            <!-- روابط المحتوى الأوفلاين (منفصلة عن الأونلاين) -->
            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                <a href="<?php echo e(route('student.offline-courses.resources', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-50 text-sky-700 rounded-lg border border-sky-100 font-medium text-sm hover:bg-sky-100">
                    <i class="fas fa-file-alt"></i> الموارد
                </a>
                <a href="<?php echo e(route('student.offline-courses.lectures', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-violet-50 text-violet-700 rounded-lg border border-violet-100 font-medium text-sm hover:bg-violet-100">
                    <i class="fas fa-chalkboard-teacher"></i> المحاضرات
                </a>
            </div>
        </div>
    </div>

    <!-- الأنشطة المطلوبة -->
    <?php if($pendingActivities->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tasks text-amber-500"></i>
                الأنشطة المطلوبة
            </h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $pendingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 mb-1"><?php echo e($activity->title); ?></h3>
                        <?php if($activity->description): ?>
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?php echo e(Str::limit($activity->description, 120)); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                            <span><i class="fas fa-tag text-sky-500 ml-1"></i><?php echo e($activity->type); ?></span>
                            <?php if($activity->due_date): ?>
                                <span><i class="fas fa-calendar text-sky-500 ml-1"></i><?php echo e($activity->due_date->format('Y-m-d')); ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-star text-amber-500 ml-1"></i><?php echo e($activity->max_score); ?> نقطة</span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('student.offline-courses.activities.show', [$offlineCourse, $activity])); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 flex-shrink-0">
                        عرض / تسليم
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- الأنشطة المكتملة -->
    <?php if($completedActivities->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                الأنشطة المكتملة
            </h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $completedActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $submission = $activity->submissions->firstWhere('student_id', auth()->id());
                ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 mb-1"><?php echo e($activity->title); ?></h3>
                        <?php if($submission && $submission->score !== null): ?>
                            <p class="text-sm text-emerald-600 font-semibold">
                                <i class="fas fa-check-circle ml-1"></i>تم التصحيح: <?php echo e($submission->score); ?>/<?php echo e($activity->max_score); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo e(route('student.offline-courses.activities.show', [$offlineCourse, $activity])); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 flex-shrink-0">
                        عرض
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/show.blade.php ENDPATH**/ ?>