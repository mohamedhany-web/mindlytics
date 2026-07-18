

<?php $__env->startSection('title', 'مجموعة عملاء جديدة'); ?>
<?php $__env->startSection('header', 'المبيعات — مجموعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $oldMembers = collect(old('member_ids', []))->map(fn ($id) => (int) $id);
?>

<div class="space-y-6 max-w-4xl">
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
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
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-black text-slate-900">مجموعة عملاء جديدة</h2>
                    <p class="text-xs text-slate-600">أنشئ المجموعة أولاً — إسناد موظفي السيلز اختياري ويمكن لاحقاً من صفحة المجموعة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 border-b border-slate-100 bg-white">
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pen text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">1. البيانات الأساسية</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">اسم المجموعة ووصف اختياري</p>
                </div>
            </div>
            <div class="rounded-xl border border-dashed border-sky-200 bg-sky-50/60 p-3 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-friends text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">2. الموظفون <span class="font-semibold text-sky-700">(اختياري)</span></p>
                    <p class="text-[11px] text-slate-500 mt-0.5">يمكن إنشاء المجموعة بدون موظف</p>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-tag text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">3. إضافة العملاء</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">بعد الإنشاء من صفحة المجموعة</p>
                </div>
            </div>
        </div>
    </section>

    <form method="post" action="<?php echo e(route('admin.sales.groups.store')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-teal-600"></i>
                    بيانات المجموعة
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المجموعة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="120"
                           placeholder="مثال: دفعة مارس — أونلاين"
                           class="<?php echo e($inputClass); ?>">
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
                    <label class="block text-xs font-semibold text-slate-700 mb-1">وصف <span class="text-slate-400 font-medium">(اختياري)</span></label>
                    <textarea name="description" rows="3" maxlength="2000"
                              placeholder="ملاحظات داخلية عن المجموعة أو الحملة..."
                              class="<?php echo e($inputClass); ?>"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden" x-data="{
            q: '',
            selected: <?php echo e($oldMembers->values()->toJson()); ?>,
            toggle(id) {
                id = Number(id);
                if (this.selected.includes(id)) {
                    this.selected = this.selected.filter(x => x !== id);
                } else {
                    this.selected.push(id);
                }
            },
            isOn(id) { return this.selected.includes(Number(id)); },
            match(name) {
                if (!this.q.trim()) return true;
                return (name || '').toLowerCase().includes(this.q.trim().toLowerCase());
            }
        }">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-friends text-sky-600"></i>
                        موظفو المبيعات
                        <span class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200 rounded-lg px-2 py-0.5">اختياري</span>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">ليس إلزامياً — يمكنك الإسناد لاحقاً. كل موظف يرى عملاءه داخل المجموعة فقط.</p>
                </div>
                <span class="text-xs font-semibold text-slate-500 tabular-nums" x-text="selected.length + ' محدّد'"></span>
            </div>

            <div class="p-5 space-y-3">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="search" x-model="q" placeholder="بحث عن موظف..."
                               class="w-full rounded-xl border border-slate-300 bg-white pr-9 pl-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <button type="button" @click="selected = []"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-semibold text-slate-600 rounded-xl border border-slate-300 hover:bg-slate-50">
                        <i class="fas fa-times"></i>
                        مسح الاختيار
                    </button>
                </div>

                <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-2 space-y-1">
                    <?php $__empty_1 = true; $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="flex items-center gap-2.5 text-sm rounded-lg px-2.5 py-2 cursor-pointer border transition-colors"
                               x-show="match(<?php echo \Illuminate\Support\Js::from($rep->name)->toHtml() ?>)"
                               :class="isOn(<?php echo e($rep->id); ?>) ? 'bg-white border-teal-200 shadow-sm' : 'border-transparent hover:bg-white hover:border-slate-200'">
                            <input type="checkbox" name="member_ids[]" value="<?php echo e($rep->id); ?>" class="rounded text-teal-600 focus:ring-teal-500"
                                   :checked="isOn(<?php echo e($rep->id); ?>)"
                                   @change="toggle(<?php echo e($rep->id); ?>)">
                            <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-700 to-slate-900 text-white text-xs font-black flex items-center justify-center flex-shrink-0">
                                <?php echo e(mb_substr($rep->name, 0, 1)); ?>

                            </span>
                            <span class="font-medium text-slate-800"><?php echo e($rep->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 m-1">لا يوجد موظفو مبيعات نشطون حالياً — يمكنك إنشاء المجموعة وإسنادهم لاحقاً.</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div class="rounded-xl border border-teal-100 bg-teal-50/70 px-3 py-2.5 text-[11px] text-teal-900 leading-relaxed">
                    <i class="fas fa-lightbulb text-amber-500 ml-1"></i>
                    بدون موظفين تُنشأ المجموعة كحاوية إدارية جاهزة، ثم تضيف الأعضاء والعملاء من صفحة التفاصيل.
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-slate-500">بعد الحفظ ستنتقل لصفحة المجموعة لإضافة العملاء وإرسال واتساب.</p>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                        <i class="fas fa-save"></i>
                        إنشاء المجموعة
                    </button>
                </div>
            </div>
        </section>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\create.blade.php ENDPATH**/ ?>