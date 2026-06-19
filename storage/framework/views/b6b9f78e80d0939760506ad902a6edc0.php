

<?php $__env->startSection('title', 'تفاصيل المحاضرة'); ?>
<?php $__env->startSection('header', 'تفاصيل المحاضرة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusMap = [
        'scheduled' => ['label' => 'مجدولة', 'class' => 'bg-amber-100 text-amber-800'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-blue-100 text-blue-800'],
        'completed' => ['label' => 'مكتملة', 'class' => 'bg-green-100 text-green-800'],
        'cancelled' => ['label' => 'ملغاة', 'class' => 'bg-red-100 text-red-800'],
    ];
    $status = $statusMap[$lecture->status ?? 'scheduled'] ?? $statusMap['scheduled'];
    $platformLabels = [
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'google_drive' => 'Google Drive',
        'direct' => 'رابط مباشر',
        'bunny' => 'Bunny.net',
    ];
    $platformLabel = $platformLabels[strtolower($lecture->video_platform ?? '')] ?? $lecture->video_platform ?? '—';
?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.lectures.index')); ?>" class="hover:text-white">المحاضرات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.lectures.by-course', $lecture->course_id)); ?>" class="hover:text-white"><?php echo e(Str::limit($lecture->course->title ?? '', 30)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تفاصيل المحاضرة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1"><?php echo e($lecture->title); ?></h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($lecture->course->title ?? ''); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.lectures.edit', $lecture)); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.lectures.by-course', $lecture->course_id)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لمحاضرات الكورس
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- العمود الرئيسي -->
        <div class="lg:col-span-2 space-y-6">
            <!-- المعلومات الأساسية -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-indigo-600"></i>
                        المعلومات الأساسية
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الكورس</p>
                            <p class="text-gray-900 font-medium"><?php echo e($lecture->course->title ?? '—'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">المحاضر</p>
                            <p class="text-gray-900 font-medium"><?php echo e($lecture->instructor->name ?? '—'); ?></p>
                        </div>
                        <?php if($lecture->lesson): ?>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الدرس المرتبط (اختياري)</p>
                            <p class="text-gray-900 font-medium"><?php echo e($lecture->lesson->title); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($lecture->description): ?>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الوصف</p>
                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($lecture->description); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- رابط التسجيل / الفيديو -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-video text-indigo-600"></i>
                        رابط تسجيل المحاضرة
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">منصة الفيديو</p>
                            <p class="text-gray-900 font-medium"><?php echo e($platformLabel); ?></p>
                        </div>
                        <?php if($lecture->recording_url): ?>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الرابط</p>
                            <a href="<?php echo e($lecture->recording_url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 font-medium break-all inline-flex items-center gap-1">
                                <?php echo e(Str::limit($lecture->recording_url, 60)); ?>

                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">لم يُضف رابط تسجيل</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- التاريخ والوقت والمدة -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                        التاريخ والوقت
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">تاريخ ووقت المحاضرة</p>
                            <p class="text-gray-900 font-medium"><?php echo e($lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d H:i') : '—'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">المدة (دقيقة)</p>
                            <p class="text-gray-900 font-medium"><?php echo e($lecture->duration_minutes ?? '—'); ?> دقيقة</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الحالة</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($status['class']); ?>">
                                <?php echo e($status['label']); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مواد المحاضرة -->
            <?php if($lecture->materials->isNotEmpty()): ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-paperclip text-indigo-600"></i>
                        مواد المحاضرة
                    </h2>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <?php $__currentLoopData = $lecture->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900"><?php echo e($material->title ?: $material->file_name); ?></p>
                                <p class="text-sm text-gray-500 mt-0.5"><?php echo e($material->file_name); ?></p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <?php if($material->is_visible_to_student): ?>
                                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-lg">ظاهر للطالب</span>
                                <?php else: ?>
                                    <span class="text-xs font-semibold text-gray-600 bg-gray-200 px-2 py-1 rounded-lg">مخفي</span>
                                <?php endif; ?>
                                <a href="<?php echo e(asset('storage/' . $material->file_path)); ?>" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- الملاحظات -->
            <?php if($lecture->notes): ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-indigo-600"></i>
                        ملاحظات
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($lecture->notes); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- خيارات المحاضرة -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-indigo-600"></i>
                        الخيارات
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl <?php echo e($lecture->has_attendance_tracking ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'); ?>">
                        <i class="fas <?php echo e($lecture->has_attendance_tracking ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400'); ?>"></i>
                        <span class="font-medium <?php echo e($lecture->has_attendance_tracking ? 'text-green-800' : 'text-gray-600'); ?>">تتبع الحضور</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl <?php echo e($lecture->has_assignment ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200'); ?>">
                        <i class="fas <?php echo e($lecture->has_assignment ? 'fa-check-circle text-blue-600' : 'fa-times-circle text-gray-400'); ?>"></i>
                        <span class="font-medium <?php echo e($lecture->has_assignment ? 'text-blue-800' : 'text-gray-600'); ?>">يوجد واجب</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl <?php echo e($lecture->has_evaluation ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200'); ?>">
                        <i class="fas <?php echo e($lecture->has_evaluation ? 'fa-check-circle text-amber-600' : 'fa-times-circle text-gray-400'); ?>"></i>
                        <span class="font-medium <?php echo e($lecture->has_evaluation ? 'text-amber-800' : 'text-gray-600'); ?>">يوجد تقييم</span>
                    </div>
                </div>
            </div>

            <?php if($lecture->has_attendance_tracking): ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">الحضور</h2>
                </div>
                <div class="p-6">
                    <a href="<?php echo e(route('admin.attendance.lecture', $lecture)); ?>" class="block w-full text-center py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors">
                        عرض تفاصيل الحضور
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- واجبات المحاضرة -->
    <?php if($lecture->has_assignment && $lecture->assignments->isNotEmpty()): ?>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tasks text-indigo-600"></i>
                واجبات المحاضرة
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $lecture->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <h4 class="font-semibold text-gray-900"><?php echo e($assignment->title); ?></h4>
                    <p class="text-sm text-gray-600 mt-1">
                        تاريخ التسليم: <?php echo e($assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—'); ?>

                    </p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\lectures\show.blade.php ENDPATH**/ ?>