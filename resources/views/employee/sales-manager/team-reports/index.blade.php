@extends('layouts.employee')

@section('title', 'تقارير الفريق للإدارة')
@section('header', 'تقارير الفريق للإدارة')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-600">فريق: {{ $team->name }}</p>
        <a href="{{ route('employee.sales-manager.team-reports.edit') }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تقرير اليوم</a>
    </div>
    @if(session('success'))<div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">تقارير مستلمة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">تسليم</th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($reports as $r)
                    <tr>
                        <td class="px-4 py-3">{{ $r->report_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $r->reports_received }}/{{ $r->team_members_count }}</td>
                        <td class="px-4 py-3">{{ $r->isSubmitted() ? 'مُرسَل للإدارة' : 'مسودة' }}</td>
                        <td class="px-4 py-3">{{ $r->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">لم تُرفَع تقارير فريق بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reports->hasPages())<div class="px-4 py-3">{{ $reports->links() }}</div>@endif
    </div>
</div>
@endsection
