<?php $__env->startSection('title', $notification->title); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .nt-show-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 999px) {
        .nt-show-layout { grid-template-columns: 1fr; }
    }
    .nt-show-aside { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1000px) {
        .nt-show-aside-sticky { position: sticky; top: 12px; }
    }
    .nt-show-head {
        display: flex; align-items: flex-start; gap: 12px;
    }
    .nt-show-ico {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .nt-show-ico.c-blue { background: rgba(73,164,162,0.14); color: var(--ml-teal-deep); }
    .nt-show-ico.c-green { background: rgba(16,185,129,0.12); color: #047857; }
    .nt-show-ico.c-yellow, .nt-show-ico.c-orange { background: rgba(245,158,11,0.16); color: #92400e; }
    .nt-show-ico.c-red { background: rgba(239,68,68,0.12); color: #b91c1c; }
    .nt-show-ico.c-purple { background: rgba(100,116,139,0.14); color: #475569; }
    .nt-show-head h2 { margin: 0 0 8px; font-size: 1.15rem; font-weight: 700; line-height: 1.35; }
    .nt-show-msg {
        margin: 0; font-size: 14px; line-height: 1.75; color: var(--ml-ink);
        white-space: pre-wrap; word-break: break-word;
    }
    .nt-show-action {
        margin-top: 14px; padding: 12px 14px; border-radius: 12px;
        background: rgba(73,164,162,0.08); border: 1px solid rgba(73,164,162,0.25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;
    }
    .nt-show-action strong { display: block; font-size: 13px; margin-bottom: 2px; }
    .nt-show-action p { margin: 0; font-size: 12px; color: var(--ml-muted); }
    .nt-meta-row {
        display: flex; justify-content: space-between; gap: 10px;
        padding: 8px 0; border-bottom: 1px solid var(--ml-line); font-size: 12px;
    }
    .nt-meta-row:last-child { border-bottom: 0; }
    .nt-meta-row .k { color: var(--ml-muted); font-weight: 600; }
    .nt-meta-row .v { font-weight: 700; color: var(--ml-ink); text-align: end; }
    .nt-other a {
        display: flex; gap: 10px; align-items: center;
        padding: 8px; border-radius: 10px; text-decoration: none !important; color: inherit !important;
        transition: background var(--ml-fast) ease;
    }
    .nt-other a:hover { background: var(--ml-well); }
    .nt-other .dot { width: 6px; height: 6px; border-radius: 999px; background: var(--ml-teal); flex-shrink: 0; }
    .nt-other strong { display: block; font-size: 12px; font-weight: 700; line-height: 1.35; }
    .nt-other span { font-size: 11px; color: var(--ml-muted); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $color = $notification->type_color ?? 'gray';
    $otherNotifications = auth()->user()->customNotifications()
        ->where(function ($q) { $q->whereNull('audience')->orWhere('audience', 'student'); })
        ->where('id', '!=', $notification->id)
        ->valid()
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
?>

<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.notification_details')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route('notifications')); ?>"><?php echo e(__('student.notifications_title')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(\Illuminate\Support\Str::limit($notification->title, 28)); ?></span>
            </nav>
            <h1><?php echo e(__('student.notification_details')); ?></h1>
            <p class="sub"><?php echo e($notification->created_at->diffForHumans()); ?></p>
        </div>
        <div class="oc-signals">
            <?php if($notification->is_read): ?>
                <span class="oc-signal"><?php echo e(__('student.notification_read')); ?></span>
            <?php else: ?>
                <span class="oc-signal oc-signal-hot"><?php echo e(__('student.notification_new')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="nt-show-layout">
        <div>
            <section class="oc-panel">
                <div class="nt-show-head">
                    <div class="nt-show-ico c-<?php echo e($color); ?>" aria-hidden="true">
                        <i class="<?php echo e($notification->type_icon ?? 'fas fa-bell'); ?>"></i>
                    </div>
                    <div class="min-w-0">
                        <h2><?php echo e($notification->title); ?></h2>
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            <span class="oc-badge oc-badge-live">
                                <?php echo e(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type); ?>

                            </span>
                            <?php if($notification->priority !== 'normal'): ?>
                                <span class="oc-badge <?php echo e(($notification->priority_color ?? '') === 'red' ? 'oc-badge-bad' : 'oc-badge-warn'); ?>">
                                    <?php echo e(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <p class="nt-show-msg"><?php echo e($notification->message); ?></p>
                </div>

                <?php if($notification->action_url && $notification->action_text): ?>
                    <div class="nt-show-action">
                        <div>
                            <strong><?php echo e(__('student.notification_action_required')); ?></strong>
                            <p><?php echo e(__('student.notification_action_hint')); ?></p>
                        </div>
                        <a href="<?php echo e(route('notifications.go', $notification)); ?>" class="oc-btn">
                            <?php echo e($notification->action_text); ?>

                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if($notification->data): ?>
                    <p class="oc-label" style="margin-top:16px"><?php echo e(__('student.notification_extra_info')); ?></p>
                    <ul class="oc-facts">
                        <?php $__currentLoopData = $notification->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <span class="k"><?php echo e(ucfirst((string) $key)); ?></span>
                                <span class="v"><?php echo e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>

        <aside class="nt-show-aside">
            <div class="oc-panel nt-show-aside-sticky">
                <p class="oc-label"><?php echo e(__('student.notification_info')); ?></p>
                <div class="nt-meta-row">
                    <span class="k"><?php echo e(__('student.notification_sender')); ?></span>
                    <span class="v"><?php echo e($notification->sender->name ?? __('student.notification_system')); ?></span>
                </div>
                <div class="nt-meta-row">
                    <span class="k"><?php echo e(__('student.notification_sent_at')); ?></span>
                    <span class="v"><?php echo e($notification->created_at->format('Y-m-d H:i')); ?></span>
                </div>
                <div class="nt-meta-row">
                    <span class="k"><?php echo e(__('student.notification_read_at')); ?></span>
                    <span class="v"><?php echo e($notification->read_at ? $notification->read_at->format('Y-m-d H:i') : __('student.notification_not_read_yet')); ?></span>
                </div>
                <?php if($notification->expires_at): ?>
                    <div class="nt-meta-row">
                        <span class="k"><?php echo e(__('student.notification_expires_at')); ?></span>
                        <span class="v"><?php echo e($notification->expires_at->format('Y-m-d H:i')); ?></span>
                    </div>
                <?php endif; ?>
                <div class="nt-meta-row">
                    <span class="k"><?php echo e(__('student.notification_status')); ?></span>
                    <span class="v"><?php echo e($notification->is_read ? __('student.notification_read') : __('student.notification_new')); ?></span>
                </div>

                <div class="oc-nav" style="margin-top:14px;flex-direction:column">
                    <?php if(! $notification->is_read): ?>
                        <button type="button" onclick="markAsRead()" class="oc-btn" style="width:100%">
                            <i class="fas fa-check text-xs"></i> <?php echo e(__('student.notification_mark_read')); ?>

                        </button>
                    <?php endif; ?>
                    <button type="button" onclick="deleteNotification()" class="oc-btn oc-btn-quiet" style="width:100%;color:#b91c1c">
                        <i class="fas fa-trash text-xs"></i> <?php echo e(__('student.notification_delete_btn')); ?>

                    </button>
                    <a href="<?php echo e(route('notifications')); ?>" class="oc-btn oc-btn-quiet" style="width:100%">
                        <?php echo e(__('student.notification_all')); ?>

                    </a>
                </div>
            </div>

            <?php if($otherNotifications->count() > 0): ?>
                <div class="oc-panel nt-other">
                    <p class="oc-label"><?php echo e(__('student.notification_other')); ?></p>
                    <?php $__currentLoopData = $otherNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('notifications.show', $other)); ?>">
                            <div class="nt-show-ico c-<?php echo e($other->type_color ?? 'gray'); ?>" style="width:32px;height:32px;border-radius:9px;font-size:12px">
                                <i class="<?php echo e($other->type_icon ?? 'fas fa-bell'); ?>"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <strong class="truncate"><?php echo e($other->title); ?></strong>
                                <span><?php echo e($other->created_at->diffForHumans()); ?></span>
                            </div>
                            <?php if(! $other->is_read): ?><span class="dot" aria-hidden="true"></span><?php endif; ?>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead() {
    fetch(<?php echo json_encode(route('notifications.mark-read', $notification), 512) ?>, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(err => console.error(err));
}

function deleteNotification() {
    if (!confirm(<?php echo json_encode(__('student.notification_confirm_delete'), 15, 512) ?>)) return;
    fetch(<?php echo json_encode(route('notifications.destroy', $notification), 512) ?>, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.href = <?php echo json_encode(route('notifications'), 15, 512) ?>;
    })
    .catch(err => console.error(err));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\notifications\show.blade.php ENDPATH**/ ?>