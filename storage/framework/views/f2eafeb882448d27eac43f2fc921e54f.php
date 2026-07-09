

<?php $__env->startSection('title', 'حجز يدوي مع تقسيط'); ?>
<?php $__env->startSection('header', 'حجز يدوي مع تقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $plans = $plans ?? collect();
    $onlineCourses = $onlineCourses ?? collect();
    $offlineCoursesGroups = $offlineCoursesGroups ?? collect();
?>
<div class="space-y-6">
    <?php echo $__env->make('admin.installments.partials.header', [
        'title' => 'حجز يدوي + خطة تقسيط',
        'description' => 'بريد الطالب + نوع الكورس — يُنشأ التسجيل والتقسيط تلقائياً إن لم يكن موجوداً.',
        'icon' => 'fa-user-plus',
        'iconGradient' => 'from-violet-500 to-purple-600',
        'actions' => [
            ['route' => 'admin.installments.agreements.create', 'label' => 'ربط بتسجيل موجود', 'icon' => 'fa-link'],
            ['route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-list'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.installments.partials.nav', ['active' => 'manual-booking'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
            <form id="manual-installment-form" action="<?php echo e(route('admin.installments.agreements.manual-booking.store')); ?>" method="POST" class="space-y-8">
                <?php echo csrf_field(); ?>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني للطالب *</label>
                    <input type="email" name="student_email" value="<?php echo e(old('student_email')); ?>" required autocomplete="off"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                           placeholder="نفس البريد المستخدم في حساب الطالب">
                    <?php $__errorArgs = ['student_email'];
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

                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">نوع الكورس *</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="course_mode" value="online" class="text-violet-600" <?php echo e(old('course_mode', 'online') === 'online' ? 'checked' : ''); ?>>
                            <span class="text-sm font-medium text-gray-800">أونلاين</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="course_mode" value="offline" class="text-violet-600" <?php echo e(old('course_mode') === 'offline' ? 'checked' : ''); ?>>
                            <span class="text-sm font-medium text-gray-800">أوفلاين</span>
                        </label>
                    </div>
                    <?php $__errorArgs = ['course_mode'];
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

                <div id="block-online" class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">الكورس الأونلاين *</label>
                    <select name="advanced_course_id" id="advanced_course_id"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $onlineCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php echo e((string) old('advanced_course_id') === (string) $c->id ? 'selected' : ''); ?>>
                                <?php echo e($c->title); ?> — <?php echo e(number_format($c->price ?? 0, 2)); ?> ج.م
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

                <div id="block-offline" class="space-y-4 hidden">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الكورس الأوفلاين *</label>
                        <select name="offline_course_id" id="offline_course_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">اختر الكورس</option>
                            <?php $__currentLoopData = $offlineCourses ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e((string) old('offline_course_id') === (string) $c->id ? 'selected' : ''); ?>>
                                    <?php echo e($c->title); ?> — <?php echo e(number_format($c->price ?? 0, 2)); ?> ج.م
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['offline_course_id'];
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
                        <label class="block text-sm font-semibold text-gray-700">المجموعة *</label>
                        <select name="offline_group_id" id="offline_group_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">اختر المجموعة بعد اختيار الكورس</option>
                        </select>
                        <?php $__errorArgs = ['offline_group_id'];
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">خطة التقسيط *</label>
                        <select name="installment_plan_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
                            <option value="">اختر خطة</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" <?php echo e(old('installment_plan_id', $selectedPlanId) == $plan->id ? 'selected' : ''); ?>>
                                    <?php echo e($plan->name); ?> — <?php echo e($plan->course->title ?? 'عامة (مناسبة لأوفلاين أو تجاوز يدوي)'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['installment_plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500">للكورس الأوفلاين اختر خطة «عامة» غير مربوطة بكورس أونلاين.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ البدء *</label>
                        <input type="date" name="start_date" value="<?php echo e(old('start_date', now()->format('Y-m-d'))); ?>" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <?php $__errorArgs = ['start_date'];
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
                        <label class="block text-sm font-semibold text-gray-700">حالة الاتفاقية</label>
                        <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">الحالة الافتراضية (نشط)</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">تفاصيل المبالغ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="total_amount" value="<?php echo e(old('total_amount')); ?>"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                                       placeholder="الخطة أو سعر الكورس">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="deposit_amount" value="<?php echo e(old('deposit_amount')); ?>"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">عدد الأقساط</label>
                            <input type="number" min="1" max="60" name="installments_count" value="<?php echo e(old('installments_count')); ?>"
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                              placeholder="اختياري"><?php echo e(old('notes')); ?></textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl shadow">
                        <i class="fas fa-check"></i>
                        إنشاء الحجز والتقسيط
                    </button>
                </div>
            </form>
        </section>

        <div class="space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-slate-900 mb-4">تنبيهات</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-user-check mt-1 text-violet-500"></i>
                        يجب أن يكون البريد مسجّلاً مسبقاً كمستخدم في المنصة.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-chalkboard mt-1 text-violet-500"></i>
                        للأوفلاين: يجب توفر أماكن في الكورس والمجموعة قبل الإرسال.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-ban mt-1 text-violet-500"></i>
                        لا يُسمح بأكثر من اتفاقية نشطة/متأخرة لنفس تسجيل الكورس.
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const groupsByCourse = <?php echo json_encode($offlineCoursesGroups, 15, 512) ?>;
    const modeInputs = document.querySelectorAll('input[name="course_mode"]');
    const blockOnline = document.getElementById('block-online');
    const blockOffline = document.getElementById('block-offline');
    const selCourse = document.getElementById('advanced_course_id');
    const selOfflineCourse = document.getElementById('offline_course_id');
    const selGroup = document.getElementById('offline_group_id');

    function fillGroups(courseId) {
        if (!selGroup) return;
        const prev = <?php echo json_encode(old('offline_group_id'), 15, 512) ?>;
        selGroup.innerHTML = '<option value=\"\">اختر المجموعة</option>';
        const row = (groupsByCourse || []).find(function (x) { return String(x.id) === String(courseId); });
        if (!row || !row.groups) return;
        row.groups.forEach(function (g) {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name;
            if (prev && String(prev) === String(g.id)) opt.selected = true;
            selGroup.appendChild(opt);
        });
    }

    function syncMode() {
        const mode = document.querySelector('input[name=\"course_mode\"]:checked')?.value || 'online';
        if (mode === 'online') {
            blockOnline.classList.remove('hidden');
            blockOffline.classList.add('hidden');
            if (selCourse) { selCourse.disabled = false; selCourse.required = true; }
            if (selOfflineCourse) { selOfflineCourse.disabled = true; selOfflineCourse.required = false; selOfflineCourse.value = ''; }
            if (selGroup) { selGroup.disabled = true; selGroup.required = false; selGroup.innerHTML = '<option value=\"\">—</option>'; }
        } else {
            blockOnline.classList.add('hidden');
            blockOffline.classList.remove('hidden');
            if (selCourse) { selCourse.disabled = true; selCourse.required = false; selCourse.value = ''; }
            if (selOfflineCourse) { selOfflineCourse.disabled = false; selOfflineCourse.required = true; }
            if (selGroup) { selGroup.disabled = false; selGroup.required = true; }
            fillGroups(selOfflineCourse && selOfflineCourse.value);
        }
    }

    modeInputs.forEach(function (el) { el.addEventListener('change', syncMode); });
    if (selOfflineCourse) selOfflineCourse.addEventListener('change', function () { fillGroups(this.value); });

    syncMode();
    <?php if(old('course_mode') === 'offline' && old('offline_course_id')): ?>
        fillGroups(<?php echo json_encode(old('offline_course_id'), 15, 512) ?>);
    <?php endif; ?>
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\installments\agreements\manual-booking.blade.php ENDPATH**/ ?>