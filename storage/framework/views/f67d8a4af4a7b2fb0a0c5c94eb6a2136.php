

<?php $__env->startSection('title', 'مجموعة عملاء جديدة'); ?>
<?php $__env->startSection('header', 'المبيعات — مجموعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
?>

<div class="max-w-3xl space-y-6">
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مجموعة عملاء جديدة</h2>
                    <p class="text-xs text-slate-600">اختر الموظفين ثم أضف العملاء من محافظهم في الخطوة التالية.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        <form method="post" action="<?php echo e(route('admin.sales.groups.store')); ?>" class="p-6 space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المجموعة *</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="120" placeholder="مثال: دفعة مارس — أونلاين" class="<?php echo e($inputClass); ?>">
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
                <label class="block text-xs font-semibold text-slate-700 mb-2">موظفو المبيعات *</label>
                <p class="text-[11px] text-slate-500 mb-2">يمكن اختيار أكثر من موظف — كل موظف يرى عملاءه المسندين إليه داخل المجموعة فقط.</p>
                <div class="max-h-56 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-3 space-y-1.5">
                    <?php $__empty_1 = true; $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="flex items-center gap-2.5 text-sm rounded-lg px-2.5 py-2 hover:bg-white cursor-pointer border border-transparent hover:border-slate-200 transition-colors">
                            <input type="checkbox" name="member_ids[]" value="<?php echo e($rep->id); ?>" class="rounded text-teal-600 focus:ring-teal-500"
                                <?php if(collect(old('member_ids', []))->contains($rep->id)): echo 'checked'; endif; ?>>
                            <span class="font-medium text-slate-800"><?php echo e($rep->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">لا يوجد موظفو مبيعات نشطون.</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">وصف (اختياري)</label>
                <textarea name="description" rows="3" maxlength="2000" placeholder="ملاحظات داخلية عن المجموعة..." class="<?php echo e($inputClass); ?>"><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-save"></i>
                    إنشاء — ثم أضف العملاء
                </button>
                <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    إلغاء
                </a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\create.blade.php ENDPATH**/ ?>