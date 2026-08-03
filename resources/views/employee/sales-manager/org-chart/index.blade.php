@extends('layouts.employee')

@section('title', 'هيكل الفريق')
@section('header', 'هيكل فريق المبيعات')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border p-4 shadow">
        <h2 class="text-lg font-black">هيكل التكامل — {{ $team->name }}</h2>
        <p class="text-xs text-slate-600 mt-1">عرض كيف يرتبط أعضاء الفريق hierarchically تحتك.</p>
    </section>
    <section class="rounded-2xl bg-slate-50 border p-4">
        @forelse($tree as $node)
            @include('admin.sales.org-chart._node', ['node' => $node, 'depth' => 0, 'staff' => $staff, 'openCounts' => $openCounts, 'readonly' => true])
        @empty
            <p class="text-sm text-slate-500">لم يُضبط الهيكل بعد. اطلب من الإدارة ربط المدير المباشر لكل موظف.</p>
        @endforelse
    </section>
</div>
@endsection
