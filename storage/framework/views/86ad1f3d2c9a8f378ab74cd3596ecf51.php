

<?php $__env->startSection('title', 'تعديل الواجب: ' . $assignment->title); ?>
<?php $__env->startSection('header', 'تعديل الواجب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php $courseId = $assignment->advanced_course_id ?? $assignment->course_id; ?>
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.assignments.index')); ?>" class="hover:text-white">الواجبات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.assignments.by-course', $courseId)); ?>" class="hover:text-white"><?php echo e(Str::limit($assignment->course?->title ?? '', 25)); ?></a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.assignments.show', $assignment)); ?>" class="hover:text-white truncate"><?php echo e(Str::limit($assignment->title, 25)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تعديل</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل الواجب</h1>
                <p class="text-sm text-white/90 mt-1 truncate"><?php echo e($assignment->title); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.assignments.show', $assignment)); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="<?php echo e(route('admin.assignments.by-course', $courseId)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لواجبات الكورس
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات الواجب</h4>
        </div>

        <form action="<?php echo e(route('admin.assignments.update', $assignment)); ?>" method="POST" class="p-6 sm:p-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="advanced_course_id" id="advanced_course_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e(old('advanced_course_id', $assignment->advanced_course_id ?? $assignment->course_id) == $c->id ? 'selected' : ''); ?>><?php echo e(Str::limit($c->title, 50)); ?></option>
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
                            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $les): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($les->id); ?>" <?php echo e(old('lesson_id', $assignment->lesson_id) == $les->id ? 'selected' : ''); ?>><?php echo e(Str::limit($les->title, 40)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <input type="text" name="title" value="<?php echo e(old('title', $assignment->title)); ?>" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="عنوان الواجب">
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
                              placeholder="وصف مختصر"><?php echo e(old('description', $assignment->description)); ?></textarea>
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
                              placeholder="تعليمات للطلاب"><?php echo e(old('instructions', $assignment->instructions)); ?></textarea>
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
                        <input type="datetime-local" name="due_date" value="<?php echo e(old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '')); ?>"
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
                        <input type="number" name="max_score" value="<?php echo e(old('max_score', $assignment->max_score)); ?>" min="1" max="1000" required
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
                           <?php echo e(old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : ''); ?>

                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="allow_late_submission" class="text-sm font-medium text-gray-700">السماح بالتسليم المتأخر</label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="draft" <?php echo e(old('status', $assignment->status) == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="published" <?php echo e(old('status', $assignment->status) == 'published' ? 'selected' : ''); ?>>منشور</option>
                        <option value="archived" <?php echo e(old('status', $assignment->status) == 'archived' ? 'selected' : ''); ?>>مؤرشف</option>
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
                    حفظ التعديلات
                </button>
                <a href="<?php echo e(route('admin.assignments.by-course', $courseId)); ?>" class="inline-flex items-center gap-2 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-semibold transition-colors">
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

        var opt = document.createElement('option');
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
                var lessons = Array.isArray(data) ? data : (data.lessons || data.data || []);
                lessons.forEach(function(lesson) {
                    var o = document.createElement('option');
                    o.value = lesson.id;
                    o.textContent = lesson.title || ('درس ' + (lesson.order || ''));
                    lessonSelect.appendChild(o);
                });
                lessonSelect.disabled = false;
            })
            .catch(function() {
                opt.remove();
                var err = document.createElement('option');
                err.value = '';
                err.textContent = 'حدث خطأ';
                err.disabled = true;
                lessonSelect.appendChild(err);
                lessonSelect.disabled = false;
            });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\assignments\edit.blade.php ENDPATH**/ ?>