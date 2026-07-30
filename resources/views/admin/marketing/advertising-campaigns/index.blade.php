@extends('layouts.admin')

@section('title', 'الحملات الإعلانية')
@section('header', 'الحملات الإعلانية')

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'inactive' => 0, 'total_cost' => 0];
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    {{-- الهيدر --}}
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <span class="text-gray-700 font-semibold">التسويق</span>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">الحملات الإعلانية</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">الحملات الإعلانية</h1>
                <p class="text-gray-600 mt-1">أنشئ الحملة بتكلفتها، وحدد موظفي السيلز الذين يرفعون بيانات الكامبين يومياً.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.advertising-campaigns.reports') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200 inline-flex items-center gap-2">
                    <i class="fas fa-chart-column"></i>
                    تقارير الكامبين
                </a>
                <a href="{{ route('admin.advertising-campaigns.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    حملة جديدة
                </a>
            </div>
        </div>
    </div>

    {{-- إحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 border-2 border-blue-200/50 shadow-xl relative overflow-hidden group"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,249,255,0.95) 100%);">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الحملات</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-rectangle-ad text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-emerald-200/50 shadow-xl relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(236,253,245,0.95) 100%);">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">نشطة</p>
                    <p class="text-3xl font-black text-emerald-700">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-slate-200/70 shadow-xl relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.95) 100%);">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">متوقفة</p>
                    <p class="text-3xl font-black text-slate-700">{{ number_format($stats['inactive']) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-slate-500 to-slate-700 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-pause text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-amber-200/50 shadow-xl relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,251,235,0.95) 100%);">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي التكلفة</p>
                    <p class="text-2xl sm:text-3xl font-black text-amber-700">{{ number_format($stats['total_cost'], 0) }}</p>
                    <p class="text-xs text-amber-800/70 font-semibold mt-0.5">ج.م</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-coins text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- الجدول --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-list text-sm"></i>
                </span>
                قائمة الحملات
            </h2>
            <span class="text-xs font-semibold text-gray-500">{{ $campaigns->total() }} حملة</span>
        </div>

        @if($campaigns->isEmpty())
            <div class="px-6 py-16 text-center text-gray-500">
                <i class="fas fa-rectangle-ad text-5xl text-gray-300 mb-4 block"></i>
                <p class="font-semibold text-gray-700 mb-1">لا توجد حملات إعلانية بعد</p>
                <p class="text-sm mb-4">ابدأ بإنشاء أول حملة وربط موظفي السيلز بها.</p>
                <a href="{{ route('admin.advertising-campaigns.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">
                    <i class="fas fa-plus"></i> حملة جديدة
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm text-right">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-bold uppercase tracking-wide text-gray-500">
                            <th class="px-4 py-3">الحملة</th>
                            <th class="px-4 py-3">المنصة</th>
                            <th class="px-4 py-3">التكلفة</th>
                            <th class="px-4 py-3">موظفو السيلز</th>
                            <th class="px-4 py-3">النتائج</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($campaigns as $campaign)
                            @php $agg = $aggregates[$campaign->id] ?? null; @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-4">
                                    <p class="font-bold text-gray-900">{{ $campaign->name }}</p>
                                    @if($campaign->start_date || $campaign->end_date)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="far fa-calendar-alt ml-1"></i>
                                            {{ $campaign->start_date?->format('Y-m-d') ?? '—' }}
                                            →
                                            {{ $campaign->end_date?->format('Y-m-d') ?? 'مستمرة' }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        {{ $campaign->platformLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-gray-900">{{ number_format((float) $campaign->cost, 2) }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $campaign->currency }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        <i class="fas fa-user-group"></i> {{ $campaign->sales_employees_count }}
                                    </span>
                                    @if($campaign->salesEmployees->isNotEmpty())
                                        <p class="text-xs text-gray-500 mt-1 max-w-[180px] truncate" title="{{ $campaign->salesEmployees->pluck('name')->join('، ') }}">
                                            {{ $campaign->salesEmployees->pluck('name')->join('، ') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-gray-700">
                                    @if($agg)
                                        <p>
                                            <span class="font-bold text-gray-900">{{ number_format($agg['new_messages']) }}</span> رسالة
                                            ·
                                            <span class="font-bold text-emerald-700">{{ number_format($agg['converted']) }}</span> تحويل
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $agg['days'] }} يوم مُسجّل</p>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($campaign->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> نشطة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> متوقفة
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.advertising-campaigns.reports', ['campaign_id' => $campaign->id]) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                            <i class="fas fa-chart-column"></i> تقارير
                                        </a>
                                        <a href="{{ route('admin.advertising-campaigns.edit', $campaign) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 hover:bg-sky-100">
                                            <i class="fas fa-pen"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.advertising-campaigns.destroy', $campaign) }}" method="POST" class="inline"
                                              onsubmit="return confirm('حذف هذه الحملة وكل تقاريرها؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 hover:bg-rose-100">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($campaigns->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $campaigns->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
