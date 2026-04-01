@extends('layouts.employee')

@section('title', 'خطط التسويق والمنصات')
@section('header', 'خطط التسويق والسوشيال ميديا')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.marketing-plans.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-plus"></i>
            خطة تسويق جديدة
        </a>
        <p class="text-sm text-gray-600 max-w-xl">نظّم المنصات، اربط الأحداث بالتقويم، واربط الخطة بدورة تصميم عند الحاجة. تظهر أحداث التسويق في <a href="{{ route('employee.calendar') }}" class="text-pink-600 font-semibold underline">تقويم الموظف</a>.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">منصات</th>
                        <th class="text-right px-4 py-3 font-semibold">أحداث</th>
                        <th class="text-right px-4 py-3 font-semibold">تحديث</th>
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($plans as $p)
                        @php
                            $st = match($p->status) {
                                'draft' => ['مسودة', 'bg-gray-100 text-gray-800'],
                                'active' => ['نشط', 'bg-emerald-100 text-emerald-800'],
                                'paused' => ['متوقف', 'bg-amber-100 text-amber-800'],
                                'completed' => ['مكتمل', 'bg-slate-200 text-slate-800'],
                                default => [$p->status, 'bg-gray-100'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs">{{ $p->id }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $p->title }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold {{ $st[1] }}">{{ $st[0] }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $p->platforms_count }}</td>
                            <td class="px-4 py-3">{{ $p->calendar_events_count }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $p->updated_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('employee.marketing-plans.show', $p) }}" class="text-pink-700 hover:text-pink-900 font-semibold">إدارة</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد خطط بعد. أنشئ خطة لربط المنصات والتقويم.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $plans->links() }}</div>
        @endif
    </div>
</div>
@endsection
