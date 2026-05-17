

<?php $__env->startSection('title', 'تسجيل طالب — أونلاين'); ?>
<?php $__env->startSection('header', 'تسجيل طالب بالقناة الأونلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تسجيل طالب — أونلاين</h1>
                <p class="text-gray-600 mt-1">بريد الطالب المسجّل في المنصة؛ يُنشأ تسجيل نشط على المجموعة الأونلاين</p>
            </div>
            <a href="<?php echo e(route('admin.online-management.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة لكورسات الأونلاين
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <?php if($courses->isEmpty()): ?>
            <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-4 mb-6">
                لا يوجد كورس نشط بمجموعة أونلاين مفعّلة. أنشئ كورس أونلاين فقط أو فعّل «الحجز الأونلاين» من
                <a href="<?php echo e(route('admin.offline-courses.index')); ?>" class="font-bold text-blue-700 underline">قائمة الكورسات الأوفلاين</a>.
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.online-management.enroll.store')); ?>" method="post" class="space-y-6" <?php if($courses->isEmpty()): ?> aria-disabled="true" <?php endif; ?>>
            <?php echo csrf_field(); ?>

            <?php if($errors->has('error')): ?>
                <div class="rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-100"><?php echo e($errors->first('error')); ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">بريد الطالب *</label>
                    <input type="email" name="student_email" value="<?php echo e(old('student_email')); ?>" required autocomplete="off" dir="ltr"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <?php $__errorArgs = ['student_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select name="offline_course_id" id="offline_course_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php if(old('offline_course_id', $selectedCourseId) == $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?> <?php if($c->online_only): ?> (أونلاين فقط) <?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['offline_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المجموعة *</label>
                    <select name="group_id" id="group_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر الكورس أولاً</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $c->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($g->online_booking_enabled && $g->is_active && $g->status === 'active'): ?>
                                    <option value="<?php echo e($g->id); ?>" data-course="<?php echo e($c->id); ?>" <?php if(old('group_id', $selectedGroupId) == $g->id): echo 'selected'; endif; ?>>
                                        [<?php echo e($c->title); ?>] <?php echo e($g->name); ?> (<?php echo e($g->current_students_online); ?>/<?php echo e($g->max_students_online); ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="mark_fully_paid" value="1" <?php echo e(old('mark_fully_paid') ? 'checked' : ''); ?> class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">تسجيل كمدفوع بالكامل (يُنشئ سجلات مالية عند وجود سعر للكورس)</span>
            </label>

            <div id="online-payment-box" class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-lg border border-gray-200 bg-gray-50">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع (عند الدفع الكامل)</label>
                    <select name="payment_method" id="online_payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="cash" <?php if(old('payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>نقدي</option>
                        <option value="wallet" <?php if(old('payment_method') === 'wallet'): echo 'selected'; endif; ?>>تحويل على محفظة</option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div id="online_wallet_wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المحفظة</label>
                    <select name="wallet_id" id="online_wallet_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر المحفظة</option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wallet->id); ?>" <?php if((string) old('wallet_id') === (string) $wallet->id): echo 'selected'; endif; ?>>
                                <?php echo e($wallet->name); ?> — <?php echo e(\App\Models\Wallet::typeLabel($wallet->type)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات للتسجيل</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" <?php if($courses->isEmpty()): echo 'disabled'; endif; ?> class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none">
                    <i class="fas fa-check"></i>
                    تفعيل التسجيل الأونلاين
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseEl = document.getElementById('offline_course_id');
    const groupEl = document.getElementById('group_id');
    const fullPaidEl = document.querySelector('input[name="mark_fully_paid"]');
    const paymentMethodEl = document.getElementById('online_payment_method');
    const walletWrapEl = document.getElementById('online_wallet_wrapper');
    const walletSelectEl = document.getElementById('online_wallet_id');
    const paymentBoxEl = document.getElementById('online-payment-box');
    if (!courseEl || !groupEl) return;

    function filterGroups() {
        const cid = courseEl.value;
        const opts = groupEl.querySelectorAll('option[data-course]');
        let firstVisible = null;
        opts.forEach(function (opt) {
            const show = !cid || opt.getAttribute('data-course') === cid;
            opt.hidden = !show;
            opt.disabled = !show;
            if (show && !firstVisible) firstVisible = opt;
        });
        const placeholder = groupEl.querySelector('option:not([data-course])');
        if (placeholder) {
            placeholder.hidden = false;
            placeholder.disabled = false;
        }
        if (cid) {
            const current = groupEl.querySelector('option[value="' + groupEl.value + '"]');
            if (!current || current.disabled || current.hidden) {
                groupEl.value = firstVisible ? firstVisible.value : '';
            }
        }
    }
    courseEl.addEventListener('change', filterGroups);
    filterGroups();

    function togglePaymentFields() {
        const isFullyPaid = !!(fullPaidEl && fullPaidEl.checked);
        const isWallet = paymentMethodEl && paymentMethodEl.value === 'wallet';

        if (paymentBoxEl) {
            paymentBoxEl.style.display = isFullyPaid ? '' : 'none';
        }
        if (walletWrapEl) {
            walletWrapEl.style.display = isFullyPaid && isWallet ? '' : 'none';
        }
        if (walletSelectEl) {
            walletSelectEl.required = isFullyPaid && isWallet;
            if (!isFullyPaid || !isWallet) {
                walletSelectEl.value = '';
            }
        }
    }

    if (fullPaidEl) fullPaidEl.addEventListener('change', togglePaymentFields);
    if (paymentMethodEl) paymentMethodEl.addEventListener('change', togglePaymentFields);
    togglePaymentFields();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/online-management/enroll.blade.php ENDPATH**/ ?>