<?php $__env->startSection('title', $lesson->title . ' - دروس ' . $course->title); ?>
<?php $__env->startSection('header', $lesson->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2 flex-wrap">
                    <a href="<?php echo e(route('instructor.courses.index')); ?>" class="hover:text-sky-600 transition-colors">الكورسات</a>
                    <span>/</span>
                    <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="hover:text-sky-600 transition-colors truncate max-w-[150px]"><?php echo e($course->title); ?></a>
                    <span>/</span>
                    <a href="<?php echo e(route('instructor.courses.lessons.index', $course->id)); ?>" class="hover:text-sky-600 transition-colors">الدروس</a>
                    <span>/</span>
                    <span class="text-slate-700 font-medium truncate max-w-[200px]"><?php echo e($lesson->title); ?></span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800 truncate"><?php echo e($lesson->title); ?></h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('instructor.courses.lessons.edit', [$course->id, $lesson->id])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('instructor.courses.lessons.index', $course->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    رجوع للدروس
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl p-5 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4">تفاصيل الدرس</h3>
                <?php if($lesson->description): ?>
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-slate-700"><?php echo e($lesson->description); ?></p>
                    </div>
                <?php endif; ?>
                <?php if($lesson->content): ?>
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-slate-500 mb-1">المحتوى</p>
                        <div class="text-slate-700 prose prose-slate max-w-none">
                            <?php echo nl2br(e($lesson->content)); ?>

                        </div>
                    </div>
                <?php endif; ?>
                <?php if($lesson->type === 'video' && $lesson->video_url): ?>
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-2">رابط الفيديو</p>
                        <a href="<?php echo e($lesson->video_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-700 font-medium">
                            <i class="fas fa-external-link-alt"></i>
                            <?php echo e($lesson->video_url); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <div class="rounded-xl p-5 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4">معلومات الدرس</h3>
                <ul class="space-y-3">
                    <li class="flex items-center justify-between gap-2 py-2 border-b border-slate-100">
                        <span class="text-slate-500 text-sm">النوع</span>
                        <?php if($lesson->type === 'video'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-video"></i> فيديو
                            </span>
                        <?php elseif($lesson->type === 'text'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-sky-100 text-sky-700">
                                <i class="fas fa-file-alt"></i> نص
                            </span>
                        <?php elseif($lesson->type === 'document'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-700">
                                <i class="fas fa-file-pdf"></i> ملف
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-violet-100 text-violet-700">
                                <i class="fas fa-question-circle"></i> اختبار
                            </span>
                        <?php endif; ?>
                    </li>
                    <li class="flex items-center justify-between gap-2 py-2 border-b border-slate-100">
                        <span class="text-slate-500 text-sm">الترتيب</span>
                        <span class="font-semibold text-slate-800"><?php echo e($lesson->order); ?></span>
                    </li>
                    <?php if($lesson->duration_minutes): ?>
                    <li class="flex items-center justify-between gap-2 py-2 border-b border-slate-100">
                        <span class="text-slate-500 text-sm">المدة</span>
                        <span class="font-semibold text-slate-800"><?php echo e($lesson->duration_minutes); ?> دقيقة</span>
                    </li>
                    <?php endif; ?>
                    <li class="flex items-center justify-between gap-2 py-2 border-b border-slate-100">
                        <span class="text-slate-500 text-sm">الحالة</span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-medium <?php echo e($lesson->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                            <?php echo e($lesson->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </li>
                    <li class="flex items-center justify-between gap-2 py-2">
                        <span class="text-slate-500 text-sm">مجاني</span>
                        <span class="font-semibold <?php echo e($lesson->is_free ? 'text-emerald-600' : 'text-slate-600'); ?>"><?php echo e($lesson->is_free ? 'نعم' : 'لا'); ?></span>
                    </li>
                </ul>
            </div>

            <?php
                $attachments = is_string($lesson->attachments) ? json_decode($lesson->attachments, true) : $lesson->attachments;
                $attachments = is_array($attachments) ? $attachments : [];
            ?>
            <?php if(count($attachments) > 0): ?>
            <div class="rounded-xl p-5 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4">المرفقات</h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $path = $att['path'] ?? null; $attUrl = $path ? (str_starts_with($path, 'http') ? $path : url('storage/' . $path)) : '#'; ?>
                        <li>
                            <a href="<?php echo e($attUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-sky-600 hover:text-sky-700">
                                <i class="fas fa-paperclip"></i>
                                <?php echo e($att['name'] ?? 'ملف'); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/lessons/show.blade.php ENDPATH**/ ?>