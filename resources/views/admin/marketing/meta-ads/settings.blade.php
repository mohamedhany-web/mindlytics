@extends('layouts.admin')

@section('title', 'إعدادات Meta Ads')
@section('header', 'Meta Ads — الإعدادات')

@section('content')
@php $s = $settings ?? []; @endphp
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
            <p class="text-gray-600 mt-1 text-sm">اربط Ad Account + Marketing API Token لإدارة الحملات والميزانية والجمهور من النظام.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.meta-ads.campaigns.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-rectangle-ad"></i> الحملات
            </a>
            @if($s['is_ready'] ?? false)
            <form method="post" action="{{ route('admin.meta-ads.settings.test') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-plug"></i> اختبار الاتصال
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(is_array($connection ?? null))
        <div class="rounded-xl border px-4 py-3 text-sm font-semibold {{ ($connection['success'] ?? false) ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-900' }}">
            {{ $connection['label'] ?? '' }}
            @if(!empty($connection['error']))
                <span class="block font-normal mt-1 text-xs opacity-90">{{ $connection['error'] }}</span>
            @endif
        </div>
    @endif

    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-black text-slate-900">بيانات Marketing API</h2>
            <p class="text-xs text-slate-600 mt-1">الصلاحيات المطلوبة على التوكن: <code class="font-mono">ads_management</code> و <code class="font-mono">ads_read</code></p>
        </div>
        <div class="p-6">
            <form method="post" action="{{ route('admin.meta-ads.settings.update') }}" class="space-y-6 max-w-3xl">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $s['enabled'] ?? false)) class="mt-1 rounded border-slate-300 text-blue-600">
                    <span>
                        <span class="block text-sm font-bold text-slate-900">تفعيل Meta Ads داخل النظام</span>
                        <span class="block text-xs text-slate-500 mt-0.5">بدون التفعيل لن تُعرض أدوات إدارة الحملات.</span>
                    </span>
                </label>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Ad Account ID</label>
                        <input type="text" name="ad_account_id" value="{{ old('ad_account_id', $s['ad_account_id'] ?? '') }}"
                               placeholder="act_1234567890" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-slate-500 mt-1">من Meta Business Suite → Ad accounts. يقبل الرقم فقط أو بصيغة act_…</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Access Token (Marketing API)</label>
                        <input type="password" name="access_token" value=""
                               placeholder="{{ ($s['has_access_token'] ?? false) ? '•••• محفوظ — اتركه فارغًا للإبقاء' : 'الصق System User Token' }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500"
                               autocomplete="new-password">
                        <p class="text-xs text-slate-500 mt-1">يُفضَّل System User token طويل الأمد من Business Settings. يُشفَّر عند الحفظ.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Graph API URL</label>
                        <input type="text" name="api_url" value="{{ old('api_url', $s['api_url'] ?? 'https://graph.facebook.com/v21.0') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Facebook Page ID (اختياري)</label>
                        <input type="text" name="page_id" value="{{ old('page_id', $s['page_id'] ?? '') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Instagram Actor ID (اختياري)</label>
                        <input type="text" name="instagram_actor_id" value="{{ old('instagram_actor_id', $s['instagram_actor_id'] ?? '') }}" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                    </div>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-xs text-blue-900 leading-relaxed">
                    <strong>ملاحظة:</strong> قسم «الحملات الإعلانية» الحالي يبقى لتتبع تكلفة الكامبين وتقارير السيلز داخليًا.
                    قسم <strong>Meta Ads</strong> يدير الحملات الحقيقية على فيسبوك/إنستجرام عبر الـ API.
                    اربط أيضًا Meta Pixel من <a href="{{ route('admin.marketing-web-analytics.settings') }}" class="underline font-bold">تتبع التسويق</a> لتحسين تحويلات الشراء.
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold shadow-lg">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
