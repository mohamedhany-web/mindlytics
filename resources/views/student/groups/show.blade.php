@extends('layouts.student-dashboard')

@section('title', $group->name)
@section('header', $group->name)

@php
    $courseTitle = $group->course?->localized('title') ?? $group->course?->title ?? '—';
    $memberCount = $group->members->count();
    $maxMembers = (int) ($group->max_members ?: 0);
    $isLeader = $group->leader && (int) $group->leader->id === (int) auth()->id();
@endphp

@section('content')
<div class="space-y-5" x-data="{ tab: 'chat' }">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            {{ session('error') }}
        </div>
    @endif

    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.groups.index') }}" class="sp-link">{{ __('student.my_groups_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ $group->name }}</span>
    </nav>

    <section class="sp-card p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="min-w-0 flex items-start gap-4">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky);width:56px;height:56px">
                    <x-student.figma-icon name="icon-community.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="sp-section-title m-0">{{ $group->name }}</h2>
                        @if($isLeader)
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.leader_label') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ $courseTitle }}</p>
                    @if($group->description)
                        <p class="text-sm text-[var(--sp-text)] m-0 mt-3 leading-relaxed">{{ $group->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-3 text-xs font-extrabold text-[var(--sp-muted)]">
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1">
                            <x-student.figma-icon name="icon-profile.svg" box="size-3.5" />
                            {{ $memberCount }}@if($maxMembers) / {{ $maxMembers }}@endif {{ __('student.member_singular') }}
                        </span>
                        @if($group->leader)
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#f7f7f5] px-2.5 py-1">
                                <x-student.figma-icon name="icon-star.svg" box="size-3.5" />
                                {{ __('student.leader_label') }}: {{ $group->leader->name }}
                            </span>
                        @endif
                        <span class="sp-pill {{ $pendingAssignmentsCount > 0 ? 'sp-pill--upcoming' : 'sp-pill--done' }}">
                            {{ $pendingAssignmentsCount }} {{ __('student.groups_pending_assignments') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('student.groups.assignments.index', $group) }}" class="sp-promo-btn !mt-0">
                    <x-student.figma-icon name="icon-messages.svg" box="size-4" class="me-2" />
                    {{ __('student.group_assignments_title') }}
                    @if($publishedAssignmentsCount > 0)
                        <span class="ms-2 inline-flex min-w-[22px] h-[22px] items-center justify-center rounded-full bg-[var(--sp-accent-text)] text-white text-[11px]">
                            {{ $publishedAssignmentsCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <button type="button"
                    @click="tab = 'chat'"
                    :class="tab === 'chat' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                    class="rounded-[30px] px-4 py-2 text-sm font-extrabold transition-colors">
                {{ __('student.group_chat_title') }}
            </button>
            <button type="button"
                    @click="tab = 'members'"
                    :class="tab === 'members' ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-muted)]'"
                    class="rounded-[30px] px-4 py-2 text-sm font-extrabold transition-colors lg:hidden">
                {{ __('student.group_members_title') }} ({{ $memberCount }})
            </button>
            <a href="{{ route('student.groups.assignments.index', $group) }}"
               class="rounded-[30px] px-4 py-2 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-muted)] hover:bg-[var(--sp-accent)] hover:text-[var(--sp-accent-text)] transition-colors">
                {{ __('student.group_assignments_title') }}
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-4" :class="{ 'hidden lg:block': tab !== 'chat' }">
            <section class="sp-card overflow-hidden flex flex-col min-h-[420px]">
                <div class="px-5 py-4 border-b border-black/5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="sp-icon-bubble !w-9 !h-9" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-messages.svg" box="size-4" />
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-sm m-0">{{ __('student.group_chat_title') }}</h3>
                            <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5">{{ __('student.group_chat_hint') }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[var(--sp-muted)] shrink-0" id="group-chat-live">{{ __('student.group_chat_live') }}</span>
                </div>

                <div class="p-4 sm:p-5 flex-1 min-h-[280px] max-h-[460px] overflow-y-auto space-y-3 scroll-smooth overscroll-contain"
                     id="group-messages"
                     data-messages-url="{{ route('student.groups.messages.index', $group) }}"
                     data-user-id="{{ auth()->id() }}"
                     data-i18n-send-fail="{{ __('student.group_message_send_fail') }}"
                     data-i18n-error="{{ __('student.group_message_error') }}">
                    @forelse($group->messages as $msg)
                        @php $isMe = (int) $msg->user_id === (int) auth()->id(); @endphp
                        <div class="flex gap-3 {{ $isMe ? 'flex-row-reverse' : '' }}" data-message-id="{{ $msg->id }}">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold shrink-0 {{ $isMe ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f0f0ec] text-[var(--sp-text)]' }}">
                                {{ mb_substr($msg->user->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0 {{ $isMe ? 'text-start' : '' }}">
                                <div class="text-[11px] font-bold text-[var(--sp-muted)] mb-1">
                                    {{ $msg->user->name ?? __('student.group_unknown_user') }}
                                    <span class="opacity-70"> · {{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="inline-block max-w-[min(100%,32rem)] rounded-[18px] px-3.5 py-2.5 text-sm leading-relaxed break-words {{ $isMe ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' : 'bg-[#f7f7f5] text-[var(--sp-text)]' }}">
                                    {{ $msg->body }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 px-4" id="group-messages-empty">
                            <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-lilac)">
                                <x-student.figma-icon name="icon-messages.svg" />
                            </span>
                            <p class="font-extrabold m-0 mb-1">{{ __('student.group_chat_empty') }}</p>
                            <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.group_chat_empty_hint') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 sm:p-5 border-t border-black/5 bg-white">
                    <form id="group-chat-form" action="{{ route('student.groups.messages.store', $group) }}" method="POST" class="flex gap-2 items-end">
                        @csrf
                        <div class="flex-1 min-w-0">
                            <label for="group-chat-input" class="sr-only">{{ __('student.group_chat_placeholder') }}</label>
                            <input type="text"
                                   name="body"
                                   id="group-chat-input"
                                   required
                                   maxlength="2000"
                                   placeholder="{{ __('student.group_chat_placeholder') }}"
                                   class="w-full rounded-[30px] border border-black/5 bg-[#f7f7f5] px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]">
                        </div>
                        <button type="submit"
                                id="group-chat-submit"
                                class="sp-promo-btn !mt-0 !px-5 !py-3 shrink-0 border-0 cursor-pointer">
                            {{ __('student.group_chat_send') }}
                        </button>
                    </form>
                    <p id="group-chat-error" class="mt-2 text-sm font-bold text-[#b45309] hidden"></p>
                </div>
            </section>
        </div>

        <aside class="space-y-4 hidden lg:block" :class="{ '!block': tab === 'members' }">
            <section class="sp-card p-5">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <h3 class="font-extrabold text-sm m-0 flex items-center gap-2">
                        <x-student.figma-icon name="icon-profile.svg" box="size-4" />
                        {{ __('student.group_members_title') }}
                    </h3>
                    <span class="sp-pill sp-pill--progress">{{ $memberCount }}</span>
                </div>
                <ul class="space-y-2 m-0 p-0 list-none">
                    @foreach($group->members as $member)
                        @php
                            $role = $member->pivot->role ?? null;
                            $isMemberLeader = $role === 'leader' || ($group->leader && (int) $group->leader->id === (int) $member->id);
                            $isMeMember = (int) $member->id === (int) auth()->id();
                        @endphp
                        <li class="flex items-center gap-3 p-2.5 rounded-[20px] {{ $isMeMember ? 'bg-[var(--sp-mint)]' : 'hover:bg-[#f7f7f5]' }}">
                            <div class="w-10 h-10 rounded-full bg-[#f0f0ec] text-[var(--sp-accent-text)] flex items-center justify-center font-extrabold text-sm shrink-0">
                                {{ mb_substr($member->name ?? '?', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-extrabold text-sm block truncate">
                                    {{ $member->name }}
                                    @if($isMeMember)
                                        <span class="text-[var(--sp-muted)] font-bold">({{ __('student.group_you') }})</span>
                                    @endif
                                </span>
                                <span class="text-xs text-[var(--sp-muted)] truncate block">{{ $member->email ?? '—' }}</span>
                            </div>
                            @if($isMemberLeader)
                                <span class="sp-pill sp-pill--upcoming shrink-0">{{ __('student.leader_label') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="sp-card p-5">
                <h3 class="font-extrabold text-sm m-0 mb-3">{{ __('student.group_quick_actions') }}</h3>
                <div class="space-y-2">
                    <a href="{{ route('student.groups.assignments.index', $group) }}" class="sp-process-row !p-3">
                        <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-amber-soft)">
                            <x-student.figma-icon name="icon-messages.svg" box="size-4" />
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-extrabold text-sm truncate">{{ __('student.group_assignments_title') }}</span>
                            <span class="block text-xs mt-0.5 text-[var(--sp-muted)]">
                                {{ __('student.groups_pending_assignments') }}: {{ $pendingAssignmentsCount }}
                            </span>
                        </span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="opacity-40 rtl:rotate-180" />
                    </a>
                    <a href="{{ route('student.groups.index') }}" class="sp-process-row !p-3">
                        <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-lilac)">
                            <x-student.figma-icon name="icon-community.svg" box="size-4" />
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-extrabold text-sm truncate">{{ __('student.groups_all_groups') }}</span>
                            <span class="block text-xs mt-0.5 text-[var(--sp-muted)]">{{ __('student.groups_back_list') }}</span>
                        </span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="opacity-40 rtl:rotate-180" />
                    </a>
                </div>
            </section>
        </aside>
    </div>
</div>

@push('styles')
<style>
    #group-messages::-webkit-scrollbar { width: 6px; }
    #group-messages::-webkit-scrollbar-track { background: transparent; }
    #group-messages::-webkit-scrollbar-thumb { background: #e0e0dc; border-radius: 999px; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    var container = document.getElementById('group-messages');
    var form = document.getElementById('group-chat-form');
    var input = document.getElementById('group-chat-input');
    var submitBtn = document.getElementById('group-chat-submit');
    var errorEl = document.getElementById('group-chat-error');
    if (!container || !form) return;

    var messagesUrl = container.getAttribute('data-messages-url');
    var currentUserId = parseInt(container.getAttribute('data-user-id'), 10);
    var sendFail = container.getAttribute('data-i18n-send-fail') || 'Send failed';
    var genericError = container.getAttribute('data-i18n-error') || 'Error';
    var tokenInput = document.querySelector('input[name="_token"]');
    var csrf = (tokenInput && tokenInput.value) || (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '';
    var pollInterval = 3500;

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function buildMessageHtml(msg) {
        var isMe = msg.user_id === currentUserId;
        var rowClass = isMe ? 'flex-row-reverse' : '';
        var bubbleClass = isMe
            ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]'
            : 'bg-[#f7f7f5] text-[var(--sp-text)]';
        var avatarClass = isMe
            ? 'bg-[var(--sp-accent)] text-[var(--sp-accent-text)]'
            : 'bg-[#f0f0ec] text-[var(--sp-text)]';
        var initial = (msg.user_name || '?').charAt(0);
        return '<div class="flex gap-3 ' + rowClass + '" data-message-id="' + msg.id + '">' +
            '<div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold shrink-0 ' + avatarClass + '">' + escapeHtml(initial) + '</div>' +
            '<div class="flex-1 min-w-0">' +
            '<div class="text-[11px] font-bold text-[var(--sp-muted)] mb-1">' + escapeHtml(msg.user_name) +
            ' <span class="opacity-70"> · ' + escapeHtml(msg.created_at_human) + '</span></div>' +
            '<div class="inline-block max-w-[min(100%,32rem)] rounded-[18px] px-3.5 py-2.5 text-sm leading-relaxed break-words ' + bubbleClass + '">' +
            escapeHtml(msg.body) + '</div></div></div>';
    }

    function removeEmptyState() {
        var empty = document.getElementById('group-messages-empty');
        if (empty) empty.remove();
    }

    function appendMessages(messages) {
        if (!messages.length) return;
        removeEmptyState();
        var wasNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 80;
        for (var i = 0; i < messages.length; i++) {
            if (container.querySelector('[data-message-id="' + messages[i].id + '"]')) continue;
            var wrap = document.createElement('div');
            wrap.innerHTML = buildMessageHtml(messages[i]);
            container.appendChild(wrap.firstElementChild);
        }
        if (wasNearBottom) container.scrollTop = container.scrollHeight;
    }

    function getLastMessageId() {
        var items = container.querySelectorAll('[data-message-id]');
        if (!items.length) return 0;
        return parseInt(items[items.length - 1].getAttribute('data-message-id'), 10);
    }

    function fetchNewMessages() {
        var lastId = getLastMessageId();
        fetch(messagesUrl + (lastId ? '?after_id=' + lastId : ''), {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.messages && data.messages.length) appendMessages(data.messages);
        }).catch(function() {});
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var body = (input && input.value) ? input.value.trim() : '';
        if (!body) return;
        if (submitBtn) { submitBtn.disabled = true; }
        if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: '_token=' + encodeURIComponent(csrf) + '&body=' + encodeURIComponent(body)
        }).then(function(r) {
            if (!r.ok) throw new Error(sendFail);
            return r.json();
        }).then(function(data) {
            if (data.success && data.message) {
                removeEmptyState();
                if (!container.querySelector('[data-message-id="' + data.message.id + '"]')) {
                    var wrap = document.createElement('div');
                    wrap.innerHTML = buildMessageHtml(data.message);
                    container.appendChild(wrap.firstElementChild);
                }
                container.scrollTop = container.scrollHeight;
            }
            if (input) input.value = '';
        }).catch(function(err) {
            if (errorEl) {
                errorEl.textContent = err.message || genericError;
                errorEl.classList.remove('hidden');
            }
        }).finally(function() {
            if (submitBtn) submitBtn.disabled = false;
            if (input) input.focus();
        });
    });

    container.scrollTop = container.scrollHeight;
    setInterval(fetchNewMessages, pollInterval);
})();
</script>
@endpush
@endsection
