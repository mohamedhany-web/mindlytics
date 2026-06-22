@extends('layouts.admin')

@section('title', 'إعدادات ربط الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="space-y-6">
    @include('admin.whatsapp._nav', ['active' => 'settings'])

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="rounded-3xl bg-sky-50 border border-sky-200 p-5 text-sm text-sky-900">
        <p class="font-semibold">خطوات التشغيل على Shared Hosting</p>
        <ol class="list-decimal list-inside mt-2 space-y-1 leading-relaxed">
            <li>على VPS أو جهازك: <code class="bg-sky-100 px-1 rounded">cd whatsapp-bridge && npm install && cp .env.example .env</code></li>
            <li>عدّل <code class="bg-sky-100 px-1 rounded">API_TOKEN</code> في <code class="bg-sky-100 px-1 rounded">.env</code> ثم <code class="bg-sky-100 px-1 rounded">npm start</code></li>
            <li>افتح المنفذ 3001 (أو استخدم ngrok / Railway URL)</li>
            <li>أدخل نفس الرابط والتوكن هنا واختر «whatsapp-web.js Bridge»</li>
        </ol>
    </div>

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 max-w-3xl">
        <h3 class="text-lg font-bold text-slate-900 mb-4">إعدادات الربط</h3>
        <form method="POST" action="{{ route('admin.whatsapp.settings.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">نوع الخدمة</label>
                <select name="service_type" class="w-full rounded-xl border-slate-300">
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
                <label class="block text-sm font-semibold text-slate-700 mb-1">رابط Bridge (Node.js)</label>
                <input type="url" name="bridge_url" value="{{ old('bridge_url', $settings['bridge_url'] ?? '') }}"
                       placeholder="https://your-vps.example.com:3001"
                       class="w-full rounded-xl border-slate-300">
                @error('bridge_url')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">توكن الأمان (API_TOKEN)</label>
                <input type="password" name="bridge_token" value="{{ old('bridge_token', $settings['bridge_token'] ?? '') }}"
                       autocomplete="new-password"
                       class="w-full rounded-xl border-slate-300">
                @error('bridge_token')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                حفظ الإعدادات
            </button>
        </form>
    </section>
</div>
@endsection
