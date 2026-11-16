@php
    /** @var array $diff */
    $diff = $diff ?? null;
    $leadShowRoute = $leadShowRoute ?? null;
    $metaAgentsUrl = Route::has('admin.meta-social.agents.index')
        ? route('admin.meta-social.agents.index')
        : null;
    if (! $diff) {
        return;
    }
    $metricLabels = [
        'calls_made' => 'مكالمات',
        'meetings_held' => 'اجتماعات',
        'followups_done' => 'متابعات',
        'messages_replied' => 'ردود رسائل',
        'numbers_worked' => 'أرقام تم العمل عليها',
    ];
    $acc = (float) ($diff['accuracy_pct'] ?? 0);
    $accClass = $acc >= 90
        ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
        : ($acc >= 70 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-rose-50 border-rose-200 text-rose-900');
@endphp

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-scale-balanced text-teal-600"></i>
                الفعلي مقابل المسلَّم
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                مقارنة أرقام التقرير بأسماء العملاء اللي اتواصل معاهم من النظام (CRM + واتساب + ميتا)
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold border {{ $accClass }}">
                دقة اليوم: {{ number_format($acc, 1) }}%
            </span>
            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                تواصل فعلي: {{ (int) ($diff['people_contacted'] ?? 0) }} عميل
            </span>
            @if(!empty($diff['flags']['inflated']))
                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">تضخيم أرقام</span>
            @endif
            @if(!empty($diff['flags']['under_reported']))
                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200">أقل من الفعلي</span>
            @endif
            @if(!empty($diff['flags']['contacts_mismatch']))
                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">اختلاف أسماء</span>
            @endif
        </div>
    </div>

    <div class="p-4 space-y-4">
        @if(empty($diff['meta_linked']))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-950 leading-relaxed">
                <i class="fas fa-link ml-1"></i>
                الموظف غير مربوط بحساب Meta داخل النظام — الردود من الماسنجر قد لا تُنسب له بدقة.
                @if($metaAgentsUrl)
                    <a href="{{ $metaAgentsUrl }}" class="font-bold underline hover:no-underline">ربط موظفي Meta</a>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-900">
                <i class="fas fa-check-circle ml-1"></i>
                مربوط بـ Meta: <span class="font-bold" dir="ltr">{{ $diff['meta_link_label'] }}</span>
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-600">
                <tr>
                    <th class="px-3 py-2 text-right font-bold">المؤشر</th>
                    <th class="px-3 py-2 text-right font-bold">مسلَّم</th>
                    <th class="px-3 py-2 text-right font-bold">فعلي</th>
                    <th class="px-3 py-2 text-right font-bold">الفرق</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach($metricLabels as $key => $label)
                    @php
                        $s = (int) ($diff['submitted'][$key] ?? 0);
                        $a = (int) ($diff['actual'][$key] ?? 0);
                        $d = (int) ($diff['deltas'][$key] ?? ($s - $a));
                        $dClass = $d > 0 ? 'text-rose-700 font-bold' : ($d < 0 ? 'text-sky-700 font-bold' : 'text-slate-600');
                    @endphp
                    <tr>
                        <td class="px-3 py-2 font-semibold text-slate-800">{{ $label }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $s }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $a }}</td>
                        <td class="px-3 py-2 tabular-nums {{ $dClass }}">{{ $d > 0 ? '+'.$d : $d }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 overflow-hidden">
                <div class="px-3 py-2 bg-emerald-50 border-b border-emerald-100 text-xs font-black text-emerald-900">
                    متطابق ({{ count($diff['contacts']['matched'] ?? []) }})
                </div>
                <ul class="max-h-64 overflow-y-auto divide-y divide-emerald-50 text-xs">
                    @forelse(($diff['contacts']['matched'] ?? []) as $row)
                        <li class="px-3 py-2">
                            <p class="font-bold text-slate-900">
                                @if($leadShowRoute && !empty($row['sales_lead_id']))
                                    <a href="{{ $leadShowRoute($row['sales_lead_id']) }}" class="hover:text-teal-700">{{ $row['name'] }}</a>
                                @else
                                    {{ $row['name'] }}
                                @endif
                            </p>
                            <p class="text-slate-500 font-mono" dir="ltr">{{ $row['phone'] }}</p>
                            <p class="text-emerald-700">{{ $row['channels_label'] ?? '—' }}</p>
                        </li>
                    @empty
                        <li class="px-3 py-4 text-slate-500 text-center">لا يوجد تطابق بعد</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-xl border border-rose-200 overflow-hidden">
                <div class="px-3 py-2 bg-rose-50 border-b border-rose-100 text-xs font-black text-rose-900">
                    في التقرير فقط ({{ count($diff['contacts']['report_only'] ?? []) }})
                </div>
                <ul class="max-h-64 overflow-y-auto divide-y divide-rose-50 text-xs">
                    @forelse(($diff['contacts']['report_only'] ?? []) as $row)
                        <li class="px-3 py-2">
                            <p class="font-bold text-slate-900">{{ $row['name'] }}</p>
                            <p class="text-slate-500 font-mono" dir="ltr">{{ $row['phone'] }}</p>
                            <p class="text-rose-700">مذكور في التقرير بدون نشاط موثّق</p>
                        </li>
                    @empty
                        <li class="px-3 py-4 text-slate-500 text-center">لا توجد أسماء زائدة</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-xl border border-sky-200 overflow-hidden">
                <div class="px-3 py-2 bg-sky-50 border-b border-sky-100 text-xs font-black text-sky-900">
                    في الفعلي فقط ({{ count($diff['contacts']['actual_only'] ?? []) }})
                </div>
                <ul class="max-h-64 overflow-y-auto divide-y divide-sky-50 text-xs">
                    @forelse(($diff['contacts']['actual_only'] ?? []) as $row)
                        <li class="px-3 py-2">
                            <p class="font-bold text-slate-900">
                                @if($leadShowRoute && !empty($row['sales_lead_id']))
                                    <a href="{{ $leadShowRoute($row['sales_lead_id']) }}" class="hover:text-sky-700">{{ $row['name'] }}</a>
                                @else
                                    {{ $row['name'] }}
                                @endif
                            </p>
                            <p class="text-slate-500 font-mono" dir="ltr">{{ $row['phone'] }}</p>
                            <p class="text-sky-700">{{ $row['channels_label'] ?? '—' }} @if(!empty($row['last_at'])) · {{ $row['last_at'] }} @endif</p>
                        </li>
                    @empty
                        <li class="px-3 py-4 text-slate-500 text-center">كل النشاط مذكور في التقرير</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 text-xs font-black text-slate-800">
                كل الليدز اللي اتواصل معاهم فعليًا ({{ count($diff['actual_leads'] ?? []) }})
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-white text-slate-500">
                    <tr>
                        <th class="px-3 py-2 text-right">الاسم</th>
                        <th class="px-3 py-2 text-right">الهاتف</th>
                        <th class="px-3 py-2 text-right">القنوات</th>
                        <th class="px-3 py-2 text-right">أول/آخر تواصل</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse(($diff['actual_leads'] ?? []) as $lead)
                        <tr>
                            <td class="px-3 py-2 font-semibold text-slate-900">
                                @if($leadShowRoute && !empty($lead['sales_lead_id']))
                                    <a href="{{ $leadShowRoute($lead['sales_lead_id']) }}" class="hover:text-teal-700">{{ $lead['name'] }}</a>
                                @else
                                    {{ $lead['name'] }}
                                @endif
                            </td>
                            <td class="px-3 py-2 font-mono" dir="ltr">{{ $lead['phone'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $lead['channels_label'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-500 whitespace-nowrap">
                                {{ $lead['first_at'] ?? '—' }}
                                @if(!empty($lead['last_at']) && ($lead['last_at'] !== ($lead['first_at'] ?? null)))
                                    → {{ $lead['last_at'] }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-slate-500">لا يوجد تواصل موثّق في هذا اليوم</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
