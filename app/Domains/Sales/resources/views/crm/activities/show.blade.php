@extends('sales::crm.layout')

@section('title', $activity->title)

@use('App\Domains\Sales\Models\SalesPlannedActivity')
@use('App\Domains\Sales\Models\SalesActivity')
@php
    $late = $activity->isOverdue();
    $statusClass = match ($activity->status) { 'done' => 'crm-good', 'missed', 'cancelled' => 'crm-bad', default => $late ? 'crm-bad' : '' };
    $canStage = $activity->kind === 'call' && $activity->lead && $crm->canWorkOn($activity->lead);
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <a href="{{ route($crm->route('activities.index')) }}" aria-label="رجوع للأنشطة" style="width: 28px; height: 28px; border: 1px solid #534D64; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #534D64"><i class="fas fa-chevron-right"></i></a>
        <h1 style="margin: 0; font-size: 26px; font-weight: 400"><i class="fas {{ SalesPlannedActivity::KIND_ICONS[$activity->kind] ?? 'fa-circle' }}" style="color: #534D64; font-size: 20px"></i> {{ $activity->title }}</h1>
        <span style="flex: 1"></span>
        @if($crm->canEditActivity($activity))
            <a href="{{ route($crm->route('activities.edit'), $activity->id) }}" class="crm-btn crm-btn-slate">تعديل / تأجيل</a>
            <form method="post" action="{{ route($crm->route('activities.cancel'), $activity->id) }}" onsubmit="return confirm('إلغاء النشاط ده؟')">
                @csrf
                <button type="submit" class="crm-btn crm-btn-muted">إلغاء النشاط</button>
            </form>
        @endif
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <div style="flex: 999 1 640px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">النوع</span><span class="v">{{ SalesPlannedActivity::kindLabel($activity->kind) }}</span></div>
                    <div class="crm-field"><span class="k">الحالة</span><span class="v {{ $statusClass }}">{{ SalesPlannedActivity::statusLabel($activity->status) }}{{ $late ? ' (متأخر)' : '' }}</span></div>
                    <div class="crm-field"><span class="k">الموعد</span><span class="v">{{ $activity->starts_at->format('d/m/Y H:i') }}{{ $activity->duration_minutes ? ' · '.$activity->duration_minutes.' دقيقة' : '' }}</span></div>
                    <div class="crm-field"><span class="k">الأولوية</span><span class="v">{{ SalesPlannedActivity::PRIORITIES[$activity->priority] ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">على</span><span class="v">
                        @if($activity->lead)عميل: <a class="crm-link" href="{{ route($crm->route('leads.show'), $activity->lead->id) }}">{{ $activity->lead->name }}</a> <span class="crm-muted" dir="ltr">{{ $activity->lead->phone }}</span>
                        @elseif($activity->student)طالب: <a class="crm-link" href="{{ route($crm->route('students.show'), $activity->student->id) }}">{{ $activity->student->name }}</a>
                        @else مهمة داخلية @endif
                    </span></div>
                    <div class="crm-field"><span class="k">المكلَّف</span><span class="v">{{ $activity->assignee?->name ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">أنشأه</span><span class="v">{{ $activity->creator?->name ?? '—' }} · {{ $activity->created_at?->format('d/m/Y H:i') }}</span></div>
                    @if($activity->completed_at)
                        <div class="crm-field"><span class="k">{{ $activity->status === 'cancelled' ? 'ألغاه' : 'سجّله' }}</span><span class="v">{{ $activity->completer?->name ?? '—' }} · {{ $activity->completed_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if($activity->outcome)
                        <div class="crm-field"><span class="k">نتيجة المكالمة</span><span class="v">{{ SalesActivity::outcomeLabel($activity->outcome) }}</span></div>
                    @endif
                    @if($activity->description)
                        <div class="crm-field" style="grid-column: 1 / -1"><span class="k">الوصف</span><span class="v" style="white-space: pre-line">{{ $activity->description }}</span></div>
                    @endif
                    @if($activity->result_notes)
                        <div class="crm-field" style="grid-column: 1 / -1"><span class="k">ملاحظات النتيجة</span><span class="v" style="white-space: pre-line">{{ $activity->result_notes }}</span></div>
                    @endif
                </div>
            </section>

            @if($crm->canComplete($activity))
                <section id="complete" class="crm-card" x-data="{ status: 'done' }" style="padding: 14px">
                    <h2 style="margin: 0 0 12px; font-size: 14px; font-weight: 700">تسجيل النتيجة</h2>
                    <form method="post" action="{{ route($crm->route('activities.complete'), $activity->id) }}" style="display: flex; flex-direction: column; gap: 12px">
                        @csrf
                        <div style="display: flex; flex-wrap: wrap; gap: 14px; font-size: 13px">
                            <label style="display: flex; gap: 6px; align-items: center"><input type="radio" name="status" value="done" x-model="status"> {{ $activity->kind === 'task' ? 'خلصت' : 'اتعملت' }}</label>
                            <label style="display: flex; gap: 6px; align-items: center"><input type="radio" name="status" value="missed" x-model="status"> ما اتعملتش</label>
                        </div>
                        @if($activity->kind === 'call')
                            <fieldset x-show="status === 'done'" style="border: 0; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 6px">
                                <legend class="crm-label" style="margin-bottom: 6px">نتيجة المكالمة</legend>
                                @foreach(SalesActivity::OUTCOMES as $k => $v)
                                    <label class="crm-chip" style="gap: 6px; cursor: pointer"><input type="radio" name="outcome" value="{{ $k }}" :required="status === 'done'"> {{ $v }}</label>
                                @endforeach
                            </fieldset>
                            @if($canStage)
                                <label x-show="status === 'done'" style="display: flex; gap: 6px; align-items: center; font-size: 13px"><input type="hidden" name="apply_stage" value="0"><input type="checkbox" name="apply_stage" value="1" checked> حرّك مرحلة العميل حسب النتيجة (زي التسجيل السريع)</label>
                            @endif
                        @endif
                        <label class="crm-label">ملاحظات<textarea class="crm-input" name="result_notes" rows="3" maxlength="2000" style="height: auto; padding: 8px"></textarea></label>
                        <div><button type="submit" class="crm-btn crm-btn-primary">حفظ النتيجة</button></div>
                        @if($activity->lead)<p class="crm-muted" style="margin: 0; font-size: 12px">لو اتعملت، بتتسجّل في سجل العميل وتتحسب في KPIs والتقرير اليومي.</p>@endif
                    </form>
                </section>
            @endif
        </div>

        <aside style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 16px">
            @if($history)
                <section class="crm-card" style="padding: 12px 14px; font-size: 13px">
                    <h2 style="margin: 0 0 6px; font-size: 13px; font-weight: 700">اتسجّل في سجل العميل</h2>
                    <div>{{ $history->title }}</div>
                    <div class="crm-muted">{{ $history->user?->name }} · {{ $history->created_at?->format('d/m H:i') }}</div>
                </section>
            @endif
            @if($activity->lead)
                <section class="crm-card">
                    <h2 style="margin: 0; padding: 10px 14px; font-size: 13px; font-weight: 700; border-bottom: 1px solid #E5E7EB">أنشطة تانية على العميل</h2>
                    <ul style="margin: 0; padding: 6px 14px 12px; list-style: none">
                        @forelse($others as $o)
                            <li style="padding: 8px 0; border-bottom: 1px solid #F0F1F3; font-size: 12px">
                                <a class="crm-link" href="{{ route($crm->route('activities.show'), $o->id) }}">{{ $o->title }}</a>
                                <div class="crm-muted">{{ SalesPlannedActivity::statusLabel($o->status) }} · {{ $o->starts_at->format('d/m H:i') }} · {{ $o->assignee?->name }}</div>
                            </li>
                        @empty
                            <li class="crm-muted" style="padding: 10px 0; font-size: 12px">مفيش.</li>
                        @endforelse
                    </ul>
                    @if($crm->can('activities.create'))<div class="crm-dashlet-foot"><a href="{{ route($crm->route('activities.create'), ['lead' => $activity->lead->id]) }}">＋ جدولة نشاط تاني</a></div>@endif
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
