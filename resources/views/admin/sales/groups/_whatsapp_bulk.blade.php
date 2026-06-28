@php
    $leadsWithPhone = $leadsWithPhone ?? ($group->leads ?? collect())->filter(fn ($l) => !empty($l->phone));
    $phoneCount = $leadsWithPhone->count();
    $waConfigured = \App\Support\WhatsAppCloudSettings::usesOfficial();
    $waMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waMeta['can_send'] ?? false);
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waTemplateVars = ['{{name}}', '{{company}}', '{{phone}}'];
    $messagePlaceholder = 'مرحباً {{name}}، ...';
@endphp

<section class="{{ $panelClass ?? 'bg-white border border-slate-200 rounded-xl p-5 space-y-4' }}">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب جماعي للمجموعة
            </h3>
            <p class="text-sm text-slate-600 mt-1">
                يُرسل لـ <strong>{{ $phoneCount }}</strong> عميل لديه رقم — عبر الطابور مع تأخير آمن بين الرسائل.
            </p>
        </div>
        @if($waCanSend)
            <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Meta متصل</span>
        @elseif($waConfigured)
            <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">{{ $waMeta['label'] ?? 'غير جاهز' }}</span>
        @else
            <span class="text-xs px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-semibold">Meta غير مفعّل</span>
        @endif
    </div>

    @if(!$waConfigured)
        <p class="text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
            إرسال الواتساب غير متاح — <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta WhatsApp</a>
        </p>
    @elseif(!$waCanSend)
        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            {{ $waMeta['label'] ?? 'WhatsApp غير مربوط' }} — <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">أكمل الربط</a>
        </p>
    @elseif($phoneCount === 0)
        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            لا يوجد عملاء بأرقام هواتف في هذه المجموعة.
        </p>
    @else
        <div class="text-xs text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
            <span>اليوم: {{ $pacing['day'] }}/{{ $pacing['max_day'] }}</span>
            <span>هذه الساعة: {{ $pacing['hour'] }}/{{ $pacing['max_hour'] }}</span>
            @if($remainingToday !== null)
                <span>متبقي اليوم: {{ $remainingToday }}</span>
            @endif
        </div>

        <form method="post" action="{{ $formAction }}"
              onsubmit="return confirm('بدء إرسال {{ $phoneCount }} رسالة واتساب في الخلفية؟\n\nسيتم الإرسال تدريجياً لتقليل خطر الحظر.');"
              class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">نص الرسالة</label>
                <textarea name="message" rows="5" required maxlength="4096"
                          placeholder="{{ $messagePlaceholder }}"
                          class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm">{{ old('message') }}</textarea>
                @error('message')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-500 mt-1">
                    متغيرات:
                    @foreach($waTemplateVars as $var)
                        <code class="bg-slate-100 px-1 rounded">{{ $var }}</code>
                    @endforeach
                </p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                <i class="fas fa-paper-plane"></i>
                إرسال لـ {{ $phoneCount }} رقم (Queue)
            </button>
            <p class="text-[11px] text-slate-500">يُعاد المحاولة تلقائياً حتى 3 مرات عند أخطاء الاتصال المؤقتة.</p>
        </form>
    @endif

    @if(!empty($latestBatch))
        <div class="pt-3 border-t border-slate-100 text-sm">
            <span class="text-slate-600">آخر دفعة:</span>
            <a href="{{ $latestBatchUrl ?? '#' }}" class="text-emerald-700 font-semibold hover:underline">
                #{{ $latestBatch->id }} — {{ $latestBatch->statusLabel() }}
                ({{ $latestBatch->sent_count }}/{{ $latestBatch->total_count }})
            </a>
        </div>
    @endif
</section>
