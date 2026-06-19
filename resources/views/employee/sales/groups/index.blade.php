@extends('layouts.employee')

@section('title', 'مجموعات العملاء')
@section('header', 'مجموعات العملاء')

@section('content')
@include('employee.sales.groups._styles')

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">مجموعات العملاء</h2>
            <p class="text-sm text-slate-500 mt-0.5">قسّم عملاءك لحملات، مناطق، أو أي تصنيف يناسبك</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales.dashboard') }}" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50">مركز المبيعات</a>
            <a href="{{ route('employee.sales.groups.create') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold">
                <i class="fas fa-plus ml-1"></i> مجموعة جديدة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <p class="text-xs text-slate-500">المجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['groups'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">عملاء في مجموعات</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['leads'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">مجموعاتي</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['mine'] ?? 0 }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-slate-500">من الإدارة</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $stats['admin'] ?? 0 }}</p>
        </div>
    </div>

    @if($groups->isEmpty())
        <div class="sales-panel p-8 text-center">
            <i class="fas fa-layer-group text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-600 mb-4">لا توجد مجموعات بعد — أنشئ مجموعة عند تسجيل العملاء أو من هنا.</p>
            <a href="{{ route('employee.sales.groups.create') }}" class="inline-flex px-5 py-2.5 bg-slate-800 text-white rounded-lg text-sm font-semibold">إنشاء أول مجموعة</a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($groups as $group)
                <a href="{{ route('employee.sales.groups.show', $group) }}" class="group-card block">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-bold text-slate-900">{{ $group->name }}</h3>
                        @if($group->is_admin_managed)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-semibold shrink-0">إدارة</span>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold shrink-0">خاصة</span>
                        @endif
                    </div>
                    @if($group->description)
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $group->description }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100 text-sm">
                        <span class="text-slate-600"><i class="fas fa-users ml-1 text-slate-400"></i> {{ $group->leads_count }} عميل</span>
                        <span class="text-slate-800 font-semibold">إدارة ←</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
