<?php $__env->startSection('title', 'تفاصيل اتفاقية التقسيط'); ?>
<?php $__env->startSection('header', 'تفاصيل اتفاقية التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $agreement = $agreement ?? null;
    $plan = $agreement?->plan;
    $student = $agreement?->student;
    $courseModeLabel = $agreement?->student_course_enrollment_id ? 'أونلاين' : ($agreement?->offline_course_enrollment_id ? 'أوفلاين' : '—');
    $payments = $agreement?->payments ?? collect();
    $pendingPayments = $payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date');
    $nextPayment = $pendingPayments->first();
    $pageStats = [
        ['label' => 'إجمالي الاتفاقية', 'value' => number_format($agreement->total_amount ?? 0, 2), 'desc' => 'ج.م — قيمة كاملة', 'icon' => 'fas fa-file-contract', 'theme' => 'sky'],
        ['label' => 'الدفعة المقدمة', 'value' => number_format($agreement->deposit_amount ?? 0, 2), 'desc' => 'ج.م — عند التوقيع', 'icon' => 'fas fa-hand-holding-usd', 'theme' => 'emerald'],
        ['label' => 'أقساط متبقية', 'value' => number_format($pendingPayments->count()), 'desc' => optional($nextPayment)->amount ? number_format($nextPayment->amount, 2) . ' ج.م قادمة' : '—', 'icon' => 'fas fa-clock', 'theme' => 'amber'],
        ['label' => 'القسط القادم', 'value' => optional($nextPayment)->due_date?->format('Y-m-d') ?? '—', 'desc' => $agreement->installments_count . ' قسط إجمالاً', 'icon' => 'fas fa-calendar-day', 'theme' => 'purple'],
    ];
?>
<div class="space-y-6">
    <?php echo $__env->make('admin.installments.partials.header', [
        'title' => $student->name ?? 'طالب غير معروف',
        'description' => $courseModeLabel . ' — ' . $agreement->display_course_title . ' — بدأت ' . optional($agreement->start_date)->format('Y-m-d'),
        'icon' => 'fa-handshake',
        'iconGradient' => 'from-emerald-500 to-teal-600',
        'meta' => ($statuses[$agreement->status] ?? $agreement->status) . ' · ' . ($plan->name ?? 'خطة غير محددة'),
        'actions' => [
            ['route' => 'admin.installments.agreements.edit', 'label' => 'تعديل', 'icon' => 'fa-edit', 'style' => 'primary', 'params' => [$agreement]],
            ['route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-list'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.installments.partials.nav', ['active' => 'agreements'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.installments.partials.stats', ['stats' => $pageStats], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 space-y-6">
                <h2 class="text-lg font-black text-slate-900">تفاصيل الطالب والكورس</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">اسم الطالب</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($student->name ?? 'غير متوفر'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($student->phone ?? 'بدون هاتف'); ?> · <?php echo e($student->email ?? 'بدون بريد'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الكورس</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($agreement->display_course_title); ?></p>
                        <p class="text-xs text-gray-500">النوع: <?php echo e($courseModeLabel); ?> — مرجع السعر: <?php echo e(number_format($agreement->display_course_price, 2)); ?> ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الخطة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900"><?php echo e($plan->name ?? 'غير محددة'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">دورية الأقساط</p>
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

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">جدول الأقساط</h2>
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
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-slate-900 mb-4">نظرة سريعة</h2>
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

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-slate-900 mb-4">إجراءات إضافية</h2>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/agreements/show.blade.php ENDPATH**/ ?>