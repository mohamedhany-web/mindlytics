

<?php $__env->startSection('title', 'تسجيل طالب في مسار تعليمي'); ?>
<?php $__env->startSection('header', 'تسجيل طالب في مسار تعليمي'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                    <i class="fas fa-user-plus text-sky-600 ml-2"></i>
                    تسجيل طالب في مسار تعليمي
                </h3>
                <a href="<?php echo e(route('admin.learning-path-enrollments.index')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.learning-path-enrollments.store')); ?>" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الطالب -->
                <div>
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        اختيار الطالب <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <option value="">اختر الطالب</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($student->id); ?>" 
                                    <?php echo e((old('user_id', request('student_id')) == $student->id) ? 'selected' : ''); ?>>
                                <?php echo e($student->name); ?> - <?php echo e($student->phone); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['user_id'];
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

                <!-- المسار التعليمي -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        اختيار المسار التعليمي <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id" id="academic_year_id" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <option value="">اختر المسار التعليمي</option>
                        <?php $__currentLoopData = $learningPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($path->id); ?>" <?php echo e(old('academic_year_id') == $path->id ? 'selected' : ''); ?>>
                                <?php echo e($path->name); ?> (<?php echo e($path->code); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['academic_year_id'];
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

                <!-- حالة التسجيل -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        حالة التسجيل <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <option value="">اختر حالة التسجيل</option>
                        <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                        <option value="active" <?php echo e(old('status', 'active') == 'active' ? 'selected' : ''); ?>>نشط</option>
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
                    <p class="mt-1 text-xs text-gray-500">
                        عند اختيار "نشط"، سيتم تسجيل الطالب تلقائياً في جميع الكورسات المجانية في المسار.
                    </p>
                </div>

                <!-- ملاحظات -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                              placeholder="أي ملاحظات إضافية..."><?php echo e(old('notes')); ?></textarea>
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
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    سيتم تسجيل الطالب في المسار التعليمي المحدد. عند التفعيل، سيتم تسجيله تلقائياً في جميع الكورسات المجانية في المسار.
                </p>
                <div class="flex gap-3">
                    <a href="<?php echo e(route('admin.learning-path-enrollments.index')); ?>" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-save"></i>
                        حفظ التسجيل
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/learning-path-enrollments/create.blade.php ENDPATH**/ ?>