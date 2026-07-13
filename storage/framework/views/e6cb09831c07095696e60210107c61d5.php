<?php $__env->startSection('title', 'تفاصيل الاتفاقية - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل الاتفاقية'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <!-- Header -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-file-contract text-sky-600"></i>
                    <?php echo e($agreement->title); ?>

                </h2>
                <p class="text-sm text-slate-500 mt-2">رقم الاتفاقية: <span class="font-semibold"><?php echo e($agreement->agreement_number); ?></span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.agreements.edit', $agreement)); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-xl shadow hover:bg-amber-700 transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.agreements.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($stats['total_earned'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">معلق</p>
                <p class="text-2xl font-bold text-amber-600"><?php echo e(number_format($stats['pending_amount'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($stats['total_payments']); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">مدفوع</p>
                <p class="text-2xl font-bold text-emerald-600"><?php echo e($stats['paid_payments']); ?></p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الاتفاقية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">المدرب</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->instructor->name); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($agreement->instructor->phone); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">نوع الاتفاقية</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->type_label); ?></p>
                        </div>
                        <?php if(($agreement->billing_type ?? '') === 'course_percentage'): ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الكورس الأونلاين</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->advancedCourse?->title ?? '—'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">نسبة المدرب</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e(number_format($agreement->course_percentage ?? 0, 2)); ?>%</p>
                        </div>
                        <?php else: ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">السعر/المعدل</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e(number_format($agreement->rate, 2)); ?> ج.م</p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($agreement->status == 'active' ? 'bg-emerald-100 text-emerald-700' : ($agreement->status == 'draft' ? 'bg-gray-100 text-gray-700' : 'bg-rose-100 text-rose-700')); ?>">
                                <?php echo e($agreement->status_label); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->start_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php if($agreement->end_date): ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($agreement->end_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($agreement->description): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700"><?php echo e($agreement->description); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($agreement->terms): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($agreement->terms); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Payments -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <th class="px-6 py-4 text-right">رقم الدفعة</th>
                                <th class="px-6 py-4 text-right">النوع</th>
                                <th class="px-6 py-4 text-right">المبلغ</th>
                                <th class="px-6 py-4 text-right">تفاصيل التفعيل</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                                <th class="px-6 py-4 text-right">التاريخ</th>
                                <th class="px-6 py-4 text-right">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white/80 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $agreement->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4"><?php echo e($payment->payment_number); ?></td>
                                    <td class="px-6 py-4">
                                        <?php echo e($payment->type_label ?? $payment->type); ?>

                                        <?php if($payment->type === 'course_activation' && $payment->enrollment): ?>
                                            <span class="block text-xs text-slate-500 mt-1">الطالب: <?php echo e($payment->enrollment->student->name ?? '—'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-semibold"><?php echo e(number_format($payment->amount, 2)); ?> ج.م</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">
                                        <?php if($payment->type === 'course_activation' && $payment->enrollment): ?>
                                            <?php $enr = $payment->enrollment; ?>
                                            <div>المدفوع: <?php echo e(number_format((float)($enr->final_price ?? 0), 2)); ?> ج.م</div>
                                            <?php if((float)($enr->discount_amount ?? 0) > 0): ?>
                                                <div class="text-emerald-700">خصم: <?php echo e(number_format((float)$enr->discount_amount, 2)); ?> ج.م</div>
                                                <div class="text-slate-500">قبل الخصم: <?php echo e(number_format((float)($enr->original_price ?? 0), 2)); ?> ج.م</div>
                                            <?php endif; ?>
                                            <?php if($enr->invoice_id): ?>
                                                <div class="mt-1">فاتورة: #<?php echo e($enr->invoice_id); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($payment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status == 'approved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')); ?>">
                                            <?php echo e($payment->status_label ?? $payment->status); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500"><?php echo e($payment->created_at->format('Y-m-d')); ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <?php if($payment->status !== 'paid'): ?>
                                                <button type="button"
                                                        onclick="document.getElementById('edit-payment-<?php echo e($payment->id); ?>').classList.toggle('hidden')"
                                                        class="text-xs px-2.5 py-1 rounded-lg bg-sky-100 text-sky-800 hover:bg-sky-200 font-semibold">
                                                    تعديل
                                                </button>
                                                <form method="POST" action="<?php echo e(route('admin.agreements.payments.destroy', $payment)); ?>"
                                                      onsubmit="return confirm('حذف هذه المدفوعة من سجل المدرب؟');" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 hover:bg-rose-200 font-semibold">
                                                        حذف
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400">مدفوع</span>
                                            <?php endif; ?>
                                        </div>
                                        <div id="edit-payment-<?php echo e($payment->id); ?>" class="hidden mt-3 p-3 rounded-xl border border-slate-200 bg-slate-50 text-right">
                                            <form method="POST" action="<?php echo e(route('admin.agreements.payments.update', $payment)); ?>" class="space-y-2">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">المبلغ (ج.م)</label>
                                                    <input type="number" name="amount" step="0.01" min="0" value="<?php echo e($payment->amount); ?>"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">الوصف</label>
                                                    <input type="text" name="description" value="<?php echo e($payment->description); ?>"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">ملاحظات</label>
                                                    <input type="text" name="notes" value="<?php echo e($payment->notes); ?>"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">حفظ</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد مدفوعات</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">إجراءات سريعة</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-3">
                    <a href="<?php echo e(route('admin.agreements.edit', $agreement)); ?>" class="block w-full text-center px-4 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الاتفاقية
                    </a>
                    <?php if($agreement->status == 'active'): ?>
                        <form method="POST" action="<?php echo e(route('admin.agreements.update', $agreement)); ?>" class="inline-block w-full">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="suspended">
                            <button type="submit" class="block w-full text-center px-4 py-2.5 bg-amber-100 text-amber-700 rounded-xl hover:bg-amber-200 transition-all">
                                <i class="fas fa-pause ml-2"></i>
                                تعليق الاتفاقية
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\agreements\show.blade.php ENDPATH**/ ?>