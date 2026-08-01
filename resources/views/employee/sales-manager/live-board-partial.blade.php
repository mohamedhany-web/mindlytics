@php
    $current = $block['current'] ?? null;
    $minutesLeft = $block['minutes_left'] ?? null;
@endphp
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">لوحة الفريق الحية</h2>
            <p class="text-sm text-slate-500">{{ $team->name ?? 'الفريق' }} · {{ $day->format('Y-m-d') }} · تحديث كل دقيقة</p>
        </div>
        <div class="text-xs font-semibold px-3 py-2 rounded-xl border {{ $current ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
            البلوك الحالي:
            <strong>{{ $current?->name ?? ($block['label'] ?? '—') }}</strong>
            @if($minutesLeft !== null)
                · متبقي {{ $minutesLeft }} د
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach([
            ['تسجيلات', $team_metrics['paid_enrollments_daily'], $team_targets['paid_enrollments_daily'], 'emerald'],
            ['محاولات اتصال', $team_metrics['call_attempts_daily'], $team_targets['call_attempts_daily'], 'sky'],
            ['تم الرد', $team_metrics['calls_answered_daily'], $team_targets['calls_answered_daily'], 'indigo'],
            ['مؤهل', $team_metrics['qualified_conversations_daily'], $team_targets['qualified_conversations_daily'], 'violet'],
            ['جلسات', $team_metrics['discovery_sessions_daily'], $team_targets['discovery_sessions_daily'], 'amber'],
            ['عروض', $team_metrics['proposals_daily'], $team_targets['proposals_daily'], 'rose'],
        ] as [$label, $actual, $target, $color])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-black text-slate-900 tabular-nums mt-1">{{ number_format($actual) }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">هدف {{ number_format($target) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">نسبة تحقيق الهدف اليومي</p>
            <p class="text-4xl font-black mt-2 {{ $team_overall_pct >= 100 ? 'text-emerald-700' : ($team_overall_pct >= 70 ? 'text-amber-700' : 'text-rose-700') }}">{{ $team_overall_pct }}%</p>
            <p class="text-xs text-slate-500 mt-1">تحويل تقريبي (تسجيلات/محاولات): {{ $conversion_pct }}%</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
            <p class="text-xs font-bold text-amber-800 uppercase">Top Seller اليوم</p>
            @if($top_seller)
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $top_seller['user']->name }}</p>
                <p class="text-sm text-slate-600 mt-1">{{ $top_seller['overall_pct'] }}% · {{ $top_seller['paid'] }} تسجيل · {{ $top_seller['calls'] }} اتصال</p>
            @else
                <p class="text-sm text-slate-500 mt-3">لا يوجد نشاط كافٍ بعد.</p>
            @endif
        </div>
        <div class="rounded-2xl border {{ count($behind_pulse) ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-5 shadow-sm">
            <p class="text-xs font-bold uppercase {{ count($behind_pulse) ? 'text-rose-800' : 'text-emerald-800' }}">نبض آخر ساعتين</p>
            @if(count($behind_pulse))
                <p class="text-sm font-bold text-rose-800 mt-2">{{ count($behind_pulse) }} متأخر عن البلوك</p>
                <ul class="mt-2 text-xs text-rose-900 space-y-1">
                    @foreach($behind_pulse as $row)
                        <li>• {{ $row['user']->name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm font-bold text-emerald-800 mt-3">الجميع على الوتيرة المطلوبة</p>
            @endif
        </div>
    </div>

    @if(!empty($outcome_totals) && array_sum($outcome_totals) > 0)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-bold text-slate-600 mb-2">توزيع نتائج المكالمات اليوم</p>
            <div class="flex flex-wrap gap-2">
                @foreach($outcome_totals as $key => $count)
                    @if($count > 0)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                            {{ \App\Models\SalesActivity::outcomeLabel($key) }}: {{ $count }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-bold text-slate-900">ترتيب الفريق</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">الموظف</th>
                        <th class="px-3 py-2">التحقيق</th>
                        <th class="px-3 py-2">اتصالات</th>
                        <th class="px-3 py-2">رد</th>
                        <th class="px-3 py-2">مؤهل</th>
                        <th class="px-3 py-2">تسجيلات</th>
                        <th class="px-3 py-2">النبض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rows as $i => $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2.5 font-bold text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-3 py-2.5 font-semibold text-slate-900">{{ $row['user']->name }}</td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold border {{ $row['status'] === 'met' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($row['status'] === 'near' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    {{ $row['overall_pct'] }}%
                                </span>
                            </td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['metrics']['call_attempts_daily'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['metrics']['calls_answered_daily'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['metrics']['qualified_conversations_daily'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums font-bold text-emerald-700">{{ $row['metrics']['paid_enrollments_daily'] }}</td>
                            <td class="px-3 py-2.5">
                                @if($row['behind_pulse'])
                                    <span class="text-xs font-bold text-rose-700">متأخر</span>
                                @else
                                    <span class="text-xs font-bold text-emerald-700">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">لا أعضاء في الفريق</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
