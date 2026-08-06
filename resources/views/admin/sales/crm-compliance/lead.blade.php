@extends('layouts.admin')

@section('title', 'رحلة العميل — ' . ($lead->name ?: '#'.$lead->id))
@section('header', 'خط زمني للعميل في الـ Pipeline')

@section('content')
@php
    $steps = $timeline['steps'] ?? [];
    $fieldLabels = [
        'profile_type' => 'الحالة',
        'age' => 'السن',
        'field_domain' => 'المجال',
        'experience_level' => 'الخبرة',
        'course_motivation' => 'الدافع',
        'start_preference' => 'موعد البدء',
        'can_pay' => 'القدرة على الدفع',
    ];
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ url()->previous() }}" class="text-xs font-semibold text-sky-700 hover:text-sky-900 inline-flex items-center gap-1 mb-2">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h2 class="text-xl font-black text-slate-900">{{ $lead->name ?: ('Lead #'.$lead->id) }}</h2>
                <p class="text-xs text-slate-600">
                    {{ $lead->phone }} ·
                    المرحلة الحالية: <span class="font-bold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                    @if($lead->assignee)
                        · المسؤول: {{ $lead->assignee->name }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.sales.leads.show', $lead) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">
                    صفحة الـ Lead
                </a>
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center">
                    <p class="text-[10px] text-slate-500">خطوات موثّقة</p>
                    <p class="text-lg font-black text-slate-900">{{ (int) $timeline['steps_count'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center">
                    <p class="text-[10px] text-slate-500">تغييرات مرحلة</p>
                    <p class="text-lg font-black text-slate-900">{{ (int) $timeline['stage_changes_count'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center">
                    <p class="text-[10px] text-slate-500">Qualification</p>
                    <p class="text-lg font-black {{ ($timeline['qualification_fill_pct'] ?? 0) >= 90 ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ number_format((float) ($timeline['qualification_fill_pct'] ?? 0), 0) }}%
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center">
                    <p class="text-[10px] text-slate-500">الدفع</p>
                    <p class="text-sm font-black {{ !empty($timeline['finance_verified']) ? 'text-emerald-700' : 'text-slate-500' }}">
                        {{ !empty($timeline['finance_verified']) ? 'مؤكد' : 'غير مؤكد' }}
                    </p>
                </div>
            </div>
        </div>

        @if(!empty($timeline['qualification_fields']))
            <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap gap-2">
                @foreach($timeline['qualification_fields'] as $field => $ok)
                    <span class="text-[11px] rounded-lg px-2 py-1 font-semibold {{ $ok ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $fieldLabels[$field] ?? $field }} {{ $ok ? '✓' : '✗' }}
                    </span>
                @endforeach
            </div>
        @endif
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900">كل خطوة مع العميل</h3>
            <p class="text-xs text-slate-500">من أول تسجيل حتى آخر نشاط — المصدر الوحيد الموثّق هو CRM</p>
        </div>
        <div class="p-4">
            @if(count($steps) === 0)
                <p class="text-center text-sm text-slate-500 py-10">لا يوجد سجل أنشطة لهذا العميل.</p>
            @else
                <ol class="relative border-r border-slate-200 mr-3 space-y-4">
                    @foreach($steps as $step)
                        <li class="relative pr-8">
                            <span class="absolute right-[-0.4rem] top-1.5 w-3 h-3 rounded-full border-2 border-white shadow
                                {{ $step['type'] === 'stage_change' ? 'bg-violet-500' : ($step['type'] === 'call' ? 'bg-sky-500' : 'bg-emerald-500') }}"></span>
                            <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $step['type_label'] }}
                                        @if($step['type'] === 'stage_change')
                                            <span class="font-semibold text-violet-700">
                                                · {{ $step['from_label'] ?? '—' }} → {{ $step['to_label'] ?? '—' }}
                                            </span>
                                        @elseif($step['outcome_label'] && $step['outcome_label'] !== '—')
                                            <span class="font-normal text-slate-600">· {{ $step['outcome_label'] }}</span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ optional($step['at'])->format('Y-m-d H:i') }} · {{ $step['user_name'] }}
                                    </p>
                                </div>
                                @if($step['title'] || $step['body'])
                                    <p class="text-xs text-slate-600 mt-1 line-clamp-3">{{ $step['title'] ?: $step['body'] }}</p>
                                @endif
                                @if(!empty($step['duration_seconds']))
                                    <p class="text-[10px] text-slate-400 mt-1">مدة: {{ (int) $step['duration_seconds'] }} ث</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </section>
</div>
@endsection
