@extends('layouts.employee')

@section('title', 'شيفتات الفريق')
@section('header', 'شيفتات وقنوات الفريق')

@section('content')
@php
    $filterUserId = $filterUserId ?? null;
    if ($board && $filterUserId) {
        foreach ($board['days'] as &$day) {
            $day['lanes'] = array_values(array_filter($day['lanes'], fn ($l) => (int) $l['user_id'] === (int) $filterUserId));
        }
        unset($day);
        $board['people_summary'] = array_values(array_filter($board['people_summary'] ?? [], fn ($p) => (int) $p['user_id'] === (int) $filterUserId));
    }
@endphp

<div class="space-y-6">
    @if(! $plan)
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-6 text-sm text-amber-900">
            <p class="font-bold">لم تُفعَّل خطة الشيفتات بعد.</p>
            <p class="mt-1">اطلب من الإدارة تفعيلها من: Admin → شيفتات وقنوات المبيعات → استيراد الجدول.</p>
        </div>
    @else
        <section class="rounded-2xl bg-white border border-violet-200 shadow-lg p-4">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-violet-700 uppercase">الآن — {{ $live['day_name'] ?? '' }} · {{ $live['hour_label'] ?? '' }}</p>
                    <h2 class="text-lg font-black text-slate-900 mt-1">من على الشيفت الآن؟</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('employee.sales-manager.shift-swaps.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 font-semibold px-3 py-2 text-sm">
                        <i class="fas fa-right-left"></i>
                        تبديلات
                        @if($pendingSwaps->count() > 0)
                            <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $pendingSwaps->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('employee.sales-manager.scorecard.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        مركز الرقابة
                    </a>
                </div>
            </div>

            @if(count($live['active_now'] ?? []) > 0)
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                    @foreach($live['active_now'] as $row)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3">
                            <p class="font-bold text-slate-900">{{ $row['user_name'] }}</p>
                            <p class="text-sm text-emerald-800 mt-1">{{ $row['channels_label'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">حتى {{ $row['end_label'] }}
                                @if(($row['mode'] ?? '') === 'home') · من البيت @endif
                            </p>
                            <a href="{{ route('employee.sales-manager.shifts.show', $row['user_id']) }}" class="text-[11px] font-semibold text-violet-700 mt-2 inline-block">تفاصيل الأسبوع</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-slate-500">لا أحد على شيفت نشط في هذه الساعة (أو خارج ساعات العمل).</p>
            @endif

            @if(! empty($live['ownership']))
                <div class="mt-4 pt-3 border-t border-violet-100">
                    <p class="text-xs font-bold text-slate-600 mb-2">ملكية القنوات الآن</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($live['ownership'] as $code => $own)
                            <span class="text-[11px] font-semibold rounded-lg bg-white border border-slate-200 px-2 py-1">
                                {{ config("sales_shifts.channels.{$code}.label", $code) }} → {{ $own['owner_name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($live['off_today'] ?? []) > 0 || count($live['not_on_shift'] ?? []) > 0)
                <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-600">
                    @if(count($live['off_today'] ?? []) > 0)
                        <span><b class="text-slate-800">أجازة/بدون شيفت:</b> {{ collect($live['off_today'])->pluck('name')->implode('، ') }}</span>
                    @endif
                    @if(count($live['not_on_shift'] ?? []) > 0)
                        <span><b class="text-slate-800">خارج الشيفت الحالي:</b> {{ collect($live['not_on_shift'])->pluck('name')->implode('، ') }}</span>
                    @endif
                </div>
            @endif
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
            <form method="get" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">
                <div>
                    <label class="text-xs font-semibold text-slate-600">فلتر موظف</label>
                    <select name="user_id" class="rounded-xl border border-slate-300 px-3 py-2 text-sm mt-1">
                        <option value="">كل الفريق</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" @selected($filterUserId == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 text-sm">تطبيق</button>
                @if($filterUserId)
                    <a href="{{ route('employee.sales-manager.shifts.show', $filterUserId) }}" class="text-sm font-semibold text-violet-700">عرض تفصيلي</a>
                @endif
            </form>
        </section>

        @if($board)
            @include('sales._shift_week_board', [
                'board' => $board,
                'navRoute' => 'employee.sales-manager.shifts.index',
                'title' => 'جدول الفريق — '.$team->name,
            ])
        @endif

        @if($pendingSwaps->isNotEmpty())
            <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-amber-900">طلبات تبديل معلّقة</h3>
                    <a href="{{ route('employee.sales-manager.shift-swaps.index') }}" class="text-xs font-semibold text-amber-800">إدارة الكل</a>
                </div>
                <ul class="divide-y divide-amber-50">
                    @foreach($pendingSwaps->take(5) as $swap)
                        <li class="px-4 py-3 text-sm">
                            <span class="font-bold">{{ $swap->requester->name }}</span> ↔ {{ $swap->partner->name }}
                            · {{ $swap->work_date->format('Y-m-d') }}
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
</div>
@endsection
