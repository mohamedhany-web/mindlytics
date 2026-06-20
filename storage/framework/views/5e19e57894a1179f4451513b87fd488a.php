

<?php $__env->startSection('title', 'عمولات المبيعات'); ?>
<?php $__env->startSection('header', 'عمولات المبيعات'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('employee.sales._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('employee.sales._hero', [
        'heroTitle' => 'عمولات المبيعات',
        'heroSubtitle' => 'ملخص العمولات المعتمدة والمعلّقة — '.$periodLabel,
        'heroIcon' => 'fa-coins',
        'backUrl' => route('employee.sales.dashboard'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-l from-amber-50 to-white border-b border-amber-100">
            <h2 class="text-lg font-bold text-gray-900">ملخص العمولات — <?php echo e($periodLabel); ?></h2>
            <p class="text-xs text-gray-600 mt-1">المعتمد = بعد اعتماد الإدارة للفوز · المعلّق = won بانتظار الاعتماد</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs text-emerald-700 font-semibold">عمولة معتمدة</p>
                <p class="text-xl font-black text-emerald-900 tabular-nums"><?php echo e(number_format($commissionFromLeads, 2)); ?> ج.م</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs text-sky-700 font-semibold">صفقات معتمدة</p>
                <p class="text-xl font-black text-sky-900"><?php echo e($confirmedWins); ?></p>
            </div>
            <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                <p class="text-xs text-violet-700 font-semibold">قيمة الصفقات</p>
                <p class="text-xl font-black text-violet-900 tabular-nums"><?php echo e(number_format($expectedSum, 2)); ?> ج.م</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs text-amber-700 font-semibold">معلّق (تقدير)</p>
                <p class="text-xl font-black text-amber-900 tabular-nums"><?php echo e(number_format($pendingEst, 2)); ?> ج.م</p>
                <p class="text-[11px] text-amber-700"><?php echo e($pendingLeads->count()); ?> صفقة</p>
            </div>
        </div>
    </section>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200">
        <div>
            <label class="block text-xs text-gray-500 mb-1">العرض</label>
            <select name="view" class="border rounded-lg px-3 py-2 text-sm">
                <option value="month" <?php if($view === 'month'): echo 'selected'; endif; ?>>شهر محدد</option>
                <option value="all" <?php if($view === 'all'): echo 'selected'; endif; ?>>كل الفترات</option>
            </select>
        </div>
        <?php if($view === 'month'): ?>
        <div>
            <label class="block text-xs text-gray-500 mb-1">الشهر</label>
            <input type="month" name="year_month" value="<?php echo e($yearMonth); ?>" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <?php endif; ?>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">تطبيق</button>
    </form>

    <?php if($pendingLeads->isNotEmpty()): ?>
    <section class="rounded-xl bg-white border border-amber-200 overflow-hidden">
        <div class="px-4 py-3 bg-amber-50 border-b border-amber-200">
            <h3 class="font-bold text-amber-900"><i class="fas fa-clock ml-1"></i> صفقات won بانتظار اعتماد الإدارة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-right py-2 px-4">العميل</th>
                    <th class="text-right py-2 px-4">القيمة</th>
                    <th class="text-right py-2 px-4">تقدير العمولة</th>
                    <th class="text-right py-2 px-4"></th>
                </tr></thead>
                <tbody class="divide-y">
                    <?php $__currentLoopData = $pendingLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-2 px-4 font-medium"><?php echo e($pl->name); ?></td>
                        <td class="py-2 px-4"><?php echo e(number_format((float) ($pl->expected_value ?? 0), 2)); ?> ج.م</td>
                        <td class="py-2 px-4 text-amber-700 font-semibold"><?php echo e(number_format($user->calculateSalesCommissionAmount((float) ($pl->expected_value ?? 0)), 2)); ?> ج.م</td>
                        <td class="py-2 px-4"><a href="<?php echo e(route('employee.sales.leads.show', $pl)); ?>" class="text-emerald-600 hover:underline">عرض</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <?php if($confirmedLeads->isNotEmpty()): ?>
    <section class="rounded-xl bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-900">صفقات معتمدة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-right py-2 px-4">العميل</th>
                    <th class="text-right py-2 px-4">التصنيف</th>
                    <th class="text-right py-2 px-4">القيمة</th>
                    <th class="text-right py-2 px-4">العمولة</th>
                    <th class="text-right py-2 px-4">تاريخ الاعتماد</th>
                </tr></thead>
                <tbody class="divide-y">
                    <?php $__currentLoopData = $confirmedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-2 px-4"><a href="<?php echo e(route('employee.sales.leads.show', $cl)); ?>" class="font-medium text-emerald-700 hover:underline"><?php echo e($cl->name); ?></a></td>
                        <td class="py-2 px-4"><?php echo e($cl->category?->name ?? '—'); ?></td>
                        <td class="py-2 px-4"><?php echo e(number_format((float) ($cl->expected_value ?? 0), 2)); ?> ج.م</td>
                        <td class="py-2 px-4 font-semibold text-emerald-700"><?php echo e(number_format((float) ($cl->commission_amount ?? 0), 2)); ?> ج.م</td>
                        <td class="py-2 px-4 text-xs text-gray-600"><?php echo e($cl->won_confirmed_at?->format('Y-m-d') ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\commissions\index.blade.php ENDPATH**/ ?>