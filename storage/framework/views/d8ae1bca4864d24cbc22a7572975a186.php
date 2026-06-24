<?php $__env->startSection('title', 'تفاصيل المعاملة المالية - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل المعاملة المالية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700']
    ];

    $typeBadges = [
        'income' => ['label' => 'إيراد', 'classes' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fas fa-arrow-down'],
        'expense' => ['label' => 'مصروف', 'classes' => 'bg-rose-100 text-rose-700', 'icon' => 'fas fa-arrow-up'],
        'transfer' => ['label' => 'تحويل', 'classes' => 'bg-blue-100 text-blue-700', 'icon' => 'fas fa-exchange-alt'],
        'refund' => ['label' => 'استرداد', 'classes' => 'bg-orange-100 text-orange-700', 'icon' => 'fas fa-undo'],
    ];
?>

<div class="space-y-10">
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">معاملة رقم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">#<?php echo e($transaction->transaction_number ?? $transaction->id); ?></h2>
                <p class="text-sm text-slate-500 mt-1">أُنشئت في <?php echo e($transaction->created_at->format('Y-m-d H:i')); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('admin.transactions.edit', $transaction)); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                    <i class="fas fa-edit"></i>
                    تعديل المعاملة
                </a>
                <a href="<?php echo e(route('admin.transactions.index')); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-6">
            <!-- معلومات المعاملة الرئيسية -->
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">معلومات المعاملة</h3>
                    <div class="flex items-center gap-3">
                        <?php
                            $typeMeta = $typeBadges[$transaction->type] ?? null;
                            $statusMeta = $statusBadges[$transaction->status] ?? null;
                        ?>
                        <?php if($typeMeta): ?>
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?php echo e($typeMeta['classes']); ?>">
                                <i class="<?php echo e($typeMeta['icon']); ?> text-[10px]"></i>
                                <?php echo e($typeMeta['label']); ?>

                            </span>
                        <?php endif; ?>
                        <?php if($statusMeta): ?>
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?php echo e($statusMeta['classes']); ?>">
                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                <?php echo e($statusMeta['label']); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">المبلغ</span>
                            <span class="text-2xl font-black <?php echo e($transaction->type == 'income' ? 'text-emerald-600' : ($transaction->type == 'expense' ? 'text-rose-600' : 'text-slate-900')); ?>">
                                <?php echo e($transaction->type == 'expense' ? '-' : '+'); ?><?php echo e(number_format($transaction->amount, 2)); ?> ج.م
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">العملة</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->currency ?? 'EGP'); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">الفئة</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->category ?? 'غير محدد'); ?></span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">تاريخ الإنشاء</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->created_at->format('Y-m-d H:i')); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">آخر تحديث</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->updated_at->format('Y-m-d H:i')); ?></span>
                        </div>
                        <?php if($transaction->createdBy): ?>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500">أنشأها</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->createdBy->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- معلومات العميل -->
            <?php if($transaction->user): ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">بيانات العميل</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الاسم</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($transaction->user->name ?? 'غير معروف'); ?></dd>
                    </div>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">رقم الهاتف</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($transaction->user->phone ?? '—'); ?></dd>
                    </div>
                    <?php if($transaction->user->email): ?>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">البريد الإلكتروني</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($transaction->user->email); ?></dd>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500">الدور</dt>
                        <dd class="font-semibold text-slate-900">
                            <?php if($transaction->user->role == 'student'): ?> طالب
                            <?php elseif($transaction->user->role == 'instructor'): ?> مدرب
                            <?php elseif($transaction->user->role == 'admin'): ?> إداري
                            <?php else: ?> <?php echo e($transaction->user->role); ?>

                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </div>
            <?php endif; ?>

            <!-- الروابط المرتبطة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if($transaction->payment): ?>
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">الدفعة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">رقم الدفعة</span>
                            <a href="<?php echo e(route('admin.payments.show', $transaction->payment)); ?>" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                <?php echo e($transaction->payment->payment_number); ?>

                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">المبلغ</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e(number_format($transaction->payment->amount, 2)); ?> ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">طريقة الدفع</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e($transaction->payment->payment_method ?? '—'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($transaction->invoice): ?>
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">الفاتورة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">رقم الفاتورة</span>
                            <a href="<?php echo e(route('admin.invoices.show', $transaction->invoice)); ?>" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                <?php echo e($transaction->invoice->invoice_number); ?>

                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">المبلغ الإجمالي</span>
                            <span class="text-sm font-semibold text-slate-900"><?php echo e(number_format($transaction->invoice->total_amount, 2)); ?> ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">الحالة</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                <?php if($transaction->invoice->status == 'paid'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($transaction->invoice->status == 'pending'): ?> bg-amber-100 text-amber-700
                                <?php else: ?> bg-rose-100 text-rose-700
                                <?php endif; ?>">
                                <?php echo e($transaction->invoice->status == 'paid' ? 'مدفوعة' : ($transaction->invoice->status == 'pending' ? 'معلقة' : 'متأخرة')); ?>

                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- الوصف -->
            <?php if($transaction->description): ?>
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-3">الوصف</h3>
                <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($transaction->description); ?></p>
            </div>
            <?php endif; ?>

            <!-- البيانات الإضافية -->
            <?php if($transaction->metadata && is_array($transaction->metadata) && count($transaction->metadata) > 0): ?>
            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">البيانات الإضافية</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <?php $__currentLoopData = $transaction->metadata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500"><?php echo e($key); ?></dt>
                        <dd class="font-semibold text-slate-900"><?php echo e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value); ?></dd>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </dl>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\transactions\show.blade.php ENDPATH**/ ?>