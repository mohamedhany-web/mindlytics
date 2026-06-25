<?php $__env->startSection('title', 'تعديل: ' . $lesson->title); ?>
<?php $__env->startSection('header', 'تعديل الدرس'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2 flex-wrap">
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="hover:text-sky-600 transition-colors">الكورسات</a>
            <span>/</span>
            <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="hover:text-sky-600 transition-colors truncate max-w-[150px]"><?php echo e($course->title); ?></a>
            <span>/</span>
            <a href="<?php echo e(route('instructor.courses.lessons.index', $course->id)); ?>" class="hover:text-sky-600 transition-colors">الدروس</a>
            <span>/</span>
            <span class="text-slate-700 font-medium">تعديل</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800">تعديل الدرس</h1>
    </div>

    <?php if($errors->any()): ?>
    <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800">
        <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle ml-2"></i> يرجى تصحيح الأخطاء:</p>
        <ul class="list-disc list-inside text-sm">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="rounded-xl p-6 bg-white border border-slate-200 shadow-sm">
        <form action="<?php echo e(route('instructor.courses.lessons.update', [$course->id, $lesson->id])); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الدرس <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="<?php echo e(old('title', $lesson->title)); ?>" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"
                               placeholder="عنوان الدرس">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الدرس <span class="text-red-500">*</span></label>
                        <select name="type" id="lessonType" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                            <option value="video" <?php echo e(old('type', $lesson->type) == 'video' ? 'selected' : ''); ?>>فيديو</option>
                            <option value="text" <?php echo e(old('type', $lesson->type) == 'text' ? 'selected' : ''); ?>>نص</option>
                            <option value="document" <?php echo e(old('type', $lesson->type) == 'document' ? 'selected' : ''); ?>>ملف</option>
                            <option value="quiz" <?php echo e(old('type', $lesson->type) == 'quiz' ? 'selected' : ''); ?>>اختبار</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"
                              placeholder="وصف مختصر"><?php echo e(old('description', $lesson->description)); ?></textarea>
                </div>

                <div class="video-section <?php echo e(old('type', $lesson->type) != 'video' ? 'hidden' : ''); ?> rounded-xl p-4 bg-slate-50 border border-slate-200">
                    <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2"><i class="fas fa-video text-red-500"></i> إعدادات الفيديو</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">رابط الفيديو</label>
                            <input type="url" name="video_url" value="<?php echo e(old('video_url', $lesson->video_url)); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 text-slate-800"
                                   placeholder="https://...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">أو رفع ملف فيديو (حتى 500MB)</label>
                            <input type="file" name="video_file" accept="video/*"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800">
                        </div>
                    </div>
                </div>

                <div class="text-section <?php echo e(old('type', $lesson->type) != 'text' ? 'hidden' : ''); ?>">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">محتوى الدرس</label>
                    <textarea name="content" rows="8" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"
                              placeholder="محتوى الدرس..."><?php echo e(old('content', $lesson->content)); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">مدة الدرس (دقيقة)</label>
                        <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', $lesson->duration_minutes)); ?>" min="0"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">الترتيب <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="<?php echo e(old('order', $lesson->order)); ?>" min="0" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">مرفقات إضافية</label>
                    <input type="file" name="attachments[]" multiple
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800">
                    <p class="text-xs text-slate-500 mt-1">حتى 10MB لكل ملف. المرفقات الحالية تبقى مضافاً عليها الجديد.</p>
                </div>

                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 space-y-3">
                    <h4 class="font-semibold text-slate-800">خيارات الدرس</h4>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $lesson->is_active) ? 'checked' : ''); ?>

                               class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-700">الدرس نشط</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_free" value="1" <?php echo e(old('is_free', $lesson->is_free) ? 'checked' : ''); ?>

                               class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-700">درس مجاني (معاينة)</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-200">
                <a href="<?php echo e(route('instructor.courses.lessons.index', $course->id)); ?>"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lessonType = document.getElementById('lessonType');
    const videoSection = document.querySelector('.video-section');
    const textSection = document.querySelector('.text-section');

    function updateSections() {
        const type = lessonType.value;
        videoSection.classList.add('hidden');
        textSection.classList.add('hidden');
        if (type === 'video') videoSection.classList.remove('hidden');
        else if (type === 'text') textSection.classList.remove('hidden');
    }

    lessonType.addEventListener('change', updateSections);
    updateSections();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/lessons/edit.blade.php ENDPATH**/ ?>