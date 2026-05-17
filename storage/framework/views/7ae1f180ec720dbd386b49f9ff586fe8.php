

<?php $__env->startSection('title', 'تعديل المحاضرة'); ?>
<?php $__env->startSection('header', 'تعديل المحاضرة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $scheduledAtValue = old('scheduled_at');
    if ($scheduledAtValue === null && $lecture->scheduled_at) {
        $scheduledAtValue = $lecture->scheduled_at->format('Y-m-d\TH:i');
    }
    $platforms = [
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'google_drive' => 'Google Drive',
        'direct' => 'رابط مباشر',
        'bunny' => 'Bunny.net',
    ];
?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.lectures.index')); ?>" class="hover:text-white">المحاضرات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.lectures.by-course', $lecture->course_id)); ?>" class="hover:text-white"><?php echo e(Str::limit($lecture->course->title ?? '', 30)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تعديل المحاضرة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل المحاضرة</h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e(Str::limit($lecture->title, 50)); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.lectures.show', $lecture)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="<?php echo e(route('admin.lectures.by-course', $lecture->course_id)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لمحاضرات الكورس
                </a>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('admin.lectures.update', $lecture)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- المعلومات الأساسية -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    المعلومات الأساسية
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="course_id" id="course_id" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $lecture->course_id) == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 55)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="instructor_id" class="block text-sm font-semibold text-gray-700 mb-2">المحاضر <span class="text-red-500">*</span></label>
                        <select name="instructor_id" id="instructor_id" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id', $lecture->instructor_id) == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['instructor_id'];
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
                    <label for="course_lesson_id" class="block text-sm font-semibold text-gray-700 mb-2">الدرس المرتبط (اختياري)</label>
                    <select name="course_lesson_id" id="course_lesson_id"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">لا يوجد</option>
                        <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lesson->id); ?>" <?php echo e(old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->title); ?></option>
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
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="<?php echo e(old('title', $lecture->title)); ?>" required
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="مثال: المحاضرة الأولى - المقدمة">
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
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                              placeholder="وصف مختصر عن المحاضرة"><?php echo e(old('description', $lecture->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
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

        <!-- رابط التسجيل / الفيديو -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-video text-indigo-600"></i>
                    رابط تسجيل المحاضرة
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="video_platform" class="block text-sm font-semibold text-gray-700 mb-2">منصة الفيديو</label>
                    <select name="video_platform" id="video_platform"
                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">—</option>
                        <?php $__currentLoopData = $platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(old('video_platform', $lecture->video_platform) == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="recording_url" class="block text-sm font-semibold text-gray-700 mb-2">رابط التسجيل أو الفيديو</label>
                    <input type="url" name="recording_url" id="recording_url" value="<?php echo e(old('recording_url', $lecture->recording_url)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://...">
                    <?php $__errorArgs = ['recording_url'];
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

        <!-- التاريخ والوقت والحالة -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                    التاريخ والوقت والحالة
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ ووقت المحاضرة <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="<?php echo e($scheduledAtValue); ?>" required
                               class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php $__errorArgs = ['scheduled_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">المدة (دقيقة) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="<?php echo e(old('duration_minutes', $lecture->duration_minutes)); ?>" min="1" max="480" required
                               class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="scheduled" <?php echo e(old('status', $lecture->status) == 'scheduled' ? 'selected' : ''); ?>>مجدولة</option>
                            <option value="in_progress" <?php echo e(old('status', $lecture->status) == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                            <option value="completed" <?php echo e(old('status', $lecture->status) == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                            <option value="cancelled" <?php echo e(old('status', $lecture->status) == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                        </select>
                        <?php $__errorArgs = ['status'];
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

        <!-- الملاحظات -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-600"></i>
                    ملاحظات
                </h2>
            </div>
            <div class="p-6">
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                          placeholder="ملاحظات إضافية"><?php echo e(old('notes', $lecture->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <!-- الخيارات -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-cog text-indigo-600"></i>
                    خيارات المحاضرة
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_attendance_tracking" value="0">
                        <input type="checkbox" name="has_attendance_tracking" value="1" <?php echo e(old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : ''); ?>

                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">تتبع الحضور</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_assignment" value="0">
                        <input type="checkbox" name="has_assignment" value="1" <?php echo e(old('has_assignment', $lecture->has_assignment) ? 'checked' : ''); ?>

                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">يوجد واجب</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-200 cursor-pointer transition-colors">
                        <input type="hidden" name="has_evaluation" value="0">
                        <input type="checkbox" name="has_evaluation" value="1" <?php echo e(old('has_evaluation', $lecture->has_evaluation) ? 'checked' : ''); ?>

                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="font-medium text-gray-800">يوجد تقييم للمحاضر</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="<?php echo e(route('admin.lectures.by-course', $lecture->course_id)); ?>" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/lectures/edit.blade.php ENDPATH**/ ?>