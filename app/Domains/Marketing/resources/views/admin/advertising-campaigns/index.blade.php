@extends('layouts.admin')

@section('title', 'الحملات الإعلانية')
@section('header', 'الحملات الإعلانية')

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'inactive' => 0, 'total_cost' => 0];
    $linkedLocals = $linkedLocals ?? collect();
    $metaAds = $metaAds ?? null;
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif
    @if(!empty($error))
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm font-medium">{{ $error }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <span class="text-gray-700 font-semibold">التسويق</span>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">الحملات الإعلانية</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fab fa-meta text-blue-600"></i>
                    الحملات الإعلانية
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    القائمة تُقرأ مباشرة من Meta Ads
                    @if(($connection['success'] ?? false))
                        — {{ $connection['label'] }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.meta-ads.settings') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200 inline-flex items-center gap-2">
                    <i class="fas fa-cog"></i>
                    إعدادات Meta Ads
                </a>
                <a href="{{ route('admin.advertising-campaigns.reports') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200 inline-flex items-center gap-2">
                    <i class="fas fa-chart-column"></i>
                    تقارير السيلز
                </a>
                <a href="{{ route('admin.meta-ads.campaigns.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    حملة جديدة على ميتا
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 border-2 border-blue-200/50 shadow-xl relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,249,255,0.95) 100%);">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">حملات ميتا</p>
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
                    <p class="text-sm font-semibold text-gray-600 mb-1">متوقفة / أخرى</p>
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
                    <p class="text-sm font-semibold text-gray-600 mb-1">تكلفة محلية مربوطة</p>
                    <p class="text-2xl sm:text-3xl font-black text-amber-700">{{ number_format($stats['total_cost'], 0) }}</p>
                    <p class="text-xs text-amber-800/70 font-semibold mt-0.5">ج.م</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-coins text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fab fa-meta text-sm"></i>
                </span>
                حملات Meta
            </h2>
            <span class="text-xs font-semibold text-gray-500">{{ count($campaigns) }} حملة</span>
        </div>

        @if(empty($campaigns))
            <div class="px-6 py-16 text-center text-gray-500">
                <i class="fab fa-meta text-5xl text-gray-300 mb-4 block"></i>
                <p class="font-semibold text-gray-700 mb-1">لا توجد حملات على حساب ميتا</p>
                <p class="text-sm mb-4">أنشئ حملة من ميتا أو تأكد من اختيار Ad Account الصحيح.</p>
                <a href="{{ route('admin.meta-ads.campaigns.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">
                    <i class="fas fa-plus"></i> حملة جديدة على ميتا
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm text-right">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-bold uppercase tracking-wide text-gray-500">
                            <th class="px-4 py-3">الحملة</th>
                            <th class="px-4 py-3">الهدف</th>
                            <th class="px-4 py-3">الميزانية</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إعداد محلي</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($campaigns as $c)
                            @php
                                $metaId = (string) ($c['id'] ?? '');
                                $status = strtoupper((string) ($c['effective_status'] ?? $c['status'] ?? ''));
                                $isActive = $status === 'ACTIVE';
                                $budget = $c['daily_budget'] ?? $c['lifetime_budget'] ?? null;
                                $local = $linkedLocals->get($metaId);
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-4">
                                    <a href="{{ route('admin.meta-ads.campaigns.show', $metaId) }}" class="font-bold text-gray-900 hover:text-blue-700">
                                        {{ $c['name'] ?? '—' }}
                                    </a>
                                    <p class="text-xs text-gray-400 font-mono mt-1" dir="ltr">{{ $metaId }}</p>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $c['objective'] ?? '—' }}</td>
                                <td class="px-4 py-4 font-semibold text-gray-800" dir="ltr">
                                    @if($budget !== null && $metaAds)
                                        {{ number_format($metaAds->fromMinorUnits($budget), 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $status ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">
                                    @if($local)
                                        <p class="text-xs">
                                            تكلفة: <span class="font-bold">{{ number_format((float) $local->cost, 0) }}</span>
                                            · سيلز: <span class="font-bold">{{ $local->sales_employees_count }}</span>
                                        </p>
                                        @if($local->meta_leads_synced_at)
                                            <p class="text-[11px] text-emerald-700 mt-0.5">آخر سحب: {{ $local->meta_leads_synced_at->format('Y-m-d H:i') }}</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">غير مربوط محلياً</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.meta-ads.campaigns.show', $metaId) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100">
                                            <i class="fas fa-eye"></i> تفاصيل ميتا
                                        </a>
                                        <form method="POST" action="{{ route('admin.advertising-campaigns.meta.sync-leads', $metaId) }}"
                                              onsubmit="return confirm('سحب ليدز هذه الحملة من ميتا؟')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 hover:bg-sky-100">
                                                <i class="fas fa-cloud-download-alt"></i> سحب ليدز
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.advertising-campaigns.meta.local-settings', $metaId) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                            <i class="fas fa-users-cog"></i> تكلفة / سيلز
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
