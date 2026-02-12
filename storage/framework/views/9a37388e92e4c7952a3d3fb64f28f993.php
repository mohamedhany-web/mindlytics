<?php $__env->startSection('title', 'مسارات التعلم'); ?>
<?php $__env->startSection('header', 'مسارات التعلم'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-sky-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-100/60 via-blue-100/40 to-sky-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-sky-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4 max-w-3xl">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-100 text-sky-700 text-sm font-semibold">
                        <i class="fas fa-route"></i>
                        إدارة مسارات التعلم في Mindlytics
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">
                        أنشئ مسارات تعليمية مترابطة تجمع المهارات، الأطر، واللغات المطلوبة لسوق العمل
                    </h1>
                    <p class="text-gray-600 text-lg">
                        كل مسار يمثل رحلة تعلم كاملة تضم مجموعات مهارية وكورسات تطبيقية. من هنا يمكنك التخطيط للمحتوى، مراقبة جاهزية المسارات، وتنسيق الفرق المسؤولة عن إنتاج الدروس.
                    </p>
                </div>
                <a href="<?php echo e(route('admin.academic-years.create')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 text-white hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 transition-all duration-300 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 hover:-translate-y-0.5 w-full sm:w-auto">
                    <i class="fas fa-plus"></i>
                    إنشاء مسار تعلم جديد
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4 mt-8">
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-sky-200/50 hover:border-sky-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-sky-800/80 mb-2">إجمالي المسارات</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e($summary['total_tracks']); ?></p>
                </div>
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-emerald-800/80 mb-2">مسارات نشطة</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e($summary['active_tracks']); ?></p>
                </div>
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-indigo-800/80 mb-2">مجموعات مهارات</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-indigo-700 via-purple-600 to-violet-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e($summary['skill_clusters']); ?></p>
                </div>
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-purple-800/80 mb-2">كورسات مرتبطة</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-purple-700 via-pink-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e($summary['courses']); ?></p>
                </div>
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-blue-800/80 mb-2">اللغات المغطاة</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(($summary['languages'] ?? collect())->count()); ?></p>
                </div>
                <div class="dashboard-card rounded-2xl p-5 card-hover-effect relative overflow-hidden group border-2 border-amber-200/50 hover:border-amber-300/70 shadow-lg hover:shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 100%);">
                    <p class="text-xs sm:text-sm font-bold text-amber-800/80 mb-2">أطر العمل</p>
                    <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-amber-700 via-yellow-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(($summary['frameworks'] ?? collect())->count()); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if($tracks->count() > 0): ?>
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $metrics = $track->track_metrics ?? [];
                    $languages = collect($metrics['languages'] ?? []);
                    $frameworks = collect($metrics['frameworks'] ?? []);
                    $levels = collect($metrics['levels'] ?? []);
                    $previewCourses = $track->preview_courses ?? collect();
                ?>
                <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-lg">
                                    <i class="fas fa-compass text-lg"></i>
                                </span>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900"><?php echo e($track->name); ?></h2>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest"><?php echo e($track->code); ?></p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 max-w-xl">
                                <?php echo e($track->description ? Str::limit($track->description, 160) : 'مسار تعلم متكامل يضم مجموعات مهارية متعددة مع كورسات تطبيقية متدرجة المستوى.'); ?>

                            </p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                    <i class="fas fa-layer-group text-[10px]"></i>
                                    <?php echo e($track->academic_subjects_count); ?> مجموعة مهارية
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">
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
                        <div class="flex flex-col sm:items-end sm:text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo e($track->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                <?php echo e($track->is_active ? 'نشط' : 'مغلق'); ?>

                            </span>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:px-6 space-y-4">
                        <?php if($languages->isNotEmpty() || $frameworks->isNotEmpty() || $levels->isNotEmpty()): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 border border-slate-100 rounded-xl p-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">اللغات</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                <?php echo e($language); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">أطر العمل</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $frameworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-slate-600 border border-slate-200">
                                                <?php echo e($framework); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">المستويات</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__empty_1 = true; $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-700 capitalize">
                                                <?php echo e(__($level)); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($previewCourses->isNotEmpty()): ?>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">كورسات حديثة في المسار</p>
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
                            </div>
                        <?php endif; ?>

                        <!-- أزرار الإجراءات -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route('admin.academic-years.edit', $track)); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700 text-xs font-semibold">
                                    <i class="fas fa-pen"></i>
                                    تعديل المسار
                                </a>
                                <a href="<?php echo e(route('admin.academic-subjects.index', ['track' => $track->id])); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 text-xs font-semibold">
                                    <i class="fas fa-layer-group"></i>
                                    إدارة المجموعات
                                </a>
                                <form method="POST" action="<?php echo e(route('admin.academic-years.toggle-status', $track)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold">
                                        <i class="fas fa-power-off"></i>
                                        <?php echo e($track->is_active ? 'إيقاف مؤقت' : 'تفعيل المسار'); ?>

                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.academic-years.destroy', $track)); ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المسار التعليمي؟ سيتم حذف جميع البيانات المرتبطة به. هذا الإجراء لا يمكن التراجع عنه!');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 text-xs font-semibold">
                                        <i class="fas fa-trash"></i>
                                        حذف المسار
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 shadow-xl p-12 text-center space-y-4" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="flex items-center justify-center">
                <span class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-route"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">لا توجد مسارات تعلم بعد</h3>
            <p class="text-gray-500 max-w-xl mx-auto">
                قم بإنشاء أول مسار تعلم لتجميع الكورسات داخل رحلة تعليمية واضحة. ابدأ بتحديد الهدف التقني، مجموعات المهارات، والمهارات المطلوبة.
            </p>
            <a href="<?php echo e(route('admin.academic-years.create')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 text-white hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 transition-all duration-300 font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 hover:-translate-y-0.5">
                <i class="fas fa-plus"></i>
                إنشاء مسار جديد
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/academic-years/index.blade.php ENDPATH**/ ?>