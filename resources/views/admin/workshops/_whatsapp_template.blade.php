@php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waConfigured = \App\Support\WhatsAppCloudSettings::isAppConfigured();
    $waConnectionMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waConnectionMeta['can_send'] ?? false);
    $tpl = $welcomeTemplate ?? null;
    $tplApproved = $tpl?->isSendable() ?? false;
    $defaultBody = $defaultWelcomeBody ?? app(\App\Services\WorkshopWhatsAppTemplateService::class)->defaultWelcomeBody();
    $displayBody = str_replace(['{{1}}', '{{2}}'], ['{{name}}', '{{workshop_name}}'], $defaultBody);
    $batches = $workshopWhatsAppBatches ?? collect();
    $tplBodyDisplay = $tpl?->body_text
        ? str_replace(['{{1}}', '{{2}}'], ['{{name}}', '{{workshop_name}}'], $tpl->body_text)
        : '';
    $textareaBody = old('body_text');
    if ($textareaBody === null) {
        $textareaBody = $tplBodyDisplay !== '' ? $tplBodyDisplay : $displayBody;
    }
@endphp

<section class="rounded-xl border border-violet-200 bg-gradient-to-br from-violet-50/80 to-white p-4 sm:p-5 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-file-alt text-violet-600"></i>
                قالب ترحيب Meta — انضمام للورشة
            </h3>
            <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                أنشئ قالباً معتمداً من Meta وارسله لجميع المسجلين. المتغيرات:
                <code class="bg-white px-1 rounded text-[10px]">@{{name}}</code>
                (اسم المسجّل) و
                <code class="bg-white px-1 rounded text-[10px]">@{{workshop_name}}</code>
                (اسم الورشة).
            </p>
        </div>
        @if($tpl)
            <span class="text-[10px] px-2.5 py-1 rounded-full font-bold
                @if($tplApproved) bg-emerald-100 text-emerald-800
                @elseif($tpl->status === 'pending') bg-amber-100 text-amber-800
                @else bg-slate-100 text-slate-700 @endif">
                {{ $tpl->statusLabel() }}
            </span>
        @endif
    </div>

    @if($tpl)
        <div class="rounded-lg border border-violet-100 bg-white/80 px-3 py-2 text-xs text-slate-700 space-y-1">
            <p><strong>اسم القالب في Meta:</strong> <code class="bg-slate-100 px-1 rounded">{{ $tpl->name }}</code></p>
            <p><strong>اللغة:</strong> {{ $tpl->language }}</p>
            @if($tpl->body_text)
                <p class="text-slate-600 whitespace-pre-line mt-2 border-t border-slate-100 pt-2">{{ $tplBodyDisplay }}</p>
            @endif
            @if($tpl->rejection_reason)
                <p class="text-rose-700 bg-rose-50 rounded px-2 py-1 mt-1"><strong>سبب الرفض:</strong> {{ $tpl->rejection_reason }}</p>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.workshops.whatsapp-template.sync', $workshop) }}" class="inline">
            @csrf
            <button type="submit" class="text-xs font-bold text-violet-700 hover:underline">
                <i class="fas fa-sync ml-1"></i> مزامنة الحالة من Meta
            </button>
        </form>
    @endif

    @if(!$waConfigured || !$waCanSend)
        <p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
            أكمل <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta WhatsApp</a> أولاً.
        </p>
    @elseif($phoneCountAll === 0)
        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            لا يوجد مسجلون بأرقام هواتف.
        </p>
    @else
        {{-- إنشاء / تحديث القالب --}}
        @if(! $tplApproved)
            <form method="POST" action="{{ route('admin.workshops.whatsapp-template.create', $workshop) }}" class="space-y-3 border-t border-violet-100 pt-4">
                @csrf
                <label class="block text-xs font-bold text-slate-800">نص رسالة الترحيب (يُحوَّل تلقائياً لصيغة Meta @{{1}} و @{{2}})</label>
                <textarea name="body_text" rows="6" maxlength="1024"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs leading-relaxed">{{ $textareaBody }}</textarea>
                <button type="submit" @disabled(!$waCanSend)
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold">
                    <i class="fas fa-cloud-upload-alt"></i>
                    {{ $tpl ? 'إعادة إرسال القالب لـ Meta' : 'إنشاء القالب وإرساله لـ Meta' }}
                </button>
                <p class="text-[10px] text-slate-500">بعد موافقة Meta (عادة خلال دقائق إلى ساعات) اضغط «مزامنة الحالة» ثم «إرسال للجميع».</p>
            </form>
        @endif

        {{-- إرسال القالب المعتمد --}}
        @if($tplApproved)
            <div class="border-t border-violet-100 pt-4 space-y-3">
                <div class="text-[11px] text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
                    <span>اليوم: {{ $pacing['day'] }}/{{ $pacing['max_day'] }}</span>
                    @if($remainingToday !== null)
                        <span>متبقي اليوم: {{ $remainingToday }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.workshops.whatsapp-template.send', $workshop) }}"
                      id="workshop-wa-template-form" class="space-y-3">
                    @csrf
                    <div class="flex flex-wrap gap-3 text-xs">
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="scope" value="all" checked class="text-violet-600"> كل الأرقام ({{ $phoneCountAll }})
                        </label>
                        @if($phoneCountOnline > 0)
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="scope" value="online" class="text-violet-600"> أونلاين ({{ $phoneCountOnline }})
                            </label>
                        @endif
                        @if($phoneCountOffline > 0)
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="scope" value="offline" class="text-violet-600"> حضوري ({{ $phoneCountOffline }})
                            </label>
                        @endif
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="scope" value="phone" class="text-violet-600"> رقم محدد
                        </label>
                    </div>
                    <input type="text" name="phone" placeholder="2010xxxxxxx" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                    <button type="submit" @disabled(!$waCanSend)
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-lg text-sm font-bold">
                        <i class="fas fa-paper-plane"></i>
                        إرسال قالب الترحيب لجميع المسجلين
                    </button>
                    <p class="text-[10px] text-slate-500">
                        يُسجَّل في «دفعات الواتساب» مع اسم القالب <code class="bg-slate-100 px-1 rounded">{{ $tpl->name }}</code> وعدد المستلمين.
                    </p>
                </form>
            </div>
        @endif
    @endif

    {{-- سجل الدفعات --}}
    @if($batches->isNotEmpty())
        <div class="border-t border-violet-100 pt-3 space-y-2">
            <p class="text-xs font-bold text-slate-800"><i class="fas fa-history text-violet-600 ml-1"></i> سجل الإرسال</p>
            <div class="space-y-1.5 max-h-40 overflow-y-auto">
                @foreach($batches as $batch)
                    @php
                        $isTemplate = ($batch->meta['send_mode'] ?? '') === 'template';
                        $tplName = $batch->meta['template_name'] ?? null;
                    @endphp
                    <a href="{{ route('admin.whatsapp.batches.show', $batch) }}"
                       class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 rounded-lg bg-white border border-slate-100 hover:border-violet-200 text-xs transition-colors">
                        <span class="font-semibold text-slate-800">
                            @if($isTemplate)
                                <i class="fas fa-file-alt text-violet-600 ml-1"></i>
                                قالب {{ $tplName ?: 'ترحيب' }}
                            @else
                                <i class="fas fa-comment text-emerald-600 ml-1"></i>
                                رسالة نصية
                            @endif
                        </span>
                        <span class="text-slate-500 tabular-nums">{{ $batch->sent_count }}/{{ $batch->total_count }} · {{ $batch->statusLabel() }}</span>
                        <span class="text-[10px] text-slate-400 w-full">{{ $batch->created_at?->format('Y-m-d H:i') }}</span>
                    </a>
                @endforeach
            </div>
            <a href="{{ route('admin.whatsapp.batches.index') }}" class="text-[10px] text-violet-700 font-bold hover:underline">كل دفعات الواتساب ←</a>
        </div>
    @endif
</section>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('workshop-wa-template-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const scope = form.querySelector('input[name="scope"]:checked')?.value || 'all';
        let count = {{ (int) $phoneCountAll }};
        if (scope === 'online') count = {{ (int) $phoneCountOnline }};
        else if (scope === 'offline') count = {{ (int) $phoneCountOffline }};
        else if (scope === 'phone') count = 1;
        if (!confirm('إرسال قالب الترحيب Meta إلى ' + count + ' مسجّل؟\n\nستظهر الدفعة في سجل الإدارة.')) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
