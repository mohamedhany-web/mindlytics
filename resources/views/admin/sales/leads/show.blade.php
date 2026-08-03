@extends('layouts.admin')

@section('title', $lead->name)
@section('header', 'عميل محتمل: ' . $lead->name)

@section('content')
@php
    $pr = $lead->priority ?? 'normal';
    $priorityBadges = [
        'urgent' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'high' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'low' => 'bg-slate-100 text-slate-700 border border-slate-200',
        'normal' => 'bg-slate-100 text-slate-700 border border-slate-200',
    ];
    $priorityClass = $priorityBadges[$pr] ?? $priorityBadges['normal'];

    $infoCards = [
        ['label' => 'قيمة متوقعة', 'value' => $lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—', 'icon' => 'fas fa-coins', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'متابعة تالية', 'value' => $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-calendar-check', 'bg' => $lead->isFollowUpOverdue() ? 'bg-rose-100' : 'bg-sky-100', 'text' => $lead->isFollowUpOverdue() ? 'text-rose-600' : 'text-sky-600'],
        ['label' => 'آخر تواصل', 'value' => $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['label' => 'أنشطة', 'value' => number_format($lead->activities->count()), 'icon' => 'fas fa-list', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    @if(!empty(session('sales_duplicate_warnings')))
        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-950 px-4 py-3 text-sm space-y-1">
            @foreach(session('sales_duplicate_warnings') as $w)
                <p><i class="fas fa-exclamation-triangle ml-1 text-amber-600"></i>{{ $w }}</p>
            @endforeach
        </div>
    @endif

    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-black text-slate-900 truncate">{{ $lead->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            {{ \App\Models\SalesLead::stageLabel($lead->stage) }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $priorityClass }}">
                            {{ \App\Models\SalesLead::priorityLabel($pr) }}
                        </span>
                        <span class="text-xs text-slate-500">{{ \App\Models\SalesLead::sourceLabel($lead->source) }}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        مسند إلى: <strong>{{ $lead->assignee->name ?? '—' }}</strong>
                        @if($lead->creator)
                            · أنشأ: {{ $lead->creator->name }}
                        @endif
                        · {{ $lead->created_at->format('Y-m-d') }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    القائمة
                </a>
                <a href="{{ route('admin.sales.leads.edit', $lead) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-edit text-sky-600"></i>
                    تعديل / إعادة إسناد
                </a>
                <form action="{{ route('admin.sales.leads.destroy', $lead) }}" method="post" onsubmit="return confirm('حذف نهائياً؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-700 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100">
                        <i class="fas fa-trash-alt"></i>
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($infoCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-lg font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-xs"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        {{-- بيانات التواصل --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-address-card text-sky-600"></i>
                    بيانات العميل
                </h3>
            </div>
            <div class="p-4 sm:p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الهاتف</dt>
                        <dd class="font-semibold text-slate-900">{{ $lead->phone ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">البريد</dt>
                        <dd class="font-semibold text-slate-900 break-all">{{ $lead->email ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 sm:col-span-2">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الشركة</dt>
                        <dd class="font-semibold text-slate-900">{{ $lead->company ?? '—' }}</dd>
                    </div>
                </dl>

                @if($lead->interestType || $lead->interest)
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">اهتمام العميل</p>
                        @if($lead->interestType)
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold text-white mb-2" style="background:{{ $lead->interestType->color }}">
                                {{ $lead->interestType->name_ar }}
                            </span>
                        @endif
                        @if($lead->interest)
                            <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $lead->interest }}</p>
                        @endif
                    </div>
                @endif

                @include('sales._transfer_timeline', ['lead' => $lead, 'wrapperClass' => 'mt-4'])

                @if($lead->notes)
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $lead->notes }}</p>
                    </div>
                @endif

                @if($lead->stage === 'lost' && $lead->lost_reason)
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50/50 px-4 py-3">
                        <p class="text-xs font-semibold text-rose-700 mb-1">سبب الخسارة</p>
                        <p class="text-sm text-slate-800">{{ $lead->lost_reason }}</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- اعتماد الفوز --}}
        @if($lead->stage === \App\Models\SalesLead::WON_STAGE)
            <section class="rounded-2xl bg-white border border-emerald-200 shadow-lg overflow-hidden h-full">
                <div class="px-4 py-3 border-b border-emerald-200 bg-emerald-50/70 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-trophy text-emerald-600"></i>
                        اعتماد الفوز وصرف الكوميشن
                    </h3>
                    @if($lead->won_confirmed_at)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                            معتمد {{ $lead->won_confirmed_at->format('Y-m-d H:i') }}
                        </span>
                    @else
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 border border-amber-200">في انتظار الموافقة</span>
                        <a href="{{ route('admin.sales.win-approvals.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline underline-offset-2">
                            صفحة موافقة Win
                        </a>
                    @endif
                </div>
                <div class="p-4 sm:p-5">
                    @if($lead->won_confirmed_at)
                        <dl class="space-y-3 text-sm">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50/40 px-3 py-2.5 flex justify-between gap-3">
                                <dt class="text-slate-600">الكوميشن</dt>
                                <dd class="font-bold text-emerald-800 tabular-nums">{{ number_format((float) ($lead->commission_amount ?? 0), 2) }} ج.م</dd>
                            </div>
                            @if($lead->commission_transaction_id)
                                <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                                    <dt class="text-slate-600">رقم القيد</dt>
                                    <dd class="font-semibold text-slate-900">{{ $lead->commission_transaction_id }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if($lead->commission_notes)
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات الكوميشن</p>
                                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $lead->commission_notes }}</p>
                            </div>
                        @endif
                    @else
                        @php
                            $rep = $lead->assignee;
                            $base = (float) ($lead->expected_value ?? 0);
                            $defaultCommission = $rep ? $rep->calculateSalesCommissionAmount($base) : 0;
                        @endphp
                        <form method="post" action="{{ route('admin.sales.leads.confirm-win', $lead) }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة المرجعية</label>
                                    <input type="text" value="{{ number_format($base, 2) }} ج.م" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                    @if($rep)
                                        <p class="text-[11px] text-slate-500 mt-1">إعداد الموظف: <strong>{{ $rep->salesCommissionLabel() }}</strong></p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مبلغ الكوميشن</label>
                                    <input type="number" step="0.01" min="0" name="commission_amount" value="{{ old('commission_amount', $defaultCommission) }}"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-[11px] text-slate-500 mt-1">اتركه للحساب الافتراضي.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات</label>
                                    <input type="text" name="commission_notes" value="{{ old('commission_notes') }}"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                           placeholder="اختياري">
                                </div>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                                    <i class="fas fa-check"></i>
                                    اعتماد وصرف الكوميشن
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>
        @else
            {{-- معلومات إضافية عندما لا يكون Won --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-violet-600"></i>
                        حالة المتابعة
                    </h3>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    @if($lead->isOpen() && $lead->isFollowUpOverdue())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            <strong>متابعة متأخرة</strong> — الموعد كان {{ $lead->next_follow_up_at?->format('Y-m-d H:i') }}.
                        </div>
                    @elseif($lead->isOpen() && $lead->isStaleContact())
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            <i class="fas fa-hourglass-half ml-1"></i>
                            <strong>بلا تواصل منذ فترة</strong> — آخر تواصل: {{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? 'لم يُسجَّل' }}.
                        </div>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <i class="fas fa-check-circle text-emerald-600 ml-1"></i>
                            لا توجد تنبيهات عاجلة على هذا Lead حالياً.
                        </div>
                    @endif
                    <dl class="grid grid-cols-1 gap-3 text-sm">
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                            <dt class="text-slate-600">آخر تحديث</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ $lead->updated_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        @if($lead->closed_at)
                            <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                                <dt class="text-slate-600">تاريخ الإغلاق</dt>
                                <dd class="font-semibold text-slate-900 tabular-nums">{{ $lead->closed_at->format('Y-m-d H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </section>
        @endif
    </div>

    {{-- سجل النشاط --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">سجل النشاط</h3>
                <p class="text-xs text-slate-600">يُسجَّل في سجل مراقبة المبيعات.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">{{ $lead->activities->count() }} نشاط</span>
        </div>

        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/30">
            <form method="post" action="{{ route('admin.sales.leads.activities.store', $lead) }}" class="space-y-3" x-data="{ actType: 'call' }">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">النوع</label>
                        <select name="type" x-model="actType" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @foreach(\App\Models\SalesActivity::TYPES as $k => $label)
                                @if($k !== 'stage_change')
                                    <option value="{{ $k }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2" x-show="actType === 'call'" x-cloak>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">نتيجة المكالمة <span class="text-rose-500">*</span></label>
                        <select name="outcome" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm font-semibold" :required="actType === 'call'">
                            <option value="">— اختر —</option>
                            @foreach(\App\Models\SalesActivity::OUTCOMES as $k => $label)
                                <option value="{{ $k }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2" x-show="actType !== 'call'">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان</label>
                        <input type="text" name="title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">التفاصيل</label>
                    <textarea name="body" rows="3" placeholder="اكتب تفاصيل النشاط..."
                              class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-plus"></i>
                        إضافة نشاط
                    </button>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-5">
            @forelse($lead->activities as $act)
                <div class="relative pr-4 pb-5 mb-5 last:mb-0 last:pb-0 border-r-2 border-emerald-200">
                    <div class="flex flex-wrap justify-between gap-2 text-xs text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                            <i class="fas fa-circle text-[6px]"></i>
                            {{ \App\Models\SalesActivity::typeLabel($act->type) }}
                            @if($act->type === 'call' && $act->outcome)
                                — {{ \App\Models\SalesActivity::outcomeLabel($act->outcome) }}
                            @endif
                        </span>
                        <span class="tabular-nums">{{ $act->created_at->format('Y-m-d H:i') }} — {{ $act->user?->name ?? '—' }}</span>
                    </div>
                    @if($act->title)
                        <p class="font-semibold text-slate-900 text-sm">{{ $act->title }}</p>
                    @endif
                    @if($act->body)
                        <p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap">{{ $act->body }}</p>
                    @endif
                </div>
            @empty
                <div class="py-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900">لا أنشطة بعد</p>
                    <p class="text-xs text-slate-500 mt-1">أضف أول نشاط من النموذج أعلاه.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
