<?php $__env->startSection('title', 'إضافة مدير فرع'); ?>
<?php $__env->startSection('header', 'إضافة مدير فرع'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-16">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="<?php echo e(route('admin.branches.index')); ?>" class="text-blue-600 hover:text-blue-700">الفروع</a>
                        <span>/</span>
                        <span class="text-slate-600">إضافة مدير فرع</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">إنشاء حساب مدير فرع</h2>
                    <p class="text-sm text-slate-600 mt-1">اختر الفرع ثم أدخل بيانات المدير. يمكن ترك كلمة المرور فارغة لتوليدها تلقائياً (تُعرض مرة واحدة بعد الحفظ).</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.branches.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shrink-0">
                <i class="fas fa-arrow-right"></i>
                قائمة الفروع
            </a>
        </div>
    </section>

    <?php if($branches->isEmpty()): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-6 py-5 text-amber-900 text-sm">
            لا يوجد فرع في النظام بعد. <a href="<?php echo e(route('admin.branches.create')); ?>" class="font-bold text-blue-700 underline">أنشئ فرعاً أولاً</a>.
        </div>
    <?php else: ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <form action="<?php echo e(route('admin.branch-managers.store')); ?>" method="POST" class="p-6 sm:p-8 space-y-6">
                <?php echo csrf_field(); ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-code-branch text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">الفرع</h3>
                            <p class="text-xs text-slate-600 mt-0.5">يُربط الحساب بهذا الفرع ويصل من لوحة <span class="font-mono text-xs">/branch-office</span> فقط.</p>
                        </div>
                    </div>
                    <div>
                        <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-building text-blue-600 text-sm"></i>
                            اختر الفرع <span class="text-rose-500">*</span>
                        </label>
                        <select name="branch_id" id="branch_id" required
                                class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer">
                            <option value="">— اختر —</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($b->id); ?>" <?php echo e((string) old('branch_id', '') === (string) $b->id ? 'selected' : ''); ?>>
                                    <?php echo e($b->name); ?> (<?php echo e($b->slug); ?>)<?php echo e($b->is_active ? '' : ' — موقوف'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-plus text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">بيانات مدير الفرع</h3>
                            <p class="text-xs text-slate-600 mt-0.5">البريد يُستخدم لتسجيل الدخول ويجب ألا يكون مسجّلاً مسبقاً.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-2">الاسم الكامل <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                                   class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-2">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required dir="ltr" autocomplete="off"
                                   class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-2">كلمة المرور (اختياري — 10 أحرف على الأقل إن وُجدت)</label>
                            <input type="password" name="password" id="password" dir="ltr" autocomplete="new-password" minlength="10"
                                   placeholder="اتركها فارغة للتوليد التلقائي"
                                   class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <?php $__errorArgs = ['password'];
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

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="<?php echo e(route('admin.branches.index')); ?>" class="inline-flex items-center justify-center rounded-xl border-2 border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-user-plus"></i>
                        إنشاء الحساب
                    </button>
                </div>
            </form>
        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\branches\branch-managers-create.blade.php ENDPATH**/ ?>