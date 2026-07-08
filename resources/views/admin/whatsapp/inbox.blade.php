@extends($waLayout ?? 'layouts.admin')

@section('title', $waPageTitle ?? 'محادثات الواتساب - Mindlytics')
@section('header', $waPageHeader ?? 'قسم الواتساب')

@section('content')
@php
    $audience = $inboxAudience ?? 'admin';
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $webhookDiag = $connectionMeta['webhook'] ?? [];
    $webhookRateLimited = (bool) ($webhookDiag['rate_limited'] ?? false);
    $webhookRateLimitNotice = $webhookDiag['rate_limit_notice'] ?? null;
    $webhookIssues = ($webhookDiag['receiving_replies'] ?? false) || ($webhookDiag['webhook_reachable'] ?? false)
        ? array_values(array_filter($webhookDiag['issues'] ?? [], fn ($issue) => ! str_contains($issue, 'لم يصل أي طلب Webhook') && ! str_contains($issue, 'غير مشترك')))
        : ($webhookDiag['issues'] ?? []);
    $webhookIssues = array_values(array_filter($webhookIssues, function ($issue) {
        $issue = mb_strtolower((string) $issue);

        return ! str_contains($issue, 'application request limit')
            && ! str_contains($issue, 'request limit reached')
            && ! str_contains($issue, '(#4)');
    }));
    $webhookTips = $webhookDiag['tips'] ?? [];
    $webhookMeta = $webhookDiag['meta'] ?? [];
    $activeId = $activeConversation?->id;
    $inboxService = app(\App\Services\WhatsAppInboxService::class);
    $routes = $inboxRoutes ?? [];
    $initialConversations = $conversations->getCollection()->map(function ($c) use ($inboxService, $audience) {
        return rescue(fn () => $inboxService->serializeConversation($c, $audience), ['id' => $c->id], false);
    })->values();
    $mediaRouteName = ($audience ?? 'admin') === 'employee'
        ? 'employee.sales.whatsapp.inbox.media'
        : 'admin.whatsapp.inbox.media';
    $initialMessages = $messages->map(function ($m) use ($inboxService, $mediaRouteName) {
        $mediaUrl = null;
        if ($inboxService->messageHasMedia($m) && \Illuminate\Support\Facades\Route::has($mediaRouteName)) {
            try {
                $mediaUrl = route($mediaRouteName, ['conversation' => $m->conversation_id, 'message' => $m->id]);
            } catch (\Throwable) {
                $mediaUrl = null;
            }
        }

        return $inboxService->serializeMessage($m, $mediaUrl);
    })->values();
    $crmNotesInitial = [];
    $crmTimelineInitial = [];
    $crmUrlsInitial = $routes['crm'] ?? [];
    if ($activeConversation && ($crmReady ?? false)) {
        try {
            $crmService = app(\App\Services\WhatsAppCrmService::class);
            $crmNotesInitial = $activeConversation->notes()
                ->with('author:id,name')
                ->latest()
                ->limit(30)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'body' => $n->body,
                    'author' => $n->author?->name,
                    'created_at_human' => $n->created_at?->diffForHumans(),
                ])->values()->all();
            $crmTimelineInitial = $crmService->timeline($activeConversation);
        } catch (\Throwable $e) {
            report($e);
        }
    }
    $inboxConfig = [
        'conversationId' => $activeId,
        'activeConversation' => $activeConversation
            ? rescue(fn () => $inboxService->serializeConversation($activeConversation, $audience), null, false)
            : null,
        'conversations' => $initialConversations,
        'messages' => $initialMessages,
        'pollUrl' => $routes['poll'] ?? '',
        'conversationUrlTemplate' => $routes['conversationUrlTemplate'] ?? '',
        'replyUrl' => $routes['reply'] ?? null,
        'reactUrl' => $routes['react'] ?? null,
        'mediaUrl' => $routes['media'] ?? null,
        'templateUrl' => $routes['template'] ?? null,
        'startUrl' => $routes['start'] ?? '',
        'templatesUrl' => $routes['templates'] ?? '',
        'inboxUrl' => $routes['index'] ?? '',
        'csrf' => csrf_token(),
        'withinWindow' => (bool) ($withinWindow ?? false),
        'lastMessageId' => $messages->first()?->id ?? 0,
        'metaTemplates' => $metaTemplates ?? [],
        'crmReady' => (bool) ($crmReady ?? false),
        'crmUrls' => $crmUrlsInitial,
        'crmNotes' => $crmNotesInitial,
        'crmTimeline' => $crmTimelineInitial,
        'inboxAudience' => $audience,
        'startLeadId' => $startLead->id ?? null,
        'startLeadPhone' => $startLead->phone ?? null,
        'startLeadName' => $startLead->name ?? null,
        'voiceNoteConversionReady' => rescue(fn () => $inboxService->voiceNoteConversionReady(), false, false),
    ];
@endphp

<script>window.__waInboxConfig = @json($inboxConfig);</script>

<div class="wa-inbox-page flex flex-col min-h-0 overflow-hidden gap-2 {{ ($waImmersiveInbox ?? false) ? 'wa-inbox-immersive admin-wa-inbox' : '' }} {{ ($inboxAudience ?? '') === 'employee' ? 'sales-wa-inbox' : '' }}" x-data="whatsappInbox()" x-cloak>
    @include('admin.whatsapp._alerts')

    @if(empty($waHideWebhookBanner) && $webhookRateLimited)
        <div class="shrink-0 rounded-xl border border-sky-300 bg-sky-50 px-4 py-3 text-sm text-sky-950 space-y-1">
            <p class="font-bold flex items-center gap-2">
                <i class="fas fa-clock text-sky-600"></i>
                حد Meta للتشخيص مؤقتاً — ليس خطأ في Webhook
            </p>
            <p class="text-xs leading-relaxed">{{ $webhookRateLimitNotice ?? 'Meta رفضت طلب فحص اشتراكات التطبيق مؤقتاً بعد كثرة طلبات API. انتظر 15–60 دقيقة.' }}</p>
            <p class="text-[11px] text-sky-800">الإرسال واستقبال الردود عبر Webhook قد يعملان بشكل طبيعي. إن لم تظهر ردود عميل بعد ساعة، راجع إعدادات الربط.</p>
        </div>
    @endif

    @if(empty($waHideWebhookBanner) && (!empty($webhookIssues) || !empty($webhookTips)))
        <div class="shrink-0 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 space-y-2">
            @if(!empty($webhookIssues))
                <p class="font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                    ردود العملاء لا تظهر بعد في المحادثات
                </p>
                <ul class="list-disc list-inside text-xs space-y-1 leading-relaxed">
                    @foreach($webhookIssues as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                </ul>
            @endif
            @if(!empty($webhookMeta) && (($webhookMeta['messages_subscribed'] ?? null) !== null || !empty($webhookMeta['callback_url'])))
                <div class="text-[11px] rounded-lg bg-white/70 border border-amber-200 px-3 py-2 space-y-1">
                    <p><strong>فحص Meta:</strong>
                        messages = {{ ($webhookMeta['messages_subscribed'] ?? false) ? 'مشترك ✓' : 'غير مشترك ✗' }},
                        WABA = {{ ($webhookMeta['waba_app_subscribed'] ?? false) ? 'مشترك ✓' : 'غير مشترك ✗' }}
                    </p>
                    @if(!empty($webhookMeta['callback_url']))
                        <p class="dir-ltr break-all">Callback في Meta: {{ $webhookMeta['callback_url'] }}</p>
                    @endif
                </div>
            @endif
            @if(!empty($webhookTips))
                <ul class="list-disc list-inside text-[11px] text-amber-900 space-y-1">
                    @foreach($webhookTips as $tip)
                        <li>{{ $tip }}</li>
                    @endforeach
                </ul>
            @endif
            @if($audience === 'admin')
                <p class="text-xs leading-relaxed border-t border-amber-200 pt-2 flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.whatsapp.settings') }}#webhook-status-panel" class="underline font-semibold">إعدادات الربط</a>
                    <span>— استخدم «تحديث الحالة» أو «مزامنة الاشتراك مع Meta» لعرض حقول Webhook مباشرة من Meta.</span>
                </p>
            @endif
        </div>
    @endif

    {{-- شريط علوي مدمج --}}
    <div class="shrink-0 flex flex-wrap items-center justify-between gap-2 px-1 {{ ($waImmersiveInbox ?? false) ? 'px-2 sm:px-3 pt-2' : '' }}">
        <div class="flex items-center gap-3 min-w-0">
            @if($waImmersiveInbox ?? false)
            <button type="button" @click="$dispatch('open-sidebar')" class="lg:hidden w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 flex items-center justify-center shrink-0">
                <i class="fas fa-bars text-sm"></i>
            </button>
            @endif
            <div class="w-10 h-10 rounded-xl {{ ($waImmersiveInbox ?? false) ? 'bg-slate-100 border border-slate-200 text-emerald-600' : (($inboxAudience ?? '') === 'employee' ? 'bg-slate-100 border border-slate-200' : 'bg-gradient-to-br from-emerald-500 to-green-600 shadow-md') }} flex items-center justify-center {{ ($waImmersiveInbox ?? false) || ($inboxAudience ?? '') === 'employee' ? 'text-emerald-600' : 'text-white' }} shrink-0">
                <i class="{{ ($waImmersiveInbox ?? false) || ($inboxAudience ?? '') === 'employee' ? 'fab fa-whatsapp' : 'fas fa-inbox' }} text-sm"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-black text-slate-900 truncate">{{ $waInboxTitle ?? 'المحادثات الواردة' }}</h2>
                <p class="text-[11px] text-slate-500 truncate hidden sm:block">{{ $waInboxSubtitle ?? 'ردّ على العملاء وتابع الـ Pipeline' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
            @if(($inboxAudience ?? '') === 'employee')
            <a href="{{ $waEmployeeLeadsUrl ?? route('employee.sales.leads.index') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-slate-800 hover:bg-slate-900 text-white transition-colors">
                <i class="fas fa-user-plus"></i>
                <span class="hidden sm:inline">العملاء</span>
            </a>
            <a href="{{ $waEmployeeSalesUrl ?? route('employee.sales.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors hidden md:inline-flex">
                <i class="fas fa-chart-line text-slate-500"></i>
                <span>مركز المبيعات</span>
            </a>
            @elseif(($inboxAudience ?? '') === 'admin' && ($waImmersiveInbox ?? false))
            <a href="{{ $waAdminSettingsUrl ?? route('admin.whatsapp.settings') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-plug text-emerald-600"></i>
                <span class="hidden sm:inline">ربط Meta</span>
            </a>
            <a href="{{ $waAdminReportsUrl ?? route('admin.whatsapp.reports') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors hidden md:inline-flex">
                <i class="fas fa-chart-pie text-slate-500"></i>
                <span>التقارير</span>
            </a>
            @endif
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white border border-slate-200 text-slate-700">
                غير مقروء: <span x-text="unreadTotal">{{ (int) $unreadTotal }}</span>
            </span>
            <button type="button" @click="showStartModal = true" class="{{ $waBtnPrimary }} text-xs sm:text-sm !py-2 !px-3">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline">محادثة جديدة</span>
            </button>
        </div>
    </div>

    @if(! $tablesReady)
        <div class="shrink-0 rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-bold">تشغيل الترحيل مطلوب</p>
            <p class="mt-1">نفّذ: <code class="bg-white px-2 py-0.5 rounded">php artisan migrate --force</code></p>
        </div>
    @elseif(! $canSend && ($inboxAudience ?? 'admin') === 'admin')
        {{-- تحذير Meta يُعرض داخل قائمة المحادثات --}}
    @elseif(! $canSend)
        <div class="shrink-0 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
            إرسال الواتساب غير متاح — تواصل مع الإدارة.
        </div>
    @endif

    {{-- الهيكل: قائمة يمين | محادثة وسط | CRM يسار (في RTL) --}}
    <div class="wa-inbox-shell flex-1 min-h-0 overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">

        {{-- قائمة المحادثات — يمين الشاشة في RTL --}}
        <aside class="wa-conv-sidebar wa-inbox-col border-s border-slate-200/80 bg-[#f0f2f5]"
               :class="(conversationId && !showSidebarMobile) ? 'max-lg:hidden' : ''">

            {{-- رأس القائمة --}}
            <div class="wa-conv-header shrink-0 border-b border-slate-200/80 bg-[#f0f2f5]">
                @if(! $canSend && ($inboxAudience ?? 'admin') === 'admin')
                <a href="{{ route('admin.whatsapp.settings') }}"
                   class="flex items-center gap-2 mx-2.5 mt-2.5 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 hover:bg-amber-100 transition-colors">
                    <i class="fas fa-exclamation-triangle text-amber-600 shrink-0"></i>
                    <span class="font-semibold">الربط غير مكتمل — <span class="underline">إعداد Meta</span></span>
                </a>
                @endif

                <div class="flex items-center justify-between gap-2 px-3 pt-2.5 pb-1">
                    <h3 class="text-sm font-black text-slate-800">المحادثات</h3>
                    <span class="text-[10px] font-bold text-slate-500 bg-white/80 border border-slate-200/60 rounded-full px-2 py-0.5 tabular-nums"
                          x-text="conversations.length + ' محادثة'"></span>
                </div>

                <div class="px-2.5 pb-2.5">
                    <div class="relative">
                        <i class="fas fa-search absolute start-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="search" x-model="searchQuery" @input.debounce.350ms="searchConversations()"
                               placeholder="بحث بالاسم أو الرقم..."
                               class="w-full rounded-xl border-0 bg-white ps-9 pe-3 py-2.5 text-sm shadow-sm ring-1 ring-slate-200/60 focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                    </div>

                    @if($crmReady ?? false)
                    <div class="mt-2 grid grid-cols-2 gap-1.5">
                        <select x-model="filterStatus" @change="applyFilters()"
                                class="col-span-1 text-[11px] rounded-lg border-0 bg-white px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 text-slate-700">
                            <option value="">كل الحالات</option>
                            @foreach($crmStatuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @if(empty($waHideAdminFilters))
                        <select x-model="filterAssigned" @change="applyFilters()"
                                class="col-span-1 text-[11px] rounded-lg border-0 bg-white px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 text-slate-700">
                            <option value="">كل الموظفين</option>
                            <option value="unassigned">غير معيّنة</option>
                            @foreach($crmAgents as $agent)
                                <option value="{{ $agent['id'] }}">{{ $agent['name'] }}</option>
                            @endforeach
                        </select>
                        <label class="col-span-2 inline-flex items-center justify-center gap-1.5 text-[11px] bg-white rounded-lg px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 cursor-pointer text-slate-600">
                            <input type="checkbox" x-model="filterMine" @change="applyFilters()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            محادثاتي فقط
                        </label>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- قائمة الشاتات فقط — scroll هنا --}}
            <div class="wa-conv-list"
                 @wheel.stop
                 @touchmove.stop>
                <template x-if="loadingList">
                    <div class="p-10 text-center text-slate-400 text-sm"><i class="fas fa-spinner fa-spin text-emerald-600"></i><p class="mt-2">جاري التحميل...</p></div>
                </template>
                <template x-if="!loadingList && conversations.length === 0">
                    <div class="p-10 text-center text-slate-500 text-sm">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                            <i class="fas fa-inbox text-2xl text-slate-300"></i>
                        </div>
                        <p class="font-semibold text-slate-700">لا توجد محادثات</p>
                        <p class="text-xs text-slate-400 mt-1">ابدأ محادثة جديدة من الزر أعلاه</p>
                    </div>
                </template>
                <template x-for="conv in conversations" :key="'c-' + conv.id">
                    <button type="button" @click="selectConversation(conv.id)"
                            class="wa-conv-item w-full text-right px-3 py-3 flex gap-3 items-center border-b border-slate-100/80 transition-colors hover:bg-[#f5f6f6]"
                            :class="conversationId === conv.id ? 'wa-conv-item--active' : ''">
                        <div class="min-w-0 flex-1 order-2">
                            <div class="flex items-baseline justify-between gap-2 mb-0.5">
                                <p class="font-bold text-slate-900 truncate text-[13px] leading-tight" x-text="conv.display_name"></p>
                                <span class="text-[10px] text-slate-400 shrink-0 whitespace-nowrap tabular-nums"
                                      x-text="conv.last_message_at_human || ''"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 dir-ltr text-end font-mono truncate leading-tight mb-1"
                               x-show="conv.formatted_phone && conv.display_name !== conv.formatted_phone"
                               x-text="conv.formatted_phone"></p>
                            <div class="flex items-center gap-1 mb-1 flex-wrap" x-show="conv.crm">
                                <span class="text-[9px] px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium"
                                      x-text="conv.crm?.status_label"></span>
                                <span class="text-[9px] text-emerald-700 font-medium truncate max-w-[8rem]"
                                      x-show="conv.crm?.assignee_name" x-text="conv.crm?.assignee_name"></span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[12px] text-slate-500 truncate leading-snug flex-1"
                                   x-text="conv.last_message_preview || '—'"></p>
                                <span x-show="conv.unread_count > 0"
                                      class="inline-flex items-center justify-center min-w-[1.125rem] h-[1.125rem] px-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold shrink-0 shadow-sm"
                                      x-text="conv.unread_count"></span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full shrink-0 order-1 flex items-center justify-center font-bold text-sm shadow-sm"
                             :class="conversationId === conv.id ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700'"
                             x-text="(conv.display_name || '?').charAt(0).toUpperCase()"></div>
                    </button>
                </template>
            </div>
        </aside>

        {{-- نافذة المحادثة — ارتفاع ثابت، بدون scroll للصفحة --}}
        <section class="wa-inbox-col wa-chat-panel overflow-hidden bg-[#efeae2]"
                 :class="conversationId ? '' : 'max-lg:hidden'">

            {{-- حالة: لا محادثة مختارة --}}
            <div x-show="!conversationId && !loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5]">
                <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                    <i class="fab fa-whatsapp text-5xl text-emerald-500"></i>
                </div>
                <p class="font-semibold text-slate-700 text-lg">محادثات الواتساب</p>
                <p class="text-sm text-slate-500 mt-1 text-center max-w-sm">اختر محادثة من القائمة أو ابدأ محادثة جديدة بقالب Meta</p>
                <button type="button" @click="showStartModal = true" class="{{ $waBtnPrimary }} mt-5 text-sm">اكتب رسالة جديدة</button>
            </div>

            {{-- حالة: تحميل محادثة --}}
            <div x-show="loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex items-center justify-center bg-[#efeae2]">
                <div class="text-center text-slate-500">
                    <i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
                    <p class="text-sm">جاري تحميل المحادثة...</p>
                </div>
            </div>

            {{-- المحادثة النشطة --}}
            <div x-show="conversationId && activeConversation && !loadingConversation" x-cloak
                 class="wa-chat-active">
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

                    {{-- الرسائل — scroll داخلي فقط داخل الإطار، لا يمدّ الصفحة --}}
                    <div id="chat-messages" class="wa-chat-messages p-3 sm:p-4">
                        <div x-show="chatMessages.length === 0" x-cloak
                             class="flex flex-col items-center justify-center py-16 text-slate-500 text-sm">
                            <i class="fas fa-comments text-3xl text-slate-300 mb-2"></i>
                            <p>لا رسائل بعد — ابدأ المحادثة بالرد من الأسفل</p>
                        </div>
                        <div class="space-y-1">
                            <template x-for="msg in chatMessages" :key="'m-' + (msg.id || msg._tmp)">
                                <div class="flex items-end gap-1 group/msg"
                                     :class="msg.is_inbound ? 'justify-start' : 'justify-end'">
                                    <div class="relative max-w-[min(85%,18rem)] w-fit">
                                        <div class="wa-msg-bubble"
                                             :class="msg.is_inbound
                                                ? 'wa-msg-bubble--in'
                                                : (msg._pending ? 'wa-msg-bubble--out wa-msg-bubble--pending' : 'wa-msg-bubble--out')">
                                            <template x-if="msg.context_preview">
                                                <div class="wa-msg-quote" x-text="msg.context_preview"></div>
                                            </template>
                                            <template x-if="msg.media?.url && (msg.media.kind === 'image' || msg.message_type === 'sticker')">
                                                <a :href="msg.media.url" target="_blank" rel="noopener" class="block mb-1">
                                                    <img :src="msg.media.url" class="rounded-lg max-w-full max-h-52 object-cover" alt="صورة">
                                                </a>
                                            </template>
                                            <template x-if="msg.media?.url && msg.media.kind === 'audio'">
                                                <audio :src="msg.media.url" controls preload="metadata" class="wa-msg-audio w-full min-w-[11rem] max-w-full mb-1"></audio>
                                            </template>
                                            <template x-if="msg.media?.url && msg.media.kind === 'video'">
                                                <video :src="msg.media.url" controls class="rounded-lg max-w-full max-h-52 mb-1"></video>
                                            </template>
                                            <template x-if="msg.media?.url && msg.media.kind === 'document'">
                                                <a :href="msg.media.url" target="_blank" rel="noopener"
                                                   class="flex items-center gap-2 text-xs text-sky-700 hover:underline mb-1">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span x-text="msg.media.filename || 'تحميل المستند'"></span>
                                                </a>
                                            </template>
                                            <p class="wa-msg-text" x-show="shouldShowBody(msg)" x-text="msg.body"></p>
                                            <template x-if="msg.template_name">
                                                <p class="wa-msg-meta-extra" x-text="'قالب: ' + msg.template_name"></p>
                                            </template>
                                            <template x-if="msg.error_message && msg.status === 'failed'">
                                                <p class="wa-msg-error" x-text="msg.error_message"></p>
                                            </template>
                                            <span class="wa-msg-meta">
                                                <span x-show="msg._pending" class="text-emerald-600">···</span>
                                                <span x-show="msg.sent_by && !msg.is_inbound && !msg._pending" x-text="msg.sent_by"></span>
                                                <span x-text="msg.created_at_human || ''"></span>
                                                <template x-if="!msg.is_inbound && !msg._pending && msg.status === 'read'">
                                                    <i class="fas fa-check-double text-sky-500"></i>
                                                </template>
                                                <template x-if="!msg.is_inbound && !msg._pending && msg.status === 'delivered'">
                                                    <i class="fas fa-check-double text-slate-400"></i>
                                                </template>
                                                <template x-if="!msg.is_inbound && !msg._pending && msg.status === 'sent'">
                                                    <i class="fas fa-check text-slate-400"></i>
                                                </template>
                                            </span>
                                        </div>
                                        <div x-show="msg.reaction_emoji" class="wa-msg-reaction" x-text="msg.reaction_emoji"></div>
                                        <div x-show="reactPickerFor === msg.id" x-cloak
                                             class="absolute z-20 flex gap-0.5 bg-white rounded-full shadow-lg border border-slate-200 px-1.5 py-1 -bottom-9 inset-inline-start-0">
                                            <template x-for="em in quickEmojis" :key="em">
                                                <button type="button" @click="sendReaction(msg, em)"
                                                        class="w-7 h-7 hover:bg-slate-100 rounded-full text-base leading-none" x-text="em"></button>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="wa-msg-actions opacity-0 group-hover/msg:opacity-100 transition-opacity flex flex-col gap-0.5 shrink-0"
                                         x-show="!msg._pending && msg.id">
                                        <button type="button" @click="startReply(msg)" title="رد"
                                                class="w-7 h-7 rounded-full bg-white/90 shadow text-slate-500 hover:text-emerald-600 text-xs">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button type="button" x-show="msg.is_inbound" @click="toggleReactPicker(msg)" title="تفاعل"
                                                class="w-7 h-7 rounded-full bg-white/90 shadow text-slate-500 hover:text-amber-500 text-xs">
                                            <i class="far fa-smile"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- شريط الإرسال --}}
                    <div class="wa-chat-composer shrink-0 bg-[#f0f2f5] px-3 py-2 sm:px-4 sm:py-3 border-t border-slate-200">
                        <div x-show="replyingTo" x-cloak
                             class="flex items-center gap-2 text-xs bg-white border border-emerald-200 border-r-4 border-r-emerald-500 rounded-lg px-3 py-2 mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-emerald-700">رد على</p>
                                <p class="text-slate-600 truncate" x-text="replyingTo?.body"></p>
                            </div>
                            <button type="button" @click="cancelReply()" class="text-slate-400 hover:text-slate-600 px-2">×</button>
                        </div>
                        <div x-show="replyError" x-cloak class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2 mb-2" x-text="replyError"></div>
                        <p x-show="!withinWindow" x-cloak class="text-[10px] text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5 mb-2">
                            إذا لم يرد العميل خلال 24 ساعة، قد يرفض Meta الرسالة النصية — جرّب الإرسال أو استخدم قالباً من الأسفل.
                        </p>

                        {{-- معاينة رسالة صوتية قبل الإرسال --}}
                        <div x-show="showVoicePreview" x-cloak
                             class="mb-2 bg-white border border-emerald-200 rounded-2xl px-3 py-3 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <i class="fas fa-microphone text-sm"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-emerald-800">معاينة الرسالة الصوتية</p>
                                    <p class="text-[10px] text-slate-500">ستُرسل كـ Voice Note في واتساب (OGG/Opus)</p>
                                </div>
                            </div>
                            <audio :src="voicePreviewUrl" controls preload="metadata" class="wa-msg-audio w-full mb-2"></audio>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="cancelVoicePreview()"
                                        class="text-xs px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200">
                                    <i class="fas fa-trash-alt ml-1"></i> حذف
                                </button>
                                <button type="button" @click="sendVoicePreview()" :disabled="sending"
                                        class="text-xs px-4 py-1.5 rounded-full bg-emerald-500 text-white font-semibold hover:bg-emerald-600 disabled:opacity-40">
                                    <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                                    <span class="mr-1">إرسال</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-end gap-2" :class="showVoicePreview ? 'opacity-50 pointer-events-none' : ''">
                            <input type="file" x-ref="mediaInput" class="hidden" accept="image/jpeg,image/png,image/webp,audio/ogg,audio/mpeg,audio/mp4,audio/aac,audio/amr,audio/webm" @change="onMediaPicked($event)">
                            <input type="file" x-ref="audioInput" class="hidden" accept="audio/ogg,audio/mpeg,audio/mp4,audio/aac,audio/amr,audio/webm,.ogg,.mp3,.m4a,.aac,.amr,.webm" @change="onMediaPicked($event)">
                            <div class="flex flex-col gap-1 shrink-0">
                                <button type="button" @click="$refs.mediaInput.click()" :disabled="sending || !conversationId"
                                        title="إرفاق صورة أو صوت"
                                        class="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-emerald-600 hover:border-emerald-300 flex items-center justify-center shadow-sm disabled:opacity-40">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <button type="button" @click="toggleRecording()" :disabled="sending || !conversationId"
                                        :title="recording ? 'إيقاف التسجيل' : 'تسجيل صوت'"
                                        class="w-10 h-10 rounded-full border flex items-center justify-center shadow-sm disabled:opacity-40"
                                        :class="recording ? 'bg-rose-500 border-rose-500 text-white animate-pulse' : 'bg-white border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-300'">
                                    <i class="fas" :class="recording ? 'fa-stop' : 'fa-microphone'"></i>
                                </button>
                            </div>
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

                        <div x-show="showTemplatePicker" x-cloak class="mt-2 space-y-2">
                            <div class="flex gap-2">
                                <select x-model="selectedTemplateKey" @change="applySelectedTemplate()"
                                        class="flex-1 rounded-full bg-white px-3 py-1.5 text-xs shadow-sm border-0">
                                    <option value="">قالب Meta...</option>
                                    <template x-for="t in metaTemplates" :key="'tp-' + t.name + t.language">
                                        <option :value="t.name + '|' + t.language" x-text="t.label"></option>
                                    </template>
                                </select>
                                <button type="button" @click="sendTemplate()" :disabled="sending || !templateName || !templateVariablesReady()"
                                        class="text-xs px-3 py-1.5 rounded-full bg-white text-emerald-700 font-semibold shadow-sm disabled:opacity-50">إرسال قالب</button>
                            </div>
                            <template x-if="selectedTemplateMeta()">
                                <div class="rounded-xl bg-white/95 border border-emerald-100 p-3 space-y-2 text-xs">
                                    <p class="text-slate-600 dir-ltr text-left" x-show="selectedTemplateMeta().body_text" x-text="selectedTemplateMeta().body_text"></p>
                                    <p class="text-[10px] text-slate-500">اللغة: <span class="font-mono" x-text="templateLang"></span></p>
                                    <template x-if="selectedTemplateMeta().header_variable_count > 0">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">متغير العنوان (Header)</label>
                                            <input type="text" x-model="templateVariables.header_1" placeholder="قيمة عنوان القالب"
                                                   class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                        </div>
                                    </template>
                                    <template x-for="i in templateBodyVarSlots()" :key="'tv-' + i">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-1" x-text="'متغير ' + i"></label>
                                            <input type="text" x-model="templateVariables[i]"
                                                   class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <p x-show="micWaiting" x-cloak class="mt-1.5 text-[10px] text-sky-800 bg-sky-50 border border-sky-100 rounded-lg px-3 py-1.5 flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> اختر <strong>السماح</strong> في رسالة المتصفح التي ظهرت أعلى الصفحة
                        </p>
                        <p x-show="recording" x-cloak class="mt-1.5 text-[10px] text-rose-700 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span>جاري التسجيل</span>
                            <span class="font-mono font-semibold" x-text="'(' + recordSeconds + 's)'"></span>
                            <span>— اضغط إيقاف للمعاينة ثم الإرسال</span>
                        </p>
                        <p x-show="!voiceNoteConversionReady && !recording && !showVoicePreview" x-cloak
                           class="mt-1.5 text-[10px] text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5">
                            لتسجيل رسائل صوتية (Voice Note) من Chrome/Edge، ثبّت <strong>ffmpeg</strong> على السيرفر أو عيّن <code class="text-[9px]">FFMPEG_PATH</code> في .env
                        </p>
                        <button type="button" @click="showTemplatePicker = !showTemplatePicker"
                                class="mt-1.5 text-[10px] text-slate-500 hover:text-emerald-600 px-1">
                            <i class="fas fa-file-alt"></i> <span x-text="showTemplatePicker ? 'إخفاء القوالب' : 'إرسال بقالب Meta (اختياري)'"></span>
                        </button>
                    </div>
                </div>

            {{-- خطأ أو محادثة غير محمّلة --}}
            <div x-show="conversationId && !activeConversation && !loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5]">
                <i class="fas fa-exclamation-circle text-3xl text-amber-500 mb-3"></i>
                <p class="font-semibold text-slate-700">تعذّر عرض المحادثة</p>
                <p class="text-sm text-slate-500 mt-1" x-text="replyError || 'اختر محادثة أخرى من القائمة'"></p>
                <button type="button" @click="selectConversation(conversationId)" class="{{ $waBtnPrimary }} mt-4 text-sm">إعادة المحاولة</button>
            </div>
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
                    <template x-if="selectedTemplateMeta() && (selectedTemplateMeta().body_variable_count > 0 || selectedTemplateMeta().header_variable_count > 0)">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 space-y-2">
                            <template x-if="selectedTemplateMeta().header_variable_count > 0">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600">متغير العنوان</label>
                                    <input type="text" x-model="templateVariables.header_1" class="{{ $waInputClass }} text-sm mt-1">
                                </div>
                            </template>
                            <template x-for="i in templateBodyVarSlots()" :key="'stv-' + i">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600" x-text="'متغير ' + i"></label>
                                    <input type="text" x-model="templateVariables[i]" class="{{ $waInputClass }} text-sm mt-1">
                                </div>
                            </template>
                        </div>
                    </template>
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

    {{-- نافذة إذن الميكروفون --}}
    <div x-show="showMicModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="closeMicModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.outside="closeMicModal()">
            <div class="px-6 pt-6 pb-4 border-b border-slate-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                         :class="micModalMode === 'denied' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                        <i class="fas text-xl" :class="micModalMode === 'requesting' ? 'fa-spinner fa-spin' : (micModalMode === 'denied' ? 'fa-microphone-slash' : 'fa-microphone')"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-lg text-slate-900" x-text="micModalTitle()"></h3>
                        <p class="text-sm text-slate-600 mt-1" x-text="micModalSubtitle()"></p>
                    </div>
                    <button type="button" @click="closeMicModal()" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="px-6 py-4 space-y-3">
                <template x-if="micModalMode === 'denied'">
                    <div class="space-y-3 text-sm text-slate-700">
                        <div class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3 text-amber-950 leading-relaxed">
                            <p class="font-semibold mb-1">لماذا لا يكفي الضغط هنا؟</p>
                            <p>بعد حظر الميكروفون، <strong>المتصفح فقط</strong> يتحكم في الإذن — لا يمكن لأي موقع فتح الإعدادات نيابةً عنك (قيد أمني في Chrome وEdge وFirefox).</p>
                        </div>
                        <p class="font-semibold text-slate-900">بدون إعدادات المتصفح — أرسل صوتاً الآن:</p>
                        <button type="button" @click="pickAudioFile()"
                                class="w-full flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-emerald-300 bg-emerald-50 text-emerald-800 font-semibold py-3 hover:bg-emerald-100 transition-colors">
                            <i class="fas fa-file-audio"></i> إرفاق ملف صوتي (بدون ميكروفون)
                        </button>
                        <p class="text-xs text-slate-500 text-center">أو فعّل الميكروفون مرة واحدة من القفل 🔒 بجانب العنوان ثم أعد التحميل</p>
                    </div>
                </template>

                <template x-if="micModalMode === 'error'">
                    <div class="rounded-xl bg-rose-50 border border-rose-100 px-4 py-3 text-sm text-rose-800" x-text="micModalMessage"></div>
                </template>

                <p x-show="micModalMessage && micModalMode === 'denied'" class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-lg px-3 py-2" x-text="micModalMessage"></p>
            </div>

            <div class="px-6 pb-6 flex flex-wrap gap-2 justify-end">
                <button type="button" @click="closeMicModal()" class="{{ $waBtnSecondary }} text-sm">إغلاق</button>
                <button type="button"
                        x-show="micModalMode === 'denied'"
                        @click="reloadForMic()"
                        class="{{ $waBtnSecondary }} text-sm">
                    <i class="fas fa-rotate-right ml-1"></i> إعادة تحميل
                </button>
                <button type="button"
                        x-show="micModalMode === 'denied'"
                        @click="retryMicAccess()"
                        class="{{ $waBtnPrimary }} text-sm">
                    <i class="fas fa-microphone ml-1"></i> طلب الإذن مجدداً
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    main:has(.wa-inbox-page) {
        overflow: hidden !important;
    }
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
    @media (min-width: 640px) {
        .wa-inbox-page {
            height: calc(100dvh - 4.75rem);
            max-height: calc(100dvh - 4.75rem);
        }
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
    body.wa-immersive-inbox .wa-inbox-shell,
    body.wa-immersive-inbox .admin-wa-inbox .wa-inbox-shell {
        border-radius: 1rem;
        border-color: #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }
    .sales-wa-inbox .wa-inbox-shell,
    .admin-wa-inbox .wa-inbox-shell {
        border-color: #e2e8f0;
    }
    .sales-wa-inbox .wa-conv-sidebar,
    .admin-wa-inbox .wa-conv-sidebar {
        background: #f8fafc;
    }
    .sales-wa-inbox .wa-conv-header,
    .admin-wa-inbox .wa-conv-header {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .wa-inbox-shell {
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: minmax(0, 1fr);
        flex: 1 1 auto;
        min-height: 0;
        max-height: 100%;
        overflow: hidden;
    }
    @media (min-width: 1024px) {
        .wa-inbox-shell {
            grid-template-columns: minmax(280px, 320px) minmax(0, 1fr);
        }
    }
    @media (min-width: 1280px) {
        .wa-inbox-shell {
            grid-template-columns: minmax(280px, 320px) minmax(0, 1fr) minmax(260px, 300px);
        }
    }
    .wa-inbox-col {
        min-height: 0;
        min-width: 0;
        max-height: 100%;
        overflow: hidden;
    }
    /* ===== القائمة الجانبية فقط: scroll هنا ===== */
    .wa-conv-sidebar {
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
        max-height: 100%;
        min-height: 0;
        overflow: hidden;
    }
    .wa-conv-header {
        flex-shrink: 0;
    }
    .wa-conv-list {
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
        background: #fff;
        scrollbar-width: thin;
        scrollbar-color: #64748b #e2e8f0;
    }
    .wa-conv-list::-webkit-scrollbar { width: 8px; }
    .wa-conv-list::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 999px; }
    .wa-conv-list::-webkit-scrollbar-thumb { background: #64748b; border-radius: 999px; }
    .wa-conv-list::-webkit-scrollbar-thumb:hover { background: #475569; }

    /* ===== نافذة المحادثة: بدون scroll للصفحة ===== */
    .wa-chat-panel {
        display: grid;
        grid-template-rows: minmax(0, 1fr);
        min-height: 0;
        max-height: 100%;
        overflow: hidden;
    }
    .wa-chat-panel__pane {
        min-height: 0;
        overflow: hidden;
    }
    .wa-chat-active {
        display: grid;
        grid-template-rows: auto minmax(0, 1fr) auto;
        min-height: 0;
        max-height: 100%;
        overflow: hidden;
    }
    .wa-chat-messages {
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }
    .wa-chat-composer {
        flex-shrink: 0;
    }
    .wa-conv-item--active {
        background-color: #f0f2f5 !important;
    }
    .wa-conv-item--active::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #10b981;
        border-radius: 0 4px 4px 0;
    }
    .wa-conv-item {
        position: relative;
    }
    html[dir="ltr"] .wa-inbox-shell {
        grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
    }
    @media (min-width: 1280px) {
        html[dir="ltr"] .wa-inbox-shell {
            grid-template-columns: minmax(260px, 300px) minmax(0, 1fr) minmax(280px, 320px);
        }
        html[dir="ltr"] .wa-conv-sidebar { order: 3; }
        html[dir="ltr"] .wa-chat-panel { order: 2; }
        html[dir="ltr"] .wa-crm-sidebar { order: 1; }
    }
    @media (max-width: 1023px) {
        .wa-inbox-shell {
            position: relative;
        }
        .wa-conv-sidebar,
        .wa-chat-panel {
            grid-column: 1;
            grid-row: 1;
        }
        .wa-chat-panel {
            z-index: 2;
        }
    }
    [x-cloak] { display: none !important; }
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
    .wa-msg-bubble--in {
        background: #fff;
        color: #111b21;
        border-top-left-radius: 0;
    }
    .wa-msg-bubble--out {
        background: #d9fdd3;
        color: #111b21;
        border-top-right-radius: 0;
    }
    .wa-msg-bubble--pending {
        opacity: 0.75;
        outline: 1px solid #86efac;
    }
    .wa-msg-text {
        white-space: pre-wrap;
        padding-inline-end: 52px;
        min-height: 1.35em;
    }
    .wa-msg-quote {
        font-size: 12px;
        line-height: 1.3;
        padding: 4px 6px;
        margin-bottom: 4px;
        border-inline-start: 3px solid #10b981;
        background: rgba(0,0,0,0.04);
        border-radius: 4px;
        color: #54656f;
        max-height: 2.6em;
        overflow: hidden;
    }
    .wa-msg-meta {
        float: left;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 2px;
        margin-inline-start: 6px;
        font-size: 10px;
        line-height: 1;
        color: rgba(17, 27, 33, 0.45);
        white-space: nowrap;
        vertical-align: bottom;
    }
    .wa-msg-meta-extra { font-size: 10px; opacity: 0.7; margin-top: 2px; }
    .wa-msg-error { font-size: 10px; color: #e11d48; margin-top: 2px; }
    .wa-msg-reaction {
        position: absolute;
        bottom: -6px;
        inset-inline-end: 8px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0 5px;
        font-size: 13px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    .wa-msg-audio { height: 36px; }
    .group\/msg { position: relative; }
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
        chatMessages: (function () {
            const list = Array.isArray(cfg.messages) ? [...cfg.messages] : [];
            return list.sort((a, b) => {
                const aTs = Date.parse(a?.created_at || '') || 0;
                const bTs = Date.parse(b?.created_at || '') || 0;
                if (aTs !== bTs) return bTs - aTs;
                return (b?.id || 0) - (a?.id || 0);
            });
        })(),
        pollUrl: cfg.pollUrl,
        conversationUrlTemplate: cfg.conversationUrlTemplate,
        replyUrl: cfg.replyUrl,
        reactUrl: cfg.reactUrl,
        mediaUrl: cfg.mediaUrl,
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
        replyingTo: null,
        reactPickerFor: null,
        quickEmojis: ['👍', '❤️', '😂', '😮', '😢', '🙏'],
        templateName: '',
        templateLang: '',
        templateVariables: {},
        selectedTemplateKey: '',
        startPhone: '',
        startBody: '',
        startTemplate: '',
        startLang: '',
        replyError: '',
        startError: '',
        sending: false,
        recording: false,
        mediaRecorder: null,
        recordChunks: [],
        recorderMime: '',
        recordStream: null,
        recordSeconds: 0,
        recordTimer: null,
        showVoicePreview: false,
        voicePreviewUrl: null,
        voicePreviewFile: null,
        showMicModal: false,
        micModalMode: 'denied',
        micModalMessage: '',
        micPermissionState: 'unknown',
        micWaiting: false,
        loadingConversation: false,
        loadingList: false,
        showStartModal: false,
        showStartTemplatePicker: false,
        showSidebarMobile: false,
        showTemplatePicker: false,
        pollTimer: null,
        searchTimer: null,
        crmReady: !!cfg.crmReady,
        crmUrls: cfg.crmUrls || {},
        crmNotes: cfg.crmNotes || [],
        crmTimeline: cfg.crmTimeline || [],
        crmStatus: '',
        crmLeadStage: '',
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
        inboxAudience: cfg.inboxAudience || 'admin',
        startLeadId: cfg.startLeadId || null,
        startLeadPhone: cfg.startLeadPhone || '',
        startLeadName: cfg.startLeadName || '',
        voiceNoteConversionReady: !!cfg.voiceNoteConversionReady,

        init() {
            this.bootstrapTemplates();
            if (this.activeConversation) {
                this.syncCrmFromConversation(this.activeConversation);
            }

            const urlConv = new URLSearchParams(window.location.search).get('conversation');
            const urlConvId = urlConv ? parseInt(urlConv, 10) : 0;
            if (urlConvId > 0) {
                if (this.activeConversation && urlConvId === this.conversationId && this.chatMessages.length > 0) {
                    this.pushUrl(urlConvId);
                    this.$nextTick(() => this.scrollChat(false));
                } else {
                    this.selectConversation(urlConvId, false);
                }
            } else if (this.conversationId) {
                this.pushUrl(this.conversationId);
                this.$nextTick(() => this.scrollChat(false));
            }

            this.pollTimer = setInterval(() => this.poll(), 6000);

            if (this.startLeadId && this.startLeadPhone) {
                this.startPhone = this.startLeadPhone;
                this.showStartModal = true;
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

        /** مسار إرسال الوسائط — مسار نسبي على نفس الصفحة (يتجنب redirect على الاستضافة المشتركة) */
        mediaSendUrl(id) {
            const cid = id || this.conversationId;
            if (!cid) return null;

            if (this.mediaUrl) {
                try {
                    const u = new URL(this.mediaUrl, window.location.origin);
                    return u.pathname;
                } catch (_) {}
            }

            const conv = this.conversationUrl(cid);
            if (!conv) return null;

            try {
                const path = (conv.startsWith('http') ? new URL(conv).pathname : conv)
                    .replace(/\/+$/, '') + '/media';
                return path.startsWith('/') ? path : '/' + path;
            } catch (_) {
                return null;
            }
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || this.csrf || '';
        },

        xsrfToken() {
            const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
            return match ? decodeURIComponent(match[1]) : '';
        },

        fileToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = () => reject(new Error('read failed'));
                reader.readAsDataURL(file);
            });
        },

        async postMediaRequest(uploadUrl, token, body, isJson = false) {
            const headers = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            };
            const xsrf = this.xsrfToken();
            if (xsrf) headers['X-XSRF-TOKEN'] = xsrf;
            if (isJson) headers['Content-Type'] = 'application/json';

            return fetch(uploadUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers,
                body: isJson ? JSON.stringify(body) : body,
            });
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
            this.crmLeadStage = conv.crm.sales_lead_stage || '';
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

        async updateLeadStage() {
            if (!this.crmUrls.lead_stage || !this.crmLeadStage) return;
            await this.crmPost(this.crmUrls.lead_stage, { stage: this.crmLeadStage });
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
            if (!this.selectedTemplateKey) {
                this.templateName = '';
                this.templateLang = '';
                this.templateVariables = {};
                return;
            }
            const parts = this.selectedTemplateKey.split('|');
            this.templateName = parts[0] || '';
            this.templateLang = parts[1] || '';
            this.resetTemplateVariables();
            if (forStart) {
                this.startTemplate = this.templateName;
                this.startLang = this.templateLang;
            }
        },

        selectedTemplateMeta() {
            if (!this.templateName || !this.templateLang) return null;
            return this.metaTemplates.find(t => t.name === this.templateName && t.language === this.templateLang) || null;
        },

        resetTemplateVariables() {
            const meta = this.selectedTemplateMeta();
            const vars = {};
            if (!meta) {
                this.templateVariables = vars;
                return;
            }
            if ((meta.header_variable_count || 0) > 0) {
                vars.header_1 = this.templateVariables.header_1 || '';
            }
            const count = meta.body_variable_count || 0;
            for (let i = 1; i <= count; i++) {
                vars[i] = this.templateVariables[i] || '';
            }
            this.templateVariables = vars;
        },

        templateVariablesReady() {
            const meta = this.selectedTemplateMeta();
            if (!meta) return !!this.templateName;
            if ((meta.header_variable_count || 0) > 0 && !(this.templateVariables.header_1 || '').trim()) {
                return false;
            }
            const count = meta.body_variable_count || 0;
            for (let i = 1; i <= count; i++) {
                if (!(this.templateVariables[i] || '').trim()) return false;
            }
            return true;
        },

        templateVariablesPayload() {
            const payload = {};
            Object.keys(this.templateVariables || {}).forEach(key => {
                const val = (this.templateVariables[key] || '').trim();
                if (val !== '') payload[key] = val;
            });
            return payload;
        },

        templateBodyVarSlots() {
            const meta = this.selectedTemplateMeta();
            const count = meta?.body_variable_count || 0;
            return Array.from({ length: count }, (_, index) => index + 1);
        },

        autoGrowComposer() {
            const el = this.$refs.composer;
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 128) + 'px';
        },

        newestMessageId() {
            let maxId = 0;
            for (const m of this.chatMessages) {
                if (m?.id && m.id > maxId) maxId = m.id;
            }
            return maxId;
        },

        sortMessagesNewestFirst(list) {
            return [...(list || [])].sort((a, b) => {
                const aPending = a?._pending || a?._tmp;
                const bPending = b?._pending || b?._tmp;
                if (aPending && !bPending) return -1;
                if (!aPending && bPending) return 1;
                const aTs = Date.parse(a?.created_at || '') || 0;
                const bTs = Date.parse(b?.created_at || '') || 0;
                if (aTs !== bTs) return bTs - aTs;
                return (b?.id || 0) - (a?.id || 0);
            });
        },

        scrollChat(smooth = true) {
            const run = () => {
                const el = document.getElementById('chat-messages');
                if (!el) return;
                el.scrollTop = 0;
            };
            this.$nextTick(() => {
                run();
                requestAnimationFrame(() => {
                    run();
                    if (smooth) {
                        const el = document.getElementById('chat-messages');
                        if (el) el.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });
        },

        appendChatMessage(msg) {
            if (!msg) return;
            const exists = this.chatMessages.some(m => m.id && msg.id && m.id === msg.id);
            if (exists) return;
            this.chatMessages = this.sortMessagesNewestFirst([msg, ...this.chatMessages]);
            if (msg.id) {
                this.lastMessageId = Math.max(this.lastMessageId, msg.id);
            }
            this.scrollChat();
        },

        replacePendingMessage(tmpId, serverMsg) {
            const idx = this.chatMessages.findIndex(m => m._tmp === tmpId);
            if (idx >= 0) {
                const next = [...this.chatMessages];
                next[idx] = serverMsg;
                this.chatMessages = this.sortMessagesNewestFirst(next);
            } else {
                this.appendChatMessage(serverMsg);
            }
            if (serverMsg?.id) {
                this.lastMessageId = Math.max(this.lastMessageId, serverMsg.id);
            }
            this.scrollChat();
        },

        removePendingMessage(tmpId) {
            this.chatMessages = this.chatMessages.filter(m => m._tmp !== tmpId);
        },

        async reloadCurrentConversation() {
            if (!this.conversationId) return null;
            try {
                const res = await fetch(this.conversationUrl(this.conversationId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.success) return null;

                this.activeConversation = data.conversation;
                this.chatMessages = this.sortMessagesNewestFirst(Array.isArray(data.messages) ? data.messages : []);
                this.withinWindow = !!data.within_service_window;
                this.replyUrl = data.reply_url || this.replyUrl;
                this.reactUrl = data.react_url || this.reactUrl;
                this.templateUrl = data.template_url || this.templateUrl;
                this.mediaUrl = data.media_url || this.mediaUrl;
                this.crmUrls = data.crm_urls || this.crmUrls;
                this.crmNotes = data.notes || this.crmNotes;
                this.crmTimeline = data.timeline || this.crmTimeline;
                this.syncCrmFromConversation(data.conversation);
                this.lastMessageId = this.newestMessageId();

                if (data.conversation) {
                    data.conversation.unread_count = 0;
                    this.upsertConversation(data.conversation);
                }

                this.scrollChat(false);
                return data;
            } catch (_) {
                return null;
            }
        },

        async parseJsonResponse(res) {
            const text = await res.text();
            const trimmed = text.trim();
            const looksHtml = trimmed.startsWith('<!DOCTYPE')
                || trimmed.startsWith('<html')
                || (trimmed.startsWith('<') && trimmed.includes('</head>'));

            if (looksHtml) {
                return {
                    success: false,
                    error: res.status >= 300 && res.status < 400
                        ? 'تم إعادة توجيه طلب الإرسال — حدّث الصفحة (Ctrl+Shift+R) وحاول مرة أخرى.'
                        : 'وصل الطلب لصفحة الويب بدل واجهة الإرسال — حدّث الصفحة وتأكد من رفع آخر تحديث للسيرفر.',
                };
            }

            try {
                const data = JSON.parse(text);
                if (data.success === undefined) {
                    if (data.message || data.errors) {
                        data.success = false;
                        const fileErr = data.errors?.file;
                        data.error = data.error || data.message || (Array.isArray(fileErr) ? fileErr[0] : fileErr) || 'فشل الطلب';
                    }
                }
                if (!res.ok && data.success !== true && !data.error) {
                    data.success = false;
                    data.error = data.message || `خطأ من الخادم (${res.status})`;
                }
                return data;
            } catch (_) {
                if (res.status === 419) {
                    return { success: false, error: 'انتهت الجلسة. حدّث الصفحة وحاول مرة أخرى.' };
                }
                if (res.status === 413) {
                    return { success: false, error: 'حجم الملف كبير جداً.' };
                }
                if (res.status === 404) {
                    return { success: false, error: 'مسار إرسال الوسائط غير موجود — نفّذ php artisan route:clear على السيرفر.' };
                }
                const snippet = text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 100);
                return {
                    success: false,
                    error: `استجابة غير متوقعة من الخادم (${res.status})${snippet ? ': ' + snippet : ''}`,
                };
            }
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

            const isSwitch = id !== this.conversationId;
            this.loadingConversation = true;
            this.replyError = '';
            this.replyBody = '';
            this.replyingTo = null;
            this.reactPickerFor = null;
            this.showSidebarMobile = false;
            this.conversationId = id;
            if (isSwitch) {
                this.chatMessages = [];
                this.activeConversation = null;
            }

            try {
                const res = await fetch(this.conversationUrl(id), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.replyError = data.error || 'تعذّر تحميل المحادثة';
                    return;
                }

                this.conversationId = id;
                this.activeConversation = data.conversation;
                this.chatMessages = this.sortMessagesNewestFirst(Array.isArray(data.messages) ? [...data.messages] : []);
                this.withinWindow = !!data.within_service_window;
                this.replyUrl = data.reply_url;
                this.reactUrl = data.react_url || this.reactUrl;
                this.mediaUrl = data.media_url || this.mediaUrl;
                this.templateUrl = data.template_url;
                this.crmUrls = data.crm_urls || {};
                this.crmNotes = data.notes || [];
                this.crmTimeline = data.timeline || [];
                this.syncCrmFromConversation(data.conversation);
                this.lastMessageId = this.newestMessageId();
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
                    const byId = Object.fromEntries(this.chatMessages.filter(m => m.id).map(m => [m.id, m]));
                    let changed = false;
                    data.messages.forEach(m => {
                        if (!m.id) return;
                        if (byId[m.id]) {
                            if (byId[m.id].reaction_emoji !== m.reaction_emoji || byId[m.id].status !== m.status) {
                                Object.assign(byId[m.id], m);
                                changed = true;
                            }
                        } else {
                            this.chatMessages.unshift(m);
                            this.lastMessageId = Math.max(this.lastMessageId, m.id);
                            changed = true;
                        }
                    });
                    if (changed) {
                        this.chatMessages = this.sortMessagesNewestFirst(this.chatMessages);
                        this.scrollChat();
                    }
                }
            } catch (_) {}
        },

        startReply(msg) {
            if (!msg?.id) return;
            this.replyingTo = msg;
            this.reactPickerFor = null;
            this.$refs.composer?.focus();
        },

        cancelReply() {
            this.replyingTo = null;
        },

        toggleReactPicker(msg) {
            if (!msg?.id) return;
            this.reactPickerFor = this.reactPickerFor === msg.id ? null : msg.id;
        },

        async sendReaction(msg, emoji) {
            if (!this.reactUrl || !msg?.id || this.sending) return;
            this.sending = true;
            this.replyError = '';
            this.reactPickerFor = null;
            try {
                const res = await fetch(this.reactUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message_id: msg.id, emoji }),
                });
                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.replyError = data.error || 'فشل التفاعل';
                    return;
                }
                if (data.message) {
                    const idx = this.chatMessages.findIndex(m => m.id === data.message.id);
                    if (idx >= 0) {
                        const next = [...this.chatMessages];
                        next[idx] = data.message;
                        this.chatMessages = next;
                    }
                }
            } catch (_) {
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        shouldShowBody(msg) {
            if (!msg?.body) return false;
            if (msg.media?.url) {
                const placeholders = ['[صورة]', '[رسالة صوتية]', '[فيديو]', '[مستند]', '[ملصق]'];
                if (placeholders.includes(msg.body)) return false;
            }
            return true;
        },

        onMediaPicked(event) {
            const file = event.target.files?.[0];
            event.target.value = '';
            if (!file) return;
            const name = (file.name || '').toLowerCase();
            const type = (file.type || '').toLowerCase();
            const isImage = type.startsWith('image/');
            const isVoiceNote = !isImage && (
                type.includes('ogg') || name.endsWith('.ogg')
                || type.includes('webm') || name.endsWith('.webm')
                || type.includes('mp4') || name.endsWith('.m4a')
            );
            this.cancelVoicePreview();
            this.uploadMediaFile(file, { voiceNote: isVoiceNote });
        },

        pickAudioFile() {
            this.closeMicModal();
            this.$refs.audioInput?.click();
        },

        async toggleRecording() {
            if (this.showVoicePreview) return;

            if (this.recording) {
                this.stopRecording();
                return;
            }

            this.replyError = '';
            this.micWaiting = false;
            this.cancelVoicePreview();

            if (!window.isSecureContext) {
                this.openMicModal('error', 'التسجيل الصوتي يتطلب فتح الموقع عبر HTTPS.');
                return;
            }

            if (!navigator.mediaDevices?.getUserMedia) {
                this.openMicModal('error', 'المتصفح لا يدعم تسجيل الصوت — استخدم «إرفاق ملف صوتي».');
                return;
            }

            await this.requestMicAccess();
        },

        micModalTitle() {
            if (this.micModalMode === 'denied') return 'الميكروفون محظور في المتصفح';
            if (this.micModalMode === 'error') return 'تعذّر التسجيل';
            return 'الميكروفون';
        },

        micModalSubtitle() {
            if (this.micModalMode === 'denied') return 'يمكنك إرفاق ملف صوتي مباشرة — أو تفعيل الميكروفون من المتصفح مرة واحدة.';
            if (this.micModalMode === 'error') return 'تحقق من المتصفح أو جرّب إرفاق ملف صوتي.';
            return '';
        },

        reloadForMic() {
            window.location.reload();
        },

        async retryMicAccess() {
            this.micModalMessage = '';
            this.showMicModal = false;
            await this.requestMicAccess();
        },

        openMicModal(mode, message = '') {
            this.micModalMode = mode;
            this.micModalMessage = message;
            this.showMicModal = true;
            this.micWaiting = false;
            this.replyError = '';
        },

        closeMicModal() {
            this.showMicModal = false;
            this.micModalMessage = '';
            this.micWaiting = false;
        },

        async queryMicPermissionState() {
            try {
                if (navigator.permissions?.query) {
                    const status = await navigator.permissions.query({ name: 'microphone' });
                    this.micPermissionState = status.state;
                    status.onchange = () => {
                        this.micPermissionState = status.state;
                        if (status.state === 'granted' && this.showMicModal) {
                            this.requestMicAccess();
                        }
                    };
                    return status.state;
                }
            } catch (_) {}
            this.micPermissionState = 'unknown';
            return 'unknown';
        },

        async requestMicAccess() {
            const getUserMedia = navigator.mediaDevices?.getUserMedia?.bind(navigator.mediaDevices);
            if (!getUserMedia) {
                this.openMicModal('error', 'المتصفح لا يدعم تسجيل الصوت.');
                return;
            }

            this.micWaiting = true;
            this.showMicModal = false;
            this.micModalMessage = '';
            this.replyError = '';

            try {
                this.recordStream = await getUserMedia({ audio: true });
                this.micWaiting = false;
                this.micPermissionState = 'granted';
                this.startMediaRecorder();
            } catch (err) {
                this.micWaiting = false;
                await this.handleMicPermissionError(err);
            }
        },

        startMediaRecorder() {
            const types = ['audio/ogg;codecs=opus', 'audio/webm;codecs=opus', 'audio/webm', 'audio/mp4'];
            this.recorderMime = types.find(t => MediaRecorder.isTypeSupported(t)) || '';
            this.mediaRecorder = new MediaRecorder(
                this.recordStream,
                this.recorderMime ? { mimeType: this.recorderMime } : undefined
            );
            this.recordChunks = [];
            this.recordSeconds = 0;
            this.clearRecordTimer();
            this.recordTimer = setInterval(() => { this.recordSeconds += 1; }, 1000);
            this.mediaRecorder.ondataavailable = (e) => {
                if (e.data?.size) this.recordChunks.push(e.data);
            };
            this.mediaRecorder.onstop = () => this.finishRecording();
            this.mediaRecorder.start(250);
            this.recording = true;
            this.replyError = '';
        },

        clearRecordTimer() {
            if (this.recordTimer) {
                clearInterval(this.recordTimer);
                this.recordTimer = null;
            }
        },

        async handleMicPermissionError(err) {
            this.cleanupMicStream();
            const name = err?.name || '';

            if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
                let permanentlyDenied = false;
                try {
                    if (navigator.permissions?.query) {
                        const status = await navigator.permissions.query({ name: 'microphone' });
                        permanentlyDenied = status.state === 'denied';
                        this.micPermissionState = status.state;
                    }
                } catch (_) {}

                const policyBlocked = !window.isSecureContext
                    || (err?.message && /policy|permission/i.test(err.message));

                this.openMicModal('denied', permanentlyDenied
                    ? 'إذا كان الميكروفون مفعّلاً في المتصفح، أعد تحميل الصفحة بعد رفع آخر تحديث للسيرفر.'
                    : (policyBlocked
                        ? 'تأكد أن الموقع يعمل عبر HTTPS ثم أعد المحاولة.'
                        : 'اضغط زر الميكروفون مرة أخرى واختر «السماح» في رسالة المتصفح.'));
                return;
            }

            if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
                this.openMicModal('error', 'لم يُعثر على ميكروفون — جرّب «إرفاق ملف صوتي».');
                return;
            }

            if (name === 'NotReadableError' || name === 'TrackStartError') {
                this.openMicModal('error', 'الميكروفون مستخدم من تطبيق آخر.');
                return;
            }

            this.openMicModal('error', 'تعذّر الوصول للميكروفون — جرّب إرفاق ملف صوتي.');
        },

        cleanupMicStream() {
            this.clearRecordTimer();
            if (this.recordStream) {
                this.recordStream.getTracks().forEach(t => t.stop());
                this.recordStream = null;
            }
            this.mediaRecorder = null;
            this.recording = false;
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
            }
            this.recording = false;
        },

        finishRecording() {
            this.cleanupMicStream();
            if (!this.recordChunks.length) {
                this.replyError = 'التسجيل فارغ — حاول مرة أخرى.';
                return;
            }
            const mime = this.recorderMime || 'audio/webm';
            const blob = new Blob(this.recordChunks, { type: mime });
            if (blob.size < 500) {
                this.replyError = 'التسجيل قصير جداً — سجّل لثانية على الأقل.';
                this.recordChunks = [];
                return;
            }
            const ext = mime.includes('ogg') ? 'ogg' : (mime.includes('mp4') ? 'm4a' : 'webm');
            this.cancelVoicePreview();
            this.voicePreviewFile = new File([blob], `voice-${Date.now()}.${ext}`, { type: mime });
            this.voicePreviewUrl = URL.createObjectURL(blob);
            this.showVoicePreview = true;
            this.recordChunks = [];
            this.replyError = '';
        },

        cancelVoicePreview() {
            if (this.voicePreviewUrl) {
                URL.revokeObjectURL(this.voicePreviewUrl);
            }
            this.voicePreviewUrl = null;
            this.voicePreviewFile = null;
            this.showVoicePreview = false;
        },

        sendVoicePreview() {
            if (!this.voicePreviewFile || this.sending) return;
            const file = this.voicePreviewFile;
            this.cancelVoicePreview();
            this.uploadMediaFile(file, { voiceNote: true });
        },

        async uploadMediaFile(file, options = {}) {
            const voiceNote = !!options.voiceNote;
            const uploadUrl = this.mediaSendUrl();
            if (!uploadUrl || this.sending) return;
            this.sending = true;
            this.replyError = '';
            const token = this.csrfToken();
            const isAudio = (file.type || '').startsWith('audio/');
            const label = voiceNote ? '[رسالة صوتية]' : (isAudio ? '[صوت]' : '[صورة]');
            const tmpId = 'tmp-media-' + Date.now();
            this.appendChatMessage({
                _tmp: tmpId,
                _pending: true,
                id: null,
                direction: 'outbound',
                body: label,
                message_type: voiceNote || isAudio ? 'audio' : 'image',
                is_inbound: false,
                status: 'pending',
                created_at_human: 'الآن',
            });
            this.scrollChat();

            try {
                let res;
                // Voice Note / صوت: JSON+base64 — يتجنب حظر multipart على Hostinger/ModSecurity
                if (voiceNote || isAudio) {
                    const base64 = await this.fileToBase64(file);
                    res = await this.postMediaRequest(uploadUrl, token, {
                        audio_base64: base64,
                        audio_mime: file.type || 'application/octet-stream',
                        audio_name: file.name || 'voice.webm',
                        voice_note: voiceNote,
                    }, true);
                } else {
                    const form = new FormData();
                    form.append('file', file);
                    form.append('_token', token);
                    res = await this.postMediaRequest(uploadUrl, token, form, false);
                }

                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.removePendingMessage(tmpId);
                    this.replyError = data.error || 'فشل إرسال الوسائط';
                    if (data.requires_template) this.showTemplatePicker = true;
                    return;
                }
                if (data.message) {
                    this.replacePendingMessage(tmpId, data.message);
                } else {
                    this.removePendingMessage(tmpId);
                    await this.reloadCurrentConversation();
                }
                if (data.conversation) {
                    this.activeConversation = data.conversation;
                    this.upsertConversation(data.conversation);
                }
                this.scrollChat();
            } catch (_) {
                this.removePendingMessage(tmpId);
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async sendReply() {
            if (!this.replyUrl || !this.replyBody.trim() || this.sending) return;
            this.sending = true;
            this.replyError = '';
            const body = this.replyBody.trim();
            const contextMsg = this.replyingTo;
            const tmpId = 'tmp-' + Date.now();
            this.replyBody = '';
            this.replyingTo = null;
            this.$nextTick(() => this.autoGrowComposer());

            this.appendChatMessage({
                _tmp: tmpId,
                _pending: true,
                id: null,
                direction: 'outbound',
                body,
                context_preview: contextMsg?.body || null,
                is_inbound: false,
                status: 'pending',
                created_at_human: 'الآن',
            });

            try {
                const payload = { body };
                if (contextMsg?.id) payload.context_message_id = contextMsg.id;

                const res = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.removePendingMessage(tmpId);
                    this.replyBody = body;
                    if (contextMsg) this.replyingTo = contextMsg;
                    this.replyError = data.error || 'فشل الإرسال';
                    if (data.requires_template) this.showTemplatePicker = true;
                    return;
                }
                if (data.message) {
                    this.replacePendingMessage(tmpId, data.message);
                } else {
                    this.removePendingMessage(tmpId);
                    await this.reloadCurrentConversation();
                }
                if (data.conversation) {
                    this.activeConversation = data.conversation;
                    this.upsertConversation(data.conversation);
                }
                this.scrollChat();
            } catch (_) {
                this.removePendingMessage(tmpId);
                this.replyBody = body;
                if (contextMsg) this.replyingTo = contextMsg;
                this.replyError = 'خطأ في الاتصال';
            } finally {
                this.sending = false;
            }
        },

        async sendTemplate() {
            if (!this.templateUrl || !this.templateName.trim() || this.sending || !this.templateVariablesReady()) return;
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
                        language_code: this.templateLang,
                        template_variables: this.templateVariablesPayload(),
                    }),
                });
                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.replyError = data.error || 'فشل إرسال القالب';
                    return;
                }
                if (data.message) {
                    this.appendChatMessage(data.message);
                } else {
                    await this.reloadCurrentConversation();
                }
                if (data.conversation) {
                    this.activeConversation = data.conversation;
                    this.upsertConversation(data.conversation);
                }
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
                if (this.startLeadId) {
                    payload.sales_lead_id = this.startLeadId;
                }
                if (body) {
                    payload.body = body;
                } else if (useTemplate) {
                    if (!this.templateVariablesReady()) {
                        this.startError = 'أكمل جميع متغيرات القالب';
                        this.sending = false;
                        return;
                    }
                    payload.template_name = this.startTemplate;
                    payload.language_code = this.startLang;
                    payload.template_variables = this.templateVariablesPayload();
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
