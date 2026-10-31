<div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div class="flex items-center gap-3">
        @php
            $dotClass = match ($member['status']) {
                'online' => 'bg-emerald-500 animate-pulse',
                'away' => 'bg-amber-500',
                'offline', 'logged_out' => 'bg-rose-500',
                'shift_completed' => 'bg-blue-500',
                default => 'bg-slate-400',
            };
        @endphp
        <span class="w-3 h-3 rounded-full {{ $dotClass }}"></span>
        <div>
            <p class="font-semibold text-slate-900">{{ $member['name'] }}</p>
            <p class="text-xs text-slate-500">{{ $member['status_label'] }}</p>
        </div>
    </div>
    <div class="text-xs text-slate-600 sm:text-left space-y-1">
        <p>آخر نشاط: {{ $member['last_seen_human'] ?? '—' }}</p>
        <p>حضور: {{ $member['clock_in_at'] ?? '—' }} · جلسة: {{ ($member['session_active'] ?? false) ? 'نشطة' : 'منتهية' }}</p>
        <p>مخالفات اليوم: {{ $member['violations_today'] ?? 0 }} · انقطاع: {{ $member['offline_minutes_today'] ?? 0 }} د</p>
        @if(!empty($member['open_violation']))
            <p class="text-rose-700 font-semibold">انقطاع من {{ $member['open_violation']['started_at'] }}</p>
        @endif
    </div>
</div>
