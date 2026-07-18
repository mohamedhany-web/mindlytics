<?php $__env->startSection('title', __('student.notifications_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .nt-filters {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        align-items: end;
    }
    @media (max-width: 900px) {
        .nt-filters { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 560px) {
        .nt-filters { grid-template-columns: 1fr; }
    }
    .nt-filters label {
        display: block; margin-bottom: 4px;
        font-size: 11px; font-weight: 700; color: var(--ml-muted);
    }
    .nt-filters select {
        width: 100%; min-height: 38px; padding: 0 10px;
        border-radius: 10px; border: 1px solid var(--ml-line);
        background: var(--ml-surface); color: var(--ml-ink);
        font-family: inherit; font-size: 13px;
    }
    .nt-filters select:focus {
        outline: none; border-color: rgba(73, 164, 162, 0.55);
        box-shadow: 0 0 0 3px rgba(73, 164, 162, 0.12);
    }
    .nt-list { display: flex; flex-direction: column; gap: 8px; }
    .nt-row {
        display: grid;
        grid-template-columns: 40px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: start;
        padding: 10px 12px;
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: 12px;
        transition: border-color var(--ml-fast) ease, background var(--ml-fast) ease;
    }
    .nt-row.is-unread {
        border-inline-start: 3px solid var(--ml-teal);
        background: rgba(73, 164, 162, 0.04);
    }
    .nt-row:hover { border-color: rgba(73, 164, 162, 0.35); }
    .nt-ico {
        width: 40px; height: 40px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 14px;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .nt-ico.c-blue { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
    .nt-ico.c-green { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .nt-ico.c-yellow,
    .nt-ico.c-orange { background: rgba(245, 158, 11, 0.16); color: #92400e; }
    .nt-ico.c-red { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .nt-ico.c-purple { background: rgba(100, 116, 139, 0.14); color: #475569; }
    .nt-body { min-width: 0; }
    .nt-title-row {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 2px;
    }
    .nt-title-row h3 {
        margin: 0; font-size: 13px; font-weight: 700; line-height: 1.35; color: var(--ml-ink);
    }
    .nt-msg {
        margin: 0;
        font-size: 12px; line-height: 1.5; color: var(--ml-muted);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .nt-meta {
        display: flex; flex-wrap: wrap; gap: 8px 12px;
        margin-top: 4px; font-size: 11px; color: var(--ml-muted);
    }
    .nt-meta span { display: inline-flex; align-items: center; gap: 4px; }
    .nt-action {
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: 6px; font-size: 12px; font-weight: 700;
        color: var(--ml-teal-deep); text-decoration: none;
    }
    .nt-action:hover { text-decoration: underline; }
    .nt-actions {
        display: flex; align-items: center; gap: 2px; flex-shrink: 0;
    }
    .nt-actions button,
    .nt-actions a {
        width: 32px; height: 32px; border-radius: 8px; border: 0;
        display: inline-flex; align-items: center; justify-content: center;
        background: transparent; color: var(--ml-muted); cursor: pointer;
        text-decoration: none; font-size: 12px;
        transition: background var(--ml-fast) ease, color var(--ml-fast) ease;
    }
    .nt-actions button:hover,
    .nt-actions a:hover { background: var(--ml-well); color: var(--ml-ink); }
    .nt-actions .ok:hover { color: #047857; background: rgba(16, 185, 129, 0.1); }
    .nt-actions .view:hover { color: var(--ml-teal-deep); background: rgba(73, 164, 162, 0.1); }
    .nt-actions .del:hover { color: #b91c1c; background: rgba(239, 68, 68, 0.1); }
    @media (max-width: 640px) {
        .nt-row { grid-template-columns: 36px minmax(0, 1fr); }
        .nt-actions { grid-column: 1 / -1; justify-content: flex-end; }
        .nt-ico { width: 36px; height: 36px; border-radius: 10px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.notifications_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.notifications_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.notifications_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.notifications_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($stats['total']); ?> <?php echo e(__('student.total_notifications')); ?></span>
            <?php if($stats['unread'] > 0): ?>
                <span class="oc-signal oc-signal-hot"><?php echo e($stats['unread']); ?> <?php echo e(__('student.unread_label')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="oc-pulse" aria-label="<?php echo e(__('student.notifications_title')); ?>">
        <div>
            <span class="lbl"><?php echo e(__('student.total_notifications')); ?></span>
            <span class="val"><?php echo e($stats['total']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.unread_label')); ?></span>
            <span class="val teal"><?php echo e($stats['unread']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.today_label')); ?></span>
            <span class="val hot"><?php echo e($stats['today']); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.urgent_label')); ?></span>
            <span class="val"><?php echo e($stats['urgent']); ?></span>
        </div>
    </div>

    <div class="oc-nav" style="margin-bottom:16px">
        <?php if($stats['unread'] > 0): ?>
            <button type="button" onclick="markAllAsRead()" class="oc-btn">
                <i class="fas fa-check text-xs"></i> <?php echo e(__('student.mark_all_read')); ?>

            </button>
        <?php endif; ?>
        <button type="button" onclick="cleanup()" class="oc-btn oc-btn-quiet">
            <i class="fas fa-broom text-xs"></i> <?php echo e(__('student.cleanup_btn')); ?>

        </button>
    </div>

    <section class="oc-panel" style="margin-bottom:16px">
        <form method="GET" class="nt-filters">
            <div>
                <label for="type"><?php echo e(__('student.notification_type_label')); ?></label>
                <select name="type" id="type">
                    <option value=""><?php echo e(__('student.all_types')); ?></option>
                    <?php $__currentLoopData = $notificationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="status"><?php echo e(__('common.status')); ?></label>
                <select name="status" id="status">
                    <option value=""><?php echo e(__('student.all_statuses')); ?></option>
                    <option value="unread" <?php echo e(request('status') == 'unread' ? 'selected' : ''); ?>><?php echo e(__('student.unread_label')); ?></option>
                    <option value="read" <?php echo e(request('status') == 'read' ? 'selected' : ''); ?>><?php echo e(__('student.read_filter')); ?></option>
                </select>
            </div>
            <div>
                <label for="priority"><?php echo e(__('student.priority_label')); ?></label>
                <select name="priority" id="priority">
                    <option value=""><?php echo e(__('student.all_priorities')); ?></option>
                    <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('priority') == $key ? 'selected' : ''); ?>><?php echo e($priority); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <button type="submit" class="oc-btn" style="width:100%">
                    <i class="fas fa-filter text-xs"></i> <?php echo e(__('student.filter_btn')); ?>

                </button>
            </div>
        </form>
    </section>

    <?php if($notifications->count() > 0): ?>
        <div class="nt-list">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $color = $notification->type_color ?? 'gray';
                    $prioClass = match ($notification->priority_color ?? '') {
                        'red' => 'oc-badge-bad',
                        'yellow' => 'oc-badge-warn',
                        default => 'oc-badge-live',
                    };
                ?>
                <article class="nt-row <?php echo e($notification->is_read ? '' : 'is-unread'); ?>">
                    <div class="nt-ico c-<?php echo e($color); ?>" aria-hidden="true">
                        <i class="<?php echo e($notification->type_icon ?? 'fas fa-bell'); ?>"></i>
                    </div>
                    <div class="nt-body">
                        <div class="nt-title-row">
                            <h3><?php echo e($notification->title); ?></h3>
                            <?php if($notification->priority !== 'normal'): ?>
                                <span class="oc-badge <?php echo e($prioClass); ?>"><?php echo e($priorities[$notification->priority] ?? $notification->priority); ?></span>
                            <?php endif; ?>
                            <?php if(! $notification->is_read): ?>
                                <span class="oc-badge oc-badge-live"><?php echo e(__('student.notification_new')); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="nt-msg"><?php echo e($notification->message); ?></p>
                        <div class="nt-meta">
                            <span>
                                <i class="fas fa-user"></i>
                                <?php echo e(__('student.notification_from')); ?>: <?php echo e($notification->sender->name ?? __('student.notification_system')); ?>

                            </span>
                            <span>
                                <i class="fas fa-clock"></i>
                                <?php echo e($notification->created_at->diffForHumans()); ?>

                            </span>
                            <?php if($notification->expires_at): ?>
                                <span>
                                    <i class="fas fa-hourglass-end"></i>
                                    <?php echo e(__('student.notification_expires')); ?> <?php echo e($notification->expires_at->diffForHumans()); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if($notification->action_url && $notification->action_text): ?>
                            <a href="<?php echo e(route('notifications.go', $notification)); ?>" class="nt-action">
                                <?php echo e($notification->action_text); ?>

                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="nt-actions">
                        <?php if(! $notification->is_read): ?>
                            <button type="button" class="ok" onclick="markAsRead(<?php echo e($notification->id); ?>)" title="<?php echo e(__('student.notification_mark_read')); ?>">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('notifications.show', $notification)); ?>" class="view" title="<?php echo e(__('student.notification_view')); ?>">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="del" onclick="deleteNotification(<?php echo e($notification->id); ?>)" title="<?php echo e(__('student.notification_delete')); ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($notifications->hasPages()): ?>
            <div style="margin-top:20px;display:flex;justify-content:center">
                <?php echo e($notifications->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-bell-slash"></i></div>
            <h3><?php echo e(__('student.no_notifications')); ?></h3>
            <p><?php echo e(__('student.no_notifications_desc')); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    if (!confirm(<?php echo json_encode(__('student.notification_confirm_mark_all'), 15, 512) ?>)) return;
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.message) alert(data.message);
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    if (!confirm(<?php echo json_encode(__('student.notification_confirm_delete'), 15, 512) ?>)) return;
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(error => console.error('Error:', error));
}

function cleanup() {
    if (!confirm(<?php echo json_encode(__('student.notification_confirm_cleanup'), 15, 512) ?>)) return;
    fetch('/notifications/cleanup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.message) alert(data.message);
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\notifications\index.blade.php ENDPATH**/ ?>