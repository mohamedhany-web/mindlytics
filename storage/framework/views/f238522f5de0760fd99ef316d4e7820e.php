<?php $__env->startSection('title', 'تعديل الدرس'); ?>
<?php $__env->startSection('header', 'تعديل الدرس: ' . $lesson->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="hover:text-white">الكورسات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>" class="hover:text-white">دروس <?php echo e(Str::limit($course->title, 25)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تعديل الدرس</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل: <?php echo e(Str::limit($lesson->title, 40)); ?></h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($course->title); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.courses.lessons.show', [$course, $lesson])); ?>" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض الدرس
                </a>
                <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-medium transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للدروس
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات الكورس (مختصرة) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-graduation-cap text-indigo-600"></i>
        </div>
        <div class="min-w-0">
            <h3 class="font-semibold text-gray-900 truncate"><?php echo e($course->title); ?></h3>
            <p class="text-sm text-gray-500"><?php echo e($course->academicYear->name ?? '—'); ?> · <?php echo e($course->academicSubject->name ?? '—'); ?></p>
        </div>
    </div>

    <!-- نموذج تعديل الدرس -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-indigo-600"></i>
                تعديل بيانات الدرس
            </h4>
        </div>

        <form action="<?php echo e(route('admin.courses.lessons.update', [$course, $lesson])); ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($e); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- عنوان الدرس -->
                <div class="lg:col-span-8">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        عنوان الدرس <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="<?php echo e(old('title', $lesson->title)); ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="أدخل عنوان الدرس"
                           required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- نوع الدرس + مدة + ترتيب في صف واحد -->
                <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">نوع الدرس <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required onchange="toggleTypeFields()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر النوع</option>
                            <option value="video" <?php echo e(old('type', $lesson->type) == 'video' ? 'selected' : ''); ?>>فيديو</option>
                            <option value="document" <?php echo e(old('type', $lesson->type) == 'document' ? 'selected' : ''); ?>>مستند</option>
                            <option value="quiz" <?php echo e(old('type', $lesson->type) == 'quiz' ? 'selected' : ''); ?>>كويز</option>
                            <option value="assignment" <?php echo e(old('type', $lesson->type) == 'assignment' ? 'selected' : ''); ?>>واجب</option>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">المدة (دقيقة)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="<?php echo e(old('duration_minutes', $lesson->duration_minutes)); ?>" min="1" placeholder="30"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">الترتيب</label>
                        <input type="number" name="order" id="order" value="<?php echo e(old('order', $lesson->order)); ?>" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- خيارات: مجاني / نشط -->
            <div class="mt-6 flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" <?php echo e(old('is_free', $lesson->is_free) ? 'checked' : ''); ?>

                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                    <span class="text-sm font-medium text-gray-700">درس مجاني</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $lesson->is_active) ? 'checked' : ''); ?>

                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                    <span class="text-sm font-medium text-gray-700">درس نشط</span>
                </label>
            </div>

            <!-- وصف ومحتوى الدرس -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">وصف الدرس</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="وصف مختصر عن محتوى الدرس"><?php echo e(old('description', $lesson->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">محتوى الدرس</label>
                    <textarea name="content" id="content" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="محتوى الدرس التفصيلي"><?php echo e(old('content', $lesson->content)); ?></textarea>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- رابط الفيديو (للفيديوهات) -->
            <div id="video_url_field" class="mt-8 p-6 rounded-xl bg-gray-50 border border-gray-200" style="display: none;">
                <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">رابط الفيديو</label>
                <input type="url" name="video_url" id="video_url" value="<?php echo e(old('video_url', $lesson->video_url)); ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       placeholder="https://www.youtube.com/watch?v=... أو رابط فيديو آخر">
                
                <?php if($lesson->video_url): ?>
                    <div class="mt-3 p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">معاينة الفيديو الحالي:</span>
                            <?php
                                $videoSource = \App\Helpers\VideoHelper::getVideoSource($lesson->video_url);
                            ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
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
                        <div class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9; max-height: 200px;">
                            <?php echo \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%'); ?>

                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-2 text-sm text-gray-500">
                    <p class="mb-1"><strong>المصادر المدعومة:</strong></p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>YouTube: https://www.youtube.com/watch?v=VIDEO_ID</li>
                        <li>Vimeo: https://vimeo.com/VIDEO_ID</li>
                        <li>Google Drive: رابط مشاركة الفيديو</li>
                        <li>ملفات فيديو مباشرة: .mp4, .avi, .mov</li>
                    </ul>
                </div>
                <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>



            <!-- المرفقات الحالية -->
            <?php if($lesson->attachments): ?>
                <?php
                    $attachments = json_decode($lesson->attachments, true);
                ?>
                <?php if($attachments && count($attachments) > 0): ?>
                    <div class="mt-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">المرفقات الحالية</label>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <i class="fas fa-file text-primary-600"></i>
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo e($attachment['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo e(number_format($attachment['size'] / 1024, 2)); ?> KB</div>
                                        </div>
                                    </div>
                                    <a href="<?php echo e($attachment['path']); ?>" 
                                       target="_blank"
                                       class="text-primary-600 hover:text-primary-700 font-medium">
                                        <i class="fas fa-download ml-1"></i>
                                        تحميل
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- رفع مرفقات جديدة -->
            <div class="mt-8">
                <label for="attachments" class="block text-sm font-semibold text-gray-700 mb-2">إضافة مرفقات جديدة (اختياري)</label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold">
                <p class="mt-1 text-sm text-gray-500">يمكن رفع عدة ملفات. الحد الأقصى لكل ملف: 10 ميجابايت. سيتم إضافتها للمرفقات الحالية.</p>
                <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- أزرار الحفظ -->
            <div class="flex flex-wrap items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    إلغاء والعودة
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleTypeFields() {
    const type = document.getElementById('type').value;
    const videoUrlField = document.getElementById('video_url_field');
    
    // إخفاء جميع الحقول أولاً
    videoUrlField.style.display = 'none';
    
    // إظهار حقل رابط الفيديو للفيديوهات فقط
    if (type === 'video') {
        videoUrlField.style.display = 'block';
    }
}

// تشغيل الدالة عند تحميل الصفحة للحفاظ على القيم القديمة
document.addEventListener('DOMContentLoaded', function() {
    toggleTypeFields();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\course-lessons\edit.blade.php ENDPATH**/ ?>