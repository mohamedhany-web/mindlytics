@extends('layouts.admin')

@section('title', 'طلبات الفرع')
@section('header', 'طلبات الفرع — ' . $branch->name)

@section('content')
<div class="p-6 lg:p-8 max-w-[1400px] mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="{{ \App\Models\Order::STATUS_PENDING }}" {{ request('status') === \App\Models\Order::STATUS_PENDING ? 'selected' : '' }}>معلّق</option>
                <option value="{{ \App\Models\Order::STATUS_APPROVED }}" {{ request('status') === \App\Models\Order::STATUS_APPROVED ? 'selected' : '' }}>موافَق</option>
                <option value="{{ \App\Models\Order::STATUS_REJECTED }}" {{ request('status') === \App\Models\Order::STATUS_REJECTED ? 'selected' : '' }}>مرفوض</option>
            </select>
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
                        <th class="text-right px-4 py-3 font-semibold">الكورس / المسار</th>
                        <th class="text-right px-4 py-3 font-semibold">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $o)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $o->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $o->user->name ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ number_format((float) $o->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $o->status }}</td>
                            <td class="px-4 py-3 max-w-xs truncate">{{ $o->course?->title ?? ($o->learningPath?->name ?? '—') }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $orders->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
