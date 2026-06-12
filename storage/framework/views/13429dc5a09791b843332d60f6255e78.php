<?php $lead = $lead ?? null; ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php if(isset($salesReps)): ?>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">مسند إلى (موظف مبيعات) <span class="text-red-500">*</span></label>
        <select name="assigned_to" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($rep->id); ?>" <?php if(old('assigned_to', $lead->assigned_to ?? '') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php endif; ?>
    <?php echo $__env->make('employee.sales._lead_fields_inner', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(isset($lead)): ?>
    <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            رضا العميل (CSAT) — بعد إغلاق الصفقة
        </h4>
        <p class="text-xs text-gray-500 mb-3">يُستخدم في مؤشرات الجودة الشهرية لموظف المبيعات.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التقييم (1–5)</label>
                <select name="csat_rating" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">— لا يوجد —</option>
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo e($i); ?>" <?php if((string) old('csat_rating', $lead->csat_rating ?? '') === (string) $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
                <?php $__errorArgs = ['csat_rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظة (اختياري)</label>
                <textarea name="csat_comment" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"><?php echo e(old('csat_comment', $lead->csat_comment)); ?></textarea>
            </div>
        </div>
        <?php if($lead->csat_recorded_at): ?>
            <p class="text-xs text-gray-400 mt-2">آخر تسجيل: <?php echo e($lead->csat_recorded_at->format('Y-m-d H:i')); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/_lead_fields.blade.php ENDPATH**/ ?>