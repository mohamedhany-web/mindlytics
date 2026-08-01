@extends('layouts.admin')

@section('title', 'محادثات Messenger & Instagram')
@section('header', 'Inbox — السوشيال')

@section('content')
@php
    $convId = $activeConversation?->id;
    $replyUrl = $convId ? route('admin.meta-social.inbox.reply', $convId) : '';
    $pollUrl = route('admin.meta-social.inbox.poll', ['page' => $pageId, 'conversation' => $convId]);
    $canUse = (bool) ($connected ?? ($connectionMeta['can_use'] ?? false));
    $crmReady = (bool) ($crmReady ?? false);
    $crmUrls = $convId ? [
        'assign' => route('admin.meta-social.inbox.assign', $convId),
        'contact' => route('admin.meta-social.inbox.contact', $convId),
        'createLead' => route('admin.meta-social.inbox.create-lead', $convId),
        'linkLead' => route('admin.meta-social.inbox.link-lead', $convId),
        'enrich' => route('admin.meta-social.inbox.enrich', $convId),
        'syncMessages' => route('admin.meta-social.inbox.sync-messages', $convId),
    ] : [];
@endphp

<div class="wa-inbox-page flex flex-col min-h-0 overflow-hidden gap-2 wa-inbox-immersive admin-wa-inbox sm-meta-inbox" x-data="metaSocialInbox()" x-cloak>
    @include('admin.meta-social._alerts')

    {{-- شريط علوي --}}
    <div class="shrink-0 flex flex-wrap items-center justify-between gap-2 px-2 sm:px-3 pt-2">
        <div class="flex items-center gap-3 min-w-0">
            <button type="button" @click="$dispatch('open-sidebar')" class="lg:hidden w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 flex items-center justify-center shrink-0">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-sky-600 flex items-center justify-center shrink-0">
                <i class="fab fa-meta text-sm"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-black text-slate-900 truncate">{{ $waInboxTitle ?? 'محادثات السوشيال' }}</h2>
                <p class="text-[11px] text-slate-500 truncate hidden sm:block">{{ $waInboxSubtitle ?? 'Messenger · Instagram' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
            <a href="{{ $waAdminSettingsUrl ?? route('admin.meta-social.settings') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-plug text-sky-600"></i>
                <span class="hidden sm:inline">ربط Meta</span>
            </a>
            <a href="{{ $waAdminPagesUrl ?? route('admin.meta-social.pages.index') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors hidden md:inline-flex">
                <i class="fab fa-facebook text-sky-500"></i>
                <span>الصفحات</span>
            </a>
            <a href="{{ $waAdminDashboardUrl ?? route('admin.meta-social.index') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors hidden md:inline-flex">
                <i class="fas fa-tachometer-alt text-slate-500"></i>
                <span>لوحة السوشيال</span>
            </a>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white border border-slate-200 text-slate-700">
                غير مقروء: {{ $unreadTotal }}
            </span>
        </div>
    </div>

    @if(! $tablesReady)
        <div class="shrink-0 rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 mx-2">
            <p class="font-bold">تشغيل الترحيل مطلوب</p>
            <p class="mt-1">نفّذ: <code class="bg-white px-2 py-0.5 rounded">php artisan migrate --force</code></p>
        </div>
    @elseif(! $canUse)
        <div class="shrink-0 mx-2">
            <a href="{{ route('admin.meta-social.settings') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 hover:bg-amber-100 transition-colors">
                <i class="fas fa-exclamation-triangle text-amber-600 shrink-0"></i>
                <span class="font-semibold">Meta غير مربوط — <span class="underline">إعداد الربط</span></span>
            </a>
        </div>
    @endif

    <div class="wa-inbox-shell flex-1 min-h-0 overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">
        {{-- قائمة المحادثات --}}
        <aside class="wa-conv-sidebar wa-inbox-col border-s border-slate-200/80 bg-[#f0f2f5]"
               :class="(conversationId && !showSidebarMobile) ? 'max-lg:hidden' : ''">
            <div class="wa-conv-header shrink-0 border-b border-slate-200/80 bg-[#f0f2f5]">
                <div class="flex items-center justify-between gap-2 px-3 pt-2.5 pb-1">
                    <h3 class="text-sm font-black text-slate-800">المحادثات</h3>
                    <span class="text-[10px] font-bold text-slate-500 bg-white/80 border border-slate-200/60 rounded-full px-2 py-0.5 tabular-nums">{{ $conversations->count() }} محادثة</span>
                </div>
                <div class="px-2.5 pb-2.5 space-y-1.5">
                    @if($pages->isNotEmpty())
                    <select class="w-full text-xs rounded-xl border-0 bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200/60 focus:ring-2 focus:ring-sky-400 focus:outline-none"
                            onchange="metaInboxNav({page: this.value})">
                        <option value="">كل الصفحات</option>
                        @foreach($pages as $p)
                            <option value="{{ $p->id }}" @selected($pageId == $p->id)>{{ $p->page_name }}</option>
                        @endforeach
                    </select>
                    @endif
                    <div class="grid grid-cols-2 gap-1.5">
                        <select class="w-full text-[11px] rounded-xl border-0 bg-white px-2 py-2 shadow-sm ring-1 ring-slate-200/60"
                                onchange="metaInboxNav({platform: this.value})">
                            <option value="">كل المنصات</option>
                            <option value="messenger" @selected(($platformFilter ?? '') === 'messenger')>Messenger</option>
                            <option value="instagram" @selected(($platformFilter ?? '') === 'instagram')>Instagram</option>
                        </select>
                        <select class="w-full text-[11px] rounded-xl border-0 bg-white px-2 py-2 shadow-sm ring-1 ring-slate-200/60"
                                onchange="metaInboxNav({assigned_to: this.value})">
                            <option value="">كل الموظفين</option>
                            <option value="unassigned" @selected(($assignedFilter ?? '') === 'unassigned')>غير معيّن</option>
                            @foreach(($agents ?? []) as $agent)
                                <option value="{{ $agent->id }}" @selected((string) ($assignedFilter ?? '') === (string) $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="wa-conv-list">
                @forelse($conversations as $c)
                    <a href="{{ route('admin.meta-social.inbox.index', array_filter(['page' => $pageId ?: null, 'conversation' => $c->id, 'platform' => $platformFilter ?: null, 'assigned_to' => $assignedFilter ?: null])) }}"
                       class="wa-conv-item block w-full text-right px-3 py-3 flex gap-3 items-center border-b border-slate-100/80 transition-colors hover:bg-[#f5f6f6] {{ $convId == $c->id ? 'wa-conv-item--active' : '' }}">
                        <div class="min-w-0 flex-1 order-2">
                            <div class="flex items-baseline justify-between gap-2 mb-0.5">
                                <p class="font-bold text-slate-900 truncate text-[13px] leading-tight">{{ $c->displayName() }}</p>
                                <span class="text-[10px] text-slate-400 shrink-0 whitespace-nowrap tabular-nums">{{ $c->last_message_at?->diffForHumans(null, true) ?? '' }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 truncate mb-1">
                                {{ $c->platformLabel() }} · {{ $c->page?->page_name }}
                                @if($c->assignee)
                                    · <span class="text-sky-700 font-semibold">{{ $c->assignee->name }}</span>
                                @endif
                            </p>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[12px] text-slate-500 truncate leading-snug flex-1">{{ $c->last_message_preview ?: '—' }}</p>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($c->sales_lead_id)
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">CRM</span>
                                    @endif
                                    @if($c->unread_count > 0)
                                        <span class="inline-flex items-center justify-center min-w-[1.125rem] h-[1.125rem] px-1 rounded-full bg-sky-500 text-white text-[10px] font-bold shadow-sm">{{ $c->unread_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full shrink-0 order-1 flex items-center justify-center font-bold text-sm shadow-sm overflow-hidden {{ $convId == $c->id ? 'bg-sky-500 text-white' : 'bg-sky-100 text-sky-700' }}">
                            @if($c->participant_profile_pic)
                                <img src="{{ $c->participant_profile_pic }}" alt="" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($c->displayName(), 0, 1) }}
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center text-slate-500 text-sm">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                            <i class="fab fa-meta text-2xl text-slate-300"></i>
                        </div>
                        <p class="font-semibold text-slate-700">لا توجد محادثات</p>
                        <p class="text-xs text-slate-400 mt-1">فعّل صفحة واضغط «مزامنة محادثات» من قسم الصفحات</p>
                        <a href="{{ route('admin.meta-social.pages.index') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-sky-700 hover:text-sky-900">
                            <i class="fab fa-facebook"></i> إدارة الصفحات
                        </a>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- نافذة المحادثة --}}
        <section class="wa-inbox-col wa-chat-panel overflow-hidden bg-[#efeae2] {{ $convId ? '' : 'max-lg:hidden' }}">
            @if($activeConversation)
                <div class="wa-chat-active">
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-3 bg-[#f0f2f5] shrink-0">
                        <button type="button" @click="showSidebarMobile = true" class="lg:hidden w-9 h-9 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold shrink-0">
                            {{ mb_substr($activeConversation->displayName(), 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-900 truncate text-sm" x-text="crm?.display_name || @js($activeConversation->displayName())">{{ $activeConversation->displayName() }}</h3>
                            <p class="text-xs text-slate-500 truncate">
                                {{ $activeConversation->platformLabel() }} · {{ $activeConversation->page?->page_name }}
                                @if($activeConversation->assignee)
                                    · <span class="text-sky-700 font-semibold">{{ $activeConversation->assignee->name }}</span>
                                @endif
                                @if($activeConversation->phone)
                                    · <span dir="ltr">{{ $activeConversation->phone }}</span>
                                @endif
                            </p>
                        </div>
                        @if($activeConversation->platform === 'instagram')
                            <span class="hidden sm:inline-flex text-[10px] font-semibold px-2 py-1 rounded-full bg-pink-100 text-pink-800 border border-pink-200">
                                <i class="fab fa-instagram ml-1"></i> Instagram
                            </span>
                        @else
                            <span class="hidden sm:inline-flex text-[10px] font-semibold px-2 py-1 rounded-full bg-sky-100 text-sky-800 border border-sky-200">
                                <i class="fab fa-facebook-messenger ml-1"></i> Messenger
                            </span>
                        @endif
                        <button type="button" @click="syncAllMessages()"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-sky-50 hover:border-sky-200 transition-colors"
                                :disabled="syncingMessages" title="جلب كل الرسائل من Meta">
                            <i class="fas fa-cloud-download-alt" :class="syncingMessages ? 'fa-spinner fa-spin' : ''"></i>
                            <span class="hidden sm:inline">جلب كل الرسائل</span>
                        </button>
                    </div>

                    <div id="sm-chat-messages" class="wa-chat-messages p-3 sm:p-4">
                        @forelse($messages as $m)
                            <div class="flex items-end gap-1 mb-1 {{ $m->direction === 'inbound' ? 'justify-start' : 'justify-end' }}">
                                <div class="relative max-w-[min(85%,18rem)] w-fit">
                                    <div class="wa-msg-bubble {{ $m->direction === 'inbound' ? 'wa-msg-bubble--in' : 'wa-msg-bubble--out' }}">
                                        @if($m->attachment_url && $m->message_type === 'image')
                                            <img src="{{ $m->attachment_url }}" alt="" class="rounded-lg max-w-full max-h-52 object-cover mb-1">
                                        @endif
                                        <p class="wa-msg-text">{{ $m->displayBody() }}</p>
                                        <span class="wa-msg-meta">
                                            @if($m->direction === 'outbound' && $m->sentBy)
                                                <span>{{ $m->sentBy->name }}</span>
                                            @endif
                                            <span>{{ $m->sent_at?->format('H:i') ?? $m->created_at?->format('H:i') }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-16 text-slate-500 text-sm">
                                <i class="fas fa-comments text-3xl text-slate-300 mb-2"></i>
                                <p>لا رسائل بعد — ابدأ المحادثة من الأسفل</p>
                            </div>
                        @endforelse
                    </div>

                    <form class="wa-chat-composer px-3 py-2.5 border-t border-slate-200 bg-[#f0f2f5] flex gap-2 items-end" @submit.prevent="sendReply()">
                        <input type="text" x-model="replyBody" placeholder="اكتب رسالة..." autocomplete="off"
                               class="flex-1 rounded-3xl border-0 bg-white px-4 py-2.5 text-sm shadow-sm ring-1 ring-slate-200/60 focus:ring-2 focus:ring-sky-400 focus:outline-none"
                               :disabled="sending">
                        <button type="submit"
                                class="w-11 h-11 rounded-full bg-sky-600 hover:bg-sky-700 text-white flex items-center justify-center shrink-0 shadow-md transition-colors disabled:opacity-50"
                                :disabled="sending || !replyBody.trim()">
                            <i class="fas fa-paper-plane text-sm" :class="sending ? 'fa-spinner fa-spin' : ''"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="wa-chat-panel__pane flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5] h-full">
                    <div class="w-24 h-24 rounded-full bg-sky-100 flex items-center justify-center mb-4">
                        <i class="fab fa-meta text-5xl text-sky-500"></i>
                    </div>
                    <p class="font-semibold text-slate-700 text-lg">محادثات السوشيال</p>
                    <p class="text-sm text-slate-500 mt-1 text-center max-w-sm">اختر محادثة من القائمة — Messenger أو Instagram</p>
                    <a href="{{ route('admin.meta-social.pages.index') }}" class="{{ $smBtnPrimary }} mt-5 text-sm">
                        <i class="fab fa-facebook"></i> إدارة الصفحات
                    </a>
                </div>
            @endif
        </section>

        @include('admin.meta-social._crm_panel')
    </div>
</div>

@push('styles')
<style>
    main:has(.wa-inbox-page) { overflow: hidden !important; }
    main:has(.wa-inbox-page) > div:last-child {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        padding-bottom: 0.75rem !important;
    }
    .wa-inbox-page {
        display: flex;
        flex-direction: column;
        height: calc(100dvh - 4.25rem);
        max-height: calc(100dvh - 4.25rem);
        min-height: 0;
        overflow: hidden;
    }
    body.wa-immersive-inbox .wa-inbox-page.wa-inbox-immersive {
        height: 100dvh;
        max-height: 100dvh;
        gap: 0.375rem;
        padding: 0.25rem 0.375rem 0.375rem;
    }
    @media (min-width: 1024px) {
        body.wa-immersive-inbox .wa-inbox-page.wa-inbox-immersive {
            padding: 0.375rem 0.5rem 0.5rem;
        }
    }
    body.wa-immersive-inbox main:has(.wa-inbox-page) > div:last-child {
        padding-bottom: 0 !important;
    }
    .sm-meta-inbox .wa-inbox-shell {
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: minmax(0, 1fr);
        flex: 1 1 auto;
        min-height: 0;
        max-height: 100%;
        overflow: hidden;
    }
    @media (min-width: 1024px) {
        .sm-meta-inbox .wa-inbox-shell {
            grid-template-columns: minmax(260px, 300px) minmax(0, 1fr) minmax(260px, 300px);
        }
    }
    .sm-crm-sidebar {
        display: none;
    }
    @media (min-width: 1024px) {
        .sm-crm-sidebar { display: flex; }
    }
    .wa-inbox-col { min-height: 0; min-width: 0; max-height: 100%; overflow: hidden; }
    .wa-conv-sidebar {
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
        max-height: 100%;
        min-height: 0;
        overflow: hidden;
    }
    .wa-conv-list {
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        background: #fff;
        scrollbar-width: thin;
    }
    .wa-chat-panel { display: grid; grid-template-rows: minmax(0, 1fr); min-height: 0; max-height: 100%; overflow: hidden; }
    .wa-chat-active { display: grid; grid-template-rows: auto minmax(0, 1fr) auto; min-height: 0; max-height: 100%; overflow: hidden; }
    .wa-chat-messages {
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }
    .wa-conv-item { position: relative; text-decoration: none; color: inherit; }
    .wa-conv-item--active { background-color: #f0f2f5 !important; }
    .sm-meta-inbox .wa-conv-item--active::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #0ea5e9;
        border-radius: 0 4px 4px 0;
    }
    .wa-msg-bubble {
        position: relative;
        display: inline-block;
        max-width: 100%;
        padding: 4px 8px 5px;
        border-radius: 7.5px;
        font-size: 14px;
        line-height: 1.35;
        box-shadow: 0 1px 0.5px rgba(11, 20, 26, 0.13);
        word-break: break-word;
    }
    .wa-msg-bubble--in { background: #fff; color: #111b21; border-top-left-radius: 0; }
    .wa-msg-bubble--out { background: #dbeafe; color: #111b21; border-top-right-radius: 0; }
    .wa-msg-text { white-space: pre-wrap; }
    .wa-msg-meta { display: flex; gap: 4px; justify-content: flex-end; align-items: center; font-size: 10px; color: #667781; margin-top: 2px; }
    @media (max-width: 1023px) {
        .sm-meta-inbox .wa-inbox-shell { position: relative; }
        .sm-meta-inbox .wa-conv-sidebar,
        .sm-meta-inbox .wa-chat-panel { grid-column: 1; grid-row: 1; }
        .sm-meta-inbox .wa-chat-panel { z-index: 2; }
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
function metaInboxNav(patch) {
    const params = new URLSearchParams(window.location.search);
    Object.entries(patch || {}).forEach(([k, v]) => {
        if (v === undefined || v === null || v === '') params.delete(k);
        else params.set(k, v);
    });
    params.delete('conversation');
    const qs = params.toString();
    window.location = @json(route('admin.meta-social.inbox.index')) + (qs ? ('?' + qs) : '');
}

function metaSocialInbox() {
    return {
        conversationId: {{ $convId ?: 'null' }},
        replyBody: '',
        sending: false,
        showSidebarMobile: false,
        replyUrl: @json($replyUrl),
        pollUrl: @json($pollUrl),
        csrf: @json(csrf_token()),
        lastMessageCount: {{ $messages->count() }},
        lastMessageId: {{ $messages->last()?->id ?: 0 }},
        crm: @json($crmPayload),
        crmUrls: @json($crmUrls),
        contactName: @json($crmPayload['display_name'] ?? $activeConversation?->displayName() ?? ''),
        contactPhone: @json($crmPayload['phone'] ?? $activeConversation?->phone ?? ''),
        contactEmail: @json($crmPayload['email'] ?? $activeConversation?->email ?? ''),
        contactNotes: @json($crmPayload['notes'] ?? $activeConversation?->notes ?? ''),
        assigneeId: @json($crmPayload['assigned_to'] ? (string) $crmPayload['assigned_to'] : ''),
        linkLeadId: '',
        crmSaving: false,
        crmError: '',
        crmOk: '',
        syncingMessages: false,
        init() {
            const el = document.getElementById('sm-chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
            if (this.conversationId) {
                setInterval(() => this.poll(), 8000);
            }
        },
        applyCrm(crm) {
            if (!crm) return;
            this.crm = crm;
            this.contactName = crm.display_name || '';
            this.contactPhone = crm.phone || '';
            this.contactEmail = crm.email || '';
            this.contactNotes = crm.notes || '';
            this.assigneeId = crm.assigned_to ? String(crm.assigned_to) : '';
        },
        async postJson(url, body) {
            this.crmSaving = true;
            this.crmError = '';
            this.crmOk = '';
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify(body || {}),
                });
                const data = await res.json();
                if (!data.success) {
                    this.crmError = data.error || 'فشلت العملية';
                    return null;
                }
                if (data.crm) this.applyCrm(data.crm);
                this.crmOk = 'تم الحفظ';
                return data;
            } catch (e) {
                this.crmError = 'خطأ في الاتصال';
                return null;
            } finally {
                this.crmSaving = false;
            }
        },
        async saveContact() {
            if (!this.crmUrls.contact) return;
            await this.postJson(this.crmUrls.contact, {
                name: this.contactName,
                phone: this.contactPhone,
                email: this.contactEmail,
                notes: this.contactNotes,
            });
        },
        async assignAgent() {
            if (!this.crmUrls.assign || !this.assigneeId) return;
            await this.postJson(this.crmUrls.assign, { assigned_to: Number(this.assigneeId) });
        },
        async createLead() {
            if (!this.crmUrls.createLead) return;
            const data = await this.postJson(this.crmUrls.createLead, {
                name: this.contactName,
                phone: this.contactPhone,
                email: this.contactEmail,
                assigned_to: this.assigneeId ? Number(this.assigneeId) : null,
            });
            if (data?.lead_id) this.crmOk = 'تم إنشاء Lead #' + data.lead_id;
        },
        async linkLead() {
            if (!this.crmUrls.linkLead || !this.linkLeadId) return;
            await this.postJson(this.crmUrls.linkLead, { sales_lead_id: Number(this.linkLeadId) });
        },
        async enrichProfile() {
            if (!this.crmUrls.enrich) return;
            await this.postJson(this.crmUrls.enrich, {});
        },
        async syncAllMessages() {
            if (!this.crmUrls.syncMessages || this.syncingMessages) return;
            this.syncingMessages = true;
            this.crmError = '';
            this.crmOk = '';
            try {
                const res = await fetch(this.crmUrls.syncMessages, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                });
                const data = await res.json();
                if (!data.success) {
                    this.crmError = data.error || 'فشل جلب الرسائل';
                    return;
                }
                this.crmOk = 'تم جلب ' + (data.imported || 0) + ' رسالة جديدة (الإجمالي المعروض: ' + (data.message_count || 0) + ')';
                // إعادة تحميل لضمان الترتيب الكامل في الواجهة
                location.reload();
            } catch (e) {
                this.crmError = 'خطأ أثناء جلب الرسائل';
            } finally {
                this.syncingMessages = false;
            }
        },
        async sendReply() {
            if (!this.replyUrl || !this.replyBody.trim() || this.sending) return;
            this.sending = true;
            try {
                const res = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({ body: this.replyBody.trim() }),
                });
                const data = await res.json();
                if (data.success) {
                    this.replyBody = '';
                    location.reload();
                } else {
                    alert(data.error || 'فشل الإرسال');
                }
            } catch (e) {
                alert('خطأ في الإرسال');
            }
            this.sending = false;
        },
        async poll() {
            if (!this.pollUrl) return;
            try {
                const url = this.pollUrl + (this.pollUrl.includes('?') ? '&' : '?') + 'after_id=' + (this.lastMessageId || 0);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (!data.success) return;
                if (data.crm) this.applyCrm(data.crm);
                if (typeof data.message_count === 'number' && data.message_count > this.lastMessageCount) {
                    // رسائل جديدة: أعد التحميل للحفاظ على ترتيب ثابت ومنظم
                    location.reload();
                }
            } catch (e) {}
        },
    };
}
</script>
@endpush
@endsection
