<?php $__env->startSection('title', 'سجل رسائل الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'messages'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'سجل رسائل الواتساب',
        'subtitle' => 'جميع الرسائل المرسلة عبر Bridge مع حالة التسليم.',
        'icon' => 'fas fa-list',
        'actions' => '<a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-plus"></i> رسالة جديدة</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                بحث وتصفية
            </h3>
        </div>
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="<?php echo e($waLabelClass); ?>">بحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="رقم أو نص الرسالة..."
                               class="<?php echo e($waInputClass); ?> pl-10">
                    </div>
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">الحالة</label>
                    <select name="status" class="<?php echo e($waSelectClass); ?>">
                        <option value="">كل الحالات</option>
                        <?php $__currentLoopData = ['sent' => 'مرسلة', 'failed' => 'فاشلة', 'pending' => 'في الانتظار']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-3 flex flex-wrap gap-2">
                    <button type="submit" class="<?php echo e($waBtnDark); ?>"><i class="fas fa-search"></i> تصفية</button>
                    <a href="<?php echo e(route('admin.whatsapp.messages')); ?>" class="<?php echo e($waBtnSecondary); ?>">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($waSectionClass); ?> overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">الرسائل</h3>
            <span class="text-xs font-semibold text-slate-500"><?php echo e($messages->total()); ?> رسالة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-5 py-3 text-right font-semibold">الرقم</th>
                        <th class="px-5 py-3 text-right font-semibold">الرسالة</th>
                        <th class="px-5 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-5 py-3 text-right font-semibold">المرسل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 tabular-nums"><?php echo e($msg->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-5 py-3.5 font-mono text-slate-800 dir-ltr text-right">+<?php echo e($msg->phone_number); ?></td>
                            <td class="px-5 py-3.5 max-w-xs sm:max-w-md">
                                <p class="truncate text-slate-700" title="<?php echo e($msg->message); ?>"><?php echo e(Str::limit($msg->message, 80)); ?></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border
                                    <?php if($msg->status === 'sent'): ?> bg-emerald-100 text-emerald-800 border-emerald-200
                                    <?php elseif($msg->status === 'failed'): ?> bg-rose-100 text-rose-800 border-rose-200
                                    <?php else: ?> bg-amber-100 text-amber-800 border-amber-200 <?php endif; ?>">
                                    <?php if($msg->status === 'sent'): ?><i class="fas fa-check"></i>
                                    <?php elseif($msg->status === 'failed'): ?><i class="fas fa-times"></i>
                                    <?php else: ?><i class="fas fa-clock"></i><?php endif; ?>
                                    <?php echo e($msg->status_text); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600"><?php echo e($msg->user?->name ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="font-semibold text-slate-600">لا توجد رسائل بعد</p>
                                    <a href="<?php echo e(route('admin.whatsapp.send')); ?>" class="<?php echo e($waBtnPrimary); ?> mt-4 text-sm">إرسال أول رسالة</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($messages->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50"><?php echo e($messages->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/whatsapp/messages.blade.php ENDPATH**/ ?>