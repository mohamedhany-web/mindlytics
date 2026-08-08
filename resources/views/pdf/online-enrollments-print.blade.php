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

        .header {
            border: 2px solid #1e40af;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .header-top {
            background: #1e40af;
            color: #ffffff;
            padding: 10px 14px;
        }
        .header-top table { width: 100%; border-collapse: collapse; }
        .header-top td { color: #ffffff; vertical-align: middle; padding: 0; }
        .brand { font-size: 15pt; font-weight: bold; }
        .brand-sub { font-size: 8.5pt; margin-top: 2px; opacity: 0.95; }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e3a8a;
            font-size: 8pt;
            font-weight: bold;
            padding: 4px 10px;
        }
        .header-meta {
            background: #eff6ff;
            padding: 8px 12px;
            border-top: 1px solid #bfdbfe;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td {
            width: 25%;
            vertical-align: top;
            padding: 2px 6px;
        }
        .meta-label { font-size: 7.5pt; color: #64748b; font-weight: bold; }
        .meta-value { font-size: 10pt; color: #0f172a; font-weight: bold; }

        .stats {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .stats td {
            width: 20%;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            text-align: center;
        }
        .stats .num {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            display: block;
        }
        .stats .lbl {
            font-size: 7.5pt;
            color: #64748b;
            font-weight: bold;
            margin-top: 2px;
            display: block;
        }

        .hint {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-right: 4px solid #f59e0b;
            padding: 6px 10px;
            margin-bottom: 8px;
            font-size: 8pt;
            color: #78350f;
        }

        .data {
            width: 100%;
            border-collapse: collapse;
        }
        .data th {
            background: #1e3a8a;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 7px 5px;
            border: 1px solid #1e40af;
            text-align: center;
        }
        .data td {
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        .data tr:nth-child(even) td { background: #f8fafc; }
        .c { text-align: center; }
        .ltr { direction: ltr; text-align: left; }
        .pct {
            font-weight: bold;
            font-size: 10pt;
            color: #1e40af;
        }
        .pct-done {
            font-weight: bold;
            font-size: 10pt;
            color: #047857;
        }
        .done-pill {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            font-size: 7pt;
            font-weight: bold;
            padding: 1px 5px;
            margin-right: 3px;
        }
        .footer-note {
            margin-top: 8px;
            font-size: 7.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <table>
                <tr>
                    <td style="width:70%;">
                        <div class="brand">{{ $app_name }}</div>
                        <div class="brand-sub">{{ $doc_title }}</div>
                    </td>
                    <td style="width:30%; text-align:left;">
                        <span class="badge">طباعة PDF</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-meta">
            <table class="meta-table">
                <tr>
                    <td>
                        <div class="meta-label">تاريخ الطباعة</div>
                        <div class="meta-value">{{ $printed_at->format('Y-m-d H:i') }}</div>
                    </td>
                    <td>
                        <div class="meta-label">الفلاتر المطبّقة</div>
                        <div class="meta-value" style="font-size:8.5pt;">{{ $filter_summary }}</div>
                    </td>
                    <td>
                        <div class="meta-label">المعروض / الإجمالي</div>
                        <div class="meta-value">{{ number_format($shown_count) }} / {{ number_format($total_matching) }}</div>
                    </td>
                    <td>
                        <div class="meta-label">متوسط التقدّم المخزّن</div>
                        <div class="meta-value">{{ number_format($avg_progress, 1) }}%</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <table class="stats">
        <tr>
            <td>
                <span class="num">{{ number_format($total_matching) }}</span>
                <span class="lbl">إجمالي مطابق للفلتر</span>
            </td>
            <td>
                <span class="num">{{ number_format($active_count) }}</span>
                <span class="lbl">نشط</span>
            </td>
            <td>
                <span class="num">{{ number_format($pending_count) }}</span>
                <span class="lbl">في الانتظار</span>
            </td>
            <td>
                <span class="num">{{ number_format($finished_count) }}</span>
                <span class="lbl">أنهى المنهج (100%)</span>
            </td>
            <td>
                <span class="num">{{ number_format($shown_count) }}</span>
                <span class="lbl">صفوف في هذا الملف</span>
            </td>
        </tr>
    </table>

    @if($truncated)
        <div class="hint">
            تم اقتصار التقرير على أول {{ number_format($shown_count) }} تسجيل من أصل {{ number_format($total_matching) }} مطابقة للفلتر (حد الطباعة).
            استخدم فلاتر أضيق أو Excel للتصدير الكامل.
        </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:16%;">اسم الطالب</th>
                <th style="width:11%;">الهاتف</th>
                <th style="width:18%;">الكورس</th>
                <th style="width:9%;">الحالة</th>
                <th style="width:9%;">التقدّم %</th>
                <th style="width:9%;">العناصر</th>
                <th style="width:9%;">مشاهدة الفيديو</th>
                <th style="width:8%;">التسجيل</th>
                <th style="width:7%;">التفعيل</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="c">{{ $row['n'] }}</td>
                    <td>
                        <strong>{{ $row['student_name'] }}</strong>
                        @if($row['email'] !== '—')
                            <div style="font-size:7pt;color:#64748b;">{{ $row['email'] }}</div>
                        @endif
                    </td>
                    <td class="ltr">{{ $row['phone'] }}</td>
                    <td>
                        {{ $row['course'] }}
                        @if($row['year_subject'] !== '—')
                            <div style="font-size:7pt;color:#64748b;">{{ $row['year_subject'] }}</div>
                        @endif
                    </td>
                    <td class="c">{{ $row['status'] }}</td>
                    <td class="c">
                        <span class="{{ $row['finished'] ? 'pct-done' : 'pct' }}">{{ $row['progress'] }}%</span>
                        @if($row['finished'])
                            <div><span class="done-pill">أنهى المنهج</span></div>
                        @endif
                    </td>
                    <td class="c"><strong>{{ $row['items'] }}</strong></td>
                    <td class="c">
                        @if($row['avg_watch'] !== '—')
                            <strong>{{ $row['avg_watch'] }}%</strong>
                        @else
                            —
                        @endif
                    </td>
                    <td class="c">{{ $row['enrolled_at'] }}</td>
                    <td class="c">{{ $row['activated_at'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="c" style="padding:18px;color:#64748b;">لا توجد تسجيلات مطابقة للفلتر.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer-note">
        التقدّم = نسبة اكتمال عناصر المنهج الفعلية · مشاهدة الفيديو = متوسط نسبة مشاهدة المحاضرات.
        الأرقام معروضة بوضوح بعد إعادة الحساب الحي عند إنشاء هذا الملف.
    </p>
</body>
</html>
