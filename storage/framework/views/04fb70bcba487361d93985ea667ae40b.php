
<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fas fa-building text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">المعلومات الأساسية</h3>
                <p class="text-xs text-slate-600 mt-1">الاسم والمعرّف اللاتيني والدومين والمنطقة.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-signature text-blue-600 text-sm"></i>
                    اسم الفرع <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="<?php echo e(old('name', $branch->name ?? '')); ?>" required maxlength="255"
                       placeholder="مثال: أكاديمية القاهرة"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-link text-blue-600 text-sm"></i>
                    slug (للنطاق الفرعي) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="slug" value="<?php echo e(old('slug', $branch->slug ?? '')); ?>" required dir="ltr"
                       placeholder="eg-cairo"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm font-mono text-left text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <p class="text-xs text-slate-500 mt-1">أحرف صغيرة وأرقام وشرطات فقط.</p>
                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-globe text-blue-600 text-sm"></i>
                    دومين مخصص (اختياري)
                </label>
                <input type="text" name="custom_domain" value="<?php echo e(old('custom_domain', $branch->custom_domain ?? '')); ?>" dir="ltr"
                       placeholder="academy.example.com"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm font-mono text-left text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['custom_domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-flag text-blue-600 text-sm"></i>
                    رمز الدولة (ISO2)
                </label>
                <input type="text" name="country_code" maxlength="2" value="<?php echo e(old('country_code', $branch->country_code ?? '')); ?>"
                       placeholder="EG"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm uppercase text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['country_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-coins text-blue-600 text-sm"></i>
                    العملة (ISO3) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="currency" maxlength="3" value="<?php echo e(old('currency', $branch->currency ?? 'EGP')); ?>" required
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm uppercase text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-clock text-blue-600 text-sm"></i>
                    المنطقة الزمنية (اختياري)
                </label>
                <input type="text" name="timezone" value="<?php echo e(old('timezone', $branch->timezone ?? '')); ?>" dir="ltr" placeholder="Africa/Cairo"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-palette text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">المظهر والإعدادات</h3>
                <p class="text-xs text-slate-600 mt-1">الترتيب، الألوان، الشعار، والملاحظات الداخلية.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-sort-numeric-down text-indigo-600 text-sm"></i>
                    ترتيب العرض
                </label>
                <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $branch->sort_order ?? 0)); ?>"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-fill-drip text-indigo-600 text-sm"></i>
                    لون العلامة (HEX)
                </label>
                <input type="text" name="primary_color" value="<?php echo e(old('primary_color', $branch->primary_color ?? '')); ?>" dir="ltr" placeholder="#2563eb"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['primary_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-image text-indigo-600 text-sm"></i>
                    مسار الشعار
                </label>
                <input type="text" name="logo_path" value="<?php echo e(old('logo_path', $branch->logo_path ?? '')); ?>" dir="ltr"
                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:border-slate-400">
                <?php $__errorArgs = ['logo_path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 space-y-1">
                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-600 text-sm"></i>
                    ملاحظات داخلية (للإدارة فقط)
                </label>
                <textarea name="internal_notes" rows="4"
                          class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-y min-h-[6rem]"><?php echo e(old('internal_notes', $branch->internal_notes ?? '')); ?></textarea>
                <?php $__errorArgs = ['internal_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 flex items-start gap-3 rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="branch_is_active" class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4"
                       <?php if((string) old('is_active', isset($branch) && $branch ? ($branch->is_active ? '1' : '0') : '1') === '1'): echo 'checked'; endif; ?>>
                <label for="branch_is_active" class="text-sm font-semibold text-slate-800 cursor-pointer leading-relaxed">
                    الفرع نشط ويُستخدم في حل الدومين والعرض العام
                </label>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/branches/_form.blade.php ENDPATH**/ ?>