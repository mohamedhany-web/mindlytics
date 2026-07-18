<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير أداء مبيعات — <?php echo e($report['rep']->name); ?></title>
    <style>
        body {
            font-family: xbriyaz, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.5;
            direction: rtl;
            text-align: right;
        }
        table, th, td, p, h2, span, div {
            font-family: xbriyaz, sans-serif;
        }
        .header {
            border-bottom: 3px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo { max-height: 52px; max-width: 140px; }
        .title { font-size: 16pt; font-weight: bold; color: #065f46; }
        .subtitle { font-size: 9pt; color: #475569; }
        .meta { font-size: 8pt; color: #64748b; text-align: left; direction: ltr; }
        h2 {
            font-size: 11pt;
            color: #065f46;
            border-right: 4px solid #10b981;
            padding-right: 8px;
            margin: 14px 0 8px;
        }
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .summary-grid td {
            width: 25%;
            border: 1px solid #d1fae5;
            background: #f0fdf4;
            padding: 8px;
            vertical-align: top;
        }
        .summary-grid .label { font-size: 8pt; color: #64748b; }
        .summary-grid .value { font-size: 12pt; font-weight: bold; color: #065f46; }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: auto;
        }
        table.data th {
            background: #065f46;
            color: #ffffff;
            font-size: 8pt;
            padding: 6px 4px;
            border: 1px solid #047857;
            text-align: center;
        }
        table.data td {
            border: 1px solid #e2e8f0;
            padding: 5px 4px;
            font-size: 8pt;
            vertical-align: top;
            text-align: right;
        }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .text-center { text-align: center; }
        .text-left { text-align: left; direction: ltr; }
        .tone-emerald { color: #047857; font-weight: bold; }
        .tone-amber { color: #b45309; font-weight: bold; }
        .tone-rose { color: #be123c; font-weight: bold; }
        .tone-slate { color: #64748b; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-emerald { background: #d1fae5; color: #065f46; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-rose { background: #ffe4e6; color: #9f1239; }
        .badge-slate { background: #f1f5f9; color: #475569; }
        .note-box {
            border: 1px solid #fcd34d;
            background: #fffbeb;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
<?php
    $rep = $report['rep'];
    $summary = $report['summary'];
    $periodReport = $report['period_report'];
    $logo = \App\Support\SiteBranding::logoDataUri();
?>

<div class="header">
    <table class="header-table">
        <tr>
            <td style="width: 22%;">
                <?php if($logo): ?>
                    <img src="<?php echo e($logo); ?>" alt="Logo" class="logo">
                <?php endif; ?>
            </td>
            <td style="width: 53%;">
                <p class="title">تقرير أداء موظف المبيعات</p>
                <p class="subtitle"><?php echo e(config('app.name', 'Mindlytics')); ?> — قسم المبيعات</p>
                <p class="subtitle"><strong><?php echo e($rep->name); ?></strong> · <?php echo e($report['lead_scope_label']); ?> · <?php echo e($report['group_filter_label'] ?? 'كل المجموعات'); ?></p>
                <p class="subtitle">الفترة: <?php echo e($report['start']->format('Y-m-d')); ?> إلى <?php echo e($report['end']->format('Y-m-d')); ?> (<?php echo e($summary['period_days']); ?> يوماً)</p>
                <?php if($summary['joined_at']): ?>
                    <p class="subtitle">تاريخ الانضمام للمنصة: <?php echo e($summary['joined_at']->format('Y-m-d')); ?></p>
                <?php endif; ?>
            </td>
            <td class="meta" style="width: 25%;">
                <div>تاريخ التقرير: <?php echo e($report['generated_at']->format('Y-m-d H:i')); ?></div>
                <?php if(!empty($report['exported_by'])): ?>
                    <div>أُعد بواسطة: <?php echo e($report['exported_by']); ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<h2>ملخص تنفيذي</h2>
<table class="summary-grid">
    <tr>
        <td><span class="label">المؤشر المركّب</span><span class="value"><?php echo e($summary['composite_score'] ?? '—'); ?></span></td>
        <td><span class="label">إيرادات الفوز</span><span class="value"><?php echo e(number_format($summary['revenue'], 2)); ?> ج.م</span></td>
        <td><span class="label">صفقات مكسوبة</span><span class="value"><?php echo e($summary['won_deals']); ?></span></td>
        <td><span class="label">إجمالي الأنشطة</span><span class="value"><?php echo e($summary['total_activities']); ?></span></td>
    </tr>
    <tr>
        <td><span class="label">أيام عمل في الفترة</span><span class="value"><?php echo e($summary['work_days']); ?></span></td>
        <td><span class="label">أيام دخول النظام</span><span class="value"><?php echo e($summary['days_with_login']); ?></span></td>
        <td><span class="label">أيام بدون دخول</span><span class="value tone-rose"><?php echo e($summary['days_without_login']); ?></span></td>
        <td><span class="label">أيام بنشاط CRM</span><span class="value"><?php echo e($summary['days_with_crm']); ?></span></td>
    </tr>
    <tr>
        <td><span class="label">Leads سجّلها</span><span class="value"><?php echo e($summary['leads_created_by_rep']); ?></span></td>
        <td><span class="label">Leads من الإدارة</span><span class="value"><?php echo e($summary['leads_from_admin']); ?></span></td>
        <td><span class="label">تقارير يومية مُسلَّمة</span><span class="value"><?php echo e($summary['daily_reports_submitted']); ?></span></td>
        <td><span class="label">تقارير يومية ناقصة</span><span class="value tone-amber"><?php echo e($summary['daily_reports_missing']); ?></span></td>
    </tr>
</table>

<?php if(!empty($periodReport['alert_flags'])): ?>
    <div class="note-box">
        <strong>تنبيهات:</strong>
        <?php echo e(implode(' — ', $periodReport['alert_flags'])); ?>

    </div>
<?php endif; ?>

<?php if(count($report['absent_work_days']) > 0): ?>
    <div class="note-box" style="border-color:#fecdd3;background:#fff1f2;">
        <strong>أيام عمل بدون تسجيل دخول:</strong>
        <?php echo e(implode('، ', $report['absent_work_days'])); ?>

    </div>
<?php endif; ?>

<h2>الجدول اليومي — ماذا فعل الموظف كل يوم؟</h2>
<table class="data">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>اليوم</th>
            <th>الحالة</th>
            <th>دخول</th>
            <th>مكالمات</th>
            <th>اجتماعات</th>
            <th>متابعات</th>
            <th>واتساب</th>
            <th>Leads جديدة</th>
            <th>من الإدارة</th>
            <th>تقرير يومي</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $report['daily_rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($row['date']); ?></td>
                <td class="text-center"><?php echo e($row['day_name']); ?></td>
                <td class="text-center">
                    <span class="badge badge-<?php echo e($row['status_tone']); ?>"><?php echo e($row['status_label']); ?></span>
                </td>
                <td class="text-center"><?php echo e($row['logged_in'] ? 'نعم' : 'لا'); ?></td>
                <td class="text-center"><?php echo e($row['calls']); ?></td>
                <td class="text-center"><?php echo e($row['meetings']); ?></td>
                <td class="text-center"><?php echo e($row['followups']); ?></td>
                <td class="text-center"><?php echo e($row['whatsapp']); ?></td>
                <td class="text-center"><?php echo e($row['leads_created']); ?></td>
                <td class="text-center"><?php echo e($row['leads_from_admin']); ?></td>
                <td class="text-center"><?php echo e($row['daily_report_label']); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<div class="page-break"></div>

<h2>مؤشرات الأداء (KPIs)</h2>
<table class="data">
    <thead>
        <tr>
            <th>المؤشر</th>
            <th>الفعلي</th>
            <th>الهدف</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $periodReport['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($line['label'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($line['actual'] ?? '—'); ?></td>
                <td class="text-center"><?php echo e($line['target'] ?? '—'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<h2>تفصيل أنواع الأنشطة</h2>
<table class="data">
    <thead>
        <tr><th>النوع</th><th>العدد</th></tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $report['activity_breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($item['label']); ?></td>
                <td class="text-center"><?php echo e($item['count']); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="2" class="text-center">لا توجد أنشطة في هذه الفترة.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>العملاء المحتملون (Leads) — <?php echo e($report['leads']->count()); ?> سجل</h2>
<table class="data">
    <thead>
        <tr>
            <th>الاسم</th>
            <th>الهاتف</th>
            <th>المجموعة</th>
            <th>المرحلة</th>
            <th>حالة التواصل</th>
            <th>آخر تواصل</th>
            <th>أُنشئ بواسطة</th>
            <th>تاريخ الإنشاء</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $report['leads_with_contact'] ?? $report['leads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $lead = is_array($item) ? $item['lead'] : $item;
                $contactLabel = is_array($item) ? ($item['contact_label'] ?? '—') : '—';
            ?>
            <tr>
                <td><?php echo e($lead->name); ?></td>
                <td class="text-left"><?php echo e($lead->phone ?? '—'); ?></td>
                <td class="text-center"><?php echo e($lead->group?->name ?? '—'); ?></td>
                <td class="text-center"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></td>
                <td class="text-center"><?php echo e($contactLabel); ?></td>
                <td class="text-center"><?php echo e($lead->last_contacted_at?->format('Y-m-d') ?? '—'); ?></td>
                <td class="text-center"><?php echo e($lead->creator?->name ?? '—'); ?></td>
                <td class="text-center"><?php echo e($lead->created_at?->format('Y-m-d')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center">لا توجد Leads ضمن الفلتر المحدد.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="page-break"></div>

<h2>سجل الأنشطة التفصيلي — <?php echo e($report['activities']->count()); ?> نشاط</h2>
<table class="data">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>النوع</th>
            <th>العميل</th>
            <th>العنوان / الملخص</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $report['activities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="text-center"><?php echo e($activity->created_at?->format('Y-m-d H:i')); ?></td>
                <td class="text-center"><?php echo e(\App\Models\SalesActivity::typeLabel($activity->type)); ?></td>
                <td><?php echo e($activity->lead?->name ?? '—'); ?></td>
                <td><?php echo e(\Illuminate\Support\Str::limit($activity->title ?: ($activity->body ?? '—'), 120)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center">لا توجد أنشطة مسجّلة.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <?php echo e(config('app.name', 'Mindlytics')); ?> — تقرير مبيعات سري · يُولَّد تلقائياً من بيانات النظام
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\pdf\sales-employee-report.blade.php ENDPATH**/ ?>