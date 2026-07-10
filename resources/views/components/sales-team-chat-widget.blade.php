{{-- شات فريق المبيعات — ويدجت عائم --}}
@php
    $stcUser = auth()->user();
    $stcShow = $stcUser && $stcUser->isSalesStaff() && app(\App\Services\SalesTeamService::class)->teamFor($stcUser);
@endphp
@if($stcShow)
<div
    id="sales-team-chat-widget"
    x-data="salesTeamChat()"
    x-cloak
    class="fixed z-[60] bottom-5 {{ app()->getLocale() === 'ar' ? 'left-5' : 'right-5' }} font-sans stc-widget"
    style="font-family: 'Tajawal', 'Cairo', 'Noto Sans Arabic', sans-serif;"
>
    <style>
        #sales-team-chat-widget {
            --stc-blue: #1e40af;
            --stc-blue-deep: #1e3a8a;
            --stc-blue-bright: #1d4ed8;
            --stc-sky: #0ea5e9;
            --stc-sky-dark: #0284c7;
        }
        #sales-team-chat-widget .stc-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
        }
        #sales-team-chat-widget .stc-fab {
            background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 45%, #1e3a8a 100%);
            box-shadow: 0 8px 24px rgba(30, 64, 175, 0.35);
        }
        #sales-team-chat-widget .stc-fab:hover {
            box-shadow: 0 12px 28px rgba(14, 165, 233, 0.4);
        }
        #sales-team-chat-widget .stc-panel {
            border-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 20px 50px rgba(30, 58, 138, 0.18);
        }
        #sales-team-chat-widget .stc-thread-bg {
            background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 40%, #f8fafc 100%);
        }
    </style>
    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="stc-panel mb-3 w-[min(100vw-2rem,380px)] h-[min(72vh,560px)] bg-white rounded-2xl border overflow-hidden flex flex-col"
        @click.outside="if (!pickingMember) open = false"
    >
        {{-- Header --}}
        <div class="stc-header px-4 py-3 text-white flex items-center justify-between gap-2 shrink-0">
            <div class="min-w-0">
                <p class="text-sm font-black truncate" x-text="view === 'thread' ? (activeTitle || 'محادثة') : 'شات الفريق'"></p>
                <p class="text-[11px] text-sky-100/90 truncate" x-show="view !== 'thread'" x-text="team?.name || ''"></p>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <button type="button" x-show="view === 'thread'" @click="backToList()" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center" title="رجوع">
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
                <button type="button" @click="open = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center" title="إغلاق">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- List view --}}
        <div x-show="view === 'list'" class="flex-1 flex flex-col min-h-0 bg-sky-50/40">
            <div class="p-3 grid grid-cols-2 gap-2 shrink-0">
                <button type="button" @click="openTeamChannel()"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-white text-xs font-bold shadow-sm"
                        style="background: linear-gradient(135deg, #0ea5e9, #1d4ed8);">
                    <i class="fas fa-users"></i> قناة الفريق
                </button>
                <button type="button" @click="pickingMember = !pickingMember"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-white border border-blue-200 hover:bg-sky-50 text-blue-900 text-xs font-bold">
                    <i class="fas fa-comment-medical text-sky-600"></i> محادثة جديدة
                </button>
            </div>

            <div x-show="pickingMember" class="mx-3 mb-2 rounded-xl border border-blue-100 bg-white overflow-hidden shrink-0 max-h-40 overflow-y-auto">
                <p class="px-3 py-2 text-[11px] font-bold text-blue-800/70 border-b border-sky-50">اختر زميلاً</p>
                <template x-for="m in members.filter(x => !x.is_me)" :key="m.id">
                    <button type="button" @click="startDirect(m.id)"
                            class="w-full text-right px-3 py-2 text-sm hover:bg-sky-50 flex items-center justify-between gap-2 border-b border-slate-50 last:border-0">
                        <span class="font-semibold text-slate-800" x-text="m.name"></span>
                        <span x-show="m.is_manager" class="text-[10px] font-bold text-blue-700">مانجر</span>
                    </button>
                </template>
            </div>

            <div class="flex-1 overflow-y-auto px-3 pb-3 space-y-1.5">
                <template x-if="loading && conversations.length === 0">
                    <p class="text-center text-xs text-slate-400 py-8">جاري التحميل…</p>
                </template>
                <template x-for="c in conversations" :key="c.id">
                    <button type="button" @click="openConversation(c)"
                            class="w-full text-right rounded-xl border border-blue-100 bg-white px-3 py-2.5 hover:border-sky-400 hover:shadow-sm transition flex gap-3 items-start">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-sm"
                             :style="c.is_team
                                ? 'background: linear-gradient(135deg, #0ea5e9, #1d4ed8)'
                                : 'background: linear-gradient(135deg, #1e40af, #1e3a8a)'">
                            <i :class="c.is_team ? 'fas fa-bullhorn' : 'fas fa-user'"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900 truncate" x-text="c.title"></p>
                                <span x-show="c.unread > 0" class="text-[10px] font-black bg-rose-500 text-white rounded-full min-w-[1.25rem] h-5 px-1.5 flex items-center justify-center" x-text="c.unread"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5" x-text="c.last_message ? ((c.last_message.user_name || '') + ': ' + c.last_message.body) : 'لا رسائل بعد'"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Thread view --}}
        <div x-show="view === 'thread'" class="stc-thread-bg flex-1 flex flex-col min-h-0">
            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-2" x-ref="messagesBox">
                <template x-if="messagesLoading && messages.length === 0">
                    <p class="text-center text-xs text-slate-400 py-8">جاري تحميل الرسائل…</p>
                </template>
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex" :class="msg.is_mine ? 'justify-start' : 'justify-end'">
                        <div class="max-w-[85%] group">
                            <div class="rounded-2xl px-3 py-2 shadow-sm border"
                                 :class="msg.is_mine
                                    ? 'text-white border-transparent rounded-br-md'
                                    : 'bg-white text-slate-800 border-blue-100 rounded-bl-md'"
                                 :style="msg.is_mine ? 'background: linear-gradient(135deg, #1d4ed8, #1e40af)' : ''">
                                <p class="text-[10px] font-bold mb-0.5 opacity-80" x-show="!msg.is_mine" x-text="msg.user_name"></p>
                                <template x-if="msg.reply_to">
                                    <div class="mb-1.5 text-[11px] rounded-lg px-2 py-1 border"
                                         :class="msg.is_mine ? 'bg-white/15 border-white/25' : 'bg-sky-50 border-sky-100 text-slate-600'">
                                        <span class="font-bold" x-text="msg.reply_to.user_name"></span>
                                        <span x-text="': ' + msg.reply_to.body"></span>
                                    </div>
                                </template>
                                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words" x-text="msg.body"></p>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <span class="text-[10px] opacity-70 tabular-nums" x-text="msg.created_at_human"></span>
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                        <button type="button" @click="setReply(msg)" class="text-[10px] px-1.5 py-0.5 rounded hover:bg-black/10" title="رد">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button type="button" @click="toggleEmojiBar(msg.id)" class="text-[10px] px-1.5 py-0.5 rounded hover:bg-black/10" title="تفاعل">
                                            <i class="far fa-smile"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-1" :class="msg.is_mine ? 'justify-start' : 'justify-end'" x-show="msg.reactions && msg.reactions.length">
                                <template x-for="r in (msg.reactions || [])" :key="r.emoji">
                                    <button type="button" @click="react(msg.id, r.emoji)"
                                            class="text-[11px] px-1.5 py-0.5 rounded-full border bg-white shadow-sm"
                                            :class="r.mine ? 'border-sky-400' : 'border-slate-200'"
                                            x-text="r.emoji + ' ' + r.count"></button>
                                </template>
                            </div>
                            <div x-show="emojiFor === msg.id" class="mt-1 flex gap-1 bg-white border border-blue-100 rounded-xl px-2 py-1 shadow-sm"
                                 :class="msg.is_mine ? 'justify-start' : 'justify-end'">
                                <template x-for="e in quickEmojis" :key="e">
                                    <button type="button" class="text-base hover:scale-110 transition" @click="react(msg.id, e); emojiFor = null" x-text="e"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="replyTo" class="px-3 py-1.5 bg-sky-50 border-t border-sky-100 flex items-center justify-between gap-2 text-xs shrink-0">
                <p class="truncate text-blue-900">
                    رد على <strong x-text="replyTo?.user_name"></strong>:
                    <span x-text="replyTo?.body"></span>
                </p>
                <button type="button" @click="replyTo = null" class="text-sky-700"><i class="fas fa-times"></i></button>
            </div>

            <form @submit.prevent="sendMessage()" class="p-3 bg-white border-t border-blue-100 flex gap-2 shrink-0">
                <input type="text" x-model="draft" maxlength="4000"
                       placeholder="اكتب رسالة…"
                       class="flex-1 px-3 py-2.5 rounded-xl border border-blue-100 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                       :disabled="sending"
                       autocomplete="off">
                <button type="submit" :disabled="sending || !draft.trim()"
                        class="w-11 h-11 rounded-xl disabled:opacity-50 text-white flex items-center justify-center shrink-0"
                        style="background: linear-gradient(135deg, #0ea5e9, #1d4ed8);">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- FAB --}}
    <button type="button" @click="toggle()"
            class="stc-fab relative w-14 h-14 rounded-full text-white hover:scale-105 transition flex items-center justify-center border-2 border-white"
            title="شات الفريق">
        <i class="fas fa-comments text-xl" x-show="!open"></i>
        <i class="fas fa-times text-xl" x-show="open"></i>
        <span x-show="unreadTotal > 0 && !open"
              class="absolute -top-1 -right-1 min-w-[1.35rem] h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center border-2 border-white"
              x-text="unreadTotal > 99 ? '99+' : unreadTotal"></span>
    </button>
</div>

<script>
function salesTeamChat() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || '{{ csrf_token() }}';
    const routes = {
        bootstrap: @json(route('employee.team-chat.bootstrap')),
        conversations: @json(route('employee.team-chat.conversations')),
        unread: @json(route('employee.team-chat.unread')),
        openTeam: @json(route('employee.team-chat.conversations.team')),
        openDirect: @json(route('employee.team-chat.conversations.direct')),
        messages: (id) => @json(url('/employee/team-chat/conversations')).replace(/\/$/, '') + '/' + id + '/messages',
        send: (id) => @json(url('/employee/team-chat/conversations')).replace(/\/$/, '') + '/' + id + '/messages',
        read: (id) => @json(url('/employee/team-chat/conversations')).replace(/\/$/, '') + '/' + id + '/read',
        react: (id) => @json(url('/employee/team-chat/messages')).replace(/\/$/, '') + '/' + id + '/reactions',
    };

    return {
        open: false,
        view: 'list',
        loading: false,
        messagesLoading: false,
        sending: false,
        pickingMember: false,
        team: null,
        me: null,
        members: [],
        conversations: [],
        messages: [],
        activeId: null,
        activeTitle: '',
        draft: '',
        replyTo: null,
        emojiFor: null,
        unreadTotal: 0,
        lastMessageId: 0,
        pollTimer: null,
        unreadTimer: null,
        quickEmojis: ['👍', '❤️', '😂', '🔥', '👏', '✅'],

        init() {
            this.fetchUnread();
            this.unreadTimer = setInterval(() => {
                if (!this.open || this.view === 'list') this.fetchUnread();
            }, 15000);
        },

        async toggle() {
            this.open = !this.open;
            if (this.open) {
                await this.bootstrap();
                this.startPolling();
            } else {
                this.stopPolling();
                this.view = 'list';
                this.pickingMember = false;
            }
        },

        async api(url, options = {}) {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(options.body ? {'Content-Type': 'application/json'} : {}),
                },
                credentials: 'same-origin',
                ...options,
            });
            if (!res.ok) {
                let msg = 'حدث خطأ';
                try {
                    const j = await res.json();
                    msg = j.message || Object.values(j.errors || {})[0]?.[0] || msg;
                } catch (e) {}
                throw new Error(msg);
            }
            return res.json();
        },

        async bootstrap() {
            this.loading = true;
            try {
                const data = await this.api(routes.bootstrap);
                this.team = data.team;
                this.me = data.me;
                this.members = data.members || [];
                this.conversations = data.conversations || [];
                this.unreadTotal = data.unread_total || 0;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        async refreshList() {
            try {
                const data = await this.api(routes.conversations);
                this.conversations = data.conversations || [];
                this.unreadTotal = data.unread_total || 0;
            } catch (e) {}
        },

        async fetchUnread() {
            try {
                const data = await this.api(routes.unread);
                this.unreadTotal = data.unread_total || 0;
            } catch (e) {}
        },

        async openTeamChannel() {
            this.pickingMember = false;
            try {
                const data = await this.api(routes.openTeam, { method: 'POST', body: '{}' });
                await this.openConversation(data.conversation);
            } catch (e) {
                alert(e.message);
            }
        },

        async startDirect(userId) {
            this.pickingMember = false;
            try {
                const data = await this.api(routes.openDirect, {
                    method: 'POST',
                    body: JSON.stringify({ user_id: userId }),
                });
                await this.openConversation(data.conversation);
            } catch (e) {
                alert(e.message);
            }
        },

        async openConversation(c) {
            this.view = 'thread';
            this.activeId = c.id;
            this.activeTitle = c.title;
            this.messages = [];
            this.lastMessageId = 0;
            this.replyTo = null;
            this.emojiFor = null;
            this.messagesLoading = true;
            try {
                const data = await this.api(routes.messages(c.id));
                this.messages = data.messages || [];
                this.lastMessageId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                await this.api(routes.read(c.id), { method: 'POST', body: '{}' });
                this.$nextTick(() => this.scrollBottom());
                this.startPolling();
            } catch (e) {
                alert(e.message);
            } finally {
                this.messagesLoading = false;
            }
        },

        backToList() {
            this.view = 'list';
            this.activeId = null;
            this.messages = [];
            this.refreshList();
            this.startPolling();
        },

        setReply(msg) {
            this.replyTo = msg;
        },

        toggleEmojiBar(id) {
            this.emojiFor = this.emojiFor === id ? null : id;
        },

        async sendMessage() {
            const body = (this.draft || '').trim();
            if (!body || !this.activeId || this.sending) return;
            this.sending = true;
            try {
                const payload = { body };
                if (this.replyTo) payload.reply_to_id = this.replyTo.id;
                const data = await this.api(routes.send(this.activeId), {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                this.draft = '';
                this.replyTo = null;
                if (data.message) {
                    this.messages.push(data.message);
                    this.lastMessageId = data.message.id;
                    this.$nextTick(() => this.scrollBottom());
                }
            } catch (e) {
                alert(e.message);
            } finally {
                this.sending = false;
            }
        },

        async react(messageId, emoji) {
            try {
                const data = await this.api(routes.react(messageId), {
                    method: 'POST',
                    body: JSON.stringify({ emoji }),
                });
                if (data.message) {
                    const idx = this.messages.findIndex(m => m.id === messageId);
                    if (idx >= 0) this.messages[idx] = data.message;
                }
            } catch (e) {
                alert(e.message);
            }
        },

        async poll() {
            if (!this.open) return;
            if (this.view === 'list') {
                await this.refreshList();
                return;
            }
            if (!this.activeId) return;
            try {
                const url = routes.messages(this.activeId) + (this.lastMessageId ? ('?after_id=' + this.lastMessageId) : '');
                const data = await this.api(url);
                const incoming = data.messages || [];
                if (incoming.length) {
                    const existing = new Set(this.messages.map(m => m.id));
                    incoming.forEach(m => {
                        if (!existing.has(m.id)) this.messages.push(m);
                    });
                    this.lastMessageId = this.messages[this.messages.length - 1].id;
                    this.$nextTick(() => this.scrollBottom());
                    await this.api(routes.read(this.activeId), { method: 'POST', body: '{}' });
                }
            } catch (e) {}
        },

        startPolling() {
            this.stopPolling();
            this.pollTimer = setInterval(() => this.poll(), 2500);
        },

        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },

        scrollBottom() {
            const el = this.$refs.messagesBox;
            if (el) el.scrollTop = el.scrollHeight;
        },
    };
}
</script>
@endif
