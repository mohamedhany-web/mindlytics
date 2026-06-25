

<?php $__env->startSection('title', 'إنشاء امتحان جديد'); ?>
<?php $__env->startSection('header', 'إنشاء امتحان جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.exams.index')); ?>" class="hover:text-primary-600">الامتحانات</a>
                <?php if($selectedCourse): ?>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.by-course', $selectedCourse)); ?>" class="hover:text-primary-600">امتحانات الكورس</a>
                <?php endif; ?>
                <span class="mx-2">/</span>
                <span>إنشاء امتحان جديد</span>
            </nav>
        </div>
        <a href="<?php echo e($selectedCourse ? route('admin.exams.by-course', $selectedCourse) : route('admin.exams.index')); ?>" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            <?php echo e($selectedCourse ? 'العودة لامتحانات الكورس' : 'العودة'); ?>

        </a>
    </div>

    <!-- نموذج إنشاء الامتحان -->
    <form action="<?php echo e(route('admin.exams.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات الامتحان</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- عنوان الامتحان -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الامتحان <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="مثال: امتحان الوحدة الأولى - الرياضيات">
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

                        <!-- الكورس والدرس -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="advanced_course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    الكورس <span class="text-red-500">*</span>
                                </label>
                                <select name="advanced_course_id" id="advanced_course_id" required onchange="loadLessons()"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر الكورس</option>
                                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->id); ?>" <?php echo e((old('advanced_course_id', $selectedCourse) == $course->id) ? 'selected' : ''); ?>>
                                            <?php echo e($course->title); ?> - <?php echo e($course->academicSubject->name ?? 'غير محدد'); ?>

                                        </option>
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
                                <label for="course_lesson_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    الدرس (اختياري)
                                </label>
                                <select name="course_lesson_id" id="course_lesson_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">امتحان عام للكورس</option>
                                    <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lesson->id); ?>" <?php echo e(old('course_lesson_id') == $lesson->id ? 'selected' : ''); ?>>
                                            <?php echo e($lesson->title); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                وصف الامتحان
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="وصف مختصر عن الامتحان ومحتواه..."><?php echo e(old('description')); ?></textarea>
                        </div>

                        <!-- التعليمات -->
                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                تعليمات الامتحان
                            </label>
                            <textarea name="instructions" id="instructions" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="اكتب التعليمات التي ستظهر للطالب قبل بدء الامتحان..."><?php echo e(old('instructions')); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- إعدادات التوقيت والدرجات -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات التوقيت والدرجات</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- مدة الامتحان -->
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    مدة الامتحان (دقيقة) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="duration_minutes" 
                                       value="<?php echo e(old('duration_minutes', 60)); ?>" min="5" max="480" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- عدد المحاولات -->
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-medium text-gray-700 mb-2">
                                    عدد المحاولات المسموحة <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" 
                                       value="<?php echo e(old('attempts_allowed', 1)); ?>" min="0" max="10" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">0 = محاولات غير محدودة</p>
                            </div>

                            <!-- درجة النجاح -->
                            <div>
                                <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    درجة النجاح (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="passing_marks" id="passing_marks" 
                                       value="<?php echo e(old('passing_marks', 60)); ?>" min="0" max="100" step="0.5" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <!-- تواريخ الامتحان -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    تاريخ بداية الامتحان
                                </label>
                                <input type="datetime-local" name="start_time" id="start_time" 
                                       value="<?php echo e(old('start_time')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    تاريخ انتهاء الامتحان
                                </label>
                                <input type="datetime-local" name="end_time" id="end_time" 
                                       value="<?php echo e(old('end_time')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والأمان -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إعدادات العرض والأمان</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- إعدادات العرض -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-900">إعدادات العرض</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="randomize_questions" value="1" 
                                           <?php echo e(old('randomize_questions') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">خلط ترتيب الأسئلة</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="randomize_options" value="1" 
                                           <?php echo e(old('randomize_options') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">خلط ترتيب الخيارات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_results_immediately" value="1" 
                                           <?php echo e(old('show_results_immediately', true) ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض النتيجة فوراً</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_correct_answers" value="1" 
                                           <?php echo e(old('show_correct_answers') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض الإجابات الصحيحة</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_explanations" value="1" 
                                           <?php echo e(old('show_explanations') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">عرض شرح الإجابات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="allow_review" value="1" 
                                           <?php echo e(old('allow_review', true) ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">السماح بمراجعة الإجابات</span>
                                </label>
                            </div>

                            <!-- إعدادات الأمان -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-900">إعدادات الأمان</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="prevent_tab_switch" value="1" 
                                           <?php echo e(old('prevent_tab_switch', true) ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">منع تبديل التبويبات</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="auto_submit" value="1" 
                                           <?php echo e(old('auto_submit', true) ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_camera" value="1" 
                                           <?php echo e(old('require_camera') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تتطلب تفعيل الكاميرا</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_microphone" value="1" 
                                           <?php echo e(old('require_microphone') ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">تتطلب تفعيل المايكروفون</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" 
                                           <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="mr-2 text-sm text-gray-700">امتحان نشط</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- معلومات سريعة -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات سريعة</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 ml-2"></i>
                                <span class="text-sm font-medium text-blue-800">نصائح</span>
                            </div>
                            <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                <li>• بعد الإنشاء ستتمكن من إضافة الأسئلة</li>
                                <li>• يمكن اختيار أسئلة من البنك أو إنشاء جديدة</li>
                                <li>• تأكد من اختبار الامتحان قبل النشر</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save ml-2"></i>
                                إنشاء الامتحان
                            </button>
                            
                            <a href="<?php echo e(route('admin.exams.index')); ?>" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function loadLessons() {
    const courseId = document.getElementById('advanced_course_id').value;
    const lessonSelect = document.getElementById('course_lesson_id');
    
    // مسح الخيارات الحالية
    lessonSelect.innerHTML = '<option value="">امتحان عام للكورس</option>';
    
    if (courseId) {
        fetch(`/admin/courses/${courseId}/lessons-list`)
            .then(response => response.json())
            .then(lessons => {
                lessons.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading lessons:', error);
            });
    }
}

// تحميل الدروس عند تحميل الصفحة إذا كان هناك كورس محدد
document.addEventListener('DOMContentLoaded', function() {
    const courseId = document.getElementById('advanced_course_id').value;
    if (courseId) {
        loadLessons();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/exams/create.blade.php ENDPATH**/ ?>