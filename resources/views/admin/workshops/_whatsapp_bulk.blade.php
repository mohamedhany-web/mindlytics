@php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waTemplateVars = ['{{name}}', '{{phone}}', '{{workshop}}', '{{attendance}}', '{{location}}'];
    $messagePlaceholder = "مرحباً {{name}}،\n\nشكراً لتسجيلك في ورشة «{{workshop}}» ({{attendance}}).\n\n...";
    $waConfigured = \App\Support\WhatsAppCloudSettings::isAppConfigured();
    $waConnectionMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waConnectionMeta['can_send'] ?? false);
@endphp

<section class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50/80 to-white p-4 sm:p-5 space-y-4 h-full">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب لكل المسجلين
            </h3>
            <p class="text-xs text-slate-600 mt-1">
                يُرسل عبر Meta Cloud API مع تأخير آمن بين الرسائل، إعادة محاولة تلقائية، ومتابعة حية لكل رقم.
            </p>
        </div>
        @if($waConfigured)
            @if($waCanSend)
                <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Meta متصل</span>
            @else
                <span class="text-[10px] px-2 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">{{ $waConnectionMeta['label'] ?? 'غير متصل' }}</span>
            @endif
        @else
            <span class="text-[10px] px-2 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">Meta غير مضبوط</span>
        @endif
    </div>

    <div class="flex flex-wrap gap-2 text-[11px]">
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong>{{ $phoneCountAll }}</strong> رقم (الكل)
        </span>
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong>{{ $phoneCountOnline }}</strong> أونلاين
        </span>
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong>{{ $phoneCountOffline }}</strong> حضوري
        </span>
    </div>

    @if(!$waConfigured)
        <p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
            الإرسال التلقائي غير متاح — أكمل
            <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta WhatsApp</a>
            أو استخدم «فتح روابط يدوياً» بالأسفل.
        </p>
    @elseif(!$waCanSend)
        <div class="text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2 space-y-2">
            <p>
                <strong>يجب ربط WhatsApp Business أولاً</strong> — {{ $waConnectionMeta['label'] ?? 'غير متصل' }}
            </p>
            <a href="{{ route('admin.whatsapp.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700">
                <i class="fas fa-qrcode"></i> ربط الواتساب الآن
            </a>
        </div>
    @elseif($phoneCountAll === 0)
        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            لا يوجد مسجلون بأرقام هواتف في هذه الورشة.
        </p>
    @else
        <div class="text-[11px] text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
            <span>اليوم: {{ $pacing['day'] }}/{{ $pacing['max_day'] }}</span>
            <span>هذه الساعة: {{ $pacing['hour'] }}/{{ $pacing['max_hour'] }}</span>
            @if($remainingToday !== null)
                <span>متبقي اليوم: {{ $remainingToday }}</span>
            @endif
        </div>

        <form method="post" action="{{ route('admin.workshops.whatsapp-bulk', $workshop) }}"
              id="workshop-wa-bulk-form"
              class="space-y-3">
            @csrf
            <div class="flex flex-wrap gap-3 text-xs">
                <label class="inline-flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="scope" value="all" checked class="text-emerald-600"> كل الأرقام ({{ $phoneCountAll }})
                </label>
                @if($phoneCountOnline > 0)
                    <label class="inline-flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="scope" value="online" class="text-emerald-600"> أونلاين فقط ({{ $phoneCountOnline }})
                    </label>
                @endif
                @if($phoneCountOffline > 0)
                    <label class="inline-flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="scope" value="offline" class="text-emerald-600"> حضوري فقط ({{ $phoneCountOffline }})
                    </label>
                @endif
                <label class="inline-flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="scope" value="phone" class="text-emerald-600"> رقم محدد
                </label>
            </div>
            <input type="text" name="phone" placeholder="2010xxxxxxx (عند اختيار رقم محدد)"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
            <div>
                <textarea name="message" rows="4" required maxlength="4096"
                          placeholder="{{ $messagePlaceholder }}"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">{{ old('message') }}</textarea>
                @error('message')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-[10px] text-slate-500 mt-1">
                    متغيرات:
                    @foreach($waTemplateVars as $var)
                        <code class="bg-slate-100 px-1 rounded">{{ $var }}</code>
                    @endforeach
                </p>
            </div>
            <button type="submit" @disabled(!$waCanSend)
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-xs font-bold">
                <i class="fas fa-paper-plane"></i>
                بدء الإرسال الآمن لكل الأرقام
            </button>
            <p class="text-[10px] text-slate-500 leading-relaxed">
                تأخير 5–14 ثانية بين الرسائل، استراحة كل 20 رسالة، حتى 3 محاولات لكل رقم، ومتابعة من صفحة الدفعة مع إعادة إرسال الفاشل فقط.
            </p>
        </form>
    @endif

    @if(!empty($latestWhatsAppBatch))
        <div class="pt-3 border-t border-emerald-200/60 text-xs">
            <span class="text-slate-600">آخر دفعة:</span>
            <a href="{{ route('admin.whatsapp.batches.show', $latestWhatsAppBatch) }}" class="text-emerald-700 font-bold hover:underline">
                #{{ $latestWhatsAppBatch->id }} — {{ $latestWhatsAppBatch->statusLabel() }}
                ({{ $latestWhatsAppBatch->sent_count }}/{{ $latestWhatsAppBatch->total_count }})
            </a>
        </div>
    @endif
</section>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('workshop-wa-bulk-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const scope = form.querySelector('input[name="scope"]:checked')?.value || 'all';
        let count = {{ (int) $phoneCountAll }};
        if (scope === 'online') count = {{ (int) $phoneCountOnline }};
        else if (scope === 'offline') count = {{ (int) $phoneCountOffline }};
        else if (scope === 'phone') count = 1;

        const msg = 'بدء إرسال ' + count + ' رسالة واتساب في الخلفية؟\n\n'
            + 'سيتم الإرسال تدريجياً لتقليل خطر الحظر.\n'
            + 'يمكنك متابعة التقدّم وإعادة إرسال الفاشل من صفحة الدفعة.';

        if (!confirm(msg)) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
