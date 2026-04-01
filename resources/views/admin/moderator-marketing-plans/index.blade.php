@extends('layouts.admin')

@section('title', 'خطط تسويق المشرفين')
@section('header', 'خطط التسويق والمنصات (مشرفو المحتوى)')

@section('content')
<div class="space-y-6">
    <form method="get" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">المشرف</label>
            <select name="moderator_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm min-w-[200px]">
                <option value="">الكل</option>
                @foreach($moderators as $m)
                    <option value="{{ $m->id }}" {{ (string) request('moderator_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(['draft', 'active', 'paused', 'completed'] as $v)
                    <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">بحث في العنوان</label>
            <input type="text" name="q" value="{{ request('q') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="...">
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">تصفية</button>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
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
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plans as $p)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs">{{ $p->id }}</td>
                            <td class="px-4 py-3">{{ $p->moderator->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $p->title }}</td>
                            <td class="px-4 py-3"><span class="text-xs font-medium px-2 py-1 rounded bg-slate-100">{{ $p->status }}</span></td>
                            <td class="px-4 py-3">{{ $p->platforms_count }}</td>
                            <td class="px-4 py-3">{{ $p->calendar_events_count }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.moderator-marketing-plans.show', $p) }}" class="text-pink-700 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">لا توجد خطط.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $plans->links() }}</div>
        @endif
    </div>
</div>
@endsection
