

<?php $__env->startSection('title', $waPageTitle ?? 'محادثات الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', $waPageHeader ?? 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $audience = $inboxAudience ?? 'admin';
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $webhookDiag = $connectionMeta['webhook'] ?? [];
    $webhookIssues = ($webhookDiag['receiving_replies'] ?? false) || ($webhookDiag['webhook_reachable'] ?? false)
        ? array_values(array_filter($webhookDiag['issues'] ?? [], fn ($issue) => ! str_contains($issue, 'لم يصل أي طلب Webhook') && ! str_contains($issue, 'غير مشترك')))
        : ($webhookDiag['issues'] ?? []);
    $webhookTips = $webhookDiag['tips'] ?? [];
    $webhookMeta = $webhookDiag['meta'] ?? [];
    $activeId = $activeConversation?->id;
    $inboxService = app(\App\Services\WhatsAppInboxService::class);
    $routes = $inboxRoutes ?? [];
    $initialConversations = $conversations->getCollection()->map(fn ($c) => $inboxService->serializeConversation($c, $audience))->values();
    $initialMessages = $messages->map(fn ($m) => $inboxService->serializeMessage($m))->values();
    $crmNotesInitial = [];
    $crmTimelineInitial = [];
    $crmUrlsInitial = $routes['crm'] ?? [];
    if ($activeConversation && ($crmReady ?? false)) {
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
    }
    $inboxConfig = [
        'conversationId' => $activeId,
        'activeConversation' => $activeConversation ? $inboxService->serializeConversation($activeConversation, $audience) : null,
        'conversations' => $initialConversations,
        'messages' => $initialMessages,
        'pollUrl' => $routes['poll'] ?? '',
        'conversationUrlTemplate' => $routes['conversationUrlTemplate'] ?? '',
        'replyUrl' => $routes['reply'] ?? null,
        'templateUrl' => $routes['template'] ?? null,
        'startUrl' => $routes['start'] ?? '',
        'templatesUrl' => $routes['templates'] ?? '',
        'inboxUrl' => $routes['index'] ?? '',
        'csrf' => csrf_token(),
        'withinWindow' => (bool) ($withinWindow ?? false),
        'lastMessageId' => $messages->last()?->id ?? 0,
        'metaTemplates' => $metaTemplates ?? [],
        'crmReady' => (bool) ($crmReady ?? false),
        'crmUrls' => $crmUrlsInitial,
        'crmNotes' => $crmNotesInitial,
        'crmTimeline' => $crmTimelineInitial,
        'inboxAudience' => $audience,
        'startLeadId' => $startLead->id ?? null,
        'startLeadPhone' => $startLead->phone ?? null,
        'startLeadName' => $startLead->name ?? null,
    ];
?>

<script>window.__waInboxConfig = <?php echo json_encode($inboxConfig, 15, 512) ?>;</script>

<div class="wa-inbox-page flex flex-col min-h-0 overflow-hidden gap-2" x-data="whatsappInbox()" x-cloak>
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(!empty($webhookIssues) || !empty($webhookTips)): ?>
        <div class="shrink-0 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 space-y-2">
            <?php if(!empty($webhookIssues)): ?>
                <p class="font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                    ردود العملاء لا تظهر بعد في المحادثات
                </p>
                <ul class="list-disc list-inside text-xs space-y-1 leading-relaxed">
                    <?php $__currentLoopData = $webhookIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($issue); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <?php if(!empty($webhookMeta) && ($webhookMeta['messages_subscribed'] !== null || $webhookMeta['callback_url'])): ?>
                <div class="text-[11px] rounded-lg bg-white/70 border border-amber-200 px-3 py-2 space-y-1">
                    <p><strong>فحص Meta:</strong>
                        messages = <?php echo e(($webhookMeta['messages_subscribed'] ?? false) ? 'مشترك ✓' : 'غير مشترك ✗'); ?>,
                        WABA = <?php echo e(($webhookMeta['waba_app_subscribed'] ?? false) ? 'مشترك ✓' : 'غير مشترك ✗'); ?>

                    </p>
                    <?php if(!empty($webhookMeta['callback_url'])): ?>
                        <p class="dir-ltr break-all">Callback في Meta: <?php echo e($webhookMeta['callback_url']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($webhookTips)): ?>
                <ul class="list-disc list-inside text-[11px] text-amber-900 space-y-1">
                    <?php $__currentLoopData = $webhookTips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($tip); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <?php if($audience === 'admin'): ?>
                <p class="text-xs leading-relaxed border-t border-amber-200 pt-2 flex flex-wrap items-center gap-2">
                    <a href="<?php echo e(route('admin.whatsapp.settings')); ?>#webhook-status-panel" class="underline font-semibold">إعدادات الربط</a>
                    <span>— استخدم «تحديث الحالة» أو «مزامنة الاشتراك مع Meta» لعرض حقول Webhook مباشرة من Meta.</span>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <div class="shrink-0 flex flex-wrap items-center justify-between gap-2 px-1">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white shadow-md shrink-0">
                <i class="fas fa-inbox text-sm"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-black text-slate-900 truncate"><?php echo e($waInboxTitle ?? 'المحادثات الواردة'); ?></h2>
                <p class="text-[11px] text-slate-500 truncate hidden sm:block"><?php echo e($waInboxSubtitle ?? 'ردّ على العملاء وتابع الـ Pipeline'); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white border border-slate-200 text-slate-700">
                غير مقروء: <span x-text="unreadTotal"><?php echo e((int) $unreadTotal); ?></span>
            </span>
            <button type="button" @click="showStartModal = true" class="<?php echo e($waBtnPrimary); ?> text-xs sm:text-sm !py-2 !px-3">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline">محادثة جديدة</span>
            </button>
        </div>
    </div>

    <?php if(! $tablesReady): ?>
        <div class="shrink-0 rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-bold">تشغيل الترحيل مطلوب</p>
            <p class="mt-1">نفّذ: <code class="bg-white px-2 py-0.5 rounded">php artisan migrate --force</code></p>
        </div>
    <?php elseif(! $canSend && ($inboxAudience ?? 'admin') === 'admin'): ?>
        
    <?php elseif(! $canSend): ?>
        <div class="shrink-0 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
            إرسال الواتساب غير متاح — تواصل مع الإدارة.
        </div>
    <?php endif; ?>

    
    <div class="wa-inbox-shell flex-1 min-h-0 overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-white">

        
        <aside class="wa-conv-sidebar wa-inbox-col border-s border-slate-200/80 bg-[#f0f2f5]"
               :class="(conversationId && !showSidebarMobile) ? 'max-lg:hidden' : ''">

            
            <div class="wa-conv-header shrink-0 border-b border-slate-200/80 bg-[#f0f2f5]">
                <?php if(! $canSend && ($inboxAudience ?? 'admin') === 'admin'): ?>
                <a href="<?php echo e(route('admin.whatsapp.settings')); ?>"
                   class="flex items-center gap-2 mx-2.5 mt-2.5 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 hover:bg-amber-100 transition-colors">
                    <i class="fas fa-exclamation-triangle text-amber-600 shrink-0"></i>
                    <span class="font-semibold">الربط غير مكتمل — <span class="underline">إعداد Meta</span></span>
                </a>
                <?php endif; ?>

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

                    <?php if($crmReady ?? false): ?>
                    <div class="mt-2 grid grid-cols-2 gap-1.5">
                        <select x-model="filterStatus" @change="applyFilters()"
                                class="col-span-1 text-[11px] rounded-lg border-0 bg-white px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 text-slate-700">
                            <option value="">كل الحالات</option>
                            <?php $__currentLoopData = $crmStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if(empty($waHideAdminFilters)): ?>
                        <select x-model="filterAssigned" @change="applyFilters()"
                                class="col-span-1 text-[11px] rounded-lg border-0 bg-white px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 text-slate-700">
                            <option value="">كل الموظفين</option>
                            <option value="unassigned">غير معيّنة</option>
                            <?php $__currentLoopData = $crmAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent['id']); ?>"><?php echo e($agent['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <label class="col-span-2 inline-flex items-center justify-center gap-1.5 text-[11px] bg-white rounded-lg px-2 py-1.5 shadow-sm ring-1 ring-slate-200/60 cursor-pointer text-slate-600">
                            <input type="checkbox" x-model="filterMine" @change="applyFilters()" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            محادثاتي فقط
                        </label>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
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

        
        <section class="wa-inbox-col wa-chat-panel overflow-hidden bg-[#efeae2]"
                 :class="conversationId ? '' : 'max-lg:hidden'">

            
            <div x-show="!conversationId && !loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5]">
                <div class="w-24 h-24 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                    <i class="fab fa-whatsapp text-5xl text-emerald-500"></i>
                </div>
                <p class="font-semibold text-slate-700 text-lg">محادثات الواتساب</p>
                <p class="text-sm text-slate-500 mt-1 text-center max-w-sm">اختر محادثة من القائمة أو ابدأ محادثة جديدة بقالب Meta</p>
                <button type="button" @click="showStartModal = true" class="<?php echo e($waBtnPrimary); ?> mt-5 text-sm">اكتب رسالة جديدة</button>
            </div>

            
            <div x-show="loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex items-center justify-center bg-[#efeae2]">
                <div class="text-center text-slate-500">
                    <i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
                    <p class="text-sm">جاري تحميل المحادثة...</p>
                </div>
            </div>

            
            <div x-show="conversationId && activeConversation && !loadingConversation" x-cloak
                 class="wa-chat-active">
                    
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

                    
                    <div id="chat-messages" class="wa-chat-messages p-3 sm:p-4">
                        <div x-show="chatMessages.length === 0" x-cloak
                             class="flex flex-col items-center justify-center py-16 text-slate-500 text-sm">
                            <i class="fas fa-comments text-3xl text-slate-300 mb-2"></i>
                            <p>لا رسائل بعد — ابدأ المحادثة من الأسفل</p>
                        </div>
                        <div class="space-y-2">
                            <template x-for="msg in chatMessages" :key="'m-' + (msg.id || msg._tmp)">
                                <div class="flex" :class="msg.is_inbound ? 'justify-start' : 'justify-end'">
                                    <div class="max-w-[88%] sm:max-w-[72%] rounded-lg px-3 py-2 shadow-sm text-sm whitespace-pre-wrap break-words relative"
                                         :class="msg.is_inbound
                                            ? 'bg-white text-slate-800 rounded-tl-none'
                                            : (msg._pending ? 'bg-[#d9fdd3]/70 text-slate-700 rounded-tr-none ring-1 ring-emerald-200' : 'bg-[#d9fdd3] text-slate-900 rounded-tr-none')">
                                        <p x-text="msg.body || '[رسالة]'"></p>
                                        <template x-if="msg.template_name">
                                            <p class="text-[10px] opacity-70 mt-1" x-text="'قالب: ' + msg.template_name"></p>
                                        </template>
                                        <template x-if="msg.error_message && msg.status === 'failed'">
                                            <p class="text-[10px] text-rose-600 mt-1" x-text="msg.error_message"></p>
                                        </template>
                                        <div class="flex items-center justify-end gap-1 mt-0.5 text-[10px] opacity-60">
                                            <span x-show="msg._pending" class="text-emerald-600">جاري الإرسال...</span>
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
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    
                    <div class="wa-chat-composer shrink-0 bg-[#f0f2f5] px-3 py-2 sm:px-4 sm:py-3 border-t border-slate-200">
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

            
            <div x-show="conversationId && !activeConversation && !loadingConversation" x-cloak
                 class="wa-chat-panel__pane flex flex-col items-center justify-center text-slate-500 p-8 bg-[#f0f2f5]">
                <i class="fas fa-exclamation-circle text-3xl text-amber-500 mb-3"></i>
                <p class="font-semibold text-slate-700">تعذّر عرض المحادثة</p>
                <p class="text-sm text-slate-500 mt-1" x-text="replyError || 'اختر محادثة أخرى من القائمة'"></p>
                <button type="button" @click="selectConversation(conversationId)" class="<?php echo e($waBtnPrimary); ?> mt-4 text-sm">إعادة المحاولة</button>
            </div>
        </section>

        <?php echo $__env->make('admin.whatsapp._crm_panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    
    <div x-show="showStartModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @keydown.escape.window="showStartModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4" @click.outside="showStartModal = false">
            <h3 class="font-bold text-lg text-slate-900">محادثة جديدة</h3>
            <p class="text-xs text-slate-600">أدخل رقم الواتساب واكتب رسالتك ثم اضغط إرسال.</p>
            <div>
                <label class="<?php echo e($waLabelClass); ?>">رقم الواتساب</label>
                <input type="text" x-model="startPhone" placeholder="2010xxxxxxx" class="<?php echo e($waInputClass); ?> dir-ltr text-sm">
            </div>
            <div>
                <label class="<?php echo e($waLabelClass); ?>">الرسالة</label>
                <textarea x-model="startBody" rows="3" placeholder="اكتب رسالتك هنا..."
                          class="<?php echo e($waInputClass); ?> text-sm resize-none"></textarea>
            </div>
            <div x-show="showStartTemplatePicker" x-cloak class="space-y-2 pt-1 border-t border-slate-100">
                <label class="<?php echo e($waLabelClass); ?>">أو أرسل بقالب Meta</label>
                <?php if(count($metaTemplates ?? []) > 0): ?>
                    <select x-model="selectedTemplateKey" @change="applySelectedTemplate(true)" class="<?php echo e($waSelectClass); ?> text-sm dir-ltr">
                        <?php $__currentLoopData = $metaTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tpl['name']); ?>|<?php echo e($tpl['language']); ?>"><?php echo e($tpl['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php else: ?>
                    <p class="text-xs text-slate-500">لا توجد قوالب معتمدة.</p>
                <?php endif; ?>
            </div>
            <button type="button" @click="showStartTemplatePicker = !showStartTemplatePicker"
                    class="text-[11px] text-slate-500 hover:text-emerald-600">
                <i class="fas fa-file-alt"></i> <span x-text="showStartTemplatePicker ? 'إخفاء القوالب' : 'إرسال بقالب بدلاً من ذلك'"></span>
            </button>
            <p x-show="startError" class="text-xs text-rose-600" x-text="startError"></p>
            <div class="flex gap-2 justify-end">
                <button type="button" @click="showStartModal = false" class="<?php echo e($waBtnSecondary); ?> text-sm">إلغاء</button>
                <button type="button" @click="startConversation()" :disabled="sending" class="<?php echo e($waBtnPrimary); ?> text-sm">
                    <i class="fas fa-paper-plane ml-1"></i> إرسال
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
        unreadTotal: <?php echo e((int) $unreadTotal); ?>,
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
            const run = () => {
                const el = document.getElementById('chat-messages');
                if (!el) return;
                el.scrollTop = el.scrollHeight;
            };
            this.$nextTick(() => {
                run();
                requestAnimationFrame(() => {
                    run();
                    if (smooth) {
                        const el = document.getElementById('chat-messages');
                        if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
                    }
                });
            });
        },

        appendChatMessage(msg) {
            if (!msg) return;
            const exists = this.chatMessages.some(m => m.id && msg.id && m.id === msg.id);
            if (exists) return;
            this.chatMessages = [...this.chatMessages, msg];
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
                this.chatMessages = next;
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
                this.chatMessages = Array.isArray(data.messages) ? data.messages : [];
                this.withinWindow = !!data.within_service_window;
                this.replyUrl = data.reply_url || this.replyUrl;
                this.templateUrl = data.template_url || this.templateUrl;
                this.crmUrls = data.crm_urls || this.crmUrls;
                this.crmNotes = data.notes || this.crmNotes;
                this.crmTimeline = data.timeline || this.crmTimeline;
                this.syncCrmFromConversation(data.conversation);
                this.lastMessageId = this.chatMessages.length
                    ? this.chatMessages[this.chatMessages.length - 1].id
                    : 0;

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
            try {
                return JSON.parse(text);
            } catch (_) {
                return { success: false, error: 'استجابة غير متوقعة من الخادم' };
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
                this.chatMessages = Array.isArray(data.messages) ? [...data.messages] : [];
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
                    const merged = [...this.chatMessages];
                    data.messages.forEach(m => {
                        if (!existingIds.has(m.id)) {
                            merged.push(m);
                            this.lastMessageId = Math.max(this.lastMessageId, m.id);
                        }
                    });
                    if (merged.length !== this.chatMessages.length) {
                        this.chatMessages = merged;
                        this.scrollChat();
                    }
                }
            } catch (_) {}
        },

        async sendReply() {
            if (!this.replyUrl || !this.replyBody.trim() || this.sending) return;
            this.sending = true;
            this.replyError = '';
            const body = this.replyBody.trim();
            const tmpId = 'tmp-' + Date.now();
            this.replyBody = '';
            this.$nextTick(() => this.autoGrowComposer());

            this.appendChatMessage({
                _tmp: tmpId,
                _pending: true,
                id: null,
                direction: 'outbound',
                body,
                is_inbound: false,
                status: 'pending',
                created_at_human: 'الآن',
            });

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
                const data = await this.parseJsonResponse(res);
                if (!data.success) {
                    this.removePendingMessage(tmpId);
                    this.replyBody = body;
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($waLayout ?? 'layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\inbox.blade.php ENDPATH**/ ?>