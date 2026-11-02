@extends('layouts.admin')

@section('title', 'الإرشاد المهني — الحجوزات')
@section('header', 'الإرشاد المهني')

@use('App\Domains\Advising\Models\AdvisingBooking')
@use('App\Domains\Advising\Http\Controllers\Admin\AdvisingAdminController')

@section('content')
@php
    $input = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-slate-400 focus:border-slate-500';
    $statusTone = ['pending' => 'bg-amber-50 text-amber-800 border-amber-200', 'confirmed' => 'bg-sky-50 text-sky-800 border-sky-200', 'done' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'no_show' => 'bg-rose-50 text-rose-800 border-rose-200', 'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200'];
@endphp
<div class="space-y-6">
    @include('advising::admin._tabs')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4"><p class="text-xs font-bold text-amber-800">حجوزات جديدة</p><p class="text-2xl font-black text-amber-900">{{ $counts['new'] }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-600">جلسات النهارده</p><p class="text-2xl font-black text-slate-900">{{ $counts['today'] }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-600">خلال 7 أيام</p><p class="text-2xl font-black text-slate-900">{{ $counts['week'] }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs font-bold text-slate-600">مواعيد فاضية متاحة للحجز</p><p class="text-2xl font-black {{ $counts['open_slots'] ? 'text-slate-900' : 'text-rose-700' }}">{{ $counts['open_slots'] }}</p>
            @unless($counts['open_slots'])<a href="{{ route('admin.advising.availability') }}" class="text-xs font-bold text-rose-700 underline">ضيف مواعيد توافر</a>@endunless
        </div>
    </div>

    <form method="get" class="rounded-xl border border-slate-200 bg-white p-4 flex flex-wrap gap-3 items-end">
        <div class="flex flex-wrap gap-2">
            @foreach(AdvisingAdminController::RANGES as $k => $label)
                <a href="{{ route('admin.advising.bookings', array_filter(['range' => $k, 'status' => $status, 'q' => request('q')])) }}" class="px-3 py-2 rounded-lg text-sm font-bold border {{ $range === $k ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300' }}">{{ $label }}</a>
            @endforeach
        </div>
        <input type="hidden" name="range" value="{{ $range }}">
        <label class="text-xs font-bold text-slate-600 flex flex-col gap-1">الحالة
            <select name="status" class="{{ $input }}"><option value="">الكل</option>@foreach(AdvisingBooking::STATUSES as $k => $l)<option value="{{ $k }}" @selected($status === $k)>{{ $l }}</option>@endforeach</select>
        </label>
        <label class="text-xs font-bold text-slate-600 flex flex-col gap-1 flex-1 min-w-[200px]">بحث
            <input type="search" name="q" value="{{ request('q') }}" placeholder="الاسم أو الموبايل أو الإيميل" class="{{ $input }}">
        </label>
        <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-bold">تطبيق</button>
    </form>

    <div class="space-y-3">
        @forelse($bookings as $b)
            <details class="rounded-xl border border-slate-200 bg-white" @if($b->status === 'pending') open @endif>
                <summary class="cursor-pointer list-none p-4 flex flex-wrap items-center gap-x-6 gap-y-2">
                    <span class="min-w-[150px]"><span class="block font-black text-slate-900">{{ $b->starts_at->translatedFormat('l d/m') }}</span><span class="text-sm text-slate-600">{{ $b->starts_at->format('g:i A') }} · {{ $b->duration_minutes }} دقيقة</span></span>
                    <span class="flex-1 min-w-[180px]"><span class="block font-bold text-slate-900">{{ $b->name }}</span><span class="text-sm text-slate-600" dir="ltr">{{ $b->phone }}</span></span>
                    <span class="text-sm text-slate-700">{{ AdvisingBooking::label(AdvisingBooking::GOALS, $b->goal) }}</span>
                    <span class="text-sm text-slate-500">{{ AdvisingBooking::label(AdvisingBooking::MODES, $b->mode) }}</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $statusTone[$b->status] ?? '' }}">{{ AdvisingBooking::STATUSES[$b->status] ?? $b->status }}</span>
                </summary>
                <div class="border-t border-slate-100 p-4 grid lg:grid-cols-2 gap-6">
                    <dl class="text-sm space-y-2">
                        <div class="flex gap-2"><dt class="w-32 text-slate-500 font-bold">المستوى</dt><dd>{{ $b->level ? AdvisingBooking::label(AdvisingBooking::LEVELS, $b->level) : '—' }}</dd></div>
                        <div class="flex gap-2"><dt class="w-32 text-slate-500 font-bold">مهتم بـ</dt><dd>{{ $b->interest ?: '—' }}</dd></div>
                        <div class="flex gap-2"><dt class="w-32 text-slate-500 font-bold">الإيميل</dt><dd dir="ltr">{{ $b->email ?: '—' }}</dd></div>
                        <div class="flex gap-2"><dt class="w-32 text-slate-500 font-bold">عن وضعه</dt><dd class="whitespace-pre-line">{{ $b->notes ?: '—' }}</dd></div>
                        <div class="flex gap-2"><dt class="w-32 text-slate-500 font-bold">اتحجز</dt><dd>{{ $b->created_at->format('d/m/Y H:i') }}</dd></div>
                        <div class="flex gap-2 pt-2">
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $b->phone) }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg border border-emerald-300 text-emerald-800 text-xs font-bold">واتساب</a>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $b->phone) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold">اتصال</a>
                        </div>
                    </dl>
                    <form method="post" action="{{ route('admin.advising.bookings.update', $b) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <label class="text-xs font-bold text-slate-600 flex flex-col gap-1">الحالة
                                <select name="status" class="{{ $input }}">@foreach(AdvisingBooking::STATUSES as $k => $l)<option value="{{ $k }}" @selected($b->status === $k)>{{ $l }}</option>@endforeach</select>
                            </label>
                            <label class="text-xs font-bold text-slate-600 flex flex-col gap-1">المرشد
                                <select name="advisor_id" class="{{ $input }}"><option value="">—</option>@foreach($advisors as $a)<option value="{{ $a->id }}" @selected($b->advisor_id === $a->id)>{{ $a->name }}</option>@endforeach</select>
                            </label>
                        </div>
                        <label class="text-xs font-bold text-slate-600 flex flex-col gap-1">البرنامج المرشَّح
                            <input type="text" name="recommended_program" value="{{ $b->recommended_program }}" class="{{ $input }}" placeholder="مثلاً: دبلومة تحليل البيانات، دفعة ديسمبر">
                        </label>
                        <label class="text-xs font-bold text-slate-600 flex flex-col gap-1">ملخص الجلسة: احتياجاته وخطة دخوله لسوق العمل
                            <textarea name="outcome" rows="3" class="{{ $input }}">{{ $b->outcome }}</textarea>
                        </label>
                        <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-bold">حفظ</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">مفيش حجوزات هنا.</div>
        @endforelse
    </div>
    {{ $bookings->links() }}
</div>
@endsection
