

<?php $__env->startSection('title', 'إضافة خصم موظف - Mindlytics'); ?>
<?php $__env->startSection('header', 'إضافة خصم موظف'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">إضافة خصم جديد</h2>
                    <p class="text-sm text-slate-600 mt-1">تسجيل خصم من راتب موظف (ضريبة، تأمين، قرض، غرامة، أخرى)</p>
                </div>
            </div>
        </div>
        <?php if($errors->any()): ?>
            <div class="mx-5 mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle ml-1"></i>يرجى تصحيح الأخطاء التالية:</p>
                <ul class="list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('admin.employee-deductions.store')); ?>" class="px-5 py-6 sm:px-8 lg:px-12">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="">اختر الموظف</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?> <?php if($emp->email): ?>(<?php echo e($emp->email); ?>)<?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الاتفاقية (اختياري)</label>
                    <select name="agreement_id" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="">— بدون ربط باتفاقية —</option>
                        <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($agr->id); ?>" <?php echo e(old('agreement_id') == $agr->id ? 'selected' : ''); ?>><?php echo e($agr->agreement_number); ?> — <?php echo e($agr->employee->name ?? ''); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['agreement_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الخصم <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" placeholder="مثال: خصم تأمين اجتماعي" />
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف (اختياري)</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" placeholder="تفاصيل إضافية..."><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المبلغ (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" placeholder="0.00" />
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الخصم <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="tax" <?php echo e(old('type') == 'tax' ? 'selected' : ''); ?>>ضريبة</option>
                        <option value="insurance" <?php echo e(old('type', 'other') == 'insurance' ? 'selected' : ''); ?>>تأمين</option>
                        <option value="loan" <?php echo e(old('type') == 'loan' ? 'selected' : ''); ?>>قرض</option>
                        <option value="penalty" <?php echo e(old('type') == 'penalty' ? 'selected' : ''); ?>>غرامة</option>
                        <option value="other" <?php echo e(old('type') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الخصم <span class="text-red-500">*</span></label>
                    <input type="date" name="deduction_date" value="<?php echo e(old('deduction_date', date('Y-m-d'))); ?>" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400" />
                    <?php $__errorArgs = ['deduction_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400">
                        <option value="pending" <?php echo e(old('status', 'pending') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="applied" <?php echo e(old('status') == 'applied' ? 'selected' : ''); ?>>مطبقة</option>
                        <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-400"><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm shadow-lg transition-all">
                    <i class="fas fa-save ml-1"></i> حفظ الخصم
                </button>
                <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-all">إلغاء</a>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-deductions\create.blade.php ENDPATH**/ ?>