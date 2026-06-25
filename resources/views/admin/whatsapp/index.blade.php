@extends('layouts.admin')

@section('title', 'لوحة الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $connectionStatus = $connectionMeta['status'] ?? 'unknown';
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
    $statusMeta = [
        'ready' => ['label' => 'متصل وجاهز', 'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fas fa-check-circle', 'iconColor' => 'text-emerald-500', 'ring' => 'ring-emerald-500/20'],
        'degraded' => ['label' => 'جلسة غير جاهزة', 'badge' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fas fa-exclamation-triangle', 'iconColor' => 'text-amber-500', 'ring' => 'ring-amber-500/20'],
        'qr' => ['label' => 'بانتظار مسح QR', 'badge' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fas fa-qrcode', 'iconColor' => 'text-amber-500', 'ring' => 'ring-amber-500/20'],
        'pairing' => ['label' => 'بانتظار رمز الربط', 'badge' => 'bg-violet-100 text-violet-800 border-violet-200', 'icon' => 'fas fa-key', 'iconColor' => 'text-violet-500', 'ring' => 'ring-violet-500/20'],
        'authenticated' => ['label' => 'تمت المصادقة', 'badge' => 'bg-sky-100 text-sky-800 border-sky-200', 'icon' => 'fas fa-shield-alt', 'iconColor' => 'text-sky-500', 'ring' => 'ring-sky-500/20'],
        'disconnected' => ['label' => 'غير متصل', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fas fa-unlink', 'iconColor' => 'text-slate-400', 'ring' => 'ring-slate-300/30'],
        'error' => ['label' => 'خطأ', 'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fas fa-times-circle', 'iconColor' => 'text-rose-500', 'ring' => 'ring-rose-500/20'],
        'auth_failure' => ['label' => 'فشل المصادقة', 'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fas fa-ban', 'iconColor' => 'text-rose-500', 'ring' => 'ring-rose-500/20'],
    ];
    $meta = $statusMeta[$connectionStatus] ?? ['label' => $connectionStatus, 'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fas fa-question-circle', 'iconColor' => 'text-slate-400', 'ring' => 'ring-slate-300/30'];
    $bridgeConfigured = !empty($settings['bridge_url']) && !empty($settings['bridge_token']);
    $tokenPreview = !empty($settings['bridge_token']) ? substr($settings['bridge_token'], 0, 8) . '••••' : '—';
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'dashboard'])

    @include('admin.whatsapp._page-header', [
        'title' => 'لوحة الواتساب',
        'subtitle' => 'إدارة الاتصال عبر QR أو رمز الربط، مع إرسال آمن يقلّل خطر الحظر.',
        'icon' => 'fab fa-whatsapp',
        'actions' => '
            <a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-paper-plane"></i> إرسال رسالة</a>
            <a href="' . route('admin.whatsapp.docs') . '" class="' . $waBtnSecondary . '"><i class="fas fa-book"></i> التوثيق</a>
            <a href="' . route('admin.whatsapp.settings') . '" class="' . $waBtnSecondary . '"><i class="fas fa-cog"></i> الإعدادات</a>
        ',
        'statCards' => [
            ['label' => 'إجمالي الرسائل', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-comments', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'كل الرسائل المسجلة'],
            ['label' => 'مرسلة اليوم', 'value' => number_format($stats['sent_today'] ?? 0), 'icon' => 'fas fa-paper-plane', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم الإرسال بنجاح اليوم'],
            ['label' => 'رسائل فاشلة', 'value' => number_format($stats['failed'] ?? 0), 'icon' => 'fas fa-exclamation-triangle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تحتاج مراجعة'],
            ['label' => 'حالة الجسر', 'value' => $meta['label'], 'icon' => $meta['icon'], 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => $bridgeConfigured ? 'الإعدادات محفوظة' : 'أكمل إعدادات الربط'],
        ],
    ])

    {{-- Bridge summary --}}
    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <i class="fas fa-server text-emerald-600"></i>
            <h3 class="text-lg font-bold text-slate-900">معلومات الربط</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <p class="text-xs font-semibold text-slate-500 mb-1">نوع الخدمة</p>
                <p class="text-sm font-bold text-slate-900">{{ $settings['service_type'] ?? 'disabled' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 md:col-span-1">
                <p class="text-xs font-semibold text-slate-500 mb-1">رابط Bridge</p>
                <p class="text-sm font-mono text-slate-800 truncate" title="{{ $settings['bridge_url'] ?? '' }}">
                    {{ $settings['bridge_url'] ?: 'غير مضبوط' }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <p class="text-xs font-semibold text-slate-500 mb-1">توكن API</p>
                <p class="text-sm font-mono text-slate-800">{{ $tokenPreview }}</p>
            </div>
        </div>
        @unless($bridgeConfigured)
            <div class="mx-5 mb-5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900 flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                <p>أكمل <a href="{{ route('admin.whatsapp.settings') }}" class="font-semibold underline">إعدادات الربط</a> برابط VPS والتوken قبل مسح QR.</p>
            </div>
        @endunless
        @if(!empty($pacingStats))
            <div class="mx-5 mb-5 rounded-xl bg-sky-50 border border-sky-200 px-4 py-3 text-sm text-sky-900">
                <p class="font-semibold flex items-center gap-2"><i class="fas fa-shield-alt text-sky-600"></i> وضع الإرسال الآمن (Human-like)</p>
                <p class="mt-1 text-sky-800/90">
                    اليوم: <strong>{{ $pacingStats['day'] }}/{{ $pacingStats['max_day'] }}</strong> رسالة ·
                    هذه الساعة: <strong>{{ $pacingStats['hour'] }}/{{ $pacingStats['max_hour'] }}</strong> ·
                    تأخير {{ config('whatsapp.pacing.min_delay_seconds', 5) }}–{{ config('whatsapp.pacing.max_delay_seconds', 14) }} ث ·
                    استراحة كل {{ config('whatsapp.pacing.pause_every', 20) }} رسالة
                </p>
                <p class="mt-2 text-xs text-sky-700">
                    <a href="{{ route('admin.whatsapp.docs') }}#anti-ban" class="font-semibold underline">اقرأ دليل تجنب الحظر</a>
                    — لا يضمن عدم الحظر 100%، لكنه يحاكي سلوكاً بشرياً.
                </p>
            </div>
        @endif
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
        {{-- Connection status --}}
        <section class="{{ $waSectionClass }}">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-signal text-emerald-600"></i>
                    حالة الاتصال
                </h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $meta['badge'] }}">
                    <i class="{{ $meta['icon'] }}"></i>
                    {{ $meta['label'] }}
                </span>
            </div>
            <div class="p-5 space-y-5">
                @if($bridgeError)
                    <div class="rounded-xl bg-rose-50 border-2 border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-start gap-3">
                        <i class="fas fa-plug text-rose-500 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">تعذّر الاتصال بالجسر</p>
                            <p class="mt-1 text-rose-700/90">{{ $bridgeError }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-white border border-slate-200 ring-4 {{ $meta['ring'] }}">
                    <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="{{ $meta['icon'] }} text-3xl {{ $meta['iconColor'] }}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        @if(!empty($status['phone']))
                            <p class="text-lg font-black text-slate-900 dir-ltr text-right">+{{ $status['phone'] }}</p>
                            @if(!empty($status['pushname']))
                                <p class="text-sm text-slate-600">{{ $status['pushname'] }}</p>
                            @endif
                            @if(!empty($status['platform']))
                                <p class="text-xs text-slate-500 mt-1"><i class="fas fa-mobile-alt ml-1"></i>{{ $status['platform'] }}</p>
                            @endif
                        @elseif($isConnected)
                            <p class="text-lg font-black text-emerald-800">الواتساب متصل ويعمل</p>
                            <p class="text-xs text-slate-500 mt-1">الجلسة نشطة — يمكنك الإرسال (رقم الحساب يظهر لاحقاً أحياناً)</p>
                        @else
                            <p class="text-sm font-semibold text-slate-700">لم يتم ربط رقم واتساب بعد</p>
                            <p class="text-xs text-slate-500 mt-1">امسح QR أو أدخل رمز الربط من البطاقة المجاورة</p>
                        @endif
                    </div>
                </div>

                @if(!empty($status['last_error']) && !$isConnected)
                    <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-800">
                        <span class="font-semibold">خطأ الاتصال:</span> {{ $status['last_error'] }}
                        <p class="text-xs mt-2 text-rose-700/90">
                            Chrome عالق على VPS — اضغط <strong>«إصلاح الاتصال»</strong> (يعيد تشغيل Bridge تلقائياً)، انتظر 15 ثانية، ثم جرّب QR أو رمز الربط.
                        </p>
                        <p class="text-xs mt-2 text-rose-700/90">أو على السيرفر:</p>
                        <code class="block bg-white px-2 py-1 rounded text-[10px] mt-1 dir-ltr text-left">cd /opt/mindlytics-whatsapp-bridge && bash repair-vps.sh</code>
                    </div>
                @endif

                <div class="flex flex-wrap gap-2 pt-1">
                    <form method="POST" action="{{ route('admin.whatsapp.start') }}">
                        @csrf
                        <button type="submit" class="{{ $waBtnPrimary }}">
                            <i class="fas fa-wrench"></i>
                            إصلاح الاتصال (بدون قطع)
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.whatsapp.logout') }}" onsubmit="return confirm('قطع اتصال الواتساب؟ ستحتاج QR أو رمز ربط جديد.');">
                        @csrf
                        <button type="submit" class="{{ $waBtnSecondary }}">
                            <i class="fas fa-sign-out-alt"></i>
                            قطع الاتصال
                        </button>
                    </form>
                </div>

                <ol class="text-xs text-slate-500 space-y-1.5 border-t border-slate-100 pt-4">
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">1.</span> تأكد أن Bridge يعمل على VPS (PM2 online)</li>
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">2.</span> اختر QR أو رمز الربط من البطاقة المجاورة</li>
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">3.</span> عند ظهور «متصل وجاهز» — جرّب إرسال رسالة</li>
                </ol>
            </div>
        </section>

        {{-- QR أو رمز الربط --}}
        <section class="{{ $waSectionClass }}" x-data="whatsappConnect()" x-init="init()">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-link text-emerald-600"></i>
                    ربط الحساب
                </h3>
                <p class="text-sm text-slate-500 mt-1">اختر طريقة الربط المناسبة لك</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" @click="setMode('qr')"
                            :class="mode === 'qr' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                        <i class="fas fa-qrcode"></i>
                        مسح QR
                    </button>
                    <button type="button" @click="setMode('pairing')"
                            :class="mode === 'pairing' ? 'bg-violet-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                        <i class="fas fa-key"></i>
                        رمز الربط
                    </button>
                </div>
            </div>
            <div class="p-5">
                <template x-if="connected">
                    <div class="text-center py-8">
                        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-4 ring-4 ring-emerald-500/20">
                            <i class="fab fa-whatsapp text-4xl text-emerald-600"></i>
                        </div>
                        <p class="text-lg font-black text-emerald-800">الواتساب متصل</p>
                        <p class="text-sm text-emerald-600/80 mt-1">يمكنك إرسال الرسائل الآن</p>
                    </div>
                </template>

                <template x-if="!connected && mode === 'qr'">
                    <div>
                        <p class="text-xs text-slate-500 mb-3">واتساب → الأجهزة المرتبطة → ربط جهاز → مسح QR</p>
                        <div class="relative flex flex-col items-center justify-center min-h-[240px] rounded-2xl border-2 border-dashed border-emerald-200/80 bg-gradient-to-br from-emerald-50/50 via-white to-sky-50/30 p-6">
                            <template x-if="qrImage">
                                <div class="text-center">
                                    <img :src="qrImage" alt="WhatsApp QR" class="max-w-[240px] rounded-xl shadow-lg border-4 border-white mx-auto">
                                    <p class="text-xs text-slate-500 mt-4">ينتهي QR خلال دقائق — حدّث إن لم ينجح المسح</p>
                                </div>
                            </template>
                            <template x-if="!qrImage && !loading">
                                <div class="text-center max-w-xs">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                                        <i class="fas fa-qrcode text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-sm text-slate-600 font-medium" x-text="message || 'جاري تحميل QR...'"></p>
                                </div>
                            </template>
                            <template x-if="loading">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin text-3xl text-emerald-500 mb-3"></i>
                                    <p class="text-sm text-slate-500">جاري تحميل QR...</p>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="refreshQr()" class="mt-4 w-full {{ $waBtnDark }}">
                            <i class="fas fa-redo"></i>
                            تحديث QR / الحالة
                        </button>
                    </div>
                </template>

                <template x-if="!connected && mode === 'pairing'">
                    <div>
                        <p class="text-xs text-slate-500 mb-3">واتساب → الأجهزة المرتبطة → ربط جهاز → <strong>ربط برقم الهاتف</strong></p>
                        <form @submit.prevent="requestPairing()" class="space-y-3 mb-4">
                            <div>
                                <label class="{{ $waLabelClass }}">رقم الواتساب (مع كود الدولة)</label>
                                <input type="text" x-model="pairingPhone" placeholder="مثال: 201012345678 أو 01012345678"
                                       class="{{ $waInputClass }} dir-ltr text-right font-mono text-sm"
                                       :disabled="loading">
                                <p class="text-[11px] text-slate-500 mt-1">يُستخدم نفس الرقم المسجّل على واتساب — بدون مسح QR</p>
                            </div>
                            <button type="submit" class="w-full {{ $waBtnPrimary }}" :disabled="loading || !pairingPhone.trim()">
                                <i class="fas fa-key"></i>
                                <span x-text="loading ? 'جاري الطلب...' : 'طلب رمز الربط'"></span>
                            </button>
                        </form>
                        <div class="relative flex flex-col items-center justify-center min-h-[160px] rounded-2xl border-2 border-dashed border-violet-200/80 bg-gradient-to-br from-violet-50/50 via-white to-fuchsia-50/30 p-6">
                            <template x-if="pairingCode">
                                <div class="text-center">
                                    <p class="text-xs text-slate-500 mb-2">أدخل هذا الرمز في تطبيق واتساب</p>
                                    <p class="text-4xl font-black tracking-[0.35em] text-violet-800 dir-ltr" x-text="pairingCode"></p>
                                    <p class="text-xs text-slate-500 mt-4">ينتهي الرمز خلال ~3 دقائق — اضغط «تحديث» إن انتهى</p>
                                </div>
                            </template>
                            <template x-if="!pairingCode && !loading">
                                <div class="text-center max-w-xs">
                                    <i class="fas fa-mobile-alt text-3xl text-violet-400 mb-3"></i>
                                    <p class="text-sm text-slate-600 font-medium" x-text="message || 'أدخل رقمك واضغط «طلب رمز الربط»'"></p>
                                </div>
                            </template>
                            <template x-if="loading && !pairingCode">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin text-3xl text-violet-500 mb-3"></i>
                                    <p class="text-sm text-slate-500">جاري إنشاء رمز الربط...</p>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="refreshPairing()" class="mt-4 w-full {{ $waBtnDark }}">
                            <i class="fas fa-redo"></i>
                            تحديث رمز الربط / الحالة
                        </button>
                    </div>
                </template>
            </div>
        </section>
    </div>

    {{-- Quick links --}}
    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                إجراءات سريعة
            </h3>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <a href="{{ route('admin.whatsapp.send') }}" class="group rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-paper-plane text-emerald-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-emerald-700">إرسال رسالة</p>
                <p class="text-xs text-slate-500 mt-1">رسالة فردية فورية</p>
            </a>
            <a href="{{ route('admin.whatsapp.messages') }}" class="group rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-history text-sky-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-sky-700">سجل الرسائل</p>
                <p class="text-xs text-slate-500 mt-1">مراجعة المرسل والفاشل</p>
            </a>
            <a href="{{ route('admin.whatsapp.settings') }}" class="group rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-plug text-violet-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-violet-700">إعدادات الربط</p>
                <p class="text-xs text-slate-500 mt-1">رابط VPS والتوken</p>
            </a>
            <a href="{{ route('admin.whatsapp.docs') }}" class="group rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-book text-violet-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-violet-700">التوثيق</p>
                <p class="text-xs text-slate-500 mt-1">الربط، الإرسال الآمن، والحدود</p>
            </a>
            <a href="{{ route('admin.messages.index') }}" class="group rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-envelope text-amber-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-amber-700">الرسائل العامة</p>
                <p class="text-xs text-slate-500 mt-1">تقارير وقوالب النظام</p>
            </a>
        </div>
    </section>
</div>

@push('scripts')
<script>
function whatsappConnect() {
    const csrf = @json(csrf_token());
    const statusUrl = @json(route('admin.whatsapp.status'));
    const qrUrl = @json(route('admin.whatsapp.qr'));
    const pairingGetUrl = @json(route('admin.whatsapp.pairing'));
    const pairingPostUrl = @json(route('admin.whatsapp.pairing.request'));
    const repairUrl = @json(route('admin.whatsapp.start'));

    return {
        mode: 'qr',
        qrImage: null,
        pairingCode: null,
        pairingPhone: '',
        connected: false,
        loading: false,
        message: '',
        pollTimer: null,
        init() {
            this.refreshStatus();
            this.refreshQr(true);
            this.pollTimer = setInterval(() => {
                this.refreshStatus(true);
                if (!this.connected && this.mode === 'qr') {
                    this.refreshQr(true);
                } else if (!this.connected && this.mode === 'pairing' && this.pairingPhone) {
                    this.refreshPairing(true);
                }
            }, 8000);
        },
        setMode(next) {
            this.mode = next;
            this.message = '';
            if (next === 'qr') {
                this.pairingCode = null;
                this.refreshQr();
            } else {
                this.qrImage = null;
            }
        },
        async refreshStatus(silent = false) {
            try {
                const statusRes = await fetch(statusUrl);
                const statusJson = await statusRes.json();
                const data = statusJson.data || statusJson;
                this.connected = !!(
                    data.send_ready
                    || (data.connected && (data.status === 'ready' || data.status === 'degraded'))
                );
                if (this.connected) {
                    this.qrImage = null;
                    this.pairingCode = null;
                    this.message = '';
                    return;
                }
                if (data.pairing_mode || data.status === 'pairing') {
                    this.mode = 'pairing';
                    if (data.pairing_phone) {
                        this.pairingPhone = data.pairing_phone;
                    }
                    if (data.pairing_code) {
                        this.pairingCode = data.pairing_code;
                    }
                }
            } catch (e) {
                if (!silent) {
                    this.message = 'تعذّر الاتصال بالجسر — تحقق من الإعدادات';
                }
            }
        },
        async refreshQr(silent = false) {
            if (!silent) this.loading = true;
            try {
                await this.refreshStatus(true);
                if (this.connected) return;

                const qrRes = await fetch(qrUrl);
                const qrJson = await qrRes.json();
                if (qrJson.success && qrJson.data?.qr_image) {
                    this.qrImage = qrJson.data.qr_image;
                    this.message = '';
                } else {
                    this.message = qrJson.error || qrJson.data?.error || 'QR غير جاهز بعد — انتظر ثوانٍ';
                }
            } catch (e) {
                this.message = 'تعذّر الاتصال بالجسر — تحقق من الإعدادات';
            } finally {
                if (!silent) this.loading = false;
            }
        },
        async requestPairing() {
            if (!this.pairingPhone.trim()) return;
            this.loading = true;
            this.message = 'جاري إصلاح الاتصال وطلب رمز الربط...';
            try {
                const res = await fetch(pairingPostUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ phone: this.pairingPhone.trim() }),
                });
                const json = await res.json();
                if (!json.success) {
                    if (json.restarting) {
                        this.message = json.error || 'Bridge يُعاد تشغيله — انتظر 15 ثانية ثم أعد المحاولة.';
                        setTimeout(() => this.refreshPairing(true), 16000);
                        return;
                    }
                    const err = json.error || '';
                    if (err.includes('browser is already running') || err.includes('userDataDir')) {
                        this.message = 'Chrome عالق على VPS — اضغط «إصلاح الاتصال» ثم أعد المحاولة، أو نفّذ repair-vps.sh على السيرفر.';
                    } else {
                        this.message = err || 'فشل طلب رمز الربط — تأكد من تحديث server.js على VPS';
                    }
                    return;
                }
                this.message = '';
                await this.refreshPairing(true);
            } catch (e) {
                this.message = 'تعذّر الاتصال بالجسر';
            } finally {
                this.loading = false;
            }
        },
        async refreshPairing(silent = false) {
            if (!silent) this.loading = true;
            try {
                await this.refreshStatus(true);
                if (this.connected) return;

                const res = await fetch(pairingGetUrl);
                const json = await res.json();
                const data = json.data || json;
                if (data.pairing_code) {
                    this.pairingCode = data.pairing_code;
                    this.message = '';
                } else if (data.success === false || !json.success) {
                    this.message = json.error || data.error || 'رمز الربط غير جاهز — اطلب رمزاً جديداً';
                }
            } catch (e) {
                this.message = 'تعذّر الاتصال بالجسر';
            } finally {
                if (!silent) this.loading = false;
            }
        },
    };
}
</script>
@endpush
@endsection
