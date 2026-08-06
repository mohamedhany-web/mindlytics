@extends('layouts.employee')

@section('title', 'اعتماد تبديل الشيفتات')
@section('header', 'طلبات تبديل الشيفت')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-amber-50">
            <h2 class="text-base font-black text-amber-900">بانتظار الاعتماد ({{ $pending->count() }})</h2>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse($pending as $swap)
                <li class="p-4">
                    <p class="font-bold text-slate-900">{{ $swap->requester->name }} يريد التبديل مع {{ $swap->partner->name }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ $swap->work_date->format('Y-m-d') }} — {{ $swap->reason }}</p>
                    <form method="post" action="{{ route('employee.sales-manager.shift-swaps.review', $swap) }}" class="mt-3 flex flex-wrap gap-2 items-end">
                        @csrf
                        <input type="text" name="manager_notes" placeholder="ملاحظة (اختياري)" class="rounded-xl border border-slate-300 px-3 py-2 text-sm flex-1 min-w-[200px]">
                        <button type="submit" name="action" value="approve" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 text-sm">اعتماد</button>
                        <button type="submit" name="action" value="reject" class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold px-4 py-2 text-sm">رفض</button>
                    </form>
                </li>
            @empty
                <li class="p-8 text-center text-slate-500 text-sm">لا طلبات معلّقة.</li>
            @endforelse
        </ul>
    </section>

    @if($recent->isNotEmpty())
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-base font-black text-slate-900">سجل الاعتمادات</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($recent as $swap)
                    <li class="px-4 py-3 text-sm">
                        <p class="font-semibold text-slate-900">{{ $swap->requester->name }} ↔ {{ $swap->partner->name }} — {{ $swap->statusLabel() }}</p>
                        <p class="text-xs text-slate-500">{{ $swap->work_date->format('Y-m-d') }} · {{ optional($swap->reviewer)->name }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
