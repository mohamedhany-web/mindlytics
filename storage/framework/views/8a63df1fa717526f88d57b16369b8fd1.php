

<?php $__env->startSection('title', 'رواتب الموظفين'); ?>
<?php $__env->startSection('header', 'مسير رواتب الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $months = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $statusLabels = ['pending'=>'معلق','paid'=>'مدفوع','overdue'=>'متأخر','cancelled'=>'ملغى'];
    $periodLabel = ($months[$month] ?? $month).' '.$year;

    $cardThemes = [
        'blue'   => ['border' => 'border-blue-200/70', 'bg' => 'from-white via-white to-blue-50/60', 'label' => 'text-blue-800/80', 'value' => 'from-blue-700 to-sky-600', 'icon' => 'from-blue-500 to-sky-600', 'desc' => 'text-blue-700/70'],
        'violet' => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'amber'  => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'emerald'=> ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'sky'    => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'indigo' => ['border' => 'border-indigo-200/70', 'bg' => 'from-white via-white to-indigo-50/60', 'label' => 'text-indigo-800/80', 'value' => 'from-indigo-700 to-violet-600', 'icon' => 'from-indigo-500 to-violet-600', 'desc' => 'text-indigo-700/70'],
    ];

    $statCards = [
        ['label' => 'موظفون في المسير', 'value' => number_format($stats['employees']), 'icon' => 'fas fa-users', 'theme' => 'blue', 'desc' => 'نشطون باتفاقية أو راتب'],
        ['label' => 'دفعات منشأة', 'value' => number_format($stats['generated']), 'icon' => 'fas fa-file-invoice-dollar', 'theme' => 'violet', 'desc' => 'لفترة '.$periodLabel],
        ['label' => 'معلق للدفع', 'value' => number_format($stats['pending']), 'icon' => 'fas fa-hourglass-half', 'theme' => 'amber', 'desc' => number_format($stats['total_net_pending'], 2).' ج.م صافي'],
        ['label' => 'مدفوع', 'value' => number_format($stats['paid']), 'icon' => 'fas fa-check-circle', 'theme' => 'emerald', 'desc' => number_format($stats['total_net_paid'], 2).' ج.م صافي'],
        ['label' => 'إجمالي الصافي', 'value' => number_format($stats['total_net_preview'], 0), 'icon' => 'fas fa-coins', 'theme' => 'sky', 'desc' => 'ج.م — معاينة الشهر'],
        ['label' => 'المحافظ المتاحة', 'value' => number_format($wallets->sum('balance'), 0), 'icon' => 'fas fa-wallet', 'theme' => 'indigo', 'desc' => 'ج.م — للدفع من المحفظة'],
    ];

    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400';
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">رواتب الموظفين</h2>
                    <p class="text-xs text-slate-600">مسير <?php echo e($periodLabel); ?> — أساسي + إضافات − خصومات → دفع من المحفظة → مصروف تلقائي</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.expenses.index', ['category' => 'salaries'])); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-receipt text-rose-600"></i>
                    مصروفات الرواتب
                </a>
                <a href="<?php echo e(route('admin.employee-additions.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-plus-circle text-emerald-600"></i>
                    إضافات الراتب
                </a>
                <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-minus-circle text-amber-600"></i>
                    الخصومات
                </a>
                <a href="<?php echo e(route('admin.wallets.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-wallet text-indigo-600"></i>
                    المحافظ
                </a>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $theme = $cardThemes[$card['theme']] ?? $cardThemes['blue']; ?>
            <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-5 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold <?php echo e($theme['label']); ?> mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-3xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="<?php echo e($card['icon']); ?> text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-medium <?php echo e($theme['desc']); ?> truncate"><?php echo e($card['desc']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($stats['pending'] > 0): ?>
        <section class="rounded-2xl border border-amber-200 bg-amber-50 shadow-lg overflow-hidden">
            <div class="px-4 py-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-amber-200 flex items-center justify-center text-amber-600">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">تنبيه مسير الرواتب</p>
                        <p class="text-lg font-black text-slate-900"><?php echo e($stats['pending']); ?> راتب/رواتب بانتظار الدفع</p>
                        <p class="text-sm text-amber-900/80 mt-1">حدّد الموظفين من الجدول ثم ادفع من المحفظة — يُسجَّل مصروف «رواتب» تلقائياً.</p>
                    </div>
                </div>
                <div class="rounded-xl bg-white/80 border border-amber-100 p-3 shadow-sm min-w-[200px]">
                    <p class="text-[10px] font-semibold text-slate-500">صافي معلّق</p>
                    <p class="text-xl font-black text-amber-800 tabular-nums"><?php echo e(number_format($stats['total_net_pending'], 2)); ?> ج.م</p>
                </div>
            </div>
        </section>
    <?php endif; ?>

    
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80">
            <h3 class="text-sm font-black text-slate-800"><i class="fas fa-sliders-h text-indigo-600 ml-1"></i> الفترة والإجراءات</h3>
        </div>
        <div class="p-4 flex flex-col xl:flex-row xl:items-end gap-4 flex-wrap">
            <form method="get" class="flex flex-wrap items-end gap-3 flex-1">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">الشهر</label>
                    <select name="month" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm min-w-[150px] focus:ring-2 focus:ring-indigo-500">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php if($month === $m): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">السنة</label>
                    <input type="number" name="year" value="<?php echo e($year); ?>" min="2020" max="2100" class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm w-28 focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">
                    <i class="fas fa-search"></i> عرض المسير
                </button>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <form method="post" action="<?php echo e(route('admin.employee-salaries.generate')); ?>" class="inline" onsubmit="return confirm('إنشاء دفعات رواتب لجميع الموظفين لشهر <?php echo e($periodLabel); ?>؟');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="month" value="<?php echo e($month); ?>">
                    <input type="hidden" name="year" value="<?php echo e($year); ?>">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md">
                        <i class="fas fa-file-invoice-dollar"></i>
                        إنشاء مسير الرواتب
                    </button>
                </form>
                <a href="<?php echo e(route('admin.employee-salaries.export', ['month'=>$month,'year'=>$year])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-sm font-bold hover:bg-emerald-100">
                    <i class="fas fa-download"></i>
                    تنزيل CSV
                </a>
            </div>
        </div>

        <?php if($stats['pending'] > 0): ?>
            <div class="px-4 pb-4 border-t border-slate-100 pt-4">
                <form method="post" action="<?php echo e(route('admin.employee-salaries.pay-batch')); ?>" id="batch-pay-form" class="flex flex-wrap items-end gap-3 rounded-xl border border-dashed border-amber-300 bg-amber-50/40 p-4" onsubmit="return confirm('دفع الرواتب المحددة من المحفظة وتسجيلها في المصروفات؟');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="month" value="<?php echo e($month); ?>">
                    <input type="hidden" name="year" value="<?php echo e($year); ?>">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المحفظة للدفع الجماعي</label>
                        <select name="wallet_id" required class="<?php echo e($inputClass); ?>">
                            <option value="">— اختر المحفظة —</option>
                            <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($w->id); ?>"><?php echo e($w->name); ?> — <?php echo e(number_format($w->balance, 2)); ?> ج.م</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold shadow-md">
                        <i class="fas fa-paper-plane"></i>
                        دفع المحدد
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </section>

    
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/80">
            <h3 class="text-sm font-black text-slate-800">
                <i class="fas fa-table text-indigo-600 ml-1"></i>
                تفاصيل مسير <?php echo e($periodLabel); ?>

            </h3>
            <span class="text-xs font-semibold text-slate-500"><?php echo e($rows->count()); ?> موظف</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <?php if($stats['pending'] > 0): ?>
                            <th class="px-4 py-3 w-10">
                                <span class="sr-only">تحديد</span>
                            </th>
                        <?php endif; ?>
                        <th class="text-right px-4 py-3 font-semibold">الموظف</th>
                        <th class="text-right px-4 py-3 font-semibold">الأساسي</th>
                        <th class="text-right px-4 py-3 font-semibold">خصومات</th>
                        <th class="text-right px-4 py-3 font-semibold">إضافات</th>
                        <th class="text-right px-4 py-3 font-semibold">الصافي</th>
                        <th class="text-right px-4 py-3 font-semibold">الدفعة</th>
                        <th class="text-right px-4 py-3 font-semibold">المصروف</th>
                        <th class="text-right px-4 py-3 font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $pay = $payments->get($row['employee_id']); ?>
                        <tr class="hover:bg-slate-50/80 transition-colors align-middle">
                            <?php if($stats['pending'] > 0): ?>
                                <td class="px-4 py-3">
                                    <?php if($pay && in_array($pay->status, ['pending','overdue'])): ?>
                                        <input type="checkbox" name="payment_ids[]" value="<?php echo e($pay->id); ?>" form="batch-pay-form" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs shrink-0">
                                        <?php echo e(mb_substr($row['employee_name'], 0, 1)); ?>

                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900"><?php echo e($row['employee_name']); ?></p>
                                        <p class="text-[11px] text-slate-500"><?php echo e($row['job_name'] ?? '—'); ?><?php if($row['employee_code']): ?> · <?php echo e($row['employee_code']); ?><?php endif; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 tabular-nums text-slate-700"><?php echo e(number_format($row['base_salary'], 2)); ?></td>
                            <td class="px-4 py-3 tabular-nums">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700">−<?php echo e(number_format($row['total_deductions'], 2)); ?></span>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">+<?php echo e(number_format($row['total_additions'], 2)); ?></span>
                            </td>
                            <td class="px-4 py-3 tabular-nums font-black text-slate-900"><?php echo e(number_format($row['net_salary'], 2)); ?></td>
                            <td class="px-4 py-3">
                                <?php if($pay): ?>
                                    <span class="font-mono text-xs text-slate-600"><?php echo e($pay->payment_number); ?></span>
                                    <?php
                                        $badgeClass = match($pay->status) {
                                            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'overdue' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            default => 'bg-amber-100 text-amber-800 border-amber-200',
                                        };
                                    ?>
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold border <?php echo e($badgeClass); ?>"><?php echo e($statusLabels[$pay->status] ?? $pay->status); ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 font-medium">لم يُنشأ</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <?php if($pay?->expense): ?>
                                    <a href="<?php echo e(route('admin.expenses.show', $pay->expense)); ?>" class="inline-flex items-center gap-1 text-indigo-700 font-bold hover:underline">
                                        <i class="fas fa-link text-[10px]"></i>
                                        <?php echo e($pay->expense->expense_number); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if($pay && in_array($pay->status, ['pending','overdue'])): ?>
                                    <a href="<?php echo e(route('admin.employee-salaries.pay', $pay)); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm">
                                        <i class="fas fa-wallet"></i> دفع
                                    </a>
                                <?php elseif($pay && $pay->status === 'paid'): ?>
                                    <span class="text-xs font-semibold text-emerald-600"><i class="fas fa-check ml-1"></i> مكتمل</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($stats['pending'] > 0 ? 9 : 8); ?>" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-slate-500">
                                    <i class="fas fa-users-slash text-3xl text-slate-300"></i>
                                    <p class="font-semibold">لا يوجد موظفون نشطون باتفاقية أو راتب</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($rows->isNotEmpty()): ?>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="<?php echo e($stats['pending'] > 0 ? 5 : 4); ?>" class="px-4 py-4 text-sm font-bold text-slate-700">إجمالي الصافي (معاينة الشهر)</td>
                            <td class="px-4 py-4 text-lg font-black text-indigo-700 tabular-nums"><?php echo e(number_format($stats['total_net_preview'], 2)); ?> ج.م</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/employee-salaries/index.blade.php ENDPATH**/ ?>