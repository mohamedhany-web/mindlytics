<?php echo csrf_field(); ?>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">الورشة المرتبطة</label>
            <select name="workshop_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
                <option value="">— بدون ربط —</option>
                <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ws->id); ?>" <?php if(old('workshop_id', $workshopPromoCode->workshop_id ?? '') == $ws->id): echo 'selected'; endif; ?>>
                        <?php echo e($ws->title); ?> <?php if($ws->starts_at): ?> (<?php echo e($ws->starts_at->format('Y-m-d')); ?>) <?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">كود الخصم *</label>
            <input type="text" name="code" required value="<?php echo e(old('code', $workshopPromoCode->code ?? \App\Models\WorkshopPromoCode::generateCode())); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 font-mono uppercase focus:ring-2 focus:ring-violet-500">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان *</label>
            <input type="text" name="title" required value="<?php echo e(old('title', $workshopPromoCode->title ?? '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الخصم *</label>
            <select name="discount_type" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
                <option value="percentage" <?php if(old('discount_type', $workshopPromoCode->discount_type ?? 'percentage') === 'percentage'): echo 'selected'; endif; ?>>نسبة مئوية</option>
                <option value="fixed" <?php if(old('discount_type', $workshopPromoCode->discount_type ?? '') === 'fixed'): echo 'selected'; endif; ?>>مبلغ ثابت</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">قيمة الخصم *</label>
            <input type="number" name="discount_value" step="0.01" min="0" required
                   value="<?php echo e(old('discount_value', $workshopPromoCode->discount_value ?? '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">حد أقصى للخصم (ج.م)</label>
            <input type="number" name="maximum_discount" step="0.01" min="0"
                   value="<?php echo e(old('maximum_discount', $workshopPromoCode->maximum_discount ?? '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">حد أدنى للطلب (ج.م)</label>
            <input type="number" name="minimum_order_amount" step="0.01" min="0"
                   value="<?php echo e(old('minimum_order_amount', $workshopPromoCode->minimum_order_amount ?? '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البداية</label>
            <input type="date" name="starts_at" value="<?php echo e(old('starts_at', isset($workshopPromoCode) && $workshopPromoCode->starts_at ? $workshopPromoCode->starts_at->format('Y-m-d') : '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء *</label>
            <input type="date" name="expires_at" value="<?php echo e(old('expires_at', isset($workshopPromoCode) && $workshopPromoCode->expires_at ? $workshopPromoCode->expires_at->format('Y-m-d') : '')); ?>"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
            <p class="text-xs text-slate-500 mt-1">ينتهي الكود تلقائياً بعد هذا التاريخ</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">حد التفعيلات</label>
            <input type="number" name="max_activations" min="1"
                   value="<?php echo e(old('max_activations', $workshopPromoCode->max_activations ?? '')); ?>"
                   placeholder="غير محدود"
                   class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
        <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-violet-500"><?php echo e(old('description', $workshopPromoCode->description ?? '')); ?></textarea>
    </div>

    <div class="rounded-xl border border-violet-100 bg-violet-50/50 p-4">
        <p class="text-sm font-bold text-violet-900 mb-3"><i class="fas fa-layer-group ml-1"></i> ينطبق على أنواع الكورسات</p>
        <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="applies_to_online" value="1" <?php if(old('applies_to_online', $workshopPromoCode->applies_to_online ?? true)): echo 'checked'; endif; ?> class="rounded border-violet-300 text-violet-600">
                <span class="text-sm">أونلاين (لايف)</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="applies_to_offline" value="1" <?php if(old('applies_to_offline', $workshopPromoCode->applies_to_offline ?? true)): echo 'checked'; endif; ?> class="rounded border-violet-300 text-violet-600">
                <span class="text-sm">أوفلاين (حضور)</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="applies_to_recorded" value="1" <?php if(old('applies_to_recorded', $workshopPromoCode->applies_to_recorded ?? true)): echo 'checked'; endif; ?> class="rounded border-violet-300 text-violet-600">
                <span class="text-sm">مسجّل / On-demand</span>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">كورسات أونلاين/مسجّلة محددة (اختياري)</label>
            <select name="applicable_advanced_course_ids[]" multiple size="6"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500">
                <?php $selAdv = old('applicable_advanced_course_ids', $workshopPromoCode->applicable_advanced_course_ids ?? []); ?>
                <?php $__currentLoopData = $advancedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php if(in_array($c->id, $selAdv ?? [])): echo 'selected'; endif; ?>><?php echo e($c->title); ?> — <?php echo e(number_format($c->price, 0)); ?> ج.م</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-slate-500 mt-1">اتركه فارغاً = كل الكورسات المؤهلة</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">كورسات أوفلاين محددة (اختياري)</label>
            <select name="applicable_offline_course_ids[]" multiple size="6"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-500">
                <?php $selOff = old('applicable_offline_course_ids', $workshopPromoCode->applicable_offline_course_ids ?? []); ?>
                <?php $__currentLoopData = $offlineCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php if(in_array($c->id, $selOff ?? [])): echo 'selected'; endif; ?>><?php echo e($c->title); ?> — <?php echo e(number_format($c->price, 0)); ?> ج.م</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $workshopPromoCode->is_active ?? true)): echo 'checked'; endif; ?> class="rounded border-slate-300 text-violet-600">
        <span class="text-sm font-semibold text-slate-700">نشط</span>
    </label>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshop-promo-codes\_form.blade.php ENDPATH**/ ?>