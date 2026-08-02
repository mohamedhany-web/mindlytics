@extends('layouts.admin')

@section('title', 'Meta Inbox — Business Suite')
@section('header', 'Meta Inbox')

@section('content')
@php
    $convId = $activeConversation?->id;
    $replyUrl = $convId ? route('admin.meta-social.inbox.reply', $convId) : '';
    $pollUrl = route('admin.meta-social.inbox.poll', ['page' => $pageId, 'conversation' => $convId]);
    $canUse = (bool) ($connected ?? ($connectionMeta['can_use'] ?? false));
    $crmReady = (bool) ($crmReady ?? false);
    $filterCounts = $filterCounts ?? ['all' => 0, 'unread' => 0, 'messenger' => 0, 'instagram' => 0, 'open' => 0, 'closed' => 0];
    $crmUrls = $convId ? [
        'assign' => route('admin.meta-social.inbox.assign', $convId),
        'contact' => route('admin.meta-social.inbox.contact', $convId),
        'createLead' => route('admin.meta-social.inbox.create-lead', $convId),
        'linkLead' => route('admin.meta-social.inbox.link-lead', $convId),
        'enrich' => route('admin.meta-social.inbox.enrich', $convId),
        'requestPhone' => route('admin.meta-social.inbox.request-phone', $convId),
        'syncMessages' => route('admin.meta-social.inbox.sync-messages', $convId),
    ] : [];
    $navBase = array_filter([
        'page' => $pageId ?: null,
        'assigned_to' => $assignedFilter ?: null,
        'q' => $search ?: null,
    ], fn ($v) => $v !== null && $v !== '');
@endphp

<div class="bs-inbox-page wa-inbox-immersive" x-data="metaSocialInbox()" x-cloak>
    @include('admin.meta-social._alerts')

    {{-- Top bar — Business Suite style --}}
    <header class="bs-topbar shrink-0">
        <div class="bs-topbar__brand">
            <button type="button" @click="$dispatch('open-sidebar')" class="lg:hidden bs-icon-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="bs-meta-mark"><i class="fab fa-meta"></i></div>
            <div class="min-w-0">
                <h1 class="bs-topbar__title">Inbox</h1>
                <p class="bs-topbar__sub">Business Suite · Messenger & Instagram</p>
            </div>
        </div>
        <div class="bs-topbar__actions">
            @if($pages->isNotEmpty())
                <select class="bs-select" onchange="metaInboxNav({page: this.value})">
                    <option value="">كل الصفحات</option>
                    @foreach($pages as $p)
                        <option value="{{ $p->id }}" @selected($pageId == $p->id)>{{ $p->page_name }}</option>
                    @endforeach
                </select>
            @endif
            <span class="bs-chip bs-chip--live" id="bs-live-chip"><i class="fas fa-circle"></i> Live</span>
            <span class="bs-chip bs-chip--unread" id="bs-unread-chip">{{ $unreadTotal }} غير مقروء</span>
            <a href="{{ route('admin.meta-social.pages.index') }}" class="bs-btn-ghost hidden md:inline-flex"><i class="fab fa-facebook"></i> الصفحات</a>
            <a href="{{ route('admin.meta-social.settings') }}" class="bs-btn-ghost hidden md:inline-flex"><i class="fas fa-cog"></i></a>
        </div>
    </header>

    @if(! $tablesReady)
        <div class="bs-banner bs-banner--warn">شغّل: <code>php artisan migrate --force</code></div>
    @elseif(! $canUse)
        <div class="bs-banner bs-banner--warn">
            Meta غير مربوط —
            <a href="{{ route('admin.meta-social.settings') }}" class="underline font-bold">إعداد الربط</a>
        </div>
    @endif

    <div class="bs-shell">
        {{-- Conversations column --}}
        <aside class="bs-col bs-list" :class="(conversationId && !showSidebarMobile) ? 'max-lg:hidden' : ''">
            <div class="bs-list__head">
                <div class="bs-search">
                    <i class="fas fa-search"></i>
                    <form method="get" action="{{ route('admin.meta-social.inbox.index') }}" class="flex-1">
                        @foreach($navBase as $k => $v)
                            @if($k !== 'q')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                        @endforeach
                        @if($platformFilter)<input type="hidden" name="platform" value="{{ $platformFilter }}">@endif
                        @if($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
                        @if($unreadOnly)<input type="hidden" name="unread" value="1">@endif
                        <input type="search" name="q" value="{{ $search }}" placeholder="بحث بالاسم، الهاتف، الرسالة..." autocomplete="off">
                    </form>
                </div>

                <div class="bs-tabs">
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase) }}"
                       class="bs-tab {{ ! $platformFilter && ! $unreadOnly && ! $statusFilter ? 'is-active' : '' }}">
                        الكل <span>{{ $filterCounts['all'] }}</span>
                    </a>
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase + ['unread' => 1]) }}"
                       class="bs-tab {{ $unreadOnly ? 'is-active' : '' }}">
                        غير مقروء <span>{{ $filterCounts['unread'] }}</span>
                    </a>
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase + ['platform' => 'messenger']) }}"
                       class="bs-tab {{ ($platformFilter ?? '') === 'messenger' ? 'is-active' : '' }}">
                        <i class="fab fa-facebook-messenger"></i> <span>{{ $filterCounts['messenger'] }}</span>
                    </a>
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase + ['platform' => 'instagram']) }}"
                       class="bs-tab {{ ($platformFilter ?? '') === 'instagram' ? 'is-active' : '' }}">
                        <i class="fab fa-instagram"></i> <span>{{ $filterCounts['instagram'] }}</span>
                    </a>
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase + ['status' => 'open']) }}"
                       class="bs-tab {{ ($statusFilter ?? '') === 'open' ? 'is-active' : '' }}">
                        مفتوح <span>{{ $filterCounts['open'] }}</span>
                    </a>
                    <a href="{{ route('admin.meta-social.inbox.index', $navBase + ['status' => 'closed']) }}"
                       class="bs-tab {{ ($statusFilter ?? '') === 'closed' ? 'is-active' : '' }}">
                        منتهي <span>{{ $filterCounts['closed'] }}</span>
                    </a>
                </div>

                <div class="bs-filters-row">
                    <select class="bs-select bs-select--sm" onchange="metaInboxNav({assigned_to: this.value})">
                        <option value="">كل الموظفين</option>
                        <option value="unassigned" @selected(($assignedFilter ?? '') === 'unassigned')>غير معيّن</option>
                        @foreach(($agents ?? []) as $agent)
                            <option value="{{ $agent->id }}" @selected((string) ($assignedFilter ?? '') === (string) $agent->id)>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <span class="bs-count">{{ $conversations->count() }} محادثة</span>
                </div>
            </div>

            <div class="bs-list__body" id="bs-conv-list">
                @forelse($conversations as $c)
                    @php
                        $href = route('admin.meta-social.inbox.index', array_filter([
                            'page' => $pageId ?: null,
                            'conversation' => $c->id,
                            'platform' => $platformFilter ?: null,
                            'assigned_to' => $assignedFilter ?: null,
                            'status' => $statusFilter ?: null,
                            'unread' => $unreadOnly ? 1 : null,
                            'q' => $search ?: null,
                        ]));
                    @endphp
                    <a href="{{ $href }}"
                       class="bs-conv {{ $convId == $c->id ? 'is-active' : '' }} {{ $c->unread_count > 0 ? 'is-unread' : '' }}"
                       data-conv-id="{{ $c->id }}"
                       data-platform="{{ $c->platform }}">
                        <div class="bs-avatar">
                            @if($c->participant_profile_pic)
                                <img src="{{ $c->participant_profile_pic }}" alt="" data-role="avatar">
                            @else
                                <span data-role="avatar-letter">{{ mb_substr($c->displayName(), 0, 1) }}</span>
                            @endif
                            <i class="bs-plat {{ $c->platform === 'instagram' ? 'fab fa-instagram ig' : 'fab fa-facebook-messenger msgr' }}"></i>
                        </div>
                        <div class="bs-conv__main">
                            <div class="bs-conv__row">
                                <p class="bs-conv__name" data-role="name">{{ $c->displayName() }}</p>
                                <time data-role="time">{{ $c->last_message_at?->format('H:i') ?? '' }}</time>
                            </div>
                            <p class="bs-conv__meta" data-role="meta">
                                {{ $c->page?->page_name }}
                                @if($c->assignee) · {{ $c->assignee->name }} @endif
                            </p>
                            <div class="bs-conv__row">
                                <p class="bs-conv__preview" data-role="preview">{{ $c->last_message_preview ?: '—' }}</p>
                                <div class="bs-conv__badges" data-role="badges">
                                    @if($c->sales_lead_id)<span class="bs-mini crm">CRM</span>@endif
                                    @if($c->status === 'closed')<span class="bs-mini done">Done</span>@endif
                                    @if($c->unread_count > 0)<span class="bs-unread" data-role="unread">{{ $c->unread_count }}</span>@endif
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bs-empty">
                        <i class="fab fa-meta"></i>
                        <p>لا توجد محادثات بهذا الفلتر</p>
                        <a href="{{ route('admin.meta-social.pages.index') }}">جلب كل الرسائل من الصفحات</a>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- Chat column --}}
        <section class="bs-col bs-chat {{ $convId ? '' : 'max-lg:hidden' }}">
            @if($activeConversation)
                <div class="bs-chat__wrap">
                    <div class="bs-chat__head">
                        <button type="button" @click="showSidebarMobile = true" class="lg:hidden bs-icon-btn"><i class="fas fa-arrow-right"></i></button>
                        <div class="bs-avatar bs-avatar--lg">
                            @if($activeConversation->participant_profile_pic)
                                <img src="{{ $activeConversation->participant_profile_pic }}" alt="">
                            @else
                                <span>{{ mb_substr($activeConversation->displayName(), 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="bs-chat__name" x-text="crm?.display_name || @js($activeConversation->displayName())">{{ $activeConversation->displayName() }}</h2>
                            <p class="bs-chat__sub">
                                <i class="{{ $activeConversation->platform === 'instagram' ? 'fab fa-instagram text-pink-500' : 'fab fa-facebook-messenger text-[#0084FF]' }}"></i>
                                {{ $activeConversation->platformLabel() }} · {{ $activeConversation->page?->page_name }}
                                <span x-show="crm?.assignee_name"> · <span x-text="crm?.assignee_name"></span></span>
                            </p>
                        </div>
                        @if($activeConversation->platform === 'messenger')
                            <button type="button" @click="requestPhone()" class="bs-btn-ghost" :disabled="crmSaving" title="طلب رقم الهاتف من العميل">
                                <i class="fas fa-mobile-alt"></i>
                                <span class="hidden sm:inline">طلب رقم</span>
                            </button>
                        @endif
                        <button type="button" @click="syncAllMessages()" class="bs-btn-ghost" :disabled="syncingMessages" title="جلب كل الرسائل">
                            <i class="fas fa-cloud-download-alt" :class="syncingMessages ? 'fa-spinner fa-spin' : ''"></i>
                            <span class="hidden sm:inline">مزامنة</span>
                        </button>
                        <button type="button" @click="toggleDone()" class="bs-btn-ghost" :disabled="crmSaving">
                            <i class="fas" :class="(crm?.status || @js($activeConversation->status)) === 'closed' ? 'fa-envelope-open-text' : 'fa-check-circle'"></i>
                            <span class="hidden sm:inline" x-text="(crm?.status || @js($activeConversation->status)) === 'closed' ? 'إعادة فتح' : 'إنهاء'"></span>
                        </button>
                        <button type="button" @click="showDetails = !showDetails" class="bs-btn-ghost xl:hidden">
                            <i class="fas fa-user-circle"></i>
                        </button>
                    </div>

                    <div id="sm-chat-messages" class="bs-chat__messages">
                        @php $lastDate = null; @endphp
                        @forelse($messages as $m)
                            @php
                                $day = $m->sent_at?->format('Y-m-d') ?? $m->created_at?->format('Y-m-d');
                                $dayLabel = $m->sent_at?->locale('ar')->translatedFormat('l j F Y')
                                    ?? $m->created_at?->locale('ar')->translatedFormat('l j F Y')
                                    ?? '';
                            @endphp
                            @if($day && $day !== $lastDate)
                                @php $lastDate = $day; @endphp
                                <div class="bs-date-pill"><span>{{ $dayLabel }}</span></div>
                            @endif
                            <div class="bs-msg {{ $m->direction === 'inbound' ? 'is-in' : 'is-out' }}" data-msg-id="{{ $m->id }}">
                                <div class="bs-bubble {{ $m->direction === 'inbound' ? 'is-in' : 'is-out' }}">
                                    @if($m->attachment_url && str_contains((string) $m->message_type, 'image'))
                                        <a href="{{ $m->attachment_url }}" target="_blank" rel="noopener">
                                            <img src="{{ $m->attachment_url }}" alt="" class="bs-bubble__img">
                                        </a>
                                    @elseif($m->attachment_url)
                                        <a href="{{ $m->attachment_url }}" target="_blank" class="bs-bubble__file" rel="noopener">
                                            <i class="fas fa-paperclip"></i> مرفق
                                        </a>
                                    @endif
                                    @if(filled($m->displayBody()) && $m->displayBody() !== '—')
                                        <p class="bs-bubble__text">{{ $m->displayBody() }}</p>
                                    @endif
                                    <div class="bs-bubble__meta">
                                        @if($m->direction === 'outbound' && $m->sentBy)
                                            <span>{{ $m->sentBy->name }}</span>
                                        @endif
                                        <span>{{ $m->sent_at?->format('H:i') ?? $m->created_at?->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bs-empty">
                                <i class="fas fa-comments"></i>
                                <p>لا رسائل بعد — اضغط «مزامنة» لجلب كل الرسائل من Meta</p>
                            </div>
                        @endforelse
                    </div>

                    <form class="bs-composer" @submit.prevent="sendReply()">
                        <textarea x-model="replyBody"
                                  rows="1"
                                  placeholder="اكتب رسالة… (Enter للإرسال · Shift+Enter لسطر جديد)"
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); sendReply(); }"
                                  :disabled="sending"
                                  class="bs-composer__input"></textarea>
                        <button type="submit" class="bs-send" :disabled="sending || !replyBody.trim()">
                            <i class="fas fa-paper-plane" :class="sending ? 'fa-spinner fa-spin' : ''"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="bs-empty bs-empty--center">
                    <div class="bs-meta-mark bs-meta-mark--xl"><i class="fab fa-meta"></i></div>
                    <p class="font-black text-lg text-slate-800">Meta Business Suite Inbox</p>
                    <p class="text-sm text-slate-500 max-w-sm text-center">اختر محادثة من القائمة — Messenger و Instagram في مكان واحد، مع CRM أقوى من Business Suite.</p>
                </div>
            @endif
        </section>

        {{-- Details / CRM --}}
        <div class="bs-col bs-details" :class="showDetails ? 'max-xl:!flex' : ''" x-show="conversationId" x-cloak>
            @include('admin.meta-social._crm_panel')
        </div>
    </div>
</div>

@push('styles')
<style>
:root {
    --bs-blue: #0084FF;
    --bs-ink: #1c2b33;
    --bs-muted: #65676b;
    --bs-line: #e4e6eb;
    --bs-bg: #f0f2f5;
    --bs-ig: #E1306C;
}
main:has(.bs-inbox-page) { overflow: hidden !important; }
main:has(.bs-inbox-page) > div:last-child {
    flex: 1 1 auto !important; min-height: 0 !important; display: flex !important;
    flex-direction: column !important; overflow: hidden !important; padding: 0 !important;
}
.bs-inbox-page {
    display: flex; flex-direction: column; height: 100dvh; max-height: 100dvh;
    min-height: 0; overflow: hidden; background: #fff; color: var(--bs-ink);
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
}
.bs-topbar {
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    padding: .65rem .9rem; border-bottom: 1px solid var(--bs-line); background: #fff; flex-shrink: 0;
}
.bs-topbar__brand { display: flex; align-items: center; gap: .65rem; min-width: 0; }
.bs-topbar__title { font-size: 1.05rem; font-weight: 800; line-height: 1.1; }
.bs-topbar__sub { font-size: 10px; color: var(--bs-muted); }
.bs-topbar__actions { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; justify-content: flex-end; }
.bs-meta-mark {
    width: 2.25rem; height: 2.25rem; border-radius: .75rem; display: grid; place-items: center;
    background: linear-gradient(135deg, #0084FF, #0064E0); color: #fff; font-size: 1rem; flex-shrink: 0;
}
.bs-meta-mark--xl { width: 4.5rem; height: 4.5rem; font-size: 2rem; border-radius: 1.25rem; margin-bottom: .75rem; }
.bs-shell {
    flex: 1 1 auto; min-height: 0; display: grid; grid-template-columns: 1fr; overflow: hidden;
}
@media (min-width: 1024px) {
    .bs-shell { grid-template-columns: minmax(280px, 340px) minmax(0, 1fr) minmax(280px, 320px); }
}
.bs-col { min-width: 0; min-height: 0; max-height: 100%; overflow: hidden; }
.bs-list { display: grid; grid-template-rows: auto minmax(0,1fr); border-inline-end: 1px solid var(--bs-line); background: #fff; }
.bs-list__head { border-bottom: 1px solid var(--bs-line); padding: .65rem .7rem .55rem; background: #fff; }
.bs-search {
    display: flex; align-items: center; gap: .5rem; background: var(--bs-bg); border-radius: 999px;
    padding: .45rem .8rem; margin-bottom: .55rem;
}
.bs-search i { color: var(--bs-muted); font-size: .75rem; }
.bs-search input {
    width: 100%; border: 0; outline: 0; background: transparent; font-size: .8rem; color: var(--bs-ink);
}
.bs-tabs { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .45rem; }
.bs-tab {
    display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .55rem; border-radius: 999px;
    font-size: 10px; font-weight: 700; color: var(--bs-muted); background: var(--bs-bg); text-decoration: none;
}
.bs-tab span { opacity: .85; }
.bs-tab.is-active { background: #e7f3ff; color: var(--bs-blue); }
.bs-filters-row { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
.bs-count { font-size: 10px; color: var(--bs-muted); font-weight: 700; }
.bs-select {
    border: 1px solid var(--bs-line); border-radius: .55rem; background: #fff; font-size: 11px;
    padding: .35rem .55rem; color: var(--bs-ink); max-width: 11rem;
}
.bs-select--sm { max-width: 100%; flex: 1; }
.bs-list__body { overflow-y: auto; overscroll-behavior: contain; }
.bs-conv {
    display: flex; gap: .7rem; padding: .7rem .8rem; border-bottom: 1px solid #f0f2f5;
    text-decoration: none; color: inherit; transition: background .12s;
}
.bs-conv:hover { background: #f7f8fa; }
.bs-conv.is-active { background: #e7f3ff; }
.bs-conv.is-unread .bs-conv__name { font-weight: 800; }
.bs-conv.is-unread .bs-conv__preview { color: var(--bs-ink); font-weight: 600; }
.bs-avatar {
    position: relative; width: 2.75rem; height: 2.75rem; border-radius: 999px; overflow: hidden;
    background: #dbeafe; color: #1d4ed8; display: grid; place-items: center; font-weight: 800; flex-shrink: 0;
}
.bs-avatar img { width: 100%; height: 100%; object-fit: cover; }
.bs-avatar--lg { width: 2.5rem; height: 2.5rem; }
.bs-plat {
    position: absolute; inset-inline-end: -2px; bottom: -2px; width: 1rem; height: 1rem; border-radius: 999px;
    background: #fff; display: grid; place-items: center; font-size: 9px; box-shadow: 0 0 0 1.5px #fff;
}
.bs-plat.msgr { color: var(--bs-blue); }
.bs-plat.ig { color: var(--bs-ig); }
.bs-conv__main { min-width: 0; flex: 1; }
.bs-conv__row { display: flex; align-items: baseline; justify-content: space-between; gap: .5rem; }
.bs-conv__name { font-size: 13px; font-weight: 700; truncate; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bs-conv__row time { font-size: 10px; color: var(--bs-muted); flex-shrink: 0; }
.bs-conv__meta { font-size: 10px; color: var(--bs-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 1px 0 2px; }
.bs-conv__preview { font-size: 12px; color: #8a8d91; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.bs-conv__badges { display: flex; align-items: center; gap: .25rem; }
.bs-mini { font-size: 9px; font-weight: 800; padding: .1rem .35rem; border-radius: 999px; }
.bs-mini.crm { background: #ecfdf5; color: #047857; }
.bs-mini.done { background: #f3f4f6; color: #4b5563; }
.bs-unread {
    min-width: 1.1rem; height: 1.1rem; padding: 0 .3rem; border-radius: 999px; background: var(--bs-blue);
    color: #fff; font-size: 10px; font-weight: 800; display: inline-grid; place-items: center;
}
.bs-chat { background: var(--bs-bg); display: grid; grid-template-rows: minmax(0,1fr); }
.bs-chat__wrap { display: grid; grid-template-rows: auto minmax(0,1fr) auto; min-height: 0; max-height: 100%; overflow: hidden; }
.bs-chat__head {
    display: flex; align-items: center; gap: .55rem; padding: .65rem .85rem; background: #fff;
    border-bottom: 1px solid var(--bs-line); flex-shrink: 0;
}
.bs-chat__name { font-size: .95rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bs-chat__sub { font-size: 11px; color: var(--bs-muted); display: flex; align-items: center; gap: .3rem; flex-wrap: wrap; }
.bs-chat__messages {
    min-height: 0; overflow-y: auto; overscroll-behavior: contain; padding: 1rem .85rem 1.25rem;
}
.bs-date-pill { display: flex; justify-content: center; margin: .75rem 0; }
.bs-date-pill span {
    background: rgba(255,255,255,.92); border: 1px solid var(--bs-line); color: var(--bs-muted);
    font-size: 11px; font-weight: 700; padding: .2rem .65rem; border-radius: 999px;
}
.bs-msg { display: flex; margin-bottom: .35rem; }
.bs-msg.is-in { justify-content: flex-start; }
.bs-msg.is-out { justify-content: flex-end; }
.bs-bubble {
    max-width: min(78%, 28rem); padding: .45rem .7rem .35rem; border-radius: 1.15rem;
    font-size: 14px; line-height: 1.4; word-break: break-word; box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.bs-bubble.is-in { background: #fff; color: var(--bs-ink); border-start-start-radius: .35rem; }
.bs-bubble.is-out { background: var(--bs-blue); color: #fff; border-start-end-radius: .35rem; }
.bs-bubble__text { white-space: pre-wrap; margin: 0; }
.bs-bubble__img { max-width: 100%; max-height: 240px; border-radius: .75rem; display: block; margin-bottom: .25rem; }
.bs-bubble__file { display: inline-flex; align-items: center; gap: .35rem; font-weight: 700; text-decoration: underline; }
.bs-bubble__meta {
    display: flex; justify-content: flex-end; gap: .35rem; margin-top: .15rem;
    font-size: 10px; opacity: .8;
}
.bs-bubble.is-out .bs-bubble__meta { color: rgba(255,255,255,.9); }
.bs-composer {
    display: flex; align-items: flex-end; gap: .5rem; padding: .65rem .75rem; background: #fff;
    border-top: 1px solid var(--bs-line); flex-shrink: 0;
}
.bs-composer__input {
    flex: 1; resize: none; min-height: 2.5rem; max-height: 7rem; border: 1px solid var(--bs-line);
    border-radius: 1.25rem; padding: .65rem .9rem; font-size: .9rem; outline: none; background: var(--bs-bg);
}
.bs-composer__input:focus { border-color: var(--bs-blue); background: #fff; box-shadow: 0 0 0 3px rgba(0,132,255,.12); }
.bs-send {
    width: 2.6rem; height: 2.6rem; border-radius: 999px; border: 0; background: var(--bs-blue); color: #fff;
    display: grid; place-items: center; flex-shrink: 0; cursor: pointer;
}
.bs-send:disabled { opacity: .45; cursor: not-allowed; }
.bs-details { display: none; border-inline-start: 1px solid var(--bs-line); background: #fff; }
@media (min-width: 1280px) { .bs-details { display: flex; flex-direction: column; } }
.bs-details .sm-crm-sidebar { display: flex !important; width: 100%; height: 100%; border: 0; }
.bs-btn-ghost, .bs-icon-btn {
    display: inline-flex; align-items: center; gap: .35rem; border: 1px solid var(--bs-line); background: #fff;
    border-radius: .65rem; padding: .4rem .6rem; font-size: 11px; font-weight: 700; color: var(--bs-ink); cursor: pointer;
    text-decoration: none;
}
.bs-icon-btn { width: 2.1rem; height: 2.1rem; justify-content: center; padding: 0; }
.bs-chip { font-size: 11px; font-weight: 800; padding: .3rem .55rem; border-radius: 999px; background: var(--bs-bg); }
.bs-chip--unread { background: #e7f3ff; color: var(--bs-blue); }
.bs-chip--live { background: #ecfdf5; color: #047857; gap: .35rem; display: inline-flex; align-items: center; }
.bs-chip--live i { font-size: 7px; color: #10b981; animation: bs-pulse 1.4s ease-in-out infinite; }
.bs-chip--live.is-stale { background: #f3f4f6; color: #6b7280; }
.bs-chip--live.is-stale i { color: #9ca3af; animation: none; }
@keyframes bs-pulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }
.bs-banner { margin: .5rem .75rem 0; padding: .55rem .75rem; border-radius: .75rem; font-size: 12px; font-weight: 600; }
.bs-banner--warn { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; }
.bs-empty { padding: 2.5rem 1rem; text-align: center; color: var(--bs-muted); font-size: .85rem; }
.bs-empty i { font-size: 1.75rem; opacity: .35; display: block; margin-bottom: .5rem; }
.bs-empty a { color: var(--bs-blue); font-weight: 800; text-decoration: underline; }
.bs-empty--center { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
@media (max-width: 1023px) {
    .bs-shell { position: relative; }
    .bs-list, .bs-chat { grid-column: 1; grid-row: 1; }
    .bs-chat { z-index: 2; }
    .bs-details.max-xl\:\!flex {
        position: absolute; inset: 0; z-index: 5; display: flex !important; background: #fff;
    }
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
    // عند تغيير فلتر عام نبدأ من قائمة نظيفة
    if (!('conversation' in (patch || {}))) params.delete('conversation');
    const qs = params.toString();
    window.location = @json(route('admin.meta-social.inbox.index')) + (qs ? ('?' + qs) : '');
}

function metaSocialInbox() {
    return {
        conversationId: {{ $convId ?: 'null' }},
        replyBody: '',
        sending: false,
        showSidebarMobile: false,
        showDetails: false,
        replyUrl: @json($replyUrl),
        pollUrl: @json($pollUrl),
        csrf: @json(csrf_token()),
        lastMessageCount: {{ $messages->count() }},
        lastMessageId: {{ $messages->last()?->id ?: 0 }},
        inboxVersion: '',
        polling: false,
        pollTimer: null,
        liveOk: true,
        crm: @json($crmPayload),
        crmUrls: @json($crmUrls),
        contactName: @json($crmPayload['display_name'] ?? $activeConversation?->displayName() ?? ''),
        contactPhone: @json($crmPayload['phone'] ?? $activeConversation?->phone ?? ''),
        contactEmail: @json($crmPayload['email'] ?? $activeConversation?->email ?? ''),
        contactNotes: @json($crmPayload['notes'] ?? $activeConversation?->notes ?? ''),
        assigneeId: @json(!empty($crmPayload['assigned_to']) ? (string) $crmPayload['assigned_to'] : ''),
        linkLeadId: '',
        crmSaving: false,
        crmError: '',
        crmOk: '',
        syncingMessages: false,
        init() {
            this.scrollChat();
            this.schedulePoll(1500);
            document.addEventListener('visibilitychange', () => {
                this.schedulePoll(document.hidden ? 8000 : 1500);
                if (!document.hidden) this.poll();
            });
        },
        schedulePoll(ms) {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => this.poll(), ms);
        },
        scrollChat() {
            const el = document.getElementById('sm-chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
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
        escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        },
        appendMessage(m) {
            if (!m || !m.id) return;
            const box = document.getElementById('sm-chat-messages');
            if (!box) return;
            if (box.querySelector('[data-msg-id="' + m.id + '"]')) return;
            const empty = box.querySelector('.bs-empty');
            if (empty) empty.remove();
            const dir = m.direction === 'outbound' ? 'is-out' : 'is-in';
            const wrap = document.createElement('div');
            wrap.className = 'bs-msg ' + dir;
            wrap.setAttribute('data-msg-id', m.id);
            let media = '';
            if (m.attachment_url && String(m.message_type || '').includes('image')) {
                media = '<a href="' + this.escapeHtml(m.attachment_url) + '" target="_blank" rel="noopener"><img src="' + this.escapeHtml(m.attachment_url) + '" alt="" class="bs-bubble__img"></a>';
            } else if (m.attachment_url) {
                media = '<a href="' + this.escapeHtml(m.attachment_url) + '" target="_blank" class="bs-bubble__file" rel="noopener"><i class="fas fa-paperclip"></i> مرفق</a>';
            }
            const text = (m.body && m.body !== '—')
                ? '<p class="bs-bubble__text">' + this.escapeHtml(m.body) + '</p>'
                : '';
            const author = (m.direction === 'outbound' && m.author)
                ? '<span>' + this.escapeHtml(m.author) + '</span>'
                : '';
            wrap.innerHTML = '<div class="bs-bubble ' + dir + '">' + media + text
                + '<div class="bs-bubble__meta">' + author + '<span>' + this.escapeHtml(m.sent_at_human || '') + '</span></div></div>';
            box.appendChild(wrap);
            this.lastMessageId = Math.max(this.lastMessageId || 0, Number(m.id) || 0);
            this.lastMessageCount = (this.lastMessageCount || 0) + 1;
            this.scrollChat();
        },
        updateConversationList(rows) {
            if (!Array.isArray(rows)) return;
            const list = document.getElementById('bs-conv-list');
            if (!list) return;
            rows.forEach((c) => {
                const row = list.querySelector('[data-conv-id="' + c.id + '"]');
                if (!row) return;
                const name = row.querySelector('[data-role="name"]');
                const time = row.querySelector('[data-role="time"]');
                const preview = row.querySelector('[data-role="preview"]');
                const badges = row.querySelector('[data-role="badges"]');
                if (name && c.name) name.textContent = c.name;
                if (time) time.textContent = c.last_at || '';
                if (preview) preview.textContent = c.preview || '—';
                row.classList.toggle('is-unread', Number(c.unread) > 0 && Number(c.id) !== Number(this.conversationId));
                if (badges) {
                    let html = '';
                    if (c.has_crm) html += '<span class="bs-mini crm">CRM</span>';
                    if (c.status === 'closed') html += '<span class="bs-mini done">Done</span>';
                    if (Number(c.unread) > 0 && Number(c.id) !== Number(this.conversationId)) {
                        html += '<span class="bs-unread" data-role="unread">' + Number(c.unread) + '</span>';
                    }
                    badges.innerHTML = html;
                }
                if (list.firstElementChild !== row && Number(c.id) !== Number(this.conversationId)) {
                    // ارفع المحادثات الجديدة للأعلى فقط لو فيها unread أو أحدث preview
                    if (Number(c.unread) > 0) list.prepend(row);
                }
            });
        },
        setLive(ok) {
            this.liveOk = ok;
            const chip = document.getElementById('bs-live-chip');
            if (chip) chip.classList.toggle('is-stale', !ok);
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
                if (data.message) this.appendMessage(data.message);
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
        async requestPhone() {
            if (!this.crmUrls.requestPhone) return;
            const data = await this.postJson(this.crmUrls.requestPhone, {});
            if (data) this.crmOk = 'تم إرسال طلب الرقم للعميل';
        },
        async toggleDone() {
            if (!this.crmUrls.contact) return;
            const next = (this.crm?.status || 'open') === 'closed' ? 'open' : 'closed';
            await this.postJson(this.crmUrls.contact, { status: next });
        },
        async syncAllMessages() {
            if (!this.crmUrls.syncMessages || this.syncingMessages) return;
            this.syncingMessages = true;
            this.crmError = '';
            try {
                const res = await fetch(this.crmUrls.syncMessages, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                });
                const data = await res.json();
                if (!data.success) {
                    this.crmError = data.error || 'فشل جلب الرسائل';
                    return;
                }
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
                    if (data.message) this.appendMessage(data.message);
                    else this.poll(true);
                } else {
                    alert(data.error || 'فشل الإرسال');
                }
            } catch (e) {
                alert('خطأ في الإرسال');
            }
            this.sending = false;
        },
        async poll(force) {
            if (!this.pollUrl || this.polling) return;
            this.polling = true;
            try {
                const sep = this.pollUrl.includes('?') ? '&' : '?';
                const url = this.pollUrl + sep + 'after_id=' + (this.lastMessageId || 0)
                    + '&v=' + encodeURIComponent(this.inboxVersion || '');
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                const data = await res.json();
                if (!data.success) {
                    this.setLive(false);
                    return;
                }
                this.setLive(true);
                if (data.inbox_version) this.inboxVersion = data.inbox_version;
                if (typeof data.unread_total === 'number') {
                    const chip = document.getElementById('bs-unread-chip');
                    if (chip) chip.textContent = data.unread_total + ' غير مقروء';
                }
                if (data.crm) this.applyCrm(data.crm);
                if (Array.isArray(data.messages)) {
                    data.messages.forEach((m) => this.appendMessage(m));
                }
                if (Array.isArray(data.conversations)) {
                    this.updateConversationList(data.conversations);
                }
                if (force && typeof data.message_count === 'number') {
                    this.lastMessageCount = data.message_count;
                }
            } catch (e) {
                this.setLive(false);
            } finally {
                this.polling = false;
            }
        },
    };
}
</script>
@endpush
@endsection
