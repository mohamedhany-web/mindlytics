

<?php $__env->startSection('title', 'طلب حجز — ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'طلب حجز كورس أوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <a href="<?php echo e(route('student.offline-courses.booking.catalog')); ?>" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
            <i class="fas fa-arrow-right ml-1"></i> العودة لقائمة الحجز
        </a>
        <h1 class="text-xl font-bold text-gray-900 mt-3"><?php echo e($offlineCourse->title); ?></h1>
        <p class="text-gray-600 mt-1">السعر: <span class="font-bold text-gray-900"><?php echo e(number_format((float) $offlineCourse->price, 2)); ?> ج.م</span></p>
    </div>

    <form action="<?php echo e(route('student.offline-courses.booking.store', $offlineCourse)); ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-5" x-data="{ method: '<?php echo e(old('payment_method', 'bank_transfer')); ?>' }">
        <?php echo csrf_field(); ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">طريقة التحويل *</label>
            <select name="payment_method" x-model="method" required class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                <option value="bank_transfer">تحويل بنكي / تعليمات عامة</option>
                <?php if($wallets->isNotEmpty()): ?>
                    <option value="wallet">محفظة إلكترونية (فودافون كاش / إنستاباي / …)</option>
                <?php endif; ?>
            </select>
            <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php if($wallets->isNotEmpty()): ?>
            <div class="space-y-2" x-show="method === 'wallet' || method === 'bank_transfer'" x-cloak>
                <label class="block text-sm font-medium text-gray-700">
                    حساب التحويل / المحفظة
                    <span class="text-red-600" x-show="method === 'wallet'">*</span>
                    <span class="text-gray-400 font-normal text-xs" x-show="method === 'bank_transfer'">(اختياري للتحويل البنكي)</span>
                </label>
                <select name="wallet_id" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500" x-bind:required="method === 'wallet'">
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($w->id); ?>" <?php if(old('wallet_id') == $w->id): echo 'selected'; endif; ?>>
                            <?php echo e(\App\Models\Wallet::typeLabel($w->type)); ?>

                            <?php if($w->name): ?> — <?php echo e($w->name); ?> <?php endif; ?>
                            <?php if($w->account_number): ?> (<?php echo e($w->account_number); ?>) <?php endif; ?>
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        <?php endif; ?>

        <div x-show="method === 'bank_transfer'" x-cloak class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-sm text-slate-700">
            بعد التحويل ارفع إيصالاً واضحاً. يمكنك عند التحويل البنكي اختيار حساب المنصة من القائمة أعلاه إن وُجد.
        </div>

        <?php if((float) $offlineCourse->price > 0): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة إيصال التحويل *</label>
                <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required class="block w-full text-sm text-gray-600">
                <p class="text-xs text-gray-500 mt-1">صورة واضحة، بحد أقصى 2 ميجابايت (jpg, png).</p>
                <?php $__errorArgs = ['payment_proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        <?php else: ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة إيصال (اختياري)</label>
                <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" class="block w-full text-sm text-gray-600">
                <?php $__errorArgs = ['payment_proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات للطالب (اختياري)</label>
            <textarea name="student_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500" placeholder="اسمك كما يظهر في التحويل، أو أي تفاصيل تساعد المراجعة"><?php echo e(old('student_notes')); ?></textarea>
            <?php $__errorArgs = ['student_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php $__errorArgs = ['error'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-red-600 text-sm"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <button type="submit" class="w-full py-3 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-700 transition-colors">
            إرسال طلب الحجز
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-booking/form.blade.php ENDPATH**/ ?>