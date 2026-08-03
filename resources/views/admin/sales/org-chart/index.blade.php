@extends('layouts.admin')

@section('title', 'هيكل المبيعات')
@section('header', 'المبيعات — الهيكل الهرمي')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
        <h2 class="text-xl font-black text-slate-900">خريطة الترابط بين موظفي السيلز</h2>
        <p class="text-xs text-slate-600 mt-1">حدد المدير المباشر لكل فرد لبناء علاقة تكاملية واضحة. الفرق اليومية تبقى كما هي.</p>
    </section>

    <section class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
        @forelse($tree as $node)
            @include('admin.sales.org-chart._node', ['node' => $node, 'depth' => 0, 'staff' => $staff, 'openCounts' => $openCounts, 'readonly' => false])
        @empty
            <p class="text-sm text-slate-500 p-4">لا يوجد هيكل بعد — عيّن المدير المباشر من القائمة بالأسفل.</p>
        @endforelse
    </section>

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-black">كل موظفي السيلز — ربط سريع</div>
        <div class="divide-y">
            @foreach($staff as $user)
                <form method="post" action="{{ route('admin.sales.org-chart.update', $user) }}" class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    @csrf @method('PUT')
                    <div class="min-w-[160px] font-bold text-slate-900">{{ $user->name }}</div>
                    <select name="sales_reports_to_id" class="rounded-xl border px-3 py-2 text-sm flex-1">
                        <option value="">— بدون —</option>
                        @foreach($staff as $cand)
                            @if($cand->id !== $user->id)
                                <option value="{{ $cand->id }}" @selected((int)$user->sales_reports_to_id === (int)$cand->id)>{{ $cand->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-xs font-semibold">تحديث</button>
                </form>
            @endforeach
        </div>
    </section>
</div>
@endsection
