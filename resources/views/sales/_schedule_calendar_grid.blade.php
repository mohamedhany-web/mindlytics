@php
    $days = $grid['days'];
    $rows = $grid['rows'];
    $statusStyles = [
        'working' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'on_leave' => 'bg-rose-50 border-rose-200 text-rose-900',
        'weekly_off' => 'bg-slate-100 border-slate-200 text-slate-600',
        'off' => 'bg-amber-50 border-amber-200 text-amber-900',
    ];
    $modeLabels = ['online' => 'أونلاين', 'offline' => 'أوفلاين'];
@endphp

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-calendar-week text-sky-600"></i>
                تقويم الشيفت والإجازات
            </h2>
            <p class="text-xs text-slate-600 mt-0.5">
                {{ $scopeLabel ?? '' }} ·
                {{ $weekStart->copy()->locale('ar')->translatedFormat('d M') }}
                —
                {{ $weekEnd->copy()->locale('ar')->translatedFormat('d M Y') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route($routeName, ['week' => $prevWeek]) }}"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">
                <i class="fas fa-chevron-right text-xs"></i> الأسبوع السابق
            </a>
            <a href="{{ route($routeName) }}"
               class="inline-flex items-center gap-1 rounded-xl bg-sky-600 hover:bg-sky-700 px-3 py-2 text-sm font-semibold text-white">
                هذا الأسبوع
            </a>
            <a href="{{ route($routeName, ['week' => $nextWeek]) }}"
               class="inline-flex items-center gap-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">
                الأسبوع التالي <i class="fas fa-chevron-left text-xs"></i>
            </a>
        </div>
    </div>

    <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap gap-3 text-[11px] font-semibold">
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-200 border border-emerald-300"></span> عمل</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-200 border border-rose-300"></span> إجازة معتمدة</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-200 border border-slate-300"></span> إجازة أسبوعية</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-200 border border-amber-300"></span> يوم راحة</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-[960px] w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                    <th class="px-3 py-3 text-right font-semibold sticky right-0 bg-slate-50 min-w-[140px]">الموظف</th>
                    @foreach($days as $day)
                        <th class="px-2 py-3 text-center font-semibold min-w-[110px]">
                            <span class="block">{{ $day->copy()->locale('ar')->translatedFormat('D') }}</span>
                            <span class="block text-[11px] font-medium text-slate-500 tabular-nums">{{ $day->format('d/m') }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-slate-50/40">
                        <td class="px-3 py-3 sticky right-0 bg-white border-l border-slate-100">
                            <p class="font-bold text-slate-900">{{ $row['user']->name }}</p>
                            @if($row['user']->workSchedule)
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $row['user']->workSchedule->name }}</p>
                            @endif
                        </td>
                        @foreach($row['days'] as $cell)
                            @php $style = $statusStyles[$cell['status']] ?? $statusStyles['off']; @endphp
                            <td class="px-1.5 py-2 align-top">
                                <div class="rounded-xl border px-2 py-2 h-full {{ $style }}">
                                    <p class="text-[10px] font-black">{{ $cell['status_label'] }}</p>
                                    @if($cell['is_working'] && $cell['shift_start'] && $cell['shift_end'])
                                        <p class="text-xs font-bold tabular-nums mt-1 dir-ltr text-right">
                                            {{ $cell['shift_start'] }} – {{ $cell['shift_end'] }}
                                        </p>
                                        @if($cell['mode'])
                                            <p class="text-[10px] mt-0.5 opacity-80">{{ $modeLabels[$cell['mode']] ?? $cell['mode'] }}</p>
                                        @endif
                                    @elseif(! $cell['is_working'] && $cell['shift_start'] && $cell['shift_end'])
                                        <p class="text-[10px] tabular-nums mt-1 opacity-60 dir-ltr text-right">
                                            شيفت: {{ $cell['shift_start'] }}–{{ $cell['shift_end'] }}
                                        </p>
                                    @else
                                        <p class="text-[10px] mt-1 opacity-60">بدون شيفت</p>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-500 text-sm">لا يوجد موظفون لعرضهم في هذا النطاق.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
