@extends('layouts.admin')

@section('title', $campaign['name'] ?? 'حملة Meta')
@section('header', 'Meta Ads — تفاصيل الحملة')

@section('content')
@php
    $status = strtoupper((string) ($campaign['effective_status'] ?? $campaign['status'] ?? ''));
    $isActive = $status === 'ACTIVE';
    $adsets = $campaign['adsets'] ?? [];
    $firstAdset = $adsets[0] ?? null;
    $currentBudget = null;
    if (is_array($firstAdset) && isset($firstAdset['daily_budget'])) {
        $currentBudget = $metaAds->fromMinorUnits($firstAdset['daily_budget']);
    } elseif (isset($campaign['daily_budget'])) {
        $currentBudget = $metaAds->fromMinorUnits($campaign['daily_budget']);
    }
    $targeting = is_array($firstAdset['targeting'] ?? null) ? $firstAdset['targeting'] : [];
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:justify-between gap-4">
            <div>
                <a href="{{ route('admin.meta-ads.campaigns.index') }}" class="text-sm text-blue-600 font-semibold hover:underline">← كل الحملات</a>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $campaign['name'] ?? '—' }}</h1>
                <p class="text-xs font-mono text-slate-400 mt-1" dir="ltr">{{ $campaign['id'] ?? '' }}</p>
                <div class="mt-3 flex flex-wrap gap-2 items-center">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $status ?: '—' }}</span>
                    <span class="text-xs text-slate-500">{{ $campaign['objective'] ?? '' }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 items-start">
                @if($isActive)
                    <form method="post" action="{{ route('admin.meta-ads.campaigns.pause', $campaign['id']) }}" onsubmit="return confirm('إيقاف الحملة؟')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold inline-flex items-center gap-2">
                            <i class="fas fa-pause"></i> إيقاف
                        </button>
                    </form>
                @else
                    <form method="post" action="{{ route('admin.meta-ads.campaigns.resume', $campaign['id']) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold inline-flex items-center gap-2">
                            <i class="fas fa-play"></i> تشغيل
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <section class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-sm font-black text-slate-800 mb-4"><i class="fas fa-coins text-amber-500 ml-1"></i> الميزانية اليومية</h2>
            <form method="post" action="{{ route('admin.meta-ads.campaigns.budget', $campaign['id']) }}" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">المبلغ ({{ $currency }})</label>
                    <input type="number" step="0.01" min="1" name="daily_budget" value="{{ old('daily_budget', $currentBudget ?? 100) }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono" dir="ltr" required>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold">تحديث الميزانية</button>
            </form>
            <p class="text-xs text-slate-500 mt-3">يُحدَّث Ad Set الأول المرتبط بالحملة.</p>
        </section>

        <section class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-sm font-black text-slate-800 mb-4"><i class="fas fa-users text-blue-500 ml-1"></i> الجمهور</h2>
            <form method="post" action="{{ route('admin.meta-ads.campaigns.audience', $campaign['id']) }}" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">من عمر</label>
                        <input type="number" name="age_min" min="13" max="65" value="{{ old('age_min', $targeting['age_min'] ?? 18) }}" class="w-full rounded-xl border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">إلى عمر</label>
                        <input type="number" name="age_max" min="13" max="65" value="{{ old('age_max', $targeting['age_max'] ?? 45) }}" class="w-full rounded-xl border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الجنس</label>
                        @php
                            $g = $targeting['genders'] ?? null;
                            $gendersVal = 'all';
                            if (is_array($g) && $g === [1]) $gendersVal = 'male';
                            if (is_array($g) && $g === [2]) $gendersVal = 'female';
                        @endphp
                        <select name="genders" class="w-full rounded-xl border px-3 py-2 text-sm">
                            <option value="all" @selected(old('genders', $gendersVal) === 'all')>الكل</option>
                            <option value="male" @selected(old('genders', $gendersVal) === 'male')>ذكور</option>
                            <option value="female" @selected(old('genders', $gendersVal) === 'female')>إناث</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الدول</label>
                    <input type="text" name="countries" dir="ltr"
                           value="{{ old('countries', implode(',', $targeting['geo_locations']['countries'] ?? ['EG'])) }}"
                           class="w-full rounded-xl border px-3 py-2 text-sm font-mono">
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تحديث الجمهور</button>
            </form>
        </section>
    </div>

    <section class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-slate-50">
            <h2 class="text-sm font-black text-slate-800">مجموعات الإعلانات (Ad Sets)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white text-slate-500">
                    <tr>
                        <th class="text-right px-4 py-2">الاسم</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">ميزانية يومية</th>
                        <th class="text-right px-4 py-2">Optimization</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($adsets as $as)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $as['name'] ?? '—' }}</div>
                                <div class="text-xs font-mono text-slate-400" dir="ltr">{{ $as['id'] ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $as['effective_status'] ?? $as['status'] ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono" dir="ltr">
                                {{ isset($as['daily_budget']) ? number_format($metaAds->fromMinorUnits($as['daily_budget']), 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $as['optimization_goal'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا توجد مجموعات إعلانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
