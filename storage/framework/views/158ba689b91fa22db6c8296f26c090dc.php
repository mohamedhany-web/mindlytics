<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
?>

<div class="space-y-5">
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم الفريق <span class="text-rose-600">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $team->name ?? '')); ?>" required class="<?php echo e($inputClass); ?>" placeholder="مثال: فريق المبيعات — القاهرة">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="<?php echo e($inputClass); ?>" placeholder="وصف مختصر للفريق (اختياري)"><?php echo e(old('description', $team->description ?? '')); ?></textarea>
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">مدير المبيعات <span class="text-rose-600">*</span></label>
        <select name="manager_id" required class="<?php echo e($inputClass); ?>">
            <option value="">— اختر مدير المبيعات —</option>
            <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m->id); ?>" <?php if(old('manager_id', $team->manager_id ?? '') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="text-xs text-slate-500 mt-1"><i class="fas fa-info-circle text-teal-600 ml-0.5"></i> يجب أن يكون الموظف بوظيفة «مدير مبيعات» — مدير واحد لكل فريق.</p>
        <?php $__errorArgs = ['manager_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-2">أعضاء الفريق (موظفو مبيعات)</label>
        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 max-h-72 overflow-y-auto">
            <?php if($salesReps->isEmpty()): ?>
                <p class="text-sm text-slate-500 text-center py-6">لا يوجد موظفو مبيعات متاحون — أضف موظفين بوظيفة «مبيعات» أولاً.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer transition-colors text-sm">
                            <input type="checkbox" name="member_ids[]" value="<?php echo e($rep->id); ?>"
                                   class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                   <?php if(in_array($rep->id, old('member_ids', $selectedMemberIds ?? []))): echo 'checked'; endif; ?>>
                            <span class="font-medium text-slate-800"><?php echo e($rep->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <label class="flex items-center gap-2.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
               <?php if(old('is_active', $team->is_active ?? true)): echo 'checked'; endif; ?>>
        <span class="text-sm font-semibold text-slate-800">فريق نشط</span>
    </label>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\sales-teams\_form.blade.php ENDPATH**/ ?>