@extends('layouts.admin')

@section('title', 'إنشاء حملة Meta')
@section('header', 'Meta Ads — حملة جديدة')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 font-semibold text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">إنشاء حملة Meta Ads</h1>
            <p class="text-sm text-gray-600 mt-1">تُنشأ الحملة + مجموعة إعلانات واحدة (Ad Set) مع استهداف مبسّط. الافتراضي: متوقفة للمراجعة.</p>
        </div>
        <a href="{{ route('admin.meta-ads.campaigns.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-700">رجوع للقائمة</a>
    </div>

    <form method="post" action="{{ route('admin.meta-ads.campaigns.store') }}" class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl p-6 space-y-6 max-w-3xl">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الحملة</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="200"
                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500"
                   placeholder="مثال: كورسات صيف 2026 — مصر">
            @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الهدف (Objective)</label>
                <select name="objective" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    @foreach([
                        'OUTCOME_TRAFFIC' => 'زيارات (Traffic)',
                        'OUTCOME_LEADS' => 'عملاء محتملون (Leads)',
                        'OUTCOME_SALES' => 'مبيعات (Sales / Purchase)',
                        'OUTCOME_ENGAGEMENT' => 'تفاعل (Engagement)',
                        'OUTCOME_AWARENESS' => 'وعي (Awareness)',
                    ] as $val => $label)
                        <option value="{{ $val }}" @selected(old('objective', 'OUTCOME_TRAFFIC') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">الحالة عند الإنشاء</label>
                <select name="status" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    <option value="PAUSED" @selected(old('status', 'PAUSED') === 'PAUSED')>متوقفة (مستحسن)</option>
                    <option value="ACTIVE" @selected(old('status') === 'ACTIVE')>نشطة فورًا</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">الميزانية اليومية ({{ $defaults['currency'] ?? 'EGP' }})</label>
            <input type="number" step="0.01" min="1" name="daily_budget" value="{{ old('daily_budget', 100) }}" required
                   class="w-full max-w-xs rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono" dir="ltr">
            @error('daily_budget')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl border border-slate-200 p-4 space-y-4">
            <h3 class="text-sm font-black text-slate-800"><i class="fas fa-users text-blue-500 ml-1"></i> الجمهور (استهداف مبسّط)</h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">العمر من</label>
                    <input type="number" name="age_min" min="13" max="65" value="{{ old('age_min', 18) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">العمر إلى</label>
                    <input type="number" name="age_max" min="13" max="65" value="{{ old('age_max', 45) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">الجنس</label>
                    <select name="genders" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="all" @selected(old('genders', 'all') === 'all')>الكل</option>
                        <option value="male" @selected(old('genders') === 'male')>ذكور</option>
                        <option value="female" @selected(old('genders') === 'female')>إناث</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الدول (رموز مفصولة بفاصلة)</label>
                <input type="text" name="countries" value="{{ old('countries', $defaults['country'] ?? 'EG') }}" dir="ltr"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" placeholder="EG,SA,AE">
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-900">
            إنشاء الإعلان الإبداعي (Creative / Ad) ما زال يتم غالبًا من Ads Manager للصور/الفيديو.
            من هنا تدير الحملة، الميزانية، التشغيل/الإيقاف، والجمهور الأساسي.
        </div>

        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
            <i class="fas fa-rocket"></i> إنشاء الحملة
        </button>
    </form>
</div>
@endsection
