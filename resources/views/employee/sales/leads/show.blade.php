@extends('layouts.employee')

@section('title', $lead->name)
@section('header', $lead->name)

@php
    $activityStyles = [
        'note' => ['icon' => 'fas fa-note-sticky', 'bubble' => 'bg-amber-100 text-amber-800', 'accent' => 'border-amber-200'],
        'call' => ['icon' => 'fas fa-phone', 'bubble' => 'bg-sky-100 text-sky-800', 'accent' => 'border-sky-200'],
        'meeting' => ['icon' => 'fas fa-users', 'bubble' => 'bg-violet-100 text-violet-800', 'accent' => 'border-violet-200'],
        'follow_up' => ['icon' => 'fas fa-redo', 'bubble' => 'bg-teal-100 text-teal-800', 'accent' => 'border-teal-200'],
        'whatsapp' => ['icon' => 'fab fa-whatsapp', 'bubble' => 'bg-green-100 text-green-800', 'accent' => 'border-green-200'],
        'email' => ['icon' => 'fas fa-envelope', 'bubble' => 'bg-slate-100 text-slate-800', 'accent' => 'border-slate-200'],
        'stage_change' => ['icon' => 'fas fa-shuffle', 'bubble' => 'bg-indigo-100 text-indigo-800', 'accent' => 'border-indigo-200'],
        'other' => ['icon' => 'fas fa-ellipsis', 'bubble' => 'bg-gray-100 text-gray-800', 'accent' => 'border-gray-200'],
    ];
    $acts = $lead->activities;
    $actCount = $acts->count();
    $pr = $lead->priority ?? 'normal';
    $prClass = match ($pr) {
        'urgent' => 'bg-rose-100 text-rose-800',
        'high' => 'bg-orange-100 text-orange-800',
        'low' => 'bg-slate-100 text-slate-700',
        default => 'bg-slate-100 text-slate-700',
    };
    $missingFollow = $lead->isOpen() && (! $lead->next_follow_up_at || $lead->next_follow_up_at->isPast());
    $missingAction = $lead->isOpen() && blank($lead->follow_up_channel);
    $needsMovement = $lead->isOpen() && app(\App\Services\SalesLeadMovementPolicy::class)->requiresActiveMovement($lead->stage);
@endphp

@push('styles')
@include('employee.sales._styles')
<style>
    .sales-hub .dashboard-card,
    .sales-hub .panel-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sales-hub .dashboard-card::before { display: none; }
    .sales-hub .dashboard-card:hover { transform: none; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06); }
    .sales-hub .panel-card-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-5 sales-hub pb-8">
    <div class="dashboard-card flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-teal-700 font-semibold hover:underline inline-flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-right text-xs"></i> العملاء المحتملون
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-2xl font-bold text-slate-900">{{ $lead->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-bold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $prClass }}">{{ \App\Models\SalesLead::priorityLabel($pr) }}</span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $lead->phone ?: 'بدون هاتف' }}
                @if($lead->email) · {{ $lead->email }} @endif
                · {{ \App\Models\SalesLead::sourceLabel($lead->source) }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            @if(!empty($whatsappInboxUrl))
                <a href="{{ $whatsappInboxUrl }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                    <i class="fab fa-whatsapp"></i>
                    {{ $whatsappConversation ? 'واتساب' : 'بدء واتساب' }}
                </a>
            @endif
            <a href="{{ route('employee.sales.leads.edit', $lead) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">تعديل البيانات</a>
            <form action="{{ route('employee.sales.leads.destroy', $lead) }}" method="post" onsubmit="return confirm('حذف هذا السجل؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg border border-rose-200 bg-white text-rose-700 text-sm font-semibold hover:bg-rose-50">حذف</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(!empty(session('sales_duplicate_warnings')))
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 space-y-1">
            @foreach(session('sales_duplicate_warnings') as $w)
                <p><i class="fas fa-exclamation-triangle ml-1"></i>{{ $w }}</p>
            @endforeach
        </div>
    @endif

    @if($needsMovement && ($missingFollow || $missingAction || $lead->isStaleContact()))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            @if($missingFollow || $missingAction)
                <p class="font-bold">العميل يحتاج حركة — سجّل اللي حصل (حجز، دفع، متابعة، أو خسارة) من الصندوق أدناه.</p>
            @endif
            @if($lead->isFollowUpOverdue())
                <p>موعد المتابعة متأخر.</p>
            @endif
            @if($lead->isStaleContact())
                <p>لم يُسجَّل تواصل خلال {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }} أيام.</p>
            @endif
        </div>
    @endif

    @include('employee.sales._pipeline_journey', ['lead' => $lead])

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <div class="xl:col-span-8 space-y-5">
            <section class="panel-card overflow-hidden">
                <div class="panel-card-head px-4 sm:px-5 py-3 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-plus ml-1 text-teal-600"></i> تسجيل نشاط</h3>
                    <span class="text-xs font-semibold text-slate-500">{{ $actCount }} في السجل</span>
                </div>
                <form method="post" action="{{ route('employee.sales.leads.activities.store', $lead) }}" class="p-4 sm:p-5 space-y-3" x-data="{ actType: '{{ old('type', 'call') }}' }">
                    @csrf
                    @if($errors->any())
                        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm px-3 py-2">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 mb-1">النوع</label>
                            <select name="type" x-model="actType" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                                @foreach(\App\Models\SalesActivity::TYPES as $k => $label)
                                    @if($k !== 'stage_change')
                                        <option value="{{ $k }}" @selected(old('type', 'call') === $k)>{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-8" x-show="actType === 'call'" x-cloak>
                            <label class="block text-xs font-bold text-slate-600 mb-1">نتيجة المكالمة *</label>
                            <select name="outcome" class="w-full rounded-lg border border-amber-200 bg-amber-50/40 px-3 py-2 text-sm" :required="actType === 'call'">
                                <option value="">— اختر —</option>
                                @foreach(\App\Models\SalesActivity::OUTCOMES as $k => $label)
                                    <option value="{{ $k }}" @selected(old('outcome') === $k)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-600">
                                <input type="checkbox" name="apply_stage" value="1" checked class="rounded border-slate-300 text-teal-600">
                                حدّث المرحلة تلقائياً حسب النتيجة
                            </label>
                        </div>
                        <div class="sm:col-span-8" x-show="actType !== 'call'">
                            <label class="block text-xs font-bold text-slate-600 mb-1">عنوان مختصر</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">التفاصيل</label>
                        <textarea name="body" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('body') }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">
                        حفظ النشاط
                    </button>
                </form>
            </section>

            <section class="panel-card overflow-hidden">
                <div class="panel-card-head px-4 sm:px-5 py-3">
                    <h3 class="font-bold text-slate-800">الخط الزمني</h3>
                </div>
                <div class="p-4 sm:p-5">
                    @forelse($acts as $index => $act)
                        @php
                            $s = $activityStyles[$act->type] ?? $activityStyles['other'];
                            $isLast = $index === $acts->count() - 1;
                        @endphp
                        <div class="flex gap-3 {{ $isLast ? '' : 'pb-1' }}">
                            <div class="flex flex-col items-center shrink-0 w-10">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $s['bubble'] }}">
                                    <i class="{{ $s['icon'] }} text-sm"></i>
                                </span>
                                @unless($isLast)
                                    <span class="w-px flex-1 min-h-[0.75rem] mt-1 bg-slate-200"></span>
                                @endunless
                            </div>
                            <article class="flex-1 min-w-0 rounded-xl border {{ $s['accent'] }} bg-white px-3 py-3 mb-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-sm font-bold text-slate-900">
                                        {{ \App\Models\SalesActivity::typeLabel($act->type) }}
                                        @if($act->type === 'call' && $act->outcome)
                                            <span class="text-[11px] font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-full mr-1">{{ \App\Models\SalesActivity::outcomeLabel($act->outcome) }}</span>
                                        @endif
                                    </span>
                                    <time class="text-xs text-slate-500">{{ $act->created_at->format('Y-m-d H:i') }} · {{ $act->user?->name ?? '—' }}</time>
                                </div>
                                @if($act->title)
                                    <p class="font-semibold text-slate-800 mt-1 text-sm">{{ $act->title }}</p>
                                @endif
                                @if($act->body)
                                    <div class="mt-1 text-sm text-slate-600 whitespace-pre-wrap">{{ $act->body }}</div>
                                @endif
                            </article>
                        </div>
                    @empty
                        <p class="text-center text-slate-500 text-sm py-8">لا توجد أنشطة بعد.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="xl:col-span-4 space-y-4 xl:sticky xl:top-20">
            <div class="panel-card p-4 sm:p-5 space-y-3">
                <h3 class="font-bold text-slate-900">بيانات العميل</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">الهاتف</dt><dd class="font-semibold text-slate-900 break-all">{{ $lead->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">البريد</dt><dd class="font-semibold text-slate-900 break-all">{{ $lead->email ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">الشركة</dt><dd class="font-semibold text-slate-900">{{ $lead->company ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">الكورس</dt><dd class="font-semibold text-slate-900 text-left">{{ $lead->linkedCourseTitle() ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">قيمة متوقعة</dt><dd class="font-semibold text-slate-900">{{ $lead->expected_value !== null ? number_format($lead->expected_value, 2).' ج.م' : '—' }}</dd></div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500">متابعة</dt>
                        <dd class="font-semibold {{ $lead->isFollowUpOverdue() ? 'text-rose-600' : 'text-slate-900' }}">{{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500">الإجراء التالي</dt>
                        <dd class="font-semibold text-slate-900">{{ $lead->follow_up_channel ? (\App\Models\SalesLead::FOLLOW_UP_CHANNELS[$lead->follow_up_channel] ?? $lead->follow_up_channel) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500">آخر تواصل</dt>
                        <dd class="font-semibold text-slate-900">{{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                    @if($lead->category)
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-500">التصنيف</dt>
                            <dd class="font-semibold" style="color: {{ $lead->category->color }}">{{ $lead->category->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($lead->isOpen())
                <div class="panel-card p-4 space-y-3">
                    <p class="text-sm font-bold text-slate-800">تغيير موعد المتابعة فقط</p>
                    <form method="post" action="{{ route('employee.sales.leads.next-follow', $lead) }}" class="space-y-2">
                        @csrf
                        <input type="datetime-local" name="next_follow_up_at" required
                               min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}"
                               value="{{ old('next_follow_up_at', $lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <select name="follow_up_channel" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $label)
                                <option value="{{ $k }}" @selected(old('follow_up_channel', $lead->follow_up_channel ?? 'call') === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="note" value="{{ old('note') }}" maxlength="500" placeholder="ملاحظة اختيارية" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <button type="submit" class="w-full py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-sm font-bold">حفظ الموعد</button>
                    </form>
                </div>
            @endif

            @if($lead->stage === \App\Models\SalesLead::WON_STAGE)
                @if($lead->isWinConfirmed())
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm">
                        <p class="font-bold text-emerald-900">تم اعتماد الفوز والكوميشن</p>
                        <p class="text-emerald-800 mt-1 tabular-nums">{{ number_format((float) ($lead->commission_amount ?? 0), 2) }} ج.م</p>
                    </div>
                @elseif($lead->isPendingWinApproval())
                    <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">
                        <p class="font-bold text-amber-900">في انتظار موافقة الإدارة</p>
                    </div>
                @endif
                <div class="panel-card p-4 space-y-2">
                    <p class="text-sm font-bold text-slate-800">تقييم رضا العميل</p>
                    @if($lead->csat_rating)
                        <p class="text-sm text-slate-700">{{ $lead->csat_rating }}/5</p>
                    @endif
                    <form method="post" action="{{ route('employee.sales.leads.csat.store', $lead) }}" class="space-y-2">
                        @csrf
                        <select name="csat_rating" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected((int) old('csat_rating', $lead->csat_rating ?? 5) === $i)>{{ $i }}</option>
                            @endfor
                        </select>
                        <textarea name="csat_comment" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('csat_comment', $lead->csat_comment) }}</textarea>
                        <button type="submit" class="w-full py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold">حفظ التقييم</button>
                    </form>
                </div>
            @endif

            @if($lead->interestType || $lead->interest)
                <div class="panel-card p-4">
                    <p class="text-xs font-bold text-slate-500 mb-1">اهتمام العميل</p>
                    @if($lead->interestType)
                        <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-[11px] font-bold text-white mb-1" style="background:{{ $lead->interestType->color }}">{{ $lead->interestType->name_ar }}</span>
                    @endif
                    @if($lead->interest)
                        <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $lead->interest }}</p>
                    @endif
                </div>
            @endif

            @include('sales._transfer_timeline', ['lead' => $lead])

            @if($lead->notes)
                <div class="panel-card p-4">
                    <p class="text-xs font-bold text-slate-500 mb-1">ملاحظات</p>
                    <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $lead->notes }}</p>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
