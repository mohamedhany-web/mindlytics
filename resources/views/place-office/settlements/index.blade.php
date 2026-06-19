@extends('layouts.place-manager')

@section('title', 'المخالصة الشهرية')
@section('header', 'المخالصة الشهرية — ' . $location->name)

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <h1 class="text-xl font-bold">المخالصات الشهرية</h1>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">الساعات</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($settlements as $s)
                    <tr>
                        <td class="px-4 py-3">{{ $s->period_month }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $s->total_hours, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $s->total_amount, 2) }} {{ $s->currency }}</td>
                        <td class="px-4 py-3">{{ $s->status_label }}</td>
                        <td class="px-4 py-3"><a href="{{ route('place.office.settlements.show', $s) }}" class="text-violet-600 font-medium">التفاصيل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد مخالصات.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $settlements->links() }}
</div>
@endsection
