@extends('layouts.admin')

@section('title', $plan->title)
@section('header', 'خطة تسويق: ' . $plan->title)

@section('content')
@php
    $evtStatus = fn ($s) => match($s) {
        'idea' => 'فكرة',
        'draft' => 'مسودة',
        'scheduled' => 'مجدول',
        'published' => 'منشور',
        'skipped' => 'تم التخطي',
        default => $s,
    };
@endphp
<div class="space-y-6">
    <a href="{{ route('admin.moderator-marketing-plans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> العودة للقائمة
    </a>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-2">
        <p class="text-sm text-slate-600">المشرف: <strong class="text-slate-900">{{ $plan->moderator->name ?? '—' }}</strong></p>
        <p class="text-sm text-slate-600">الحالة: <span class="font-semibold">{{ $plan->status }}</span></p>
        @if($plan->start_date || $plan->end_date)
            <p class="text-sm text-slate-600">
                @if($plan->start_date) من {{ $plan->start_date->format('Y-m-d') }} @endif
                @if($plan->end_date) — إلى {{ $plan->end_date->format('Y-m-d') }} @endif
            </p>
        @endif
        @if($plan->designTaskCycle)
            <p class="text-sm text-slate-600">دورة تصميم مرتبطة: <a href="{{ route('admin.design-task-cycles.show', $plan->designTaskCycle) }}" class="text-fuchsia-700 font-semibold">#{{ $plan->designTaskCycle->id }} {{ $plan->designTaskCycle->title }}</a></p>
        @endif
    </div>

    @if($plan->summary)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-500 uppercase mb-2">الملخص</h2>
            <p class="text-slate-800 whitespace-pre-wrap">{{ $plan->summary }}</p>
        </div>
    @endif
    @if($plan->goals)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-500 uppercase mb-2">الأهداف</h2>
            <p class="text-slate-800 whitespace-pre-wrap">{{ $plan->goals }}</p>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-900">المنصات</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-right px-4 py-2">المنصة</th>
                        <th class="text-right px-4 py-2">رابط</th>
                        <th class="text-right px-4 py-2">استراتيجية</th>
                        <th class="text-right px-4 py-2">إيقاع النشر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plan->platforms as $plat)
                        <tr>
                            <td class="px-4 py-2 font-semibold">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full border" style="background: {{ $plat->color_hex }}"></span>
                                    {{ $plat->displayName() }}
                                </span>
                            </td>
                            <td class="px-4 py-2 break-all max-w-xs">
                                @if($plat->profile_url)<a href="{{ $plat->profile_url }}" target="_blank" class="text-blue-600 text-xs">{{ \Illuminate\Support\Str::limit($plat->profile_url, 40) }}</a>@else — @endif
                            </td>
                            <td class="px-4 py-2 text-slate-700">{{ $plat->strategy_notes ?: '—' }}</td>
                            <td class="px-4 py-2 text-slate-700">{{ $plat->cadence_notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا منصات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-900">أحداث التقويم</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-right px-4 py-2">الوقت</th>
                        <th class="text-right px-4 py-2">العنوان</th>
                        <th class="text-right px-4 py-2">المنصة</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">دورة تصميم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plan->calendarEvents as $ev)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $ev->starts_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2">{{ $ev->title }}</td>
                            <td class="px-4 py-2">{{ $ev->platform ? $ev->platform->displayName() : '—' }}</td>
                            <td class="px-4 py-2">{{ $evtStatus($ev->status) }}</td>
                            <td class="px-4 py-2">
                                @if($ev->design_task_cycle_id)
                                    <a href="{{ route('admin.design-task-cycles.show', $ev->design_task_cycle_id) }}" class="text-fuchsia-700 font-semibold">#{{ $ev->design_task_cycle_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا أحداث.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
