@extends('layouts.admin')

@section('title', 'تقارير الواتساب')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $totals = $report['totals'] ?? [];
    $statCards = [
        ['label' => 'محادثات نشطة', 'value' => number_format($totals['conversations'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'رسائل الفترة', 'value' => number_format($totals['messages_in_period'] ?? 0), 'icon' => 'fas fa-comments', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'واردة / صادرة', 'value' => number_format($totals['inbound'] ?? 0) . ' / ' . number_format($totals['outbound'] ?? 0), 'icon' => 'fas fa-exchange-alt', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['label' => 'مرتبطة بـ CRM', 'value' => number_format($totals['linked_leads'] ?? 0), 'icon' => 'fas fa-link', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'فوز من واتساب', 'value' => number_format($totals['won_from_whatsapp'] ?? 0), 'icon' => 'fas fa-trophy', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
        ['label' => 'غير مقروء', 'value' => number_format($totals['unread'] ?? 0), 'icon' => 'fas fa-envelope', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
    ];
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background:#f8fafc; min-height:100vh;">
    @include('admin.whatsapp._alerts')

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقارير الواتساب</h2>
                    <p class="text-xs text-slate-600">أداء المحادثات، الربط مع المبيعات، ونشاط الموظفين.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.whatsapp.inbox') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-inbox text-emerald-600"></i>
                    المحادثات
                </a>
            </div>
        </div>

        <form method="get" class="p-4 border-b border-slate-100 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">من</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">إلى</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="rounded-lg border-slate-200 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">تطبيق</button>
        </form>

        @if(!($report['ready'] ?? false))
            <div class="p-6 text-sm text-amber-800 bg-amber-50">
                جداول الواتساب غير جاهزة — نفّذ <code>php artisan migrate --force</code>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 p-4">
                @foreach($statCards as $card)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                                <p class="text-lg font-black text-slate-900 tabular-nums">{{ $card['value'] }}</p>
                            </div>
                            <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} shrink-0">
                                <i class="{{ $card['icon'] }} text-sm"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    @if($report['ready'] ?? false)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-headset text-emerald-600"></i> أداء الموظفين
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 border-b">
                                <th class="text-right py-2">الموظف</th>
                                <th class="text-right py-2">محادثات</th>
                                <th class="text-right py-2">ردود</th>
                                <th class="text-right py-2">مفتوحة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['agent_performance'] ?? [] as $row)
                                <tr class="border-b border-slate-50">
                                    <td class="py-2 font-medium">{{ $row['name'] }}</td>
                                    <td class="py-2 tabular-nums">{{ $row['conversations'] }}</td>
                                    <td class="py-2 tabular-nums">{{ $row['replies'] }}</td>
                                    <td class="py-2 tabular-nums">{{ $row['open'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-slate-400">لا بيانات بعد</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-filter text-sky-600"></i> حالات المحادثات
                </h3>
                <div class="space-y-2">
                    @forelse($report['by_status'] ?? [] as $row)
                        <div class="flex items-center justify-between text-sm rounded-lg bg-slate-50 px-3 py-2">
                            <span>{{ $row['label'] }}</span>
                            <span class="font-bold tabular-nums">{{ $row['total'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">لا محادثات</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-project-diagram text-violet-600"></i> Pipeline المرتبط
                </h3>
                <div class="space-y-2">
                    @forelse($report['pipeline_breakdown'] ?? [] as $row)
                        <div class="flex items-center justify-between text-sm rounded-lg bg-violet-50 px-3 py-2">
                            <span>{{ $row['label'] }}</span>
                            <span class="font-bold tabular-nums">{{ $row['total'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">لا محادثات مرتبطة بعملاء</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-tags text-amber-600"></i> أكثر الوسوم
                </h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($report['top_tags'] ?? [] as $tag)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-900">
                            {{ $tag['name'] }} <span class="text-amber-600">({{ $tag['total'] }})</span>
                        </span>
                    @empty
                        <p class="text-sm text-slate-400">لا وسوم</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-slate-600"></i> آخر أحداث CRM
            </h3>
            <div class="space-y-3">
                @forelse($report['recent_events'] ?? [] as $event)
                    <div class="flex gap-3 text-sm border-b border-slate-50 pb-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-2 shrink-0"></div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $event['title'] }}</p>
                            @if($event['description'])
                                <p class="text-slate-600 text-xs">{{ $event['description'] }}</p>
                            @endif
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $event['performer'] ? $event['performer'] . ' · ' : '' }}{{ $event['at'] }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">لا أحداث مسجّلة</p>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection
