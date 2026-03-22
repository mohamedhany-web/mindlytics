

<?php $__env->startSection('title', 'المسار التعليمي - ' . $learningPath->name); ?>
<?php $__env->startSection('header', 'المسار التعليمي - ' . $learningPath->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* فيديو المقدمة (iframe Bunny / YouTube — نسبة 16:9) */
    .intro-video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        height: 0;
        background: #000;
        border-radius: 1rem;
        overflow: hidden;
    }
    .intro-video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-50 via-green-50 to-blue-50 rounded-2xl p-6 border-2 border-blue-200 shadow-lg">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <?php if($learningPath->icon): ?>
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="<?php echo e($learningPath->icon); ?> text-3xl"></i>
                        </div>
                    <?php else: ?>
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
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
            <div class="flex-shrink-0">
                <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg text-center min-w-[150px]">
                    <div class="text-4xl font-black text-blue-600 mb-2"><?php echo e($learningPath->progress); ?>%</div>
                    <div class="text-sm text-gray-600 font-semibold">التقدم</div>
                    <div class="mt-3 w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-600 to-green-500 h-3 rounded-full transition-all duration-500" style="width: <?php echo e($learningPath->progress); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">إجمالي الكورسات</p>
                    <p class="text-3xl font-black text-blue-600"><?php echo e($learningPath->courses_count); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">الكورسات المسجلة</p>
                    <p class="text-3xl font-black text-green-600"><?php echo e($learningPath->enrolled_courses_count); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">المتبقي</p>
                    <p class="text-3xl font-black text-yellow-600"><?php echo e($learningPath->courses_count - $learningPath->enrolled_courses_count); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">تاريخ التسجيل</p>
                    <p class="text-lg font-black text-purple-600"><?php echo e($enrollment->enrolled_at->format('Y-m-d')); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Introduction (YouTube / Vimeo / Bunny مباشر أو embed) -->
    <?php if($learningPath->video_url): ?>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-play-circle text-blue-600"></i>
            <?php echo e(__('public.path_intro_video_title')); ?>

        </h2>
        <?php echo $__env->make('partials.intro-video-embed', [
            'url' => trim((string) $learningPath->video_url),
            'title' => __('public.path_intro_video_title'),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php endif; ?>

    <!-- Courses List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-blue-600"></i>
                الكورسات في المسار
            </h2>
        </div>

        <div class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $learningPath->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-6 hover:bg-gray-50 transition-all <?php echo e($course->is_enrolled ? 'bg-green-50/50' : ''); ?>">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                <?php echo e($index + 1); ?>

                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-gray-900 mb-1"><?php echo e($course->title); ?></h3>
                                <?php if($course->academicSubject): ?>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-book mr-1"></i>
                                        <?php echo e($course->academicSubject->name); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if($course->is_enrolled): ?>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    مسجل
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($course->description): ?>
                            <p class="text-gray-600 mb-3 line-clamp-2"><?php echo e(Str::limit($course->description, 150)); ?></p>
                        <?php endif; ?>

                        <div class="flex items-center gap-4 flex-wrap text-sm text-gray-600">
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
                            <?php if(($course->price ?? 0) > 0): ?>
                                <span class="flex items-center gap-1 font-bold text-blue-600">
                                    <i class="fas fa-tag"></i>
                                    <?php echo e(number_format($course->price, 0)); ?> ج.م
                                </span>
                            <?php else: ?>
                                <span class="flex items-center gap-1 font-bold text-green-600">
                                    <i class="fas fa-gift"></i>
                                    مجاني
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <?php if($course->is_enrolled): ?>
                            <a href="<?php echo e(route('my-courses.show', $course->id)); ?>" 
                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-play mr-1"></i>
                                ابدأ التعلم
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('courses.show', $course->id)); ?>" 
                               class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                عرض الكورس
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600">لا توجد كورسات في هذا المسار حالياً</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\learning-path\show.blade.php ENDPATH**/ ?>