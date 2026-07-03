<?php
    $plan = $plan ?? null;
    $processSteps = old('process_steps', $plan?->process_steps ?? [['title' => '', 'description' => '']]);
    if (! is_array($processSteps) || $processSteps === []) {
        $processSteps = [['title' => '', 'description' => '']];
    }
?>

<div class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="<?php echo e($invLabelClass); ?>">عنوان الخطة *</label>
            <input type="text" name="title" value="<?php echo e(old('title', $plan?->title)); ?>" required class="<?php echo e($invInputClass); ?>">
        </div>
        <div class="md:col-span-2">
            <label class="<?php echo e($invLabelClass); ?>">وصف مختصر</label>
            <input type="text" name="short_description" value="<?php echo e(old('short_description', $plan?->short_description)); ?>" maxlength="500" class="<?php echo e($invInputClass); ?>">
        </div>
        <div class="md:col-span-2">
            <label class="<?php echo e($invLabelClass); ?>">تفاصيل الخطة</label>
            <textarea name="description" rows="5" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('description', $plan?->description)); ?></textarea>
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">نوع الاستثمار *</label>
            <select name="plan_type" required class="<?php echo e($invSelectClass); ?>">
                <?php $__currentLoopData = $planTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if(old('plan_type', $plan?->plan_type) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">نموذج العائد *</label>
            <select name="return_model" required class="<?php echo e($invSelectClass); ?>">
                <?php $__currentLoopData = $returnModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if(old('return_model', $plan?->return_model) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">الحد الأدنى *</label>
            <input type="number" step="0.01" min="0" name="min_investment" value="<?php echo e(old('min_investment', $plan?->min_investment ?? 0)); ?>" required class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">الحد الأقصى</label>
            <input type="number" step="0.01" min="0" name="max_investment" value="<?php echo e(old('max_investment', $plan?->max_investment)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">الهدف التمويلي</label>
            <input type="number" step="0.01" min="0" name="target_amount" value="<?php echo e(old('target_amount', $plan?->target_amount)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">العملة</label>
            <input type="text" name="currency" value="<?php echo e(old('currency', $plan?->currency ?? 'EGP')); ?>" maxlength="3" class="<?php echo e($invInputClass); ?> dir-ltr">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">المدة (بالأشهر)</label>
            <input type="number" min="1" name="duration_months" value="<?php echo e(old('duration_months', $plan?->duration_months)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">مستوى المخاطر *</label>
            <select name="risk_level" required class="<?php echo e($invSelectClass); ?>">
                <?php $__currentLoopData = $riskLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if(old('risk_level', $plan?->risk_level ?? 'medium') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">العائد المتوقع (% — من)</label>
            <input type="number" step="0.01" min="0" name="expected_return_min" value="<?php echo e(old('expected_return_min', $plan?->expected_return_min)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">العائد المتوقع (% — إلى)</label>
            <input type="number" step="0.01" min="0" name="expected_return_max" value="<?php echo e(old('expected_return_max', $plan?->expected_return_max)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">بداية العرض</label>
            <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', $plan?->starts_at?->format('Y-m-d\TH:i'))); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">نهاية العرض</label>
            <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', $plan?->ends_at?->format('Y-m-d\TH:i'))); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
        <div>
            <label class="<?php echo e($invLabelClass); ?>">ترتيب العرض</label>
            <input type="number" min="0" name="sort_order" value="<?php echo e(old('sort_order', $plan?->sort_order ?? 0)); ?>" class="<?php echo e($invInputClass); ?>">
        </div>
    </div>

    <div>
        <label class="<?php echo e($invLabelClass); ?>">شروط الأهلية</label>
        <textarea name="eligibility_criteria" rows="3" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('eligibility_criteria', $plan?->eligibility_criteria)); ?></textarea>
    </div>
    <div>
        <label class="<?php echo e($invLabelClass); ?>">المزايا للمستثمر</label>
        <textarea name="benefits" rows="3" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('benefits', $plan?->benefits)); ?></textarea>
    </div>
    <div>
        <label class="<?php echo e($invLabelClass); ?>">ملخص الشروط</label>
        <textarea name="terms_summary" rows="3" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('terms_summary', $plan?->terms_summary)); ?></textarea>
    </div>
    <div>
        <label class="<?php echo e($invLabelClass); ?>">ملاحظات قانونية خاصة بالخطة</label>
        <textarea name="legal_notes" rows="3" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('legal_notes', $plan?->legal_notes)); ?></textarea>
    </div>

    <div>
        <label class="<?php echo e($invLabelClass); ?>">خطوات التنفيذ (اختياري)</label>
        <div class="space-y-3">
            <?php $__currentLoopData = $processSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-3 rounded-xl bg-amber-50/50 border border-amber-100">
                    <input type="text" name="process_steps[<?php echo e($i); ?>][title]" value="<?php echo e($step['title'] ?? ''); ?>" placeholder="عنوان الخطوة" class="<?php echo e($invInputClass); ?>">
                    <input type="text" name="process_steps[<?php echo e($i); ?>][description]" value="<?php echo e($step['description'] ?? ''); ?>" placeholder="الوصف" class="<?php echo e($invInputClass); ?>">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $plan?->is_active ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-amber-600">
            <span class="text-sm text-slate-700 font-medium">الخطة نشطة</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $plan?->is_featured ?? false) ? 'checked' : ''); ?> class="rounded border-slate-300 text-amber-600">
            <span class="text-sm text-slate-700 font-medium">خطة مميزة</span>
        </label>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\plans\_form.blade.php ENDPATH**/ ?>