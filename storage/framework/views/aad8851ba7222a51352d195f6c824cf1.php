

<?php $__env->startSection('title', 'محادثات الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $activeId = $activeConversation?->id;
    $withinWindow = $activeConversation ? app(\App\Services\WhatsAppInboxService::class)->isWithinServiceWindow($activeConversation) : false;
    $inboxConfig = [
        'conversationId' => $activeId,
        'pollUrl' => route('admin.whatsapp.inbox.poll'),
        'replyUrl' => $activeId ? route('admin.whatsapp.inbox.reply', $activeConversation) : null,
        'templateUrl' => $activeId ? route('admin.whatsapp.inbox.template', $activeConversation) : null,
        'startUrl' => route('admin.whatsapp.inbox.start'),
        'csrf' => csrf_token(),
        'withinWindow' => $withinWindow,
        'lastMessageId' => $messages->last()?->id ?? 0,
    ];
?>

<script>window.__waInboxConfig = <?php echo json_encode($inboxConfig, 15, 512) ?>;</script>

<div class="p-3 sm:p-4 md:p-6 space-y-4" style="background:#f8fafc; min-height:100vh;" x-data="whatsappInbox()">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'inbox'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'المحادثات الواردة',
        'subtitle' => 'استقبال رسائل العملاء والرد من النظام — مع دعم قوالب Meta لبدء المحادثة.',
        'icon' => 'fas fa-inbox',
        'actions' => '
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white border border-slate-200 text-slate-700">
                غير مقروء: <span x-text="unreadTotal">' . (int) $unreadTotal . '</span>
            </span>
            <button type="button" @click="showStartModal = true" class="' . $waBtnPrimary . ' text-sm"><i class="fas fa-plus"></i> محادثة جديدة</button>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(! $tablesReady): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
            <p class="font-bold">تشغيل الترحيل مطلوب</p>
            <p class="mt-1">نفّذ على السيرفر: <code class="bg-white px-2 py-0.5 rounded">php artisan migrate --force</code></p>
        </div>
    <?php elseif(! $canSend): ?>
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            الربط غير مكتمل — <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-bold underline">أكمل إعداد Meta</a> وفعّل Webhook لاستقبال الرسائل.
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs text-sky-900">
            <strong>Webhook مطلوب للرسائل الواردة:</strong> Callback URL <code class="dir-ltr"><?php echo e(\App\Support\WhatsAppCloudSettings::webhookUrl()); ?></code>
            — اشترك في <code>messages</code> و <code>message_status</code> في Meta Developers.
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-[70vh]">
        
        <aside class="lg:col-span-4 xl:col-span-3 <?php echo e($waSectionClass); ?> flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <form method="GET" action="<?php echo e(route('admin.whatsapp.inbox')); ?>" class="flex gap-2">
                    <?php if($activeId): ?>
                        <input type="hidden" name="conversation" value="<?php echo e($activeId); ?>">
                    <?php endif; ?>
                    <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو الرقم..."
                           class="<?php echo e($waInputClass); ?> text-sm flex-1">
                    <button type="submit" class="<?php echo e($waBtnDark); ?> !px-3"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.whatsapp.inbox', ['conversation' => $conv->id, 'search' => request('search')])); ?>"
                       class="block px-4 py-3 hover:bg-emerald-50/60 transition-colors <?php echo e($activeId === $conv->id ? 'bg-emerald-50 border-r-4 border-emerald-500' : ''); ?>">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 truncate"><?php echo e($conv->displayName()); ?></p>
                                <p class="text-[11px] text-slate-500 dir-ltr text-right font-mono"><?php echo e($conv->formattedPhone()); ?></p>
                                <?php if($conv->last_message_preview): ?>
                                    <p class="text-xs text-slate-600 mt-1 truncate"><?php echo e($conv->last_message_preview); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-left shrink-0">
                                <?php if($conv->unread_count > 0): ?>
                                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold"><?php echo e($conv->unread_count); ?></span>
                                <?php endif; ?>
                                <p class="text-[10px] text-slate-400 mt-1 whitespace-nowrap"><?php echo e($conv->last_message_at?->diffForHumans()); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-slate-500 text-sm">
                        <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                        <p>لا توجد محادثات بعد</p>
                        <p class="text-xs mt-2">عند وصول رسالة عبر Webhook ستظهر هنا</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($conversations->hasPages()): ?>
                <div class="p-3 border-t border-slate-200"><?php echo e($conversations->links()); ?></div>
            <?php endif; ?>
        </aside>

        
        <section class="lg:col-span-8 xl:col-span-9 <?php echo e($waSectionClass); ?> flex flex-col overflow-hidden min-h-[60vh]">
            <?php if($activeConversation): ?>
                <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div>
                        <h3 class="font-bold text-slate-900"><?php echo e($activeConversation->displayName()); ?></h3>
                        <p class="text-xs text-slate-500 dir-ltr"><?php echo e($activeConversation->formattedPhone()); ?>

                            <?php if($activeConversation->user): ?>
                                · <?php echo e($activeConversation->user->name); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($withinWindow): ?>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-clock"></i> نافذة 24 ساعة مفتوحة
                            </span>
                        <?php else: ?>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                <i class="fas fa-file-alt"></i> استخدم قالب Meta للرد
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 bg-[#e5ddd5]/30" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.25\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('admin.whatsapp._inbox_message', ['msg' => $msg], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                  class="<?php echo e($waInputClass); ?> text-sm flex-1 resize-none" @keydown.ctrl.enter="sendReply()"></textarea>
                        <button type="button" @click="sendReply()" :disabled="sending || !replyBody.trim()"
                                class="<?php echo e($waBtnPrimary); ?> !px-4 shrink-0 disabled:opacity-50">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>

                    <div x-show="!withinWindow" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 p-3 space-y-2">
                        <p class="text-xs text-amber-900 font-semibold">خارج نافذة 24 ساعة — أرسل قالب Meta معتمد:</p>
                        <div class="flex flex-wrap gap-2">
                            <input type="text" x-model="templateName" placeholder="اسم القالب (مثل hello_world)"
                                   class="<?php echo e($waInputClass); ?> text-sm dir-ltr flex-1 min-w-[140px]">
                            <input type="text" x-model="templateLang" placeholder="en_US"
                                   class="<?php echo e($waInputClass); ?> text-sm dir-ltr w-24">
                            <button type="button" @click="sendTemplate()" :disabled="sending"
                                    class="<?php echo e($waBtnPrimary); ?> text-sm disabled:opacity-50">
                                <i class="fas fa-file-alt"></i> إرسال قالب
                            </button>
                        </div>
                    </div>

                    <div x-show="withinWindow" class="flex flex-wrap gap-2 pt-1 border-t border-slate-100">
                        <span class="text-[10px] text-slate-500 w-full">أو أرسل قالب (للتأكيد):</span>
                        <input type="text" x-model="templateName" placeholder="hello_world"
                               class="<?php echo e($waInputClass); ?> text-xs dir-ltr w-36 py-1.5">
                        <button type="button" @click="sendTemplate()" :disabled="sending"
                                class="<?php echo e($waBtnSecondary); ?> text-xs !py-1.5">قالب</button>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8">
                    <i class="fab fa-whatsapp text-5xl text-emerald-300 mb-4"></i>
                    <p class="font-semibold text-slate-700">اختر محادثة أو ابدأ محادثة جديدة</p>
                    <button type="button" @click="showStartModal = true" class="<?php echo e($waBtnPrimary); ?> mt-4 text-sm">محادثة جديدة بقالب</button>
                </div>
            <?php endif; ?>
        </section>
    </div>

    
    <div x-show="showStartModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @keydown.escape.window="showStartModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4" @click.outside="showStartModal = false">
            <h3 class="font-bold text-lg text-slate-900">بدء محادثة جديدة</h3>
            <p class="text-xs text-slate-600">أول رسالة يجب أن تكون قالب Meta معتمد (مثل <code>hello_world</code>).</p>
            <div>
                <label class="<?php echo e($waLabelClass); ?>">رقم الواتساب</label>
                <input type="text" x-model="startPhone" placeholder="2010xxxxxxx" class="<?php echo e($waInputClass); ?> dir-ltr text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">اسم القالب</label>
                    <input type="text" x-model="startTemplate" value="hello_world" class="<?php echo e($waInputClass); ?> dir-ltr text-sm">
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">اللغة</label>
                    <input type="text" x-model="startLang" value="en_US" class="<?php echo e($waInputClass); ?> dir-ltr text-sm">
                </div>
            </div>
            <p x-show="startError" class="text-xs text-rose-600" x-text="startError"></p>
            <div class="flex gap-2 justify-end">
                <button type="button" @click="showStartModal = false" class="<?php echo e($waBtnSecondary); ?> text-sm">إلغاء</button>
                <button type="button" @click="startConversation()" :disabled="sending" class="<?php echo e($waBtnPrimary); ?> text-sm">بدء</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function whatsappInbox() {
    const cfg = window.__waInboxConfig || {};
    return {
        conversationId: cfg.conversationId || null,
        pollUrl: cfg.pollUrl,
        replyUrl: cfg.replyUrl,
        templateUrl: cfg.templateUrl,
        startUrl: cfg.startUrl,
        csrf: cfg.csrf,
        withinWindow: !!cfg.withinWindow,
        lastMessageId: cfg.lastMessageId || 0,
        unreadTotal: <?php echo e((int) $unreadTotal); ?>,
        replyBody: '',
        templateName: 'hello_world',
        templateLang: 'en_US',
        startPhone: '',
        startTemplate: 'hello_world',
        startLang: 'en_US',
        newMessages: [],
        replyError: '',
        startError: '',
        sending: false,
        showStartModal: false,
        pollTimer: null,

        init() {
            this.scrollChat();
            if (this.conversationId) {
                this.pollTimer = setInterval(() => this.poll(), 8000);
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
            if (!this.startUrl || !this.startPhone.trim() || !this.startTemplate.trim()) {
                this.startError = 'أدخل الرقم واسم القالب';
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\inbox.blade.php ENDPATH**/ ?>