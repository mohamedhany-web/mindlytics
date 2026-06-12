<?php $__env->startSection('title', 'مجموعات المهارات'); ?>
<?php $__env->startSection('header', 'مجموعات المهارات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <?php if($currentTrack ?? null): ?>
                        <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="hover:text-white">المسارات</a>
                        <span class="mx-2">/</span>
                        <span class="text-white truncate"><?php echo e(Str::limit($currentTrack->name ?? '', 25)); ?></span>
                        <span class="mx-2">/</span>
                    <?php endif; ?>
                    <span class="text-white">مجموعات المهارات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">مجموعات المهارات</h1>
                <p class="text-sm text-white/90 mt-1">
                    إدارة المجموعات المهارية ضمن مسارات التعلم وربطها بالكورسات
                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <?php if($currentTrack ?? null): ?>
                    <a href="<?php echo e(route('admin.academic-years.index')); ?>" 
                       class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                        <i class="fas fa-arrow-right"></i>
                        الرجوع لمسار <?php echo e(Str::limit($currentTrack->name, 20)); ?>

                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.academic-subjects.create', $currentTrack ? ['track' => $currentTrack->id] : [])); ?>" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة مجموعة مهارية
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-medium text-gray-500 mb-1">إجمالي المجموعات</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($summary['total_clusters']); ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-medium text-gray-500 mb-1">المجموعات النشطة</p>
            <p class="text-2xl font-bold text-emerald-600"><?php echo e($summary['active_clusters']); ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-medium text-gray-500 mb-1">كورسات مرتبطة</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($summary['courses']); ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-medium text-gray-500 mb-1">اللغات</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(($summary['languages'] ?? collect())->count()); ?></p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-lg">
            <p class="text-xs font-medium text-gray-500 mb-1">أطر العمل</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(($summary['frameworks'] ?? collect())->count()); ?></p>
        </div>
    </div>

    <?php if($clusters->count() > 0): ?>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 2xl:grid-cols-3">
            <?php $__currentLoopData = $clusters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cluster): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $metrics = $cluster->cluster_metrics ?? [];
                    $languages = collect($metrics['languages'] ?? []);
                    $frameworks = collect($metrics['frameworks'] ?? []);
                    $levels = collect($metrics['levels'] ?? []);
                    $previewCourses = $cluster->preview_courses ?? collect();
                    $track = $cluster->academicYear;
                ?>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full overflow-hidden">
                    <div class="px-5 py-6 flex flex-col gap-5 flex-1">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl text-white shadow-lg"
                                          style="background: linear-gradient(135deg, <?php echo e($cluster->color ?? '#0ea5e9'); ?> 0%, <?php echo e($cluster->color ?? '#0ea5e9'); ?> 100%);">
                                        <i class="<?php echo e($cluster->icon ?? 'fas fa-layer-group'); ?> text-lg"></i>
                                    </span>
                                    <div class="space-y-1">
                                        <h2 class="text-lg font-bold text-gray-900"><?php echo e($cluster->name); ?></h2>
                                        <p class="text-xs text-gray-500 uppercase tracking-widest"><?php echo e($cluster->code); ?></p>
                                        <?php if($track): ?>
                                            <p class="text-xs text-sky-600 font-semibold">
                                                جزء من مسار <?php echo e($track->name); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($cluster->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                    <?php echo e($cluster->is_active ? 'نشطة' : 'معلقة'); ?>

                                </span>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <?php echo e($cluster->description ? Str::limit($cluster->description, 200) : 'مجموعة مهارات تركز على إتقان أدوات ولغات محددة مع كورسات تطبيقية متدرجة.'); ?>

                            </p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                    <i class="fas fa-graduation-cap text-[10px]"></i>
                                    <?php echo e($metrics['courses_count'] ?? 0); ?> كورس متخصص
                                </span>
                                <?php if(!empty($metrics['avg_duration'])): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        مدة متوسطة <?php echo e($metrics['avg_duration']); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($languages->isNotEmpty() || $frameworks->isNotEmpty() || $levels->isNotEmpty()): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 border border-slate-100 rounded-xl p-4">
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">اللغات الأساسية</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                <?php echo e($language); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">لم يتم تحديد لغات</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">أطر العمل</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $frameworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                <?php echo e($framework); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">لم يتم تحديد أطر</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">المستويات المستهدفة</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 capitalize">
                                                <?php echo e(__($level)); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">لم يتم تحديد مستويات</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">كورسات حديثة ضمن المجموعة</p>
                            <?php if($previewCourses->isNotEmpty()): ?>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $previewCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between gap-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="w-2 h-2 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600"></span>
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
                            <?php else: ?>
                                <p class="text-xs text-gray-400">لم يتم ربط كورسات بعد بهذه المجموعة.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50/80">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <a href="<?php echo e(route('admin.academic-subjects.edit', $cluster)); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-semibold transition-colors">
                                <i class="fas fa-pen"></i>
                                تعديل
                            </a>
                            <a href="<?php echo e(route('admin.advanced-courses.index', ['cluster' => $cluster->id])); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-100 text-sky-700 hover:bg-sky-200 text-sm font-semibold transition-colors border border-sky-200">
                                <i class="fas fa-graduation-cap"></i>
                                الكورسات
                            </a>
                            <form method="POST" action="<?php echo e(route('admin.academic-subjects.toggle-status', $cluster)); ?>" class="inline-flex">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl <?php echo e($cluster->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200'); ?> text-sm font-semibold transition-colors">
                                    <i class="fas fa-power-off"></i>
                                    <?php echo e($cluster->is_active ? 'إيقاف مؤقت' : 'تفعيل'); ?>

                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.academic-subjects.destroy', $cluster)); ?>" class="inline-flex" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم فقدان أي ربط يدوي للكورسات مع هذا الاسم.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-sm font-semibold transition-colors">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-layer-group"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد مجموعات مهارية بعد</h3>
            <p class="text-gray-500 max-w-lg mx-auto mb-6">
                أنشئ أول مجموعة مهارات لتقسيم المسار التعليمي إلى وحدات متخصصة. اختر اسمًا، رمزًا، وحدد المهارات المستهدفة.
            </p>
            <a href="<?php echo e(route('admin.academic-subjects.create', ($currentTrack ?? null) ? ['track' => $currentTrack->id] : [])); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة مجموعة مهارية
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/academic-subjects/index.blade.php ENDPATH**/ ?>