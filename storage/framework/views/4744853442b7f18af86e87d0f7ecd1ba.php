

<?php $__env->startSection('title', 'تسجيل طالب جديد - الأوفلاين'); ?>
<?php $__env->startSection('header', 'تسجيل طالب جديد - الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- معلومات التسجيل -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">تسجيل طالب في كورس أوفلاين</h3>
                <a href="<?php echo e(route('admin.offline-enrollments.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.offline-enrollments.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الطالب -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار الطالب <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر الطالب</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($student->id); ?>" 
                                    <?php echo e(old('user_id', request('student_id')) == $student->id ? 'selected' : ''); ?>>
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

                <!-- الكورس -->
                <div>
                    <label for="offline_course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار الكورس <span class="text-red-500">*</span>
                    </label>
                    <select name="offline_course_id" id="offline_course_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('offline_course_id') == $course->id ? 'selected' : ''); ?>>
                                <?php echo e($course->title); ?> - <?php echo e($course->instructor->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['offline_course_id'];
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

                <!-- المجموعة -->
                <div>
                    <label for="group_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار المجموعة (اختياري)
                    </label>
                    <select name="group_id" id="group_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">بدون مجموعة</option>
                        <!-- سيتم ملء المجموعات عبر JavaScript بناءً على الكورس المختار -->
                    </select>
                    <?php $__errorArgs = ['group_id'];
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

                <!-- الحالة -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        حالة التسجيل <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                        <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
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

            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
                <a href="<?php echo e(route('admin.offline-enrollments.index')); ?>" 
                   class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-times mr-2"></i>إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>حفظ التسجيل
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('offline_course_id').addEventListener('change', function() {
    const courseId = this.value;
    const groupSelect = document.getElementById('group_id');
    
    // مسح الخيارات السابقة
    groupSelect.innerHTML = '<option value="">بدون مجموعة</option>';
    
    if (courseId) {
        // جلب المجموعات للكورس المختار
        fetch(`/admin/offline-courses/${courseId}/groups`)
            .then(response => response.json())
            .then(data => {
                if (data.groups && data.groups.length > 0) {
                    data.groups.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.textContent = group.name;
                        groupSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching groups:', error);
            });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-enrollments/create.blade.php ENDPATH**/ ?>