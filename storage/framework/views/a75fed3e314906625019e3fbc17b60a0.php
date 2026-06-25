<?php $__env->startSection('title', 'تعديل خطة التقسيط - Mindlytics'); ?>
<?php $__env->startSection('header', 'تعديل خطة التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $frequencyUnits = $frequencyUnits ?? [];
?>
<div class="space-y-6">
    <?php echo $__env->make('admin.installments.partials.header', [
        'title' => 'تعديل خطة التقسيط',
        'description' => 'تحديث بيانات الخطة — راجع الأقساط المرتبطة قبل الحفظ.',
        'icon' => 'fa-edit',
        'iconGradient' => 'from-sky-500 to-blue-600',
        'meta' => $plan->name ?? '',
        'actions' => [
            ['route' => 'admin.installments.plans.show', 'label' => 'عرض الخطة', 'icon' => 'fa-eye', 'params' => [$plan]],
            ['route' => 'admin.installments.plans.index', 'label' => 'قائمة الخطط', 'icon' => 'fa-list'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.installments.partials.nav', ['active' => 'plans'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8 max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-black text-gray-900">بيانات الخطة</h2>
                    <p class="text-sm text-gray-500 mt-1">حدّث التفاصيل الأساسية والأقساط والدورية.</p>
                </div>
            </div>

            <form action="<?php echo e(route('admin.installments.plans.update', $plan)); ?>" method="POST" class="space-y-8">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اسم الخطة *</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $plan->name)); ?>" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
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
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الكورس المرتبط (اختياري)</label>
                        <select name="advanced_course_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">خطة عامة</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id', $plan->advanced_course_id) == $course->id ? 'selected' : ''); ?>>
                                    <?php echo e($course->title); ?> (<?php echo e(number_format($course->price ?? 0, 2)); ?> ج.م)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['advanced_course_id'];
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
                    <label class="block text-sm font-semibold text-gray-700">تفاصيل الخطة</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                              placeholder="أضف تفاصيل توضيحية إضافية عن خطة التقسيط"><?php echo e(old('description', $plan->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
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

                <div class="rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-4 text-xs text-sky-700 leading-relaxed">
                    <strong class="block text-sm mb-2">نصائح سريعة</strong>
                    - اترك المبلغ الإجمالي فارغاً ليتم استخدام سعر الكورس إن وجد.
                    <br>- يمكنك استخدام الخطة لكورس واحد أو كخطة عامة لكافة الطلاب.
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">الجانب المالي</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="total_amount" value="<?php echo e(old('total_amount', $plan->total_amount)); ?>"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                       placeholder="يتم استخدام سعر الكورس إن تركته فارغًا">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                            <?php $__errorArgs = ['total_amount'];
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
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="deposit_amount" value="<?php echo e(old('deposit_amount', $plan->deposit_amount)); ?>"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                            <?php $__errorArgs = ['deposit_amount'];
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
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">عدد الأقساط *</label>
                            <input type="number" min="1" max="36" name="installments_count" value="<?php echo e(old('installments_count', $plan->installments_count)); ?>" required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__errorArgs = ['installments_count'];
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
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">الدورية والسماح</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">وحدة الدورية *</label>
                            <select name="frequency_unit"
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <?php $__currentLoopData = $frequencyUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(old('frequency_unit', $plan->frequency_unit) === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['frequency_unit'];
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
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الفاصل الزمني *</label>
                            <input type="number" min="1" max="12" name="frequency_interval" value="<?php echo e(old('frequency_interval', $plan->frequency_interval)); ?>" required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__errorArgs = ['frequency_interval'];
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
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">فترة السماح (أيام)</label>
                            <input type="number" min="0" max="30" name="grace_period_days" value="<?php echo e(old('grace_period_days', $plan->grace_period_days)); ?>"
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__errorArgs = ['grace_period_days'];
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
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="auto_generate_on_enrollment" value="1" <?php echo e(old('auto_generate_on_enrollment', $plan->auto_generate_on_enrollment) ? 'checked' : ''); ?> class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        إنشاء جدول الأقساط تلقائيًا عند تفعيل التسجيل
                    </label>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $plan->is_active) ? 'checked' : ''); ?> class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        تفعيل الخطة فورًا
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="<?php echo e(route('admin.installments.plans.show', $plan)); ?>" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl shadow">
                        <i class="fas fa-save"></i>
                        تحديث الخطة
                    </button>
                </div>
            </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/plans/edit.blade.php ENDPATH**/ ?>