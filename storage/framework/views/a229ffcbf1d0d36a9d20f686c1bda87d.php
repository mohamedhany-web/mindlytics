<?php $__env->startSection('title', 'إضافة اتفاقية جديدة - Mindlytics'); ?>
<?php $__env->startSection('header', 'إضافة اتفاقية جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">إضافة اتفاقية جديدة</h2>
                    <p class="text-sm text-slate-600 mt-1">إنشاء اتفاقية عمل جديدة مع أحد المدربين</p>
                </div>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('admin.agreements.store')); ?>" class="px-5 py-6 sm:px-8 lg:px-12">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المدرب <span class="text-red-500">*</span></label>
                    <select name="instructor_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="">اختر المدرب</option>
                        <?php $__empty_1 = true; $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id') == $instructor->id ? 'selected' : ''); ?>>
                                <?php echo e($instructor->name); ?> 
                                <?php if($instructor->phone): ?>
                                    - <?php echo e($instructor->phone); ?>

                                <?php endif; ?>
                                <?php if($instructor->email): ?>
                                    (<?php echo e($instructor->email); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="" disabled>لا يوجد مدربين متاحين</option>
                        <?php endif; ?>
                    </select>
                    <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($instructors->isEmpty()): ?>
                        <p class="mt-1 text-xs text-amber-600 font-medium">
                            <i class="fas fa-exclamation-triangle ml-1"></i>
                            لا يوجد مدربين في النظام. يرجى إضافة مدربين أولاً.
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الاتفاقية <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="course_price" <?php echo e(old('type') == 'course_price' ? 'selected' : ''); ?>>سعر للكورس كاملاً</option>
                        <option value="hourly_rate" <?php echo e(old('type') == 'hourly_rate' ? 'selected' : ''); ?>>سعر للساعة المسجلة</option>
                        <option value="monthly_salary" <?php echo e(old('type') == 'monthly_salary' ? 'selected' : ''); ?>>راتب شهري</option>
                        <option value="course_percentage" <?php echo e(old('type') == 'course_percentage' ? 'selected' : ''); ?>>نسبة من الكورس</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">نسبة من الكورس: يُحتسب للمدرب نسبة من مبلغ كل تفعيل للطالب في الكورس الأونلاين.</p>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="rate-field">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">السعر/المعدل (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="rate" id="rate" step="0.01" min="0" value="<?php echo e(old('rate')); ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" placeholder="0.00" />
                    <p class="mt-1 text-xs text-slate-500" id="rate-help">المبلغ المحدد لكل كورس</p>
                    <?php $__errorArgs = ['rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="course-percentage-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس الأونلاين <span class="text-red-500">*</span></label>
                        <select name="advanced_course_id" id="advanced_course_id" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                            <option value="">اختر المدرب أولاً ثم الكورس</option>
                            <?php $__currentLoopData = $advancedCourses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ac->id); ?>" data-instructor-id="<?php echo e($ac->instructor_id ?? ''); ?>" <?php echo e(old('advanced_course_id') == $ac->id ? 'selected' : ''); ?>><?php echo e($ac->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">تظهر فقط الكورسات المُعيَّنة للمدرب المختار.</p>
                        <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نسبة المدرب (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="course_percentage" id="course_percentage" step="0.01" min="0" max="100" value="<?php echo e(old('course_percentage')); ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" placeholder="مثال: 30" />
                        <p class="mt-1 text-xs text-slate-500">من 0 إلى 100. تُحسب من مبلغ تفعيل الطالب في الكورس.</p>
                        <?php $__errorArgs = ['course_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الاتفاقية <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="مثال: اتفاقية عمل مع المدرب..." />
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="<?php echo e(old('start_date', date('Y-m-d'))); ?>" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="<?php echo e(old('end_date')); ?>" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" />
                    <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="draft" <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                        <option value="suspended" <?php echo e(old('status') == 'suspended' ? 'selected' : ''); ?>>معلق</option>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="وصف مختصر للاتفاقية..."><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">شروط العقد</label>
                    <textarea name="terms" rows="5" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="شروط وأحكام الاتفاقية..."><?php echo e(old('terms')); ?></textarea>
                    <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" placeholder="ملاحظات إضافية..."><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                <a href="<?php echo e(route('admin.agreements.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ الاتفاقية
                </button>
            </div>
        </form>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const rateField = document.getElementById('rate-field');
    const rateInput = document.getElementById('rate');
    const rateHelp = document.getElementById('rate-help');
    const coursePercentageBlock = document.getElementById('course-percentage-fields');
    const advancedCourseId = document.getElementById('advanced_course_id');
    const coursePercentageInput = document.getElementById('course_percentage');

    function filterCoursesByInstructor() {
        const instructorSelect = document.querySelector('select[name="instructor_id"]');
        if (!advancedCourseId || !instructorSelect) return;
        const selectedInstructor = instructorSelect.value;
        const options = advancedCourseId.querySelectorAll('option[data-instructor-id]');
        options.forEach(function(opt) {
            const show = !selectedInstructor || (opt.getAttribute('data-instructor-id') === selectedInstructor);
            opt.style.display = show ? '' : 'none';
            opt.disabled = !show;
            if (opt.value && !show) advancedCourseId.value = '';
        });
        if (advancedCourseId.value) {
            const chosen = advancedCourseId.querySelector('option:checked');
            if (chosen && chosen.disabled) advancedCourseId.value = '';
        }
    }

    function toggleTypeFields() {
        const type = typeSelect.value;
        const isPercentage = type === 'course_percentage';
        if (rateField) rateField.style.display = isPercentage ? 'none' : 'block';
        if (coursePercentageBlock) coursePercentageBlock.style.display = isPercentage ? 'grid' : 'none';
        if (rateInput) {
            rateInput.required = !isPercentage;
            rateInput.value = isPercentage ? '0' : rateInput.value;
        }
        if (advancedCourseId) advancedCourseId.required = isPercentage;
        if (coursePercentageInput) coursePercentageInput.required = isPercentage;
        if (isPercentage) filterCoursesByInstructor();

        if (!isPercentage && rateHelp) {
            if (type === 'course_price') rateHelp.textContent = 'المبلغ المحدد لكل كورس';
            else if (type === 'hourly_rate') rateHelp.textContent = 'المبلغ المحدد لكل ساعة تدريس';
            else if (type === 'monthly_salary') rateHelp.textContent = 'الراتب الشهري الثابت';
        }
    }

    typeSelect.addEventListener('change', toggleTypeFields);
    document.querySelector('select[name="instructor_id"]').addEventListener('change', function() {
        if (document.getElementById('type').value === 'course_percentage') filterCoursesByInstructor();
    });
    toggleTypeFields();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/agreements/create.blade.php ENDPATH**/ ?>