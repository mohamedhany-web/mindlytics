@extends('layouts.admin')

@section('title', 'ساعات الأماكن')
@section('header', 'مراجعة ساعات ومصاريف الأماكن')

@section('content')
<div class="p-4 md:p-6 space-y-6 max-w-7xl mx-auto">
    @if(session('success'))<div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm">{{ session('error') }}</div>@endif

    <form method="GET" class="flex flex-wrap gap-2 bg-white p-3 rounded-xl border text-sm">
        <select name="location_id" class="rounded-lg border-slate-300 text-sm min-w-[140px]">
            <option value="">كل الأماكن</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الحالات</option>
            <option value="pending" @selected(request('status') === 'pending')>في الانتظار</option>
            <option value="approved" @selected(request('status') === 'approved')>موافق</option>
            <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
        </select>
        <input type="month" name="month" value="{{ request('month') }}" class="rounded-lg border-slate-300 text-sm">
        <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-4 py-2 bg-slate-50 border-b font-bold text-sm">ساعات الاستخدام</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-right">المكان</th>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-right">النوع</th>
                        <th class="px-3 py-2 text-right">الكورس</th>
                        <th class="px-3 py-2 text-right">ساعات</th>
                        <th class="px-3 py-2 text-right">المُسجّل</th>
                        <th class="px-3 py-2 text-right">الحالة</th>
                        <th class="px-3 py-2 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-3 py-2">{{ $log->location?->name }}</td>
                            <td class="px-3 py-2">{{ $log->usage_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-xs">{{ $log->usage_type_label }}</td>
                            <td class="px-3 py-2 text-xs text-slate-600">{{ $log->offlineCourse?->title ?? '—' }}</td>
                            <td class="px-3 py-2 font-semibold">{{ number_format((float) $log->hours, 2) }}</td>
                            <td class="px-3 py-2 text-xs">{{ $log->logger?->name }}</td>
                            <td class="px-3 py-2 text-xs">{{ $log->status_label }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if($log->status === 'pending')
                                    <form action="{{ route('admin.place-usage-logs.approve', $log) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-emerald-600 font-medium text-xs">موافقة</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('reject-log-{{ $log->id }}').classList.toggle('hidden')" class="text-rose-600 font-medium text-xs mr-2">رفض</button>
                                    <form id="reject-log-{{ $log->id }}" action="{{ route('admin.place-usage-logs.reject', $log) }}" method="POST" class="hidden mt-1">
                                        @csrf
                                        <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="text-xs border rounded px-2 py-1 w-40 mb-1">
                                        <button class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded">تأكيد</button>
                                    </form>
                                @else — @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">لا توجد سجلات ساعات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-4 py-2 bg-violet-50 border-b font-bold text-sm text-violet-900">مصاريف يومية (أكل، مشروبات…)</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-right">المكان</th>
                        <th class="px-3 py-2 text-right">التاريخ</th>
                        <th class="px-3 py-2 text-right">البيان</th>
                        <th class="px-3 py-2 text-right">الفئة</th>
                        <th class="px-3 py-2 text-right">المبلغ</th>
                        <th class="px-3 py-2 text-right">المُسجّل</th>
                        <th class="px-3 py-2 text-right">الحالة</th>
                        <th class="px-3 py-2 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($dailyExpenses as $expense)
                        <tr>
                            <td class="px-3 py-2">{{ $expense->location?->name }}</td>
                            <td class="px-3 py-2">{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">{{ $expense->title }}</td>
                            <td class="px-3 py-2 text-xs">{{ $expense->category_label }}</td>
                            <td class="px-3 py-2 font-semibold tabular-nums">{{ number_format($expense->lineTotal(), 2) }}</td>
                            <td class="px-3 py-2 text-xs">{{ $expense->logger?->name }}</td>
                            <td class="px-3 py-2 text-xs">{{ $expense->status_label }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if($expense->status === 'pending')
                                    <form action="{{ route('admin.place-daily-expenses.approve', $expense) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-emerald-600 font-medium text-xs">موافقة</button>
                                    </form>
                                    <button type="button" onclick="document.getElementById('reject-exp-{{ $expense->id }}').classList.toggle('hidden')" class="text-rose-600 font-medium text-xs mr-2">رفض</button>
                                    <form id="reject-exp-{{ $expense->id }}" action="{{ route('admin.place-daily-expenses.reject', $expense) }}" method="POST" class="hidden mt-1">
                                        @csrf
                                        <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="text-xs border rounded px-2 py-1 w-40 mb-1">
                                        <button class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded">تأكيد</button>
                                    </form>
                                @else — @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">لا توجد مصاريف يومية.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $dailyExpenses->links() }}
    </div>
</div>
@endsection
