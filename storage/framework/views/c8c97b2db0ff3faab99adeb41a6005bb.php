<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">يوم الإجازة الأسبوعية</label>
    <select name="weekly_off_day" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        <option value="">— افتراضي (عطلة نهاية الأسبوع) —</option>
        <?php $__currentLoopData = \App\Models\User::weeklyOffDayOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($value); ?>" <?php if((string) old('weekly_off_day', $employee->weekly_off_day ?? '') === (string) $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <p class="text-xs text-gray-500 mt-1">يُستثنى من التقرير اليومي الإلزامي والخصم التلقائي. مثال: موظف بإجازة الجمعة يعمل السبت والأحد.</p>
    <?php $__errorArgs = ['weekly_off_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/employees/_weekly_off_day_field.blade.php ENDPATH**/ ?>