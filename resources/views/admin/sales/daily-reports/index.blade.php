@extends('layouts.admin')

@section('title', 'التقارير اليومية — المبيعات')
@section('header', 'التقارير اليومية — المبيعات')

@section('content')
@php
    $statCards = [
        ['label' => 'إجمالي التقارير', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-clipboard-list', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'في الفترة المحددة'],
        ['label' => 'مسلّمة', 'value' => number_format($stats['submitted'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم تسليمها'],
        ['label' => 'بخصم تلقائي', 'value' => number_format($stats['with_penalty'] ?? 0), 'icon' => 'fas fa-gavel', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تأخرت عن الموعد'],
    ];
    $statusBadges = [
        'submitted' => ['label' => 'مسلّم', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- الهيدر + إحصائيات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقارير موظفي المبيعات اليومية</h2>
                    <p class="text-xs text-slate-600">نشاط، إنتاجية، ومشاكل العملاء — تصدير Excel لتحليل الأنماط.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.daily-reports.settings') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-cog text-slate-500"></i>
                    إعدادات وخصم
                </a>
                <a href="{{ route('admin.sales.daily-reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4">
            @foreach ($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
        <div class="px-4 pb-4">
            <p class="text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                الفترة: <strong>{{ $from->format('Y-m-d') }}</strong> — <strong>{{ $to->format('Y-m-d') }}</strong>
                @if($userId)
                    · الموظف: <strong>{{ $reps->firstWhere('id', $userId)?->name ?? $userId }}</strong>
                @endif
                @if($status)
                    · الحالة: <strong>{{ $statusBadges[$status]['label'] ?? $status }}</strong>
                @endif
            </p>
        </div>
    </section>

    {{-- الفلاتر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">تصفية حسب التاريخ أو الموظف أو حالة التسليم.</p>
        </div>
        <div class="p-4">
            <form method="get" action="{{ route('admin.sales.daily-reports.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">الكل</option>
                        @foreach($reps as $rep)
                            <option value="{{ $rep->id }}" @selected($userId == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">الكل</option>
                        <option value="submitted" @selected($status === 'submitted')>مسلّم</option>
                        <option value="draft" @selected($status === 'draft')>مسودة</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    @if(request()->hasAny(['from', 'to', 'user_id', 'status']))
                        <a href="{{ route('admin.sales.daily-reports.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- قائمة التقارير --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">التقارير</h3>
                <p class="text-xs text-slate-600">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">{{ $reports->count() }} تقرير</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                        <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-center font-semibold">رسائل</th>
                        <th class="px-4 py-3 text-center font-semibold">مكالمات</th>
                        <th class="px-4 py-3 text-center font-semibold">تواصل</th>
                        <th class="px-4 py-3 text-center font-semibold w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $r)
                        @php
                            $statusKey = $r->isSubmitted() ? 'submitted' : 'draft';
                            $statusMeta = $statusBadges[$statusKey];
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-800 tabular-nums">{{ $r->report_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $r->user->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $statusMeta['classes'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $statusMeta['label'] }}
                                </span>
                                @if($r->auto_deduction_id)
                                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200 mr-1">خصم</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ $r->messages_replied ?? '—' }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ $r->calls_made ?? '—' }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ $r->contacts->count() }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.sales.daily-reports.show', $r->id) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-sky-600 hover:bg-sky-50 text-sm"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا توجد تقارير</p>
                                <p class="text-xs text-slate-500 mt-1">لم يتم تسجيل تقارير في هذه الفترة أو لا توجد نتائج للفلتر.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
