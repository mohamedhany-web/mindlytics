<?php
    $lead = $lead ?? null;
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $labelClass = 'block text-xs font-semibold text-slate-700 mb-1';
?>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <div class="md:col-span-2 xl:col-span-3">
        <label class="<?php echo e($labelClass); ?>">الاسم <span class="text-rose-600">*</span></label>
        <input type="text" name="name" required value="<?php echo e(old('name', $lead->name ?? '')); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="<?php echo e($labelClass); ?>">الهاتف</label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $lead->phone ?? '')); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">البريد</label>
        <input type="email" name="email" value="<?php echo e(old('email', $lead->email ?? '')); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">الشركة</label>
        <input type="text" name="company" value="<?php echo e(old('company', $lead->company ?? '')); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="<?php echo e($labelClass); ?>">التصنيف <span class="text-rose-600">*</span></label>
        <select name="category_id" required class="<?php echo e($inputClass); ?>">
            <option value="">— اختر التصنيف —</option>
            <?php $__currentLoopData = $categories ?? \App\Models\SalesLeadCategory::active()->ordered()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>" <?php if(old('category_id', $lead->category_id ?? '') == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">المصدر <span class="text-rose-600">*</span></label>
        <select name="source" required class="<?php echo e($inputClass); ?>">
            <?php $__currentLoopData = \App\Models\SalesLead::SOURCES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('source', $lead->source ?? 'other') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">المرحلة <span class="text-rose-600">*</span></label>
        <select name="stage" required class="<?php echo e($inputClass); ?>">
            <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('stage', $lead->stage ?? 'new') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['stage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">الأولوية <span class="text-rose-600">*</span></label>
        <select name="priority" required class="<?php echo e($inputClass); ?>">
            <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('priority', $lead->priority ?? 'normal') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="text-[11px] text-slate-500 mt-1">«عاجل» يظهر أعلى القائمة عند ترتيب الأولوية.</p>
        <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="<?php echo e($labelClass); ?>">قيمة متوقعة (ج.م)</label>
        <input type="number" step="0.01" min="0" name="expected_value" value="<?php echo e(old('expected_value', $lead->expected_value ?? '')); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['expected_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="<?php echo e($labelClass); ?>">متابعة تالية</label>
        <input type="datetime-local" name="next_follow_up_at"
               value="<?php echo e(old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '')); ?>"
               class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['next_follow_up_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <label class="<?php echo e($labelClass); ?>">اهتمام / منتج</label>
        <textarea name="interest" rows="2" class="<?php echo e($inputClass); ?>"><?php echo e(old('interest', $lead->interest ?? '')); ?></textarea>
        <?php $__errorArgs = ['interest'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="md:col-span-2 xl:col-span-3">
        <label class="<?php echo e($labelClass); ?>">ملاحظات</label>
        <textarea name="notes" rows="3" class="<?php echo e($inputClass); ?>"><?php echo e(old('notes', $lead->notes ?? '')); ?></textarea>
        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <?php
        $savedLostReason = old('lost_reason', $lead->lost_reason ?? '');
        $matchedLossCode = collect(\App\Models\SalesLead::LOSS_REASONS)->search($savedLostReason, true);
        $lossCode = old('lost_reason_code', $matchedLossCode !== false ? $matchedLossCode : '');
        $lossCustom = old('lost_reason_custom', ($matchedLossCode === false ? $savedLostReason : ''));
    ?>
    <div class="md:col-span-2 xl:col-span-3 pt-2 border-t border-slate-100">
        <label class="<?php echo e($labelClass); ?>">سبب الخسارة (إلزامي عند مرحلة «خسارة»)</label>
        <select name="lost_reason_code" id="lost_reason_code" class="<?php echo e($inputClass); ?>">
            <option value="">— اختر السبب —</option>
            <?php $__currentLoopData = \App\Models\SalesLead::LOSS_REASONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if((string) $lossCode === (string) $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['lost_reason_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="md:col-span-2 xl:col-span-3" id="lost_reason_custom_wrap" style="display: <?php echo e(($lossCode === 'other') ? 'block' : 'none'); ?>;">
        <label class="<?php echo e($labelClass); ?>">اكتب سبب الخسارة</label>
        <input type="text" name="lost_reason_custom" id="lost_reason_custom" value="<?php echo e($lossCustom); ?>" class="<?php echo e($inputClass); ?>">
        <?php $__errorArgs = ['lost_reason_custom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const setupLossReasonToggle = () => {
            const select = document.getElementById('lost_reason_code');
            const stage = document.querySelector('select[name="stage"]');
            const customWrap = document.getElementById('lost_reason_custom_wrap');
            const customInput = document.getElementById('lost_reason_custom');
            if (!select || !stage || !customWrap || !customInput) return;

            const refresh = () => {
                customWrap.style.display = select.value === 'other' ? 'block' : 'none';
                select.required = stage.value === 'lost';
                customInput.required = stage.value === 'lost' && select.value === 'other';
            };

            select.addEventListener('change', refresh);
            stage.addEventListener('change', refresh);
            refresh();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupLossReasonToggle, { once: true });
        } else {
            setupLossReasonToggle();
        }
    })();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/_lead_fields_inner.blade.php ENDPATH**/ ?>