

<?php $__env->startSection('title', 'رواتب الموظفين'); ?>
<?php $__env->startSection('header', 'مسير رواتب الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $months = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $statusLabels = ['pending'=>'معلق','paid'=>'مدفوع','overdue'=>'متأخر','cancelled'=>'ملغى'];
?>
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b bg-gradient-to-l from-blue-50 to-white flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-lg"><i class="fas fa-money-check-alt"></i></div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">رواتب الموظفين — مسير شهري</h2>
                    <p class="text-xs text-slate-600">الأساسي + الإضافات (أوفر تايم) − الخصومات → دفع من المحفظة → مصروف تلقائي</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.expenses.index', ['category' => 'salaries'])); ?>" class="px-3 py-2 rounded-xl border text-sm font-semibold">مصروفات الرواتب</a>
                <a href="<?php echo e(route('admin.employee-additions.index')); ?>" class="px-3 py-2 rounded-xl border text-sm font-semibold">إضافات الراتب</a>
                <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="px-3 py-2 rounded-xl border text-sm font-semibold">خصومات</a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4 border-b bg-slate-50/80">
            <?php $__currentLoopData = [
                ['label'=>'موظفون','value'=>$stats['employees'],'class'=>''],
                ['label'=>'دفعات منشأة','value'=>$stats['generated'],'class'=>'text-violet-700'],
                ['label'=>'معلق للدفع','value'=>$stats['pending'],'class'=>'text-amber-700'],
                ['label'=>'مدفوع','value'=>$stats['paid'],'class'=>'text-emerald-700'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border bg-white p-3">
                    <p class="text-[10px] font-semibold text-slate-500"><?php echo e($c['label']); ?></p>
                    <p class="text-xl font-black tabular-nums <?php echo e($c['class']); ?>"><?php echo e(number_format($c['value'])); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="p-4 flex flex-wrap items-end gap-3 border-b">
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الشهر</label>
                    <select name="month" class="rounded-xl border px-3 py-2 text-sm min-w-[140px]">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php if($month === $m): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">السنة</label>
                    <input type="number" name="year" value="<?php echo e($year); ?>" min="2020" max="2100" class="rounded-xl border px-3 py-2 text-sm w-28">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">عرض</button>
            </form>

            <form method="post" action="<?php echo e(route('admin.employee-salaries.generate')); ?>" class="inline" onsubmit="return confirm('إنشاء دفعات رواتب لجميع الموظفين لهذا الشهر؟');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="month" value="<?php echo e($month); ?>">
                <input type="hidden" name="year" value="<?php echo e($year); ?>">
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
                    <i class="fas fa-file-invoice-dollar ml-1"></i> إنشاء مسير الرواتب
                </button>
            </form>

            <a href="<?php echo e(route('admin.employee-salaries.export', ['month'=>$month,'year'=>$year])); ?>" class="px-4 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-sm font-bold">
                <i class="fas fa-download ml-1"></i> تنزيل CSV
            </a>
        </div>

        <?php if($stats['pending'] > 0): ?>
            <form method="post" action="<?php echo e(route('admin.employee-salaries.pay-batch')); ?>" id="batch-pay-form" class="p-4 bg-amber-50/60 border-b border-amber-100 flex flex-wrap items-end gap-3" onsubmit="return confirm('دفع الرواتب المحددة من المحفظة وتسجيلها في المصروفات؟');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="month" value="<?php echo e($month); ?>">
                <input type="hidden" name="year" value="<?php echo e($year); ?>">
                <div>
                    <label class="block text-xs font-semibold mb-1">المحفظة للدفع الجماعي</label>
                    <select name="wallet_id" required class="rounded-xl border px-3 py-2 text-sm min-w-[200px]">
                        <option value="">— اختر المحفظة —</option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($w->id); ?>"><?php echo e($w->name); ?> (<?php echo e(number_format($w->balance,2)); ?> ج.م)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white text-sm font-bold">دفع المحدد (<?php echo e($stats['pending']); ?> معلق)</button>
                <p class="text-xs text-amber-800 w-full">حدّد الموظفين من الجدول أدناه ثم ادفع دفعة واحدة لكل محدد.</p>
            </form>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-800 text-white text-xs">
                    <tr>
                        <?php if($stats['pending'] > 0): ?><th class="px-3 py-2 w-10"></th><?php endif; ?>
                        <th class="text-right px-3 py-2">الموظف</th>
                        <th class="text-right px-3 py-2">الأساسي</th>
                        <th class="text-right px-3 py-2">خصومات</th>
                        <th class="text-right px-3 py-2">إضافات</th>
                        <th class="text-right px-3 py-2">الصافي</th>
                        <th class="text-right px-3 py-2">الدفعة</th>
                        <th class="text-right px-3 py-2">المصروف</th>
                        <th class="text-right px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $pay = $payments->get($row['employee_id']); ?>
                        <tr class="hover:bg-slate-50 align-middle">
                            <?php if($stats['pending'] > 0): ?>
                                <td class="px-3 py-2">
                                    <?php if($pay && in_array($pay->status, ['pending','overdue'])): ?>
                                        <input type="checkbox" name="payment_ids[]" value="<?php echo e($pay->id); ?>" form="batch-pay-form" class="rounded batch-pay-cb">
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="px-3 py-2">
                                <p class="font-bold text-slate-900"><?php echo e($row['employee_name']); ?></p>
                                <p class="text-[10px] text-slate-500"><?php echo e($row['job_name'] ?? '—'); ?> <?php if($row['employee_code']): ?> · <?php echo e($row['employee_code']); ?><?php endif; ?></p>
                            </td>
                            <td class="px-3 py-2 tabular-nums"><?php echo e(number_format($row['base_salary'],2)); ?></td>
                            <td class="px-3 py-2 tabular-nums text-rose-700">-<?php echo e(number_format($row['total_deductions'],2)); ?></td>
                            <td class="px-3 py-2 tabular-nums text-emerald-700">+<?php echo e(number_format($row['total_additions'],2)); ?></td>
                            <td class="px-3 py-2 tabular-nums font-black text-slate-900"><?php echo e(number_format($row['net_salary'],2)); ?></td>
                            <td class="px-3 py-2 text-xs">
                                <?php if($pay): ?>
                                    <span class="font-mono"><?php echo e($pay->payment_number); ?></span>
                                    <span class="block mt-0.5 font-bold <?php echo e($pay->status === 'paid' ? 'text-emerald-700' : 'text-amber-700'); ?>"><?php echo e($statusLabels[$pay->status] ?? $pay->status); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-400">لم يُنشأ</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <?php if($pay?->expense): ?>
                                    <a href="<?php echo e(route('admin.expenses.show', $pay->expense)); ?>" class="text-blue-700 font-bold"><?php echo e($pay->expense->expense_number); ?></a>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <?php if($pay && in_array($pay->status, ['pending','overdue'])): ?>
                                    <a href="<?php echo e(route('admin.employee-salaries.pay', $pay)); ?>" class="text-sm font-bold text-blue-700">دفع</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="9" class="py-12 text-center text-slate-500">لا يوجد موظفون نشطون باتفاقية أو راتب.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($rows->isNotEmpty()): ?>
                    <tfoot class="bg-slate-100 font-bold text-sm">
                        <tr>
                            <td colspan="<?php echo e($stats['pending'] > 0 ? 5 : 4); ?>" class="px-3 py-3 text-left">إجمالي الصافي (معاينة)</td>
                            <td class="px-3 py-3 tabular-nums"><?php echo e(number_format($stats['total_net_preview'],2)); ?> ج.م</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-salaries\index.blade.php ENDPATH**/ ?>