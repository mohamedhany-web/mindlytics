@extends('layouts.admin')

@section('title', 'طلبات محرر الفيديو')
@section('header', 'طلبات محرر الفيديو — مشرف / محرر')

@section('content')
@php
    use App\Models\ModeratorMontageRequest;
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500';
    $statCards = [
        ['label' => 'إجمالي الطلبات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-film', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
        ['label' => 'نشطة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-spinner', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'بانتظار المشرف', 'value' => number_format($stats['awaiting_moderator'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'تسليم مشرف', 'value' => number_format($stats['in_delivery'] ?? 0), 'icon' => 'fas fa-truck', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-film"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">طلبات محرر الفيديو</h2>
                    <p class="text-xs text-slate-600">متابعة طلبات المونتاج بين مشرفي المحتوى ومحرري الفيديو.</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-5 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold text-slate-600">{{ $card['label'] }}</p>
                    <p class="text-xl font-black text-slate-900 tabular-nums">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="عنوان، رقم، اسم..." class="{{ $inputClass }}">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach(ModeratorMontageRequest::statuses() as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                    <label class="block text-xs font-semibold text-slate-700 mb-1">محرر الفيديو</label>
                    <select name="montage_employee_id" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($editors as $e)
                            <option value="{{ $e->id }}" {{ (string) request('montage_employee_id') === (string) $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <button type="submit" class="w-full px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-semibold">تصفية</button>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">المشرف</th>
                        <th class="text-right px-4 py-3 font-semibold">محرر الفيديو</th>
                        <th class="text-right px-4 py-3 font-semibold">حد التسليم</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $item)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-500">{{ $item->id }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item->title }}</td>
                            <td class="px-4 py-3">{{ $item->moderator->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $item->montageEmployee->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $item->deadline_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100">{{ $item->statusLabel() }}</span></td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.montage-requests.show', $item) }}" class="text-cyan-700 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">لا توجد طلبات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $requests->withQueryString()->links() }}</div>
        @endif
    </section>
</div>
@endsection
