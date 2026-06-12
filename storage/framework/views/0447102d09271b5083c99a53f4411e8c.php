<?php $__env->startSection('title', $group->name . ' - Mindlytics'); ?>
<?php $__env->startSection('header', $group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('student.groups.index')); ?>" class="hover:text-sky-600">مجموعاتي</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($group->name); ?></span>
        </nav>
        <div class="flex flex-wrap items-center gap-2">
            <h1 class="text-xl font-bold text-slate-800"><?php echo e($group->name); ?></h1>
            <span class="text-sm text-slate-500"><?php echo e($group->course->title ?? '—'); ?></span>
            <span class="text-xs text-slate-500">(<?php echo e($group->members->count()); ?> / <?php echo e($group->max_members); ?> عضو)</span>
        </div>
        <?php if($group->description): ?>
            <p class="text-slate-600 text-sm mt-2"><?php echo e($group->description); ?></p>
        <?php endif; ?>
        <div class="mt-4">
            <a href="<?php echo e(route('student.groups.assignments.index', $group)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold">
                <i class="fas fa-tasks"></i>
                واجبات المجموعة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المحادثة -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center gap-2 shrink-0">
                    <i class="fas fa-comments text-sky-500"></i>
                    <h3 class="font-bold text-slate-800">محادثة المجموعة</h3>
                </div>
                <div class="p-4 min-h-[280px] max-h-[420px] overflow-y-auto overflow-x-hidden space-y-3 scroll-smooth overscroll-contain" id="group-messages" style="scrollbar-width: thin;"
                     data-messages-url="<?php echo e(route('student.groups.messages.index', $group)); ?>"
                     data-user-id="<?php echo e(auth()->id()); ?>">
                    <?php $__empty_1 = true; $__currentLoopData = $group->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex gap-3 <?php echo e($msg->user_id === auth()->id() ? 'flex-row-reverse' : ''); ?>" data-message-id="<?php echo e($msg->id); ?>">
                            <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0">
                                <?php echo e(mb_substr($msg->user->name ?? '?', 0, 1)); ?>

                            </div>
                            <div class="flex-1 min-w-0 <?php echo e($msg->user_id === auth()->id() ? 'text-left' : 'text-right'); ?>">
                                <div class="text-xs text-slate-500 mb-0.5">
                                    <?php echo e($msg->user->name ?? 'غير معروف'); ?>

                                    <span class="text-slate-400"> · <?php echo e($msg->created_at->diffForHumans()); ?></span>
                                </div>
                                <div class="text-slate-800 text-sm bg-slate-50 rounded-xl px-3 py-2 inline-block max-w-full break-words">
                                    <?php echo e($msg->body); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-slate-500 text-sm text-center py-6" id="group-messages-empty">لا توجد رسائل بعد. ابدأ المحادثة.</p>
                    <?php endif; ?>
                </div>
                <div class="p-4 border-t border-slate-200 bg-white shrink-0">
                    <form id="group-chat-form" action="<?php echo e(route('student.groups.messages.store', $group)); ?>" method="POST" class="flex gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="body" id="group-chat-input" required maxlength="2000" placeholder="اكتب رسالة..."
                               class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <button type="submit" id="group-chat-submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold shrink-0">
                            <i class="fas fa-paper-plane ml-1"></i> إرسال
                        </button>
                    </form>
                    <p id="group-chat-error" class="mt-1 text-sm text-red-500 hidden"></p>
                </div>
            </div>
        </div>

        <!-- الأعضاء -->
        <div class="space-y-4">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-friends text-sky-500"></i>
                    الأعضاء (<?php echo e($group->members->count()); ?>)
                </h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50">
                            <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-sm shrink-0">
                                <?php echo e(mb_substr($member->name ?? '?', 0, 1)); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-medium text-slate-800 block truncate"><?php echo e($member->name); ?></span>
                                <span class="text-xs text-slate-500 truncate block"><?php echo e($member->email ?? '—'); ?></span>
                            </div>
                            <?php if($member->pivot->role === 'leader'): ?>
                                <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-medium">قائد</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    #group-messages::-webkit-scrollbar { width: 8px; }
    #group-messages::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    #group-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #group-messages::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
        var alignClass = isMe ? 'text-left' : 'text-right';
        var initial = (msg.user_name || '?').charAt(0);
        return '<div class="flex gap-3 ' + rowClass + '" data-message-id="' + msg.id + '">' +
            '<div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0">' + escapeHtml(initial) + '</div>' +
            '<div class="flex-1 min-w-0 ' + alignClass + '">' +
            '<div class="text-xs text-slate-500 mb-0.5">' + escapeHtml(msg.user_name) + ' <span class="text-slate-400"> · ' + escapeHtml(msg.created_at_human) + '</span></div>' +
            '<div class="text-slate-800 text-sm bg-slate-50 rounded-xl px-3 py-2 inline-block max-w-full break-words">' + escapeHtml(msg.body) + '</div>' +
            '</div></div>';
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
            if (!r.ok) throw new Error('فشل الإرسال');
            return r.json();
        }).then(function(data) {
            if (data.success && data.message) {
                removeEmptyState();
                var wrap = document.createElement('div');
                wrap.innerHTML = buildMessageHtml(data.message);
                container.appendChild(wrap.firstElementChild);
                container.scrollTop = container.scrollHeight;
            }
            if (input) input.value = '';
        }).catch(function(err) {
            if (errorEl) { errorEl.textContent = err.message || 'حدث خطأ. حاول مرة أخرى.'; errorEl.classList.remove('hidden'); }
        }).finally(function() {
            if (submitBtn) submitBtn.disabled = false;
        });
    });

    container.scrollTop = container.scrollHeight;
    setInterval(fetchNewMessages, pollInterval);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/groups/show.blade.php ENDPATH**/ ?>