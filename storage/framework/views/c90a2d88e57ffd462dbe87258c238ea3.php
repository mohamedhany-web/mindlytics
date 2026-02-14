<?php $__env->startSection('title', 'تفاصيل الدرس'); ?>
<?php $__env->startSection('header', 'تفاصيل الدرس: ' . $lesson->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="hover:text-white">الكورسات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>" class="hover:text-white">دروس <?php echo e(Str::limit($course->title, 25)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate"><?php echo e(Str::limit($lesson->title, 35)); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1 truncate"><?php echo e($lesson->title); ?></h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($course->title); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.courses.lessons.edit', [$course, $lesson])); ?>" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل الدرس
                </a>
                <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    العودة للدروس
                </a>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي + الشريط الجانبي -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2 space-y-6">
            <!-- معلومات أساسية -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                            <?php if($lesson->type == 'video'): ?> bg-blue-100 text-blue-600
                            <?php elseif($lesson->type == 'document'): ?> bg-green-100 text-green-600
                            <?php elseif($lesson->type == 'quiz'): ?> bg-yellow-100 text-yellow-600
                            <?php else: ?> bg-purple-100 text-purple-600
                            <?php endif; ?>">
                            <i class="fas <?php if($lesson->type == 'video'): ?> fa-play <?php elseif($lesson->type == 'document'): ?> fa-file-alt <?php elseif($lesson->type == 'quiz'): ?> fa-question-circle <?php else: ?> fa-tasks <?php endif; ?>"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 truncate"><?php echo e($lesson->title); ?></h3>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e($lesson->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                        <?php if($lesson->is_free): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">مجاني</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">نوع الدرس</div>
                            <div class="text-sm font-semibold text-gray-900">
                                <?php if($lesson->type == 'video'): ?> فيديو
                                <?php elseif($lesson->type == 'document'): ?> مستند
                                <?php elseif($lesson->type == 'quiz'): ?> كويز
                                <?php else: ?> واجب
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">المدة</div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($lesson->duration_minutes ?? 0); ?> دقيقة</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">الترتيب</div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($lesson->order); ?></div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">تاريخ الإنشاء</div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($lesson->created_at->format('Y-m-d')); ?></div>
                        </div>
                    </div>

                    <?php if($lesson->description): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">وصف الدرس</label>
                            <div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100"><?php echo e($lesson->description); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if($lesson->content): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">محتوى الدرس</label>
                            <div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-wrap"><?php echo e($lesson->content); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- محتوى الفيديو -->
            <?php if($lesson->type == 'video' && $lesson->video_url): ?>
                <?php $videoSource = \App\Helpers\VideoHelper::getVideoSource($lesson->video_url); ?>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                        <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-play-circle text-indigo-600"></i>
                            الفيديو
                        </h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            <?php if($videoSource == 'youtube'): ?> bg-red-100 text-red-800
                            <?php elseif($videoSource == 'vimeo'): ?> bg-blue-100 text-blue-800
                            <?php elseif($videoSource == 'google_drive'): ?> bg-yellow-100 text-yellow-800
                            <?php else: ?> bg-gray-100 text-gray-800
                            <?php endif; ?>">
                            <?php if($videoSource == 'youtube'): ?> YouTube
                            <?php elseif($videoSource == 'vimeo'): ?> Vimeo
                            <?php elseif($videoSource == 'google_drive'): ?> Google Drive
                            <?php elseif($videoSource == 'direct'): ?> ملف مباشر
                            <?php else: ?> مصدر آخر
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9;">
                            <?php echo \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%'); ?>

                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500">
                            <span><i class="fas fa-info-circle ml-1"></i> المصدر: <?php echo e($videoSource == 'youtube' ? 'YouTube' : ($videoSource == 'vimeo' ? 'Vimeo' : ($videoSource == 'google_drive' ? 'Google Drive' : ($videoSource == 'direct' ? 'ملف مباشر' : 'مصدر آخر')))); ?></span>
                            <a href="<?php echo e($lesson->video_url); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                <i class="fas fa-external-link-alt ml-1"></i> فتح في نافذة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- المرفقات -->
            <?php if($lesson->attachments): ?>
                <?php $attachments = json_decode($lesson->attachments, true); ?>
                <?php if($attachments && count($attachments) > 0): ?>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-paperclip text-indigo-600"></i>
                                المرفقات
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file text-indigo-600"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 truncate"><?php echo e($attachment['name']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo e(number_format($attachment['size'] / 1024, 2)); ?> KB</div>
                                            </div>
                                        </div>
                                        <a href="<?php echo e($attachment['path']); ?>" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 font-semibold flex-shrink-0">
                                            <i class="fas fa-download"></i> تحميل
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-4">
            <!-- معلومات الكورس -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                <h5 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-indigo-600"></i>
                    معلومات الكورس
                </h5>
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-xs font-medium text-gray-500 mb-0.5">اسم الكورس</div>
                        <div class="text-sm font-semibold text-gray-900 truncate"><?php echo e($course->title); ?></div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-xs font-medium text-gray-500 mb-0.5">المسار / المادة</div>
                        <div class="text-sm font-semibold text-gray-900"><?php echo e($course->academicYear->name ?? '—'); ?> · <?php echo e($course->academicSubject->name ?? '—'); ?></div>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.advanced-courses.show', $course)); ?>" class="mt-4 block text-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    عرض الكورس <i class="fas fa-arrow-left mr-1"></i>
                </a>
            </div>

            <!-- إجراءات سريعة -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                <h5 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-indigo-600"></i>
                    إجراءات سريعة
                </h5>
                <div class="space-y-3">
                    <button type="button" data-toggle-url="<?php echo e(route('admin.courses.lessons.toggle-status', [$course, $lesson])); ?>"
                            onclick="toggleLessonStatus(this)"
                            class="w-full <?php echo e($lesson->is_active ? 'bg-red-50 hover:bg-red-100 text-red-700 border border-red-200' : 'bg-green-50 hover:bg-green-100 text-green-700 border border-green-200'); ?> px-4 py-3 rounded-xl font-semibold transition-colors text-sm">
                        <i class="fas <?php echo e($lesson->is_active ? 'fa-pause' : 'fa-play'); ?> ml-2"></i>
                        <?php echo e($lesson->is_active ? 'إيقاف الدرس' : 'تفعيل الدرس'); ?>

                    </button>
                    <a href="<?php echo e(route('admin.courses.lessons.edit', [$course, $lesson])); ?>" 
                       class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-3 rounded-xl font-semibold transition-colors block text-center text-sm border border-indigo-200">
                        <i class="fas fa-edit ml-2"></i> تعديل الدرس
                    </a>
                    <form action="<?php echo e(route('admin.courses.lessons.destroy', [$course, $lesson])); ?>" method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟ لا يمكن التراجع عن هذا الإجراء.');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 px-4 py-3 rounded-xl font-semibold transition-colors text-sm border border-red-200">
                            <i class="fas fa-trash ml-2"></i> حذف الدرس
                        </button>
                    </form>
                </div>
            </div>

            <!-- تواريخ -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                <h5 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                    التواريخ
                </h5>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">تاريخ الإنشاء</span>
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($lesson->created_at->format('Y-m-d')); ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">آخر تحديث</span>
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($lesson->updated_at->format('Y-m-d')); ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">رقم الدرس</span>
                        <span class="text-sm font-semibold text-gray-900">#<?php echo e($lesson->id); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleLessonStatus(btn) {
    var url = btn.getAttribute('data-toggle-url');
    if (!url) return;
    if (!confirm('هل تريد تغيير حالة هذا الدرس؟')) return;
    btn.disabled = true;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) location.reload();
        else { alert('حدث خطأ في تغيير حالة الدرس'); btn.disabled = false; }
    })
    .catch(function(err) {
        console.error(err);
        alert('حدث خطأ في تغيير حالة الدرس');
        btn.disabled = false;
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/course-lessons/show.blade.php ENDPATH**/ ?>