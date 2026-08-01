<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $doc_title }}</title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 9pt;
            color: #0f172a;
            line-height: 1.45;
            direction: rtl;
            text-align: right;
        }
        table, th, td, p, h1, h2, span, div {
            font-family: dejavusans, sans-serif;
        }

        .sheet-header {
            border: 2px solid #0f766e;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .sheet-header-top {
            background: #0f766e;
            color: #ffffff;
            padding: 10px 14px;
        }
        .sheet-header-top table { width: 100%; border-collapse: collapse; }
        .sheet-header-top td { vertical-align: middle; padding: 0; color: #ffffff; }
        .brand {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .brand-sub {
            font-size: 8pt;
            opacity: 0.92;
            margin-top: 2px;
        }
        .doc-badge {
            display: inline-block;
            background: #ccfbf1;
            color: #115e59;
            font-size: 8pt;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .sheet-header-meta {
            background: #f0fdfa;
            padding: 8px 12px;
            border-top: 1px solid #99f6e4;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td {
            width: 25%;
            vertical-align: top;
            padding: 2px 6px;
        }
        .meta-label {
            font-size: 7.5pt;
            color: #64748b;
            font-weight: bold;
        }
        .meta-value {
            font-size: 10pt;
            color: #0f172a;
            font-weight: bold;
        }

        .hint {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-right: 4px solid #f59e0b;
            padding: 7px 10px;
            margin-bottom: 10px;
            font-size: 8pt;
            color: #78350f;
        }

        .section-head {
            background: #134e4a;
            color: #ffffff;
            padding: 7px 12px;
            margin: 12px 0 6px;
            border-radius: 6px;
        }
        .section-head table { width: 100%; border-collapse: collapse; }
        .section-head td { color: #ffffff; vertical-align: middle; padding: 0; }
        .section-title { font-size: 11pt; font-weight: bold; }
        .section-count {
            font-size: 8pt;
            background: #ccfbf1;
            color: #134e4a;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
        }

        table.sheet {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: auto;
        }
        table.sheet thead { display: table-header-group; }
        table.sheet tr { page-break-inside: avoid; page-break-after: auto; }
        table.sheet th {
            background: #0f766e;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #0d9488;
            text-align: center;
            vertical-align: middle;
        }
        table.sheet td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-size: 8pt;
            vertical-align: top;
            text-align: right;
            height: 28px;
        }
        table.sheet tr:nth-child(even) td { background: #f8fafc; }
        table.sheet tr:nth-child(odd) td { background: #ffffff; }

        .col-num { width: 3.5%; text-align: center; font-weight: bold; color: #475569; }
        .col-name { width: 14%; }
        .col-phone { width: 11%; text-align: left; direction: ltr; font-weight: bold; }
        .col-interest { width: 12%; }
        .col-stage { width: 8%; text-align: center; }
        .col-prio { width: 6%; text-align: center; }
        .col-sys { width: 12%; color: #475569; font-size: 7.5pt; }
        .col-result { width: 16%; background: #fff7ed !important; }
        .col-notes { width: 17.5%; background: #eff6ff !important; }

        .name-main { font-weight: bold; color: #0f172a; font-size: 8.5pt; }
        .name-sub { font-size: 7pt; color: #64748b; margin-top: 1px; }

        .write-lines {
            color: #94a3b8;
            font-size: 7pt;
            line-height: 1.7;
        }
        .checks {
            font-size: 7pt;
            color: #334155;
            line-height: 1.55;
        }
        .box {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1.2px solid #475569;
            margin-left: 3px;
            vertical-align: middle;
        }

        .stage-pill {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            background: #e2e8f0;
            color: #334155;
        }
        .prio-high { background: #fee2e2; color: #991b1b; }
        .prio-urgent { background: #fecaca; color: #7f1d1d; }
        .prio-normal { background: #e0f2fe; color: #075985; }
        .prio-low { background: #f1f5f9; color: #475569; }

        .empty-state {
            border: 2px dashed #cbd5e1;
            padding: 28px;
            text-align: center;
            color: #64748b;
            margin: 20px 0;
            border-radius: 8px;
        }

        .legend {
            margin-top: 6px;
            font-size: 7.5pt;
            color: #475569;
        }
        .legend strong { color: #0f172a; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php
    use App\Models\SalesLead;
@endphp

{{-- Header --}}
<div class="sheet-header">
    <div class="sheet-header-top">
        <table>
            <tr>
                <td style="width:62%;">
                    <div class="brand">{{ $app_name }}</div>
                    <div class="brand-sub">نموذج متابعة ميداني — قسم المبيعات · يُعبَّأ يدوياً ثم تُسجَّل النتائج على النظام</div>
                </td>
                <td style="width:38%; text-align:left;">
                    <span class="doc-badge">PRINT / PDF FORM</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="sheet-header-meta">
        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">المجموعة</div>
                    <div class="meta-value">{{ $group->name }}</div>
                </td>
                <td>
                    <div class="meta-label">الموظف</div>
                    <div class="meta-value">{{ $employee_label }}</div>
                </td>
                <td>
                    <div class="meta-label">عدد العملاء</div>
                    <div class="meta-value">{{ number_format($leads_total) }}</div>
                </td>
                <td>
                    <div class="meta-label">تاريخ الطباعة</div>
                    <div class="meta-value" style="direction:ltr;text-align:right;">{{ $printed_at->format('Y-m-d H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="hint">
    <strong>طريقة الاستخدام:</strong>
    استخدم هذا النموذج أثناء الاتصال/المتابعة — سجّل نتيجة كل عميل في الخانات الصفراء والزرقاء —
    ثم افتح النظام وأدخل الأكشن (تغيير المرحلة / متابعة / ملاحظة) بناءً على ما كتبته هنا.
</div>

@if($leads_total === 0)
    <div class="empty-state">
        <div style="font-size:12pt;font-weight:bold;margin-bottom:4px;">لا يوجد عملاء في هذه المجموعة للطباعة</div>
        <div>أضف عملاء للمجموعة أو اختر موظفاً لديه عملاء مسندين داخل المجموعة.</div>
    </div>
@else
    @foreach($sections as $sectionIndex => $section)
        @php
            $sectionEmployee = $section['employee'] ?? null;
            $sectionLeads = $section['leads'] ?? collect();
            $sectionName = $sectionEmployee?->name ?? 'بدون إسناد';
        @endphp

        @if($mode === 'all' || $sectionIndex === 0)
            <div class="section-head" @if($mode === 'all' && $sectionIndex > 0) style="page-break-before:always;" @endif>
                <table>
                    <tr>
                        <td style="width:70%;">
                            <span class="section-title">ورقة عمل — {{ $sectionName }}</span>
                            @if($mode === 'all')
                                <span style="font-size:8pt;opacity:.9;margin-right:8px;">مجموعة: {{ $group->name }}</span>
                            @endif
                        </td>
                        <td style="width:30%; text-align:left;">
                            <span class="section-count">{{ $sectionLeads->count() }} عميل</span>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <table class="sheet">
            <thead>
                <tr>
                    <th class="col-num">م</th>
                    <th class="col-name">اسم العميل</th>
                    <th class="col-phone">الهاتف</th>
                    <th class="col-interest">الاهتمام / الشركة</th>
                    <th class="col-stage">المرحلة</th>
                    <th class="col-prio">الأولوية</th>
                    <th class="col-sys">ملاحظات النظام</th>
                    <th class="col-result">نتيجة الاتصال (يدوي)</th>
                    <th class="col-notes">ملاحظات الموظف (يدوي)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sectionLeads as $i => $lead)
                    @php
                        $prio = $lead->priority ?: 'normal';
                        $prioClass = match ($prio) {
                            'urgent' => 'prio-urgent',
                            'high' => 'prio-high',
                            'low' => 'prio-low',
                            default => 'prio-normal',
                        };
                        $interestBits = array_filter([
                            trim((string) ($lead->interest ?? '')),
                            trim((string) ($lead->company ?? '')),
                        ]);
                        $sysNotes = trim((string) ($lead->notes ?? ''));
                        if (mb_strlen($sysNotes) > 90) {
                            $sysNotes = mb_substr($sysNotes, 0, 90).'…';
                        }
                    @endphp
                    <tr>
                        <td class="col-num" style="text-align:center;">{{ $i + 1 }}</td>
                        <td class="col-name">
                            <div class="name-main">{{ $lead->name ?: '—' }}</div>
                            @if($lead->email)
                                <div class="name-sub" style="direction:ltr;text-align:right;">{{ $lead->email }}</div>
                            @endif
                        </td>
                        <td class="col-phone">{{ $lead->phone ?: '—' }}</td>
                        <td class="col-interest">{{ $interestBits !== [] ? implode(' · ', $interestBits) : '—' }}</td>
                        <td class="col-stage">
                            <span class="stage-pill">{{ SalesLead::stageLabel((string) $lead->stage) }}</span>
                        </td>
                        <td class="col-prio">
                            <span class="stage-pill {{ $prioClass }}">{{ SalesLead::priorityLabel($prio) }}</span>
                        </td>
                        <td class="col-sys">{{ $sysNotes !== '' ? $sysNotes : '—' }}</td>
                        <td class="col-result">
                            <div class="checks">
                                <span class="box"></span> تم الاتصال<br>
                                <span class="box"></span> مهتم<br>
                                <span class="box"></span> غير مهتم<br>
                                <span class="box"></span> متابعة / موعد<br>
                                <span class="box"></span> تحويل / حجز
                            </div>
                        </td>
                        <td class="col-notes">
                            <div class="write-lines">
                                _________________________<br>
                                _________________________<br>
                                _________________________
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="legend">
        <strong>تذكير:</strong>
        الخانة الصفراء = نتيجة الاتصال · الخانة الزرقاء = ملاحظاتك اليدوية ·
        بعد الانتهاء افتح النظام وسجّل الأكشن على كل عميل للحفاظ على دقة التقارير والـ KPI.
    </div>
@endif
</body>
</html>
