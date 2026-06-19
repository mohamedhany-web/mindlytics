@extends('layouts.admin')

@section('title', 'خطط تسويق المشرفين')
@section('header', 'خطط التسويق — أتمتة ورقابة')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-pink-500';
    $statusLabels = ['draft' => 'مسودة', 'active' => 'نشط', 'paused' => 'متوقف', 'completed' => 'مكتمل'];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-l from-pink-50 to-white border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مركز خطط التسويق</h2>
                    <p class="text-xs text-slate-600">منصات · جدول المحتوى · توجيه للمصمم/المونتاج · تأكيد التنفيذ · غرامات تلقائية</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.moderator-marketing-plans.settings') }}" class="px-3 py-2 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-white">
                    <i class="fas fa-cog text-slate-600"></i> الإعدادات
                </a>
                <a href="{{ route('admin.moderator-marketing-plans.create') }}" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-bold">
                    <i class="fas fa-plus ml-1"></i> خطة جديدة
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3 p-4">
            @foreach([
                ['label' => 'إجمالي الخطط', 'value' => $stats['total'] ?? 0, 'class' => ''],
                ['label' => 'نشطة', 'value' => $stats['active'] ?? 0, 'class' => 'text-emerald-700'],
                ['label' => 'مشرفون', 'value' => $stats['moderators'] ?? 0, 'class' => ''],
                ['label' => 'منصات', 'value' => $stats['platforms'] ?? 0, 'class' => ''],
                ['label' => 'أحداث اليوم', 'value' => $stats['events_today'] ?? 0, 'class' => 'text-violet-700'],
                ['label' => 'بانتظار تأكيد', 'value' => $stats['pending_confirm_today'] ?? 0, 'class' => 'text-amber-700'],
                ['label' => 'غرامات الشهر', 'value' => $stats['penalties_month'] ?? 0, 'class' => 'text-rose-700'],
            ] as $card)
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-[10px] font-semibold text-slate-500">{{ $card['label'] }}</p>
                    <p class="text-xl font-black tabular-nums {{ $card['class'] }}">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if(($stats['pending_confirm_today'] ?? 0) > 0)
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-exclamation-triangle ml-1"></i>
            <strong>{{ $stats['pending_confirm_today'] }}</strong> حدث/أحداث اليوم لم يُؤكَّد تنفيذها بعد — سيتم تطبيق غرامة تلقائياً عند منتصف الليل إن لم يُؤكَّد.
        </div>
    @endif

    <form method="get" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">المشرف</label>
            <select name="moderator_id" class="rounded-xl border border-slate-300 px-3 py-2 text-sm min-w-[200px]">
                <option value="">الكل</option>
                @foreach($moderators as $m)
                    <option value="{{ $m->id }}" {{ (string) request('moderator_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
            <select name="status" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($statusLabels as $k => $l)
                    <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="بحث في العنوان..." class="{{ $inputClass }}">
        </div>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
    </form>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">المشرف</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">منصات</th>
                        <th class="text-right px-4 py-3 font-semibold">أحداث</th>
                        <th class="text-right px-4 py-3 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plans as $p)
                        <tr class="hover:bg-pink-50/30">
                            <td class="px-4 py-3 font-mono text-xs">{{ $p->id }}</td>
                            <td class="px-4 py-3">{{ $p->moderator->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $p->title }}</td>
                            <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-lg bg-slate-100">{{ $statusLabels[$p->status] ?? $p->status }}</span></td>
                            <td class="px-4 py-3">{{ $p->platforms_count }}</td>
                            <td class="px-4 py-3">{{ $p->calendar_events_count }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.moderator-marketing-plans.show', $p) }}" class="text-pink-700 font-semibold text-xs">عرض</a>
                                    <a href="{{ route('admin.moderator-marketing-plans.edit', $p) }}" class="text-amber-700 font-semibold text-xs">تعديل</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-16 text-center text-slate-500">لا توجد خطط. <a href="{{ route('admin.moderator-marketing-plans.create') }}" class="text-pink-600 font-semibold">إنشاء خطة</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())<div class="px-4 py-3 border-t">{{ $plans->links() }}</div>@endif
    </section>
</div>
@endsection
