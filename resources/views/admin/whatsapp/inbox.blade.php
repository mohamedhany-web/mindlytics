@extends('layouts.admin')

@section('title', 'محادثات الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $activeId = $activeConversation?->id;
    $inboxService = app(\App\Services\WhatsAppInboxService::class);
    $initialConversations = $conversations->getCollection()->map(fn ($c) => $inboxService->serializeConversation($c))->values();
    $initialMessages = $messages->map(fn ($m) => $inboxService->serializeMessage($m))->values();
    $inboxConfig = [
        'conversationId' => $activeId,
        'activeConversation' => $activeConversation ? $inboxService->serializeConversation($activeConversation) : null,
        'conversations' => $initialConversations,
        'messages' => $initialMessages,
        'pollUrl' => route('admin.whatsapp.inbox.poll'),
        'conversationUrlTemplate' => route('admin.whatsapp.inbox.conversation', ['conversation' => '__ID__']),
        'replyUrl' => $activeId ? route('admin.whatsapp.inbox.reply', $activeConversation) : null,
        'templateUrl' => $activeId ? route('admin.whatsapp.inbox.template', $activeConversation) : null,
        'startUrl' => route('admin.whatsapp.inbox.start'),
        'templatesUrl' => route('admin.whatsapp.inbox.templates'),
        'inboxUrl' => route('admin.whatsapp.inbox'),
        'csrf' => csrf_token(),
        'withinWindow' => (bool) ($withinWindow ?? false),
        'lastMessageId' => $messages->last()?->id ?? 0,
        'metaTemplates' => $metaTemplates ?? [],
        'crmReady' => (bool) ($crmReady ?? false),
    ];
@endphp

<script>window.__waInboxConfig = @json($inboxConfig);</script>

<div class="p-3 sm:p-4 md:p-6 space-y-4" style="background:#f8fafc; min-height:100vh;" x-data="whatsappInbox()" x-cloak>
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'المحادثات الواردة',
        'subtitle' => 'تنقّل بين المحادثات وردّ على العملاء مباشرة — بدون إعادة تحميل الصفحة.',
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
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-900 hidden lg:block">
            <strong>Webhook للرسائل الواردة:</strong> Callback URL <code class="dir-ltr">{{ \App\Support\WhatsAppCloudSettings::webhookUrl() }}</code>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 lg:gap-4 rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-white"
         style="height: calc(100vh - 13rem); min-height: 520px; max-height: 900px;">

        {{-- قائمة المحادثات --}}
        <aside class="lg:col-span-4 xl:col-span-3 flex flex-col border-l border-slate-200 bg-white h-full"
               :class="{ 'hidden lg:flex': conversationId && !showSidebarMobile, 'flex': !conversationId || showSidebarMobile }">
            <div class="p-3 border-b border-slate-200 bg-[#f0f2f5] shrink-0">
                <div class="flex gap-2">
                    <input type="search" x-model="searchQuery" @input.debounce.350ms="searchConversations()"
                           placeholder="بحث بالاسم أو الرقم..."
                           class="flex-1 rounded-full border-0 bg-white px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-emerald-400">
                    <button type="button" @click="searchConversations()" class="w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shrink-0 flex items-center justify-center">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </div>
                @if($crmReady ?? false)
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <select x-model="filterStatus" @change="applyFilters()" class="text-[10px] rounded-full border-0 bg-white px-2 py-1 shadow-sm">
                        <option value="">كل الحالات</option>
                        @foreach($crmStatuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select x-model="filterAssigned" @change="applyFilters()" class="text-[10px] rounded-full border-0 bg-white px-2 py-1 shadow-sm">
                        <option value="">كل الموظفين</option>
                        <option value="unassigned">غير معيّنة</option>
                        @foreach($crmAgents as $agent)
                            <option value="{{ $agent['id'] }}">{{ $agent['name'] }}</option>
                        @endforeach
                    </select>
                    <label class="inline-flex items-center gap-1 text-[10px] bg-white rounded-full px-2 py-1 shadow-sm cursor-pointer">
                        <input type="checkbox" x-model="filterMine" @change="applyFilters()" class="rounded border-slate-300 text-emerald-600">
                        محادثاتي
                    </label>
                </div>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain divide-y divide-slate-100 wa-conv-scroll">
                <template x-if="loadingList">
                    <div class="p-8 text-center text-slate-400 text-sm"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>
                </template>
                <template x-if="!loadingList && conversations.length === 0">
                    <div class="p-8 text-center text-slate-500 text-sm">
                        <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                        <p>لا توجد محادثات</p>
                    </div>
                </template>
                <template x-for="conv in conversations" :key="'c-' + conv.id">
                    <button type="button" @click="selectConversation(conv.id)"
                            class="w-full text-right px-4 py-3 hover:bg-emerald-50/70 transition-colors flex gap-3 items-start"
                            :class="conversationId === conv.id ? 'bg-emerald-50 border-r-4 border-emerald-500' : ''">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-sm"
                             x-text="(conv.display_name || '?').charAt(0).toUpperCase()"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-bold text-slate-900 truncate text-sm" x-text="conv.display_name"></p>
                                <span class="text-[10px] text-slate-400 shrink-0 whitespace-nowrap" x-text="conv.last_message_at_human || ''"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 dir-ltr text-right font-mono truncate" x-text="conv.formatted_phone"></p>
                            <div class="flex items-center gap-1 mt-0.5 flex-wrap" x-show="conv.crm">
                                <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600" x-text="conv.crm?.status_label"></span>
                                <span class="text-[9px] text-emerald-700" x-show="conv.crm?.assignee_name" x-text="conv.crm?.assignee_name"></span>
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-xs text-slate-600 truncate" x-text="conv.last_message_preview || '—'"></p>
                                <span x-show="conv.unread_count > 0"
                                      class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold shrink-0"
                                      x-text="conv.unread_count"></span>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </aside>

        {{-- نافذة المحادثة --}}
        <section class="lg:col-span-8 xl:col-span-6 flex flex-col h-full bg-[#efeae2] min-h-0"
                 :class="{ 'flex': conversationId, 'hidden lg:flex': !conversationId }">

            {{-- حالة: لا محادثة مختارة --}}
            <template x-if="!conversationId && !loadingConversation">
                <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5]">
                    <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                        <i class="fab fa-whatsapp text-5xl text-emerald-500"></i>
                    </div>
                    <p class="font-semibold text-slate-700 text-lg">محادثات الواتساب</p>
                    <p class="text-sm text-slate-500 mt-1 text-center max-w-sm">اختر محادثة من القائمة أو ابدأ محادثة جديدة بقالب Meta</p>
                    <button type="button" @click="showStartModal = true" class="{{ $waBtnPrimary }} mt-5 text-sm">اكتب رسالة جديدة</button>
                </div>
            </template>

            {{-- حالة: تحميل محادثة --}}
            <template x-if="loadingConversation">
                <div class="flex-1 flex items-center justify-center bg-[#efeae2]">
                    <div class="text-center text-slate-500">
                        <i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
                        <p class="text-sm">جاري تحميل المحادثة...</p>
                    </div>
                </div>
            </template>

            {{-- المحادثة النشطة --}}
            <template x-if="conversationId && activeConversation && !loadingConversation">
                <div class="flex flex-col h-full min-h-0">
                    {{-- رأس المحادثة --}}
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-3 bg-[#f0f2f5] shrink-0">
                        <button type="button" @click="backToList()" class="lg:hidden w-9 h-9 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold shrink-0"
                             x-text="(activeConversation.display_name || '?').charAt(0).toUpperCase()"></div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-900 truncate text-sm" x-text="activeConversation.display_name"></h3>
                            <p class="text-xs text-slate-500 dir-ltr truncate">
                                <span x-text="activeConversation.formatted_phone"></span>
                                <template x-if="activeConversation.user_name">
                                    <span> · <span x-text="activeConversation.user_name"></span></span>
                                </template>
                            </p>
                        </div>
                        <span x-show="withinWindow" class="hidden sm:inline-flex text-[10px] font-semibold px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i class="fas fa-clock ml-1"></i> 24 ساعة
                        </span>
                    </div>

                    {{-- الرسائل --}}
                    <div id="chat-messages" class="flex-1 overflow-y-auto overscroll-contain p-3 sm:p-4 space-y-2 min-h-0 wa-chat-scroll"
                         style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                        <template x-for="msg in chatMessages" :key="'m-' + msg.id">
                            <div class="flex" :class="msg.is_inbound ? 'justify-start' : 'justify-end'">
                                <div class="max-w-[88%] sm:max-w-[72%] rounded-lg px-3 py-2 shadow-sm text-sm whitespace-pre-wrap break-words relative"
                                     :class="msg.is_inbound
                                        ? 'bg-white text-slate-800 rounded-tl-none'
                                        : 'bg-[#d9fdd3] text-slate-900 rounded-tr-none'">
                                    <p x-text="msg.body"></p>
                                    <template x-if="msg.template_name">
                                        <p class="text-[10px] opacity-70 mt-1" x-text="'قالب: ' + msg.template_name"></p>
                                    </template>
                                    <template x-if="msg.error_message && msg.status === 'failed'">
                                        <p class="text-[10px] text-rose-600 mt-1" x-text="msg.error_message"></p>
                                    </template>
                                    <div class="flex items-center justify-end gap-1 mt-0.5 text-[10px] opacity-60">
                                        <span x-show="msg.sent_by && !msg.is_inbound" x-text="msg.sent_by"></span>
                                        <span x-text="msg.created_at_human"></span>
                                        <template x-if="!msg.is_inbound && msg.status === 'read'">
                                            <i class="fas fa-check-double text-sky-500"></i>
                                        </template>
                                        <template x-if="!msg.is_inbound && msg.status === 'delivered'">
                                            <i class="fas fa-check-double text-slate-400"></i>
                                        </template>
                                        <template x-if="!msg.is_inbound && msg.status === 'sent'">
                                            <i class="fas fa-check text-slate-400"></i>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- شريط الإرسال — مثل واتساب --}}
                    <div class="shrink-0 bg-[#f0f2f5] px-3 py-2 sm:px-4 sm:py-3 border-t border-slate-200">
                        <div x-show="replyError" x-cloak class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2 mb-2" x-text="replyError"></div>
                        <p x-show="!withinWindow" x-cloak class="text-[10px] text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5 mb-2">
                            إذا لم يرد العميل خلال 24 ساعة، قد يرفض Meta الرسالة النصية — جرّب الإرسال أو استخدم قالباً من الأسفل.
                        </p>

                        <div class="flex items-end gap-2">
                            <div class="flex-1 flex items-end gap-2 bg-white rounded-3xl px-3 py-1.5 shadow-sm border border-slate-100 min-h-[48px]">
                                <textarea x-ref="composer" x-model="replyBody" rows="1" placeholder="اكتب رسالة..."
                                          @input="autoGrowComposer()"
                                          @keydown.enter="if(!$event.shiftKey){ $event.preventDefault(); sendReply(); }"
                                          class="flex-1 border-0 bg-transparent text-sm resize-none py-2 px-1 max-h-32 focus:ring-0 focus:outline-none placeholder:text-slate-400"></textarea>
                            </div>
                            <button type="button" @click="sendReply()"
                                    :disabled="sending || !replyBody.trim()"
                                    class="w-12 h-12 rounded-full bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white flex items-center justify-center shrink-0 shadow-md transition-all active:scale-95">
                                <i class="fas text-lg" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                            </button>
                        </div>

                        <div x-show="showTemplatePicker" x-cloak class="mt-2 flex gap-2">
                            <select x-model="selectedTemplateKey" @change="applySelectedTemplate()"
                                    class="flex-1 rounded-full bg-white px-3 py-1.5 text-xs shadow-sm border-0">
                                <option value="">قالب Meta...</option>
                                <template x-for="t in metaTemplates" :key="'tp-' + t.name + t.language">
                                    <option :value="t.name + '|' + t.language" x-text="t.label"></option>
                                </template>
                            </select>
                            <button type="button" @click="sendTemplate()" :disabled="sending || !templateName"
                                    class="text-xs px-3 py-1.5 rounded-full bg-white text-emerald-700 font-semibold shadow-sm">إرسال قالب</button>
                        </div>
                        <button type="button" @click="showTemplatePicker = !showTemplatePicker"
                                class="mt-1.5 text-[10px] text-slate-500 hover:text-emerald-600 px-1">
                            <i class="fas fa-file-alt"></i> <span x-text="showTemplatePicker ? 'إخفاء القوالب' : 'إرسال بقالب Meta (اختياري)'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </section>

        @include('admin.whatsapp._crm_panel')
    </div>

    {{-- modal محادثة جديدة --}}
    <div x-show="showStartModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @keydown.escape.window="showStartModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4" @click.outside="showStartModal = false">
            <h3 class="font-bold text-lg text-slate-900">محادثة جديدة</h3>
            <p class="text-xs text-slate-600">أدخل رقم الواتساب واكتب رسالتك ثم اضغط إرسال.</p>
            <div>
                <label class="{{ $waLabelClass }}">رقم الواتساب</label>
                <input type="text" x-model="startPhone" placeholder="2010xxxxxxx" class="{{ $waInputClass }} dir-ltr text-sm">
            </div>
            <div>
                <label class="{{ $waLabelClass }}">الرسالة</label>
                <textarea x-model="startBody" rows="3" placeholder="اكتب رسالتك هنا..."
                          class="{{ $waInputClass }} text-sm resize-none"></textarea>
            </div>
            <div x-show="showStartTemplatePicker" x-cloak class="space-y-2 pt-1 border-t border-slate-100">
                <label class="{{ $waLabelClass }}">أو أرسل بقالب Meta</label>
                @if(count($metaTemplates ?? []) > 0)
                    <select x-model="selectedTemplateKey" @change="applySelectedTemplate(true)" class="{{ $waSelectClass }} text-sm dir-ltr">
                        @foreach($metaTemplates as $tpl)
                            <option value="{{ $tpl['name'] }}|{{ $tpl['language'] }}">{{ $tpl['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <p class="text-xs text-slate-500">لا توجد قوالب معتمدة.</p>
                @endif
            </div>
            <button type="button" @click="showStartTemplatePicker = !showStartTemplatePicker"
                    class="text-[11px] text-slate-500 hover:text-emerald-600">
                <i class="fas fa-file-alt"></i> <span x-text="showStartTemplatePicker ? 'إخفاء القوالب' : 'إرسال بقالب بدلاً من ذلك'"></span>
            </button>
            <p x-show="startError" class="text-xs text-rose-600" x-text="startError"></p>
            <div class="flex gap-2 justify-end">
                <button type="button" @click="showStartModal = false" class="{{ $waBtnSecondary }} text-sm">إلغاء</button>
                <button type="button" @click="startConversation()" :disabled="sending" class="{{ $waBtnPrimary }} text-sm">
                    <i class="fas fa-paper-plane ml-1"></i> إرسال
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .wa-conv-scroll::-webkit-scrollbar,
    .wa-chat-scroll::-webkit-scrollbar { width: 6px; }
    .wa-conv-scroll::-webkit-scrollbar-thumb,
    .wa-chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
function whatsappInbox() {
    const cfg = window.__waInboxConfig || {};

    return {
        conversationId: cfg.conversationId || null,
        activeConversation: cfg.activeConversation || null,
        conversations: cfg.conversations || [],
        chatMessages: cfg.messages || [],
        pollUrl: cfg.pollUrl,
        conversationUrlTemplate: cfg.conversationUrlTemplate,
        replyUrl: cfg.replyUrl,
        templateUrl: cfg.templateUrl,
        startUrl: cfg.startUrl,
        templatesUrl: cfg.templatesUrl,
        inboxUrl: cfg.inboxUrl,
        csrf: cfg.csrf,
        withinWindow: !!cfg.withinWindow,
        lastMessageId: cfg.lastMessageId || 0,
        unreadTotal: {{ (int) $unreadTotal }},
        metaTemplates: cfg.metaTemplates || [],
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        replyBody: '',
        templateName: '',
        templateLang: '',
        selectedTemplateKey: '',
        startPhone: '',
        startBody: '',
        startTemplate: '',
        startLang: '',
        replyError: '',
        startError: '',
        sending: false,
        loadingConversation: false,
        loadingList: false,
        showStartModal: false,
        showStartTemplatePicker: false,
        showSidebarMobile: false,
        showTemplatePicker: false,
        pollTimer: null,
        searchTimer: null,
        crmReady: !!cfg.crmReady,
        crmUrls: {},
        crmNotes: [],
        crmTimeline: [],
        crmStatus: '',
        crmAssignee: '',
        crmAssigneePending: '',
        transferReason: '',
        transferReasonOpen: false,
        noteBody: '',
        crmSaving: false,
        crmError: '',
        filterStatus: new URLSearchParams(window.location.search).get('status') || '',
        filterAssigned: new URLSearchParams(window.location.search).get('assigned_to') || '',
        filterMine: new URLSearchParams(window.location.search).get('mine') === '1',

        init() {
            this.bootstrapTemplates();
            this.scrollChat(false);
            this.pollTimer = setInterval(() => this.poll(), 6000);

            const urlConv = new URLSearchParams(window.location.search).get('conversation');
            if (urlConv && parseInt(urlConv, 10) !== this.conversationId) {
                this.selectConversation(parseInt(urlConv, 10), false);
            } else if (this.conversationId) {
                this.pushUrl(this.conversationId);
            }

            window.addEventListener('popstate', (e) => {
                const id = e.state?.conversationId || null;
                if (id) {
                    this.selectConversation(id, false);
                } else {
                    this.conversationId = null;
                    this.activeConversation = null;
                    this.chatMessages = [];
                    this.showSidebarMobile = true;
                }
            });
        },

        conversationUrl(id) {
            return (this.conversationUrlTemplate || '').replace('__ID__', id);
        },

        pushUrl(id) {
            const params = new URLSearchParams();
            if (id) params.set('conversation', id);
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.filterAssigned) params.set('assigned_to', this.filterAssigned);
            if (this.filterMine) params.set('mine', '1');
            const qs = params.toString();
            const url = this.inboxUrl + (qs ? '?' + qs : '');
            window.history.pushState({ conversationId: id }, '', url);
        },

        filterParams() {
            const params = new URLSearchParams();
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.filterAssigned) params.set('assigned_to', this.filterAssigned);
            if (this.filterMine) params.set('mine', '1');
            return params;
        },

        applyFilters() {
            this.pushUrl(this.conversationId);
            this.searchConversations();
        },

        syncCrmFromConversation(conv) {
            if (!conv?.crm) return;
            this.crmStatus = conv.crm.status || 'open';
            this.crmAssignee = conv.crm.assigned_to ? String(conv.crm.assigned_to) : '';
            this.crmAssigneePending = this.crmAssignee;
            this.transferReasonOpen = false;
            this.transferReason = '';
        },

        hasTag(tagId) {
            const tags = this.activeConversation?.crm?.tags || [];
            return tags.some(t => t.id === tagId);
        },

        async crmPost(url, body = {}) {
            this.crmSaving = true;
            this.crmError = '';
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (!data.success) {
                    this.crmError = data.error || data.message || 'فشلت العملية';
                    return null;
                }
                if (data.conversation) {
                    this.activeConversation = data.conversation;
                    this.syncCrmFromConversation(data.conversation);
                    this.upsertConversation(data.conversation);
                }
                if (Array.isArray(data.timeline)) this.crmTimeline = data.timeline;
                return data;
            } catch (_) {
                this.crmError = 'خطأ في الاتصال';
                return null;
            } finally {
                this.crmSaving = false;
            }
        },

        async updateCrmStatus() {
            if (!this.crmUrls.status || !this.crmStatus) return;
            await this.crmPost(this.crmUrls.status, { status: this.crmStatus });
        },

        transferConversation() {
            if (!this.crmUrls.transfer) return;
            if (String(this.crmAssignee) === String(this.crmAssigneePending)) return;
            this.transferReasonOpen = true;
        },

        async confirmTransfer() {
            if (!this.crmUrls.transfer) return;
            const data = await this.crmPost(this.crmUrls.transfer, {
                assigned_to: parseInt(this.crmAssignee, 10),
                reason: this.transferReason || null,
            });
            if (data) {
                this.crmAssigneePending = this.crmAssignee;
                this.transferReasonOpen = false;
                this.transferReason = '';
            }
        },

        async toggleTag(tagId) {
            if (!this.crmUrls.tag) return;
            const attach = !this.hasTag(tagId);
            const url = this.crmUrls.tag + '/' + tagId;
            await this.crmPost(url, { attach });
        },

        async addNote() {
            if (!this.crmUrls.notes || !this.noteBody.trim()) return;
            const data = await this.crmPost(this.crmUrls.notes, { body: this.noteBody.trim() });
            if (data?.note) {
                this.crmNotes.unshift(data.note);
                this.noteBody = '';
            }
        },

        backToList() {
            this.conversationId = null;
            this.activeConversation = null;
            this.chatMessages = [];
            this.showSidebarMobile = true;
            this.pushUrl(null);
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
            this.templateName = parts[0] || '';
            this.templateLang = parts[1] || 'en_US';
            if (forStart) {
                this.startTemplate = this.templateName;
                this.startLang = this.templateLang;
            }
        },

        autoGrowComposer() {
            const el = this.$refs.composer;
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 128) + 'px';
        },

        scrollChat(smooth = true) {
            this.$nextTick(() => {
                const el = document.getElementById('chat-messages');
                if (!el) return;
                el.scrollTo({ top: el.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
            });
        },

        upsertConversation(conv) {
            if (!conv?.id) return;
            const idx = this.conversations.findIndex(c => c.id === conv.id);
            if (idx >= 0) {
                this.conversations[idx] = { ...this.conversations[idx], ...conv };
                this.conversations.sort((a, b) => {
                    const ta = a.last_message_at || '';
                    const tb = b.last_message_at || '';
                    return tb.localeCompare(ta);
                });
            } else {
                this.conversations.unshift(conv);
            }
        },

        async searchConversations() {
            this.loadingList = true;
            try {
                const params = this.filterParams();
                const res = await fetch(this.pollUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (data.success && Array.isArray(data.conversations)) {
                    this.conversations = data.conversations;
                    this.unreadTotal = data.unread_total ?? this.unreadTotal;
                }
            } catch (_) {}
            finally { this.loadingList = false; }
        },

        async selectConversation(id, updateUrl = true) {
            if (!id || this.loadingConversation) return;
            if (id === this.conversationId && this.chatMessages.length > 0) {
                this.showSidebarMobile = false;
                return;
            }

            const isSwitch = id !== this.conversationId;
            this.loadingConversation = true;
            this.replyError = '';
            this.replyBody = '';
            this.showSidebarMobile = false;
            if (isSwitch) {
                this.conversationId = id;
                this.chatMessages = [];
                this.activeConversation = null;
            }

            try {
                const res = await fetch(this.conversationUrl(id), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.success) {
                    this.replyError = data.error || 'تعذّر تحميل المحادثة';
                    return;
                }

                this.conversationId = id;
                this.activeConversation = data.conversation;
                this.chatMessages = data.messages || [];
                this.withinWindow = !!data.within_service_window;
                this.replyUrl = data.reply_url;
                this.templateUrl = data.template_url;
                this.crmUrls = data.crm_urls || {};
                this.crmNotes = data.notes || [];
                this.crmTimeline = data.timeline || [];
                this.syncCrmFromConversation(data.conversation);
                this.lastMessageId = this.chatMessages.length ? this.chatMessages[this.chatMessages.length - 1].id : 0;
                this.unreadTotal = data.unread_total ?? this.unreadTotal;

                if (data.conversation) {
                    data.conversation.unread_count = 0;
                    this.upsertConversation(data.conversation);
                }

                if (updateUrl) this.pushUrl(id);
                this.scrollChat(false);
                this.$nextTick(() => this.autoGrowComposer());
            } catch (_) {
                this.replyError = 'خطأ في تحميل المحادثة';
            } finally {
                this.loadingConversation = false;
            }
        },

        async poll() {
            if (!this.pollUrl) return;
            try {
                const params = this.filterParams();
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

                if (Array.isArray(data.conversations)) {
                    data.conversations.forEach(c => this.upsertConversation(c));
                }

                if (data.within_service_window !== undefined) {
                    this.withinWindow = !!data.within_service_window;
                }

                if (Array.isArray(data.messages) && data.messages.length) {
                    const existingIds = new Set(this.chatMessages.map(m => m.id));
                    data.messages.forEach(m => {
                        if (!existingIds.has(m.id)) {
                            this.chatMessages.push(m);
                            this.lastMessageId = Math.max(this.lastMessageId, m.id);
                        }
                    });
                    this.scrollChat();
                }
            } catch (_) {}
        },

        async sendReply() {
            if (!this.replyUrl || !this.replyBody.trim() || this.sending) return;
            this.sending = true;
            this.replyError = '';
            const body = this.replyBody.trim();
            this.replyBody = '';
            this.$nextTick(() => this.autoGrowComposer());

            try {
                const res = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ body }),
                });
                const data = await res.json();
                if (!data.success) {
                    this.replyBody = body;
                    this.replyError = data.error || 'فشل الإرسال';
                    if (data.requires_template) this.showTemplatePicker = true;
                    return;
                }
                this.chatMessages.push(data.message);
                this.lastMessageId = data.message.id;
                if (data.conversation) this.upsertConversation(data.conversation);
                this.scrollChat();
            } catch (_) {
                this.replyBody = body;
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async sendTemplate() {
            if (!this.templateUrl || !this.templateName.trim() || this.sending) return;
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
                this.chatMessages.push(data.message);
                this.lastMessageId = data.message.id;
                if (data.conversation) this.upsertConversation(data.conversation);
                this.withinWindow = true;
                this.scrollChat();
            } catch (_) {
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async startConversation() {
            if (!this.startUrl || !this.startPhone.trim()) {
                this.startError = 'أدخل رقم الواتساب';
                return;
            }

            const body = this.startBody.trim();
            const useTemplate = this.showStartTemplatePicker && this.startTemplate.trim();

            if (!body && !useTemplate) {
                this.startError = 'اكتب رسالة أو اختر قالباً';
                return;
            }

            if (useTemplate && !body) {
                this.applySelectedTemplate(true);
            }

            this.sending = true;
            this.startError = '';
            try {
                const payload = { phone: this.startPhone };
                if (body) {
                    payload.body = body;
                } else if (useTemplate) {
                    payload.template_name = this.startTemplate;
                    payload.language_code = this.startLang || 'en_US';
                }

                const res = await fetch(this.startUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!data.success) {
                    this.startError = data.error || 'فشل الإرسال';
                    if (data.requires_template) this.showStartTemplatePicker = true;
                    return;
                }
                this.showStartModal = false;
                this.startPhone = '';
                this.startBody = '';
                if (data.conversation?.id) {
                    this.upsertConversation(data.conversation);
                    await this.selectConversation(data.conversation.id);
                }
            } catch (_) {
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
