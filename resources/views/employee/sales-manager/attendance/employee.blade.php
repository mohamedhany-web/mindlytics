@extends('layouts.employee')

@section('title', 'حضور '.$employee->name)
@section('header', 'حضور '.$employee->name)

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'أيام مكتملة', 'value' => $summary['completed_days']],
            ['label' => 'تأخير', 'value' => $summary['late_days']],
            ['label' => 'إجمالي ساعات', 'value' => $summary['total_hours']],
            ['label' => 'أيام نشطة', 'value' => $summary['active_days']],
        ] as $s)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>
    <p><a href="{{ route('employee.sales-manager.attendance.index') }}" class="text-sm text-emerald-700 font-semibold">← العودة لحضور الفريق</a></p>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">دخول</th>
                <th class="px-4 py-3 text-right">خروج</th>
                <th class="px-4 py-3 text-right">الحالة</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($records as $rec)
                    <tr>
                        <td class="px-4 py-3">{{ $rec->work_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_in_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_out_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ \App\Models\EmployeeAttendanceRecord::statusLabels()[$rec->status] ?? $rec->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($records->hasPages())<div class="px-4 py-3">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
