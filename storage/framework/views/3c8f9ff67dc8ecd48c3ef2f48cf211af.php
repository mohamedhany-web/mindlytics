<?php $__env->startSection('title', 'KPIs والأداء — المبيعات'); ?>
<?php $__env->startSection('header', 'KPIs والأداء'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('employee.sales._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $w = config('sales_kpi.weights', []);
    $d = $report['day'];
    $wk = $report['week'];
    $mo = $report['month'];
?>
<div class="space-y-6 pb-6">
    <?php echo $__env->make('employee.sales._hero', [
        'heroTitle' => 'لوحة مؤشرات الأداء (KPIs)',
        'heroSubtitle' => 'قياس يومي وأسبوعي وشهري — نتائج '.(int)(($w['results'] ?? 0) * 100).'٪ · نشاط '.(int)(($w['activity'] ?? 0) * 100).'٪ · جودة '.(int)(($w['quality'] ?? 0) * 100).'٪ · التزام '.(int)(($w['discipline'] ?? 0) * 100).'٪',
        'heroIcon' => 'fa-bullseye',
        'backUrl' => route('employee.sales.dashboard'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl border-2 border-emerald-600/30 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide">المؤشر المركّب — شهر <?php echo e($report['reference']->translatedFormat('F Y')); ?></p>
                <p class="text-5xl font-black text-emerald-900 tabular-nums mt-1"><?php echo e($report['composite_month']); ?><span class="text-2xl text-emerald-700/80">/100</span></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $mo['pillars'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl border border-emerald-200 bg-white px-4 py-2 shadow-sm">
                        <p class="text-[10px] text-gray-500 uppercase"><?php echo e($key); ?></p>
                        <p class="text-xl font-black text-gray-900"><?php echo e($p['score'] ?? '—'); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php if(!empty($report['alert_flags'])): ?>
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 space-y-1">
                <?php $__currentLoopData = $report['alert_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><i class="fas fa-shield-halved ml-1 text-rose-600"></i><?php echo e($f); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-sun text-amber-500"></i> اليوم</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Leads جديدة</dt><dd class="font-bold"><?php echo e($d['new_leads']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">مكالمات</dt><dd class="font-bold"><?php echo e($d['calls']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">اجتماعات / ديمو</dt><dd class="font-bold"><?php echo e($d['meetings']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متابعات</dt><dd class="font-bold"><?php echo e($d['followups']); ?></dd></div>
            </dl>
            <?php if(!empty($d['scores'])): ?>
                <p class="text-xs font-bold text-gray-500 mt-4 mb-2">إنجاز مقارنة بالهدف اليومي</p>
                <ul class="text-xs space-y-1">
                    <?php $__currentLoopData = $d['scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk => $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between border-t border-gray-100 pt-1"><span><?php echo e($sk); ?></span><span class="font-mono font-bold <?php if($sv>=80): ?> text-emerald-600 <?php elseif($sv>=50): ?> text-amber-600 <?php else: ?> text-rose-600 <?php endif; ?>"><?php echo e($sv); ?>%</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-calendar-week text-violet-500"></i> الأسبوع</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Leads جديدة</dt><dd class="font-bold"><?php echo e($wk['new_leads']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">صفقات فوز</dt><dd class="font-bold"><?php echo e($wk['won_closed']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">إيراد (قيمة متوقعة)</dt><dd class="font-bold"><?php echo e(number_format($wk['revenue_closed'], 0)); ?> ج.م</dd></div>
            </dl>
            <?php if(!empty($wk['scores'])): ?>
                <p class="text-xs font-bold text-gray-500 mt-4 mb-2">إنجاز أسبوعي</p>
                <ul class="text-xs space-y-1">
                    <?php $__currentLoopData = $wk['scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk => $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between border-t border-gray-100 pt-1"><span><?php echo e($sk); ?></span><span class="font-mono font-bold"><?php echo e($sv); ?>%</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-blue-500"></i> الشهر</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">تحويل %</dt><dd class="font-bold"><?php echo e($mo['conversion_pct'] ?? '—'); ?><?php if($mo['conversion_pct'] !== null): ?>%<?php endif; ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متوسط أول رد (دقيقة)</dt><dd class="font-bold"><?php echo e($mo['avg_response_minutes'] ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">نسبة إغلاق</dt><dd class="font-bold"><?php echo e($mo['closing_ratio_pct'] ?? '—'); ?><?php if($mo['closing_ratio_pct'] !== null): ?>%<?php endif; ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">CSAT متوسط</dt><dd class="font-bold"><?php echo e($mo['csat_avg'] ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">فرص مفتوحة</dt><dd class="font-bold"><?php echo e($mo['open_opportunities']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متوسط دورة البيع (يوم)</dt><dd class="font-bold"><?php echo e($mo['sales_cycle_avg_days'] ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">متابعات متأخرة</dt><dd class="font-bold text-rose-600"><?php echo e($mo['overdue_followups']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">عملاء بلا تواصل كافٍ</dt><dd class="font-bold text-amber-700"><?php echo e($mo['stale_open_leads']); ?></dd></div>
            </dl>
        </section>
    </div>

    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">تفاصيل المؤشرات مقابل الأهداف (الشهر)</h2>
            <p class="text-xs text-gray-500 mt-1">الإيراد هنا = مجموع «القيمة المتوقعة» للصفقات المسجلة كفوز في الشهر.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs font-bold">
                        <th class="px-4 py-3 text-right">المؤشر</th>
                        <th class="px-4 py-3 text-left">الفعلي</th>
                        <th class="px-4 py-3 text-left">الهدف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $mo['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-2.5 font-medium text-gray-900"><?php echo e($row['label']); ?></td>
                            <td class="px-4 py-2.5 tabular-nums text-gray-800"><?php echo e($row['actual'] ?? '—'); ?></td>
                            <td class="px-4 py-2.5 tabular-nums text-gray-500"><?php echo e($row['target'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-xs text-gray-500 text-center max-w-3xl mx-auto">سجّل المكالمات والاجتماعات و«المتابعة» من صفحة كل عميل لرفع دقة النشاط والالتزام. بعد الفوز سجّل CSAT لتحسين مؤشر الجودة.</p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/sales/kpi/index.blade.php ENDPATH**/ ?>