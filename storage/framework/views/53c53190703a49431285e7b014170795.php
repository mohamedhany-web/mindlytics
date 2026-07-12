<?php $lead = $lead ?? null; $groups = $groups ?? collect(); ?>
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">الاسم <span class="text-red-500">*</span></label>
    <input type="text" name="name" required value="<?php echo e(old('name', $lead->name ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الهاتف</label>
    <input type="text" name="phone" value="<?php echo e(old('phone', $lead->phone ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">البريد</label>
    <input type="email" name="email" value="<?php echo e(old('email', $lead->email ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الشركة</label>
    <input type="text" name="company" value="<?php echo e(old('company', $lead->company ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">المصدر <span class="text-red-500">*</span></label>
    <select name="source" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <?php $__currentLoopData = \App\Models\SalesLead::SOURCES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(old('source', $lead->source ?? 'other') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">المرحلة <span class="text-red-500">*</span></label>
    <select name="stage" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(old('stage', $lead->stage ?? 'new') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['stage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الأولوية <span class="text-red-500">*</span></label>
    <select name="priority" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(old('priority', $lead->priority ?? 'normal') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <p class="text-xs text-gray-500 mt-1">عاجل يظهر في لوحة التحكم ويُرتّب أعلى القائمة عند اختيار ترتيب الأولوية.</p>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">المجموعة <span class="text-gray-400">(اختياري)</span></label>
    <select name="sales_lead_group_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <option value="">— بدون مجموعة —</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($group->id); ?>" <?php if(old('sales_lead_group_id', $lead->sales_lead_group_id ?? '') == $group->id): echo 'selected'; endif; ?>>
                <?php echo e($group->name); ?><?php if($group->is_admin_managed ?? false): ?> (إدارة) <?php endif; ?>
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['sales_lead_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">قيمة متوقعة (ج.م)</label>
    <input type="number" step="0.01" min="0" name="expected_value" value="<?php echo e(old('expected_value', $lead->expected_value ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
    <?php $__errorArgs = ['expected_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">متابعة تالية</label>
    <input type="datetime-local" name="next_follow_up_at" value="<?php echo e(old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
    <?php $__errorArgs = ['next_follow_up_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">اهتمام / منتج</label>
    <textarea name="interest" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"><?php echo e(old('interest', $lead->interest ?? '')); ?></textarea>
</div>
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
    <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"><?php echo e(old('notes', $lead->notes ?? '')); ?></textarea>
</div>
<div class="md:col-span-2">
    <?php
        $savedLostReason = old('lost_reason', $lead->lost_reason ?? '');
        $matchedLossCode = collect(\App\Models\SalesLead::LOSS_REASONS)->search($savedLostReason, true);
        $lossCode = old('lost_reason_code', $matchedLossCode !== false ? $matchedLossCode : '');
        $lossCustom = old('lost_reason_custom', ($matchedLossCode === false ? $savedLostReason : ''));
    ?>
    <label class="block text-sm font-medium text-gray-700 mb-1">سبب الخسارة (إلزامي عند مرحلة "خسارة")</label>
    <select name="lost_reason_code" id="lost_reason_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <option value="">— اختر السبب —</option>
        <?php $__currentLoopData = \App\Models\SalesLead::LOSS_REASONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if((string) $lossCode === (string) $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['lost_reason_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div class="md:col-span-2" id="lost_reason_custom_wrap" style="display: <?php echo e(($lossCode === 'other') ? 'block' : 'none'); ?>;">
    <label class="block text-sm font-medium text-gray-700 mb-1">اكتب سبب الخسارة</label>
    <input type="text" name="lost_reason_custom" id="lost_reason_custom" value="<?php echo e($lossCustom); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
    <?php $__errorArgs = ['lost_reason_custom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

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
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\_lead_fields_inner.blade.php ENDPATH**/ ?>