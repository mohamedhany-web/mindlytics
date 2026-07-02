@extends('layouts.admin')

@section('title', 'محادثات الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $activeId = $activeConversation?->id;
    $withinWindow = (bool) ($withinWindow ?? false);
    $inboxConfig = [
        'conversationId' => $activeId,
        'pollUrl' => route('admin.whatsapp.inbox.poll'),
        'replyUrl' => $activeId ? route('admin.whatsapp.inbox.reply', $activeConversation) : null,
        'templateUrl' => $activeId ? route('admin.whatsapp.inbox.template', $activeConversation) : null,
        'startUrl' => route('admin.whatsapp.inbox.start'),
        'templatesUrl' => route('admin.whatsapp.inbox.templates'),
        'csrf' => csrf_token(),
        'withinWindow' => $withinWindow,
        'lastMessageId' => $messages->last()?->id ?? 0,
        'metaTemplates' => $metaTemplates ?? [],
    ];
    $firstTemplate = ($metaTemplates ?? [])[0] ?? null;
@endphp

<script>window.__waInboxConfig = @json($inboxConfig);</script>

<div class="p-3 sm:p-4 md:p-6 space-y-4" style="background:#f8fafc; min-height:100vh;" x-data="whatsappInbox()">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'inbox'])

    @include('admin.whatsapp._page-header', [
        'title' => 'المحادثات الواردة',
        'subtitle' => 'استقبال رسائل العملاء والرد من النظام — مع دعم قوالب Meta لبدء المحادثة.',
        'icon' => 'fas fa-inbox',
        'actions' => '
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white border border-slate-200 text-slate-700">
                غير مقروء: <span x-text="unreadTotal">' . (int) $unreadTotal . '</span>
            </span>
            <button type="button" @click="showStartModal = true" class="' . $waBtnPrimary . ' text-sm"><i class="fas fa-plus"></i> محادثة جديدة</button>
        ',
    ])

    @if(! $tablesReady)
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
            <p class="font-bold">تشغيل الترحيل مطلوب</p>
            <p class="mt-1">نفّذ على السيرفر: <code class="bg-white px-2 py-0.5 rounded">php artisan migrate --force</code></p>
        </div>
    @elseif(! $canSend)
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            الربط غير مكتمل — <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">أكمل إعداد Meta</a> وفعّل Webhook لاستقبال الرسائل.
        </div>
    @else
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-900">
            <strong>Webhook مطلوب للرسائل الواردة:</strong> Callback URL <code class="dir-ltr">{{ \App\Support\WhatsAppCloudSettings::webhookUrl() }}</code>
            — اشترك في <code>messages</code> و <code>message_status</code> في Meta Developers.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-[70vh]">
        {{-- قائمة المحادثات --}}
        <aside class="lg:col-span-4 xl:col-span-3 {{ $waSectionClass }} flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <form method="GET" action="{{ route('admin.whatsapp.inbox') }}" class="flex gap-2">
                    @if($activeId)
                        <input type="hidden" name="conversation" value="{{ $activeId }}">
                    @endif
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الرقم..."
                           class="{{ $waInputClass }} text-sm flex-1">
                    <button type="submit" class="{{ $waBtnDark }} !px-3"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    <a href="{{ route('admin.whatsapp.inbox', ['conversation' => $conv->id, 'search' => request('search')]) }}"
                       data-wa-conv-link
                       class="block px-4 py-3 hover:bg-emerald-50/60 transition-colors {{ $activeId === $conv->id ? 'bg-emerald-50 border-r-4 border-emerald-500' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 truncate">{{ $conv->displayName() }}</p>
                                <p class="text-[11px] text-slate-500 dir-ltr text-right font-mono">{{ $conv->formattedPhone() }}</p>
                                @if($conv->last_message_preview)
                                    <p class="text-xs text-slate-600 mt-1 truncate">{{ $conv->last_message_preview }}</p>
                                @endif
                            </div>
                            <div class="text-left shrink-0">
                                @if($conv->unread_count > 0)
                                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold">{{ $conv->unread_count }}</span>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-1 whitespace-nowrap">{{ $conv->last_message_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                        <p>لا توجد محادثات بعد</p>
                        <p class="text-xs mt-2">ستظهر هنا الرسائل المرسلة من النظام، ورسائل العملاء الواردة عبر Webhook</p>
                    </div>
                @endforelse
            </div>
            @if($conversations->hasPages())
                <div class="p-3 border-t border-slate-200">{{ $conversations->links() }}</div>
            @endif
        </aside>

        {{-- نافذة المحادثة --}}
        <section class="lg:col-span-8 xl:col-span-9 {{ $waSectionClass }} flex flex-col overflow-hidden min-h-[60vh]">
            @if($activeConversation)
                <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $activeConversation->displayName() }}</h3>
                        <p class="text-xs text-slate-500 dir-ltr">{{ $activeConversation->formattedPhone() }}
                            @if($activeConversation->user)
                                · {{ $activeConversation->user->name }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($withinWindow)
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-clock"></i> نافذة 24 ساعة مفتوحة
                            </span>
                        @else
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                <i class="fas fa-file-alt"></i> استخدم قالب Meta للرد
                            </span>
                        @endif
                    </div>
                </div>

                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 bg-[#e5ddd5]/30" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.25\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    @foreach($messages as $msg)
                        @include('admin.whatsapp._inbox_message', ['msg' => $msg])
                    @endforeach
                    <template x-for="msg in newMessages" :key="'n-' + msg.id">
                        <div class="flex" :class="msg.is_inbound ? 'justify-start' : 'justify-end'">
                            <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl px-4 py-2.5 shadow-sm text-sm whitespace-pre-wrap break-words"
                                 :class="msg.is_inbound ? 'bg-white text-slate-800 rounded-tl-sm' : 'bg-emerald-100 text-emerald-950 rounded-tr-sm border border-emerald-200'">
                                <p x-text="msg.body"></p>
                                <p class="text-[10px] mt-1 opacity-60" x-text="msg.created_at_human"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-slate-200 bg-white p-4 space-y-3">
                    <div x-show="replyError" x-cloak class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2" x-text="replyError"></div>

                    <div x-show="withinWindow" class="flex gap-2 items-end">
                        <textarea x-model="replyBody" rows="2" placeholder="اكتب ردك..."
                                  class="{{ $waInputClass }} text-sm flex-1 resize-none" @keydown.ctrl.enter="sendReply()"></textarea>
                        <button type="button" @click="sendReply()" :disabled="sending || !replyBody.trim()"
                                class="{{ $waBtnPrimary }} !px-4 shrink-0 disabled:opacity-50">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>

                    <div x-show="!withinWindow" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 p-3 space-y-2">
                        <p class="text-xs text-amber-900 font-semibold">خارج نافذة 24 ساعة — أرسل قالب Meta معتمد:</p>
                        <div class="flex flex-wrap gap-2">
                            <select x-model="selectedTemplateKey" @change="applySelectedTemplate()" class="{{ $waSelectClass }} text-sm flex-1 min-w-[180px]">
                                <option value="">اختر قالباً معتمداً...</option>
                                <template x-for="t in metaTemplates" :key="t.name + '|' + t.language">
                                    <option :value="t.name + '|' + t.language" x-text="t.label"></option>
                                </template>
                            </select>
                            <button type="button" @click="sendTemplate()" :disabled="sending || !templateName"
                                    class="{{ $waBtnPrimary }} text-sm disabled:opacity-50">
                                <i class="fas fa-file-alt"></i> إرسال قالب
                            </button>
                        </div>
                        <p x-show="metaTemplates.length === 0" class="text-[11px] text-amber-800">
                            لا توجد قوالب معتمدة — أنشئ قالباً في
                            <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank" rel="noopener" class="underline font-semibold">WhatsApp Manager</a>
                        </p>
                    </div>

                    <div x-show="withinWindow" class="flex flex-wrap gap-2 pt-1 border-t border-slate-100">
                        <span class="text-[10px] text-slate-500 w-full">أو أرسل قالب معتمد:</span>
                        <select x-model="selectedTemplateKey" @change="applySelectedTemplate()" class="{{ $waSelectClass }} text-xs flex-1 min-w-[140px] py-1.5">
                            <option value="">قالب...</option>
                            <template x-for="t in metaTemplates" :key="'r-' + t.name + t.language">
                                <option :value="t.name + '|' + t.language" x-text="t.label"></option>
                            </template>
                        </select>
                        <button type="button" @click="sendTemplate()" :disabled="sending || !templateName"
                                class="{{ $waBtnSecondary }} text-xs !py-1.5">قالب</button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8">
                    <i class="fab fa-whatsapp text-5xl text-emerald-300 mb-4"></i>
                    <p class="font-semibold text-slate-700">اختر محادثة أو ابدأ محادثة جديدة</p>
                    <button type="button" @click="showStartModal = true" class="{{ $waBtnPrimary }} mt-4 text-sm">محادثة جديدة بقالب</button>
                </div>
            @endif
        </section>
    </div>

    {{-- modal محادثة جديدة --}}
    <div x-show="showStartModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @keydown.escape.window="showStartModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4" @click.outside="showStartModal = false">
            <h3 class="font-bold text-lg text-slate-900">بدء محادثة جديدة</h3>
            <p class="text-xs text-slate-600">أول رسالة يجب أن تكون <strong>قالب Meta معتمد (Approved)</strong> من حسابكم — ليس بالضرورة <code>hello_world</code>.</p>
            @if(!empty($metaTemplatesError))
                <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">{{ $metaTemplatesError }}</p>
            @endif
            <div>
                <label class="{{ $waLabelClass }}">رقم الواتساب</label>
                <input type="text" x-model="startPhone" placeholder="2010xxxxxxx" class="{{ $waInputClass }} dir-ltr text-sm">
            </div>
            <div>
                <label class="{{ $waLabelClass }}">قالب Meta المعتمد</label>
                @if(count($metaTemplates ?? []) > 0)
                    <select x-model="selectedTemplateKey" @change="applySelectedTemplate(true)" class="{{ $waSelectClass }} text-sm dir-ltr">
                        @foreach($metaTemplates as $tpl)
                            <option value="{{ $tpl['name'] }}|{{ $tpl['language'] }}">{{ $tpl['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
                        لا توجد قوالب معتمدة في حسابك.
                        <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank" rel="noopener" class="underline font-semibold">أنشئ قالباً في WhatsApp Manager</a>
                        ثم حدّث الصفحة.
                    </p>
                @endif
            </div>
            <p x-show="startError" class="text-xs text-rose-600" x-text="startError"></p>
            <div class="flex gap-2 justify-end">
                <button type="button" @click="showStartModal = false" class="{{ $waBtnSecondary }} text-sm">إلغاء</button>
                <button type="button" @click="startConversation()" :disabled="sending" class="{{ $waBtnPrimary }} text-sm">بدء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function whatsappInbox() {
    const cfg = window.__waInboxConfig || {};
    return {
        conversationId: cfg.conversationId || null,
        pollUrl: cfg.pollUrl,
        replyUrl: cfg.replyUrl,
        templateUrl: cfg.templateUrl,
        startUrl: cfg.startUrl,
        templatesUrl: cfg.templatesUrl,
        csrf: cfg.csrf,
        withinWindow: !!cfg.withinWindow,
        lastMessageId: cfg.lastMessageId || 0,
        unreadTotal: {{ (int) $unreadTotal }},
        metaTemplates: cfg.metaTemplates || [],
        replyBody: '',
        templateName: '',
        templateLang: '',
        selectedTemplateKey: '',
        startPhone: '',
        startTemplate: '',
        startLang: '',
        newMessages: [],
        replyError: '',
        startError: '',
        sending: false,
        showStartModal: false,
        pollTimer: null,

        init() {
            this.bootstrapTemplates();
            this.scrollChat();
            this.pollTimer = setInterval(() => this.poll(), 8000);
        },

        bootstrapTemplates() {
            if (this.metaTemplates.length > 0) {
                const first = this.metaTemplates[0];
                this.selectedTemplateKey = first.name + '|' + first.language;
                this.applySelectedTemplate(true);
                return;
            }
            if (this.templatesUrl) {
                fetch(this.templatesUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        if (Array.isArray(data.templates) && data.templates.length) {
                            this.metaTemplates = data.templates;
                            const first = data.templates[0];
                            this.selectedTemplateKey = first.name + '|' + first.language;
                            this.applySelectedTemplate(true);
                        }
                    })
                    .catch(() => {});
            }
        },

        applySelectedTemplate(forStart = false) {
            if (!this.selectedTemplateKey) return;
            const parts = this.selectedTemplateKey.split('|');
            const name = parts[0] || '';
            const lang = parts[1] || 'en_US';
            this.templateName = name;
            this.templateLang = lang;
            if (forStart) {
                this.startTemplate = name;
                this.startLang = lang;
            }
        },

        scrollChat() {
            this.$nextTick(() => {
                const el = document.getElementById('chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        async poll() {
            if (!this.pollUrl) return;
            try {
                const params = new URLSearchParams();
                if (this.conversationId) {
                    params.set('conversation_id', this.conversationId);
                    params.set('after_id', this.lastMessageId);
                }
                const res = await fetch(this.pollUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.success) return;
                this.unreadTotal = data.unread_total ?? this.unreadTotal;
                if (Array.isArray(data.conversations) && data.conversations.length && !this.conversationId && !document.querySelector('[data-wa-conv-link]')) {
                    window.location.reload();
                    return;
                }
                if (data.within_service_window !== undefined) {
                    this.withinWindow = !!data.within_service_window;
                }
                if (Array.isArray(data.messages) && data.messages.length) {
                    data.messages.forEach(m => {
                        this.newMessages.push(m);
                        this.lastMessageId = Math.max(this.lastMessageId, m.id);
                    });
                    this.scrollChat();
                }
            } catch (_) {}
        },

        async sendReply() {
            if (!this.replyUrl || !this.replyBody.trim()) return;
            this.sending = true;
            this.replyError = '';
            try {
                const res = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ body: this.replyBody }),
                });
                const data = await res.json();
                if (!data.success) {
                    this.replyError = data.error || 'فشل الإرسال';
                    if (data.requires_template) this.withinWindow = false;
                    return;
                }
                this.newMessages.push(data.message);
                this.lastMessageId = data.message.id;
                this.replyBody = '';
                this.scrollChat();
            } catch (e) {
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async sendTemplate() {
            if (!this.templateUrl || !this.templateName.trim()) return;
            this.sending = true;
            this.replyError = '';
            try {
                const res = await fetch(this.templateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        template_name: this.templateName,
                        language_code: this.templateLang || 'en_US',
                    }),
                });
                const data = await res.json();
                if (!data.success) {
                    this.replyError = data.error || 'فشل إرسال القالب';
                    return;
                }
                this.newMessages.push(data.message);
                this.lastMessageId = data.message.id;
                this.scrollChat();
            } catch (e) {
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async startConversation() {
            this.applySelectedTemplate(true);
            if (!this.startUrl || !this.startPhone.trim() || !this.startTemplate.trim()) {
                this.startError = 'أدخل الرقم واختر قالباً معتمداً من القائمة';
                return;
            }
            this.sending = true;
            this.startError = '';
            try {
                const res = await fetch(this.startUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        phone: this.startPhone,
                        template_name: this.startTemplate,
                        language_code: this.startLang || 'en_US',
                    }),
                });
                const data = await res.json();
                if (!data.success) {
                    this.startError = data.error || 'فشل البدء';
                    return;
                }
                window.location.href = data.redirect;
            } catch (e) {
                this.startError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },
    };
}
</script>
@endpush
@endsection
