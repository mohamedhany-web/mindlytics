@php
    $summary = $employeeReport['summary'];
    $dailyRows = $employeeReport['daily_rows'];
    $groupBreakdown = $employeeReport['group_breakdown'] ?? [];
    $leadsWithContact = $employeeReport['leads_with_contact'] ?? collect();
@endphp

{{-- ملخص تنفيذي --}}
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-gradient-to-l from-emerald-50 to-white flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-black text-slate-900">تقرير الموظف — {{ $selectedRep->name }}</h3>
            <p class="text-xs text-slate-600">ملخص واضح لما أنجزه الموظف خلال الفترة، وأيام الدخول، والبيانات التي أدخلها.</p>
            <p class="text-[11px] text-slate-500 mt-1">
                {{ $employeeReport['lead_scope_label'] }}
                @if(!empty($employeeReport['selected_group']))
                    · مجموعة: <strong>{{ $employeeReport['selected_group']->name }}</strong>
                @endif
            </p>
        </div>
        <form method="get" action="{{ route('admin.sales.reports.pdf') }}" class="inline">
            <input type="hidden" name="date_from" value="{{ $employeeReport['start']->format('Y-m-d') }}">
            <input type="hidden" name="date_to" value="{{ $employeeReport['end']->format('Y-m-d') }}">
            <input type="hidden" name="user_id" value="{{ $selectedRep->id }}">
            <input type="hidden" name="lead_scope" value="{{ $employeeReport['lead_scope'] }}">
            @if(!empty($employeeReport['group_id']))
                <input type="hidden" name="group_id" value="{{ $employeeReport['group_id'] }}">
            @endif
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2 text-sm font-semibold text-white">
                <i class="fas fa-file-pdf"></i>
                تحميل PDF
            </button>
        </form>
    </div>

    <div class="p-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
            <p class="text-[11px] text-emerald-800 font-semibold">أيام دخول النظام</p>
            <p class="text-2xl font-black text-emerald-900 tabular-nums">{{ $summary['days_with_login'] }} <span class="text-sm font-semibold">/ {{ $summary['work_days'] }} يوم عمل</span></p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3">
            <p class="text-[11px] text-rose-800 font-semibold">أيام بدون دخول</p>
            <p class="text-2xl font-black text-rose-900 tabular-nums">{{ $summary['days_without_login'] }}</p>
        </div>
        <div class="rounded-xl border border-violet-200 bg-violet-50 p-3">
            <p class="text-[11px] text-violet-800 font-semibold">Leads في التقرير / تواصل في الفترة</p>
            <p class="text-2xl font-black text-violet-900 tabular-nums">{{ $summary['leads_in_scope'] }} <span class="text-sm">/ {{ $summary['leads_contacted_in_period'] ?? 0 }}</span></p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
            <p class="text-[11px] text-amber-800 font-semibold">أنشطة CRM مسجّلة</p>
            <p class="text-2xl font-black text-amber-900 tabular-nums">{{ $summary['total_activities'] }}</p>
            <p class="text-[10px] text-amber-700 mt-1">مكالمات {{ $summary['calls'] }} · اجتماعات {{ $summary['meetings'] }} · متابعات {{ $summary['followups'] }}</p>
        </div>
    </div>

    @if(count($employeeReport['absent_work_days']) > 0)
        <div class="mx-4 mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-1"><i class="fas fa-user-clock ml-1"></i> أيام عمل لم يُسجَّل فيها دخول للنظام:</p>
            <p class="text-xs leading-relaxed">{{ implode('، ', $employeeReport['absent_work_days']) }}</p>
        </div>
    @endif

    @if($summary['joined_at'])
        <p class="px-4 pb-3 text-xs text-slate-500">انضم للمنصة: <strong>{{ $summary['joined_at']->format('Y-m-d') }}</strong></p>
    @endif
</section>

{{-- ملخص مجموعات العملاء --}}
@if(count($groupBreakdown) > 0)
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="text-base font-black text-slate-900">مجموعات العملاء المسندة للموظف</h3>
            <p class="text-xs text-slate-600">كم Lead في كل مجموعة، وكم منهم تم التواصل معهم خلال الفترة أو سابقاً.</p>
        </div>
        <span class="text-xs font-semibold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-lg border border-violet-200">{{ count($groupBreakdown) }} مجموعة</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-[900px] w-full text-xs">
            <thead class="bg-violet-50 text-violet-900 border-b border-violet-200">
                <tr>
                    <th class="px-3 py-2 text-right font-semibold">المجموعة</th>
                    <th class="px-3 py-2 text-center font-semibold">إجمالي Leads</th>
                    <th class="px-3 py-2 text-center font-semibold">تواصل في الفترة</th>
                    <th class="px-3 py-2 text-center font-semibold">لم يُتواصل في الفترة</th>
                    <th class="px-3 py-2 text-center font-semibold">تواصل سابقاً</th>
                    <th class="px-3 py-2 text-center font-semibold">لم يُتواصل أبداً</th>
                    <th class="px-3 py-2 text-center font-semibold">حسب المرحلة</th>
                    <th class="px-3 py-2 text-center font-semibold">عرض</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($groupBreakdown as $row)
                    @php $g = $row['group']; @endphp
                    <tr class="hover:bg-violet-50/40 {{ !empty($employeeReport['group_id']) && (int)$employeeReport['group_id'] === (int)$g->id ? 'bg-violet-50/70' : '' }}">
                        <td class="px-3 py-2">
                            <span class="font-bold text-slate-900">{{ $g->name }}</span>
                            @if($g->is_admin_managed)
                                <span class="text-[10px] text-sky-700 bg-sky-50 border border-sky-200 rounded px-1 mr-1">من الإدارة</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center tabular-nums font-bold">{{ $row['total'] }}</td>
                        <td class="px-3 py-2 text-center tabular-nums text-emerald-700 font-bold">{{ $row['contacted_in_period'] }}</td>
                        <td class="px-3 py-2 text-center tabular-nums text-amber-700">{{ $row['not_contacted_in_period'] }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['contacted_ever'] }}</td>
                        <td class="px-3 py-2 text-center tabular-nums text-rose-700">{{ $row['not_contacted'] }}</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex flex-wrap gap-1 justify-center">
                                @foreach($row['by_stage'] as $stageKey => $stageRow)
                                    @if($stageRow['count'] > 0)
                                        <span class="inline-flex px-1.5 py-0.5 rounded bg-slate-100 text-[10px] text-slate-700" title="{{ $stageRow['label'] }}">
                                            {{ $stageRow['label'] }}: {{ $stageRow['count'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('admin.sales.reports.employee', [
                                'user_id' => $selectedRep->id,
                                'date_from' => $employeeReport['start']->format('Y-m-d'),
                                'date_to' => $employeeReport['end']->format('Y-m-d'),
                                'lead_scope' => 'in_groups',
                                'group_id' => $g->id,
                            ]) }}"
                               class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-semibold">
                                <i class="fas fa-filter text-[10px]"></i>
                                تفصيل
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

{{-- جدول يومي --}}
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900">الجدول اليومي</h3>
        <p class="text-xs text-slate-600">كل صف = يوم واحد. الأخضر = نشط، الأحمر = لم يدخل، البرتقالي = دخل بدون نشاط مبيعات.</p>
    </div>
    <div class="overflow-x-auto max-h-[520px] overflow-y-auto">
        <table class="min-w-[960px] w-full text-xs">
            <thead class="sticky top-0 z-10 bg-slate-800 text-white">
                <tr>
                    <th class="px-3 py-2 text-right font-semibold">التاريخ</th>
                    <th class="px-3 py-2 text-center font-semibold">اليوم</th>
                    <th class="px-3 py-2 text-center font-semibold">الحالة</th>
                    <th class="px-3 py-2 text-center font-semibold">دخول</th>
                    <th class="px-3 py-2 text-center font-semibold">مكالمات</th>
                    <th class="px-3 py-2 text-center font-semibold">اجتماعات</th>
                    <th class="px-3 py-2 text-center font-semibold">متابعات</th>
                    <th class="px-3 py-2 text-center font-semibold">واتساب</th>
                    <th class="px-3 py-2 text-center font-semibold">Leads جديدة</th>
                    <th class="px-3 py-2 text-center font-semibold">من الإدارة</th>
                    <th class="px-3 py-2 text-center font-semibold">تقرير يومي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($dailyRows as $row)
                    @php
                        $rowBg = match($row['status_tone']) {
                            'emerald' => 'bg-emerald-50/60',
                            'rose' => 'bg-rose-50/70',
                            'amber' => 'bg-amber-50/70',
                            default => 'bg-slate-50/40',
                        };
                        $badge = match($row['status_tone']) {
                            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <tr class="{{ $rowBg }} hover:brightness-[0.98]">
                        <td class="px-3 py-2 font-mono text-slate-700">{{ $row['date'] }}</td>
                        <td class="px-3 py-2 text-center text-slate-600">{{ $row['day_name'] }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $badge }}">{{ $row['status_label'] }}</span>
                        </td>
                        <td class="px-3 py-2 text-center">{{ $row['logged_in'] ? '✓' : '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['calls'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['meetings'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['followups'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['whatsapp'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['leads_created'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $row['leads_from_admin'] ?: '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['daily_report_label'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

{{-- Leads كاملة --}}
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="text-base font-black text-slate-900">العملاء المحتملون ({{ $employeeReport['leads']->count() }})</h3>
            <p class="text-xs text-slate-600">حالة التواصل ومرحلة كل Lead حسب الفلتر المحدد.</p>
        </div>
        <span class="text-xs text-slate-500">{{ $employeeReport['lead_scope_label'] }} · {{ $employeeReport['group_filter_label'] }}</span>
    </div>
    <div class="overflow-x-auto max-h-96 overflow-y-auto">
        <table class="min-w-full text-xs">
            <thead class="sticky top-0 bg-violet-50 text-violet-900 border-b border-violet-200">
                <tr>
                    <th class="px-3 py-2 text-right">الاسم</th>
                    <th class="px-3 py-2 text-right">الهاتف</th>
                    <th class="px-3 py-2 text-center">المجموعة</th>
                    <th class="px-3 py-2 text-center">المرحلة</th>
                    <th class="px-3 py-2 text-center">حالة التواصل</th>
                    <th class="px-3 py-2 text-center">آخر تواصل</th>
                    <th class="px-3 py-2 text-center">أُنشئ بواسطة</th>
                    <th class="px-3 py-2 text-center">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leadsWithContact as $item)
                    @php
                        $lead = $item['lead'];
                        $contactBadge = match($item['contact_tone']) {
                            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                            default => 'bg-rose-100 text-rose-800 border-rose-200',
                        };
                    @endphp
                    <tr class="hover:bg-violet-50/40">
                        <td class="px-3 py-2 font-semibold text-slate-900">
                            <a href="{{ route('admin.sales.leads.show', $lead) }}" class="hover:text-emerald-700 hover:underline">{{ $lead->name }}</a>
                        </td>
                        <td class="px-3 py-2 font-mono dir-ltr text-right">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $lead->group?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $contactBadge }}">{{ $item['contact_label'] }}</span>
                        </td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $lead->last_contacted_at?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $lead->creator?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-center tabular-nums">{{ $lead->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">لا توجد Leads في هذه الفترة حسب الفلتر.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- الأنشطة --}}
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900">سجل الأنشطة ({{ $employeeReport['activities']->count() }})</h3>
    </div>
    <div class="overflow-x-auto max-h-96 overflow-y-auto">
        <table class="min-w-full text-xs">
            <thead class="sticky top-0 bg-amber-50 text-amber-900 border-b border-amber-200">
                <tr>
                    <th class="px-3 py-2 text-right">التاريخ</th>
                    <th class="px-3 py-2 text-center">النوع</th>
                    <th class="px-3 py-2 text-right">العميل</th>
                    <th class="px-3 py-2 text-right">التفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($employeeReport['activities'] as $activity)
                    <tr class="hover:bg-amber-50/40">
                        <td class="px-3 py-2 tabular-nums whitespace-nowrap">{{ $activity->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2 text-center font-semibold">{{ \App\Models\SalesActivity::typeLabel($activity->type) }}</td>
                        <td class="px-3 py-2">{{ $activity->lead?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ \Illuminate\Support\Str::limit($activity->title ?: ($activity->body ?? '—'), 100) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا توجد أنشطة مسجّلة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
