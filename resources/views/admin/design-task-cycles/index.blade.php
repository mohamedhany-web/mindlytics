@extends('layouts.admin')

@section('title', 'دورات التصميم')
@section('header', 'دورات التصميم — مشرف / مصمم')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500';
    $statCards = [
        ['label' => 'إجمالي الدورات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'description' => 'كل الطلبات'],
        ['label' => 'نشطة (تصميم)', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-paint-brush', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'بانتظار أو قيد التنفيذ'],
        ['label' => 'بانتظار المشرف', 'value' => number_format($stats['awaiting_moderator'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'تم تسليم التصميم'],
        ['label' => 'تسليم مشرف', 'value' => number_format($stats['in_delivery'] ?? 0), 'icon' => 'fas fa-truck', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'مرحلة التسليم النهائي'],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'دورات منتهية'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-fuchsia-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-palette"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">دورات التصميم</h2>
                    <p class="text-xs text-slate-600">إدارة طلبات التصميم بين المشرفين والمصممين — إنشاء، تعديل، ومتابعة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.design-task-cycles.performance-report') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-pie text-fuchsia-600"></i>
                    تقرير الأداء الشهري
                </a>
                <a href="{{ route('admin.design-task-cycles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 shadow-sm">
                    <i class="fas fa-plus"></i>
                    دورة جديدة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-5 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-fuchsia-600"></i>
                تصفية الدورات
            </h3>
        </div>
        <div class="p-4">
            <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="عنوان، رقم، اسم..." class="{{ $inputClass }}">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach(['pending_design','design_in_progress','design_submitted','moderator_delivery_pending','completed','cancelled'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ \App\Models\DesignTaskCycle::statusLabel($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">المشرف</label>
                    <select name="moderator_id" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($moderators as $m)
                            <option value="{{ $m->id }}" {{ (string) request('moderator_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">المصمم</label>
                    <select name="designer_employee_id" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($designers as $d)
                            <option value="{{ $d->id }}" {{ (string) request('designer_employee_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3 flex flex-wrap gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-search"></i> تطبيق
                    </button>
                    <a href="{{ route('admin.design-task-cycles.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-semibold">مسح</a>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">قائمة الدورات</h3>
            <span class="text-xs text-slate-500">{{ $cycles->total() }} نتيجة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">المشرف</th>
                        <th class="text-right px-4 py-3 font-semibold">المصمم</th>
                        <th class="text-right px-4 py-3 font-semibold">الأولوية</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">الموعد</th>
                        <th class="text-right px-4 py-3 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cycles as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $c->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $c->title }}</p>
                                <p class="text-[11px] text-slate-500">{{ $c->created_at?->format('Y-m-d') }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $c->moderator->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $c->designer->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold {{ \App\Models\DesignTaskCycle::priorityBadgeClass($c->priority) }}">
                                    {{ \App\Models\DesignTaskCycle::priorityLabel($c->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold border {{ \App\Models\DesignTaskCycle::statusBadgeClass($c->status) }}">
                                    {{ \App\Models\DesignTaskCycle::statusLabel($c->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600">{{ $c->deadline_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.design-task-cycles.show', $c) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-semibold">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                    <a href="{{ route('admin.design-task-cycles.edit', $c) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-semibold">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="text-slate-400 mb-3"><i class="fas fa-palette text-4xl"></i></div>
                                <p class="text-slate-600 font-semibold">لا توجد دورات تصميم</p>
                                <a href="{{ route('admin.design-task-cycles.create') }}" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-fuchsia-600 hover:text-fuchsia-800">
                                    <i class="fas fa-plus"></i> إنشاء أول دورة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cycles->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $cycles->links() }}</div>
        @endif
    </section>
</div>
@endsection
