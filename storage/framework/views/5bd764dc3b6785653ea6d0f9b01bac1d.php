<?php $__env->startSection('title', 'تفاصيل الكورس الأوفلاين - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'تفاصيل الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600 transition-colors">كورساتي الأوفلاين</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 font-semibold truncate block sm:inline"><?php echo e($offlineCourse->title); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mb-3"><?php echo e($offlineCourse->title); ?></h1>
                <div class="flex flex-wrap items-center gap-2">
                    <?php
                        $statusClass = match($offlineCourse->status ?? '') {
                            'active' => 'bg-emerald-100 text-emerald-700',
                            'completed' => 'bg-violet-100 text-violet-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        $statusLabel = match($offlineCourse->status ?? '') {
                            'active' => 'نشط',
                            'completed' => 'منتهي',
                            default => 'مسودة',
                        };
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusClass); ?>">
                        <i class="fas <?php echo e($offlineCourse->status === 'active' ? 'fa-check-circle' : ($offlineCourse->status === 'completed' ? 'fa-flag-checkered' : 'fa-pen')); ?>"></i>
                        <?php echo e($statusLabel); ?>

                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800">
                        <i class="fas fa-map-marker-alt"></i> أوفلاين
                    </span>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl p-4 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-user-graduate text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_students'] ?? 0); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1">إجمالي الطلاب</div>
        </div>
        <div class="rounded-xl p-4 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-user-check text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['active_students'] ?? 0); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1">طلاب نشطين</div>
        </div>
        <div class="rounded-xl p-4 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_groups'] ?? 0); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1">مجموعة</div>
        </div>
        <div class="rounded-xl p-4 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-tasks text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_activities'] ?? 0); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1">نشاط</div>
        </div>
    </div>

    <!-- إدارة المحتوى الأوفلاين: موارد، محاضرات، أنشطة -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-folder-open text-amber-500"></i>
                إدارة محتوى الكورس الأوفلاين
            </h3>
            <p class="text-sm text-slate-600 mb-4">إضافة الموارد والمحاضرات والواجبات/الاختبارات للطلاب (واجهات منفصلة عن الكورسات الأونلاين).</p>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('instructor.offline-courses.resources.index', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 font-semibold transition-colors">
                    <i class="fas fa-file-alt"></i>
                    <span>الموارد</span>
                    <span class="text-sky-500">(<?php echo e($offlineCourse->resources()->count()); ?>)</span>
                </a>
                <a href="<?php echo e(route('instructor.offline-courses.lectures.index', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-violet-50 text-violet-700 hover:bg-violet-100 border border-violet-200 font-semibold transition-colors">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>المحاضرات</span>
                    <span class="text-violet-500">(<?php echo e($offlineCourse->offlineLectures()->count()); ?>)</span>
                </a>
                <a href="<?php echo e(route('instructor.offline-courses.activities.index', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 font-semibold transition-colors">
                    <i class="fas fa-tasks"></i>
                    <span>الواجبات والاختبارات</span>
                    <span class="text-amber-500">(<?php echo e($offlineCourse->activities()->count()); ?>)</span>
                </a>
                <a href="<?php echo e(route('instructor.exams.index', ['offline_course_id' => $offlineCourse->id])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-semibold transition-colors">
                    <i class="fas fa-clipboard-check"></i>
                    <span>امتحانات الأكاديمية</span>
                    <span class="text-emerald-500">(<?php echo e($offlineCourse->exams()->count()); ?>)</span>
                </a>
                <a href="<?php echo e(route('instructor.exams.create', ['course_type' => 'offline', 'offline_course_id' => $offlineCourse->id])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-violet-500 hover:bg-violet-600 text-white font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>إنشاء اختبار (نظام الأكاديمية)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-amber-500"></i>
                معلومات الكورس
            </h3>
        </div>
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">العنوان</label>
                    <div class="font-bold text-slate-800"><?php echo e($offlineCourse->title); ?></div>
                </div>
                <?php if($offlineCourse->description): ?>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">الوصف</label>
                        <div class="text-slate-700 whitespace-pre-line"><?php echo e($offlineCourse->description); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->locationModel || $offlineCourse->location): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">المكان</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->locationModel->name ?? $offlineCourse->location ?? '—'); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->start_date): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">تاريخ البداية</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->start_date->format('Y-m-d')); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->end_date): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">تاريخ النهاية</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->end_date->format('Y-m-d')); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->duration_hours): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">المدة (ساعات)</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->duration_hours); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->sessions_count): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">عدد الجلسات</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->sessions_count); ?></div>
                    </div>
                <?php endif; ?>
                <?php if(isset($offlineCourse->price) && $offlineCourse->price > 0): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">السعر</label>
                        <div class="text-slate-800 font-semibold"><?php echo e(number_format($offlineCourse->price, 2)); ?> ج.م</div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->max_students): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">الحد الأقصى للطلاب</label>
                        <div class="text-slate-800 font-medium"><?php echo e($offlineCourse->current_students ?? 0); ?> / <?php echo e($offlineCourse->max_students); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($offlineCourse->notes): ?>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide">ملاحظات</label>
                        <div class="text-slate-700 whitespace-pre-line"><?php echo e($offlineCourse->notes); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/show.blade.php ENDPATH**/ ?>