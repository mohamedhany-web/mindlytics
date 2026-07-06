@extends('layouts.employee')

@section('title', $lead->name)
@section('header', $lead->name)

@php
    $activityStyles = [
        'note' => ['icon' => 'fas fa-note-sticky', 'bubble' => 'bg-amber-100 text-amber-800', 'accent' => 'border-amber-300'],
        'call' => ['icon' => 'fas fa-phone', 'bubble' => 'bg-sky-100 text-sky-800', 'accent' => 'border-sky-300'],
        'meeting' => ['icon' => 'fas fa-users', 'bubble' => 'bg-violet-100 text-violet-800', 'accent' => 'border-violet-300'],
        'follow_up' => ['icon' => 'fas fa-redo', 'bubble' => 'bg-teal-100 text-teal-800', 'accent' => 'border-teal-300'],
        'whatsapp' => ['icon' => 'fab fa-whatsapp', 'bubble' => 'bg-green-100 text-green-800', 'accent' => 'border-green-400'],
        'email' => ['icon' => 'fas fa-envelope', 'bubble' => 'bg-slate-100 text-slate-800', 'accent' => 'border-slate-300'],
        'stage_change' => ['icon' => 'fas fa-shuffle', 'bubble' => 'bg-indigo-100 text-indigo-800', 'accent' => 'border-indigo-300'],
        'other' => ['icon' => 'fas fa-ellipsis', 'bubble' => 'bg-gray-100 text-gray-800', 'accent' => 'border-gray-300'],
    ];
    $acts = $lead->activities;
    $actCount = $acts->count();
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-10">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex gap-2">
            @if(!empty($whatsappInboxUrl))
                <a href="{{ $whatsappInboxUrl }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                    <i class="fab fa-whatsapp ml-1"></i>
                    {{ $whatsappConversation ? 'فتح المحادثة' : 'بدء واتساب' }}
                </a>
            @endif
            <a href="{{ route('employee.sales.leads.edit', $lead) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">تعديل بيانات العميل</a>
            <form action="{{ route('employee.sales.leads.destroy', $lead) }}" method="post" onsubmit="return confirm('حذف هذا السجل؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-medium">حذف</button>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        {{-- العمود الرئيسي: النشاطات --}}
        <div class="lg:col-span-8 space-y-6 order-1">
            <section class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-br from-emerald-50 via-white to-teal-50/30 shadow-md overflow-hidden" aria-labelledby="activities-heading">
                <div class="px-5 sm:px-8 py-6 border-b border-emerald-100/80 bg-white/60 backdrop-blur-sm">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 id="activities-heading" class="text-2xl font-bold text-gray-900 tracking-tight">سجل النشاطات</h2>
                            <p class="text-sm text-gray-600 mt-1 max-w-xl">كل تواصل أو ملاحظة تُسجَّل هنا يبني تاريخاً واضحاً للعميل. استخدم النموذج أسفل العنوان لإضافة نشاط بسرعة.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-sm">
                                <i class="fas fa-list-ul"></i>
                                {{ $actCount }} {{ $actCount === 1 ? 'نشاط' : 'أنشطة' }}
                            </span>
                            @if($acts->isNotEmpty())
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-900 text-xs font-semibold">
                                    آخر نشاط: {{ $acts->first()->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-8 space-y-6">
                    <form method="post" action="{{ route('employee.sales.leads.activities.store', $lead) }}" class="rounded-xl border border-emerald-200 bg-white p-5 sm:p-6 shadow-sm space-y-4">
                        @csrf
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white text-sm"><i class="fas fa-plus"></i></span>
                            تسجيل نشاط جديد
                        </h3>
                        @if($errors->any())
                            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm px-3 py-2">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">نوع النشاط</label>
                                <select name="type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                    @foreach(\App\Models\SalesActivity::TYPES as $k => $label)
                                        @if($k !== 'stage_change')
                                            <option value="{{ $k }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-8">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان مختصر <span class="text-gray-400 font-normal">(اختياري)</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: متابعة عرض السعر" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">التفاصيل</label>
                            <textarea name="body" rows="5" placeholder="اكتب ما دار في المكالمة، أو موعد الاجتماع القادم، أو أي ملاحظة مهمة…" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('body') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1.5">يُنصح بتوثيق النتيجة وخطوة المتابعة التالية في كل نشاط.</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md transition-colors">
                            <i class="fas fa-paper-plane"></i>
                            حفظ النشاط في السجل
                        </button>
                    </form>

                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">الخط الزمني</h3>
                        @forelse($acts as $index => $act)
                            @php
                                $s = $activityStyles[$act->type] ?? $activityStyles['other'];
                                $isLast = $index === $acts->count() - 1;
                            @endphp
                            <div class="flex gap-4 sm:gap-5 {{ $isLast ? '' : 'pb-2' }}">
                                <div class="flex flex-col items-center shrink-0 w-12 sm:w-14">
                                    <span class="flex h-11 w-11 sm:h-12 sm:w-12 items-center justify-center rounded-2xl shadow-sm border-2 border-white {{ $s['bubble'] }}" title="{{ \App\Models\SalesActivity::typeLabel($act->type) }}">
                                        <i class="{{ $s['icon'] }} text-base sm:text-lg"></i>
                                    </span>
                                    @unless($isLast)
                                        <span class="w-0.5 flex-1 min-h-[1.25rem] mt-2 rounded-full bg-gradient-to-b from-emerald-200 to-emerald-100" aria-hidden="true"></span>
                                    @endunless
                                </div>
                                <article class="flex-1 min-w-0 rounded-2xl border-2 {{ $s['accent'] }} bg-white px-4 py-4 sm:px-5 sm:py-5 shadow-sm mb-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 gap-y-1">
                                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-900">
                                            {{ \App\Models\SalesActivity::typeLabel($act->type) }}
                                        </span>
                                        <time class="text-xs text-gray-500 font-medium tabular-nums" datetime="{{ $act->created_at->toIso8601String() }}">
                                            {{ $act->created_at->format('Y-m-d H:i') }}
                                            <span class="text-gray-400">·</span>
                                            {{ $act->user?->name ?? '—' }}
                                        </time>
                                    </div>
                                    @if($act->title)
                                        <p class="font-semibold text-gray-900 mt-2 text-base leading-snug">{{ $act->title }}</p>
                                    @endif
                                    @if($act->body)
                                        <div class="mt-2 text-sm sm:text-[15px] text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $act->body }}</div>
                                    @endif
                                </article>
                            </div>
                        @empty
                            <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/40 px-6 py-14 text-center">
                                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-2xl mb-4">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <p class="text-gray-800 font-semibold text-lg">لا توجد أنشطة بعد</p>
                                <p class="text-gray-600 text-sm mt-2 max-w-md mx-auto">ابدأ بأول مكالمة أو ملاحظة باستخدام النموذج أعلاه — سيظهر كل شيء هنا بترتيب زمني.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        {{-- الشريط الجانبي: ملخص العميل --}}
        <aside class="lg:col-span-4 space-y-4 order-2 lg:sticky lg:top-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                    @php
                        $pr = $lead->priority ?? 'normal';
                        $prClass = match ($pr) {
                            'urgent' => 'bg-rose-100 text-rose-800',
                            'high' => 'bg-orange-100 text-orange-800',
                            'low' => 'bg-slate-100 text-slate-700',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $prClass }}">{{ \App\Models\SalesLead::priorityLabel($pr) }}</span>
                    <span class="text-sm text-gray-500">{{ \App\Models\SalesLead::sourceLabel($lead->source) }}</span>
                    @if($lead->category)
                        <span class="px-3 py-1 rounded-full text-xs font-bold" style="color: {{ $lead->category->color }}; background: {{ $lead->category->color }}18">{{ $lead->category->name }}</span>
                    @endif
                    @if($lead->import_batch)
                        <span class="text-xs text-gray-400">دفعة: {{ $lead->import_batch }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $lead->name }}</h2>
                <dl class="space-y-3 text-sm border-t border-gray-100 pt-4">
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">الهاتف</dt><dd class="font-medium text-gray-900 text-left sm:text-right break-all">{{ $lead->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">البريد</dt><dd class="font-medium text-gray-900 text-left sm:text-right break-all">{{ $lead->email ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">الشركة</dt><dd class="font-medium text-gray-900">{{ $lead->company ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">قيمة متوقعة</dt><dd class="font-medium text-gray-900">{{ $lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—' }}</dd></div>
                    <div>
                        <dt class="text-gray-500 mb-1">متابعة تالية</dt>
                        <dd class="font-semibold @if($lead->isFollowUpOverdue()) text-rose-600 @else text-gray-900 @endif">{{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">آخر تواصل مسجّل</dt>
                        <dd class="font-medium text-gray-900">{{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                </dl>
                @if($lead->stage === 'won')
                    @if($lead->isWinConfirmed())
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm">
                            <p class="font-bold text-emerald-900 flex items-center gap-2"><i class="fas fa-check-circle"></i> تم اعتماد الفوز والكوميشن</p>
                            <p class="text-emerald-800 mt-1 tabular-nums">المبلغ: <strong>{{ number_format((float) ($lead->commission_amount ?? 0), 2) }} ج.م</strong></p>
                            <p class="text-xs text-emerald-700 mt-0.5">{{ $lead->won_confirmed_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    @elseif($lead->isPendingWinApproval())
                        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">
                            <p class="font-bold text-amber-900 flex items-center gap-2"><i class="fas fa-hourglass-half"></i> في انتظار موافقة الإدارة</p>
                            <p class="text-amber-800 mt-1">تم تسجيل الفوز — سيتم احتساب الكوميشن بعد اعتماد الإدارة.</p>
                            @if($lead->assignee)
                                @php $est = $lead->assignee->calculateSalesCommissionAmount((float) ($lead->expected_value ?? 0)); @endphp
                                <p class="text-xs text-amber-700 mt-1">كوميشن مقدّر: <strong>{{ number_format($est, 2) }} ج.م</strong></p>
                            @endif
                        </div>
                    @endif
                    <div class="rounded-xl border border-amber-200 bg-amber-50/90 p-4 space-y-3">
                        <p class="text-sm font-bold text-amber-900 flex items-center gap-2"><i class="fas fa-star"></i> تقييم رضا العميل (CSAT)</p>
                        @if($lead->csat_rating)
                            <p class="text-sm text-gray-800">التقييم الحالي: <strong>{{ $lead->csat_rating }}/5</strong>@if($lead->csat_recorded_at) — {{ $lead->csat_recorded_at->format('Y-m-d') }}@endif</p>
                            @if($lead->csat_comment)<p class="text-xs text-gray-600 whitespace-pre-wrap">{{ $lead->csat_comment }}</p>@endif
                        @endif
                        <form method="post" action="{{ route('employee.sales.leads.csat.store', $lead) }}" class="space-y-2">
                            @csrf
                            @error('csat_rating')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">التقييم (1–5)</label>
                                <select name="csat_rating" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" @selected((int) old('csat_rating', $lead->csat_rating ?? 5) === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">ملاحظة</label>
                                <textarea name="csat_comment" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('csat_comment', $lead->csat_comment) }}</textarea>
                            </div>
                            <button type="submit" class="w-full py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold">حفظ التقييم</button>
                        </form>
                    </div>
                @endif
                @if($lead->isOpen() && ($lead->isFollowUpOverdue() || $lead->isStaleContact()))
                    <div class="rounded-xl border border-rose-200 bg-rose-50/80 p-3 text-sm text-rose-900">
                        @if($lead->isFollowUpOverdue())
                            <p class="font-semibold"><i class="fas fa-clock ml-1"></i> موعد المتابعة متأخر — راجع الخطوة التالية.</p>
                        @endif
                        @if($lead->isStaleContact())
                            <p class="font-semibold mt-1"><i class="fas fa-hourglass-end ml-1"></i> لم يُسجَّل تواصل خلال {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }} أيام — سجّل نشاطاً.</p>
                        @endif
                    </div>
                @endif
                @if($lead->interest)
                    <div class="rounded-xl bg-amber-50/80 border border-amber-100 p-3">
                        <p class="text-xs font-semibold text-amber-900 mb-1">الاهتمام</p>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $lead->interest }}</p>
                    </div>
                @endif
                @if($lead->notes)
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-xs font-semibold text-gray-600 mb-1">ملاحظات</p>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $lead->notes }}</p>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
