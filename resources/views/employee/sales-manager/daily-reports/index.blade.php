@extends('layouts.employee')

@section('title', 'تقارير أعضاء الفريق')
@section('header', 'تقارير أعضاء الفريق')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 max-w-md">
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-slate-500">مُسلَّمة</p><p class="text-2xl font-bold">{{ $stats['submitted'] }}</p></div>
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-slate-500">بانتظار مراجعتك</p><p class="text-2xl font-bold text-amber-700">{{ $stats['pending_review'] }}</p></div>
    </div>
    <form method="GET" class="flex flex-wrap gap-3 bg-white rounded-xl border p-4">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="px-3 py-2 border rounded-lg text-sm">
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="px-3 py-2 border rounded-lg text-sm">
        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">كل الحالات</option>
            <option value="submitted" @selected(request('status')==='submitted')>مُسلَّم</option>
            <option value="draft" @selected(request('status')==='draft')>مسودة</option>
        </select>
        <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
    </form>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">الموظف</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">مراجع</th>
                <th class="px-4 py-3"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($reports as $r)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $r->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $r->report_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $r->status === 'submitted' ? 'مُسلَّم' : 'مسودة' }}</td>
                        <td class="px-4 py-3">{{ $r->manager_reviewed_at ? 'نعم' : '—' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('employee.sales-manager.daily-reports.show', $r) }}" class="text-emerald-700 font-semibold">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد تقارير.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reports->hasPages())<div class="px-4 py-3">{{ $reports->links() }}</div>@endif
    </div>
</div>
@endsection
