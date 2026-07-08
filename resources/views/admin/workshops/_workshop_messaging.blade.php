@php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $waConfigured = \App\Support\WhatsAppCloudSettings::isAppConfigured();
    $waConnectionMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waConnectionMeta['can_send'] ?? false);
    $templates = $approvedWhatsAppTemplates ?? collect();
    $welcomeTpl = $welcomeTemplate ?? null;
    $batches = $workshopWhatsAppBatches ?? collect();
    $defaultTplKey = $welcomeTpl?->isSendable()
        ? $welcomeTpl->name.'|'.$welcomeTpl->language
        : ($templates->first() ? $templates->first()->name.'|'.$templates->first()->language : '');
    $messagePlaceholder = "مرحباً @{{name}}،\n\nشكراً لتسجيلك في ورشة «@{{workshop}}» (@{{attendance}}).";
@endphp

<section class="rounded-2xl border border-emerald-200 bg-white shadow-sm overflow-hidden" x-data="{ mode: 'template', showAdvanced: false }">
    <div class="px-5 py-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50/80 to-white flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب للمسجلين
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">اختر المستلمين والقالب ثم اضغط إرسال — تُتابع الدفعة من سجل الإرسال.</p>
        </div>
        <div class="flex flex-wrap gap-2 text-[11px]">
            <span class="px-2.5 py-1 rounded-full bg-white border border-slate-200 font-semibold">{{ $phoneCountAll }} رقم</span>
            @if($waCanSend)
                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Meta متصل</span>
            @else
                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">غير متصل</span>
            @endif
        </div>
    </div>

    <div class="p-5 space-y-4">
        @if(!$waConfigured || !$waCanSend)
            <p class="text-sm text-rose-800 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3">
                أكمل <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta WhatsApp</a> قبل الإرسال.
            </p>
        @elseif($phoneCountAll === 0)
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                لا يوجد مسجلون بأرقام هواتف في هذه الورشة.
            </p>
        @else
            {{-- المستلمون --}}
            <div>
                <p class="text-xs font-bold text-slate-800 mb-2">المستلمون</p>
                <div class="flex flex-wrap gap-3 text-sm" id="wa-scope-radios">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="wa_scope_ui" value="all" checked class="text-emerald-600"> كل المسجلين ({{ $phoneCountAll }})
                    </label>
                    @if($phoneCountOnline > 0)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="wa_scope_ui" value="online" class="text-emerald-600"> أونلاين ({{ $phoneCountOnline }})
                        </label>
                    @endif
                    @if($phoneCountOffline > 0)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="wa_scope_ui" value="offline" class="text-emerald-600"> حضوري ({{ $phoneCountOffline }})
                        </label>
                    @endif
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="wa_scope_ui" value="phone" class="text-emerald-600"> رقم محدد
                    </label>
                </div>
                <input type="text" id="wa-phone-input" placeholder="2010xxxxxxx" class="mt-2 w-full max-w-xs rounded-lg border border-slate-200 px-3 py-2 text-sm hidden">
            </div>

            {{-- نوع الإرسال --}}
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="mode='template'"
                        :class="mode === 'template' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors">
                    <i class="fas fa-file-alt ml-1"></i> قالب Meta
                </button>
                <button type="button" @click="mode='text'"
                        :class="mode === 'text' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors">
                    <i class="fas fa-comment ml-1"></i> رسالة نصية
                </button>
            </div>

            {{-- قالب Meta --}}
            <form x-show="mode === 'template'" x-cloak method="POST" action="{{ route('admin.workshops.whatsapp-template.send', $workshop) }}"
                  id="workshop-wa-template-form" class="space-y-3 rounded-xl border border-violet-100 bg-violet-50/40 p-4">
                @csrf
                <input type="hidden" name="scope" id="tpl-scope" value="all">
                <input type="hidden" name="phone" id="tpl-phone" value="">

                @if($templates->isEmpty())
                    <p class="text-sm text-amber-800">لا توجد قوالب معتمدة.
                        <a href="{{ route('admin.workshops.whatsapp-template.create.form', $workshop) }}" class="font-bold underline">أنشئ قالب ترحيب للورشة</a>
                        أو من <a href="{{ route('admin.whatsapp.templates.index') }}" class="font-bold underline">قسم القوالب</a>.
                    </p>
                @else
                    <label class="block text-xs font-bold text-slate-800">القالب</label>
                    <select name="template_name" id="wa-template-name" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white">
                        @foreach($templates as $tpl)
                            @php $key = $tpl->name.'|'.$tpl->language; @endphp
                            <option value="{{ $tpl->name }}" data-lang="{{ $tpl->language }}"
                                    @selected($key === $defaultTplKey)>
                                {{ $tpl->name }} · {{ $tpl->language }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="template_language" id="wa-template-lang"
                           value="{{ $templates->firstWhere(fn($t) => ($t->name.'|'.$t->language) === $defaultTplKey)?->language ?? $templates->first()?->language }}">

                    <p class="text-[11px] text-slate-600">
                        المتغيرات تُملأ تلقائياً: @{{1}} الاسم، @{{2}} الورشة، @{{3}} رابط الجروب، @{{4}} الهاتف، @{{5}} الحضور.
                    </p>

                    <button type="submit" @disabled(!$waCanSend)
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold">
                        <i class="fas fa-paper-plane"></i>
                        إرسال القالب للمسجلين
                    </button>
                @endif
            </form>

            {{-- رسالة نصية --}}
            <form x-show="mode === 'text'" x-cloak method="POST" action="{{ route('admin.workshops.whatsapp-bulk', $workshop) }}"
                  id="workshop-wa-bulk-form" class="space-y-3 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                @csrf
                <input type="hidden" name="scope" id="text-scope" value="all">
                <input type="hidden" name="phone" id="text-phone" value="">
                <textarea name="message" rows="4" required maxlength="4096" placeholder="{{ $messagePlaceholder }}"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('message') }}</textarea>
                <p class="text-[10px] text-slate-500">
                    متغيرات: <code class="bg-white px-1 rounded">@{{name}}</code>
                    <code class="bg-white px-1 rounded">@{{workshop}}</code>
                    <code class="bg-white px-1 rounded">@{{phone}}</code>
                    <code class="bg-white px-1 rounded">@{{attendance}}</code>
                </p>
                <button type="submit" @disabled(!$waCanSend)
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الرسالة للمسجلين
                </button>
            </form>
        @endif

        {{-- سجل الإرسال --}}
        @if($batches->isNotEmpty())
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-700 mb-2"><i class="fas fa-history text-slate-500 ml-1"></i> آخر الإرسالات</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($batches->take(5) as $batch)
                        @php
                            $isTemplate = ($batch->meta['send_mode'] ?? '') === 'template';
                            $tplName = $batch->meta['template_name'] ?? null;
                        @endphp
                        <a href="{{ route('admin.whatsapp.batches.show', $batch) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 hover:border-emerald-300 text-xs font-semibold text-slate-800">
                            @if($isTemplate)
                                <i class="fas fa-file-alt text-violet-600"></i>
                                {{ Str::limit($tplName ?: 'قالب', 20) }}
                            @else
                                <i class="fas fa-comment text-emerald-600"></i>
                                نصية
                            @endif
                            <span class="text-slate-500 tabular-nums">{{ $batch->sent_count }}/{{ $batch->total_count }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('admin.whatsapp.batches.index') }}" class="text-xs text-emerald-700 font-bold self-center hover:underline">كل الدفعات</a>
                </div>
            </div>
        @endif

        {{-- إعدادات متقدمة --}}
        <details class="rounded-xl border border-slate-200" @if($welcomeTpl && !$welcomeTpl->isSendable()) open @endif>
            <summary class="px-4 py-3 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50">
                <i class="fas fa-cog text-slate-400 ml-1"></i> إعداد قالب ترحيب الورشة · إيميل
            </summary>
            <div class="px-4 pb-4 pt-1 space-y-4 border-t border-slate-100">
                @if($welcomeTpl)
                    <div class="text-xs text-slate-600 space-y-1">
                        <p>قالب الورشة: <code class="bg-slate-100 px-1 rounded">{{ $welcomeTpl->name }}</code>
                            — <span class="font-bold">{{ $welcomeTpl->statusLabel() }}</span></p>
                        @if($welcomeTpl->rejection_reason)
                            <p class="text-rose-700">{{ $welcomeTpl->rejection_reason }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.workshops.whatsapp-template.sync', $workshop) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-violet-700 hover:underline">مزامنة من Meta</button>
                    </form>
                @endif

                @if(!$welcomeTpl || !$welcomeTpl->isSendable())
                    <div class="rounded-lg border border-violet-100 bg-violet-50/50 p-3 space-y-2">
                        <p class="text-xs text-slate-700">أنشئ أو عدّل قالب الترحيب بنفس نموذج Meta الرسمي (Header، Body، Footer، أزرار، متغيرات).</p>
                        <a href="{{ route('admin.workshops.whatsapp-template.create.form', $workshop) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-violet-600 text-white text-xs font-bold hover:bg-violet-700">
                            <i class="fas fa-plus-circle"></i>
                            {{ $welcomeTpl ? 'تعديل قالب الورشة' : 'إنشاء قالب ترحيب' }}
                        </a>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.workshops.send-acceptance', $workshop) }}" class="flex flex-wrap items-end gap-2 pt-2 border-t border-slate-100">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-bold text-slate-700 block mb-1">إيميل القبول ({{ $emailPendingCount ?? 0 }} متبقي)</label>
                        <select name="scope" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                            <option value="all">كل المسجلين</option>
                            <option value="email">بريد محدد</option>
                        </select>
                    </div>
                    <input type="email" name="email" placeholder="email@..." class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold">إرسال</button>
                </form>
            </div>
        </details>
    </div>
</section>

@push('scripts')
<script>
(function () {
    const scopeRadios = document.querySelectorAll('input[name="wa_scope_ui"]');
    const phoneInput = document.getElementById('wa-phone-input');
    const tplScope = document.getElementById('tpl-scope');
    const tplPhone = document.getElementById('tpl-phone');
    const textScope = document.getElementById('text-scope');
    const textPhone = document.getElementById('text-phone');

    function syncScope() {
        const scope = document.querySelector('input[name="wa_scope_ui"]:checked')?.value || 'all';
        const phone = phoneInput?.value || '';
        if (phoneInput) {
            phoneInput.classList.toggle('hidden', scope !== 'phone');
        }
        [tplScope, textScope].forEach(el => { if (el) el.value = scope; });
        [tplPhone, textPhone].forEach(el => { if (el) el.value = phone; });
    }

    scopeRadios.forEach(r => r.addEventListener('change', syncScope));
    phoneInput?.addEventListener('input', syncScope);
    syncScope();

    const tplSelect = document.getElementById('wa-template-name');
    const tplLang = document.getElementById('wa-template-lang');
    if (tplSelect && tplLang) {
        tplLang.value = tplSelect.selectedOptions[0]?.dataset.lang || tplLang.value;
        tplSelect.addEventListener('change', function () {
            tplLang.value = this.selectedOptions[0]?.dataset.lang || '';
        });
    }

    function confirmCount(scope) {
        let count = {{ (int) $phoneCountAll }};
        if (scope === 'online') count = {{ (int) $phoneCountOnline }};
        else if (scope === 'offline') count = {{ (int) $phoneCountOffline }};
        else if (scope === 'phone') count = 1;
        return count;
    }

    document.getElementById('workshop-wa-template-form')?.addEventListener('submit', function (e) {
        syncScope();
        const scope = tplScope?.value || 'all';
        if (!confirm('إرسال القالب إلى ' + confirmCount(scope) + ' مسجّل؟')) e.preventDefault();
    });

    document.getElementById('workshop-wa-bulk-form')?.addEventListener('submit', function (e) {
        syncScope();
        const scope = textScope?.value || 'all';
        if (!confirm('إرسال الرسالة إلى ' + confirmCount(scope) + ' مسجّل؟')) e.preventDefault();
    });
})();
</script>
@endpush
