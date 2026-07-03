

<?php $__env->startSection('title', 'طلب — ' . $inquiry->full_name); ?>
<?php $__env->startSection('header', 'تفاصيل الطلب'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.investment._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.investment._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.investment._nav', ['active' => 'inquiries'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.investment._header', [
        'title' => $inquiry->full_name,
        'subtitle' => $inquiry->investorTypeLabel() . ' · ' . ($inquiry->plan?->title ?? 'بدون خطة'),
        'icon' => 'fas fa-user-tie',
        'actions' => '<a href="' . route('admin.investment.inquiries.index') . '" class="' . $invBtnSecondary . '"><i class="fas fa-arrow-right"></i> العودة للقائمة</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <section class="<?php echo e($invSectionClass); ?>">
                <?php echo $__env->make('admin.investment._section-head', ['icon' => 'fas fa-id-card', 'title' => 'بيانات المستثمر'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-slate-500 mb-1">البريد</dt><dd class="font-semibold dir-ltr text-right"><?php echo e($inquiry->email); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">الهاتف</dt><dd class="font-semibold dir-ltr text-right"><?php echo e($inquiry->phone); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">نوع المستثمر</dt><dd class="font-medium"><?php echo e($inquiry->investorTypeLabel()); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">الشركة</dt><dd class="font-medium"><?php echo e($inquiry->company_name ?: '—'); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">الخطة</dt><dd class="font-medium"><?php echo e($inquiry->plan?->title ?? '—'); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">المبلغ المقترح</dt><dd class="font-mono font-bold"><?php echo e($inquiry->proposed_amount ? number_format($inquiry->proposed_amount, 0) . ' ' . $inquiry->currency : '—'); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">تاريخ التقديم</dt><dd><?php echo e($inquiry->created_at?->format('Y-m-d H:i')); ?></dd></div>
                        <div><dt class="text-slate-500 mb-1">IP</dt><dd class="dir-ltr text-right text-xs"><?php echo e($inquiry->ip_address ?? '—'); ?></dd></div>
                    </dl>
                    <?php if($inquiry->experience_notes): ?>
                        <h3 class="font-bold text-slate-900 mt-6 mb-2">الخبرة / الخلفية</h3>
                        <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($inquiry->experience_notes); ?></p>
                    <?php endif; ?>
                    <?php if($inquiry->message): ?>
                        <h3 class="font-bold text-slate-900 mt-6 mb-2">رسالة المستثمر</h3>
                        <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($inquiry->message); ?></p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <div>
            <section class="<?php echo e($invSectionClass); ?>">
                <?php echo $__env->make('admin.investment._section-head', ['icon' => 'fas fa-tasks', 'title' => 'تحديث الحالة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <form method="POST" action="<?php echo e(route('admin.investment.inquiries.update', $inquiry)); ?>" class="p-6 space-y-4">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div>
                        <label class="<?php echo e($invLabelClass); ?>">الحالة</label>
                        <select name="status" class="<?php echo e($invSelectClass); ?>">
                            <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if($inquiry->status === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="<?php echo e($invLabelClass); ?>">ملاحظات داخلية</label>
                        <textarea name="admin_notes" rows="5" class="<?php echo e($invTextareaClass); ?>"><?php echo e(old('admin_notes', $inquiry->admin_notes)); ?></textarea>
                    </div>
                    <?php if($inquiry->reviewer): ?>
                        <p class="text-xs text-slate-500">آخر مراجعة: <?php echo e($inquiry->reviewer->name); ?> — <?php echo e($inquiry->reviewed_at?->format('Y-m-d H:i')); ?></p>
                    <?php endif; ?>
                    <button type="submit" class="<?php echo e($invBtnPrimary); ?> w-full justify-center"><i class="fas fa-save"></i> حفظ</button>
                </form>
                <form method="POST" action="<?php echo e(route('admin.investment.inquiries.destroy', $inquiry)); ?>" class="px-6 pb-6" onsubmit="return confirm('حذف هذا الطلب؟');">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full text-sm text-rose-700 border border-rose-200 rounded-xl py-2.5 hover:bg-rose-50 bg-white">حذف الطلب</button>
                </form>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\investment\inquiries\show.blade.php ENDPATH**/ ?>