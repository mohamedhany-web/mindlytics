@php
    $st = $report['statements'] ?? [];
    $compare = $st['income_compare'] ?? [];
    $position = $st['position'] ?? [];
    $ratios = $st['ratios'] ?? [];
    $priorPeriod = $st['prior_period'] ?? [];
    $currentPeriod = $st['current_period'] ?? [];
    $cashCompare = $st['cash_compare'] ?? [];
    $dupont = $st['dupont'] ?? [];
    $ccc = $st['ccc'] ?? [];
    $executive = $st['executive'] ?? [];
    $recommendations = $st['recommendations'] ?? [];
    $fmtPct = function (?float $pct): string {
        return \App\Support\AccountingAnalytics::formatSignedPct($pct);
    };
    $pctTone = function (?float $pct, string $nature = 'income'): string {
        if ($pct === null || abs($pct) < 0.05) {
            return 'text-slate-500';
        }
        $upIsGood = $nature !== 'cost';
        $good = $upIsGood ? $pct > 0 : $pct < 0;

        return $good ? 'text-emerald-700' : 'text-rose-700';
    };
    $ratioVal = function (?float $value, string $unit): string {
        if ($value === null) {
            return 'n.m.';
        }
        if ($unit === '%') {
            return number_format($value, 1).'%';
        }
        if ($unit === '×') {
            return number_format($value, 2).'×';
        }
        if ($unit === 'يوم') {
            return number_format($value, 1).' يوم';
        }

        return number_format($value, 2);
    };
    $money = fn ($v) => number_format((float) $v, 2);
@endphp

@if(!empty($executive))
<section class="rounded-2xl border border-slate-800 bg-white shadow-lg overflow-hidden print:shadow-none">
    <div class="px-6 py-4 bg-slate-800 text-white">
        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-300">Executive Memorandum</p>
        <h3 class="text-lg font-black mt-1">المذكرة التنفيذية</h3>
        <p class="text-xs text-slate-300 mt-1">{{ $st['entity'] ?? config('app.name') }} — تحليل الأداء المالي</p>
    </div>
    <div class="p-6 space-y-3 text-sm leading-7 text-slate-800">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
            <p><span class="font-bold text-slate-500">إلى:</span> {{ $executive['to'] }}</p>
            <p><span class="font-bold text-slate-500">من:</span> {{ $executive['from'] }}</p>
            <p><span class="font-bold text-slate-500">التاريخ:</span> {{ $executive['date'] }}</p>
            <p><span class="font-bold text-slate-500">الموضوع:</span> {{ $executive['subject'] }}</p>
        </div>
        <p><span class="font-black text-slate-900">الغرض. </span>{{ $executive['purpose'] }}</p>
        <p><span class="font-black text-slate-900">النتائج. </span>{{ $executive['findings'] }}</p>
        <p><span class="font-black text-slate-900">المحركات. </span>{{ $executive['drivers'] }}</p>
        <p class="text-[11px] text-slate-500 border-t border-slate-100 pt-3">{{ $st['basis'] ?? '' }} العملة {{ $st['currency'] ?? 'EGP' }}.</p>
    </div>
</section>
@endif

@if(count($compare))
<section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <p class="text-[11px] font-black tracking-widest text-sky-700">A. TREND ANALYSIS</p>
        <h3 class="text-lg font-black text-slate-900 mt-1">أ. تحليل الاتجاه — أفقي ورأسي</h3>
        <p class="text-xs text-slate-500 mt-1">
            الفترة الحالية {{ $currentPeriod['start'] ?? '—' }} → {{ $currentPeriod['end'] ?? '—' }}
            مقابل السابقة {{ $priorPeriod['start'] ?? '—' }} → {{ $priorPeriod['end'] ?? '—' }}.
            الأفقي = نسبة التغيّر. الرأسي = نسبة من صافي المبيعات.
        </p>
    </div>
    <div class="px-6 py-3 text-sm font-bold text-slate-800">1. التحليل الأفقي والرأسي — قائمة الدخل</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">البند</th>
                    <th class="px-4 py-3 text-left font-semibold">الفترة السابقة</th>
                    <th class="px-4 py-3 text-left font-semibold">الفترة الحالية</th>
                    <th class="px-4 py-3 text-left font-semibold">التغيّر</th>
                    <th class="px-4 py-3 text-left font-semibold">أفقي %</th>
                    <th class="px-4 py-3 text-left font-semibold">رأسي سابق</th>
                    <th class="px-4 py-3 text-left font-semibold">رأسي حالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compare as $row)
                    @php $pad = !empty($row['indent']) ? 'pr-8 text-slate-600' : 'text-slate-800'; @endphp
                    <tr class="border-b border-slate-100 {{ !empty($row['emphasis']) ? 'bg-sky-50 font-bold' : '' }}">
                        <td class="px-4 py-2.5 {{ $pad }} {{ !empty($row['emphasis']) ? 'font-black text-slate-900' : '' }}">{{ $row['label'] }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums">{{ $money($row['prior']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums font-semibold">{{ $money($row['current']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums {{ $pctTone($row['change'] ?? 0, $row['nature'] ?? 'income') }}">{{ $money($row['change']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums font-semibold {{ $pctTone($row['change_pct'] ?? null, $row['nature'] ?? 'income') }}">{{ $fmtPct($row['change_pct'] ?? null) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums text-slate-500">{{ $row['vertical_prior'] !== null ? number_format($row['vertical_prior'], 1).'%' : '—' }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums text-slate-700">{{ $row['vertical_current'] !== null ? number_format($row['vertical_current'], 1).'%' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="px-6 py-3 text-xs text-slate-500">الخلاصة: صافي الدخل يُحسب بعد تكلفة الخدمة ومصروفات البيع (شاملة عمولات البوابات) ومصروفات التشغيل. لا تُحذف أي قيود مصدرية.</p>
</section>
@endif

@if(!empty($position['vertical']))
<section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900">2. التحليل الرأسي — قائمة المركز المالي (% من إجمالي الأصول)</h3>
        <p class="text-xs text-slate-500 mt-1">{{ $position['snapshot_note'] ?? '' }} المعادلة: الأصول = الخصوم + حقوق الملكية.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">البند</th>
                    <th class="px-4 py-3 text-left font-semibold">المبلغ</th>
                    <th class="px-4 py-3 text-left font-semibold">% من الأصول</th>
                </tr>
            </thead>
            <tbody>
                @foreach($position['vertical'] as $row)
                    <tr class="border-b border-slate-100 {{ !empty($row['emphasis']) ? 'bg-emerald-50 font-bold' : '' }}">
                        <td class="px-4 py-2.5 text-slate-800">{{ $row['label'] }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums">{{ $money($row['amount']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums">{{ $row['pct_of_assets'] !== null ? number_format($row['pct_of_assets'], 1).'%' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 text-xs">
        <p>أصول متداولة: <strong>{{ $money($position['current_assets'] ?? 0) }}</strong></p>
        <p>خصوم متداولة: <strong>{{ $money($position['current_liabilities'] ?? 0) }}</strong></p>
        <p>حقوق الملكية: <strong>{{ $money($position['total_equity'] ?? 0) }}</strong> — منها رأس مال مؤسسين {{ $money($position['founder_capital'] ?? 0) }}</p>
        <p class="{{ !empty($position['balances']) ? 'text-emerald-700' : 'text-rose-700' }}">
            التوازن: {{ !empty($position['balances']) ? 'متزن' : 'غير متزن' }}
        </p>
    </div>
</section>
@endif

@if(count($cashCompare))
<section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <p class="text-[11px] font-black tracking-widest text-teal-700">CASH FLOW STATEMENT</p>
        <h3 class="text-lg font-black text-slate-900 mt-1">قائمة التدفقات النقدية (مباشرة) — تشغيل / استثمار / تمويل</h3>
        <p class="text-xs text-slate-500 mt-1">تمويل الجيب يظهر كتدفق تمويلي مقابل المصروف التشغيلي حتى تبقى أرصدة المحافظ كما هي في النظام.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">البند</th>
                    <th class="px-4 py-3 text-left font-semibold">السابق</th>
                    <th class="px-4 py-3 text-left font-semibold">الحالي</th>
                    <th class="px-4 py-3 text-left font-semibold">التغيّر</th>
                    <th class="px-4 py-3 text-left font-semibold">أفقي %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cashCompare as $row)
                    <tr class="border-b border-slate-100 {{ !empty($row['emphasis']) ? 'bg-teal-50 font-bold' : '' }}">
                        <td class="px-4 py-2.5">{{ $row['label'] }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums">{{ $money($row['prior']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums font-semibold">{{ $money($row['current']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums {{ $pctTone($row['change'] ?? 0, $row['nature'] ?? 'income') }}">{{ $money($row['change']) }}</td>
                        <td class="px-4 py-2.5 text-left tabular-nums {{ $pctTone($row['change_pct'] ?? null, $row['nature'] ?? 'income') }}">{{ $fmtPct($row['change_pct'] ?? null) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

@php
    $ratioSections = [
        'profitability' => ['letter' => 'B', 'title' => 'نسب الربحية', 'intro' => 'القدرة على توليد الربح من المبيعات والأصول ورأس المال. المعيار استرشادي لخدمات التعليم — ليس معيار صناعة الأسمنت.'],
        'liquidity' => ['letter' => 'C', 'title' => 'نسب السيولة', 'intro' => 'القدرة على سداد الالتزامات قصيرة الأجل. EBITDA-إلى-الفائدة غير قابل للحساب إن لم يُفصح عن الفائدة.'],
        'solvency' => ['letter' => 'D', 'title' => 'الملاءة والرفع المالي', 'intro' => 'مقياس الدين هو إجمالي الخصوم، كما في الملف عندما لا يُفصَل الدين الحامل للفائدة.'],
        'efficiency' => ['letter' => 'E', 'title' => 'نسب الكفاءة', 'intro' => 'دوران المخزون غير منطبق. الذمم والأصول من لقطة المركز الحالية.'],
        'coverage' => ['letter' => 'F', 'title' => 'نسب التغطية', 'intro' => 'نسب الفائدة وخدمة الدين لا تُحسب دون إفصاح عن الفائدة. تغطية الأصول هي المقياس المعمول به.'],
    ];
@endphp

@foreach($ratioSections as $group => $meta)
    @if(!empty($ratios[$group]))
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <p class="text-[11px] font-black tracking-widest text-violet-700">{{ $meta['letter'] }}. {{ strtoupper($group) }}</p>
            <h3 class="text-lg font-black text-slate-900 mt-1">{{ $meta['letter'] === 'B' ? 'ب' : ($meta['letter'] === 'C' ? 'ج' : ($meta['letter'] === 'D' ? 'د' : ($meta['letter'] === 'E' ? 'هـ' : 'و'))) }}. {{ $meta['title'] }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ $meta['intro'] }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">النسبة</th>
                        <th class="px-4 py-3 text-left font-semibold">السابق</th>
                        <th class="px-4 py-3 text-left font-semibold">الحالي</th>
                        <th class="px-4 py-3 text-left font-semibold">المعيار</th>
                        <th class="px-4 py-3 text-left font-semibold">التغيّر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ratios[$group] as $ratio)
                        @php $nature = ($ratio['better_when'] ?? 'up') === 'down' ? 'cost' : 'income'; @endphp
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-2.5">
                                <p class="font-semibold text-slate-800">{{ $ratio['label'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $ratio['note'] }}</p>
                            </td>
                            <td class="px-4 py-2.5 text-left tabular-nums text-slate-500">{{ $ratioVal($ratio['prior'] ?? null, $ratio['unit'] ?? '') }}</td>
                            <td class="px-4 py-2.5 text-left tabular-nums font-black text-slate-900">{{ $ratioVal($ratio['current'] ?? null, $ratio['unit'] ?? '') }}</td>
                            <td class="px-4 py-2.5 text-left text-slate-600">{{ $ratio['benchmark'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-left tabular-nums font-semibold {{ $pctTone($ratio['change_pct'] ?? null, $nature) }}">{{ $fmtPct($ratio['change_pct'] ?? null) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($group === 'profitability' && !empty($dupont['current']))
            <div class="px-6 py-5 border-t border-slate-100">
                <h4 class="text-sm font-black text-slate-900 mb-2">تحليل دوبونت لـ ROE</h4>
                <p class="text-xs text-slate-500 mb-3">{{ $dupont['note'] ?? '' }} ROE = هامش صافي الربح × دوران الأصول × مضاعف الملكية.</p>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-right">المكوّن</th>
                                <th class="px-3 py-2 text-left">السابق</th>
                                <th class="px-3 py-2 text-left">الحالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t"><td class="px-3 py-2">هامش صافي الربح</td><td class="px-3 py-2 text-left tabular-nums">{{ number_format($dupont['prior']['npm'] ?? 0, 1) }}%</td><td class="px-3 py-2 text-left tabular-nums font-bold">{{ number_format($dupont['current']['npm'] ?? 0, 1) }}%</td></tr>
                            <tr class="border-t"><td class="px-3 py-2">دوران الأصول</td><td class="px-3 py-2 text-left tabular-nums">{{ number_format($dupont['prior']['asset_turnover'] ?? 0, 2) }}×</td><td class="px-3 py-2 text-left tabular-nums font-bold">{{ number_format($dupont['current']['asset_turnover'] ?? 0, 2) }}×</td></tr>
                            <tr class="border-t"><td class="px-3 py-2">مضاعف الملكية (الرفع)</td><td class="px-3 py-2 text-left tabular-nums">{{ number_format($dupont['prior']['equity_multiplier'] ?? 0, 2) }}×</td><td class="px-3 py-2 text-left tabular-nums font-bold">{{ number_format($dupont['current']['equity_multiplier'] ?? 0, 2) }}×</td></tr>
                            <tr class="border-t bg-violet-50 font-black"><td class="px-3 py-2">= العائد على حقوق الملكية (ROE)</td><td class="px-3 py-2 text-left tabular-nums">{{ number_format($dupont['prior']['roe'] ?? 0, 1) }}%</td><td class="px-3 py-2 text-left tabular-nums">{{ number_format($dupont['current']['roe'] ?? 0, 1) }}%</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($group === 'liquidity' && !empty($ccc['components']))
            <div class="px-6 py-5 border-t border-slate-100">
                <h4 class="text-sm font-black text-slate-900 mb-2">دورة التحويل النقدي (CCC)</h4>
                <p class="text-xs text-slate-500 mb-3">{{ $ccc['note'] ?? '' }}</p>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-right">المكوّن (أيام)</th>
                                <th class="px-3 py-2 text-left">السابق</th>
                                <th class="px-3 py-2 text-left">الحالي</th>
                                <th class="px-3 py-2 text-right">المعيار</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ccc['components'] as $comp)
                                <tr class="border-t {{ $comp['key'] === 'ccc' ? 'bg-sky-50 font-black' : '' }}">
                                    <td class="px-3 py-2">{{ $comp['label'] }}</td>
                                    <td class="px-3 py-2 text-left tabular-nums">{{ $comp['prior'] === null ? 'n.m.' : number_format((float) $comp['prior'], 1) }}</td>
                                    <td class="px-3 py-2 text-left tabular-nums">{{ $comp['current'] === null ? 'n.m.' : number_format((float) $comp['current'], 1) }}</td>
                                    <td class="px-3 py-2 text-slate-500">{{ $comp['benchmark'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
    @endif
@endforeach

@if(count($recommendations))
<section class="rounded-2xl border border-slate-800 bg-white shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-slate-800 text-white">
        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-300">Conclusion & Recommendations</p>
        <h3 class="text-lg font-black mt-1">الخلاصة والتوصيات</h3>
    </div>
    <div class="p-6 space-y-4">
        <div>
            <h4 class="font-black text-slate-900 mb-2">الخلاصة</h4>
            <p class="text-sm leading-7 text-slate-700">{{ $executive['findings'] ?? '' }} {{ $executive['drivers'] ?? '' }}</p>
        </div>
        <div>
            <h4 class="font-black text-slate-900 mb-2">التوصيات</h4>
            <ol class="list-decimal pr-5 space-y-2 text-sm text-slate-800 leading-7">
                @foreach($recommendations as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ol>
        </div>
        <p class="text-[11px] text-slate-500">المصدر: سجلات التحصيل والمصروفات والسحوبات والمحافظ داخل النظام. لا تُصدَّر البيانات خارج المنصة عبر هذا التقرير إلا إذا اخترت تنزيل Excel بنفسك.</p>
    </div>
</section>
@endif
