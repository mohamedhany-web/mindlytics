@extends('layouts.employee')

@section('title', 'تفاصيل التقرير اليومي')
@section('header', 'تفاصيل التقرير اليومي')

@section('content')
@php
    $campaignEntries = $campaignEntries ?? collect();
    $dayActivities = $dayActivities ?? collect();
    $statusBadges = [
        'submitted' => ['label' => 'مسلّم', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
    ];
    $statusKey = $report->isSubmitted() ? 'submitted' : 'draft';
    $statusMeta = $statusBadges[$statusKey];

    $activityStats = [
        ['label' => 'ردود رسائل', 'value' => $report->messages_replied ?? '—', 'icon' => 'fas fa-comment-dots', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'مؤهلون', 'value' => $report->leads_qualified ?? '—', 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'حجوزات', 'value' => $report->bookings_from_leads ?? '—', 'icon' => 'fas fa-calendar-check', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
    ];
    $productivityStats = [
        ['label' => 'أرقام', 'value' => $report->numbers_worked ?? '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        ['label' => 'متابعات', 'value' => $report->followups_done ?? '—', 'icon' => 'fas fa-redo', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'مكالمات / اجتماعات / ردود', 'value' => ($report->calls_made ?? '—') . ' / ' . ($report->meetings_held ?? '—') . ' / ' . ($report->calls_answered ?? '—'), 'icon' => 'fas fa-headset', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
    ];

    $leadShowRoute = fn ($lead) => route('employee.sales-manager.leads.show', $lead);
@endphp

<div class="w-full space-y-6">
    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقرير يومي — {{ $report->user->name ?? '—' }}</h2>
                    <p class="text-xs text-slate-600">
                        <i class="fas fa-calendar ml-0.5"></i>
                        {{ $report->report_date?->format('Y-m-d') }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold {{ $statusMeta['classes'] }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ $statusMeta['label'] }}
                </span>
                @if($report->autoDeduction)
                    <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fas fa-gavel text-[10px]"></i>
                        خصم: {{ $report->autoDeduction->deduction_number }} ({{ number_format($report->autoDeduction->amount, 2) }} ج.م)
                    </span>
                @endif
                <a href="{{ route('employee.sales-manager.kpi.targets', ['user_id' => $report->user_id]) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-bullseye text-teal-600"></i>
                    أهداف KPI
                </a>
                <a href="{{ route('employee.sales-manager.campaign-reports.index', ['user_id' => $report->user_id, 'from' => $report->report_date?->toDateString(), 'to' => $report->report_date?->toDateString()]) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-bullhorn text-violet-600"></i>
                    كامبين اليوم
                </a>
                <a href="{{ route('employee.sales-manager.daily-reports.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    @if($kpiComparison ?? null)
        @php
            $kc = $kpiComparison;
            $kcClass = match ($kc['status'] ?? '') {
                'met' => 'border-emerald-200 bg-emerald-50',
                'near' => 'border-amber-200 bg-amber-50',
                default => 'border-rose-200 bg-rose-50',
            };
        @endphp
        <section class="rounded-2xl border {{ $kcClass }} p-5">
            <h3 class="font-black text-slate-900 mb-2"><i class="fas fa-bullseye ml-1"></i> مقارنة KPI ليوم التقرير</h3>
            <p class="text-sm font-semibold mb-3">{{ $kc['status_label'] ?? '' }} — {{ $kc['overall_pct'] ?? 0 }}%</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                @foreach($kc['lines'] ?? [] as $line)
                    <div class="rounded-lg bg-white/80 border border-white px-3 py-2">
                        <p class="text-slate-600 text-xs">{{ $line['label'] }}</p>
                        <p class="font-bold">{{ $line['actual'] }} / {{ $line['target'] }} ({{ $line['pct'] }}%)</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        {{-- نشاط اليوم --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full" id="day-activity">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i>
                    نشاط اليوم
                </h3>
                <span class="text-xs font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                    {{ $dayActivities->count() }} حركة
                </span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                    @foreach($activityStats as $stat)
                        <a href="#day-activity-list" class="rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 hover:shadow-sm transition-all block">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-slate-600">{{ $stat['label'] }}</p>
                                    <p class="text-xl font-black text-slate-900 tabular-nums">{{ $stat['value'] }}</p>
                                </div>
                                <div class="w-9 h-9 rounded-lg {{ $stat['bg'] }} flex items-center justify-center {{ $stat['text'] }}">
                                    <i class="{{ $stat['icon'] }} text-xs"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div id="day-activity-list" class="rounded-xl border border-slate-200 overflow-hidden mb-4">
                    <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 flex items-center justify-between">
                        <span>سجل الحركات — اضغط للدخول على بيانات العميل</span>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                        @forelse($dayActivities as $activity)
                            @php
                                $lead = $activity->lead;
                                $leadUrl = $lead ? $leadShowRoute($lead) : null;
                            @endphp
                            <div class="px-3 py-3 hover:bg-sky-50/40 transition-colors flex gap-3 items-start">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0 text-xs font-bold tabular-nums">
                                    {{ $activity->created_at?->format('H:i') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ \App\Models\SalesActivity::typeLabel($activity->type) }}
                                        @if($activity->title)
                                            <span class="font-semibold text-slate-600">— {{ $activity->title }}</span>
                                        @endif
                                    </p>
                                    @if($lead && $leadUrl)
                                        <a href="{{ $leadUrl }}" class="inline-flex items-center gap-1.5 mt-1 text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                            <i class="fas fa-external-link-alt text-[10px]"></i>
                                            {{ $lead->name }}
                                            @if($lead->phone)
                                                <span class="text-slate-500 font-medium text-xs">({{ $lead->phone }})</span>
                                            @endif
                                        </a>
                                        <p class="text-[11px] text-slate-500 mt-0.5">المرحلة: {{ \App\Models\SalesLead::stageLabel($lead->stage) }}</p>
                                    @else
                                        <p class="text-xs text-slate-500 mt-1">بدون عميل مرتبط</p>
                                    @endif
                                    @if($activity->body)
                                        <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $activity->body }}</p>
                                    @endif
                                </div>
                                @if($leadUrl)
                                    <a href="{{ $leadUrl }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-2.5 py-1.5 text-[11px] font-semibold text-white flex-shrink-0">
                                        فتح
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-slate-500">
                                لا توجد حركات مسجّلة في النظام لهذا اليوم.
                                @if($report->activity_notes)
                                    <p class="text-xs mt-1">الملاحظات النصية موجودة بالأسفل.</p>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($report->activity_notes)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات النشاط (نص)</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $report->activity_notes }}</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- الإنتاجية --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                    الإنتاجية
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                    @foreach($productivityStats as $stat)
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-600">{{ $stat['label'] }}</p>
                                    <p class="text-lg font-black text-slate-900 tabular-nums truncate">{{ $stat['value'] }}</p>
                                </div>
                                <div class="w-9 h-9 rounded-lg {{ $stat['bg'] }} flex items-center justify-center {{ $stat['text'] }} flex-shrink-0">
                                    <i class="{{ $stat['icon'] }} text-xs"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($report->productivity_notes)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات الإنتاجية</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $report->productivity_notes }}</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- تقارير الكامبين --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-2">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-bullhorn text-violet-600"></i>
                تقارير الكامبين لهذا اليوم
            </h3>
            <span class="text-xs font-semibold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-lg border border-violet-200">{{ $campaignEntries->count() }} سجل</span>
        </div>
        @if($campaignEntries->isEmpty())
            <div class="p-8 text-center text-sm text-slate-500">لا توجد إدخالات كامبين لهذا اليوم.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-right">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-4 py-2">الحملة</th>
                            <th class="px-4 py-2">جديدة</th>
                            <th class="px-4 py-2">واتساب</th>
                            <th class="px-4 py-2">ماسنجر</th>
                            <th class="px-4 py-2">إنستجرام</th>
                            <th class="px-4 py-2">Qual</th>
                            <th class="px-4 py-2">Unqual</th>
                            <th class="px-4 py-2">Conv</th>
                            <th class="px-4 py-2">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($campaignEntries as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $c->campaign?->name ?? '—' }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->new_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->whatsapp_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->messenger_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums">{{ $c->instagram_messages }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-indigo-700 font-semibold">{{ $c->qualified }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-slate-500">{{ $c->unqualified }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-emerald-700 font-semibold">{{ $c->converted }}</td>
                                <td class="px-4 py-2.5 text-slate-500 max-w-[220px] truncate" title="{{ $c->notes }}">{{ $c->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- المكالمات والاجتماعات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">المكالمات والاجتماعات</h3>
                <p class="text-xs text-slate-600">حالة العميل والمشاكل المسجّلة.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">{{ $report->contacts->count() }} سجل</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($report->contacts as $c)
                <div class="p-4 sm:p-5 hover:bg-slate-50/60 transition-colors">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $c->interactionTypeLabel() }}
                                @if($c->contact_name || $c->contact_phone)
                                    — {{ $c->contact_name ?: '—' }}
                                    @if($c->contact_phone)
                                        <span class="text-slate-500 font-medium">({{ $c->contact_phone }})</span>
                                    @endif
                                @endif
                            </p>
                            @if($c->lead)
                                <a href="{{ $leadShowRoute($c->lead) }}"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline mt-1.5">
                                    <i class="fas fa-user-tag"></i>
                                    فتح بيانات العميل: {{ $c->lead->name }}
                                    <i class="fas fa-external-link-alt text-[9px]"></i>
                                </a>
                            @endif
                        </div>
                        @if($c->lead)
                            <a href="{{ $leadShowRoute($c->lead) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white">
                                التفاصيل
                            </a>
                        @endif
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2">
                            <dt class="text-xs font-semibold text-slate-500 mb-1">حالة العميل</dt>
                            <dd class="text-slate-800">{{ $c->client_status ?: '—' }}</dd>
                        </div>
                        <div class="rounded-lg border border-rose-200 bg-rose-50/30 px-3 py-2">
                            <dt class="text-xs font-semibold text-rose-700 mb-1">المشاكل</dt>
                            <dd class="text-slate-800">{{ $c->client_problems ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            @empty
                <div class="p-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-phone-slash text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900">لا توجد سجلات تواصل</p>
                    <p class="text-xs text-slate-500 mt-1">لم يُسجّل أي تواصل في هذا التقرير.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
