@extends('layouts.admin')

@section('title', 'مدفوعات الفرع')
@section('header', 'مدفوعات الفرع — ' . $branch->name)

@section('content')
<div class="p-6 lg:p-8 max-w-[1400px] mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
            <input type="text" name="status" value="{{ request('status') }}" placeholder="مثال: completed"
                   class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-48">
        </div>
        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">المستخدم</th>
                        <th class="text-right px-4 py-3 font-semibold">المبلغ</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $p)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $p->id }}</td>
                            <td class="px-4 py-3">{{ $p->user->name ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ number_format((float) $p->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $p->status }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $payments->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
