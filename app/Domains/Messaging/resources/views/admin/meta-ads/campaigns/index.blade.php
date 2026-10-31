@extends('layouts.admin')

@section('title', 'Meta Ads')
@section('header', 'Meta Ads — الحملات')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif
    @if($error)
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm font-medium">{{ $error }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <span class="text-gray-700 font-semibold">التسويق</span>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">Meta Ads</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fab fa-meta text-blue-600"></i>
                    حملات Meta Ads
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    @if(($connection['success'] ?? false))
                        {{ $connection['label'] }}
                    @else
                        إدارة مباشرة عبر Marketing API
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.meta-ads.settings') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium border border-gray-200 inline-flex items-center gap-2">
                    <i class="fas fa-cog"></i> الإعدادات
                </a>
                <a href="{{ route('admin.meta-ads.campaigns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> حملة جديدة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl p-5 border-2 border-blue-200/50 shadow-xl bg-white">
            <p class="text-sm font-semibold text-gray-600">إجمالي</p>
            <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl p-5 border-2 border-emerald-200/50 shadow-xl bg-white">
            <p class="text-sm font-semibold text-gray-600">نشطة</p>
            <p class="text-3xl font-black text-emerald-700">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl p-5 border-2 border-slate-200 shadow-xl bg-white">
            <p class="text-sm font-semibold text-gray-600">متوقفة / أخرى</p>
            <p class="text-3xl font-black text-slate-700">{{ number_format($stats['paused'] ?? 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-bold">الحملة</th>
                        <th class="text-right px-4 py-3 font-bold">الهدف</th>
                        <th class="text-right px-4 py-3 font-bold">الحالة</th>
                        <th class="text-right px-4 py-3 font-bold">الميزانية</th>
                        <th class="text-right px-4 py-3 font-bold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $c)
                        @php
                            $status = strtoupper((string) ($c['effective_status'] ?? $c['status'] ?? ''));
                            $isActive = $status === 'ACTIVE';
                            $budget = $c['daily_budget'] ?? $c['lifetime_budget'] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.meta-ads.campaigns.show', $c['id']) }}" class="font-bold text-slate-900 hover:text-blue-700">{{ $c['name'] ?? '—' }}</a>
                                <div class="text-xs text-slate-400 font-mono mt-0.5" dir="ltr">{{ $c['id'] ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $c['objective'] ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $status ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800" dir="ltr">
                                @if($budget !== null)
                                    {{ number_format($metaAds->fromMinorUnits($budget), 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.meta-ads.campaigns.show', $c['id']) }}" class="text-blue-600 hover:underline font-semibold text-xs">إدارة</a>
                                    @if($isActive)
                                        <form method="post" action="{{ route('admin.meta-ads.campaigns.pause', $c['id']) }}" onsubmit="return confirm('إيقاف الحملة؟')">
                                            @csrf
                                            <button type="submit" class="text-amber-700 hover:underline font-semibold text-xs">إيقاف</button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('admin.meta-ads.campaigns.resume', $c['id']) }}">
                                            @csrf
                                            <button type="submit" class="text-emerald-700 hover:underline font-semibold text-xs">تشغيل</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                لا توجد حملات بعد.
                                <a href="{{ route('admin.meta-ads.campaigns.create') }}" class="text-blue-600 font-semibold hover:underline">أنشئ أول حملة</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
