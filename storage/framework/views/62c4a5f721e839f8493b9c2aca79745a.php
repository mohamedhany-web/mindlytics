

<?php $__env->startSection('title', 'سجل رسائل الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'messages'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">سجل الرسائل</h3>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..."
                       class="rounded-xl border-slate-300 text-sm">
                <select name="status" class="rounded-xl border-slate-300 text-sm">
                    <option value="">كل الحالات</option>
                    <?php $__currentLoopData = ['sent' => 'مرسلة', 'failed' => 'فاشلة', 'pending' => 'انتظار']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="px-3 py-2 rounded-xl bg-slate-800 text-white text-sm">تصفية</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right">التاريخ</th>
                        <th class="px-4 py-3 text-right">الرقم</th>
                        <th class="px-4 py-3 text-right">الرسالة</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">المرسل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600"><?php echo e($msg->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3 font-mono"><?php echo e($msg->phone_number); ?></td>
                            <td class="px-4 py-3 max-w-md truncate" title="<?php echo e($msg->message); ?>"><?php echo e(Str::limit($msg->message, 80)); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    <?php if($msg->status === 'sent'): ?> bg-emerald-100 text-emerald-800
                                    <?php elseif($msg->status === 'failed'): ?> bg-rose-100 text-rose-800
                                    <?php else: ?> bg-amber-100 text-amber-800 <?php endif; ?>">
                                    <?php echo e($msg->status_text); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($msg->user?->name ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد رسائل بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($messages->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200"><?php echo e($messages->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/messages.blade.php ENDPATH**/ ?>