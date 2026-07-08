@extends('layouts.employee')

@section('title', 'عملاء الفريق')
@section('header', 'عملاء الفريق')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">عملاء فريق {{ $team->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">عرض وتحويل Leads بين أعضاء الفريق</p>
            </div>
            <a href="{{ route('employee.sales-manager.transfer.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold"><i class="fas fa-exchange-alt"></i> تحويل جماعي</a>
        </div>
        <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
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
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $lead->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $lead->assignee->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ \App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="text-emerald-700 font-semibold hover:underline">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد نتائج.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
