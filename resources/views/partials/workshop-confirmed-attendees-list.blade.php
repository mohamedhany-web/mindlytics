@php
    $attendees = $confirmedAttendees ?? collect();
    $count = $confirmedCount ?? $attendees->count();
    $showPhone = $showPhone ?? false;
@endphp
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden {{ $wrapperClass ?? '' }}">
    <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-check text-emerald-600"></i>
                {{ $title ?? 'من أكّدوا الحضور' }}
            </h4>
            @if(!empty($subtitle))
                <p class="text-[11px] text-slate-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
            {{ number_format($count) }}
        </span>
    </div>
    <div class="overflow-y-auto divide-y divide-slate-50" style="max-height: {{ $maxHeight ?? '360px' }}">
        @forelse($attendees as $attendee)
            <div class="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-slate-50/80 text-sm">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-[10px] font-bold">
                        {{ mb_substr($attendee->name, 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ $attendee->name }}</p>
                        @if($showPhone && $attendee->phone)
                            <p class="text-[11px] text-slate-500 truncate" dir="ltr">{{ $attendee->phone }}</p>
                        @endif
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 whitespace-nowrap flex-shrink-0">
                    {{ $attendee->checked_in_at?->format('Y-m-d H:i') }}
                </span>
            </div>
        @empty
            <div class="px-4 py-10 text-center text-slate-500">
                <i class="fas fa-user-clock text-2xl text-slate-300 mb-2 block"></i>
                <p class="text-xs font-medium">{{ $emptyText ?? 'لا يوجد مؤكدون بعد' }}</p>
            </div>
        @endforelse
    </div>
</div>
