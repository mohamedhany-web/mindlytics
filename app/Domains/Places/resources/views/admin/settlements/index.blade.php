@extends('layouts.admin')

@section('title', 'مخالصات الأماكن')
@section('header', 'المخالصات الشهرية للأماكن')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 bg-white p-4 rounded-xl border">
        <select name="location_id" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الأماكن</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">كل الحالات</option>
            @foreach(['open','submitted','approved','closed','paid'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
            @endforeach
        </select>
        <input type="month" name="month" value="{{ request('month') }}" class="rounded-lg border-slate-300 text-sm">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">تصفية</button>
    </form>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">المكان</th>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">ساعات</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($settlements as $s)
                    <tr>
                        <td class="px-4 py-3">{{ $s->location?->name }}</td>
                        <td class="px-4 py-3">{{ $s->period_month }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $s->total_hours, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $s->total_amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $s->status_label }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.place-settlements.show', $s) }}" class="text-blue-600">مراجعة</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد مخالصات.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $settlements->links() }}
</div>
@endsection
