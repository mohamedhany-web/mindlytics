<?php
    $lead = $lead ?? null;
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
?>

<?php if(isset($salesReps)): ?>
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-user-tie text-violet-600"></i>
            الإسناد
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">اختر موظف المبيعات المسؤول عن هذا Lead.</p>
    </div>
    <div class="p-4 sm:p-6">
        <div class="max-w-xl">
            <label class="block text-xs font-semibold text-slate-700 mb-1">مسند إلى (موظف مبيعات) <span class="text-rose-600">*</span></label>
            <select name="assigned_to" required class="<?php echo e($inputClass); ?>">
                <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($rep->id); ?>" <?php if(old('assigned_to', $lead->assigned_to ?? '') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-address-card text-sky-600"></i>
            بيانات العميل والصفقة
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">معلومات التواصل، المرحلة، الأولوية، والمتابعة.</p>
    </div>
    <div class="p-4 sm:p-6">
        <?php echo $__env->make('admin.sales.leads._lead_fields_inner', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>

<?php if(isset($lead)): ?>
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-amber-200 bg-amber-50/70">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            رضا العميل (CSAT)
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">بعد إغلاق الصفقة — يُستخدم في مؤشرات الجودة الشهرية.</p>
    </div>
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">التقييم (1–5)</label>
                <select name="csat_rating" class="<?php echo e($inputClass); ?>">
                    <option value="">— لا يوجد —</option>
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo e($i); ?>" <?php if((string) old('csat_rating', $lead->csat_rating ?? '') === (string) $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
                <?php $__errorArgs = ['csat_rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظة (اختياري)</label>
                <textarea name="csat_comment" rows="2" class="<?php echo e($inputClass); ?>"><?php echo e(old('csat_comment', $lead->csat_comment)); ?></textarea>
            </div>
        </div>
        <?php if($lead->csat_recorded_at): ?>
            <p class="text-xs text-slate-500 mt-3">آخر تسجيل: <?php echo e($lead->csat_recorded_at->format('Y-m-d H:i')); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/_lead_fields.blade.php ENDPATH**/ ?>