<?php

use App\Models\SalesDailyReport;
use App\Models\User;
use App\Services\SalesDailyReportService;
use App\Services\SalesDailyReportsExcelExportService;
use App\Services\SalesKpiService;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$results = ['ok' => true, 'steps' => []];

function step(array &$results, string $name, callable $fn): void
{
    try {
        $out = $fn();
        $results['steps'][$name] = ['pass' => true, 'detail' => $out];
        echo "✓ {$name}\n";
        if ($out !== null && $out !== true) {
            echo '  → '.(is_string($out) ? $out : json_encode($out, JSON_UNESCAPED_UNICODE))."\n";
        }
    } catch (Throwable $e) {
        $results['ok'] = false;
        $results['steps'][$name] = ['pass' => false, 'error' => $e->getMessage()];
        echo "✗ {$name}: {$e->getMessage()}\n";
    }
}

$rep = User::salesEmployees()->where('is_active', true)->first();
if (! $rep) {
    echo "لا يوجد موظف مبيعات نشط — أنشئ مستخدماً بـ employee_job = sales\n";
    exit(1);
}

$svc = app(SalesDailyReportService::class);
$today = now()->startOfDay();

step($results, 'حذف تقرير اختبار سابق لليوم', function () use ($rep) {
    SalesDailyReport::where('user_id', $rep->id)->whereDate('report_date', today())->delete();

    return 'cleared';
});

step($results, 'رفض تسليم ناقص', function () use ($svc, $rep, $today) {
    try {
        $svc->saveReport($rep, $today, ['messages_replied' => 1], [], true);
        throw new RuntimeException('كان يجب رفض التسليم');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return 'مرفوض كما متوقع ('.count($e->errors()).' أخطاء)';
    }
});

step($results, 'حفظ مسودة ناقصة', function () use ($svc, $rep, $today) {
    $r = $svc->saveReport($rep, $today, ['messages_replied' => 5], [], false);

    return "draft id={$r->id} status={$r->status}";
});

step($results, 'تسليم تقرير كامل', function () use ($svc, $rep, $today) {
    $lead = \App\Models\SalesLead::where('assigned_to', $rep->id)->first();
    $contacts = [
        [
            'interaction_type' => 'call',
            'contact_phone' => '01098765432',
            'contact_name' => 'عميل اختبار أ',
            'client_status' => 'مهتم بالعرض',
            'client_problems' => 'السعر مرتفع قليلاً',
            'sales_lead_id' => $lead?->id,
        ],
        [
            'interaction_type' => 'call',
            'contact_phone' => '01122334455',
            'contact_name' => 'عميل اختبار ب',
            'client_status' => 'يراجع مع الإدارة',
            'client_problems' => 'يحتاج خصم إضافي',
        ],
        [
            'interaction_type' => 'meeting',
            'contact_phone' => '01233445566',
            'contact_name' => 'عميل اختبار ج',
            'client_status' => 'جاهز للتسجيل',
            'client_problems' => 'لا توجد مشاكل',
        ],
    ];
    $r = $svc->saveReport($rep, $today, [
        'messages_replied' => 18,
        'leads_qualified' => 4,
        'bookings_from_leads' => 2,
        'numbers_worked' => 10,
        'followups_done' => 6,
        'calls_made' => 2,
        'meetings_held' => 1,
        'calls_answered' => 2,
        'activity_notes' => 'اختبار آلي — نشاط اليوم',
        'productivity_notes' => 'اختبار آلي — إنتاجية',
    ], $contacts, true);

    return "submitted id={$r->id} contacts={$r->contacts()->count()}";
});

step($results, 'منع تعديل بعد التسليم', function () use ($svc, $rep, $today) {
    try {
        $svc->saveReport($rep, $today, ['messages_replied' => 99], [], false);
        throw new RuntimeException('كان يجب منع التعديل');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return 'ممنوع كما متوقع';
    }
});

step($results, 'KPI — نسبة التسليم', function () use ($svc, $rep) {
    $pct = $svc->submissionRatePct($rep->id, now()->startOfMonth(), now());
    $report = app(SalesKpiService::class)->buildReport($rep);
    $line = $report['month']['kpi_lines']['daily_reports'] ?? null;

    return [
        'submission_pct' => $pct,
        'discipline_score' => $report['month']['pillars']['discipline']['score'] ?? null,
        'daily_reports_line' => $line,
    ];
});

step($results, 'تصدير Excel', function () use ($svc) {
    $reports = $svc->reportsQuery(null, now()->subDays(7), now(), 'submitted');
    $ss = app(SalesDailyReportsExcelExportService::class)->buildSpreadsheet($reports, 'اختبار');
    $path = storage_path('app/test-daily-reports.xlsx');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
    $writer->save($path);

    return 'saved '.basename($path).' ('.filesize($path).' bytes, '.$reports->count().' reports)';
});

step($results, 'خصم تلقائي (آخر يوم عمل بدون تقرير)', function () use ($svc, $rep) {
    $date = Carbon::yesterday()->startOfDay();
    for ($i = 0; $i < 7; $i++) {
        if ($svc->isWorkDay($date, $rep)) {
            break;
        }
        $date->subDay();
    }
    if (! $svc->isWorkDay($date, $rep)) {
        return 'تخطي — لا يوجد يوم عمل في آخر 7 أيام';
    }

    SalesDailyReport::where('user_id', $rep->id)->whereDate('report_date', $date)->delete();

    $ded = $svc->applyPenaltyForDate($rep, $date);
    if (! $ded) {
        if (! SalesDailyReportSettings::penaltyEnabled()) {
            return 'الخصم معطّل في الإعدادات';
        }

        throw new RuntimeException('لم يُنشأ خصم لتاريخ '.$date->toDateString());
    }

    $linked = SalesDailyReport::where('user_id', $rep->id)->whereDate('report_date', $date)->value('auto_deduction_id');

    return "date={$date->toDateString()} deduction={$ded->deduction_number} amount={$ded->amount} linked=".($linked ? 'yes' : 'no');
});

echo "\n".($results['ok'] ? 'كل الاختبارات نجحت.' : 'فشل بعض الاختبارات.')."\n";
exit($results['ok'] ? 0 : 1);
