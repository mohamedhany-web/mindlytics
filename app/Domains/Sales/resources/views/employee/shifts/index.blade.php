@extends('layouts.employee')

@section('title', 'جدول الشيفتات')
@section('header', 'جدول الشيفتات والقنوات')

@section('content')
@php
    $user = auth()->user();
    $today = $board['my_today'] ?? null;
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if(! $board)
        <div class="rounded-2xl bg-white border border-slate-200 p-8 text-center text-slate-600 text-sm">
            لم يُفعَّل جدول الشيفتات بعد. تواصل مع الإدارة.
        </div>
    @else
        @if($today)
            <section class="rounded-2xl bg-white border border-violet-200 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-violet-700 uppercase">شيفتك اليوم — {{ $today['day_name'] ?? '' }}</p>
                        @if($today['is_working_today'] ?? false)
                            @if($today['current'] ?? null)
                                <p class="text-xl font-black text-slate-900 mt-1">
                                    الآن: {{ $today['current']['channels_label'] }}
                                    <span class="text-sm font-semibold text-slate-500">حتى {{ $today['current']['end_label'] }}</span>
                                </p>
                                @if(($today['current']['mode'] ?? '') === 'home')
                                    <p class="text-xs text-amber-700 font-semibold mt-1"><i class="fas fa-house"></i> من البيت (opener)</p>
                                @endif
                            @elseif($today['next'] ?? null)
                                <p class="text-lg font-bold text-slate-800 mt-1">التالي: {{ $today['next']['channels_label'] }} — {{ $today['next']['start_label'] }}</p>
                            @else
                                <p class="text-sm text-slate-600 mt-1">انتهى شيفتك اليوم</p>
                            @endif
                        @else
                            <p class="text-lg font-bold text-slate-700 mt-1">{{ $today['message'] ?? 'يوم راحة' }}</p>
                        @endif
                    </div>
                    @if(! empty($board['ownership_now']))
                        <div class="text-xs text-slate-600 space-y-1">
                            @foreach(array_slice($board['ownership_now'], 0, 4) as $code => $own)
                                <p>{{ config("sales_shifts.channels.{$code}.label", $code) }}: <b>{{ $own['owner_name'] }}</b></p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @include('sales._shift_week_board', [
            'board' => $board,
            'navRoute' => 'employee.sales.shifts.index',
            'highlightUserId' => $user->id,
            'title' => 'أسبوعي — شيفتك والقنوات',
        ])

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
                <h3 class="text-sm font-black text-slate-900 mb-3">طلب تبديل شيفت</h3>
                <p class="text-xs text-slate-500 mb-3">التبديل يُكتب هنا ويُعتمد من مدير المبيعات — مش اتفاق شخصي.</p>
                <form method="post" action="{{ route('employee.sales.shifts.swap.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-slate-600">مع من؟</label>
                        <select name="partner_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach($colleagues as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">تاريخ الشيفت</label>
                        <input type="date" name="work_date" required value="{{ today()->toDateString() }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">السبب</label>
                        <textarea name="reason" rows="3" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="اكتب سبب التبديل بوضوح…"></textarea>
                    </div>
                    <button type="submit" class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-4 py-2 text-sm w-full">إرسال الطلب</button>
                </form>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-sm font-black text-slate-900">طلبات التبديل</h3>
                </div>
                <ul class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    @forelse($swaps as $swap)
                        <li class="px-4 py-3 text-sm">
                            <p class="font-bold text-slate-900">{{ $swap->requester->name }} ↔ {{ $swap->partner->name }}</p>
                            <p class="text-xs text-slate-500">{{ $swap->work_date->format('Y-m-d') }} · {{ $swap->statusLabel() }}</p>
                            @if((int) $swap->requester_id === (int) $user->id && $swap->status === 'pending')
                                <form method="post" action="{{ route('employee.sales.shifts.swap.cancel', $swap) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 font-semibold">إلغاء</button>
                                </form>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-slate-500 text-sm">لا طلبات.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    @endif
</div>
@endsection
