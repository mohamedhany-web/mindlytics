<?php
    $courses = $courses ?? [];
?>

<form action="<?php echo e(route('instructor.assignments.store')); ?>" method="POST" id="assignmentForm" class="space-y-5">
    <?php echo csrf_field(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label for="advanced_course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس <span class="text-red-500">*</span></label>
            <select name="advanced_course_id" id="advanced_course_id" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">اختر الكورس</option>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id', request('advanced_course_id')) == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label for="lesson_id" class="block text-sm font-semibold text-slate-700 mb-1">الدرس (اختياري)</label>
            <select name="lesson_id" id="lesson_id"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">بدون درس محدد</option>
            </select>
            <p class="mt-1 text-xs text-slate-500">يتم ملء القائمة حسب الكورس المختار</p>
            <?php $__errorArgs = ['lesson_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php $courseGroups = $courseGroups ?? collect(); ?>
        <div class="md:col-span-2">
            <label for="group_id" class="block text-sm font-semibold text-slate-700 mb-1">للمجموعة (اختياري)</label>
            <select name="group_id" id="group_id"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">لجميع طلاب الكورس</option>
                <?php $__currentLoopData = $courseGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cid => $groups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" data-course-id="<?php echo e($cid); ?>" class="group-option" <?php echo e(old('group_id', request('group_id')) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="mt-1 text-xs text-slate-500">إن اخترت مجموعة سيظهر الواجب لأعضاءها فقط ويمكنهم التسليم جماعياً</p>
            <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">عنوان الواجب <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white"
                   placeholder="مثال: واجب البرمجة الكائنية">
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"
                      placeholder="وصف مختصر عن الواجب..."><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1">التعليمات</label>
            <textarea name="instructions" id="instructions" rows="4"
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"
                      placeholder="تعليمات للطلاب حول إنجاز الواجب..."><?php echo e(old('instructions')); ?></textarea>
            <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label for="due_date" class="block text-sm font-semibold text-slate-700 mb-1">تاريخ الاستحقاق</label>
            <input type="datetime-local" name="due_date" id="due_date" value="<?php echo e(old('due_date')); ?>"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
            <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label for="max_score" class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
            <input type="number" name="max_score" id="max_score" value="<?php echo e(old('max_score', 100)); ?>" min="1" max="1000" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
            <?php $__errorArgs = ['max_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors w-full">
                <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1" <?php echo e(old('allow_late_submission') ? 'checked' : ''); ?>

                       class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                <span class="text-sm font-medium text-slate-700">السماح بالتسليم المتأخر</span>
            </label>
        </div>

        <div class="md:col-span-2">
            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">الحالة <span class="text-red-500">*</span></label>
            <select name="status" id="status" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="draft" <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                <option value="published" <?php echo e(old('status') == 'published' ? 'selected' : ''); ?>>منشور</option>
                <option value="archived" <?php echo e(old('status') == 'archived' ? 'selected' : ''); ?>>مؤرشف</option>
            </select>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5 border-t border-slate-200">
        <?php if(isset($isModal) && $isModal): ?>
            <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-times ml-2"></i> إلغاء
            </button>
        <?php else: ?>
            <a href="<?php echo e(route('instructor.assignments.index')); ?>"
               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors text-center">
                <i class="fas fa-times ml-2"></i> إلغاء
            </a>
        <?php endif; ?>
        <button type="submit"
                class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
            <i class="fas fa-save ml-2"></i> إنشاء الواجب
        </button>
    </div>
</form>

<script>
if (typeof updateLessonsOnCourseChange === 'undefined') {
    window.updateLessonsOnCourseChange = function() {
        var courseSelect = document.getElementById('advanced_course_id');
        if (!courseSelect) return;
        courseSelect.addEventListener('change', function() {
            var courseId = this.value;
            var lessonSelect = document.getElementById('lesson_id');
            if (!lessonSelect) return;
            while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
            if (courseId) {
                var loadingOption = document.createElement('option');
                loadingOption.value = '';
                loadingOption.textContent = 'جاري التحميل...';
                loadingOption.disabled = true;
                lessonSelect.appendChild(loadingOption);
                lessonSelect.disabled = true;
                fetch('/instructor/api/courses/' + courseId + '/lessons-list', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        loadingOption.remove();
                        var lessons = Array.isArray(data) ? data : (data.lessons || []);
                        if (lessons.length > 0) {
                            lessons.forEach(function(lesson) {
                                var opt = document.createElement('option');
                                opt.value = lesson.id;
                                opt.textContent = lesson.title || 'درس ' + (lesson.order || '');
                                lessonSelect.appendChild(opt);
                            });
                        } else {
                            var noOpt = document.createElement('option');
                            noOpt.value = '';
                            noOpt.textContent = 'لا يوجد دروس في هذا الكورس';
                            noOpt.disabled = true;
                            lessonSelect.appendChild(noOpt);
                        }
                        lessonSelect.disabled = false;
                    })
                    .catch(function() {
                        loadingOption.remove();
                        var errOpt = document.createElement('option');
                        errOpt.value = '';
                        errOpt.textContent = 'حدث خطأ';
                        errOpt.disabled = true;
                        lessonSelect.appendChild(errOpt);
                        lessonSelect.disabled = false;
                    });
            } else {
                lessonSelect.disabled = false;
            }
        });
    };
    document.addEventListener('DOMContentLoaded', function() { updateLessonsOnCourseChange(); });
}
</script>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/assignments/create-form.blade.php ENDPATH**/ ?>