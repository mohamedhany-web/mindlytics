<?php $__env->startSection('title', 'موارد الكورس الأوفلاين - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'موارد الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', $offlineCourse)); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الموارد</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-sky-500"></i>
                موارد الكورس (أوفلاين)
            </h1>
            <a href="<?php echo e(route('instructor.offline-courses.resources.create', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700 transition-colors">
                <i class="fas fa-plus"></i>
                إضافة مورد
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <?php if($resources->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p>لا توجد موارد بعد. أضف ملفات أو روابط للطلاب.</p>
                <a href="<?php echo e(route('instructor.offline-courses.resources.create', $offlineCourse)); ?>" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">إضافة مورد</a>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-slate-800"><?php echo e($resource->title); ?></span>
                                <?php if($resource->group_id): ?>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600"><?php echo e($resource->group->name ?? ''); ?></span>
                                <?php endif; ?>
                                <?php if(!$resource->is_active): ?>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-200 text-slate-600">معطل</span>
                                <?php endif; ?>
                            </div>
                            <?php if($resource->description): ?>
                                <p class="text-sm text-slate-600 mt-1 line-clamp-2"><?php echo e(Str::limit($resource->description, 120)); ?></p>
                            <?php endif; ?>
                            <p class="text-xs text-slate-500 mt-1">
                                <?php if($resource->type === 'link'): ?>
                                    <i class="fas fa-link ml-1"></i> رابط
                                <?php else: ?>
                                    <?php $files = $resource->getAllFiles(); ?>
                                    <i class="fas fa-file ml-1"></i>
                                    <?php if(count($files) > 1): ?>
                                        <?php echo e(count($files)); ?> ملفات
                                    <?php else: ?>
                                        <?php echo e($resource->file_name ?? (count($files) ? ($files[0]['name'] ?? 'ملف') : 'ملف')); ?>

                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <?php if($resource->type === 'link' && $resource->url): ?>
                                <a href="<?php echo e($resource->url); ?>" target="_blank" rel="noopener" class="px-3 py-1.5 text-sm bg-sky-100 text-sky-700 rounded-lg hover:bg-sky-200">فتح الرابط</a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('instructor.offline-courses.resources.edit', [$offlineCourse, $resource])); ?>" class="px-3 py-1.5 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">تعديل</a>
                            <form action="<?php echo e(route('instructor.offline-courses.resources.destroy', [$offlineCourse, $resource])); ?>" method="post" class="inline" onsubmit="return confirm('حذف هذا المورد؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100">حذف</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/resources/index.blade.php ENDPATH**/ ?>