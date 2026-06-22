

<?php $__env->startSection('title', 'تعديل المحاضرة - ' . $lecture->title); ?>
<?php $__env->startSection('header', 'تعديل المحاضرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المحاضرة</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1"><?php echo e($lecture->title); ?></p>
            </div>
            <a href="<?php echo e(route('instructor.lectures.show', $lecture)); ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- النموذج -->
    <form action="<?php echo e(route('instructor.lectures.update', $lecture)); ?>" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- الكورس -->
                <div>
                    <label for="course_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        الكورس <span class="text-rose-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $lecture->course_id) == $course->id ? 'selected' : ''); ?>>
                                <?php echo e($course->title); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الدرس (اختياري) -->
                <div>
                    <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        الدرس (اختياري)
                    </label>
                    <select name="course_lesson_id" id="course_lesson_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                        <option value="">بدون درس محدد</option>
                        <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lesson->id); ?>" <?php echo e(old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : ''); ?>>
                                <?php echo e($lesson->title); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يمكنك ربط المحاضرة بدرس محدد من الكورس</p>
                    <?php $__errorArgs = ['course_lesson_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- العنوان -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        عنوان المحاضرة <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="<?php echo e(old('title', $lecture->title)); ?>" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"><?php echo e(old('description', $lecture->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- التاريخ والوقت -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        التاريخ والوقت <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                           value="<?php echo e(old('scheduled_at', $lecture->scheduled_at->format('Y-m-d\TH:i'))); ?>" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <?php $__errorArgs = ['scheduled_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- المدة -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        المدة (بالدقائق) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="duration_minutes" id="duration_minutes" 
                           value="<?php echo e(old('duration_minutes', $lecture->duration_minutes)); ?>" min="15" max="480" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <!-- نسبة المشاهدة المطلوبة للمحاضرة التالية -->
                <div>
                    <label for="min_watch_percent_to_unlock_next" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        نسبة المشاهدة المطلوبة لفتح المحاضرة التالية
                    </label>
                    <input type="number" name="min_watch_percent_to_unlock_next" id="min_watch_percent_to_unlock_next"
                           value="<?php echo e(old('min_watch_percent_to_unlock_next', $lecture->min_watch_percent_to_unlock_next ?? 0)); ?>" min="0" max="100"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                           placeholder="مثال: 80 يعني يجب مشاهدة 80% من هذه المحاضرة لفتح التالية">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">اتركها 0 أو فارغة إذا لم ترغب في قفل المحاضرة التالية على نسبة مشاهدة معينة.</p>
                </div>
            </div>

            <!-- الحالة -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="scheduled" <?php echo e(old('status', $lecture->status) == 'scheduled' ? 'selected' : ''); ?>>مجدولة</option>
                    <option value="in_progress" <?php echo e(old('status', $lecture->status) == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                    <option value="completed" <?php echo e(old('status', $lecture->status) == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                    <option value="cancelled" <?php echo e(old('status', $lecture->status) == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- رابط التسجيل -->
            <div>
                <label for="recording_url" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    رابط تسجيل المحاضرة
                </label>
                <input type="url" name="recording_url" id="recording_url" 
                       value="<?php echo e(old('recording_url', $lecture->recording_url)); ?>"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                <?php $__errorArgs = ['recording_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- مواد المحاضرة -->
            <?php $lecture->load('materials'); ?>
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    مواد المحاضرة
                </label>
                <?php $__currentLoopData = $lecture->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-wrap items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-gray-900 dark:text-white"><?php echo e($mat->title ?: $mat->file_name); ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block"><?php echo e($mat->file_name); ?></span>
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="material_visible_old[<?php echo e($mat->id); ?>]" value="0">
                            <input type="checkbox" name="material_visible_old[<?php echo e($mat->id); ?>]" value="1" <?php echo e($mat->is_visible_to_student ? 'checked' : ''); ?> class="w-4 h-4 text-sky-600 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">ظاهر للطالب</span>
                        </label>
                        <label class="flex items-center gap-2 text-rose-600 text-sm cursor-pointer">
                            <input type="checkbox" name="material_delete_old[]" value="<?php echo e($mat->id); ?>" class="w-4 h-4 rounded">
                            <span>حذف</span>
                        </label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div id="edit-materials-new" class="space-y-3"></div>
                <button type="button" id="edit-add-material" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg font-medium text-sm hover:bg-sky-200 dark:hover:bg-sky-800 transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة مادة جديدة
                </button>
            </div>

            <!-- الملاحظات -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الملاحظات
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"><?php echo e(old('notes', $lecture->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-rose-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الخيارات -->
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    الخيارات
                </label>
                
                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_attendance_tracking" value="1" 
                           <?php echo e(old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : ''); ?>

                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">تتبع الحضور</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">تسجيل حضور الطلاب تلقائياً أو يدوياً</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_assignment" value="1" 
                           <?php echo e(old('has_assignment', $lecture->has_assignment) ? 'checked' : ''); ?>

                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">يوجد واجب</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">إضافة واجب مرتبط بهذه المحاضرة</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <input type="checkbox" name="has_evaluation" value="1" 
                           <?php echo e(old('has_evaluation', $lecture->has_evaluation) ? 'checked' : ''); ?>

                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">يوجد تقييم</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">السماح للطلاب بتقييم المحاضرة</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex items-center justify-end gap-3">
            <a href="<?php echo e(route('instructor.lectures.show', $lecture)); ?>" 
               class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-save ml-2"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        var newMaterialsContainer = document.getElementById('edit-materials-new');
        var addBtn = document.getElementById('edit-add-material');
        if (newMaterialsContainer && addBtn) {
            var rowHtml = '<div class="edit-material-row flex flex-wrap items-end gap-3 p-4 bg-slate-50 dark:bg-gray-700 rounded-lg border border-slate-200 dark:border-gray-600">' +
                '<div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">الملف</label>' +
                '<input type="file" name="material_files[]" class="w-full text-sm" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg"></div>' +
                '<div class="w-48"><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">عنوان (اختياري)</label>' +
                '<input type="text" name="material_titles[]" placeholder="عنوان المادة" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white"></div>' +
                '<label class="flex items-center gap-2 pb-2"><input type="hidden" name="material_visible[]" value="0"><input type="checkbox" name="material_visible[]" value="1" checked class="w-4 h-4 text-sky-600 rounded"><span class="text-sm">ظاهر للطالب</span></label>' +
                '<button type="button" class="edit-remove-material px-3 py-2 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded-lg text-sm font-medium hover:bg-rose-200 dark:hover:bg-rose-800">حذف</button></div>';
            addBtn.addEventListener('click', function() {
                var div = document.createElement('div');
                div.innerHTML = rowHtml;
                newMaterialsContainer.appendChild(div.firstElementChild);
            });
            newMaterialsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.edit-remove-material')) e.target.closest('.edit-material-row').remove();
            });
        }
    })();
    // تحديث قائمة الدروس عند اختيار الكورس
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('course_lesson_id');
        
        // مسح الخيارات الحالية (ما عدا الخيار الأول)
        while (lessonSelect.children.length > 1) {
            lessonSelect.removeChild(lessonSelect.lastChild);
        }
        
        if (courseId) {
            // جلب دروس الكورس
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(data => {
                    if (data.lessons) {
                        data.lessons.forEach(lesson => {
                            const option = document.createElement('option');
                            option.value = lesson.id;
                            option.textContent = lesson.title;
                            lessonSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching lessons:', error);
                });
        }
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\lectures\edit.blade.php ENDPATH**/ ?>