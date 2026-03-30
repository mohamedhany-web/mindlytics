<?php $lead = $lead ?? null; ?>
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
    <label class="block text-sm font-medium text-gray-700 mb-1">قيمة متوقعة (ج.م)</label>
    <input type="number" step="0.01" min="0" name="expected_value" value="<?php echo e(old('expected_value', $lead->expected_value ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">متابعة تالية</label>
    <input type="datetime-local" name="next_follow_up_at" value="<?php echo e(old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
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
    <label class="block text-sm font-medium text-gray-700 mb-1">سبب الخسارة (إن وُجد)</label>
    <input type="text" name="lost_reason" value="<?php echo e(old('lost_reason', $lead->lost_reason ?? '')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/_lead_fields_inner.blade.php ENDPATH**/ ?>