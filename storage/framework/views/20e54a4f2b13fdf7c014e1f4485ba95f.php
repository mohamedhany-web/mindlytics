

<?php $__env->startSection('title', 'دفعات إرسال الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'batches'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'دفعات الإرسال',
        'subtitle' => 'متابعة الإرسال الجماعي في الخلفية — من تم ومن لم يُرسل.',
        'icon' => 'fas fa-layer-group',
        'actions' => '<a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-paper-plane"></i> إرسال جديد</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">كل الدفعات</h3>
            <form method="GET" class="flex flex-wrap gap-2 text-sm">
                <select name="status" class="<?php echo e($waSelectClass); ?> !py-1.5 !text-xs" onchange="this.form.submit()">
                    <option value="">كل الحالات</option>
                    <?php $__currentLoopData = ['pending' => 'في الانتظار', 'processing' => 'جاري الإرسال', 'completed' => 'اكتمل']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="source_type" class="<?php echo e($waSelectClass); ?> !py-1.5 !text-xs" onchange="this.form.submit()">
                    <option value="">كل المصادر</option>
                    <option value="workshop" <?php if(request('source_type') === 'workshop'): echo 'selected'; endif; ?>>ورش</option>
                    <option value="admin_bulk" <?php if(request('source_type') === 'admin_bulk'): echo 'selected'; endif; ?>>إرسال جماعي</option>
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">#</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">العنوان</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">النتيجة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">التاريخ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($batch->id); ?></td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-900"><?php echo e($batch->title ?: 'دفعة #' . $batch->id); ?></p>
                                <p class="text-[11px] text-slate-500"><?php echo e($batch->source_type === 'workshop' ? 'ورشة' : 'إرسال جماعي'); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                    $badge = match($batch->status) {
                                        'processing' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border <?php echo e($badge); ?>"><?php echo e($batch->statusLabel()); ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm tabular-nums">
                                <span class="text-emerald-700 font-semibold"><?php echo e($batch->sent_count); ?></span>
                                <span class="text-slate-400">/</span>
                                <span class="text-rose-600 font-semibold"><?php echo e($batch->failed_count); ?></span>
                                <span class="text-slate-400">/</span>
                                <span class="text-slate-600"><?php echo e($batch->total_count); ?></span>
                                <p class="text-[10px] text-slate-400">نجح / فشل / إجمالي</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($batch->created_at->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('admin.whatsapp.batches.show', $batch)); ?>" class="<?php echo e($waBtnSecondary); ?> !text-xs !py-1.5">
                                    <i class="fas fa-eye"></i> التفاصيل
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">لا توجد دفعات بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($batches->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200"><?php echo e($batches->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\batches\index.blade.php ENDPATH**/ ?>