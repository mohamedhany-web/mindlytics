

<?php $__env->startSection('title', $academicYear->name . ' - المجموعات المهارية'); ?>
<?php $__env->startSection('header', 'مجموعات المهارات المتخصصة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <div class="bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4 max-w-3xl">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-100 text-sky-700 text-sm font-semibold">
                        <i class="fas fa-route"></i>
                        مسار <?php echo e($academicYear->name); ?>

                    </span>
                    <h1 class="text-3xl font-black text-gray-900">
                        اختر المجموعة المهارية التي تناسب هدفك داخل هذا المسار
                    </h1>
                    <p class="text-gray-600 text-lg">
                        قمنا بإعادة تنظيم المواد إلى مجموعات مهارية تركز على التقنيات والمهام العملية المطلوبة في سوق العمل. كل مجموعة تحتوي على كورسات مترابطة تساعدك على بناء مشروع متكامل أو احتراف تقنية محددة.
                    </p>
                </div>
                <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-700 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمسارات
                </a>
            </div>
        </div>
    </div>

    <?php if($subjects->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $stats = $subject->catalog_stats ?? [];
                    $coursesCount = $stats['courses_count'] ?? 0;
                    $languages = collect($stats['languages'] ?? []);
                    $frameworks = collect($stats['frameworks'] ?? []);
                    $levels = collect($stats['levels'] ?? []);
                    $previewCourses = $subject->preview_courses ?? collect();
                    $colorPalette = [
                        ['from' => 'from-sky-500', 'to' => 'to-indigo-600', 'icon' => 'fa-terminal'],
                        ['from' => 'from-emerald-500', 'to' => 'to-teal-600', 'icon' => 'fa-database'],
                        ['from' => 'from-purple-500', 'to' => 'to-pink-600', 'icon' => 'fa-brain'],
                        ['from' => 'from-amber-500', 'to' => 'to-orange-600', 'icon' => 'fa-robot'],
                        ['from' => 'from-rose-500', 'to' => 'to-fuchsia-600', 'icon' => 'fa-layer-group'],
                    ];
                    $palette = $colorPalette[$index % count($colorPalette)];
                ?>
                <a href="<?php echo e(route('subjects.courses', $subject)); ?>" class="group relative bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 bg-gradient-to-br <?php echo e($palette['from']); ?> <?php echo e($palette['to']); ?>/10 pointer-events-none"></div>
                    <div class="relative p-6 space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br <?php echo e($palette['from']); ?> <?php echo e($palette['to']); ?> text-white shadow-lg">
                                    <i class="fas <?php echo e($palette['icon']); ?> text-lg"></i>
                                </span>
                                <div class="space-y-2">
                                    <h2 class="text-lg font-bold text-gray-900"><?php echo e($subject->name); ?></h2>
                                    <p class="text-sm text-gray-500">
                                        <?php echo e($subject->description ? Str::limit($subject->description, 110) : 'مجموعة تركز على إتقان مهارات محددة مع مشاريع تطبيقية عملية واختبارات تقييمية.'); ?>

                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600">
                                    تصفح المجموعة
                                    <i class="fas fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                <i class="fas fa-graduation-cap text-[10px]"></i>
                                <?php echo e($coursesCount); ?> كورس متخصص
                            </span>
                            <?php if($languages->isNotEmpty()): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">
                                    <i class="fas fa-code text-[10px]"></i>
                                    <?php echo e($languages->take(2)->implode(' • ')); ?><?php if($languages->count() > 2): ?> +<?php echo e($languages->count() - 2); ?><?php endif; ?>
                                </span>
                            <?php endif; ?>
                            <?php if($frameworks->isNotEmpty()): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                    <i class="fas fa-cubes text-[10px]"></i>
                                    <?php echo e($frameworks->take(2)->implode(' • ')); ?><?php if($frameworks->count() > 2): ?> +<?php echo e($frameworks->count() - 2); ?><?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if($languages->isNotEmpty() || $frameworks->isNotEmpty() || $levels->isNotEmpty()): ?>
                            <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 space-y-3">
                                <?php if($languages->isNotEmpty()): ?>
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">اللغات</span>
                                        <div class="flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                    <?php echo e($language); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($frameworks->isNotEmpty()): ?>
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">الأطر</span>
                                        <div class="flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $frameworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                    <?php echo e($framework); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($levels->isNotEmpty()): ?>
                                    <div class="flex items-start gap-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">المستويات</span>
                                        <div class="flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 capitalize">
                                                    <?php echo e(__($level)); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($previewCourses->isNotEmpty()): ?>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">كورسات مميزة في هذه المجموعة</p>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $previewCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between gap-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="w-2 h-2 rounded-full bg-gradient-to-br <?php echo e($palette['from']); ?> <?php echo e($palette['to']); ?>"></span>
                                                <span class="truncate"><?php echo e($course->title); ?></span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                                <?php if($course->programming_language): ?>
                                                    <span><i class="fas fa-code ml-1"></i><?php echo e($course->programming_language); ?></span>
                                                <?php endif; ?>
                                                <?php if($course->level): ?>
                                                    <span><i class="fas fa-signal ml-1"></i><?php echo e($course->level); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="bg-white border border-gray-100 rounded-3xl shadow-xl p-12 text-center space-y-4">
            <div class="flex items-center justify-center">
                <span class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-layer-group"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">لم يتم إعداد مجموعات مهارية بعد</h3>
            <p class="text-gray-500 max-w-xl mx-auto">
                لم يتم ربط هذا المسار بالكورسات البرمجية حتى الآن. تواصل مع فريق المنصة لإضافة مجموعات مهارات وتخصيص الكورسات المناسبة.
            </p>
            <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للمسارات
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>










<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\academic-years\subjects.blade.php ENDPATH**/ ?>