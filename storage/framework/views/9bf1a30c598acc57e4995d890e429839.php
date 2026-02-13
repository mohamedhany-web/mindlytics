

<?php $__env->startSection('title', 'تعديل المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-widest text-white/70 mb-3">تعديل المحفظة</p>
                    <h1 class="text-3xl sm:text-4xl font-bold"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h1>
                    <p class="mt-3 text-white/80 flex items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo e($wallet->user?->name ?? 'غير مرتبط بمستخدم'); ?></span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-right">
                    <div class="bg-white/15 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">الرصيد الحالي</p>
                        <p class="text-xl font-semibold"><?php echo e(number_format($wallet->balance, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-4 py-3">
                        <p class="text-xs text-white/70">نوع المحفظة</p>
                        <p class="text-lg font-semibold"><?php echo e($wallet->type_name ?? 'غير محدد'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden" x-data="{ walletType: '<?php echo e(old('type', $wallet->type)); ?>' }">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">بيانات المحفظة</h2>
                    <p class="text-sm text-gray-500">قم بتحديث معلومات المحفظة واحفظ التغييرات.</p>
                </div>
                <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-sky-600 hover:text-sky-700">
                    <i class="fas fa-arrow-right ml-1"></i>
                    العودة للتفاصيل
                </a>
            </div>

            <form action="<?php echo e(route('admin.wallets.update', $wallet)); ?>" method="POST" class="p-6 sm:p-8 space-y-10">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <section class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم المحفظة *</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $wallet->name)); ?>" required
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            <?php $__errorArgs = ['name'];
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">نوع المحفظة *</label>
                            <select name="type" x-model="walletType" required
                                    class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                                <option value="vodafone_cash">فودافون كاش</option>
                                <option value="instapay">إنستا باي</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="cash">كاش</option>
                                <option value="other">أخرى</option>
                            </select>
                            <?php $__errorArgs = ['type'];
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الحساب/المحفظة</label>
                            <input type="text" name="account_number" value="<?php echo e(old('account_number', $wallet->account_number)); ?>"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            <?php $__errorArgs = ['account_number'];
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

                        <div x-show="walletType === 'bank_transfer'" x-cloak>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم البنك</label>
                            <input type="text" name="bank_name" value="<?php echo e(old('bank_name', $wallet->bank_name)); ?>"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            <?php $__errorArgs = ['bank_name'];
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم صاحب الحساب</label>
                            <input type="text" name="account_holder" value="<?php echo e(old('account_holder', $wallet->account_holder)); ?>"
                                   class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            <?php $__errorArgs = ['account_holder'];
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الرصيد الحالي</label>
                            <div class="relative">
                                <input type="number" name="balance" value="<?php echo e(old('balance', $wallet->balance)); ?>" step="0.01" min="0" readonly
                                       class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-gray-900 shadow-sm cursor-not-allowed">
                                <span class="absolute inset-y-0 left-4 flex items-center text-xs text-gray-500">غير قابل للتعديل</span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">لتعديل الرصيد استخدم صفحة المعاملات.</p>
                        </div>
                    </div>
                </section>

                <section class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/60 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"><?php echo e(old('notes', $wallet->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
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

                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $wallet->is_active) ? 'checked' : ''); ?>

                               class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل المحفظة</p>
                            <p class="text-xs text-gray-500">فعّل هذا الخيار للسماح باستخدام المحفظة.</p>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-gray-100">
                    <a href="<?php echo e(route('admin.wallets.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-right ml-1"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-sky-500/20 transition">
                        <i class="fas fa-save ml-1"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/wallets/edit.blade.php ENDPATH**/ ?>