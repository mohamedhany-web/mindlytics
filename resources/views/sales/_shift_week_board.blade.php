@php
    $board = $board ?? null;
    if (! $board) {
        return;
    }
    $days = $board['days'] ?? [];
    $workStart = (int) ($board['work_start_hour'] ?? 10);
    $workEnd = (int) ($board['work_end_hour'] ?? 26);
    $span = max(1, $workEnd - $workStart);
    $highlightUserId = $highlightUserId ?? null;
    $compact = $compact ?? false;
    $schedule = app(\App\Services\SalesShiftScheduleService::class);
@endphp

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden {{ $compact ? '' : '' }}">
    @if(! $compact)
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-week text-violet-600"></i>
                    {{ $title ?? 'شيفتات وقنوات الفريق' }}
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">
                    {{ $board['week_start']->copy()->locale('ar')->translatedFormat('d M') }}
                    —
                    {{ $board['week_end']->copy()->locale('ar')->translatedFormat('d M Y') }}
                    · يوم العمل {{ $schedule->formatHourLabel($workStart) }} – {{ $schedule->formatHourLabel($workEnd) }}
                </p>
            </div>
            @if(! empty($navRoute))
                <div class="flex flex-wrap gap-2">
                    @php $navParams = $navRouteParams ?? []; @endphp
                    <a href="{{ route($navRoute, array_merge($navParams, ['week' => $board['prev_week']])) }}"
                       class="inline-flex items-center gap-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">
                        <i class="fas fa-chevron-right text-xs"></i> السابق
                    </a>
                    <a href="{{ route($navRoute, $navParams) }}"
                       class="inline-flex items-center gap-1 rounded-xl bg-violet-600 hover:bg-violet-700 px-3 py-2 text-sm font-semibold text-white">
                        هذا الأسبوع
                    </a>
                    <a href="{{ route($navRoute, array_merge($navParams, ['week' => $board['next_week']])) }}"
                       class="inline-flex items-center gap-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">
                        التالي <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if(! empty($board['ownership_now']))
        <div class="px-4 py-3 border-b border-slate-100 bg-violet-50/60 flex flex-wrap gap-2">
            <span class="text-xs font-bold text-violet-900 w-full mb-1">مالك القناة الآن:</span>
            @foreach($board['ownership_now'] as $code => $own)
                @php $ch = config("sales_shifts.channels.{$code}.label", $code); @endphp
                <span class="text-[11px] font-semibold rounded-lg bg-white border border-violet-200 text-violet-900 px-2 py-1">
                    {{ $ch }} → {{ $own['owner_name'] }}
                    @if($own['can_takeover'])
                        <span class="text-amber-600">(يمكن التدخل)</span>
                    @endif
                </span>
            @endforeach
        </div>
    @endif

    <div class="p-4 overflow-x-auto">
        <div class="min-w-[980px]">
            {{-- ruler --}}
            <div class="grid grid-cols-[92px_1fr] mb-2">
                <div></div>
                <div class="relative h-6 mr-[100px]">
                    @for($h = $workStart; $h <= $workEnd; $h += 2)
                        <span class="absolute text-[11px] font-semibold text-slate-500 -translate-x-1/2 tabular-nums"
                              style="right: {{ (($h - $workStart) / $span) * 100 }}%">
                            {{ $schedule->formatHourLabel($h) }}
                        </span>
                    @endfor
                </div>
            </div>

            @foreach($days as $day)
                <div class="grid grid-cols-[92px_1fr] border-t border-slate-200 py-3 {{ !empty($day['is_today']) ? 'bg-sky-50/40 -mx-4 px-4 rounded-xl' : '' }}">
                    <div class="pr-2">
                        <p class="font-bold text-slate-900">{{ $day['name'] }}</p>
                        <p class="text-[10px] text-slate-500 tabular-nums">{{ $day['date_str'] ?? '' }}</p>
                        @if(! empty($day['location_badge']))
                            <span class="inline-block mt-1 text-[10px] font-semibold border border-slate-300 rounded px-1.5 py-0.5 text-slate-600">{{ $day['location_badge'] }}</span>
                        @endif
                        @if(! empty($day['off_today']))
                            <span class="block mt-1 text-[10px] text-slate-500">
                                أجازة: {{ collect($day['off_today'])->pluck('name')->implode(' + ') }}
                            </span>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        @forelse($day['lanes'] as $lane)
                            @php
                                $isMe = $highlightUserId && (int) $lane['user_id'] === (int) $highlightUserId;
                                $color = $lane['color'] ?? '#0EA5E9';
                            @endphp
                            <div class="grid grid-cols-[100px_1fr] items-center gap-2 {{ $isMe ? 'ring-2 ring-violet-400 ring-offset-1 rounded-lg p-1' : '' }}">
                                <div class="text-right">
                                    <p class="text-sm font-bold leading-tight" style="color: {{ $color }}">{{ $lane['user_name'] }}</p>
                                    <p class="text-[10px] text-slate-500 tabular-nums">{{ $lane['from_label'] }} – {{ $lane['to_label'] }}</p>
                                </div>
                                <div class="relative h-10 rounded-lg bg-gradient-to-l from-slate-100 to-slate-50 overflow-hidden">
                                    <span class="absolute top-0 bottom-0 w-px bg-slate-200" style="right: 25%"></span>
                                    <span class="absolute top-0 bottom-0 w-px bg-slate-200" style="right: 50%"></span>
                                    @foreach($lane['segments'] as $seg)
                                        <div class="absolute top-0 bottom-0 flex items-center justify-center text-[10px] font-bold overflow-hidden px-1
                                            {{ $seg['is_home'] ? 'border-2 border-dashed text-slate-700' : 'text-slate-900 shadow-inner' }}"
                                             style="right: {{ $seg['left_pct'] }}%; width: {{ $seg['width_pct'] }}%;
                                                    {{ $seg['is_home'] ? "border-color: {$color}; color: {$color};" : "background: {$color};" }}"
                                             title="{{ $seg['channels_label'] }}">
                                            <span class="truncate">{{ $seg['channels_label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">لا توجد شيفتات لهذا اليوم.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if(! $compact && ! empty($board['people_summary']))
        <div class="px-4 pb-4 border-t border-slate-100 pt-4">
            <h3 class="text-sm font-black text-slate-900 mb-3">ملخص الفريق</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                @foreach($board['people_summary'] as $person)
                    <div class="rounded-xl border border-slate-200 p-3 border-t-4" style="border-top-color: {{ $person['color'] }}">
                        <p class="font-bold" style="color: {{ $person['color'] }}">{{ $person['name'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">{{ $person['base_channels'] }}</p>
                        <div class="mt-2 space-y-0.5 text-[11px] text-slate-600">
                            <p>أيام: <b>{{ $person['work_days'] }}</b> · ساعات: <b>{{ $person['total_hours'] }}</b></p>
                            <p>ليلي: <b>{{ $person['night_shifts'] }}</b> · أجازة: <b>{{ $person['weekly_off'] }}</b></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(! $compact && ! empty($board['rules']))
        <div class="px-4 pb-4">
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <h3 class="text-sm font-black text-slate-900 mb-2">قواعد العمل</h3>
                <ul class="space-y-1.5 text-xs text-slate-700">
                    @foreach($board['rules'] as $rule)
                        <li class="flex gap-2"><span class="text-violet-500">▪</span> {{ $rule }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</section>
