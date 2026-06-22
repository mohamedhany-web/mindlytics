@extends('layouts.admin')

@section('title', 'إعدادات ربط الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'settings'])

    @include('admin.whatsapp._page-header', [
        'title' => 'إعدادات الربط',
        'subtitle' => 'اربط Laravel على Hostinger بـ whatsapp-web.js Bridge على VPS.',
        'icon' => 'fas fa-plug',
    ])

    <div class="rounded-2xl bg-gradient-to-r from-sky-50 to-emerald-50 border-2 border-sky-200/60 p-5 text-sm text-slate-800 shadow-sm">
        <p class="font-bold flex items-center gap-2 text-sky-900">
            <i class="fas fa-lightbulb text-amber-500"></i>
            كيف يعمل الربط؟
        </p>
        <ol class="list-decimal list-inside mt-3 space-y-2 leading-relaxed text-slate-700 mr-1">
            <li>Bridge يعمل على VPS عبر PM2 (<code class="bg-white/80 px-1.5 py-0.5 rounded text-xs font-mono">wa-api.yourdomain.com</code>)</li>
            <li>Laravel يتصل بالجسر عبر HTTPS + Bearer Token</li>
            <li>امسح QR من لوحة الواتساب لربط حسابك</li>
        </ol>
    </div>

    <section class="{{ $waSectionClass }} max-w-3xl">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-cog text-emerald-600"></i>
                إعدادات Bridge
            </h3>
        </div>
        <div class="p-5 sm:p-6">
            <form method="POST" action="{{ route('admin.whatsapp.settings.update') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="{{ $waLabelClass }}">نوع الخدمة</label>
                    <select name="service_type" class="{{ $waSelectClass }}">
                        @foreach([
                            'disabled' => 'معطّل (حفظ فقط بدون إرسال حقيقي)',
                            'wwebjs' => 'whatsapp-web.js Bridge (موصى به)',
                            'local' => 'محلي / Bridge (نفس wwebjs)',
                            'official' => 'WhatsApp Business API (Meta)',
                            'custom' => 'API مخصص',
                        ] as $val => $label)
                            <option value="{{ $val }}" @selected(old('service_type', $settings['service_type'] ?? 'disabled') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">رابط Bridge (Node.js على VPS)</label>
                    <input type="url" name="bridge_url" value="{{ old('bridge_url', $settings['bridge_url'] ?? '') }}"
                           placeholder="https://wa-api.mindlytics-academy.com"
                           class="{{ $waInputClass }} dir-ltr text-right font-mono text-sm">
                    @error('bridge_url')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">توكن الأمان (API_TOKEN)</label>
                    <input type="password" name="bridge_token" value="{{ old('bridge_token', $settings['bridge_token'] ?? '') }}"
                           autocomplete="new-password" placeholder="نفس API_TOKEN في .env على VPS"
                           class="{{ $waInputClass }} dir-ltr text-right font-mono text-sm">
                    @error('bridge_token')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <button type="submit" class="{{ $waBtnPrimary }}">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                    <a href="{{ route('admin.whatsapp.index') }}" class="{{ $waBtnSecondary }}">العودة للوحة</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
