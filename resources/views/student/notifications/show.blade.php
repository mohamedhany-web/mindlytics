@extends('layouts.student-dashboard')

@section('title', $notification->title)

@push('styles')
@include('student.offline-courses.partials.los-styles')
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
@endpush

@section('content')
@php
    $color = $notification->type_color ?? 'gray';
    $otherNotifications = auth()->user()->customNotifications()
        ->where(function ($q) { $q->whereNull('audience')->orWhere('audience', 'student'); })
        ->where('id', '!=', $notification->id)
        ->valid()
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
@endphp

<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.notification_details') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('notifications') }}">{{ __('student.notifications_title') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ \Illuminate\Support\Str::limit($notification->title, 28) }}</span>
            </nav>
            <h1>{{ __('student.notification_details') }}</h1>
            <p class="sub">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        <div class="oc-signals">
            @if($notification->is_read)
                <span class="oc-signal">{{ __('student.notification_read') }}</span>
            @else
                <span class="oc-signal oc-signal-hot">{{ __('student.notification_new') }}</span>
            @endif
        </div>
    </header>

    <div class="nt-show-layout">
        <div>
            <section class="oc-panel">
                <div class="nt-show-head">
                    <div class="nt-show-ico c-{{ $color }}" aria-hidden="true">
                        <i class="{{ $notification->type_icon ?? 'fas fa-bell' }}"></i>
                    </div>
                    <div class="min-w-0">
                        <h2>{{ $notification->title }}</h2>
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            <span class="oc-badge oc-badge-live">
                                {{ \App\Models\Notification::getTypes()[$notification->type] ?? $notification->type }}
                            </span>
                            @if($notification->priority !== 'normal')
                                <span class="oc-badge {{ ($notification->priority_color ?? '') === 'red' ? 'oc-badge-bad' : 'oc-badge-warn' }}">
                                    {{ \App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <p class="nt-show-msg">{{ $notification->message }}</p>
                </div>

                @if($notification->action_url && $notification->action_text)
                    <div class="nt-show-action">
                        <div>
                            <strong>{{ __('student.notification_action_required') }}</strong>
                            <p>{{ __('student.notification_action_hint') }}</p>
                        </div>
                        <a href="{{ route('notifications.go', $notification) }}" class="oc-btn">
                            {{ $notification->action_text }}
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                @endif

                @if($notification->data)
                    <p class="oc-label" style="margin-top:16px">{{ __('student.notification_extra_info') }}</p>
                    <ul class="oc-facts">
                        @foreach($notification->data as $key => $value)
                            <li>
                                <span class="k">{{ ucfirst((string) $key) }}</span>
                                <span class="v">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <aside class="nt-show-aside">
            <div class="oc-panel nt-show-aside-sticky">
                <p class="oc-label">{{ __('student.notification_info') }}</p>
                <div class="nt-meta-row">
                    <span class="k">{{ __('student.notification_sender') }}</span>
                    <span class="v">{{ $notification->sender->name ?? __('student.notification_system') }}</span>
                </div>
                <div class="nt-meta-row">
                    <span class="k">{{ __('student.notification_sent_at') }}</span>
                    <span class="v">{{ $notification->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="nt-meta-row">
                    <span class="k">{{ __('student.notification_read_at') }}</span>
                    <span class="v">{{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i') : __('student.notification_not_read_yet') }}</span>
                </div>
                @if($notification->expires_at)
                    <div class="nt-meta-row">
                        <span class="k">{{ __('student.notification_expires_at') }}</span>
                        <span class="v">{{ $notification->expires_at->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
                <div class="nt-meta-row">
                    <span class="k">{{ __('student.notification_status') }}</span>
                    <span class="v">{{ $notification->is_read ? __('student.notification_read') : __('student.notification_new') }}</span>
                </div>

                <div class="oc-nav" style="margin-top:14px;flex-direction:column">
                    @if(! $notification->is_read)
                        <button type="button" onclick="markAsRead()" class="oc-btn" style="width:100%">
                            <i class="fas fa-check text-xs"></i> {{ __('student.notification_mark_read') }}
                        </button>
                    @endif
                    <button type="button" onclick="deleteNotification()" class="oc-btn oc-btn-quiet" style="width:100%;color:#b91c1c">
                        <i class="fas fa-trash text-xs"></i> {{ __('student.notification_delete_btn') }}
                    </button>
                    <a href="{{ route('notifications') }}" class="oc-btn oc-btn-quiet" style="width:100%">
                        {{ __('student.notification_all') }}
                    </a>
                </div>
            </div>

            @if($otherNotifications->count() > 0)
                <div class="oc-panel nt-other">
                    <p class="oc-label">{{ __('student.notification_other') }}</p>
                    @foreach($otherNotifications as $other)
                        <a href="{{ route('notifications.show', $other) }}">
                            <div class="nt-show-ico c-{{ $other->type_color ?? 'gray' }}" style="width:32px;height:32px;border-radius:9px;font-size:12px">
                                <i class="{{ $other->type_icon ?? 'fas fa-bell' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <strong class="truncate">{{ $other->title }}</strong>
                                <span>{{ $other->created_at->diffForHumans() }}</span>
                            </div>
                            @if(! $other->is_read)<span class="dot" aria-hidden="true"></span>@endif
                        </a>
                    @endforeach
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsRead() {
    fetch(@json(route('notifications.mark-read', $notification)), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(err => console.error(err));
}

function deleteNotification() {
    if (!confirm(@json(__('student.notification_confirm_delete')))) return;
    fetch(@json(route('notifications.destroy', $notification)), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.href = @json(route('notifications'));
    })
    .catch(err => console.error(err));
}
</script>
@endpush
