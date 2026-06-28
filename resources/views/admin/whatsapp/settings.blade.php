@extends('layouts.admin')

@section('title', 'ربط WhatsApp Business — Meta Cloud API')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'settings'])

    @include('admin.whatsapp._page-header', [
        'title' => 'ربط WhatsApp Business (Meta)',
        'subtitle' => 'التكامل الرسمي عبر Meta Cloud API و Embedded Signup — بدون Bridge أو QR.',
        'icon' => 'fab fa-whatsapp',
    ])

    @php
        $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
        $canEmbed = !empty($config['app_id']) && !empty($config['embedded_signup_config_id']);
    @endphp

    {{-- حالة الربط --}}
    <section class="{{ $waSectionClass }}">
        <div class="p-5 sm:p-6 space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">حالة الاتصال</h3>
                    <p class="text-sm text-slate-600 mt-1">{{ $connectionMeta['label'] ?? '—' }}</p>
                    @if(!empty($connectionMeta['last_error']) && !$isConnected)
                        <p class="text-xs text-rose-700 mt-2">{{ $connectionMeta['last_error'] }}</p>
                    @endif
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold border {{ $isConnected ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200' }}">
                    <i class="fas {{ $isConnected ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                    {{ $isConnected ? 'متصل وجاهز' : 'غير مربوط' }}
                </span>
            </div>

            @if($connection)
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">الرقم</p>
                        <p class="font-bold text-slate-900">{{ $connection->display_phone_number ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">الاسم المعتمد</p>
                        <p class="font-bold text-slate-900">{{ $connection->verified_display_name ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Phone Number ID</p>
                        <p class="font-mono text-xs text-slate-800 break-all">{{ $connection->phone_number_id }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">WABA ID</p>
                        <p class="font-mono text-xs text-slate-800 break-all">{{ $connection->waba_id ?? '—' }}</p>
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap gap-2 pt-2">
                @if($canEmbed)
                    <button type="button" id="btn-embedded-signup"
                            class="{{ $waBtnPrimary }}">
                        <i class="fab fa-facebook"></i>
                        ربط WhatsApp Business
                    </button>
                @else
                    <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        أدخل <strong>App ID</strong> و <strong>Embedded Signup Config ID</strong> بالأسفل ثم احفظ لتفعيل زر الربط.
                    </p>
                @endif

                <button type="button" id="btn-test-connection" class="{{ $waBtnSecondary }}">
                    <i class="fas fa-vial"></i>
                    اختبار الربط
                </button>

                @if($connection)
                    <form method="POST" action="{{ route('admin.whatsapp.disconnect') }}" onsubmit="return confirm('فصل حساب WhatsApp Business؟');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                            <i class="fas fa-unlink"></i>
                            فصل الحساب
                        </button>
                    </form>
                @endif
            </div>

            <div id="test-result" class="hidden rounded-xl border px-4 py-3 text-sm"></div>
        </div>
    </section>

    {{-- إعدادات Meta --}}
    <section class="{{ $waSectionClass }} max-w-4xl">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-key text-emerald-600"></i>
                متغيرات الربط (Meta)
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحفظ في إعدادات المنصة (مشفّرة) — لا حاجة لتعديل ملف <code class="bg-slate-100 px-1 rounded">.env</code></p>
        </div>
        <div class="p-5 sm:p-6">
            <form method="POST" action="{{ route('admin.whatsapp.settings.update') }}" class="space-y-4" id="wa-settings-form">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $waLabelClass }}">Meta App ID *</label>
                        <input type="text" name="app_id" value="{{ old('app_id', $config['app_id']) }}" required
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm" placeholder="123456789012345">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Meta App Secret {{ ($config['has_app_secret'] ?? false) ? '' : '*' }}</label>
                        <input type="password" name="app_secret" value=""
                               autocomplete="new-password" class="{{ $waInputClass }} dir-ltr font-mono text-sm"
                               placeholder="{{ ($config['has_app_secret'] ?? false) ? 'محفوظ — اتركه فارغاً للإبقاء' : 'App Secret من Meta Developers' }}"
                               @if(!($config['has_app_secret'] ?? false)) required @endif>
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Embedded Signup Config ID</label>
                        <input type="text" name="embedded_signup_config_id" value="{{ old('embedded_signup_config_id', $config['embedded_signup_config_id']) }}"
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm" placeholder="Config ID من Meta Developers">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Graph API URL *</label>
                        <input type="url" name="api_url" value="{{ old('api_url', $config['api_url']) }}" required
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Webhook Verify Token</label>
                        <input type="text" name="webhook_verify_token" value="{{ old('webhook_verify_token', $config['webhook_verify_token']) }}"
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm" placeholder="سلسلة عشوائية للتحقق">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Webhook URL (للنسخ في Meta)</label>
                        <input type="text" readonly value="{{ $config['webhook_url'] }}"
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm bg-slate-50" onclick="this.select()">
                    </div>
                    <div class="md:col-span-2 border-t border-slate-100 pt-4">
                        <p class="text-xs font-bold text-slate-700 mb-3">ربط يدوي (بدون Embedded Signup)</p>
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Access Token</label>
                        <input type="password" name="access_token" value=""
                               autocomplete="new-password" class="{{ $waInputClass }} dir-ltr font-mono text-sm"
                               placeholder="{{ ($config['has_access_token'] ?? false) ? 'محفوظ — اتركه فارغاً للإبقاء' : 'System User Token أو Token من Meta' }}">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">Phone Number ID</label>
                        <input type="text" name="phone_number_id" value="{{ old('phone_number_id', $config['phone_number_id']) }}"
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm">
                    </div>
                    <div>
                        <label class="{{ $waLabelClass }}">WhatsApp Business Account ID (WABA)</label>
                        <input type="text" name="business_account_id" value="{{ old('business_account_id', $config['business_account_id']) }}"
                               class="{{ $waInputClass }} dir-ltr font-mono text-sm">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="enable_service" value="1" @checked(old('enable_service', $config['enabled'])) class="rounded text-emerald-600">
                            تفعيل إرسال الواتساب
                        </label>
                    </div>
                </div>

                <div class="rounded-xl bg-sky-50 border border-sky-200 p-4 text-xs text-sky-900 leading-relaxed space-y-2">
                    <p class="font-bold">صلاحيات Meta المطلوبة:</p>
                    <p><code>whatsapp_business_management</code> · <code>whatsapp_business_messaging</code> · <code>business_management</code></p>
                    <p>بعد الربط: أضف Webhook URL أعلاه في Meta Developers → WhatsApp → Configuration، واستخدم نفس Verify Token.</p>
                </div>

                <button type="submit" class="{{ $waBtnPrimary }}">
                    <i class="fas fa-save"></i>
                    حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>

    {{-- اختبار إرسال --}}
    <section class="{{ $waSectionClass }} max-w-4xl">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">اختبار إرسال رسالة</h3>
        </div>
        <div class="p-5 sm:p-6 space-y-3">
            <div class="grid sm:grid-cols-2 gap-3">
                <input type="text" id="test-phone" placeholder="2010xxxxxxx" class="{{ $waInputClass }} text-sm">
                <input type="text" id="test-message" placeholder="رسالة الاختبار (اختياري)" class="{{ $waInputClass }} text-sm">
            </div>
            <p class="text-[11px] text-slate-500">اترك الرقم فارغاً للتحقق من Token فقط. أدخل رقماً لإرسال رسالة اختبار حقيقية (يجب أن يكون ضمن نافذة 24 ساعة أو Template معتمد).</p>
        </div>
    </section>
</div>

@if($canEmbed)
@push('scripts')
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ar_AR/sdk.js"></script>
<script>
(function () {
    const appId = @json($config['app_id']);
    const configId = @json($config['embedded_signup_config_id']);
    const csrf = @json(csrf_token());
    const signupUrl = @json(route('admin.whatsapp.embedded-signup'));
    const testUrl = @json(route('admin.whatsapp.test-connection'));

    window.fbAsyncInit = function () {
        FB.init({ appId: appId, cookie: true, xfbml: true, version: 'v21.0' });
    };

    window.addEventListener('message', function (event) {
        if (event.origin !== 'https://www.facebook.com' && event.origin !== 'https://web.facebook.com') return;
        try {
            const data = JSON.parse(event.data);
            if (data.type === 'WA_EMBEDDED_SIGNUP') {
                finishSignup({
                    phone_number_id: data.data?.phone_number_id,
                    waba_id: data.data?.waba_id,
                    business_id: data.data?.business_id,
                });
            }
        } catch (_) {}
    });

    function finishSignup(payload) {
        fetch(signupUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'تم الربط بنجاح');
                    location.reload();
                } else {
                    alert(data.error || data.message || 'فشل الربط');
                }
            })
            .catch(() => alert('خطأ في الاتصال بالسيرفر'));
    }

    document.getElementById('btn-embedded-signup')?.addEventListener('click', function () {
        FB.login(function (response) {
            if (response.authResponse?.code) {
                finishSignup({ code: response.authResponse.code });
            }
        }, {
            config_id: configId,
            response_type: 'code',
            override_default_response_type: true,
            extras: { feature: 'whatsapp_embedded_signup', sessionInfoVersion: 2 },
        });
    });

    async function runTest() {
        const box = document.getElementById('test-result');
        box.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800');
        box.textContent = 'جاري الاختبار…';

        const form = document.getElementById('wa-settings-form');
        const fd = new FormData(form);
        fd.append('test_phone', document.getElementById('test-phone')?.value || '');
        fd.append('test_message', document.getElementById('test-message')?.value || '');

        try {
            const res = await fetch(testUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                box.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
                box.textContent = data.message || 'نجح الاختبار';
            } else {
                box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
                box.textContent = data.error || 'فشل الاختبار';
            }
        } catch (e) {
            box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
            box.textContent = 'خطأ في الاتصال';
        }
    }

    document.getElementById('btn-test-connection')?.addEventListener('click', runTest);
})();
</script>
@endpush
@else
@push('scripts')
<script>
(function () {
    const csrf = @json(csrf_token());
    const testUrl = @json(route('admin.whatsapp.test-connection'));
    document.getElementById('btn-test-connection')?.addEventListener('click', async function () {
        const box = document.getElementById('test-result');
        box.classList.remove('hidden');
        box.textContent = 'جاري الاختبار…';
        const form = document.getElementById('wa-settings-form');
        const fd = new FormData(form);
        fd.append('test_phone', document.getElementById('test-phone')?.value || '');
        const res = await fetch(testUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: fd });
        const data = await res.json();
        box.textContent = data.success ? (data.message || 'نجح') : (data.error || 'فشل');
        box.className = 'rounded-xl border px-4 py-3 text-sm ' + (data.success ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800');
    });
})();
</script>
@endpush
@endif
@endsection
