@extends('layouts.admin')

@section('title', 'تقارير فرق المبيعات')
@section('header', 'تقارير فرق المبيعات')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-5">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option value="submitted" @selected(request('status')==='submitted')>مُرسَل</option>
                <option value="draft" @selected(request('status')==='draft')>مسودة</option>
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border rounded-lg text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border rounded-lg text-sm">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm">تصفية</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">الفريق</th>
                <th class="px-4 py-3 text-right">المدير</th>
                <th class="px-4 py-3 text-right">تقارير مستلمة</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($reports as $r)
                    <tr>
                        <td class="px-4 py-3">{{ $r->report_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $r->team->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $r->manager->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $r->reports_received }}/{{ $r->team_members_count }}</td>
                        <td class="px-4 py-3">{{ $r->isSubmitted() ? 'مُرسَل' : 'مسودة' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.sales.team-daily-reports.show', $r) }}" class="text-emerald-700 font-semibold">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">لا توجد تقارير.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reports->hasPages())<div class="px-4 py-3">{{ $reports->links() }}</div>@endif
    </div>
</div>
@endsection
