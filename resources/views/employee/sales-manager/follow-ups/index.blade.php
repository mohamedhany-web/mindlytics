@extends('layouts.employee')

@section('title', 'رقابة المتابعات')
@section('header', 'رقابة متابعات الفريق')

@section('content')
@php
    $filterLabels = [
        'overdue' => 'متأخرة',
        'today' => 'اليوم',
        'week' => 'خلال 7 أيام',
        'none' => 'بدون موعد',
        'stale' => 'بلا تواصل',
        'all' => 'كل المفتوحة',
    ];
@endphp
<div class="space-y-5">
    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-sky-50/50 px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-teal-600"></i>
                    رقابة المتابعات — {{ $team->name }}
                </h1>
                <p class="text-xs text-slate-600 mt-1">متابعة الفريق: المتأخرون، مواعيد اليوم، ومن لم يُتواصل معهم.</p>
            </div>
            <a href="{{ route('employee.sales-manager.leads.index') }}" class="text-xs font-bold text-teal-700 hover:underline">عملاء الفريق ←</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        @foreach([
            ['key' => 'overdue', 'label' => 'متأخرة', 'color' => 'text-rose-700'],
            ['key' => 'today', 'label' => 'اليوم', 'color' => 'text-amber-700'],
            ['key' => 'week', 'label' => 'خلال أسبوع', 'color' => 'text-teal-700'],
            ['key' => 'stale', 'label' => 'بلا تواصل', 'color' => 'text-orange-700'],
            ['key' => 'none', 'label' => 'بدون موعد', 'color' => 'text-slate-700'],
            ['key' => 'all', 'label' => 'مفتوحة', 'color' => 'text-slate-900'],
        ] as $card)
            <a href="{{ route('employee.sales-manager.follow-ups.index', array_filter(['filter' => $card['key'], 'assignee' => request('assignee'), 'stage' => request('stage'), 'search' => request('search')])) }}"
               class="rounded-xl border bg-white p-4 hover:border-teal-300 transition-colors {{ $filter === $card['key'] ? 'border-teal-400 ring-1 ring-teal-100' : 'border-slate-200' }}">
                <p class="text-[11px] text-slate-500 font-semibold">{{ $card['label'] }}</p>
                <p class="text-2xl font-black tabular-nums {{ $card['color'] }}">{{ $counts[$card['key']] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    @if($members->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">ملخص حسب الموظف</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-2 text-right">الموظف</th>
                            <th class="px-4 py-2 text-right">متأخر</th>
                            <th class="px-4 py-2 text-right">اليوم</th>
                            <th class="px-4 py-2 text-right">بلا تواصل</th>
                            <th class="px-4 py-2 text-right">بدون موعد</th>
                            <th class="px-4 py-2 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($members as $m)
                            @php $row = $byMember->get($m->user_id); @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ $m->user->name ?? '—' }}</td>
                                <td class="px-4 py-2.5 tabular-nums {{ (int) ($row->overdue_count ?? 0) > 0 ? 'text-rose-700 font-bold' : 'text-slate-600' }}">{{ (int) ($row->overdue_count ?? 0) }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-slate-700">{{ (int) ($row->today_count ?? 0) }}</td>
                                <td class="px-4 py-2.5 tabular-nums {{ (int) ($row->stale_count ?? 0) > 0 ? 'text-orange-700 font-bold' : 'text-slate-600' }}">{{ (int) ($row->stale_count ?? 0) }}</td>
                                <td class="px-4 py-2.5 tabular-nums text-slate-700">{{ (int) ($row->none_count ?? 0) }}</td>
                                <td class="px-4 py-2.5">
                                    <a href="{{ route('employee.sales-manager.follow-ups.index', ['filter' => 'overdue', 'assignee' => $m->user_id]) }}"
                                       class="text-xs font-bold text-teal-700 hover:underline">عرض</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
        <div class="flex flex-wrap gap-2">
            @foreach($filterLabels as $key => $label)
                <a href="{{ route('employee.sales-manager.follow-ups.index', array_filter(['filter' => $key, 'assignee' => request('assignee'), 'stage' => request('stage'), 'search' => request('search')])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border
                   {{ $filter === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200' }}">
                    {{ $label }}
                    <span class="tabular-nums opacity-80">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث..."
                   class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <select name="assignee" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                @foreach($members as $m)
                    <option value="{{ $m->user_id }}" @selected(request('assignee') == $m->user_id)>{{ $m->user->name }}</option>
                @endforeach
            </select>
            <select name="stage" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل المراحل</option>
                @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                    <option value="{{ $k }}" @selected(request('stage') === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                    <th class="px-4 py-3 text-right font-semibold hidden md:table-cell">المرحلة</th>
                    <th class="px-4 py-3 text-right font-semibold">المتابعة</th>
                    <th class="px-4 py-3 text-right font-semibold hidden lg:table-cell">آخر تواصل</th>
                    <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                    @php
                        $daysSinceContact = null;
                        if ($lead->last_contacted_at) {
                            $daysSinceContact = (int) $lead->last_contacted_at->diffInDays(now());
                        } elseif ($lead->created_at) {
                            $daysSinceContact = (int) $lead->created_at->diffInDays(now());
                        }
                        $badges = [];
                        if ($lead->isFollowUpOverdue()) $badges[] = ['متأخر', 'bg-rose-50 text-rose-700'];
                        elseif ($lead->next_follow_up_at?->isToday()) $badges[] = ['اليوم', 'bg-amber-50 text-amber-800'];
                        elseif ($lead->next_follow_up_at?->isFuture()) $badges[] = ['قادم', 'bg-teal-50 text-teal-800'];
                        elseif (! $lead->next_follow_up_at) $badges[] = ['بدون موعد', 'bg-slate-100 text-slate-600'];
                        if ($lead->isStaleContact()) $badges[] = ['بلا تواصل', 'bg-orange-50 text-orange-800'];
                    @endphp
                    <tr class="hover:bg-slate-50 {{ $lead->isFollowUpOverdue() ? 'bg-rose-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900">{{ $lead->name }}</p>
                            <p class="text-[11px] text-slate-500 dir-ltr">{{ $lead->phone ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $lead->assignee->name ?? '—' }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-slate-600">{{ \App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage }}</td>
                        <td class="px-4 py-3 whitespace-nowrap @if($lead->isFollowUpOverdue()) text-rose-700 font-bold @else text-slate-700 @endif">
                            {{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500">
                            {{ $lead->last_contacted_at?->format('Y-m-d') ?? '—' }}
                            @if($daysSinceContact !== null)
                                <span class="block text-[10px]">منذ {{ $daysSinceContact }} يوم</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($badges as [$label, $cls])
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $cls }}">{{ $label }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="text-xs font-bold text-emerald-700 hover:underline">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">لا توجد نتائج لهذا الفلتر.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
