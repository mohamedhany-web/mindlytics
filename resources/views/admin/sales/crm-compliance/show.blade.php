@extends('layouts.admin')

@section('title', 'تدقيق CRM — ' . $employee->name)
@section('header', 'تفاصيل التزام CRM')

@section('content')
@php
    $toneClass = function ($tone) {
        return match ($tone) {
            'excellent' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'label' => 'ممتاز'],
            'good' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'bar' => 'bg-sky-500', 'border' => 'border-sky-200', 'label' => 'جيد'],
            'warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'bar' => 'bg-amber-500', 'border' => 'border-amber-200', 'label' => 'تحت المتابعة'],
            default => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500', 'border' => 'border-rose-200', 'label' => 'حرج'],
        };
    };
    $t = $toneClass($row['tone'] ?? 'critical');
    $selectClass = 'rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('admin.sales.crm-compliance.index', request()->only(['period', 'date_from', 'date_to'])) }}"
                   class="text-xs font-semibold text-sky-700 hover:text-sky-900 inline-flex items-center gap-1 mb-2">
                    <i class="fas fa-arrow-right"></i> العودة للقائمة
                </a>
                <h2 class="text-xl font-black text-slate-900">{{ $employee->name }}</h2>
                <p class="text-xs text-slate-600">{{ $row['job_title'] }} · {{ $dateFrom }} ← {{ $dateTo }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl border {{ $t['border'] }} {{ $t['bg'] }} px-4 py-3 text-center">
                    <p class="text-[10px] font-semibold {{ $t['text'] }}">درجة الالتزام</p>
                    <p class="text-2xl font-black {{ $t['text'] }} tabular-nums">{{ number_format((float) $row['compliance_score'], 1) }}%</p>
                    <p class="text-[10px] {{ $t['text'] }}">{{ $t['label'] }}</p>
                </div>
            </div>
        </div>

        <form method="get" class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end border-b border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الفترة</label>
                <select name="period" class="{{ $selectClass }} w-full">
                    <option value="day" @selected($period === 'day')>اليوم</option>
                    <option value="week" @selected($period === 'week')>آخر 7 أيام</option>
                    <option value="month" @selected($period === 'month')>هذا الشهر</option>
                    <option value="custom" @selected($period === 'custom')>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="{{ $selectClass }} w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="{{ $selectClass }} w-full">
            </div>
            <div>
                <button type="submit" class="w-full rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 text-sm">تحديث</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3 p-4">
            @foreach($row['pillars'] as $key => $pillar)
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-[11px] font-semibold text-slate-500 mb-1">{{ $pillar['label'] }}</p>
                    <p class="text-xl font-black text-slate-900 tabular-nums">{{ number_format((float) $pillar['score'], 1) }}%</p>
                    <ul class="mt-2 space-y-0.5">
                        @foreach($pillar['details'] as $d)
                            <li class="text-[10px] text-slate-500">{{ $d }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    @if(count($row['exceptions']) > 0)
        <section class="rounded-2xl bg-white border border-rose-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-rose-100 bg-rose-50">
                <h3 class="text-sm font-black text-rose-800">استثناءات تحتاج مراجعة</h3>
            </div>
            <ul class="divide-y divide-rose-50">
                @foreach($row['exceptions'] as $ex)
                    <li class="px-4 py-3">
                        <p class="text-sm font-bold text-slate-900">{{ $ex['title'] }}</p>
                        <p class="text-xs text-slate-600">{{ $ex['detail'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">حركة الـ Pipeline</h3>
            </div>
            <div class="p-4 space-y-2">
                <p class="text-xs text-slate-600 mb-2">
                    {{ (int) $row['pipeline']['stage_changes'] }} انتقال ·
                    {{ (int) $row['pipeline']['unique_leads_moved'] }} عميل
                </p>
                @forelse($row['stage_changes'] as $change)
                    @php
                        $meta = is_array($change->meta) ? $change->meta : [];
                        $fromL = isset($meta['from']) ? \App\Models\SalesLead::stageLabel((string) $meta['from']) : '—';
                        $toL = isset($meta['to']) ? \App\Models\SalesLead::stageLabel((string) $meta['to']) : '—';
                    @endphp
                    <div class="rounded-xl border border-slate-100 px-3 py-2 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">
                                {{ $change->lead->name ?? ('Lead #'.$change->sales_lead_id) }}
                            </p>
                            <p class="text-xs text-slate-600">{{ $fromL }} → {{ $toL }}</p>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <p class="text-[10px] text-slate-500">{{ optional($change->created_at)->format('m-d H:i') }}</p>
                            @if($change->sales_lead_id)
                                <a href="{{ route('admin.sales.crm-compliance.lead', $change->sales_lead_id) }}"
                                   class="text-[10px] font-semibold text-sky-700">الخط الزمني</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 py-6 text-center">لا توجد تغييرات مرحلة في الفترة.</p>
                @endforelse

                @if(!empty($row['pipeline']['top_transitions']))
                    <div class="pt-3 mt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-700 mb-2">أكثر الانتقالات</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($row['pipeline']['top_transitions'] as $pair => $cnt)
                                <span class="text-[11px] rounded-lg bg-slate-100 text-slate-700 px-2 py-1 font-semibold">{{ $pair }} × {{ $cnt }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">التقرير اليومي مقابل CRM</h3>
            </div>
            <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl border border-slate-100 p-3">
                    <p class="text-[11px] text-slate-500">مكالمات معلنة</p>
                    <p class="font-black text-slate-900">{{ (int) $row['report']['claimed_calls'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-3">
                    <p class="text-[11px] text-slate-500">مكالمات موثّقة</p>
                    <p class="font-black text-emerald-700">{{ (int) $row['report']['verified_calls'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-3">
                    <p class="text-[11px] text-slate-500">اجتماعات معلنة / موثّقة</p>
                    <p class="font-black text-slate-900">{{ (int) $row['report']['claimed_meetings'] }} / {{ (int) $row['report']['verified_meetings'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-3">
                    <p class="text-[11px] text-slate-500">متابعات معلنة / موثّقة</p>
                    <p class="font-black text-slate-900">{{ (int) $row['report']['claimed_followups'] }} / {{ (int) $row['report']['verified_followups'] }}</p>
                </div>
                <div class="col-span-2 rounded-xl border border-slate-100 p-3">
                    <p class="text-[11px] text-slate-500">دقة التطابق</p>
                    <p class="text-lg font-black text-slate-900">
                        {{ $row['report']['accuracy_pct'] !== null ? number_format((float) $row['report']['accuracy_pct'], 1).'%' : 'لا تقارير مسلّمة' }}
                    </p>
                </div>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex justify-between">
                <h3 class="text-sm font-black text-slate-900">أنشطة CRM الموثّقة</h3>
                <span class="text-xs text-slate-500">{{ (int) $row['usage']['crm_activities'] }} نشاط</span>
            </div>
            <ul class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                @forelse($row['recent_activities'] as $act)
                    <li class="px-4 py-2.5 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">
                                {{ \App\Models\SalesActivity::typeLabel($act->type) }}
                                @if($act->outcome)
                                    <span class="text-slate-500 font-normal">· {{ \App\Models\SalesActivity::outcomeLabel($act->outcome) }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-500 truncate">{{ $act->lead->name ?? ('#'.$act->sales_lead_id) }}</p>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <p class="text-[10px] text-slate-400">{{ optional($act->created_at)->format('m-d H:i') }}</p>
                            @if($act->sales_lead_id)
                                <a href="{{ route('admin.sales.crm-compliance.lead', $act->sales_lead_id) }}" class="text-[10px] text-sky-700 font-semibold">رحلة العميل</a>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-slate-500">لا أنشطة موثّقة في الفترة.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">عملاء تم لمسهم</h3>
            </div>
            <ul class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                @forelse($row['leads_touched'] as $lead)
                    <li class="px-4 py-2.5 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $lead->name ?: ('Lead #'.$lead->id) }}</p>
                            <p class="text-xs text-slate-500">{{ \App\Models\SalesLead::stageLabel($lead->stage) }} · {{ $lead->phone }}</p>
                        </div>
                        <a href="{{ route('admin.sales.crm-compliance.lead', $lead) }}"
                           class="text-xs font-semibold text-sky-700 whitespace-nowrap">الخط الزمني</a>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-slate-500">لا عملاء في الفترة.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
