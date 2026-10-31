@extends('layouts.admin')

@section('title', 'تحليلات Mindlytics Journey')
@section('header', 'تحليلات الرحلة والمشاركات')

@section('content')
@php
    $channelLabels = [
        'linkedin' => 'LinkedIn',
        'facebook' => 'Facebook',
        'x' => 'X',
        'copy' => 'نسخ الرابط',
        'download' => 'بطاقة PNG',
        'og_fetch' => 'مشاهدات البطاقة (OG)',
        'project_view' => 'مشاهدة مشروع',
        'profile_view' => 'مشاهدة ملف رحلة',
    ];
@endphp
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">تحليلات Mindlytics Journey</h1>
                <p class="text-slate-500 mt-1">مشاركات LinkedIn، مشاهدات البطاقات، وأقوى المشاريع والملفات — بيانات نمو المعرض.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.portfolio.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">رقابة المشاريع</a>
                <a href="{{ route('public.portfolio.talent') }}" target="_blank" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold">دليل المواهب</a>
            </div>
        </div>
        <div class="p-5 sm:p-8">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="w-full rounded-xl border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="w-full rounded-xl border-slate-200 text-sm">
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 px-4 py-2.5 bg-slate-800 text-white rounded-xl font-semibold text-sm">تصفية</button>
                    <a href="{{ route('admin.journey-analytics.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold text-sm">مسح</a>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold text-slate-500">مشاريع منشورة</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($catalog['published_projects']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold text-slate-500">ملفات مواهب عامة</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($catalog['public_profiles']) }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold text-amber-700">Featured</p>
            <p class="text-2xl font-black text-amber-900 mt-1">{{ number_format($catalog['featured_projects']) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-semibold text-emerald-700">Open to work</p>
            <p class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($catalog['open_to_work']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'كل الأحداث', 'value' => $totals['events']],
            ['label' => 'مشاركات', 'value' => $totals['shares']],
            ['label' => 'مشاهدات بطاقة', 'value' => $totals['card_views']],
            ['label' => 'مشاهدات صفحات', 'value' => $totals['page_views']],
            ['label' => 'LinkedIn', 'value' => $totals['linkedin']],
            ['label' => 'مستخدمون نشطون', 'value' => $totals['unique_actors']],
        ] as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-bold text-slate-900 mb-4">التوزيع حسب القناة</h2>
            @if($by_channel->isEmpty())
                <p class="text-sm text-slate-500">لا توجد أحداث في الفترة المحددة.</p>
            @else
                <div class="space-y-3">
                    @foreach($by_channel as $channel => $count)
                        @php $max = max(1, $by_channel->max()); $pct = round(($count / $max) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold text-slate-700">{{ $channelLabels[$channel] ?? $channel }}</span>
                                <span class="text-slate-500">{{ number_format($count) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-sky-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-bold text-slate-900 mb-4">النشاط اليومي</h2>
            @if($daily->isEmpty())
                <p class="text-sm text-slate-500">لا توجد بيانات يومية.</p>
            @else
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @foreach($daily as $row)
                        <div class="flex items-center justify-between text-sm border-b border-slate-50 py-1.5">
                            <span class="text-slate-600">{{ $row->day }}</span>
                            <span class="font-bold text-slate-900">{{ number_format($row->aggregate) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-bold text-slate-900 mb-4">أقوى المشاريع تفاعلاً</h2>
            @forelse($top_projects as $item)
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <div class="min-w-0">
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" class="font-semibold text-sky-700 hover:underline truncate block">{{ $item->label }}</a>
                        @else
                            <span class="font-semibold text-slate-800">{{ $item->label }}</span>
                        @endif
                        <div class="text-xs text-slate-500">{{ $item->meta }}</div>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ number_format($item->aggregate) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">لا توجد تفاعلات على المشاريع بعد.</p>
            @endforelse
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-bold text-slate-900 mb-4">أقوى ملفات الرحلة</h2>
            @forelse($top_profiles as $item)
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <div class="min-w-0">
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" class="font-semibold text-sky-700 hover:underline truncate block">{{ $item->label }}</a>
                        @else
                            <span class="font-semibold text-slate-800">{{ $item->label }}</span>
                        @endif
                        <div class="text-xs text-slate-500">{{ $item->meta }}</div>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ number_format($item->aggregate) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">لا توجد تفاعلات على الملفات بعد.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
