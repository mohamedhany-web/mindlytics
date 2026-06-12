

<?php $__env->startSection('title', 'طلب إجازة جديد'); ?>
<?php $__env->startSection('header', 'طلب إجازة جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form action="<?php echo e(route('employee.leaves.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="space-y-6">
                <!-- نوع الإجازة -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع الإجازة <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">اختر نوع الإجازة</option>
                        <option value="annual" <?php echo e(old('type') == 'annual' ? 'selected' : ''); ?>>سنوية</option>
                        <option value="sick" <?php echo e(old('type') == 'sick' ? 'selected' : ''); ?>>مرضية</option>
                        <option value="emergency" <?php echo e(old('type') == 'emergency' ? 'selected' : ''); ?>>طارئة</option>
                        <option value="unpaid" <?php echo e(old('type') == 'unpaid' ? 'selected' : ''); ?>>بدون راتب</option>
                        <option value="other" <?php echo e(old('type') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                    <?php $__errorArgs = ['type'];
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

                <!-- من تاريخ -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        من تاريخ <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required
                           value="<?php echo e(old('start_date')); ?>"
                           min="<?php echo e(date('Y-m-d')); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['start_date'];
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

                <!-- إلى تاريخ -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        إلى تاريخ <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required
                           value="<?php echo e(old('end_date')); ?>"
                           min="<?php echo e(date('Y-m-d')); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['end_date'];
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

                <!-- السبب -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        سبب الإجازة <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              placeholder="اكتب سبب طلب الإجازة..."><?php echo e(old('reason')); ?></textarea>
                    <?php $__errorArgs = ['reason'];
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

                <!-- الأزرار -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="<?php echo e(route('employee.leaves.index')); ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        تقديم الطلب
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // تحديث الحد الأدنى لتاريخ النهاية عند تغيير تاريخ البداية
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');
        if (startDate) {
            endDateInput.min = startDate;
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = startDate;
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/leaves/create.blade.php ENDPATH**/ ?>