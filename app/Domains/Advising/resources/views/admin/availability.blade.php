@extends('layouts.admin')

@section('title', 'الإرشاد المهني — مواعيد التوافر')
@section('header', 'الإرشاد المهني')

@use('App\Domains\Advising\Models\AdvisingAvailability')
@use('App\Domains\Advising\Models\AdvisingBooking')

@section('content')
@php
    $input = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-slate-400 focus:border-slate-500';
    $label = 'text-xs font-bold text-slate-600 flex flex-col gap-1';
    $hm = fn ($t) => substr((string) $t, 0, 5);
@endphp
<div class="space-y-6">
    @include('advising::admin._tabs')

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            {{-- إضافة توافر --}}
            <form method="post" action="{{ route('admin.advising.availability.store') }}" class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                @csrf
                <div>
                    <h2 class="font-black text-slate-900">إضافة مواعيد توافر</h2>
                    <p class="text-sm text-slate-600">اختار الأيام والفترة. الفترة بتتقسم لجلسات حسب مدة الجلسة، والطالب بيختار واحدة منها.</p>
                </div>
                <fieldset>
                    <legend class="text-xs font-bold text-slate-600 mb-2">الأيام</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach(AdvisingAvailability::WEEKDAYS as $d => $name)
                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-300 text-sm cursor-pointer has-[:checked]:bg-slate-900 has-[:checked]:text-white has-[:checked]:border-slate-900">
                                <input type="checkbox" name="weekdays[]" value="{{ $d }}" class="sr-only" @checked(in_array((string) $d, (array) old('weekdays', []), true))> {{ $name }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <label class="{{ $label }}">من<input type="time" name="start_time" value="{{ old('start_time', '18:00') }}" required class="{{ $input }}"></label>
                    <label class="{{ $label }}">إلى<input type="time" name="end_time" value="{{ old('end_time', '21:00') }}" required class="{{ $input }}"></label>
                    <label class="{{ $label }}">مدة الجلسة
                        <select name="slot_minutes" class="{{ $input }}">@foreach([15, 20, 30, 45, 60, 90] as $m)<option value="{{ $m }}" @selected((int) old('slot_minutes', 30) === $m)>{{ $m }} دقيقة</option>@endforeach</select>
                    </label>
                    <label class="{{ $label }}">طريقة الجلسة
                        <select name="mode" class="{{ $input }}">@foreach(AdvisingBooking::MODES as $k => $l)<option value="{{ $k }}" @selected(old('mode') === $k)>{{ $l[0] }}</option>@endforeach</select>
                    </label>
                    <label class="{{ $label }}">حجوزات في نفس الميعاد
                        <input type="number" name="capacity" value="{{ old('capacity', 1) }}" min="1" max="20" required class="{{ $input }}">
                    </label>
                    <label class="{{ $label }}">المرشد (اختياري)
                        <select name="advisor_id" class="{{ $input }}"><option value="">—</option>@foreach($advisors as $a)<option value="{{ $a->id }}" @selected((int) old('advisor_id') === $a->id)>{{ $a->name }}</option>@endforeach</select>
                    </label>
                </div>
                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-bold">إضافة</button>
            </form>

        </div>

        <div class="space-y-6">
            {{-- إعدادات الحجز --}}
            <form method="post" action="{{ route('admin.advising.settings.update') }}" class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                @csrf @method('PUT')
                <h2 class="font-black text-slate-900">إعدادات الحجز</h2>
                <label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="enabled" value="1" @checked($settings['enabled'])> الحجز مفتوح للطلاب</label>
                <label class="{{ $label }}">الحجز متاح لكام يوم قدام
                    <input type="number" name="window_days" min="1" max="60" value="{{ $settings['window_days'] }}" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">أقل وقت قبل الجلسة (ساعات)
                    <input type="number" name="min_notice_hours" min="0" max="72" value="{{ $settings['min_notice_hours'] }}" class="{{ $input }}">
                </label>
                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-bold">حفظ</button>
            </form>

            {{-- الأيام المقفولة --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5 space-y-4">
                <h2 class="font-black text-slate-900">أيام مقفولة</h2>
                <p class="text-sm text-slate-600 -mt-2">إجازة أو حدث: مفيش حجوزات اليوم ده حتى لو فيه توافر.</p>
                <form method="post" action="{{ route('admin.advising.blocked.store') }}" class="grid grid-cols-2 gap-2 items-end">
                    @csrf
                    <label class="{{ $label }}">اليوم<input type="date" name="date" min="{{ today()->toDateString() }}" required class="{{ $input }}"></label>
                    <label class="{{ $label }}">السبب<input type="text" name="reason" class="{{ $input }}" placeholder="اختياري"></label>
                    <button class="col-span-2 px-4 py-2 rounded-lg border border-slate-900 text-slate-900 text-sm font-bold">قفل اليوم</button>
                </form>
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($blocked as $bd)
                        <li class="flex items-center justify-between gap-2 py-2">
                            <span><b>{{ $bd->date->translatedFormat('l d/m/Y') }}</b> @if($bd->reason)<span class="text-slate-500">— {{ $bd->reason }}</span>@endif</span>
                            <form method="post" action="{{ route('admin.advising.blocked.destroy', $bd) }}">@csrf @method('DELETE')<button class="text-xs font-bold text-rose-700">فتح</button></form>
                        </li>
                    @empty
                        <li class="py-2 text-slate-500">مفيش أيام مقفولة.</li>
                    @endforelse
                </ul>
            </section>

            {{-- معاينة اللي الطالب هيشوفه --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
                <h2 class="font-black text-slate-900">اللي الطالب هيشوفه دلوقتي</h2>
                @forelse($preview as $day)
                    <div>
                        <p class="text-xs font-bold text-slate-600">{{ AdvisingAvailability::WEEKDAYS[$day['weekday']] ?? '' }} {{ \Illuminate\Support\Carbon::parse($day['date'])->format('d/m') }}</p>
                        <p class="text-sm text-slate-800">{{ collect($day['slots'])->pluck('time')->implode(' · ') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-rose-700 font-bold">مفيش مواعيد متاحة للحجز حالياً.</p>
                @endforelse
            </section>
        </div>
    </div>

    {{-- التوافر الحالي --}}
    <section class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <h2 class="font-black text-slate-900 p-4 border-b border-slate-100">مواعيد التوافر الأسبوعية</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
        <thead class="bg-slate-50 text-slate-600"><tr>
            <th class="text-right p-3">اليوم</th><th class="text-right p-3">من</th><th class="text-right p-3">إلى</th><th class="text-right p-3">الجلسة</th><th class="text-right p-3">العدد</th><th class="text-right p-3">الطريقة</th><th class="text-right p-3">المرشد</th><th class="text-right p-3">مفعّل</th><th class="p-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($rules as $r)
                @php($f = 'rule-'.$r->id)
                <tr class="{{ $r->is_active ? '' : 'bg-slate-50 text-slate-500' }}">
            <td class="p-2 min-w-[120px]"><select form="{{ $f }}" name="weekday" class="{{ $input }}">@foreach(AdvisingAvailability::WEEKDAYS as $d => $name)<option value="{{ $d }}" @selected($r->weekday === $d)>{{ $name }}</option>@endforeach</select></td>
            <td class="p-2 min-w-[130px]"><input form="{{ $f }}" type="time" name="start_time" value="{{ $hm($r->start_time) }}" class="{{ $input }}"></td>
            <td class="p-2 min-w-[130px]"><input form="{{ $f }}" type="time" name="end_time" value="{{ $hm($r->end_time) }}" class="{{ $input }}"></td>
            <td class="p-2 min-w-[100px]"><select form="{{ $f }}" name="slot_minutes" class="{{ $input }}">@foreach([15, 20, 30, 45, 60, 90] as $m)<option value="{{ $m }}" @selected($r->slot_minutes === $m)>{{ $m }} د</option>@endforeach</select></td>
            <td class="p-2 min-w-[84px]"><input form="{{ $f }}" type="number" name="capacity" min="1" max="20" value="{{ $r->capacity }}" class="{{ $input }}"></td>
            <td class="p-2 min-w-[170px]"><select form="{{ $f }}" name="mode" class="{{ $input }}">@foreach(AdvisingBooking::MODES as $k => $l)<option value="{{ $k }}" @selected($r->mode === $k)>{{ $l[0] }}</option>@endforeach</select></td>
            <td class="p-2 min-w-[150px]"><select form="{{ $f }}" name="advisor_id" class="{{ $input }}"><option value="">—</option>@foreach($advisors as $a)<option value="{{ $a->id }}" @selected($r->advisor_id === $a->id)>{{ $a->name }}</option>@endforeach</select></td>
            <td class="p-2 text-center"><input form="{{ $f }}" type="checkbox" name="is_active" value="1" @checked($r->is_active) aria-label="مفعّل"></td>
            <td class="p-2">
                <div class="flex gap-2 justify-end">
                    <form id="{{ $f }}" method="post" action="{{ route('admin.advising.availability.update', $r) }}">@csrf @method('PUT')<button class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold">حفظ</button></form>
                    <form method="post" action="{{ route('admin.advising.availability.destroy', $r) }}" onsubmit="return confirm('مسح فترة التوافر دي؟')">@csrf @method('DELETE')<button class="px-3 py-1.5 rounded-lg border border-rose-300 text-rose-700 text-xs font-bold">مسح</button></form>
                </div>
            </td>
                </tr>
            @empty
                <tr><td colspan="9" class="p-8 text-center text-slate-500">مفيش مواعيد توافر لسه. ضيف من الفورم اللي فوق، ومن غيرها الطلاب مش هيلاقوا مواعيد يحجزوها.</td></tr>
            @endforelse
        </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
