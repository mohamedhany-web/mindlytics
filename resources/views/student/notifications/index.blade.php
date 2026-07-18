@extends('layouts.student-dashboard')

@section('title', __('student.notifications_title'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
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
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.notifications_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.notifications_title') }}</span>
            </nav>
            <h1>{{ __('student.notifications_title') }}</h1>
            <p class="sub">{{ __('student.notifications_subtitle') }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ $stats['total'] }} {{ __('student.total_notifications') }}</span>
            @if($stats['unread'] > 0)
                <span class="oc-signal oc-signal-hot">{{ $stats['unread'] }} {{ __('student.unread_label') }}</span>
            @endif
        </div>
    </header>

    <div class="oc-pulse" aria-label="{{ __('student.notifications_title') }}">
        <div>
            <span class="lbl">{{ __('student.total_notifications') }}</span>
            <span class="val">{{ $stats['total'] }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.unread_label') }}</span>
            <span class="val teal">{{ $stats['unread'] }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.today_label') }}</span>
            <span class="val hot">{{ $stats['today'] }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.urgent_label') }}</span>
            <span class="val">{{ $stats['urgent'] }}</span>
        </div>
    </div>

    <div class="oc-nav" style="margin-bottom:16px">
        @if($stats['unread'] > 0)
            <button type="button" onclick="markAllAsRead()" class="oc-btn">
                <i class="fas fa-check text-xs"></i> {{ __('student.mark_all_read') }}
            </button>
        @endif
        <button type="button" onclick="cleanup()" class="oc-btn oc-btn-quiet">
            <i class="fas fa-broom text-xs"></i> {{ __('student.cleanup_btn') }}
        </button>
    </div>

    <section class="oc-panel" style="margin-bottom:16px">
        <form method="GET" class="nt-filters">
            <div>
                <label for="type">{{ __('student.notification_type_label') }}</label>
                <select name="type" id="type">
                    <option value="">{{ __('student.all_types') }}</option>
                    @foreach($notificationTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status">
                    <option value="">{{ __('student.all_statuses') }}</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('student.unread_label') }}</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('student.read_filter') }}</option>
                </select>
            </div>
            <div>
                <label for="priority">{{ __('student.priority_label') }}</label>
                <select name="priority" id="priority">
                    <option value="">{{ __('student.all_priorities') }}</option>
                    @foreach($priorities as $key => $priority)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="oc-btn" style="width:100%">
                    <i class="fas fa-filter text-xs"></i> {{ __('student.filter_btn') }}
                </button>
            </div>
        </form>
    </section>

    @if($notifications->count() > 0)
        <div class="nt-list">
            @foreach($notifications as $notification)
                @php
                    $color = $notification->type_color ?? 'gray';
                    $prioClass = match ($notification->priority_color ?? '') {
                        'red' => 'oc-badge-bad',
                        'yellow' => 'oc-badge-warn',
                        default => 'oc-badge-live',
                    };
                @endphp
                <article class="nt-row {{ $notification->is_read ? '' : 'is-unread' }}">
                    <div class="nt-ico c-{{ $color }}" aria-hidden="true">
                        <i class="{{ $notification->type_icon ?? 'fas fa-bell' }}"></i>
                    </div>
                    <div class="nt-body">
                        <div class="nt-title-row">
                            <h3>{{ $notification->title }}</h3>
                            @if($notification->priority !== 'normal')
                                <span class="oc-badge {{ $prioClass }}">{{ $priorities[$notification->priority] ?? $notification->priority }}</span>
                            @endif
                            @if(! $notification->is_read)
                                <span class="oc-badge oc-badge-live">{{ __('student.notification_new') }}</span>
                            @endif
                        </div>
                        <p class="nt-msg">{{ $notification->message }}</p>
                        <div class="nt-meta">
                            <span>
                                <i class="fas fa-user"></i>
                                {{ __('student.notification_from') }}: {{ $notification->sender->name ?? __('student.notification_system') }}
                            </span>
                            <span>
                                <i class="fas fa-clock"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            @if($notification->expires_at)
                                <span>
                                    <i class="fas fa-hourglass-end"></i>
                                    {{ __('student.notification_expires') }} {{ $notification->expires_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        @if($notification->action_url && $notification->action_text)
                            <a href="{{ route('notifications.go', $notification) }}" class="nt-action">
                                {{ $notification->action_text }}
                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                    <div class="nt-actions">
                        @if(! $notification->is_read)
                            <button type="button" class="ok" onclick="markAsRead({{ $notification->id }})" title="{{ __('student.notification_mark_read') }}">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                        <a href="{{ route('notifications.show', $notification) }}" class="view" title="{{ __('student.notification_view') }}">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="del" onclick="deleteNotification({{ $notification->id }})" title="{{ __('student.notification_delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </article>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div style="margin-top:20px;display:flex;justify-content:center">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-bell-slash"></i></div>
            <h3>{{ __('student.no_notifications') }}</h3>
            <p>{{ __('student.no_notifications_desc') }}</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    if (!confirm(@json(__('student.notification_confirm_mark_all')))) return;
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
    if (!confirm(@json(__('student.notification_confirm_delete')))) return;
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(error => console.error('Error:', error));
}

function cleanup() {
    if (!confirm(@json(__('student.notification_confirm_cleanup')))) return;
    fetch('/notifications/cleanup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
@endpush
