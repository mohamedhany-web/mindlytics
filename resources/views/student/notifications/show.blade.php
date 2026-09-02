@extends('layouts.student-dashboard')

@section('title', $notification->title)
@section('header', __('student.notification_details'))

@section('content')
<div class="space-y-5 max-w-5xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('notifications') }}" class="sp-link">{{ __('student.notifications') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)] truncate max-w-[60vw]">{{ $notification->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <section class="sp-card p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex items-start gap-4 min-w-0">
                        <x-student.notif-icon :type="$notification->type" box="size-7" class="!w-14 !h-14" />
                        <div class="min-w-0">
                            <h2 class="sp-section-title m-0">{{ $notification->title }}</h2>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="sp-pill sp-pill--progress">
                                    {{ $notificationTypes[$notification->type] ?? $notification->type }}
                                </span>
                                @if($notification->priority !== 'normal')
                                    <span class="sp-pill {{ $notification->priority === 'urgent' ? 'sp-pill--upcoming' : 'sp-pill--done' }}">
                                        {{ $priorities[$notification->priority] ?? $notification->priority }}
                                    </span>
                                @endif
                                @if($notification->is_read)
                                    <span class="sp-pill sp-pill--done">{{ __('student.read_filter') }}</span>
                                @else
                                    <span class="sp-pill sp-pill--upcoming">{{ __('student.new_notifications') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('notifications') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors shrink-0">
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="rtl:rotate-180" />
                        {{ __('student.back_to_notifications') }}
                    </a>
                </div>

                <div class="text-sm sm:text-base text-[var(--sp-text)] leading-relaxed whitespace-pre-wrap">{{ $notification->message }}</div>

                @if($notification->action_url && $notification->action_text)
                    <div class="rounded-[20px] p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="background:var(--sp-mint)">
                        <div>
                            <p class="font-extrabold text-sm m-0 text-[var(--sp-accent-text)]">{{ __('student.notif_action_required') }}</p>
                            <p class="text-xs text-[var(--sp-accent-text)] m-0 mt-1 opacity-80">{{ __('student.notif_action_hint') }}</p>
                        </div>
                        <a href="{{ route('notifications.go', $notification) }}" class="sp-promo-btn !mt-0 shrink-0">
                            {{ $notification->action_text }}
                        </a>
                    </div>
                @endif

                @if($notification->data)
                    <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.notif_extra_data') }}</p>
                        <dl class="space-y-2 m-0">
                            @foreach($notification->data as $key => $value)
                                <div class="flex flex-wrap items-start justify-between gap-2 text-sm">
                                    <dt class="font-bold text-[var(--sp-muted)]">{{ ucfirst($key) }}</dt>
                                    <dd class="m-0 text-[var(--sp-text)] text-start">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <section class="sp-card p-5 space-y-3">
                <h3 class="font-extrabold text-sm m-0">{{ __('student.notif_meta_section') }}</h3>
                <dl class="space-y-3 m-0 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.notif_sender') }}</dt>
                        <dd class="m-0 font-extrabold text-end">{{ $notification->sender->name ?? __('student.notif_from_system') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.notif_sent_at') }}</dt>
                        <dd class="m-0 font-extrabold text-end">{{ $notification->created_at->format('Y/m/d H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.notif_read_at') }}</dt>
                        <dd class="m-0 font-extrabold text-end">
                            {{ $notification->read_at ? $notification->read_at->format('Y/m/d H:i') : __('student.notif_not_read_yet') }}
                        </dd>
                    </div>
                    @if($notification->expires_at)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.notif_expires_at') }}</dt>
                            <dd class="m-0 font-extrabold text-end {{ $notification->isExpired() ? 'text-[#b45309]' : '' }}">
                                {{ $notification->expires_at->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.notif_status') }}</dt>
                        <dd class="m-0">
                            @if($notification->is_read)
                                <span class="sp-pill sp-pill--done">{{ __('student.read_filter') }}</span>
                            @else
                                <span class="sp-pill sp-pill--upcoming">{{ __('student.unread_label') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="sp-card p-5 space-y-2">
                <h3 class="font-extrabold text-sm m-0 mb-1">{{ __('student.notif_actions') }}</h3>
                @if(!$notification->is_read)
                    <button type="button" onclick="markAsRead()" class="sp-promo-btn !mt-0 w-full border-0 cursor-pointer">
                        {{ __('student.notif_mark_read') }}
                    </button>
                @endif
                <button type="button"
                        onclick="deleteNotification()"
                        class="w-full inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[#b45309] hover:bg-[#f9e4d7] border-0 cursor-pointer">
                    {{ __('student.notif_delete') }}
                </button>
                <a href="{{ route('notifications') }}"
                   class="w-full inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                    {{ __('student.notifications') }}
                </a>
            </section>

            @if($otherNotifications->isNotEmpty())
                <section class="sp-card p-5 space-y-3">
                    <h3 class="font-extrabold text-sm m-0">{{ __('student.notif_other') }}</h3>
                    <div class="space-y-2">
                        @foreach($otherNotifications as $other)
                            <a href="{{ route('notifications.show', $other) }}" class="sp-process-row !p-3">
                                <x-student.notif-icon :type="$other->type" box="size-4" class="!w-9 !h-9" />
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-sm truncate">{{ $other->title }}</span>
                                    <span class="block text-xs mt-0.5 text-[var(--sp-muted)]">{{ $other->created_at->diffForHumans() }}</span>
                                </span>
                                @if(!$other->is_read)
                                    <span class="size-2 rounded-full bg-[var(--sp-accent)] shrink-0"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); }).catch(() => {});
}

function deleteNotification() {
    if (!confirm(@json(__('student.notif_confirm_delete')))) return;
    fetch(@json(route('notifications.destroy', $notification)), {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = @json(route('notifications'));
    }).catch(() => {});
}
</script>
@endpush
