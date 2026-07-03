

<?php $__env->startSection('title', 'طلب حجز #' . $offlineCourseBooking->id); ?>
<?php $__env->startSection('header', 'طلب حجز أوفلاين #' . $offlineCourseBooking->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl">
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('admin.offline-course-bookings.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-arrow-right ml-2"></i> القائمة
        </a>
        <a href="<?php echo e(route('admin.offline-courses.show', $offlineCourseBooking->course)); ?>" class="inline-flex items-center px-4 py-2 bg-purple-50 text-purple-800 rounded-lg text-sm hover:bg-purple-100">
            صفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-bold text-gray-900">بيانات الطلب</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500">الطالب</dt><dd class="font-semibold text-gray-900"><?php echo e($offlineCourseBooking->user->name); ?></dd></div>
            <div><dt class="text-gray-500">البريد</dt><dd class="text-gray-800"><?php echo e($offlineCourseBooking->user->email); ?></dd></div>
            <div><dt class="text-gray-500">الكورس</dt><dd class="font-semibold text-gray-900"><?php echo e($offlineCourseBooking->course->title); ?></dd></div>
            <?php if($offlineCourseBooking->requestedGroup): ?>
                <div class="sm:col-span-2"><dt class="text-gray-500">المجموعة المطلوبة (رابط الحجز)</dt><dd class="font-semibold text-purple-800"><?php echo e($offlineCourseBooking->requestedGroup->name); ?></dd></div>
            <?php endif; ?>
            <div><dt class="text-gray-500">سعر الكورس</dt><dd class="text-gray-800"><?php echo e(number_format((float) $offlineCourseBooking->course->price, 2)); ?> ج.م</dd></div>
            <div><dt class="text-gray-500">طريقة الدفع</dt><dd class="text-gray-800"><?php echo e($offlineCourseBooking->payment_method === 'wallet' ? 'محفظة إلكترونية' : 'تحويل بنكي'); ?></dd></div>
            <div><dt class="text-gray-500">الاسم</dt><dd class="text-gray-800"><?php echo e($offlineCourseBooking->transfer_name ?: '—'); ?></dd></div>
            <?php if($offlineCourseBooking->wallet): ?>
                <div><dt class="text-gray-500">قناة التحويل</dt><dd class="text-gray-800"><?php echo e(\App\Models\Wallet::typeLabel($offlineCourseBooking->wallet->type)); ?> <?php if($offlineCourseBooking->wallet->name): ?> — <?php echo e($offlineCourseBooking->wallet->name); ?> <?php endif; ?></dd></div>
            <?php endif; ?>
            <div><dt class="text-gray-500">الحالة</dt><dd class="font-semibold"><?php echo e($offlineCourseBooking->status); ?></dd></div>
            <div><dt class="text-gray-500">تاريخ الإرسال</dt><dd class="text-gray-800"><?php echo e($offlineCourseBooking->created_at?->format('Y-m-d H:i')); ?></dd></div>
        </dl>
        <?php if($offlineCourseBooking->student_notes): ?>
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">ملاحظات الطالب</p>
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo e($offlineCourseBooking->student_notes); ?></p>
            </div>
        <?php endif; ?>
        <?php if($offlineCourseBooking->payment_proof): ?>
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-2">إيصال التحويل</p>
                <a href="<?php echo e(asset('storage/' . $offlineCourseBooking->payment_proof)); ?>" target="_blank" rel="noopener" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-image ml-2"></i> عرض الصورة
                </a>
            </div>
        <?php endif; ?>
        <?php if($offlineCourseBooking->admin_notes): ?>
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">ملاحظات الإدارة</p>
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo e($offlineCourseBooking->admin_notes); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($offlineCourseBooking->isPending()): ?>
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">قبول الطلب وإضافة للمجموعة</h2>
            <p class="text-sm text-gray-600">عند الموافقة يُنشأ تسجيل نشط للطالب، وتُحسب الفاتورة/الدفعة بالكامل بمبلغ سعر الكورس (إن كان أكبر من صفر).</p>
            <form action="<?php echo e(route('admin.offline-course-bookings.approve', $offlineCourseBooking)); ?>" method="post" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المجموعة *</label>
                    <select name="group_id" required class="w-full max-w-md rounded-lg border-gray-300">
                        <option value="">— اختر مجموعة نشطة —</option>
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($g->id); ?>" <?php if(! $g->canEnroll()): echo 'disabled'; endif; ?>>
                                <?php echo e($g->name); ?> — متاح <?php echo e($g->availableSeats()); ?> / <?php echo e($g->max_students); ?>

                                <?php if(! $g->canEnroll()): ?> (غير متاحة) <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-lg border-gray-300"><?php echo e(old('admin_notes')); ?></textarea>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700">موافقة وتفعيل التسجيل</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-red-100 p-6">
            <h2 class="text-lg font-bold text-red-800 mb-2">رفض الطلب</h2>
            <form action="<?php echo e(route('admin.offline-course-bookings.reject', $offlineCourseBooking)); ?>" method="post" class="space-y-3">
                <?php echo csrf_field(); ?>
                <textarea name="admin_notes" rows="2" class="w-full rounded-lg border-gray-300" placeholder="سبب الرفض (اختياري)"><?php echo e(old('admin_notes')); ?></textarea>
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">رفض الطلب</button>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
            تمت معالجة هذا الطلب
            <?php if($offlineCourseBooking->reviewed_at): ?>
                في <?php echo e($offlineCourseBooking->reviewed_at->format('Y-m-d H:i')); ?>

            <?php endif; ?>
            <?php if($offlineCourseBooking->assignedGroup): ?>
                — المجموعة: <?php echo e($offlineCourseBooking->assignedGroup->name); ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\offline-course-bookings\show.blade.php ENDPATH**/ ?>