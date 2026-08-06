@extends('layouts.admin')

@section('title', 'تتبع التسويق')
@section('header', 'تتبع التسويق — GTM / GA4 / Clarity / Meta')

@section('content')
@php
    $s = $settings ?? [];
@endphp
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 font-semibold text-sm">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-sky-50 via-indigo-50 to-violet-50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-violet-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">إعدادات التتبع والحملات</h2>
                    <p class="text-sm text-slate-600 mt-1">Google Tag Manager · GA4 · Microsoft Clarity · Meta Pixel (فيسبوك / إنستجرام Ads)</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="post" action="{{ route('admin.marketing-web-analytics.settings.update') }}" class="space-y-8 max-w-3xl">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $s['enabled'] ?? true)) class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    <span>
                        <span class="block text-sm font-bold text-slate-900">تفعيل التتبع على الصفحات العامة</span>
                        <span class="block text-xs text-slate-500 mt-0.5">لا يُحقَن على لوحات الموظفين أو الأدمن حتى لا تتلوّث تقارير التسويق.</span>
                    </span>
                </label>

                {{-- Google --}}
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fab fa-google text-red-500"></i>
                        <h3 class="text-sm font-black text-slate-800">Google — GTM &amp; GA4</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">GTM Container ID</label>
                            <input type="text" name="gtm_container_id" value="{{ old('gtm_container_id', $s['gtm_container_id'] ?? '') }}"
                                   placeholder="GTM-XXXXXXX" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                            @error('gtm_container_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-1">نقطة الحقن الرئيسية. اربط GA4 داخل حاوية GTM.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">GA4 Measurement ID</label>
                            <input type="text" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $s['ga4_measurement_id'] ?? '') }}"
                                   placeholder="G-XXXXXXXXXX" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                            @error('ga4_measurement_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-1">مرجع للتوثيق؛ التتبع الفعلي يتم عبر GTM + dataLayer (أحداث التجارة الإلكترونية للكورسات).</p>
                        </div>
                    </div>
                </div>

                {{-- Clarity --}}
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fab fa-microsoft text-sky-600"></i>
                        <h3 class="text-sm font-black text-slate-800">Microsoft Clarity</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Clarity Project ID</label>
                            <input type="text" name="clarity_project_id" value="{{ old('clarity_project_id', $s['clarity_project_id'] ?? '') }}"
                                   placeholder="xxxxxxxxxx" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                            @error('clarity_project_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-1">اختياري — Session replay وHeatmaps. اتركه فارغًا للإيقاف.</p>
                        </div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="rounded-2xl border-2 border-blue-100 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b flex items-center gap-2">
                        <i class="fab fa-meta text-blue-600"></i>
                        <h3 class="text-sm font-black text-slate-800">Meta Pixel — حملات فيسبوك / إنستجرام</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <label class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/40 p-4 cursor-pointer">
                            <input type="checkbox" name="meta_pixel_enabled" value="1" @checked(old('meta_pixel_enabled', $s['meta_pixel_enabled'] ?? true)) class="mt-1 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="block text-sm font-bold text-slate-900">تفعيل Meta Pixel</span>
                                <span class="block text-xs text-slate-500 mt-0.5">PageView + ViewContent + InitiateCheckout + Purchase متزامنة مع قمع الشراء.</span>
                            </span>
                        </label>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Pixel ID</label>
                            <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $s['meta_pixel_id'] ?? '') }}"
                                   placeholder="123456789012345" dir="ltr" inputmode="numeric"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                            @error('meta_pixel_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-1">من Events Manager في Meta Business Suite. ضروري لقياس الحملات الإعلانية والتحويلات (Purchase).</p>
                        </div>
                    </div>
                </div>

                {{-- Catalog defaults --}}
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fas fa-store text-emerald-600"></i>
                        <h3 class="text-sm font-black text-slate-800">إعدادات الكتالوج (Ecommerce)</h3>
                    </div>
                    <div class="p-4 grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">العملة</label>
                            <input type="text" name="currency" value="{{ old('currency', $s['currency'] ?? 'EGP') }}"
                                   placeholder="EGP" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">العلامة التجارية (item_brand)</label>
                            <input type="text" name="item_brand" value="{{ old('item_brand', $s['item_brand'] ?? 'Mindlytics') }}"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-xs text-amber-900 leading-relaxed">
                    <strong class="font-bold">بعد الحفظ:</strong>
                    في GTM أضف GA4 Configuration Tag + Event Tags لأحداث
                    <code class="font-mono">view_item</code> /
                    <code class="font-mono">begin_checkout</code> /
                    <code class="font-mono">purchase</code>.
                    في Meta Events Manager فعّل التحقق من الدومين واختبر Purchase عبر Test Events.
                    القيم الافتراضية يمكن أيضًا ضبطها في <code class="font-mono">.env</code> ثم تعديلها من هنا.
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-violet-600 text-white text-sm font-bold shadow-lg hover:opacity-95">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
