<?php $__env->startSection('title', 'موارد الكورس - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'موارد الكورس الأوفلاين'); ?>

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
                <i class="fas fa-file-alt text-sky-500"></i>
                موارد الكورس (أوفلاين) — <?php echo e($offlineCourse->title); ?>

            </h1>
        </div>
        <?php if($resources->isEmpty()): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p>لا توجد موارد متاحة حالياً.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-100">
                <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-4 sm:p-5 hover:bg-gray-50/50">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900"><?php echo e($resource->title); ?></h3>
                                <?php if($resource->description): ?>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo e(Str::limit($resource->description, 150)); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex-shrink-0 flex flex-wrap gap-2 justify-end">
                                <?php if($resource->type === 'link' && $resource->url): ?>
                                    <a href="<?php echo e($resource->url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">
                                        <i class="fas fa-external-link-alt"></i>
                                        فتح الرابط
                                    </a>
                                <?php else: ?>
                                    <?php $__currentLoopData = $resource->getAllFiles(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(asset('storage/' . $file['path'])); ?>" download="<?php echo e($file['name'] ?? 'download'); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                            <i class="fas fa-download"></i>
                                            <?php echo e(Str::limit($file['name'] ?? 'تحميل', 25)); ?>

                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/resources.blade.php ENDPATH**/ ?>