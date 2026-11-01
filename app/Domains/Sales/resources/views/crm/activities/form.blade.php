@extends('sales::crm.layout')

@use('App\Domains\Sales\Models\SalesPlannedActivity')
@php
    $editing = $activity->exists;
    $kindLabel = SalesPlannedActivity::kindLabel($activity->kind);
@endphp

@section('title', $editing ? 'تعديل '.$kindLabel : 'جدولة '.$kindLabel)

@section('content')
<form method="post" action="{{ $editing ? route($crm->route('activities.update'), $activity->id) : route($crm->route('activities.store')) }}" style="display: flex; flex-direction: column; gap: 14px; max-width: 980px">
    @csrf
    @if($editing) @method('PUT') @endif
    @if($redirectTo)<input type="hidden" name="redirect_to" value="{{ $redirectTo }}">@endif

    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <h1 style="margin: 0; font-size: 26px; font-weight: 300; color: #4B4B4B">{{ $editing ? $activity->title.' « تعديل' : 'جدولة نشاط' }}</h1>
        <span style="flex: 1"></span>
        <button type="submit" class="crm-btn crm-btn-primary">حفظ</button>
        <a href="{{ $editing ? route($crm->route('activities.show'), $activity->id) : ($redirectTo ?: route($crm->route('activities.index'))) }}" class="crm-btn crm-btn-muted">إلغاء</a>
    </div>

    <section class="crm-card">
        <h2 style="margin: 0; padding: 10px 14px; background: #627587; color: #fff; font-size: 14px; font-weight: 700">بيانات النشاط</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; padding: 16px">
            <label class="crm-label">النوع
                @if($editing)
                    <input class="crm-input" value="{{ $kindLabel }}" disabled>
                @else
                    <select class="crm-select" name="kind" required>@foreach(SalesPlannedActivity::KINDS as $k => $v)<option value="{{ $k }}" @selected(old('kind', $activity->kind) === $k)>{{ $v }}</option>@endforeach</select>
                @endif
            </label>
            <label class="crm-label" style="grid-column: 1 / -1">الموضوع<input class="crm-input" name="title" maxlength="255" required value="{{ old('title', $activity->title) }}"></label>

            <label class="crm-label">الموعد<input class="crm-input" type="datetime-local" name="starts_at" required value="{{ old('starts_at', $activity->starts_at?->format('Y-m-d\TH:i')) }}"></label>
            <label class="crm-label">المدة (دقيقة)<input class="crm-input" type="number" name="duration_minutes" min="5" max="480" step="5" value="{{ old('duration_minutes', $activity->duration_minutes) }}"></label>
            <label class="crm-label">الأولوية
                <select class="crm-select" name="priority">@foreach(SalesPlannedActivity::PRIORITIES as $k => $v)<option value="{{ $k }}" @selected(old('priority', $activity->priority ?? 'normal') === $k)>{{ $v }}</option>@endforeach</select>
            </label>

            <label class="crm-label">المكلَّف
                <select class="crm-select" name="assigned_to" required @if($assignable->count() === 1) aria-readonly="true" @endif>
                    @foreach($assignable as $u)<option value="{{ $u->id }}" @selected((int) old('assigned_to', $activity->assigned_to) === (int) $u->id)>{{ $u->name }}{{ (int) $u->id === (int) $crm->user->id ? ' (أنا)' : '' }}</option>@endforeach
                </select>
            </label>

            <div class="crm-label" style="grid-column: 1 / -1">على
                @if($lead)
                    @unless($editing)<input type="hidden" name="sales_lead_id" value="{{ $lead->id }}">@endunless
                    <span class="crm-input" style="display: flex; align-items: center; background: #F7F8F9">عميل: {{ $lead->name }} <span class="crm-muted" dir="ltr" style="margin-inline-start: 8px">{{ $lead->phone }}</span></span>
                @elseif($student)
                    @unless($editing)<input type="hidden" name="student_user_id" value="{{ $student->id }}">@endunless
                    <span class="crm-input" style="display: flex; align-items: center; background: #F7F8F9">طالب: {{ $student->name }}</span>
                @elseif(! $editing)
                    <select class="crm-select" name="sales_lead_id">
                        <option value="">— بدون عميل (مهمة داخلية) —</option>
                        @foreach($leadOptions as $l)<option value="{{ $l->id }}" @selected((int) old('sales_lead_id') === (int) $l->id)>{{ $l->name }} — {{ $l->phone }}</option>@endforeach
                    </select>
                    <span class="crm-muted" style="font-weight: 400">المكالمة والاجتماع لازم يكونوا على عميل. الأسهل: افتح ملف العميل واضغط «جدولة».</span>
                @else
                    <span class="crm-input" style="display: flex; align-items: center; background: #F7F8F9">مهمة داخلية</span>
                @endif
            </div>

            <label class="crm-label" style="grid-column: 1 / -1">الوصف<textarea class="crm-input" name="description" rows="4" maxlength="5000" style="height: auto; padding: 8px">{{ old('description', $activity->description) }}</textarea></label>
        </div>
    </section>
    <p class="crm-muted" style="margin: 0; font-size: 12px">المكالمة والاجتماع على عميل مفتوح بيحدّثوا «المتابعة التالية» بتاعته لو الموعد أقرب، فتظهر في متابعات اليوم والكاليندر.</p>
</form>
@endsection
