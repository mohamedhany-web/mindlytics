@extends('layouts.employee')

@section('title', $lead->name)
@section('header', 'تفاصيل العميل')

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
    $pr = $lead->priority ?? 'normal';
    $prClass = match ($pr) {
        'urgent' => 'bg-rose-100 text-rose-800 border-rose-200',
        'high' => 'bg-orange-100 text-orange-800 border-orange-200',
        'low' => 'bg-slate-100 text-slate-700 border-slate-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200',
    };
    $infoCards = [
        ['label' => 'قيمة متوقعة', 'value' => $lead->expected_value !== null ? number_format($lead->expected_value, 2).' ج.م' : '—', 'icon' => 'fas fa-coins', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'متابعة تالية', 'value' => $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-calendar-check', 'bg' => $lead->isFollowUpOverdue() ? 'bg-rose-100' : 'bg-sky-100', 'text' => $lead->isFollowUpOverdue() ? 'text-rose-600' : 'text-sky-600'],
        ['label' => 'آخر تواصل', 'value' => $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['label' => 'الأنشطة', 'value' => number_format($actCount), 'icon' => 'fas fa-list', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-5 pb-10">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex items-start gap-2">
            <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 bg-gradient-to-l from-slate-50 via-white to-indigo-50/40 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-slate-800 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 truncate">{{ $lead->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            {{ \App\Models\SalesLead::stageLabel($lead->stage) }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold border {{ $prClass }}">
                            {{ \App\Models\SalesLead::priorityLabel($pr) }}
                        </span>
                        <span class="text-xs text-slate-500">{{ \App\Models\SalesLead::sourceLabel($lead->source) }}</span>
                        @if($lead->category)
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold border" style="color: {{ $lead->category->color }}; background: {{ $lead->category->color }}18; border-color: {{ $lead->category->color }}44">{{ $lead->category->name }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        مسند إلى: <strong>{{ $lead->assignee->name ?? '—' }}</strong>
                        · فريق <strong>{{ $team->name }}</strong>
                        @if($lead->creator)
                            · أنشأ: {{ $lead->creator->name }}
                        @endif
                        · {{ $lead->created_at->format('Y-m-d') }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('employee.sales-manager.leads.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> القائمة
                </a>
                <a href="{{ route('employee.sales-manager.follow-ups.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-lg border border-slate-300 bg-white hover:bg-slate-50">
                    <i class="fas fa-clipboard-list"></i> المتابعات
                </a>
                @if($whatsappInboxUrl)
                    <a href="{{ $whatsappInboxUrl }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-white rounded-lg bg-emerald-600 hover:bg-emerald-700">
                        <i class="fab fa-whatsapp"></i>
                        {{ $whatsappConversation ? 'محادثة واتساب' : 'واتساب' }}
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($infoCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">{{ $card['label'] }}</p>
                            <p class="text-base sm:text-lg font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-xs"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Pipeline --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-project-diagram text-indigo-600"></i>
                مسار المبيعات (Pipeline)
            </h2>
            <span class="text-[11px] font-semibold text-slate-500">المرحلة الحالية: {{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
        </div>
        <div class="p-4 sm:p-5 overflow-x-auto">
            <ol class="flex items-center min-w-[640px] gap-0">
                @foreach($pipelineStages as $i => $stageKey)
                    @php
                        $done = $i < $currentStageIndex;
                        $current = $i === $currentStageIndex;
                        $lost = $lead->stage === 'lost';
                        $won = $lead->stage === 'won';
                        if ($lost && $stageKey !== 'lost') {
                            $done = false;
                            $current = false;
                        }
                        if ($won && $i < $currentStageIndex) {
                            $done = true;
                        }
                    @endphp
                    <li class="flex-1 flex items-center {{ $i === count($pipelineStages) - 1 ? '' : '' }}">
                        <div class="flex flex-col items-center w-full relative">
                            @if($i > 0)
                                <span class="absolute end-1/2 top-4 h-0.5 w-full -translate-y-1/2 {{ ($done || $current) && !($lost && $stageKey !== 'lost' && $i <= $currentStageIndex) ? ($lost ? 'bg-rose-300' : 'bg-indigo-400') : 'bg-slate-200' }}"
                                      style="right: 50%; width: 100%; z-index: 0;"></span>
                            @endif
                            <span @class([
                                'relative z-10 flex h-8 w-8 items-center justify-center rounded-full text-[11px] font-black border-2',
                                'bg-indigo-600 border-indigo-600 text-white' => $current && ! $lost,
                                'bg-rose-600 border-rose-600 text-white' => $current && $lost,
                                'bg-emerald-500 border-emerald-500 text-white' => $done && ! $lost,
                                'bg-white border-slate-300 text-slate-400' => ! $done && ! $current,
                            ])>
                                @if($done && ! $current)
                                    <i class="fas fa-check text-[10px]"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                            <span @class([
                                'mt-2 text-[11px] font-bold text-center leading-tight px-1',
                                'text-indigo-700' => $current && ! $lost,
                                'text-rose-700' => $current && $lost,
                                'text-emerald-700' => $done && ! $current,
                                'text-slate-400' => ! $done && ! $current,
                            ])>
                                {{ \App\Models\SalesLead::stageLabel($stageKey) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Alerts --}}
    @if($lead->isOpen() && ($lead->isFollowUpOverdue() || $lead->isStaleContact()))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 space-y-1">
            @if($lead->isFollowUpOverdue())
                <p class="font-semibold"><i class="fas fa-clock ml-1"></i> متابعة متأخرة — الموعد كان {{ $lead->next_follow_up_at?->format('Y-m-d H:i') }}</p>
            @endif
            @if($lead->isStaleContact())
                <p class="font-semibold"><i class="fas fa-hourglass-end ml-1"></i> بلا تواصل منذ {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }} أيام — آخر تواصل: {{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? 'لم يُسجَّل' }}</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- Main: timeline --}}
        <div class="lg:col-span-8 space-y-5 order-2 lg:order-1">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-5 py-4 border-b border-slate-100 bg-gradient-to-l from-white to-teal-50/40 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">سجل الأحداث والنشاطات</h2>
                        <p class="text-xs text-slate-500 mt-1">كل ما تم مع العميل بترتيب زمني — للمراجعة والرقابة</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 text-white text-xs font-bold">
                            <i class="fas fa-list-ul"></i> {{ $actCount }} {{ $actCount === 1 ? 'نشاط' : 'أنشطة' }}
                        </span>
                        @if($acts->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-teal-200 text-teal-900 text-[11px] font-semibold">
                                آخر نشاط: {{ $acts->first()->created_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    @forelse($acts as $index => $act)
                        @php
                            $s = $activityStyles[$act->type] ?? $activityStyles['other'];
                            $isLast = $index === $acts->count() - 1;
                        @endphp
                        <div class="flex gap-4 sm:gap-5">
                            <div class="flex flex-col items-center shrink-0 w-11 sm:w-12">
                                <span class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl shadow-sm border-2 border-white {{ $s['bubble'] }}" title="{{ \App\Models\SalesActivity::typeLabel($act->type) }}">
                                    <i class="{{ $s['icon'] }} text-sm"></i>
                                </span>
                                @unless($isLast)
                                    <span class="w-0.5 flex-1 min-h-[1.25rem] mt-2 rounded-full bg-slate-200" aria-hidden="true"></span>
                                @endunless
                            </div>
                            <article class="flex-1 min-w-0 rounded-xl border {{ $s['accent'] }} bg-white px-4 py-3.5 shadow-sm mb-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-sm font-bold text-slate-900">{{ \App\Models\SalesActivity::typeLabel($act->type) }}</span>
                                    <time class="text-[11px] text-slate-500 font-medium tabular-nums" datetime="{{ $act->created_at->toIso8601String() }}">
                                        {{ $act->created_at->format('Y-m-d H:i') }}
                                        <span class="text-slate-300">·</span>
                                        {{ $act->user?->name ?? '—' }}
                                    </time>
                                </div>
                                @if($act->title)
                                    <p class="font-semibold text-slate-900 mt-1.5 text-sm leading-snug">{{ $act->title }}</p>
                                @endif
                                @if($act->body)
                                    <div class="mt-1.5 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $act->body }}</div>
                                @endif
                                @if(is_array($act->meta) && $act->meta !== [])
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach($act->meta as $mk => $mv)
                                            @if(is_scalar($mv) || $mv === null)
                                                <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $mk }}: {{ $mv === null || $mv === '' ? '—' : $mv }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        </div>
                    @empty
                        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 px-6 py-12 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 text-xl mb-3">
                                <i class="fas fa-comments"></i>
                            </div>
                            <p class="text-slate-800 font-semibold">لا توجد أنشطة مسجّلة بعد</p>
                            <p class="text-slate-500 text-sm mt-1">سيظهر هنا كل تواصل أو تغيير مرحلة يسجّله الموظف.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Transfer history --}}
            @if($lead->transfers->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50/80">
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-amber-600"></i>
                            سجل التحويلات ({{ $lead->transfers->count() }})
                        </h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($lead->transfers as $tr)
                            <div class="px-4 sm:px-5 py-3 text-sm flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $tr->fromUser->name ?? '—' }}
                                        <i class="fas fa-arrow-left text-slate-400 text-xs mx-1"></i>
                                        {{ $tr->toUser->name ?? '—' }}
                                    </p>
                                    @if($tr->reason)
                                        <p class="text-xs text-slate-600 mt-1">{{ $tr->reason }}</p>
                                    @endif
                                    <p class="text-[11px] text-slate-400 mt-1">بواسطة {{ $tr->transferredBy->name ?? '—' }}</p>
                                </div>
                                <time class="text-[11px] text-slate-500 tabular-nums">{{ $tr->created_at->format('Y-m-d H:i') }}</time>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-4 space-y-4 order-1 lg:order-2 lg:sticky lg:top-4">
            {{-- Contact data --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-address-card text-sky-600"></i>
                        بيانات العميل
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <dl class="grid grid-cols-1 gap-2.5 text-sm">
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                            <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">الهاتف</dt>
                            <dd class="font-semibold text-slate-900 break-all">
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="hover:text-indigo-700">{{ $lead->phone }}</a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                            <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">البريد</dt>
                            <dd class="font-semibold text-slate-900 break-all">
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}" class="hover:text-indigo-700">{{ $lead->email }}</a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                            <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">الشركة</dt>
                            <dd class="font-semibold text-slate-900">{{ $lead->company ?? '—' }}</dd>
                        </div>
                        @if($lead->group)
                            <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                                <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">المجموعة</dt>
                                <dd class="font-semibold text-slate-900">{{ $lead->group->name }}</dd>
                            </div>
                        @endif
                        @if($lead->import_batch)
                            <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                                <dt class="text-[11px] font-semibold text-slate-500 mb-0.5">دفعة الاستيراد</dt>
                                <dd class="font-semibold text-slate-900 text-xs">{{ $lead->import_batch }}</dd>
                            </div>
                        @endif
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                            <dt class="text-[11px] font-semibold text-slate-500">آخر تحديث</dt>
                            <dd class="font-semibold text-slate-900 text-xs tabular-nums">{{ $lead->updated_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        @if($lead->closed_at)
                            <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-2">
                                <dt class="text-[11px] font-semibold text-slate-500">تاريخ الإغلاق</dt>
                                <dd class="font-semibold text-slate-900 text-xs tabular-nums">{{ $lead->closed_at->format('Y-m-d H:i') }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if($lead->interest)
                        <div class="rounded-xl border border-amber-100 bg-amber-50/80 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-amber-900 mb-1">الاهتمام</p>
                            <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $lead->interest }}</p>
                        </div>
                    @endif
                    @if($lead->notes)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-slate-600 mb-1">ملاحظات</p>
                            <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $lead->notes }}</p>
                        </div>
                    @endif
                    @if($lead->stage === 'lost' && $lead->lost_reason)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-rose-700 mb-1">سبب الخسارة</p>
                            <p class="text-sm text-slate-800">{{ \App\Models\SalesLead::LOSS_REASONS[$lead->lost_reason] ?? $lead->lost_reason }}</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Assignee --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-tie text-indigo-600"></i>
                        الموظف المسند
                    </h3>
                </div>
                <div class="p-4 text-sm space-y-2">
                    <p class="font-bold text-slate-900 text-base">{{ $lead->assignee->name ?? '—' }}</p>
                    @if($lead->assignee?->email)
                        <p class="text-xs text-slate-500 break-all">{{ $lead->assignee->email }}</p>
                    @endif
                    @if($lead->assignee?->phone)
                        <p class="text-xs text-slate-500">{{ $lead->assignee->phone }}</p>
                    @endif
                </div>
            </section>

            {{-- Win / CSAT --}}
            @if($lead->stage === 'won')
                <section class="rounded-2xl bg-white border border-emerald-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-emerald-100 bg-emerald-50/70">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-trophy text-emerald-600"></i>
                            حالة الفوز
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 text-sm">
                        @if($lead->isWinConfirmed())
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                                <p class="font-bold text-emerald-900">تم اعتماد الفوز والكوميشن</p>
                                <p class="text-emerald-800 mt-1 tabular-nums">المبلغ: <strong>{{ number_format((float) ($lead->commission_amount ?? 0), 2) }} ج.م</strong></p>
                                <p class="text-[11px] text-emerald-700 mt-0.5">{{ $lead->won_confirmed_at?->format('Y-m-d H:i') }}</p>
                                @if($lead->commission_notes)
                                    <p class="text-xs text-slate-600 mt-2 whitespace-pre-wrap">{{ $lead->commission_notes }}</p>
                                @endif
                            </div>
                        @else
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <p class="font-bold text-amber-900">في انتظار موافقة الإدارة</p>
                                @if($lead->assignee)
                                    @php $est = $lead->assignee->calculateSalesCommissionAmount((float) ($lead->expected_value ?? 0)); @endphp
                                    <p class="text-xs text-amber-800 mt-1">كوميشن مقدّر: <strong>{{ number_format($est, 2) }} ج.م</strong></p>
                                @endif
                            </div>
                        @endif
                        @if($lead->csat_rating)
                            <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3">
                                <p class="text-[11px] font-bold text-amber-900 mb-1">تقييم رضا العميل (CSAT)</p>
                                <p class="font-bold text-slate-900">{{ $lead->csat_rating }}/5</p>
                                @if($lead->csat_comment)
                                    <p class="text-xs text-slate-600 mt-1 whitespace-pre-wrap">{{ $lead->csat_comment }}</p>
                                @endif
                                @if($lead->csat_recorded_at)
                                    <p class="text-[10px] text-slate-400 mt-1">{{ $lead->csat_recorded_at->format('Y-m-d') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Transfer form --}}
            <section class="rounded-2xl bg-white border border-amber-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-amber-100 bg-amber-50/70">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-amber-600"></i>
                        تحويل لعضو آخر
                    </h3>
                </div>
                <form method="POST" action="{{ route('employee.sales-manager.leads.transfer', $lead) }}" class="p-4 space-y-3"
                      onsubmit="return confirm('تأكيد تحويل هذا العميل؟')">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">تحويل إلى</label>
                        <select name="to_user_id" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            @foreach($members as $m)
                                @if((int) $m->user_id !== (int) $lead->assigned_to)
                                    <option value="{{ $m->user_id }}">{{ $m->user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">سبب التحويل (اختياري)</label>
                        <textarea name="reason" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm" placeholder="مثال: إعادة توزيع الحمل">{{ old('reason') }}</textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold">
                        تحويل الآن
                    </button>
                </form>
            </section>
        </aside>
    </div>
</div>
@endsection
