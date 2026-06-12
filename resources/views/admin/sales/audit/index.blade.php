@extends('layouts.admin')

@section('title', 'سجل أنشطة المبيعات')
@section('header', 'سجل أنشطة المبيعات')

@section('content')
@php
    $hasFilters = request()->hasAny(['user_id', 'action', 'date_from', 'date_to', 'search']);
    $statCards = [
        ['label' => 'إجمالي السجلات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-history', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'حسب الفلتر الحالي'],
        ['label' => 'هذه الصفحة', 'value' => number_format($logs->count()), 'icon' => 'fas fa-file-alt', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'سجلات معروضة'],
        ['label' => 'أنواع الأحداث', 'value' => number_format(count($actionLabels)), 'icon' => 'fas fa-tags', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'أحداث مبيعات'],
        ['label' => 'المستخدمون', 'value' => number_format($filterUsers->count()), 'icon' => 'fas fa-users', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'للفلترة'],
    ];
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
@endphp

<div class="space-y-6">
    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">سجل أنشطة المبيعات</h2>
                    <p class="text-xs text-slate-600">عرض، إنشاء، تعديل، حذف، نشاط، وإعادة إسناد — للموظفين والإدارة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="{{ route('admin.sales.transfer.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-exchange-alt text-violet-600"></i>
                    تحويل بيانات
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
        @if($hasFilters)
            <div class="px-4 pb-4">
                <p class="text-xs text-slate-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    <i class="fas fa-filter text-amber-600 ml-1"></i>
                    فلتر نشط — <strong>{{ number_format($stats['total'] ?? 0) }}</strong> سجل مطابق.
                </p>
            </div>
        @endif
    </section>

    {{-- الفلاتر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-4">
            <form method="get" action="{{ route('admin.sales.audit-log.index') }}" class="flex flex-col gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المستخدم</label>
                        <select name="user_id" class="{{ $inputClass }}">
                            <option value="">الكل</option>
                            @foreach($filterUsers as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الحدث</label>
                        <select name="action" class="{{ $inputClass }}">
                            <option value="">الكل</option>
                            @foreach($actionLabels as $key => $label)
                                <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="{{ $inputClass }}">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 xl:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="الوصف أو الرابط..." class="{{ $inputClass }}">
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('admin.sales.audit-log.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- السجلات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">السجلات</h3>
                <p class="text-xs text-slate-600">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">{{ number_format($logs->total()) }} سجل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الوقت</th>
                        <th class="px-4 py-3 text-right font-semibold">المستخدم</th>
                        <th class="px-4 py-3 text-right font-semibold">الحدث</th>
                        <th class="px-4 py-3 text-right font-semibold">الوصف</th>
                        <th class="px-4 py-3 text-right font-semibold">الرابط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap tabular-nums text-xs">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $log->user->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    {{ $actionLabels[$log->action] ?? $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 max-w-md">{{ \Illuminate\Support\Str::limit($log->description, 200) }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500 max-w-[200px] truncate" title="{{ $log->url }}">{{ \Illuminate\Support\Str::limit($log->url, 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-clipboard-list text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا سجلات مطابقة</p>
                                <p class="text-xs text-slate-500 mt-1">جرّب تغيير الفلاتر أو نطاق التاريخ.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
