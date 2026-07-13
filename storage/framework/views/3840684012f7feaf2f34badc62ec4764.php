<?php $__env->startSection('title', 'إضافة محاضرة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.lectures.index')); ?>" class="hover:text-white">المحاضرات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">إضافة محاضرة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إضافة محاضرة جديدة</h1>
                <p class="text-sm text-white/90 mt-1">إنشاء محاضرة جديدة وربطها بكورس ومحاضر</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e($preselectedCourseId ? route('admin.lectures.by-course', $preselectedCourseId) : route('admin.lectures.index')); ?>" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    <?php echo e($preselectedCourseId ? 'العودة لمحاضرات الكورس' : 'العودة للمحاضرات'); ?>

                </a>
            </div>
        </div>
    </div>

    <!-- نموذج إضافة المحاضرة -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات المحاضرة</h4>
        </div>

        <form action="<?php echo e(route('admin.lectures.store')); ?>" method="POST" class="p-6 sm:p-8">
            <?php echo csrf_field(); ?>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                        <select name="course_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر الكورس</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $preselectedCourseId ?? null) == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 60)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['course_id'];
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">المحاضر <span class="text-red-500">*</span></label>
                        <select name="instructor_id" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر المحاضر</option>
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id') == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['instructor_id'];
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="مثال: المحاضرة الأولى - المقدمة">
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
                              placeholder="وصف مختصر عن المحاضرة"><?php echo e(old('description')); ?></textarea>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ ووقت المحاضرة <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at')); ?>" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <?php $__errorArgs = ['scheduled_at'];
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">مدة المحاضرة (دقيقة)</label>
                        <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', 60)); ?>" min="1"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="60">
                        <?php $__errorArgs = ['duration_minutes'];
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رابط تسجيل المحاضرة (بعد الانتهاء)</label>
                    <input type="url" name="recording_url" value="<?php echo e(old('recording_url')); ?>"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="رابط التسجيل أو الفيديو المسجل">
                    <?php $__errorArgs = ['recording_url'];
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="ملاحظات إضافية"><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
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

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-sm font-semibold text-gray-700 mb-3">خيارات المحاضرة</p>
                    <div class="flex flex-wrap gap-6 gap-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="has_attendance_tracking" value="1" <?php echo e(old('has_attendance_tracking', true) ? 'checked' : ''); ?>

                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="text-sm font-medium text-gray-700">تتبع الحضور</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="has_assignment" value="1" <?php echo e(old('has_assignment') ? 'checked' : ''); ?>

                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="text-sm font-medium text-gray-700">يوجد واجب</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="has_evaluation" value="1" <?php echo e(old('has_evaluation') ? 'checked' : ''); ?>

                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="text-sm font-medium text-gray-700">يوجد تقييم للمحاضر</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="<?php echo e(route('admin.lectures.index')); ?>" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-save"></i>
                        حفظ المحاضرة
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\lectures\create.blade.php ENDPATH**/ ?>