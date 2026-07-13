

<?php $__env->startSection('title', 'حساب المدرب - ' . $instructor->name); ?>
<?php $__env->startSection('header', 'حساب المدرب - ' . $instructor->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background: #f8fafc;">
    
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="<?php echo e(route('admin.accounting.instructor-accounts.index')); ?>" class="text-slate-600 hover:text-slate-900 text-sm font-medium inline-flex items-center gap-1 mb-2">
                    <i class="fas fa-arrow-right"></i>
                    العودة إلى حسابات المدربين
                </a>
                <h1 class="text-2xl font-bold text-slate-900"><?php echo e($instructor->name); ?></h1>
                <p class="text-slate-600 mt-1"><?php echo e($instructor->email ?? ''); ?> <?php if($instructor->phone): ?> — <?php echo e($instructor->phone); ?> <?php endif; ?></p>
            </div>
            <a href="<?php echo e(route('admin.salaries.instructor', $instructor)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium">
                <i class="fas fa-money-bill-wave"></i>
                الماليات والدفع
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-amber-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">مطلوب الدفع</p>
            <p class="text-2xl font-black text-amber-700"><?php echo e(number_format($totals['pending'], 2)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-emerald-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">تم الدفع (إجمالي)</p>
            <p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($totals['paid'], 2)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-blue-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">من تفعيلات الطلاب (نسبة الكورس)</p>
            <p class="text-2xl font-black text-blue-700"><?php echo e(number_format($totals['from_activations'], 2)); ?> ج.م</p>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">الاتفاقيات</h2>
            <p class="text-sm text-slate-600 mt-0.5">جميع اتفاقيات هذا المدرب (بما فيها نسبة من الكورس)</p>
        </div>
        <?php if($agreements->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">رقم الاتفاقية</th>
                        <th class="px-6 py-3">العنوان</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ / النسبة</th>
                        <th class="px-6 py-3">من - إلى</th>
                        <th class="px-6 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm"><?php echo e($agr->agreement_number ?? '—'); ?></td>
                        <td class="px-6 py-4 font-medium text-slate-900"><?php echo e($agr->title ?? '—'); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php if(($agr->billing_type ?? '') === 'course_percentage'): ?>
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800">نسبة من الكورس</span>
                                <?php if($agr->advancedCourse): ?><span class="block text-xs text-slate-500 mt-1"><?php echo e($agr->advancedCourse->title); ?></span><?php endif; ?>
                            <?php else: ?>
                                <?php echo e($agr->type_label ?? $agr->type); ?>

                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            <?php if(($agr->billing_type ?? '') === 'course_percentage'): ?>
                                <?php echo e(number_format($agr->course_percentage ?? 0, 2)); ?>%
                            <?php else: ?>
                                <?php echo e(number_format((float)($agr->rate ?? 0), 2)); ?> ج.م
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <?php echo e($agr->start_date ? $agr->start_date->format('Y-m-d') : '—'); ?>

                            <?php if($agr->end_date): ?> — <?php echo e($agr->end_date->format('Y-m-d')); ?> <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($agr->status === 'active'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">نشط</span>
                            <?php elseif($agr->status === 'draft'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">مسودة</span>
                            <?php elseif($agr->status === 'completed'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">مكتمل</span>
                            <?php else: ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800"><?php echo e($agr->status ?? '—'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="px-6 py-8 text-center text-slate-500">لا توجد اتفاقيات لهذا المدرب.</div>
        <?php endif; ?>
    </div>

    
    <?php if($activationPayments->isNotEmpty()): ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-blue-50">
            <h2 class="text-lg font-bold text-slate-900">تفعيلات الطلاب (نسبة من الكورس)</h2>
            <p class="text-sm text-slate-600 mt-0.5">كل تفعيل لطالب في كورس مربوط باتفاقية نسبة — نسبة المدرب وحصته من مبلغ الشراء</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">التاريخ</th>
                        <th class="px-6 py-3">الطالب</th>
                        <th class="px-6 py-3">الكورس</th>
                        <th class="px-6 py-3">مبلغ الشراء (ج.م)</th>
                        <th class="px-6 py-3">نسبة المدرب</th>
                        <th class="px-6 py-3">حصة المدرب (ج.م)</th>
                        <th class="px-6 py-3">حالة الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $activationPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm"><?php echo e($p->created_at?->format('Y-m-d') ?? '—'); ?></td>
                        <td class="px-6 py-4 font-medium"><?php echo e($p->enrollment?->student?->name ?? '—'); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo e($p->course?->title ?? '—'); ?></td>
                        <td class="px-6 py-4"><?php echo e($p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—'); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php if($p->agreement): ?>
                                <?php echo e(number_format($p->agreement->course_percentage ?? 0, 2)); ?>%
                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-700"><?php echo e(number_format($p->amount, 2)); ?> ج.م</td>
                        <td class="px-6 py-4">
                            <?php if($p->status === 'paid'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مدفوع</span>
                            <?php elseif($p->status === 'approved'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">موافق عليه</span>
                            <?php else: ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">قيد المراجعة</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-left">
            <span class="font-bold text-slate-900">إجمالي أرباح التفعيلات: <?php echo e(number_format($activationPayments->sum('amount'), 2)); ?> ج.م</span>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">سجل المدفوعات</h2>
            <p class="text-sm text-slate-600 mt-0.5">جميع المدافيع (قيد المراجعة، موافق عليه، مدفوع)</p>
        </div>
        <?php if($payments->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">رقم المدفوعة</th>
                        <th class="px-6 py-3">الاتفاقية / الوصف</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ</th>
                        <th class="px-6 py-3">الحالة</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm"><?php echo e($p->payment_number); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo e($p->agreement->title ?? $p->description ?? '—'); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo e($p->type_label ?? $p->type); ?></td>
                        <td class="px-6 py-4 font-bold text-slate-900"><?php echo e(number_format($p->amount, 2)); ?> ج.م</td>
                        <td class="px-6 py-4">
                            <?php if($p->status === 'pending'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">قيد المراجعة</span>
                            <?php elseif($p->status === 'approved'): ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">موافق عليه</span>
                            <?php else: ?> <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مدفوع</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($p->status === 'approved'): ?>
                                <a href="<?php echo e(route('admin.salaries.pay', $p)); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg font-medium">دفع وتحويل</a>
                            <?php elseif($p->status === 'paid' && $p->transfer_receipt_path): ?>
                                <a href="<?php echo e(asset('storage/' . $p->transfer_receipt_path)); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm rounded-lg font-medium">إيصال</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="px-6 py-12 text-center text-slate-500">لا توجد مدفوعات مسجّلة.</div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\accounting\instructor-accounts\show.blade.php ENDPATH**/ ?>