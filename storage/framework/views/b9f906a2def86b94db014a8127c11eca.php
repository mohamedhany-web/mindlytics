

<?php $__env->startSection('title', 'إضافة واجب جديد'); ?>
<?php $__env->startSection('header', 'إضافة واجب جديد'); ?>

<?php
    $selectedCourse = $selectedCourse ?? null;
    $backUrl = $selectedCourse ? route('admin.assignments.by-course', $selectedCourse) : route('admin.assignments.index');
?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            <p class="font-semibold mb-1">يرجى تصحيح الأخطاء:</p>
            <ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.assignments.index')); ?>" class="hover:text-white">الواجبات</a>
                    <?php if($selectedCourse): ?>
                        <?php $course = $courses->firstWhere('id', $selectedCourse); ?>
                        <?php if($course): ?>
                            <span class="mx-2">/</span>
                            <a href="<?php echo e(route('admin.assignments.by-course', $course)); ?>" class="hover:text-white"><?php echo e(Str::limit($course->title, 25)); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <span class="mx-2">/</span>
                    <span class="text-white">إضافة واجب</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إضافة واجب جديد</h1>
                <p class="text-sm text-white/90 mt-1">إنشاء واجب وربطه بكورس ودرس (اختياري)</p>
            </div>
            <a href="<?php echo e($backUrl); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                <i class="fas fa-arrow-right"></i>
                <?php echo e($selectedCourse ? 'رجوع لواجبات الكورس' : 'العودة للواجبات'); ?>

            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات الواجب</h4>
        </div>

        <form action="<?php echo e(route('admin.assignments.store')); ?>" method="POST" class="p-6 sm:p-8">
            <?php echo csrf_field(); ?>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="advanced_course_id" id="advanced_course_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر الكورس</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id', $selectedCourse) == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 50)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['advanced_course_id'];
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
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الدرس (اختياري)</label>
                        <select name="lesson_id" id="lesson_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">بدون درس محدد</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">يتم تعبئة الدروس حسب الكورس المختار</p>
                        <?php $__errorArgs = ['lesson_id'];
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
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان الواجب <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="مثال: واجب البرمجة الكائنية">
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

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="وصف مختصر عن الواجب"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
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

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التعليمات</label>
                    <textarea name="instructions" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="تعليمات للطلاب"><?php echo e(old('instructions')); ?></textarea>
                    <?php $__errorArgs = ['instructions'];
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الاستحقاق</label>
                        <input type="datetime-local" name="due_date" value="<?php echo e(old('due_date')); ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <?php $__errorArgs = ['due_date'];
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
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الدرجة الكلية <span class="text-red-500">*</span></label>
                        <input type="number" name="max_score" value="<?php echo e(old('max_score', 100)); ?>" min="1" max="1000" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <?php $__errorArgs = ['max_score'];
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
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1"
                           <?php echo e(old('allow_late_submission') ? 'checked' : ''); ?>

                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="allow_late_submission" class="text-sm font-medium text-gray-700">السماح بالتسليم المتأخر</label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="draft" <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="published" <?php echo e(old('status') == 'published' ? 'selected' : ''); ?>>منشور</option>
                        <option value="archived" <?php echo e(old('status') == 'archived' ? 'selected' : ''); ?>>مؤرشف</option>
                    </select>
                    <?php $__errorArgs = ['status'];
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
            </div>

            <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    إنشاء الواجب
                </button>
                <a href="<?php echo e($backUrl); ?>" class="inline-flex items-center gap-2 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    const lessonSelect = document.getElementById('lesson_id');
    if (!courseSelect || !lessonSelect) return;

    function clearLessonsExceptFirst() {
        while (lessonSelect.options.length > 1) {
            lessonSelect.remove(1);
        }
    }

    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        clearLessonsExceptFirst();
        if (!courseId) return;

        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'جاري التحميل...';
        opt.disabled = true;
        lessonSelect.appendChild(opt);
        lessonSelect.disabled = true;

        fetch('<?php echo e(url("/admin/courses")); ?>/' + courseId + '/lessons-list', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                opt.remove();
                const lessons = Array.isArray(data) ? data : (data.lessons || data.data || []);
                lessons.forEach(function(lesson) {
                    const o = document.createElement('option');
                    o.value = lesson.id;
                    o.textContent = lesson.title || ('درس ' + (lesson.order || ''));
                    lessonSelect.appendChild(o);
                });
                lessonSelect.disabled = false;
            })
            .catch(function() {
                opt.remove();
                const err = document.createElement('option');
                err.value = '';
                err.textContent = 'حدث خطأ';
                err.disabled = true;
                lessonSelect.appendChild(err);
                lessonSelect.disabled = false;
            });
    });

    <?php if(old('advanced_course_id', $selectedCourse)): ?>
        courseSelect.dispatchEvent(new Event('change'));
        setTimeout(function() {
            lessonSelect.value = '<?php echo e(old("lesson_id")); ?>';
        }, 500);
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/assignments/create.blade.php ENDPATH**/ ?>