@extends('layouts.admin')

@section('title', 'دورات التصميم (مشرف / مصمم)')
@section('header', 'دورات التصميم')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">إجمالي</p>
            <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">نشطة (تصميم)</p>
            <p class="text-2xl font-black text-amber-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">بانتظار المشرف</p>
            <p class="text-2xl font-black text-sky-700">{{ $stats['awaiting_moderator'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">تسليم مشرف</p>
            <p class="text-2xl font-black text-violet-700">{{ $stats['in_delivery'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">مكتملة</p>
            <p class="text-2xl font-black text-emerald-700">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="get" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="عنوان، رقم، اسم..." class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    @foreach(['pending_design','design_in_progress','design_submitted','moderator_delivery_pending','completed','cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ \App\Models\DesignTaskCycle::statusLabel($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">مشرف</label>
                <select name="moderator_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    @foreach($moderators as $m)
                        <option value="{{ $m->id }}" {{ (string) request('moderator_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">مصمم</label>
                <select name="designer_employee_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    @foreach($designers as $d)
                        <option value="{{ $d->id }}" {{ (string) request('designer_employee_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">تطبيق</button>
                <a href="{{ route('admin.design-task-cycles.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm font-semibold">مسح</a>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">العنوان</th>
                        <th class="text-right px-4 py-3">المشرف</th>
                        <th class="text-right px-4 py-3">المصمم</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">تاريخ</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cycles as $c)
                        <tr>
                            <td class="px-4 py-2 font-mono text-xs">{{ $c->id }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $c->title }}</td>
                            <td class="px-4 py-2">{{ $c->moderator->name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $c->designer->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <span class="text-xs font-semibold text-fuchsia-800">{{ \App\Models\DesignTaskCycle::statusLabel($c->status) }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-600">{{ $c->created_at?->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.design-task-cycles.show', $c) }}" class="text-blue-600 font-semibold text-xs">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">لا توجد دورات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cycles->hasPages())
            <div class="px-4 py-3 border-t">{{ $cycles->links() }}</div>
        @endif
    </div>

    <a href="{{ route('admin.design-task-cycles.performance-report') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-fuchsia-600 text-white font-semibold text-sm">
        <i class="fas fa-chart-pie"></i>
        تقرير الأداء الشهري
    </a>
</div>
@endsection
