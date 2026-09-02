@extends('layouts.student-dashboard')

@section('title', __('student.notifications_title'))
@section('header', __('student.notifications_title'))

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.notif_index_eyebrow') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ __('student.notif_index_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            @if($stats['unread'] > 0)
                <button type="button" onclick="markAllAsRead()" class="sp-promo-btn !mt-0 border-0 cursor-pointer">
                    {{ __('student.mark_all_read') }}
                </button>
            @endif
            <button type="button"
                    onclick="cleanup()"
                    class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors border-0 cursor-pointer">
                {{ __('student.cleanup_btn') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.total_notifications') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-notifications.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.unread_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['unread'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-messages.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.today_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['today'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-calendar.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.urgent_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['urgent'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:#f9e4d7">
                    <x-student.figma-icon name="icon-exams.svg" />
                </span>
            </div>
        </div>
    </div>

    <div class="sp-card p-4 sm:p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
            <div class="xl:col-span-2">
                <label for="notif-q" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.notif_search_placeholder') }}</label>
                <div class="sp-search !shadow-none !bg-[#f7f7f5] w-full">
                    <x-student.figma-icon name="icon-search.svg" box="size-5" />
                    <input type="search" id="notif-q" name="q" value="{{ request('q') }}" placeholder="{{ __('student.notif_search_placeholder') }}" class="!text-sm">
                </div>
            </div>
            <div>
                <label for="type" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.notification_type_label') }}</label>
                <select name="type" id="type" class="w-full rounded-[16px] border border-black/5 bg-[#f7f7f5] px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]">
                    <option value="">{{ __('student.all_types') }}</option>
                    @foreach($notificationTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('type') == $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('common.status') }}</label>
                <select name="status" id="status" class="w-full rounded-[16px] border border-black/5 bg-[#f7f7f5] px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]">
                    <option value="">{{ __('student.all_statuses') }}</option>
                    <option value="unread" @selected(request('status') == 'unread')>{{ __('student.unread_label') }}</option>
                    <option value="read" @selected(request('status') == 'read')>{{ __('student.read_filter') }}</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.priority_label') }}</label>
                <select name="priority" id="priority" class="w-full rounded-[16px] border border-black/5 bg-[#f7f7f5] px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]">
                    <option value="">{{ __('student.all_priorities') }}</option>
                    @foreach($priorities as $key => $label)
                        <option value="{{ $key }}" @selected(request('priority') == $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 xl:col-span-5 flex flex-wrap gap-2 pt-1">
                <button type="submit" class="sp-promo-btn !mt-0 border-0 cursor-pointer">{{ __('student.filter_btn') }}</button>
                @if(request()->hasAny(['q', 'type', 'status', 'priority']))
                    <a href="{{ route('notifications') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)]">
                        {{ __('student.clear_search') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <article class="sp-card p-4 sm:p-5 {{ !$notification->is_read ? 'ring-2 ring-[var(--sp-accent)] ring-offset-2' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <x-student.notif-icon :type="$notification->type" box="size-5" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="font-extrabold text-[15px] m-0 leading-snug">{{ $notification->title }}</h3>
                                    @if(!$notification->is_read)
                                        <span class="sp-pill sp-pill--progress">{{ __('student.new_notifications') }}</span>
                                    @endif
                                    @if($notification->priority !== 'normal')
                                        <span class="sp-pill {{ $notification->priority === 'urgent' ? 'sp-pill--upcoming' : 'sp-pill--done' }}">
                                            {{ $priorities[$notification->priority] ?? $notification->priority }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] font-bold text-[var(--sp-muted)] rounded-full bg-[#f7f7f5] px-2 py-0.5">
                                        {{ $notificationTypes[$notification->type] ?? $notification->type }}
                                    </span>
                                </div>

                                <p class="text-sm text-[var(--sp-text)] m-0 leading-relaxed line-clamp-3">{{ $notification->message }}</p>

                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-xs font-bold text-[var(--sp-muted)]">
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-student.figma-icon name="icon-profile.svg" box="size-3.5" />
                                        {{ __('student.notif_from_sender', ['name' => $notification->sender->name ?? __('student.notif_from_system')]) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-student.figma-icon name="icon-calendar.svg" box="size-3.5" />
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if($notification->expires_at)
                                        <span class="inline-flex items-center gap-1.5">
                                            {{ __('student.notif_expires') }} {{ $notification->expires_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                @if($notification->action_url && $notification->action_text)
                                    <a href="{{ route('notifications.go', $notification) }}"
                                       class="sp-link inline-flex items-center gap-1.5 text-sm font-extrabold mt-3">
                                        {{ $notification->action_text }}
                                        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-60 rtl:rotate-180" />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 sm:flex-col sm:items-stretch">
                            @if(!$notification->is_read)
                                <button type="button"
                                        onclick="markAsRead({{ $notification->id }})"
                                        class="inline-flex items-center justify-center rounded-[30px] px-3 py-2 text-xs font-extrabold bg-[var(--sp-mint)] text-[var(--sp-accent-text)] hover:opacity-90 border-0 cursor-pointer"
                                        title="{{ __('student.notif_mark_read') }}">
                                    {{ __('student.notif_mark_read') }}
                                </button>
                            @endif
                            <a href="{{ route('notifications.show', $notification) }}"
                               class="inline-flex items-center justify-center rounded-[30px] px-3 py-2 text-xs font-extrabold bg-[var(--sp-accent)] text-[var(--sp-accent-text)] hover:opacity-90">
                                {{ __('student.notif_open') }}
                            </a>
                            <button type="button"
                                    onclick="deleteNotification({{ $notification->id }})"
                                    class="inline-flex items-center justify-center rounded-[30px] px-3 py-2 text-xs font-extrabold bg-[#f7f7f5] text-[#b45309] hover:bg-[#f9e4d7] border-0 cursor-pointer"
                                    title="{{ __('student.notif_delete') }}">
                                {{ __('student.notif_delete') }}
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div class="flex justify-center pt-2">{{ $notifications->links() }}</div>
        @endif
    @else
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                <x-student.figma-icon name="icon-notifications.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.no_notifications') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-md mx-auto">{{ __('student.notif_empty_hint') }}</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const csrf = '{{ csrf_token() }}';

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); }).catch(() => {});
}

function markAllAsRead() {
    if (!confirm(@json(__('student.notif_confirm_mark_all')))) return;
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); }).catch(() => {});
}

function deleteNotification(notificationId) {
    if (!confirm(@json(__('student.notif_confirm_delete')))) return;
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); }).catch(() => {});
}

function cleanup() {
    if (!confirm(@json(__('student.notif_confirm_cleanup')))) return;
    fetch('/notifications/cleanup', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); }).catch(() => {});
}
</script>
@endpush
