
<?php
    $threadKey = $kind.'-'.$panelType.'-'.$item->id;
    $listUrl = route('my-courses.learn-discussions.index', $course);
    $storeUrl = route('my-courses.learn-discussions.store', $course);
    $placeholder = $kind === 'qa'
        ? 'اكتب سؤالك للمدرب…'
        : 'شارك فكرة أو سؤالاً مع زملائك…';
    $emptyLabel = $kind === 'qa'
        ? 'لا توجد أسئلة بعد — كن أول من يسأل المدرب.'
        : 'لا توجد مشاركات بعد — ابدأ النقاش.';
?>
<div class="learn-thread"
     x-data="learnThreadPanel({
        key: <?php echo \Illuminate\Support\Js::from($threadKey)->toHtml() ?>,
        kind: <?php echo \Illuminate\Support\Js::from($kind)->toHtml() ?>,
        contextType: <?php echo \Illuminate\Support\Js::from($panelType)->toHtml() ?>,
        contextId: <?php echo e((int) $item->id); ?>,
        listUrl: <?php echo \Illuminate\Support\Js::from($listUrl)->toHtml() ?>,
        storeUrl: <?php echo \Illuminate\Support\Js::from($storeUrl)->toHtml() ?>,
        csrf: <?php echo \Illuminate\Support\Js::from(csrf_token())->toHtml() ?>,
        emptyLabel: <?php echo \Illuminate\Support\Js::from($emptyLabel)->toHtml() ?>,
        placeholder: <?php echo \Illuminate\Support\Js::from($placeholder)->toHtml() ?>
     })"
     @learn-load-thread.window="onLoadEvent($event.detail)"
     @learn-panel-tab.window="onTabEvent($event.detail)">
    <div class="learn-thread-composer">
        <textarea x-model="body"
                  :placeholder="placeholder"
                  rows="3"
                  maxlength="5000"
                  class="learn-thread-input"
                  @keydown.ctrl.enter.prevent="submit()"></textarea>
        <div class="learn-thread-composer-bar">
            <span class="learn-thread-hint" x-text="kind === 'qa' ? 'سيظهر سؤالك للمدرب مباشرة' : 'النقاش مرئي لزملائك في هذا الدرس'"></span>
            <button type="button" class="learn-thread-submit" @click="submit()" :disabled="sending || body.trim().length < 2">
                <span x-show="!sending">إرسال</span>
                <span x-show="sending" x-cloak>جاري الإرسال…</span>
            </button>
        </div>
        <p class="learn-thread-error" x-show="error" x-text="error" x-cloak></p>
    </div>

    <div class="learn-thread-list" x-show="!loading || items.length">
        <template x-for="item in items" :key="item.id">
            <article class="learn-thread-post" :class="{ 'is-instructor': item.is_instructor }">
                <header class="learn-thread-post-h">
                    <strong x-text="item.user.name"></strong>
                    <span class="learn-thread-role" x-text="item.user.role_label"></span>
                    <time x-text="item.created_at"></time>
                </header>
                <p class="learn-thread-body" x-text="item.body"></p>
                <div class="learn-thread-replies" x-show="item.replies && item.replies.length">
                    <template x-for="reply in item.replies" :key="reply.id">
                        <div class="learn-thread-reply" :class="{ 'is-instructor': reply.is_instructor }">
                            <header class="learn-thread-post-h">
                                <strong x-text="reply.user.name"></strong>
                                <span class="learn-thread-role" x-text="reply.user.role_label"></span>
                                <time x-text="reply.created_at"></time>
                            </header>
                            <p class="learn-thread-body" x-text="reply.body"></p>
                        </div>
                    </template>
                </div>
                <div class="learn-thread-reply-box">
                    <button type="button" class="learn-thread-reply-toggle" @click="toggleReply(item.id)" x-text="replyOpen === item.id ? 'إلغاء' : 'رد'"></button>
                    <div x-show="replyOpen === item.id" x-cloak class="learn-thread-reply-form">
                        <textarea x-model="replyBody" rows="2" class="learn-thread-input" placeholder="اكتب ردك…"></textarea>
                        <button type="button" class="learn-thread-submit" @click="submitReply(item)" :disabled="sending || replyBody.trim().length < 2">إرسال الرد</button>
                    </div>
                </div>
            </article>
        </template>
    </div>

    <div class="learn-discussion-placeholder" x-show="loading" x-cloak>
        <p>جاري التحميل…</p>
    </div>
    <div class="learn-discussion-placeholder" x-show="!loading && items.length === 0" x-cloak>
        <i class="fas <?php echo e($kind === 'qa' ? 'fa-question-circle' : 'fa-comments'); ?> text-3xl text-[#CBD5E1] mb-3 block"></i>
        <p x-text="emptyLabel"></p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\partials\learn-thread-panel.blade.php ENDPATH**/ ?>