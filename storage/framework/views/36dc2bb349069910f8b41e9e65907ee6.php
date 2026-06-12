

<?php $__env->startSection('title', 'تعديل اتفاقية التقسيط'); ?>
<?php $__env->startSection('header', 'تعديل اتفاقية التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreement = $agreement ?? null;
    $plans = $plans ?? collect();
?>
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight"><?php echo e($agreement->student->name ?? 'طالب غير معروف'); ?></h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-pen text-xs"></i>
                        تحديث تفاصيل الاتفاقية
                    </span>
                </div>
                <p class="mt-3 text-white/80 max-w-2xl">
                    يمكنك تغيير حالة الاتفاقية أو نقلها إلى خطة أخرى، بالإضافة إلى إضافة ملاحظات إدارية.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="<?php echo e(route('admin.installments.agreements.show', $agreement)); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-amber-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للتفاصيل
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg border border-gray-100 p-8 space-y-6">
        <form action="<?php echo e(route('admin.installments.agreements.update', $agreement)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">الخطة المرتبطة</label>
                    <select name="installment_plan_id" disabled class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-100 text-gray-500">
                        <option><?php echo e($agreement->plan->name ?? 'خطة عامة'); ?></option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">حالة الاتفاقية *</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('status', $agreement->status) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-rose-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">ملاحظات إدارية</label>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"><?php echo e(old('notes', $agreement->notes)); ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?php echo e(route('admin.installments.agreements.show', $agreement)); ?>" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl shadow">
                    <i class="fas fa-save"></i>
                    تحديث الاتفاقية
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/agreements/edit.blade.php ENDPATH**/ ?>