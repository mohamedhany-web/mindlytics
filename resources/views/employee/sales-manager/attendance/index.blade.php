@extends('layouts.employee')

@section('title', 'حضور الفريق')
@section('header', 'حضور وغياب الفريق')

@section('content')
@php
    $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels();
@endphp
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            ['label' => 'سجلات', 'value' => $stats['total']],
            ['label' => 'مكتمل', 'value' => $stats['completed']],
            ['label' => 'متأخر', 'value' => $stats['late']],
            ['label' => 'جاري العمل', 'value' => $stats['active_now']],
            ['label' => 'غياب', 'value' => $stats['absent']],
        ] as $s)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold text-slate-900">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="employee_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" @selected(request('employee_id') == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">دخول</th>
                    <th class="px-4 py-3 text-right">خروج</th>
                    <th class="px-4 py-3 text-right">ساعات</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($records as $rec)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $rec->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->work_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_in_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_out_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->worked_minutes ? round($rec->worked_minutes / 60, 1) : '—' }}</td>
                        <td class="px-4 py-3">{{ $statusLabels[$rec->status] ?? $rec->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد سجلات.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($records->hasPages())<div class="px-4 py-3 border-t">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
