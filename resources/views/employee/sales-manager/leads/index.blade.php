@extends('layouts.employee')

@section('title', 'عملاء الفريق')
@section('header', 'عملاء الفريق')

@section('content')
@php
    $req = request();
    $activePreset = 'all';
    if ($req->boolean('stale')) {
        $activePreset = 'stale';
    } elseif ($req->get('follow_up') === 'today') {
        $activePreset = 'today';
    } elseif ($req->get('follow_up') === 'overdue') {
        $activePreset = 'overdue';
    } elseif ($req->get('stage') === 'new') {
        $activePreset = 'new';
    }
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">عملاء فريق {{ $team->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">عرض وتحويل Leads بين أعضاء الفريق</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.follow-ups.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold"><i class="fas fa-clipboard-list"></i> رقابة المتابعات</a>
                <a href="{{ route('employee.sales-manager.transfer.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold"><i class="fas fa-exchange-alt"></i> تحويل جماعي</a>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('employee.sales-manager.leads.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $activePreset === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200' }}">الكل</a>
            <a href="{{ route('employee.sales-manager.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up']) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $activePreset === 'overdue' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-600 border-slate-200' }}">
                متأخرة <span class="tabular-nums opacity-80">{{ $quickCounts['overdue'] ?? 0 }}</span>
            </a>
            <a href="{{ route('employee.sales-manager.leads.index', ['follow_up' => 'today', 'sort' => 'follow_up']) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $activePreset === 'today' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-600 border-slate-200' }}">
                اليوم <span class="tabular-nums opacity-80">{{ $quickCounts['today'] ?? 0 }}</span>
            </a>
            <a href="{{ route('employee.sales-manager.leads.index', ['stale' => 1]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $activePreset === 'stale' ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-slate-600 border-slate-200' }}">
                بلا تواصل <span class="tabular-nums opacity-80">{{ $quickCounts['stale'] ?? 0 }}</span>
            </a>
            <a href="{{ route('employee.sales-manager.leads.index', ['stage' => 'new_lead']) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border {{ $activePreset === 'new' ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-slate-600 border-slate-200' }}">
                جديد <span class="tabular-nums opacity-80">{{ $quickCounts['new'] ?? 0 }}</span>
            </a>
        </div>

        <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
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
            <select name="follow_up" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل المتابعات</option>
                <option value="overdue" @selected(request('follow_up') === 'overdue')>متأخرة</option>
                <option value="today" @selected(request('follow_up') === 'today')>اليوم</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold">مسند إلى</th>
                    <th class="px-4 py-3 text-right font-semibold">المرحلة</th>
                    <th class="px-4 py-3 text-right font-semibold">متابعة</th>
                    <th class="px-4 py-3 text-right font-semibold hidden md:table-cell">آخر تواصل</th>
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50 {{ $lead->isFollowUpOverdue() ? 'bg-rose-50/40' : '' }}">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $lead->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->assignee->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ \App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage }}</td>
                        <td class="px-4 py-3 @if($lead->isFollowUpOverdue()) text-rose-700 font-bold @else text-slate-500 @endif">
                            {{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-500">
                            {{ $lead->last_contacted_at?->format('Y-m-d') ?? '—' }}
                            @if($lead->isStaleContact())
                                <span class="block text-[10px] text-orange-700 font-bold">بلا تواصل</span>
                            @endif
                        </td>
                        <td class="px-4 py-3"><a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="text-emerald-700 font-semibold hover:underline">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد نتائج.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
