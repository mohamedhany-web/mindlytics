<?php $__env->startSection('title', $offlineCourse->title); ?>
<?php $__env->startSection('header', $offlineCourse->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.index')); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <?php
        $resourcesCount = $offlineCourse->resources()
            ->active()
            ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
                $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            }))
            ->count();

        $lecturesCount = $offlineCourse->offlineLectures()
            ->active()
            ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
                $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            }))
            ->count();

        $activitiesCount = $offlineCourse->activities()
            ->where('status', 'published')
            ->when($enrollment->group_id, fn ($q) => $q->where(function ($x) use ($enrollment) {
                $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            }))
            ->count();

        $examsCount = \App\Models\AdvancedExam::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('is_active', true)
            ->where('is_published', true)
            ->count();
    ?>

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
            <?php if(filled($offlineCourse->description)): ?>
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-2">نبذة عن الكورس</p>
                <p class="text-sm text-gray-700 leading-relaxed"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->description, 280)); ?></p>
                <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.curriculum', $offlineCourse)); ?>" class="inline-flex items-center gap-1.5 mt-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
                    عرض التوصيف الكامل والمنهج
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            </div>
            <?php endif; ?>
            <!-- روابط المحتوى الأوفلاين -->
            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.curriculum', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 text-slate-800 rounded-lg border border-slate-200 font-medium text-sm hover:bg-slate-100">
                    <i class="fas fa-sitemap"></i> المنهج والتوصيف
                </a>
                <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.schedule', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-50 text-indigo-800 rounded-lg border border-indigo-100 font-medium text-sm hover:bg-indigo-100">
                    <i class="fas fa-calendar-alt"></i> التقويم
                </a>
                <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.resources', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-50 text-sky-700 rounded-lg border border-sky-100 font-medium text-sm hover:bg-sky-100">
                    <i class="fas fa-file-alt"></i> الموارد
                </a>
                <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.lectures', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-violet-50 text-violet-700 rounded-lg border border-violet-100 font-medium text-sm hover:bg-violet-100">
                    <i class="fas fa-chalkboard-teacher"></i> المحاضرات
                </a>
                <a href="<?php echo e(route('student.exams.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-100 font-medium text-sm hover:bg-emerald-100">
                    <i class="fas fa-clipboard-check"></i> الاختبارات
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.curriculum', $offlineCourse)); ?>" class="group bg-white rounded-xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 inline-flex items-center justify-center">
                    <i class="fas fa-sitemap"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-50 text-slate-600 font-semibold">منهج</span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">المنهج والتوصيف</p>
            <p class="text-xs text-gray-500 mt-1">وصف الكورس، نبذة المدرب، وهيكل المحتوى</p>
        </a>

        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.schedule', $offlineCourse)); ?>" class="group bg-white rounded-xl border border-indigo-100 shadow-sm p-4 hover:shadow-md hover:border-indigo-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 inline-flex items-center justify-center">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">جدول</span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">تقويم الجلسات</p>
            <p class="text-xs text-gray-500 mt-1">كل الجلسات والمواعيد والاختبارات المجدولة</p>
        </a>

        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.resources', $offlineCourse)); ?>" class="group bg-white rounded-xl border border-sky-100 shadow-sm p-4 hover:shadow-md hover:border-sky-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-sky-100 text-sky-600 inline-flex items-center justify-center">
                    <i class="fas fa-file-alt"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-sky-50 text-sky-700 font-semibold"><?php echo e($resourcesCount); ?></span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">الموارد</p>
            <p class="text-xs text-gray-500 mt-1">فتح وتحميل الملفات التعليمية</p>
        </a>

        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.lectures', $offlineCourse)); ?>" class="group bg-white rounded-xl border border-violet-100 shadow-sm p-4 hover:shadow-md hover:border-violet-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-violet-100 text-violet-600 inline-flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-violet-50 text-violet-700 font-semibold"><?php echo e($lecturesCount); ?></span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">المحاضرات</p>
            <p class="text-xs text-gray-500 mt-1">الدروس المتاحة لمجموعتك</p>
        </a>

        <a href="#activities-required" class="group bg-white rounded-xl border border-amber-100 shadow-sm p-4 hover:shadow-md hover:border-amber-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 inline-flex items-center justify-center">
                    <i class="fas fa-tasks"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700 font-semibold"><?php echo e($activitiesCount); ?></span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">الأنشطة</p>
            <p class="text-xs text-gray-500 mt-1">المطلوب تسليمه ومتابعته</p>
        </a>

        <a href="<?php echo e(route('student.exams.index')); ?>" class="group bg-white rounded-xl border border-emerald-100 shadow-sm p-4 hover:shadow-md hover:border-emerald-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 inline-flex items-center justify-center">
                    <i class="fas fa-clipboard-check"></i>
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold"><?php echo e($examsCount); ?></span>
            </div>
            <p class="mt-3 text-sm font-bold text-gray-900">الاختبارات</p>
            <p class="text-xs text-gray-500 mt-1">الدخول والبدء من واجهة الطالب</p>
        </a>
    </div>

    <?php if($enrollment->group): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 inline-flex items-center justify-center shrink-0">
                    <i class="fas fa-calendar-check text-lg"></i>
                </span>
                <div>
                    <h2 class="text-base font-bold text-gray-900">الجلسات والمواعيد</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        مجموعة <span class="font-semibold text-gray-700"><?php echo e($enrollment->group->name); ?></span>
                        <?php if(($enrollment->group->sessions_count ?? 0) > 0): ?>
                            — <?php echo e($enrollment->group->sessions_count); ?> جلسة مسجّلة
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.schedule', $offlineCourse)); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-sm shrink-0">
                <i class="fas fa-calendar-alt"></i>
                فتح التقويم الكامل
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- حالة الدفع -->
    <?php if((float)$enrollment->total_amount > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-green-500"></i>
                حالة الدفع
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="py-3 px-4 rounded-xl border <?php echo e($enrollment->payment_status === 'paid' ? 'bg-green-50 border-green-200' : ($enrollment->payment_status === 'partial' ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200')); ?>">
                    <p class="text-xs font-medium text-gray-500 mb-1">حالة الدفع</p>
                    <?php
                        $pTexts = ['paid' => 'مكتمل', 'partial' => 'جزئي', 'unpaid' => 'لم يتم الدفع'];
                        $pIcons = ['paid' => 'fa-check-circle text-green-600', 'partial' => 'fa-exclamation-circle text-amber-600', 'unpaid' => 'fa-times-circle text-red-600'];
                    ?>
                    <p class="text-sm font-bold">
                        <i class="fas <?php echo e($pIcons[$enrollment->payment_status] ?? ''); ?> ml-1"></i>
                        <?php echo e($pTexts[$enrollment->payment_status] ?? '—'); ?>

                    </p>
                </div>
                <div class="py-3 px-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-1">المبلغ المدفوع</p>
                    <p class="text-sm font-bold text-gray-900"><?php echo e(number_format($enrollment->paid_amount, 2)); ?> ج.م</p>
                </div>
                <div class="py-3 px-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 mb-1">المبلغ المتبقي</p>
                    <p class="text-sm font-bold <?php echo e((float)$enrollment->remaining_amount > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                        <?php echo e(number_format($enrollment->remaining_amount, 2)); ?> ج.م
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- الأنشطة المطلوبة -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="activities-required">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tasks text-amber-500"></i>
                الأنشطة المطلوبة
            </h2>
            <?php if($pendingActivities->count() > 0): ?>
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
                        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.show', [$offlineCourse, $activity])); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 flex-shrink-0">
                            عرض / تسليم
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-600">
                    لا توجد أنشطة مطلوبة حالياً.
                </div>
            <?php endif; ?>
        </div>
    </div>

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
                    <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.show', [$offlineCourse, $activity])); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 flex-shrink-0">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\offline-courses\show.blade.php ENDPATH**/ ?>