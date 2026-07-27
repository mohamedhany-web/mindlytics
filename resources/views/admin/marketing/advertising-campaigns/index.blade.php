@extends('layouts.admin')
@section('title', 'الحملات الإعلانية')
@section('header', 'الحملات الإعلانية')
@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">الحملات الإعلانية</h1>
                <p class="text-slate-500 mt-1">أنشئ الحملة بتكلفتها المادية، وحدّد موظفي السيلز الذين سيرفعون بيانات الكامبين يومياً في تقريرهم.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.advertising-campaigns.reports') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-all">
                    <i class="fas fa-chart-column"></i>
                    <span>تقارير الكامبين</span>
                </a>
                <a href="{{ route('admin.advertising-campaigns.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                    <i class="fas fa-plus"></i>
                    <span>حملة جديدة</span>
                </a>
            </div>
        </div>
        <div class="p-5 sm:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">الحملة</th>
                            <th class="px-4 py-3">المنصة</th>
                            <th class="px-4 py-3">التكلفة</th>
                            <th class="px-4 py-3">موظفو السيلز</th>
                            <th class="px-4 py-3">النتائج (رسائل / Converted)</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($campaigns as $campaign)
                        @php $agg = $aggregates[$campaign->id] ?? null; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">
                                {{ $campaign->name }}
                                @if($campaign->start_date || $campaign->end_date)
                                    <span class="block text-xs text-slate-400 mt-0.5">
                                        {{ $campaign->start_date?->format('Y-m-d') ?? '—' }} ← {{ $campaign->end_date?->format('Y-m-d') ?? 'مستمرة' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $campaign->platformLabel() }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ number_format((float) $campaign->cost, 2) }} {{ $campaign->currency }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-700">
                                    <i class="fas fa-user-group"></i> {{ $campaign->sales_employees_count }}
                                </span>
                                @if($campaign->salesEmployees->isNotEmpty())
                                    <span class="block text-xs text-slate-400 mt-1 max-w-[180px] truncate">{{ $campaign->salesEmployees->pluck('name')->join('، ') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if($agg)
                                    <span class="font-semibold text-slate-800">{{ number_format($agg['new_messages']) }}</span> رسالة
                                    · <span class="font-semibold text-emerald-700">{{ number_format($agg['converted']) }}</span> تحويل
                                    <span class="block text-xs text-slate-400">{{ $agg['days'] }} يوم مُسجّل</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($campaign->is_active)
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">نشطة</span>
                                @else
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">متوقفة</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.advertising-campaigns.reports', ['campaign_id' => $campaign->id]) }}" class="text-indigo-600 hover:text-indigo-700 font-medium ml-2">التقارير</a>
                                <a href="{{ route('admin.advertising-campaigns.edit', $campaign) }}" class="text-sky-600 hover:text-sky-700 font-medium ml-2">تعديل</a>
                                <form action="{{ route('admin.advertising-campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذه الحملة وكل تقاريرها؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                <i class="fas fa-rectangle-ad text-4xl text-slate-300 mb-3 block"></i>
                                <p>لا توجد حملات إعلانية. <a href="{{ route('admin.advertising-campaigns.create') }}" class="text-sky-600 hover:underline">أنشئ حملة جديدة</a></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $campaigns->links() }}</div>
        </div>
    </div>
</div>
@endsection
