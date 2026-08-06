@extends('layouts.admin')

@section('title', 'إعدادات Meta Ads')
@section('header', 'Meta Ads — الإعدادات')

@section('content')
@php
    $s = $settings ?? [];
    $social = $s['meta_social'] ?? [];
    $socialConnected = (bool) ($social['connected'] ?? false);
    $adAccounts = $adAccounts ?? [];
@endphp
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 font-semibold text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 font-semibold text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <nav class="text-sm text-gray-500 mb-1">
                <span class="font-semibold text-gray-700">التسويق</span>
                <span class="mx-2">/</span>
                <span class="font-semibold text-gray-700">Meta Ads</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fab fa-meta text-blue-600"></i>
                إعدادات حساب الإعلانات
            </h1>
            <p class="text-gray-600 mt-1 text-sm">يستخدم نفس ربط Meta الموجود في السوشيال ميديا — بدون إدخال توكن جديد.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.meta-social.settings') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-share-nodes"></i> ربط السوشيال
            </a>
            <a href="{{ route('admin.meta-ads.campaigns.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-rectangle-ad"></i> الحملات
            </a>
            @if($s['has_access_token'] ?? false)
            <form method="post" action="{{ route('admin.meta-ads.settings.test') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-plug"></i> اختبار الاتصال
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Shared connection status --}}
    <div class="rounded-xl border px-4 py-4 text-sm {{ $socialConnected ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-amber-200 bg-amber-50 text-amber-900' }}">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="font-bold">{{ $social['label'] ?? '—' }}</p>
                <p class="text-xs mt-1 opacity-90">
                    مصدر التوكن:
                    @if(($s['token_source'] ?? '') === 'meta_social')
                        ربط Meta Social (OAuth)
                    @elseif(($s['token_source'] ?? '') === 'override')
                        توكن مخصّص / .env
                    @else
                        غير متوفر
                    @endif
                    · Graph: <code class="font-mono" dir="ltr">{{ $s['api_url'] ?? '' }}</code>
                </p>
            </div>
            @unless($socialConnected)
                <a href="{{ route('admin.meta-social.settings') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-600 text-white text-xs font-bold hover:bg-amber-700">
                    اذهب للربط
                </a>
            @endunless
        </div>
        @if(is_array($connection ?? null) && !empty($connection['error']) && !($connection['success'] ?? false))
            <p class="text-xs mt-2 font-normal opacity-90">{{ $connection['error'] }}</p>
        @endif
        @if(is_array($connection ?? null) && ($connection['success'] ?? false))
            <p class="text-xs mt-2 font-semibold text-emerald-800">{{ $connection['label'] }}</p>
        @endif
    </div>

    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-black text-slate-900">اختيار حساب الإعلانات</h2>
            <p class="text-xs text-slate-600 mt-1">لا حاجة لإعادة إدخال App ID أو Access Token — اختر Ad Account المرتبط بنفس حساب Meta.</p>
        </div>
        <div class="p-6">
            <form method="post" action="{{ route('admin.meta-ads.settings.update') }}" class="space-y-6 max-w-3xl">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $s['enabled'] ?? true)) class="mt-1 rounded border-slate-300 text-blue-600">
                    <span>
                        <span class="block text-sm font-bold text-slate-900">تفعيل Meta Ads داخل النظام</span>
                        <span class="block text-xs text-slate-500 mt-0.5">يستخدم توكن السوشيال تلقائيًا لإدارة الحملات.</span>
                    </span>
                </label>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">حساب الإعلانات (Ad Account)</label>
                    @if(count($adAccounts) > 0)
                        <select name="ad_account_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" dir="ltr">
                            <option value="">— اختر حسابًا —</option>
                            @foreach($adAccounts as $acc)
                                @php
                                    $accId = \App\Support\MetaAdsSettings::normalizeAdAccountId((string) ($acc['id'] ?? $acc['account_id'] ?? ''));
                                    $label = trim(($acc['name'] ?? '').' · '.$accId.' · '.($acc['currency'] ?? ''));
                                @endphp
                                <option value="{{ $accId }}" @selected(old('ad_account_id', $s['ad_account_id'] ?? '') === $accId)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="ad_account_id" value="{{ old('ad_account_id', $s['ad_account_id'] ?? '') }}"
                               placeholder="act_1234567890" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                        @if($adAccountsError)
                            <p class="text-xs text-amber-700 mt-2 leading-relaxed">
                                {{ $adAccountsError }}
                                <br>إن كان الخطأ بسبب الصلاحيات: من إعدادات السوشيال أعد الربط بعد التأكد أن الـ scopes تشمل
                                <code class="font-mono">ads_management</code> و <code class="font-mono">ads_read</code>
                                (أُضيفت تلقائيًا للإعدادات الافتراضية).
                            </p>
                        @elseif(!$socialConnected)
                            <p class="text-xs text-slate-500 mt-1">اربط Meta Social أولًا لعرض الحسابات تلقائيًا، أو الصق Ad Account ID يدويًا.</p>
                        @endif
                    @endif
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">العملة الافتراضية</label>
                        <input type="text" name="default_currency" value="{{ old('default_currency', $s['default_currency'] ?? 'EGP') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">دولة الاستهداف الافتراضية</label>
                        <input type="text" name="default_country" value="{{ old('default_country', $s['default_country'] ?? 'EG') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono" placeholder="EG">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Facebook Page ID</label>
                        <input type="text" name="page_id" value="{{ old('page_id', $s['page_id'] ?? '') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono"
                               placeholder="يُملأ تلقائيًا من الصفحات المربوطة إن وُجدت">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instagram Actor ID (اختياري)</label>
                        <input type="text" name="instagram_actor_id" value="{{ old('instagram_actor_id', $s['instagram_actor_id'] ?? '') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-xs text-blue-900 leading-relaxed">
                    التوكن يُؤخذ من <strong>السوشيال ميديا → إعدادات Meta</strong> بعد OAuth.
                    إن فشل جلب الحسابات أو الحملات، أعد تسجيل الدخول لـ Meta مرة واحدة لتمنح صلاحيات الإعلانات الجديدة.
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold shadow-lg">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
