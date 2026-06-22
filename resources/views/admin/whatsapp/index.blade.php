@extends('layouts.admin')

@section('title', 'لوحة الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="space-y-6">
    @include('admin.whatsapp._nav', ['active' => 'dashboard'])

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="rounded-3xl bg-amber-50 border border-amber-200 p-5 text-sm text-amber-900">
        <p class="font-semibold flex items-center gap-2"><i class="fas fa-server"></i> Shared Hosting</p>
        <p class="mt-2 leading-relaxed">
            سيرفر Hostinger المشترك لا يشغّل Node.js أو Chrome. شغّل مجلد <code class="bg-amber-100 px-1 rounded">whatsapp-bridge</code>
            على VPS أو Railway أو Render أو جهازك المحلي، ثم اربط الرابط والتوكن من «إعدادات الربط».
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">إجمالي الرسائل</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">مرسلة اليوم</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ number_format($stats['sent_today'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">فاشلة</p>
            <p class="text-3xl font-bold text-rose-600 mt-2">{{ number_format($stats['failed'] ?? 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">حالة الاتصال</h3>

            @if($bridgeError)
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm mb-4">
                    {{ $bridgeError }}
                </div>
            @endif

            @php
                $connectionStatus = $status['status'] ?? 'unknown';
                $statusLabels = [
                    'ready' => ['label' => 'متصل وجاهز', 'class' => 'bg-emerald-100 text-emerald-800'],
                    'qr' => ['label' => 'بانتظار مسح QR', 'class' => 'bg-amber-100 text-amber-800'],
                    'authenticated' => ['label' => 'تمت المصادقة', 'class' => 'bg-sky-100 text-sky-800'],
                    'disconnected' => ['label' => 'غير متصل', 'class' => 'bg-slate-100 text-slate-700'],
                    'error' => ['label' => 'خطأ', 'class' => 'bg-rose-100 text-rose-800'],
                    'auth_failure' => ['label' => 'فشل المصادقة', 'class' => 'bg-rose-100 text-rose-800'],
                ];
                $badge = $statusLabels[$connectionStatus] ?? ['label' => $connectionStatus, 'class' => 'bg-slate-100 text-slate-700'];
            @endphp

            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                @if(!empty($status['phone']))
                    <span class="text-sm text-slate-600">+{{ $status['phone'] }} @if(!empty($status['pushname'])) — {{ $status['pushname'] }} @endif</span>
                @endif
            </div>

            @if(!empty($status['last_error']))
                <p class="text-sm text-rose-600 mb-4">آخر خطأ: {{ $status['last_error'] }}</p>
            @endif

            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.whatsapp.start') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                        بدء / إعادة الاتصال
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.whatsapp.logout') }}" onsubmit="return confirm('قطع اتصال الواتساب؟');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-800 text-sm font-semibold hover:bg-slate-300">
                        قطع الاتصال
                    </button>
                </form>
            </div>
        </section>

        <section class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6" x-data="whatsappQr()" x-init="init()">
            <h3 class="text-lg font-bold text-slate-900 mb-4">رمز QR للربط</h3>
            <p class="text-sm text-slate-500 mb-4">افتح واتساب على الهاتف ← الأجهزة المرتبطة ← ربط جهاز ← امسح الرمز.</p>

            <div class="flex flex-col items-center justify-center min-h-[220px] rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-4">
                <template x-if="connected">
                    <div class="text-center text-emerald-700">
                        <i class="fab fa-whatsapp text-5xl mb-3"></i>
                        <p class="font-semibold">الواتساب متصل بالفعل</p>
                    </div>
                </template>
                <template x-if="!connected && qrImage">
                    <img :src="qrImage" alt="WhatsApp QR" class="max-w-[240px] rounded-lg shadow">
                </template>
                <template x-if="!connected && !qrImage && !loading">
                    <p class="text-sm text-slate-500 text-center" x-text="message || 'اضغط «تحديث QR» أو «بدء الاتصال»'"></p>
                </template>
                <template x-if="loading">
                    <p class="text-sm text-slate-500"><i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...</p>
                </template>
            </div>

            <button type="button" @click="refresh()" class="mt-4 w-full px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                تحديث QR / الحالة
            </button>
        </section>
    </div>
</div>

@push('scripts')
<script>
function whatsappQr() {
    return {
        qrImage: null,
        connected: false,
        loading: false,
        message: '',
        pollTimer: null,
        init() {
            this.refresh();
            this.pollTimer = setInterval(() => this.refresh(true), 8000);
        },
        async refresh(silent = false) {
            if (!silent) this.loading = true;
            try {
                const statusRes = await fetch(@json(route('admin.whatsapp.status')));
                const statusJson = await statusRes.json();
                const data = statusJson.data || {};
                this.connected = data.status === 'ready';
                if (this.connected) {
                    this.qrImage = null;
                    this.message = '';
                    return;
                }
                const qrRes = await fetch(@json(route('admin.whatsapp.qr')));
                const qrJson = await qrRes.json();
                if (qrJson.success && qrJson.data?.qr_image) {
                    this.qrImage = qrJson.data.qr_image;
                    this.message = '';
                } else {
                    this.message = qrJson.error || qrJson.data?.error || statusJson.error || 'QR غير جاهز بعد';
                }
            } catch (e) {
                this.message = 'تعذّر الاتصال بالجسر';
            } finally {
                if (!silent) this.loading = false;
            }
        }
    };
}
</script>
@endpush
@endsection
