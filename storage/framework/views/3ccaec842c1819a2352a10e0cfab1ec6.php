<?php $__env->startSection('title', 'محاضرات الكورس - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'محاضرات الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route('student.offline-courses.show', $offlineCourse)); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-violet-500"></i>
                محاضرات الكورس (أوفلاين) — <?php echo e($offlineCourse->title); ?>

            </h1>
        </div>
        <?php if($lectures->isEmpty()): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-chalkboard-teacher text-4xl mb-3 opacity-50"></i>
                <p>لا توجد محاضرات متاحة حالياً.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-100">
                <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-4 sm:p-5 hover:bg-gray-50/50">
                        <h3 class="font-semibold text-gray-900 mb-2"><?php echo e($lecture->title); ?></h3>
                        <?php if($lecture->description): ?>
                            <p class="text-sm text-gray-600 mb-3"><?php echo e(Str::limit($lecture->description, 200)); ?></p>
                        <?php endif; ?>
                        <?php if($lecture->scheduled_at): ?>
                            <p class="text-xs text-gray-500 mb-2"><i class="fas fa-calendar ml-1"></i><?php echo e($lecture->scheduled_at->format('Y-m-d H:i')); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php if($lecture->recording_url): ?>
                                <a href="<?php echo e($lecture->recording_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-100 text-violet-700 rounded-lg text-sm font-medium hover:bg-violet-200">
                                    <i class="fas fa-play"></i> تسجيل المحاضرة
                                </a>
                            <?php endif; ?>
                            <?php if($lecture->download_links && count($lecture->download_links) > 0): ?>
                                <?php $__currentLoopData = $lecture->download_links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($link['url'] ?? '#'); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                        <i class="fas fa-download"></i> <?php echo e($link['label'] ?? 'تحميل'); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php if($lecture->attachments && count($lecture->attachments) > 0): ?>
                                <?php $__currentLoopData = $lecture->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(asset('storage/' . ($att['path'] ?? ''))); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                                        <i class="fas fa-file"></i> <?php echo e($att['name'] ?? 'ملف'); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/lectures.blade.php ENDPATH**/ ?>