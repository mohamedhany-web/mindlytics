@extends('layouts.admin')

@section('title', 'إعدادات Meta Ads')
@section('header', 'Meta Ads — الإعدادات')

@section('content')
@php
    $s = $settings ?? [];
    $social = $s['meta_social'] ?? [];
    $socialConnected = (bool) ($social['connected'] ?? false);
    $adAccounts = $adAccounts ?? [];
    $selectedId = old('ad_account_id', $s['ad_account_id'] ?? '');
    $needsAdsReauth = (bool) ($needsAdsReauth ?? false);
    $statusLabels = [
        1 => 'نشط',
        2 => 'معطّل',
        3 => 'غير مسوّى',
        7 => 'في انتظار المراجعة',
        9 => 'في وضع الترقية',
        100 => 'معلّق',
        101 => 'مغلق',
    ];
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
                اختر حساب الإعلانات
            </h1>
            <p class="text-gray-600 mt-1 text-sm">الحسابات تُجلب من نفس ربط Meta في السوشيال — اضغط على حساب للاختيار.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.meta-ads.settings') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-sync"></i> تحديث الحسابات
            </a>
            <a href="{{ $oauthLoginUrl ?? route('admin.meta-social.oauth.redirect') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                <i class="fab fa-meta"></i> إعادة ربط Meta (صلاحيات Ads)
            </a>
            @if(($s['ad_account_id'] ?? '') !== '')
                <a href="{{ route('admin.meta-ads.campaigns.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    <i class="fas fa-rectangle-ad"></i> الحملات
                </a>
            @endif
        </div>
    </div>

    <div class="rounded-xl border px-4 py-4 text-sm {{ $socialConnected ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-amber-200 bg-amber-50 text-amber-900' }}">
        <p class="font-bold">{{ $social['label'] ?? '—' }}</p>
        <p class="text-xs mt-1 opacity-90">
            مصدر التوكن:
            @if(($s['token_source'] ?? '') === 'meta_social') ربط Meta Social @else {{ $s['token_source'] ?? '—' }} @endif
            · Graph: <code class="font-mono" dir="ltr">{{ $s['api_url'] ?? '' }}</code>
        </p>
        @if(!empty($permissions))
            <p class="text-xs mt-2 font-mono opacity-80" dir="ltr">
                permissions: {{ implode(', ', array_slice($permissions, 0, 12)) }}{{ count($permissions) > 12 ? '…' : '' }}
            </p>
        @endif
        @if($needsAdsReauth)
            <div class="mt-3 rounded-lg bg-white/70 border border-amber-200 px-3 py-2 text-amber-950 text-xs leading-relaxed">
                <strong>مطلوب مرة واحدة:</strong> التوكن الحالي من السوشيال بدون صلاحيات الإعلانات.
                اضغط <strong>«إعادة ربط Meta»</strong> ووافق على <code class="font-mono">ads_management</code> و <code class="font-mono">ads_read</code>
                ثم ارجع لهذه الصفحة لاختيار الحساب.
            </div>
        @endif
    </div>

    {{-- Account picker cards --}}
    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-black text-slate-900">الحسابات المتاحة ({{ count($adAccounts) }})</h2>
                <p class="text-xs text-slate-600 mt-1">من حسابك المربوط + حسابات الـ Business</p>
            </div>
        </div>
        <div class="p-6">
            @if(count($adAccounts) > 0)
                <div class="grid sm:grid-cols-2 gap-3 mb-6">
                    @foreach($adAccounts as $acc)
                        @php
                            $accId = \App\Support\MetaAdsSettings::normalizeAdAccountId((string) ($acc['id'] ?? $acc['account_id'] ?? ''));
                            $isSelected = $selectedId === $accId;
                            $status = (int) ($acc['account_status'] ?? 0);
                            $statusText = $statusLabels[$status] ?? ('حالة '.$status);
                        @endphp
                        <form method="post" action="{{ route('admin.meta-ads.settings.select-account') }}" class="h-full">
                            @csrf
                            <input type="hidden" name="ad_account_id" value="{{ $accId }}">
                            <button type="submit"
                                    class="w-full h-full text-right rounded-2xl border-2 p-4 transition-all {{ $isSelected ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-black text-slate-900">{{ $acc['name'] ?? 'حساب بدون اسم' }}</p>
                                        <p class="text-xs font-mono text-slate-500 mt-1" dir="ltr">{{ $accId }}</p>
                                        <p class="text-xs text-slate-600 mt-2">
                                            {{ $acc['currency'] ?? '—' }}
                                            · {{ $statusText }}
                                            @if(!empty($acc['business_name']))
                                                · {{ $acc['business_name'] }}
                                            @endif
                                            · {{ $acc['source'] ?? '' }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                        <i class="fas {{ $isSelected ? 'fa-check' : 'fa-hand-pointer' }} text-sm"></i>
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-blue-700 mt-3">
                                    {{ $isSelected ? 'الحساب المختار — اضغط للتأكيد مجددًا' : 'اضغط للاختيار والانتقال للحملات' }}
                                </p>
                            </button>
                        </form>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-950 mb-4">
                    <p class="font-bold mb-1">لا تظهر حسابات بعد</p>
                    <p class="text-xs leading-relaxed">
                        {{ $adAccountsError ?? 'تعذر جلب الحسابات من Meta.' }}
                    </p>
                    <a href="{{ $oauthLoginUrl ?? route('admin.meta-social.oauth.redirect') }}"
                       class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold">
                        <i class="fab fa-meta"></i> إعادة ربط Meta بصلاحيات الإعلانات
                    </a>
                </div>
            @endif

            <form method="post" action="{{ route('admin.meta-ads.settings.update') }}" class="space-y-6 max-w-3xl border-t border-slate-100 pt-6">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $s['enabled'] ?? true)) class="mt-1 rounded border-slate-300 text-blue-600">
                    <span>
                        <span class="block text-sm font-bold text-slate-900">تفعيل Meta Ads داخل النظام</span>
                    </span>
                </label>

                @if(count($adAccounts) === 0)
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">أو الصق Ad Account ID يدويًا</label>
                        <input type="text" name="ad_account_id" value="{{ $selectedId }}"
                               placeholder="act_1234567890" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                @else
                    <input type="hidden" name="ad_account_id" value="{{ $selectedId }}">
                @endif

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
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instagram Actor ID</label>
                        <input type="text" name="instagram_actor_id" value="{{ old('instagram_actor_id', $s['instagram_actor_id'] ?? '') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold shadow-lg">
                    <i class="fas fa-save"></i> حفظ باقي الإعدادات
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
