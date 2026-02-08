<?php $__env->startSection('title', 'تعديل الاختبار'); ?>
<?php $__env->startSection('header', 'تعديل الاختبار: ' . $exam->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">تعديل الاختبار</h1>
                <p class="text-sm text-slate-500 mt-0.5">تعديل معلومات الاختبار والإعدادات</p>
            </div>
            <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <form action="<?php echo e(route('instructor.exams.update', $exam)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">معلومات الاختبار</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">عنوان الاختبار <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title', $exam->title)); ?>" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                                   placeholder="مثال: اختبار الوحدة الأولى">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">نوع الكورس</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="course_type" value="online" <?php echo e(old('course_type', $exam->offline_course_id ? 'offline' : 'online') !== 'offline' ? 'checked' : ''); ?> class="course-type-radio rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                        <span>أونلاين</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="course_type" value="offline" <?php echo e(old('course_type', $exam->offline_course_id ? 'offline' : 'online') === 'offline' ? 'checked' : ''); ?> class="course-type-radio rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                        <span>أوفلاين</span>
                                    </label>
                                </div>
                            </div>
                            <div id="edit-online-course-wrap" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="advanced_course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس (أونلاين)</label>
                                    <select name="advanced_course_id" id="advanced_course_id" onchange="loadLessons()"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                        <option value="">اختر الكورس</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div>
                                    <label for="course_lesson_id" class="block text-sm font-semibold text-slate-700 mb-1">الدرس (اختياري)</label>
                                    <select name="course_lesson_id" id="course_lesson_id"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                        <option value="">اختبار عام للكورس</option>
                                        <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($lesson->id); ?>" <?php echo e(old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div id="edit-offline-course-wrap" style="display: none;">
                                <label for="offline_course_id_edit" class="block text-sm font-semibold text-slate-700 mb-1">الكورس (أوفلاين)</label>
                                <select name="offline_course_id" id="offline_course_id_edit"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                    <option value="">اختر الكورس الأوفلاين</option>
                                    <?php $__currentLoopData = $offlineCourses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($oc->id); ?>" <?php echo e(old('offline_course_id', $exam->offline_course_id) == $oc->id ? 'selected' : ''); ?>><?php echo e($oc->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['offline_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">وصف الاختبار</label>
                            <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="وصف مختصر..."><?php echo e(old('description', $exam->description)); ?></textarea>
                        </div>
                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1">تعليمات الاختبار</label>
                            <textarea name="instructions" id="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="تعليمات تظهر للطالب..."><?php echo e(old('instructions', $exam->instructions)); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات التوقيت والدرجات</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-1">مدة الاختبار (دقيقة) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" id="duration_minutes" value="<?php echo e(old('duration_minutes', $exam->duration_minutes)); ?>" min="5" max="480" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="total_marks" class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                                <input type="number" name="total_marks" id="total_marks" value="<?php echo e(old('total_marks', $exam->total_marks)); ?>" min="1" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="passing_marks" class="block text-sm font-semibold text-slate-700 mb-1">درجة النجاح <span class="text-red-500">*</span></label>
                                <input type="number" name="passing_marks" id="passing_marks" value="<?php echo e(old('passing_marks', $exam->passing_marks)); ?>" min="0" step="0.5" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            </div>
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-semibold text-slate-700 mb-1">عدد المحاولات <span class="text-red-500">*</span></label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" value="<?php echo e(old('attempts_allowed', $exam->attempts_allowed)); ?>" min="1" max="10" required
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">1–10 محاولات</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1">بداية الاختبار</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="<?php echo e(old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '')); ?>"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">فارغ = متاح دائماً</p>
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-1">نهاية الاختبار</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="<?php echo e(old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '')); ?>"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                                <p class="mt-1 text-xs text-slate-500">فارغ = متاح دائماً</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات العرض</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h4 class="font-semibold text-slate-800">العرض</h4>
                                <?php $__currentLoopData = ['randomize_questions' => 'خلط ترتيب الأسئلة', 'randomize_options' => 'خلط ترتيب الخيارات', 'show_results_immediately' => 'عرض النتيجة فوراً', 'show_correct_answers' => 'عرض الإجابات الصحيحة', 'show_explanations' => 'عرض شرح الإجابات', 'allow_review' => 'السماح بمراجعة الإجابات']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="<?php echo e($name); ?>" value="1" <?php echo e(old($name, $exam->$name) ? 'checked' : ''); ?>

                                               class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                                        <span class="text-sm font-medium text-slate-700"><?php echo e($label); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="space-y-3">
                                <h4 class="font-semibold text-slate-800">الحالة</h4>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $exam->is_active) ? 'checked' : ''); ?>

                                           class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                                    <span class="text-sm font-medium text-slate-700">اختبار نشط</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">إعدادات السايدبار</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="show_in_sidebar" value="1" <?php echo e(old('show_in_sidebar', $exam->show_in_sidebar ?? true) ? 'checked' : ''); ?>

                                   class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                            <span class="text-sm font-medium text-slate-700">إظهار الاختبار في السايدبار</span>
                        </label>
                        <div>
                            <label for="sidebar_position" class="block text-sm font-semibold text-slate-700 mb-1">موضع في السايدبار (1–10)</label>
                            <input type="number" name="sidebar_position" id="sidebar_position" value="<?php echo e(old('sidebar_position', $exam->sidebar_position ?? 1)); ?>" min="1" max="10"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <p class="mt-1 text-xs text-slate-500">الرقم الأصغر يظهر أولاً</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">معلومات سريعة</h3>
                    </div>
                    <div class="p-6">
                        <div class="p-4 rounded-xl border border-sky-200 bg-sky-50">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-sky-600"></i>
                                <span class="text-sm font-semibold text-sky-800">نصائح</span>
                            </div>
                            <ul class="text-sm text-sky-700 space-y-1.5">
                                <li>• يمكنك تعديل جميع معلومات الاختبار</li>
                                <li>• احفظ التغييرات قبل الخروج</li>
                                <li>• إدارة الأسئلة من صفحة عرض الاختبار</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-save ml-2"></i> حفظ التغييرات
                        </button>
                        <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>" class="w-full px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors block text-center">
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function loadLessons() {
    var courseId = document.getElementById('advanced_course_id').value;
    var lessonSelect = document.getElementById('course_lesson_id');
    lessonSelect.innerHTML = '<option value="">اختبار عام للكورس</option>';
    if (courseId) {
        fetch('/instructor/api/courses/' + courseId + '/lessons-list')
            .then(function(r) { return r.json(); })
            .then(function(lessons) {
                lessons.forEach(function(lesson) {
                    var option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    <?php if($exam->course_lesson_id): ?>
                    if (lesson.id == <?php echo e($exam->course_lesson_id); ?>) option.selected = true;
                    <?php endif; ?>
                    lessonSelect.appendChild(option);
                });
            })
            .catch(function(e) { console.error('Error loading lessons:', e); });
    }
}
function toggleEditCourseType() {
    var isOffline = document.querySelector('input[name="course_type"]:checked').value === 'offline';
    var onlineWrap = document.getElementById('edit-online-course-wrap');
    var offlineWrap = document.getElementById('edit-offline-course-wrap');
    var advSelect = document.getElementById('advanced_course_id');
    var lessonSelect = document.getElementById('course_lesson_id');
    var offSelect = document.getElementById('offline_course_id_edit');
    onlineWrap.style.display = isOffline ? 'none' : 'block';
    offlineWrap.style.display = isOffline ? 'block' : 'none';
    if (isOffline) {
        advSelect.removeAttribute('required');
        advSelect.disabled = true;
        lessonSelect.disabled = true;
        offSelect.disabled = false;
    } else {
        advSelect.disabled = false;
        lessonSelect.disabled = false;
        offSelect.disabled = true;
        if (advSelect.value) advSelect.setAttribute('required', 'required');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var courseId = document.getElementById('advanced_course_id').value;
    if (courseId) loadLessons();
    document.querySelectorAll('.course-type-radio').forEach(function(r) {
        r.addEventListener('change', toggleEditCourseType);
    });
    toggleEditCourseType();
    document.querySelector('form').addEventListener('submit', function() {
        var isOffline = document.querySelector('input[name="course_type"]:checked').value === 'offline';
        document.getElementById('advanced_course_id').disabled = isOffline;
        document.getElementById('course_lesson_id').disabled = isOffline;
        document.getElementById('offline_course_id_edit').disabled = !isOffline;
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/exams/edit.blade.php ENDPATH**/ ?>