<?php
    $filter = $filter ?? [];
    $period = $period ?? ($filter['period'] ?? 'month');
    $filterStart = $filter['filterStart'] ?? '';
    $filterEnd = $filter['filterEnd'] ?? '';
    $periodLabel = $periodLabel ?? ($filter['periodLabel'] ?? '');
?>
<div class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-2">الفترة الزمنية</label>
            <select name="period" id="report_period"
                    class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400"
                    onchange="handleReportPeriodChange(this)">
                <option value="day" <?php echo e($period === 'day' ? 'selected' : ''); ?>>اليوم</option>
                <option value="week" <?php echo e($period === 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                <option value="year" <?php echo e($period === 'year' ? 'selected' : ''); ?>>هذه السنة</option>
                <option value="all" <?php echo e($period === 'all' ? 'selected' : ''); ?>>كل الفترات</option>
                <option value="custom" <?php echo e($period === 'custom' ? 'selected' : ''); ?>>فترة مخصصة</option>
            </select>
        </div>
        <div id="custom_start_wrap">
            <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
            <input type="date" name="start_date" id="report_start" value="<?php echo e($filterStart); ?>"
                   class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400"
                   <?php echo e($period !== 'custom' ? 'disabled' : ''); ?> />
        </div>
        <div id="custom_end_wrap">
            <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
            <input type="date" name="end_date" id="report_end" value="<?php echo e($filterEnd); ?>"
                   class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400"
                   <?php echo e($period !== 'custom' ? 'disabled' : ''); ?> />
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                <i class="fas fa-filter"></i>
                تطبيق الفلترة
            </button>
        </div>
    </div>
    <?php if($periodLabel): ?>
        <div class="rounded-xl border border-sky-200 bg-sky-50/80 px-4 py-3 text-sm text-sky-900">
            <i class="fas fa-calendar-alt ml-1 text-sky-600"></i>
            <strong>الفترة النشطة:</strong> <?php echo e($periodLabel); ?>

            <?php if(isset($startDate, $endDate)): ?>
                <span class="text-sky-700">(<?php echo e($startDate->format('Y-m-d')); ?> → <?php echo e($endDate->format('Y-m-d')); ?>)</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <p class="text-xs text-slate-500">
        اختر «فترة مخصصة» لتحديد من/إلى تاريخ. عند اختيار اليوم أو الشهر أو «كل الفترات» تُتجاهل حقول التاريخ تلقائياً.
    </p>
</div>
<script>
function handleReportPeriodChange(sel) {
    const isCustom = sel.value === 'custom';
    const start = document.getElementById('report_start');
    const end = document.getElementById('report_end');
    if (start) {
        start.disabled = !isCustom;
        if (!isCustom) start.value = '';
    }
    if (end) {
        end.disabled = !isCustom;
        if (!isCustom) end.value = '';
    }
    if (!isCustom) {
        sel.form.submit();
    }
}
document.getElementById('reportPeriodForm')?.addEventListener('submit', function () {
    const period = document.getElementById('report_period')?.value;
    const start = document.getElementById('report_start');
    const end = document.getElementById('report_end');
    if (period !== 'custom') {
        if (start) { start.disabled = false; start.removeAttribute('name'); }
        if (end) { end.disabled = false; end.removeAttribute('name'); }
    }
});
</script>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/partials/report-period-filter.blade.php ENDPATH**/ ?>