

<?php $__env->startSection('title', 'تفاصيل اتفاقية التقسيط'); ?>
<?php $__env->startSection('header', 'تفاصيل اتفاقية التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreement = $agreement ?? null;
    $plan = $agreement?->plan;
    $student = $agreement?->student;
    $course = $agreement?->course;
    $payments = $agreement?->payments ?? collect();
    $pendingPayments = $payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date');
    $nextPayment = $pendingPayments->first();
?>
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-emerald-500 via-emerald-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight"><?php echo e($student->name ?? 'طالب غير معروف'); ?></h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'bg-emerald-100 text-emerald-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_ACTIVE,
                            'bg-amber-100 text-amber-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_OVERDUE,
                            'bg-purple-100 text-purple-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_COMPLETED,
                            'bg-rose-100 text-rose-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_CANCELLED,
                            'bg-white/20 text-white' => !in_array($agreement->status, [\App\Models\InstallmentAgreement::STATUS_ACTIVE, \App\Models\InstallmentAgreement::STATUS_OVERDUE, \App\Models\InstallmentAgreement::STATUS_COMPLETED, \App\Models\InstallmentAgreement::STATUS_CANCELLED])
                        ]); ?>"">
                        <span class="w-2 h-2 rounded-full bg-current/60"></span>
                        <?php echo e($statuses[$agreement->status] ?? $agreement->status); ?>

                    </span>
                </div>
                <p class="mt-3 text-white/80 max-w-2xl">
                    الكورس: <?php echo e($course->title ?? 'خطة عامة'); ?> — بدأت في <?php echo e(optional($agreement->start_date)->format('Y-m-d')); ?>. تتبع أدناه جدول الأقساط والمبالغ المستحقة.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="<?php echo e(route('admin.installments.agreements.edit', $agreement)); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل الاتفاقية
                </a>
                <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6">
            <p class="text-sm font-semibold text-sky-500">إجمالي الاتفاقية</p>
            <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($agreement->total_amount ?? 0, 2)); ?> ج.م</p>
            <p class="text-xs text-gray-500 mt-3">القيمة الكاملة التي سيتم سدادها عبر الخطة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <p class="text-sm font-semibold text-emerald-500">الدفعة المقدمة</p>
            <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($agreement->deposit_amount ?? 0, 2)); ?> ج.م</p>
            <p class="text-xs text-gray-500 mt-3">تم تحصيلها عند توقيع الاتفاقية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100.dark:border-gray-700 p-6">
            <p class="text-sm font-semibold text-amber-500">الأقساط المتبقية</p>
            <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e($pendingPayments->count()); ?></p>
            <p class="text-xs text-gray-500 mt-3">القيمة التالية: <?php echo e(optional($nextPayment)->amount ? number_format($nextPayment->amount, 2) . ' ج.م' : '—'); ?></p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <p class="text-sm font-semibold text-purple-500">القسط القادم</p>
            <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(optional($nextPayment)->due_date?->format('Y-m-d') ?? '—'); ?></p>
            <p class="text-xs text-gray-500 mt-3">عدد الأقساط الكلي: <?php echo e($agreement->installments_count); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white.dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-black text-gray-900">تفاصيل الطالب والكورس</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">اسم الطالب</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($student->name ?? 'غير متوفر'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($student->phone ?? 'بدون هاتف'); ?> · <?php echo e($student->email ?? 'بدون بريد'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الكورس</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($course->title ?? 'خطة عامة'); ?></p>
                        <p class="text-xs text-gray-500">سعر الكورس: <?php echo e(number_format($course->price ?? 0, 2)); ?> ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الخطة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($plan->name ?? 'غير محددة'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500.dark:text-gray-400 uppercase">دورية الأقساط</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">كل <?php echo e($plan->frequency_interval ?? '—'); ?> <?php echo e(($plan && $frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit ?? '-')); ?></p>
                        <p class="text-xs text-gray-500">فترة السماح: <?php echo e($plan->grace_period_days ?? 0); ?> يوم</p>
                    </div>
                </div>
                <?php if($agreement->notes): ?>
                    <div class="bg-gray-50 rounded-2xl px-4 py-3 text-xs text-gray-600 leading-relaxed">
                        <strong class="text-sm text-gray-900">ملاحظات الاتفاقية:</strong>
                        <p class="mt-2"><?php echo e($agreement->notes); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-900">جدول الأقساط</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                        <i class="fas fa-stream text-xs"></i>
                        <?php echo e($payments->count()); ?> دفعات
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">تاريخ الاستحقاق</th>
                                <th class="px-4 py-3 text-right">المبلغ</th>
                                <th class="px-4 py-3 text-right">الحالة</th>
                                <th class="px-4 py-3 text-right">ملاحظات</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold"><?php echo e($payment->sequence_number); ?></td>
                                    <td class="px-4 py-3"><?php echo e(optional($payment->due_date)->format('Y-m-d')); ?></td>
                                    <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e(number_format($payment->amount ?? 0, 2)); ?> ج.م</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'bg-emerald-100 text-emerald-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_PAID,
                                                'bg-amber-100 text-amber-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_OVERDUE,
                                                'bg-rose-100 text-rose-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_SKIPPED,
                                                'bg-gray-100 text-gray-700' => $payment->status === \App\Models\InstallmentPayment::STATUS_PENDING,
                                            ]); ?>"">
                                            <?php echo e($paymentStatuses[$payment->status] ?? $payment->status); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3"><?php echo e($payment->notes ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-left">
                                        <form action="<?php echo e(route('admin.installments.agreements.mark-payment', $payment)); ?>" method="POST" class="inline-flex gap-2">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="status" value="<?php echo e($payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? \App\Models\InstallmentPayment::STATUS_PENDING : \App\Models\InstallmentPayment::STATUS_PAID); ?>">
                                            <button type="submit" class="text-xs px-3 py-1 rounded-xl bg-sky-100 text-sky-600 hover:bg-sky-200">
                                                <?php echo e($payment->status === \App\Models\InstallmentPayment::STATUS_PAID ? 'تعيين كقيد الانتظار' : 'وضع علامة كمدفوع'); ?>

                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500 text-sm">لم يتم توليد جدول أقساط بعد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">نظرة سريعة</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-wallet mt-1 text-emerald-500"></i>
                        مجموع ما تم دفعه حتى الآن: <?php echo e(number_format($payments->where('status', \App\Models\InstallmentPayment::STATUS_PAID)->sum('amount'), 2)); ?> ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-balance-scale mt-1 text-emerald-500"></i>
                        المبلغ المتبقي: <?php echo e(number_format(($agreement->total_amount ?? 0) - $payments->where('status', \App\Models\InstallmentPayment::STATUS_PAID)->sum('amount'), 2)); ?> ج.م
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle mt-1 text-emerald-500"></i>
                        الأقساط المتأخرة: <?php echo e($payments->where('status', \App\Models\InstallmentPayment::STATUS_OVERDUE)->count()); ?> قسط
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">إجراءات إضافية</h2>
                <div class="space-y-3">
                    <form action="<?php echo e(route('admin.installments.agreements.destroy', $agreement)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الاتفاقية؟');" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl border border-rose-100 bg-rose-50/70 text-rose-600 hover:border-rose-200 transition-all">
                            <span class="font-semibold text-sm">إلغاء الاتفاقية</span>
                            <i class="fas fa-ban text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\installments\agreements\show.blade.php ENDPATH**/ ?>