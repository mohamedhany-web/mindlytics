<?php $__env->startSection('title', 'موارد الكورس - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'موارد الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse)); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-sky-500"></i>
                        موارد الكورس (أوفلاين) — <?php echo e($offlineCourse->title); ?>

                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        اعرض الموارد حسب <span class="font-semibold text-gray-700">المحاضرات</span>، مع قسم للموارد العامة.
                    </p>
                </div>
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="text"
                            name="q"
                            value="<?php echo e($search ?? ''); ?>"
                            placeholder="ابحث بالعنوان أو الوصف أو اسم الملف..."
                            class="w-full sm:w-80 pr-9 pl-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm"
                        />
                    </div>
                    <select name="per_page" class="w-full sm:w-auto px-3 py-2 rounded-lg border border-gray-200 text-sm">
                        <?php $pp = (int) ($perPage ?? 10); ?>
                        <option value="5" <?php echo e($pp === 5 ? 'selected' : ''); ?>>5</option>
                        <option value="10" <?php echo e($pp === 10 ? 'selected' : ''); ?>>10</option>
                        <option value="15" <?php echo e($pp === 15 ? 'selected' : ''); ?>>15</option>
                        <option value="25" <?php echo e($pp === 25 ? 'selected' : ''); ?>>25</option>
                    </select>
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                    <?php if(!empty($search)): ?>
                        <a href="<?php echo e(url()->current()); ?>?per_page=<?php echo e($pp); ?>" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200">
                            مسح
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php
            $hasGeneral = isset($generalResources) && $generalResources && $generalResources->isNotEmpty();
            $hasLectures = isset($lectures) && $lectures && $lectures->count() > 0;
        ?>

        <?php if(! $hasGeneral && ! $hasLectures): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p>لا توجد موارد متاحة حالياً.</p>
            </div>
        <?php else: ?>
            <?php if($hasGeneral): ?>
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/40">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-slate-500"></i>
                        موارد عامة
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">موارد غير مرتبطة بمحاضرة محددة.</p>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <?php $__currentLoopData = $generalResources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50/40 transition-colors">
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-gray-900 leading-snug break-words"><?php echo e($resource->title); ?></h3>
                                    <?php if($resource->description): ?>
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed"><?php echo e(Str::limit($resource->description, 180)); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <?php if($resource->type === 'link' && $resource->url): ?>
                                        <a href="<?php echo e($resource->url); ?>" target="_blank" rel="noopener"
                                           class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">
                                            <i class="fas fa-external-link-alt"></i>
                                            فتح الرابط
                                        </a>
                                    <?php else: ?>
                                        <?php $files = $resource->getAllFiles(); ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(offline_course_resource_file_url($file)); ?>"
                                                   download="<?php echo e($file['name'] ?? 'download'); ?>"
                                                   class="group inline-flex items-center gap-2 w-full max-w-full px-3 py-2 rounded-xl bg-sky-50 text-sky-700 text-sm font-semibold hover:bg-sky-100 border border-sky-100">
                                                    <i class="fas fa-download flex-shrink-0"></i>
                                                    <span class="truncate min-w-0"><?php echo e($file['name'] ?? 'تحميل'); ?></span>
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($hasLectures): ?>
                <div class="p-4 sm:p-5 border-t border-gray-100 bg-white">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-500"></i>
                        المحاضرات
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">اختر محاضرة لتحميل مواردها.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $dateLabel = optional($lec->groupSession)->session_date
                                ? \Carbon\Carbon::parse($lec->groupSession->session_date)->format('Y-m-d')
                                : ($lec->scheduled_at ? $lec->scheduled_at->format('Y-m-d') : null);
                            $groupLabel = optional(optional($lec->groupSession)->group)->name ?? optional($lec->group)->name;
                            $resourcesForLecture = $lec->resources ?? collect();
                        ?>
                        <details class="group">
                            <summary class="cursor-pointer list-none p-4 sm:p-5 hover:bg-gray-50/50 flex items-center justify-between gap-3 select-none">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900"><?php echo e($lec->title); ?></span>
                                        <?php if($dateLabel): ?>
                                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700"><?php echo e($dateLabel); ?></span>
                                        <?php endif; ?>
                                        <?php if($groupLabel): ?>
                                            <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700"><?php echo e($groupLabel); ?></span>
                                        <?php endif; ?>
                                        <span class="text-xs px-2 py-1 rounded-full bg-sky-50 text-sky-700">
                                            <?php echo e($resourcesForLecture->count()); ?> مورد
                                        </span>
                                    </div>
                                    <?php if($lec->description): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e(Str::limit($lec->description, 140)); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
                                </div>
                            </summary>
                            <div class="px-4 sm:px-5 pb-5">
                                <?php if($resourcesForLecture->isEmpty()): ?>
                                    <div class="text-sm text-gray-500 py-3">لا توجد موارد مرتبطة بهذه المحاضرة.</div>
                                <?php else: ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                        <?php $__currentLoopData = $resourcesForLecture; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50/40 transition-colors">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-gray-900 leading-snug break-words"><?php echo e($resource->title); ?></div>
                                                    <?php if($resource->description): ?>
                                                        <div class="text-sm text-gray-600 mt-1 leading-relaxed"><?php echo e(Str::limit($resource->description, 180)); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mt-3">
                                                    <?php if($resource->type === 'link' && $resource->url): ?>
                                                        <a href="<?php echo e($resource->url); ?>" target="_blank" rel="noopener"
                                                           class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">
                                                            <i class="fas fa-external-link-alt"></i>
                                                            فتح الرابط
                                                        </a>
                                                    <?php else: ?>
                                                        <?php $files = $resource->getAllFiles(); ?>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <a href="<?php echo e(offline_course_resource_file_url($file)); ?>"
                                                                   download="<?php echo e($file['name'] ?? 'download'); ?>"
                                                                   class="group inline-flex items-center gap-2 w-full max-w-full px-3 py-2 rounded-xl bg-sky-50 text-sky-700 text-sm font-semibold hover:bg-sky-100 border border-sky-100">
                                                                    <i class="fas fa-download flex-shrink-0"></i>
                                                                    <span class="truncate min-w-0"><?php echo e($file['name'] ?? 'تحميل'); ?></span>
                                                                </a>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="p-4 sm:p-5 border-t border-gray-100">
                    <?php echo e($lectures->links()); ?>

                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\offline-courses\resources.blade.php ENDPATH**/ ?>