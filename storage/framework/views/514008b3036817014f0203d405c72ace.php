<?php
    $schedule = $schedule ?? null;
    $selectedDays = old('work_days', $schedule?->work_days ?? \App\Models\WorkSchedule::defaultWorkDays());
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الموعد *</label>
        <input type="text" name="name" value="<?php echo e(old('name', $schedule?->name)); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الساعات المطلوبة *</label>
        <input type="number" step="0.5" min="1" max="24" name="required_hours" value="<?php echo e(old('required_hours', $schedule?->required_hours ?? 8)); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">بداية الدوام *</label>
        <input type="time" name="start_time" value="<?php echo e(old('start_time', $schedule ? substr((string) $schedule->start_time, 0, 5) : '09:00')); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">نهاية الدوام *</label>
        <input type="time" name="end_time" value="<?php echo e(old('end_time', $schedule ? substr((string) $schedule->end_time, 0, 5) : '17:00')); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">سماح التأخير (دقيقة)</label>
        <input type="number" min="0" max="120" name="grace_minutes" value="<?php echo e(old('grace_minutes', $schedule?->grace_minutes ?? 15)); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">فتح مبكر (دقيقة)</label>
        <input type="number" min="0" max="120" name="early_access_minutes" value="<?php echo e(old('early_access_minutes', $schedule?->early_access_minutes ?? 10)); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">أيام العمل *</label>
        <div class="flex flex-wrap gap-3">
            <?php $__currentLoopData = $dayOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNum => $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="work_days[]" value="<?php echo e($dayNum); ?>" <?php if(in_array($dayNum, $selectedDays)): echo 'checked'; endif; ?>>
                    <span class="text-sm"><?php echo e($dayLabel); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
        <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('description', $schedule?->description)); ?></textarea>
    </div>
    <div>
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $schedule?->is_active ?? true)): echo 'checked'; endif; ?> class="rounded">
            <span class="text-sm font-medium text-gray-700">نشط</span>
        </label>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\work-schedules\_form.blade.php ENDPATH**/ ?>