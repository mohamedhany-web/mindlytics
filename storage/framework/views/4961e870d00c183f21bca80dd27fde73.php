

<?php $__env->startSection('title', 'تحرير الامتحان'); ?>
<?php $__env->startSection('header', 'تحرير الامتحان'); ?>

<?php
    $startTime = old('start_time');
    if ($startTime === null && $exam->start_time) $startTime = $exam->start_time->format('Y-m-d\TH:i');
    $endTime = old('end_time');
    if ($endTime === null && $exam->end_time) $endTime = $exam->end_time->format('Y-m-d\TH:i');
?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.index')); ?>" class="hover:text-white">الامتحانات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="hover:text-white"><?php echo e(Str::limit($exam->course->title ?? '', 30)); ?></a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.show', $exam)); ?>" class="hover:text-white"><?php echo e(Str::limit($exam->title, 25)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تحرير</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تحرير الامتحان</h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e(Str::limit($exam->title, 50)); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.exams.show', $exam)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لامتحانات الكورس
                </a>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('admin.exams.update', $exam)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-600"></i>
                            معلومات الامتحان
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">عنوان الامتحان <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title', $exam->title)); ?>" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="مثال: امتحان الوحدة الأولى">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="advanced_course_id" class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                            <select name="advanced_course_id" id="advanced_course_id" required
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">اختر الكورس</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : ''); ?>>
                                        <?php echo e($course->title); ?><?php echo e($course->academicSubject ? ' — ' . $course->academicSubject->name : ''); ?>

                                    </option>
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
                            <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 mb-2">الدرس (اختياري)</label>
                            <select name="course_lesson_id" id="course_lesson_id"
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">لا يوجد</option>
                                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($lesson->id); ?>" <?php echo e(old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['course_lesson_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                            <textarea name="description" id="description" rows="3" placeholder="وصف مختصر"
                                      class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"><?php echo e(old('description', $exam->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">تعليمات الامتحان</label>
                            <textarea name="instructions" id="instructions" rows="4" placeholder="تعليمات للطلاب قبل البدء"
                                      class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"><?php echo e(old('instructions', $exam->instructions)); ?></textarea>
                            <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الامتحان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-cog text-indigo-600"></i>
                            إعدادات الامتحان
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">مدة الامتحان (دقيقة) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" id="duration_minutes" value="<?php echo e(old('duration_minutes', $exam->duration_minutes)); ?>" required min="5" max="480"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="60">
                                <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="attempts_allowed" class="block text-sm font-semibold text-gray-700 mb-2">المحاولات المسموحة <span class="text-red-500">*</span></label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" value="<?php echo e(old('attempts_allowed', $exam->attempts_allowed)); ?>" required min="0" max="10"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="1">
                                <p class="mt-1 text-xs text-gray-500">0 = غير محدود</p>
                                <?php $__errorArgs = ['attempts_allowed'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="passing_marks" class="block text-sm font-semibold text-gray-700 mb-2">درجة النجاح (%) <span class="text-red-500">*</span></label>
                                <input type="number" name="passing_marks" id="passing_marks" value="<?php echo e(old('passing_marks', $exam->passing_marks)); ?>" required min="0" max="100" step="0.1"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="50">
                                <?php $__errorArgs = ['passing_marks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="total_marks" class="block text-sm font-semibold text-gray-700 mb-2">إجمالي الدرجات</label>
                                <input type="number" name="total_marks" id="total_marks" value="<?php echo e(old('total_marks', $exam->total_marks)); ?>" min="0" step="0.1"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="يُحسب من الأسئلة">
                                <?php $__errorArgs = ['total_marks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- توقيتات الامتحان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-indigo-600"></i>
                            توقيتات الامتحان
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">وقت البداية</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="<?php echo e($startTime); ?>"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">فارغ = متاح فوراً</p>
                                <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">وقت النهاية</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="<?php echo e($endTime); ?>"
                                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">فارغ = متاح باستمرار</p>
                                <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والمراجعة -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-eye text-indigo-600"></i>
                            إعدادات العرض والمراجعة
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="randomize_questions" value="0">
                                    <input type="checkbox" name="randomize_questions" value="1" <?php echo e(old('randomize_questions', $exam->randomize_questions) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">خلط ترتيب الأسئلة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="randomize_options" value="0">
                                    <input type="checkbox" name="randomize_options" value="1" <?php echo e(old('randomize_options', $exam->randomize_options) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">خلط خيارات الإجابة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_results_immediately" value="0">
                                    <input type="checkbox" name="show_results_immediately" value="1" <?php echo e(old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض النتائج فور الانتهاء</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="allow_review" value="0">
                                    <input type="checkbox" name="allow_review" value="1" <?php echo e(old('allow_review', $exam->allow_review) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">السماح بمراجعة الأسئلة والإجابات</span>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_correct_answers" value="0">
                                    <input type="checkbox" name="show_correct_answers" value="1" <?php echo e(old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض الإجابات الصحيحة</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="show_explanations" value="0">
                                    <input type="checkbox" name="show_explanations" value="1" <?php echo e(old('show_explanations', $exam->show_explanations) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">عرض تفسيرات الإجابات</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="prevent_tab_switch" value="0">
                                    <input type="checkbox" name="prevent_tab_switch" value="1" <?php echo e(old('prevent_tab_switch', $exam->prevent_tab_switch) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">منع تبديل التبويبات أثناء الامتحان</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="hidden" name="auto_submit" value="0">
                                    <input type="checkbox" name="auto_submit" value="1" <?php echo e(old('auto_submit', $exam->auto_submit) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-800">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الأمان -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-indigo-600"></i>
                            إعدادات الأمان
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                <input type="hidden" name="require_camera" value="0">
                                <input type="checkbox" name="require_camera" value="1" <?php echo e(old('require_camera', $exam->require_camera) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-800">تتطلب تفعيل الكاميرا</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                <input type="hidden" name="require_microphone" value="0">
                                <input type="checkbox" name="require_microphone" value="1" <?php echo e(old('require_microphone', $exam->require_microphone) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-800">تتطلب تفعيل الميكروفون</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">حالة الامتحان</h2>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $exam->is_active) ? 'checked' : ''); ?> class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="font-semibold text-gray-800">امتحان نشط</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">غير النشط لا يظهر للطلاب</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">معلومات</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">تاريخ الإنشاء</span><span class="font-medium text-gray-800"><?php echo e($exam->created_at->format('Y-m-d H:i')); ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">آخر تحديث</span><span class="font-medium text-gray-800"><?php echo e($exam->updated_at->format('Y-m-d H:i')); ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">الأسئلة</span><span class="font-medium text-gray-800"><?php echo e($exam->examQuestions->count()); ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">المحاولات</span><span class="font-medium text-gray-800"><?php echo e($exam->attempts->count()); ?></span></div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        حفظ التغييرات
                    </button>
                    <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseSelect = document.getElementById('advanced_course_id');
    var lessonSelect = document.getElementById('course_lesson_id');
    if (!courseSelect || !lessonSelect) return;

    courseSelect.addEventListener('change', function() {
        var courseId = this.value;
        lessonSelect.innerHTML = '<option value="">جاري التحميل...</option>';

        if (!courseId) {
            lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
            return;
        }

        var url = '/admin/courses/' + courseId + '/lessons-list';
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
                var list = Array.isArray(data) ? data : (data.lessons || data.data || []);
                list.forEach(function(lesson) {
                    var opt = document.createElement('option');
                    opt.value = lesson.id;
                    opt.textContent = lesson.title;
                    lessonSelect.appendChild(opt);
                });
            })
            .catch(function() {
                lessonSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/exams/edit.blade.php ENDPATH**/ ?>