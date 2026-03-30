

<?php $__env->startSection('title', 'طلبات حجز الكورسات الأوفلاين'); ?>
<?php $__env->startSection('header', 'طلبات حجز الكورسات الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-amber-200 p-5 shadow-sm">
            <p class="text-sm text-gray-600">قيد المراجعة</p>
            <p class="text-3xl font-black text-amber-700"><?php echo e(number_format($pendingCount)); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form method="get" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>قيد المراجعة</option>
                    <option value="approved" <?php if(request('status') === 'approved'): echo 'selected'; endif; ?>>مقبول</option>
                    <option value="rejected" <?php if(request('status') === 'rejected'): echo 'selected'; endif; ?>>مرفوض</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الكورس</label>
                <select name="offline_course_id" class="rounded-lg border-gray-300 text-sm min-w-[200px]">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if((string) request('offline_course_id') === (string) $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">تصفية</button>
            <a href="<?php echo e(route('admin.offline-course-bookings.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">إعادة تعيين</a>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">#</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الطالب</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الكورس</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">المجموعة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الطريقة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-gray-600"><?php echo e($b->id); ?></td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?php echo e($b->user->name ?? '—'); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($b->user->email ?? ''); ?></div>
                            </td>
                            <td class="px-4 py-3 text-gray-800"><?php echo e($b->course->title ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                <?php if($b->requestedGroup): ?>
                                    <?php echo e($b->requestedGroup->name); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo e($b->payment_method === 'wallet' ? 'محفظة' : 'تحويل'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php if($b->status === 'pending'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">قيد المراجعة</span>
                                <?php elseif($b->status === 'approved'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مقبول</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">مرفوض</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap"><?php echo e($b->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.offline-course-bookings.show', $b)); ?>" class="text-blue-600 hover:text-blue-800 font-medium">تفاصيل</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($bookings->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-course-bookings/index.blade.php ENDPATH**/ ?>