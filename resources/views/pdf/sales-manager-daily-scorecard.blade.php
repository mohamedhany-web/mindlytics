<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: dejavusans, sans-serif; font-size: 11px; color: #0f172a; direction: rtl; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        h2 { font-size: 13px; margin: 14px 0 6px; color: #0f766e; }
        .muted { color: #64748b; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: right; }
        th { background: #0f766e; color: #fff; font-size: 10px; }
        .score { font-size: 22px; font-weight: bold; color: #0f766e; }
        .box { border: 1px solid #e2e8f0; padding: 8px; margin-bottom: 8px; }
        .warn { background: #fffbeb; border-color: #fde68a; }
    </style>
</head>
<body>
@php
    $date = $board['date'] instanceof \Carbon\Carbon ? $board['date']->format('Y-m-d') : $board['date'];
    $s = $board['summary'];
@endphp

<h1>مركز رقابة مدير المبيعات</h1>
<p class="muted">{{ $board['team']->name ?? '—' }} — {{ $date }} — نشاط CRM الموثّق فقط</p>

<div class="box">
    <strong>ملخص الفريق:</strong>
    أعضاء {{ $s['members'] ?? 0 }} |
    متوسط الدرجة {{ $s['avg_score'] ?? 0 }}/100 |
    محاولات {{ $s['call_attempts'] ?? 0 }} |
    ردود {{ $s['calls_answered'] ?? 0 }} |
    مدفوع مؤكد {{ $s['finance_verified_paid'] ?? 0 }} |
    استثناءات {{ $s['exceptions_total'] ?? 0 }}
</div>

@if(($mode ?? 'team') === 'employee' && $row)
    <div class="box">
        <strong>{{ $row['name'] }}</strong>
        <div class="score">{{ $row['verified_score'] }}/100</div>
        @php $recMap = config('sales_manager_scorecard.recommendations', []); @endphp
        <p class="muted">توصية مقترحة: {{ $recMap[$row['suggested_recommendation']] ?? $row['suggested_recommendation'] ?? '—' }}</p>
    </div>
@endif

<h2>درجات الموظفين</h2>
<table>
    <thead>
        <tr>
            <th>الموظف</th>
            <th>الدرجة</th>
            <th>نتائج</th>
            <th>نشاط</th>
            <th>جودة</th>
            <th>CRM</th>
            <th>حضور</th>
            <th>مدفوع مؤكد</th>
            <th>تقرير</th>
        </tr>
    </thead>
    <tbody>
        @foreach($board['rows'] as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['verified_score'] }}</td>
                <td>{{ $r['pillars']['results']['score'] ?? 0 }}</td>
                <td>{{ $r['pillars']['activity']['score'] ?? 0 }}</td>
                <td>{{ $r['pillars']['quality']['score'] ?? 0 }}</td>
                <td>{{ $r['pillars']['crm_discipline']['score'] ?? 0 }}</td>
                <td>{{ $r['pillars']['attendance']['score'] ?? 0 }}</td>
                <td>{{ $r['financial']['finance_verified_paid'] ?? 0 }}</td>
                <td>{{ $r['daily_report_submitted'] ? 'مسلّم' : 'ناقص' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if(!empty($board['exceptions']))
    <h2>استثناءات لا تدخل الدرجة</h2>
    <table>
        <thead><tr><th>الموظف</th><th>الوصف</th><th>العدد</th></tr></thead>
        <tbody>
            @foreach(array_slice($board['exceptions'], 0, 40) as $ex)
                <tr>
                    <td>{{ $ex['employee_name'] ?? ($row['name'] ?? '—') }}</td>
                    <td>{{ $ex['label'] }}</td>
                    <td>{{ $ex['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(($mode ?? '') === 'employee' && $row)
    <h2>أعمدة الدرجة</h2>
    @foreach($row['pillars'] as $pillar)
        <div class="box">
            <strong>{{ $pillar['label'] }} — {{ $pillar['score'] }}/100</strong>
            <div class="muted">{{ implode(' · ', $pillar['details'] ?? []) }}</div>
        </div>
    @endforeach

    @if($row['review'])
        <div class="box">
            <strong>مراجعة المدير:</strong> {{ $row['review']->statusLabel() }}
            — {{ $row['review']->recommendationLabel() }}
            @if($row['review']->manager_notes)
                <div>{{ $row['review']->manager_notes }}</div>
            @endif
            <p class="muted">لا يُنشأ خصم مالي تلقائياً من هذا التقرير.</p>
        </div>
    @endif
@endif

<p class="muted" style="margin-top:16px;">أُنشئ آلياً من Mindlytics — {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
