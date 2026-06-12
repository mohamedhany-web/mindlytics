@extends('layouts.admin')

@section('title', 'كورسات أوفلاين — الفرع')
@section('header', 'كورسات أوفلاين — ' . $branch->name)

@section('content')
<div class="p-6 lg:p-8 max-w-[1400px] mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">بحث</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="عنوان الدورة"
                   class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
        </div>
        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">السعر</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">نشط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($courses as $c)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $c->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $c->title }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ number_format((float) $c->price, 2) }}</td>
                            <td class="px-4 py-3">{{ $c->status }}</td>
                            <td class="px-4 py-3">{{ $c->is_active ? 'نعم' : 'لا' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $courses->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
